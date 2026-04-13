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
                    es_recurrente, frecuencia_pago, fecha_proximo_pago
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $datos['cliente_id'],
                $datos['tipo_servicio'] ?? 'Otro',
                $datos['nombre_proyecto'],
                $datos['tipo_pago'] ?? 'unico',

                empty($datos['costo_total']) ? 0 : $datos['costo_total'],
                empty($datos['pago_inicial']) ? 0 : $datos['pago_inicial'],
                empty($datos['mensualidad_financiamiento']) ? 0 : $datos['mensualidad_financiamiento'],
                empty($datos['meses_financiamiento']) ? 0 : $datos['meses_financiamiento'],

                empty($datos['es_recurrente']) ? 0 : 1,
                empty($datos['frecuencia_pago']) ? 'ninguno' : $datos['frecuencia_pago'],
                empty($datos['fecha_proximo_pago']) ? null : $datos['fecha_proximo_pago']
            ]);

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
                    es_recurrente = ?, frecuencia_pago = ?, fecha_proximo_pago = ?
                WHERE id = ? AND cliente_id = ?
            ");

            $result = $stmt->execute([
                $datos['tipo_servicio'] ?? 'Otro',
                $datos['nombre_proyecto'],
                $datos['tipo_pago'] ?? 'unico',

                empty($datos['costo_total']) ? 0 : $datos['costo_total'],
                empty($datos['pago_inicial']) ? 0 : $datos['pago_inicial'],
                empty($datos['mensualidad_financiamiento']) ? 0 : $datos['mensualidad_financiamiento'],
                empty($datos['meses_financiamiento']) ? 0 : $datos['meses_financiamiento'],

                empty($datos['es_recurrente']) ? 0 : 1,
                empty($datos['frecuencia_pago']) ? 'ninguno' : $datos['frecuencia_pago'],
                empty($datos['fecha_proximo_pago']) ? null : $datos['fecha_proximo_pago'],

                $datos['id'],
                $datos['cliente_id']
            ]);

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
            $stmt = $this->db->prepare("
                INSERT INTO cliente_pagos (servicio_id, monto_pagado, fecha_pago, metodo_pago, referencia)
                VALUES (?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $datos['servicio_id'],
                $datos['monto_pagado'],
                $datos['fecha_pago'],
                $datos['metodo_pago'] ?? 'Transferencia',
                $datos['referencia'] ?? null
            ]);
            return ['success' => $result, 'message' => $result ? 'Pago registrado correctamente.' : 'Error al registrar el pago.'];
        } catch (PDOException $e) {
            error_log("addPago Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al registrar el pago.'];
        }
    }

    /**
     * Elimina un pago
     */
    public function deletePago($pago_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM cliente_pagos WHERE id = ?");
            $result = $stmt->execute([$pago_id]);
            return ['success' => $result, 'message' => 'Pago revocado correctamente.'];
        } catch (PDOException $e) {
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
                    cp.fecha_pago,
                    cp.metodo_pago,
                    cp.referencia,
                    cs.id as servicio_id,
                    cs.nombre_proyecto,
                    c.id as cliente_id,
                    c.nombre_empresa
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

}
?>