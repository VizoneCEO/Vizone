<?php
require_once BACK_PATH . 'config/database.php';
require_once BACK_PATH . 'services/AuthService.php';

class ClienteService
{
    private $db;
    private $authService;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->authService = new AuthService();
    }

    /**
     * Obtiene todos los clientes para la tabla principal.
     */
    public function getAllClientes()
    {
        try {
            $stmt = $this->db->query("
                SELECT c.*, u.username 
                FROM clientes c 
                LEFT JOIN users u ON c.user_id = u.id 
                ORDER BY c.nombre_empresa ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllClientes Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un cliente por su ID con sus detalles, servicios y documentos.
     */
    public function getClienteById($id)
    {
        try {
            // Datos del cliente
            $stmt = $this->db->prepare("
                SELECT c.*, u.username, u.role 
                FROM clientes c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch();

            if (!$cliente)
                return false;

            // Servicios del cliente con la suma de sus pagos reales
            $stmtSrv = $this->db->prepare("
                SELECT cs.*, 
                       COALESCE((SELECT SUM(monto_pagado) FROM cliente_pagos WHERE servicio_id = cs.id), 0) as total_pagado
                FROM cliente_servicios cs 
                WHERE cs.cliente_id = ? 
                ORDER BY cs.created_at DESC
            ");
            $stmtSrv->execute([$id]);
            $servicios = $stmtSrv->fetchAll();

            $stmtPagos = $this->db->prepare("SELECT * FROM cliente_pagos WHERE servicio_id = ? ORDER BY fecha_pago DESC, created_at DESC");
            foreach ($servicios as &$srv) {
                $stmtPagos->execute([$srv['id']]);
                $srv['pagos_historial'] = $stmtPagos->fetchAll();
            }
            $stmtAmort = $this->db->prepare("
                SELECT *, 
                       CASE WHEN fecha_esperada < CURRENT_DATE() AND estado = 'pendiente' THEN 1 ELSE 0 END as es_vencido 
                FROM servicio_amortizacion 
                WHERE servicio_id = ? 
                ORDER BY numero_pago ASC
            ");
            foreach ($servicios as &$srv) {
                $stmtAmort->execute([$srv['id']]);
                $srv['amortizaciones'] = $stmtAmort->fetchAll();
            }
            $cliente['servicios'] = $servicios;

            // Documentos del cliente
            $stmtDocs = $this->db->prepare("SELECT * FROM cliente_documentos WHERE cliente_id = ? ORDER BY uploaded_at DESC");
            $stmtDocs->execute([$id]);
            $cliente['documentos'] = $stmtDocs->fetchAll();

            return $cliente;

        } catch (PDOException $e) {
            error_log("getClienteById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un cliente por su ID de Usuario de login
     */
    public function getClienteByUserId($user_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.username, u.role 
                FROM clientes c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $cliente = $stmt->fetch();

            if (!$cliente)
                return false;

            // Servicios del cliente con la suma de sus pagos reales
            $stmtSrv = $this->db->prepare("
                SELECT cs.*, 
                       COALESCE((SELECT SUM(monto_pagado) FROM cliente_pagos WHERE servicio_id = cs.id), 0) as total_pagado
                FROM cliente_servicios cs 
                WHERE cs.cliente_id = ? 
                ORDER BY cs.created_at DESC
            ");
            $stmtSrv->execute([$cliente['id']]);
            $servicios = $stmtSrv->fetchAll();

            $stmtPagos = $this->db->prepare("SELECT * FROM cliente_pagos WHERE servicio_id = ? ORDER BY fecha_pago DESC, created_at DESC");
            foreach ($servicios as &$srv) {
                $stmtPagos->execute([$srv['id']]);
                $srv['pagos_historial'] = $stmtPagos->fetchAll();
            }
            $stmtAmort = $this->db->prepare("
                SELECT *, 
                       CASE WHEN fecha_esperada < CURRENT_DATE() AND estado = 'pendiente' THEN 1 ELSE 0 END as es_vencido 
                FROM servicio_amortizacion 
                WHERE servicio_id = ? 
                ORDER BY numero_pago ASC
            ");
            foreach ($servicios as &$srv) {
                $stmtAmort->execute([$srv['id']]);
                $srv['amortizaciones'] = $stmtAmort->fetchAll();
            }
            $cliente['servicios'] = $servicios;

            // Documentos del cliente
            $stmtDocs = $this->db->prepare("SELECT * FROM cliente_documentos WHERE cliente_id = ? ORDER BY uploaded_at DESC");
            $stmtDocs->execute([$cliente['id']]);
            $cliente['documentos'] = $stmtDocs->fetchAll();

            return $cliente;

        } catch (PDOException $e) {
            error_log("getClienteByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un cliente y su usuario asociado.
     */
    public function createCliente($datos)
    {
        try {
            $this->db->beginTransaction();

            // 1. Crear el usuario para el cliente
            // Generamos una contraseña por defecto (ej: DNI, Telefono o algo genérico. Aquí usaremos un default 'vizone2024')
            $defaultPassword = 'password123';
            $resUser = $this->authService->createUser($datos['username'], $defaultPassword, 'cliente');

            if (!$resUser['success']) {
                $this->db->rollBack();
                return $resUser; // Retorna el error si el username ya existe
            }

            // Obtener el ID del nuevo usuario insertado
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$datos['username']]);
            $user_id = $stmt->fetchColumn();

            // 2. Insertar los datos del cliente
            $insertCli = $this->db->prepare("
                INSERT INTO clientes (user_id, nombre_empresa, contacto_principal, telefono, email) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $insertCli->execute([
                $user_id,
                $datos['nombre_empresa'],
                $datos['contacto_principal'],
                $datos['telefono'],
                $datos['email']
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Cliente creado correctamente. Contraseña inicial: password123'];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("createCliente Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear el cliente.'];
        }
    }

    /**
     * Añade un nuevo servicio financiero/proyecto a un cliente
     */
    public function addServicio($datos)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cliente_servicios (
                    cliente_id, tipo_servicio, nombre_proyecto, tipo_pago, 
                    costo_total, pago_inicial, 
                    mensualidad_financiamiento, meses_financiamiento, 
                    es_recurrente, frecuencia_pago, fecha_proximo_pago,
                    incluye_iva
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $esRecurrente = intval($datos['es_recurrente'] ?? 0);

            $result = $stmt->execute([
                $datos['cliente_id'],
                $datos['tipo_servicio'] ?? 'Otro',
                $datos['nombre_proyecto'],
                $datos['tipo_pago'] ?? 'unico',

                floatval($datos['costo_total'] ?? 0),
                floatval($datos['pago_inicial'] ?? 0),
                floatval($datos['mensualidad_financiamiento'] ?? 0),
                intval($datos['meses_financiamiento'] ?? 0),

                $esRecurrente,
                (!empty($datos['frecuencia_pago']) && $datos['frecuencia_pago'] !== 'ninguno') ? $datos['frecuencia_pago'] : 'ninguno',
                !empty($datos['fecha_proximo_pago']) ? $datos['fecha_proximo_pago'] : null,
                intval($datos['incluye_iva'] ?? 0)
            ]);

            if ($result) {
                $servicio_id = $this->db->lastInsertId();
                if (($datos['tipo_pago'] ?? 'unico') === 'varios') {
                    $this->generarAmortizacion($servicio_id, $datos);
                } elseif ($esRecurrente === 1 && !empty($datos['fecha_proximo_pago'])) {
                    $this->generarCobrosRecurrentes($servicio_id, $datos);
                }
            }

            return ['success' => $result, 'message' => $result ? 'Servicio/Proyecto añadido al cliente.' : 'Error al guardar el servicio.'];
        } catch (PDOException $e) {
            error_log("addServicio Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al guardar el servicio.'];
        }
    }

    /**
     * Elimina un servicio/proyecto de un cliente
     */
    public function deleteServicio($servicio_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM cliente_servicios WHERE id = ?");
            $result = $stmt->execute([$servicio_id]);
            // Amortizacion se elimina en cascada en mysql gracias a ON DELETE CASCADE
            return ['success' => $result, 'message' => 'Servicio eliminado correctamente.'];
        } catch (PDOException $e) {
            error_log("deleteServicio Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el servicio.'];
        }
    }

    /**
     * Sube un documento de cliente y lo registra en la base de datos
     */
    public function addDocumento($cliente_id, $file)
    {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Error al subir el archivo físico.'];
            }

            // Validar extensión
            $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'md', 'xlsx'];
            $fileInfo = pathinfo($file['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');

            if (!in_array($extension, $allowedExtensions)) {
                return ['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten PDF, DOC, TXT, MD, XLSX.'];
            }

            // Crear directorio cliente si no existe
            $uploadDir = BACK_PATH . 'uploads/clientes/' . $cliente_id . '/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Nombre seguro para el archivo lógico
            $safeName = md5(uniqid()) . '.' . $extension;
            $destination = $uploadDir . $safeName;

            // Mover el archivo físico
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return ['success' => false, 'message' => 'Error al mover el archivo al servidor. Verifique permisos.'];
            }

            // Insertar en BD
            $stmt = $this->db->prepare("
                INSERT INTO cliente_documentos (cliente_id, nombre_archivo, nombre_original, ruta_fisica) 
                VALUES (?, ?, ?, ?)
            ");

            // La ruta_fisica se guarda como relativa a BACK_PATH/uploads para portabilidad
            $ruta_relativa = 'clientes/' . $cliente_id . '/' . $safeName;

            $result = $stmt->execute([
                $cliente_id,
                $safeName,
                $file['name'], // Nombre original completo (ej: mi_manual_v2.pdf)
                $ruta_relativa
            ]);

            return ['success' => $result, 'message' => $result ? 'Documento subido con éxito.' : 'Archivo subido, pero error en BD.'];

        } catch (PDOException $e) {
            error_log("addDocumento Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al guardar el registro del documento.'];
        }
    }

    /**
     * Actualiza un servicio/proyecto de un cliente
     */
    public function updateServicio($datos)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE cliente_servicios SET
                    tipo_servicio = ?, nombre_proyecto = ?, tipo_pago = ?, 
                    costo_total = ?, pago_inicial = ?, 
                    mensualidad_financiamiento = ?, meses_financiamiento = ?, 
                    es_recurrente = ?, frecuencia_pago = ?, fecha_proximo_pago = ?,
                    incluye_iva = ?
                WHERE id = ? AND cliente_id = ?
            ");

            $esRecurrente = intval($datos['es_recurrente'] ?? 0);

            $result = $stmt->execute([
                $datos['tipo_servicio'] ?? 'Otro',
                $datos['nombre_proyecto'],
                $datos['tipo_pago'] ?? 'unico',

                floatval($datos['costo_total'] ?? 0),
                floatval($datos['pago_inicial'] ?? 0),
                floatval($datos['mensualidad_financiamiento'] ?? 0),
                intval($datos['meses_financiamiento'] ?? 0),

                $esRecurrente,
                (!empty($datos['frecuencia_pago']) && $datos['frecuencia_pago'] !== 'ninguno') ? $datos['frecuencia_pago'] : 'ninguno',
                !empty($datos['fecha_proximo_pago']) ? $datos['fecha_proximo_pago'] : null,
                intval($datos['incluye_iva'] ?? 0),

                $datos['id'],
                $datos['cliente_id']
            ]);

            if ($result && ($datos['tipo_pago'] ?? 'unico') === 'varios') {
                $this->generarAmortizacion($datos['id'], $datos);
            } elseif ($result && $esRecurrente === 1 && !empty($datos['fecha_proximo_pago'])) {
                // Recurrente: regenerar cobros
                $this->generarCobrosRecurrentes($datos['id'], $datos);
            } elseif ($result) {
                // Si ya no es recurrente ni varios, limpiar amortizaciones
                $stmtDel = $this->db->prepare("DELETE FROM servicio_amortizacion WHERE servicio_id = ?");
                $stmtDel->execute([$datos['id']]);
            }

            return ['success' => $result, 'message' => $result ? 'Servicio actualizado correctamente.' : 'Error al actualizar el servicio.'];
        } catch (PDOException $e) {
            error_log("updateServicio Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al actualizar el servicio.'];
        }
    }

    /**
     * Añade un nuevo pago a un servicio
     */
    public function addPago($datos)
    {
        try {
            $this->db->beginTransaction();

            $comprobante_url = null;
            if (is_array($datos['comprobante_file'] ?? null) && !empty($datos['comprobante_file']['name']) && $datos['comprobante_file']['error'] === UPLOAD_ERR_OK) {
                $file = $datos['comprobante_file'];
                $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
                $fileInfo = pathinfo($file['name']);
                $extension = strtolower($fileInfo['extension'] ?? '');

                if (in_array($extension, $allowedExtensions)) {
                    $uploadDir = BACK_PATH . 'uploads/comprobantes/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $safeName = md5(uniqid()) . '.' . $extension;
                    $destination = $uploadDir . $safeName;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $comprobante_url = 'comprobantes/' . $safeName;
                    }
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO cliente_pagos (servicio_id, monto_pagado, concepto, amortizacion_id, fecha_pago, metodo_pago, referencia, comprobante_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $concepto = $datos['concepto'] ?? 'Abono Libre';
            $amortizacion_id = !empty($datos['amortizacion_id']) ? $datos['amortizacion_id'] : null;

            $result = $stmt->execute([
                $datos['servicio_id'],
                $datos['monto_pagado'],
                $concepto,
                $amortizacion_id,
                $datos['fecha_pago'],
                $datos['metodo_pago'] ?? 'Transferencia',
                $datos['referencia'] ?? null,
                $comprobante_url
            ]);

            if ($result && $amortizacion_id) {
                $stmtAmort = $this->db->prepare("UPDATE servicio_amortizacion SET estado = 'pagado' WHERE id = ?");
                $stmtAmort->execute([$amortizacion_id]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Pago registrado correctamente.'];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("addPago Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al registrar el pago.'];
        }
    }

    public function deletePago($pago_id)
    {
        try {
            $this->db->beginTransaction();

            // Buscar si estaba ligado a una amortización
            $stmt = $this->db->prepare("SELECT amortizacion_id FROM cliente_pagos WHERE id = ?");
            $stmt->execute([$pago_id]);
            $pagoInfo = $stmt->fetch();

            if ($pagoInfo && !empty($pagoInfo['amortizacion_id'])) {
                // Revertir a pendiente
                $stmtRev = $this->db->prepare("UPDATE servicio_amortizacion SET estado = 'pendiente' WHERE id = ?");
                $stmtRev->execute([$pagoInfo['amortizacion_id']]);
            }

            $stmtDel = $this->db->prepare("DELETE FROM cliente_pagos WHERE id = ?");
            $result = $stmtDel->execute([$pago_id]);

            $this->db->commit();
            return ['success' => $result, 'message' => 'Pago revocado correctamente.'];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("deletePago Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al revocar el pago.'];
        }
    }

    /**
     * Obtiene todos los pagos de un servicio en especifico
     */
    public function getPagosByServicio($servicio_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cliente_pagos WHERE servicio_id = ? ORDER BY fecha_pago DESC, created_at DESC");
            $stmt->execute([$servicio_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getPagosByServicio Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene absolutamente todos los pagos del sistema (Global / Dashboard)
     */
    public function getAllPagosGlobal()
    {
        try {
            $sql = "
                SELECT 
                    cp.id as pago_id,
                    cp.monto_pagado,
                    cp.concepto,
                    cp.fecha_pago,
                    cp.metodo_pago,
                    cp.referencia,
                    cp.comprobante_url,
                    cp.factura_pdf_url,
                    cp.factura_xml_url,
                    cs.id as servicio_id,
                    cs.nombre_proyecto,
                    c.id as cliente_id,
                    c.nombre_empresa,
                    cs.cliente_id as srv_cliente_id
                FROM cliente_pagos cp
                JOIN cliente_servicios cs ON cp.servicio_id = cs.id
                JOIN clientes c ON cs.cliente_id = c.id
                ORDER BY cp.fecha_pago DESC, cp.created_at DESC
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllPagosGlobal Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los servicios activos del sistema (Global / Dashboard)
     */
    public function getAllServiciosActivos()
    {
        try {
            $sql = "
                SELECT 
                    cs.id, 
                    cs.nombre_proyecto, 
                    cs.costo_total,
                    c.nombre_empresa
                FROM cliente_servicios cs
                JOIN clientes c ON cs.cliente_id = c.id
                WHERE cs.estado = 'Activo'
                ORDER BY c.nombre_empresa ASC, cs.nombre_proyecto ASC
            ";
            $stmt = $this->db->query($sql);
            $servicios = $stmt->fetchAll();

            // Calcular el restante a liquidar para cada servicio activo
            foreach ($servicios as &$srv) {
                $stmtPagos = $this->db->prepare("SELECT SUM(monto_pagado) as total_pagado FROM cliente_pagos WHERE servicio_id = ?");
                $stmtPagos->execute([$srv['id']]);
                $pagos = $stmtPagos->fetch();
                $totalPagado = $pagos['total_pagado'] ?? 0;
                $srv['restante'] = max(0, $srv['costo_total'] - $totalPagado);
            }

            return $servicios;
        } catch (PDOException $e) {
            error_log("getAllServiciosActivos Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea un perfil de cliente básico vinculado a un usuario existente.
     */
    public function createClienteFromUser($user_id, $username)
    {
        try {
            $insertCli = $this->db->prepare("
                INSERT INTO clientes (user_id, nombre_empresa) 
                VALUES (?, ?)
            ");
            $insertCli->execute([
                $user_id,
                $username // Por defecto usamos el username original
            ]);
            return ['success' => true, 'message' => 'Cliente vinculado creado.'];
        } catch (PDOException $e) {
            error_log("createClienteFromUser Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear la vinculacion de cliente.'];
        }
    }

    /**
     * Actualiza el perfil comercial de un cliente y su usuario en cascada si cambia el username.
     */
    public function updateProfile($datos)
    {
        try {
            $this->db->beginTransaction();

            $id = $datos['cliente_id'];
            $username = $datos['username'];

            // 1. Obtener cliente actual para revisar su user_id
            $stmt = $this->db->prepare("SELECT user_id FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $user_id = $stmt->fetchColumn();

            // 2. Si se cambió el username y tiene user_id, validarlo y actualizar
            if ($user_id) {
                // Verificar posible colisión
                $chk = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $chk->execute([$username, $user_id]);
                if ($chk->fetch()) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'El nombre de usuario elegido ya está en uso.'];
                }

                // Update username in users list
                $usrUp = $this->db->prepare("UPDATE users SET username = ? WHERE id = ?");
                $usrUp->execute([$username, $user_id]);
            }

            // 3. Actualizar la info general del cliente
            $stmtUpd = $this->db->prepare("
                UPDATE clientes 
                SET nombre_empresa = ?, contacto_principal = ?, telefono = ?, email = ? 
                WHERE id = ?
            ");
            $result = $stmtUpd->execute([
                $datos['nombre_empresa'],
                $datos['contacto_principal'],
                $datos['telefono'],
                $datos['email'],
                $id
            ]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Perfil del cliente actualizado correctamente.'];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("updateProfile Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al actualizar el perfil.'];
        }
    }

    /**
     * Elimina un cliente y por cascada sus servicios y documentos.
     * También pregunta si se quiere borrar el usuario asociado, pero aquí solo anularemos/dejaremos o lo borramos.
     * Dado que el user_id está en la tabla clientes, podemos borrar ambos.
     */
    public function deleteCliente($id)
    {
        try {
            $this->db->beginTransaction();

            // Opcional: Obtener el user_id para eliminar el usuario también
            $stmt = $this->db->prepare("SELECT user_id FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $user_id = $stmt->fetchColumn();

            // Eliminar el cliente (Servicios y Documentos se borran en cascada asumiendo ON DELETE CASCADE,
            // pero si no, habría que borrarlos a mano. En init_db.php estan ON DELETE CASCADE)
            $stmtDelCli = $this->db->prepare("DELETE FROM clientes WHERE id = ?");
            $stmtDelCli->execute([$id]);

            // Eliminar el usuario si lo tiene asociado
            if ($user_id) {
                $stmtDelUser = $this->db->prepare("DELETE FROM users WHERE id = ?");
                $stmtDelUser->execute([$user_id]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Cliente eliminado correctamente.'];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("deleteCliente Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el cliente.'];
        }
    }

    /**
     * Genera cobros recurrentes para servicios con es_recurrente = 1.
     * Crea entradas en servicio_amortizacion desde la fecha_proximo_pago hasta
     * cubrir todos los períodos vencidos más el siguiente período pendiente.
     */
    private function generarCobrosRecurrentes($servicio_id, $datos) {
        // Borrar cobros anteriores pendientes (conservar los pagados)
        $stmtDelPend = $this->db->prepare("DELETE FROM servicio_amortizacion WHERE servicio_id = ? AND estado = 'pendiente'");
        $stmtDelPend->execute([$servicio_id]);

        $frecuencia   = !empty($datos['frecuencia_pago']) ? $datos['frecuencia_pago'] : 'mensual';
        $fecha_inicio = !empty($datos['fecha_proximo_pago']) ? $datos['fecha_proximo_pago'] : null;
        $monto        = floatval($datos['costo_total'] ?? 0);

        // Requerir al menos una fecha de inicio
        if (empty($fecha_inicio)) return;

        // Calcular el intervalo en función de la frecuencia
        $interval_map = [
            'quincenal' => '+15 days',
            'mensual'   => '+1 month',
            'semestral' => '+6 months',
            'anual'     => '+1 year',
        ];
        $interval = $interval_map[$frecuencia] ?? '+1 month';

        // Normalizar hora para comparaciones de fecha limpias
        $hoy = new \DateTime();
        $hoy->setTime(23, 59, 59); // Final del día de hoy
        $fecha = new \DateTime($fecha_inicio);
        $fecha->setTime(0, 0, 0);

        // Obtener el último número de pago ya registrado (pagados)
        $stmtMax = $this->db->prepare("SELECT COALESCE(MAX(numero_pago), 0) FROM servicio_amortizacion WHERE servicio_id = ?");
        $stmtMax->execute([$servicio_id]);
        $ultimo_num = (int)$stmtMax->fetchColumn();

        $stmtIns = $this->db->prepare(
            "INSERT INTO servicio_amortizacion (servicio_id, numero_pago, monto_esperado, fecha_esperada) VALUES (?, ?, ?, ?)"
        );

        $pago_num = $ultimo_num + 1;
        $limite = $ultimo_num + 600;

        // Generar todos los cobros vencidos (desde fecha inicio hasta hoy inclusive)
        // más el siguiente cobro futuro pendiente.
        while ($pago_num <= $limite) {
            $fecha_str = $fecha->format('Y-m-d');

            $stmtIns->execute([$servicio_id, $pago_num, $monto, $fecha_str]);
            $pago_num++;

            // Avanzar al siguiente período ANTES de chequear si ya pasamos de hoy
            $fecha->modify($interval);

            // Si el siguiente período ya está en el futuro, terminamos
            if ($fecha > $hoy) {
                // Insertar ese primer cobro futuro
                $fecha_str = $fecha->format('Y-m-d');
                $stmtIns->execute([$servicio_id, $pago_num, $monto, $fecha_str]);
                break;
            }
        }
    }

    private function generarAmortizacion($servicio_id, $datos) {
        $stmtDel = $this->db->prepare("DELETE FROM servicio_amortizacion WHERE servicio_id = ?");
        $stmtDel->execute([$servicio_id]);
        
        $costo = floatval($datos['costo_total'] ?? 0);
        $anticipo = floatval($datos['pago_inicial'] ?? 0);
        $mensualidad = floatval($datos['mensualidad_financiamiento'] ?? 0);
        
        $restante = $costo - $anticipo;
        if ($restante <= 0 || $mensualidad <= 0) return;
        
        $fecha_str = !empty($datos['fecha_proximo_pago']) ? $datos['fecha_proximo_pago'] : date('Y-m-d');
        
        $pago_num = 1;
        $stmtIns = $this->db->prepare("INSERT INTO servicio_amortizacion (servicio_id, numero_pago, monto_esperado, fecha_esperada) VALUES (?, ?, ?, ?)");
        
        while ($restante > 0) {
            $monto_pago = $mensualidad;
            if ($restante < $mensualidad) {
                $monto_pago = $restante;
            }
            $restante -= $monto_pago;
            if ($restante < 0.01) $restante = 0;
            
            $stmtIns->execute([$servicio_id, $pago_num, $monto_pago, $fecha_str]);
            
            // Incrementar mes (1 exact month)
            $fecha_str = date('Y-m-d', strtotime('+1 month', strtotime($fecha_str)));
            $pago_num++;
        }
    }

    /**
     * Actualiza los datos editables de un pago
     */
    public function updatePago($pago_id, $datos)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE cliente_pagos SET
                    concepto = ?, monto_pagado = ?, fecha_pago = ?, metodo_pago = ?, referencia = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $datos['concepto'],
                $datos['monto_pagado'],
                $datos['fecha_pago'],
                $datos['metodo_pago'],
                $datos['referencia'],
                $pago_id
            ]);
            return ['success' => $result, 'message' => $result ? 'Pago actualizado correctamente.' : 'Error al actualizar el pago.'];
        } catch (PDOException $e) {
            error_log("updatePago Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al actualizar el pago.'];
        }
    }

    /**
     * Guarda la Factura PDF o XML vinculada a un pago
     */
    public function saveFactura($pago_id, $tipo, $file)
    {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK || empty($file['name'])) {
                return ['success' => false, 'message' => 'Error al recibir el archivo.'];
            }

            if ($tipo === 'pdf') {
                $allowedExt = ['pdf'];
                $column = 'factura_pdf_url';
                $label = 'Factura PDF';
            } elseif ($tipo === 'xml') {
                $allowedExt = ['xml'];
                $column = 'factura_xml_url';
                $label = 'XML CFDI';
            } elseif ($tipo === 'comprobante') {
                $allowedExt = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
                $column = 'comprobante_url';
                $label = 'Comprobante de Pago';
            } else {
                return ['success' => false, 'message' => 'Tipo de document no reconocido.'];
            }

            $fileInfo = pathinfo($file['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');

            if (!in_array($extension, $allowedExt)) {
                return ['success' => false, 'message' => "Solo se permite el formato .{$allowedExt[0]} para $label."];
            }

            $uploadDir = BACK_PATH . 'uploads/facturas/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = md5(uniqid()) . '.' . $extension;
            $destination = $uploadDir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return ['success' => false, 'message' => 'Error al mover el archivo al servidor.'];
            }

            $ruta_relativa = 'facturas/' . $safeName;
            $stmt = $this->db->prepare("UPDATE cliente_pagos SET {$column} = ? WHERE id = ?");
            $result = $stmt->execute([$ruta_relativa, $pago_id]);

            return ['success' => $result, 'message' => $result ? "$label cargada correctamente." : "Error al guardar la ruta en la base de datos."];
        } catch (PDOException $e) {
            error_log("saveFactura Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al guardar la factura.'];
        }
    }

}
?>