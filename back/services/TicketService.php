<?php

require_once BACK_PATH . 'config/database.php';

class TicketService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /* ─── LISTADO ──────────────────────────────────────────────────── */

    public function getAll($filtros = [])
    {
        try {
            $where  = ['1=1'];
            $params = [];

            if (!empty($filtros['tipo'])) {
                $where[]  = 't.tipo = ?';
                $params[] = $filtros['tipo'];
            }
            if (!empty($filtros['estado'])) {
                $where[]  = 't.estado = ?';
                $params[] = $filtros['estado'];
            }
            if (!empty($filtros['cliente_id'])) {
                $where[]  = 't.cliente_id = ?';
                $params[] = $filtros['cliente_id'];
            }

            $whereStr = implode(' AND ', $where);

            $stmt = $this->db->prepare("
                SELECT
                    t.*,
                    c.nombre_empresa,
                    cs.nombre_proyecto,
                    u.username AS asignado_nombre
                FROM tickets t
                LEFT JOIN clientes c  ON t.cliente_id  = c.id
                LEFT JOIN cliente_servicios cs ON t.servicio_id = cs.id
                LEFT JOIN users u     ON t.asignado_a  = u.id
                WHERE {$whereStr}
                ORDER BY
                    FIELD(t.estado,'abierto','en_progreso','revision','cerrado'),
                    FIELD(t.prioridad,'critica','alta','media','baja'),
                    t.created_at DESC
            ");
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TicketService::getAll — ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    t.*,
                    c.nombre_empresa,
                    cs.nombre_proyecto,
                    u.username AS asignado_nombre
                FROM tickets t
                LEFT JOIN clientes c  ON t.cliente_id  = c.id
                LEFT JOIN cliente_servicios cs ON t.servicio_id = cs.id
                LEFT JOIN users u     ON t.asignado_a  = u.id
                WHERE t.id = ?
            ");
            $stmt->execute([$id]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ticket) return null;

            // Comentarios
            $stmtC = $this->db->prepare("
                SELECT tc.*, u.username
                FROM ticket_comentarios tc
                LEFT JOIN users u ON tc.user_id = u.id
                WHERE tc.ticket_id = ?
                ORDER BY tc.created_at ASC
            ");
            $stmtC->execute([$id]);
            $ticket['comentarios'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);

            return $ticket;
        } catch (PDOException $e) {
            error_log('TicketService::getById — ' . $e->getMessage());
            return null;
        }
    }

    /* ─── KPIs ─────────────────────────────────────────────────────── */

    public function getKpis()
    {
        try {
            $stmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    SUM(estado = 'abierto')      as abiertos,
                    SUM(estado = 'en_progreso')  as en_progreso,
                    SUM(estado = 'revision')     as revision,
                    SUM(estado = 'cerrado')      as cerrados,
                    SUM(tipo = 'sprint')         as sprints,
                    SUM(tipo = 'update')         as updates,
                    SUM(tipo = 'soporte')        as soportes,
                    SUM(prioridad = 'critica' AND estado != 'cerrado') as criticos
                FROM tickets
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('TicketService::getKpis — ' . $e->getMessage());
            return [];
        }
    }

    /* ─── CREAR ─────────────────────────────────────────────────────── */

    public function create($datos)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO tickets
                    (tipo, titulo, descripcion, servicio_id, proyecto_externo,
                     cliente_id, prioridad, estado, fecha_inicio, fecha_limite, porcentaje, asignado_a)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $ok = $stmt->execute([
                $datos['tipo'],
                $datos['titulo'],
                $datos['descripcion']      ?? null,
                $datos['servicio_id']      ?: null,
                $datos['proyecto_externo'] ?: null,
                $datos['cliente_id']       ?: null,
                $datos['prioridad']        ?? 'media',
                $datos['estado']           ?? 'abierto',
                $datos['fecha_inicio']     ?: null,
                $datos['fecha_limite']     ?: null,
                $datos['porcentaje']       ?? 0,
                $datos['asignado_a']       ?: null,
            ]);
            return ['success' => $ok, 'id' => $this->db->lastInsertId(),
                    'message' => $ok ? 'Ticket creado.' : 'Error al crear el ticket.'];
        } catch (PDOException $e) {
            error_log('TicketService::create — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /* ─── ACTUALIZAR ────────────────────────────────────────────────── */

    public function update($id, $datos)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tickets SET
                    titulo           = ?,
                    descripcion      = ?,
                    tipo             = ?,
                    prioridad        = ?,
                    estado           = ?,
                    servicio_id      = ?,
                    proyecto_externo = ?,
                    cliente_id       = ?,
                    fecha_inicio     = ?,
                    fecha_limite     = ?,
                    porcentaje       = ?,
                    asignado_a       = ?
                WHERE id = ?
            ");
            $ok = $stmt->execute([
                $datos['titulo'],
                $datos['descripcion']      ?? null,
                $datos['tipo'],
                $datos['prioridad']        ?? 'media',
                $datos['estado']           ?? 'abierto',
                $datos['servicio_id']      ?: null,
                $datos['proyecto_externo'] ?: null,
                $datos['cliente_id']       ?: null,
                $datos['fecha_inicio']     ?: null,
                $datos['fecha_limite']     ?: null,
                $datos['porcentaje']       ?? 0,
                $datos['asignado_a']       ?: null,
                $id,
            ]);
            return ['success' => $ok, 'message' => $ok ? 'Ticket actualizado.' : 'Error al actualizar.'];
        } catch (PDOException $e) {
            error_log('TicketService::update — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /* Cambio rápido de estado (drag & drop Kanban) */
    public function updateEstado($id, $estado, $porcentaje = null)
    {
        try {
            if ($porcentaje !== null) {
                $stmt = $this->db->prepare("UPDATE tickets SET estado=?, porcentaje=? WHERE id=?");
                $ok = $stmt->execute([$estado, $porcentaje, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE tickets SET estado=? WHERE id=?");
                $ok = $stmt->execute([$estado, $id]);
            }
            return ['success' => $ok, 'message' => 'Estado actualizado.'];
        } catch (PDOException $e) {
            error_log('TicketService::updateEstado — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /* ─── COMENTARIOS ───────────────────────────────────────────────── */

    public function addComentario($ticket_id, $user_id, $mensaje, $es_interno = 0)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ticket_comentarios (ticket_id, user_id, mensaje, es_interno)
                VALUES (?,?,?,?)
            ");
            $ok = $stmt->execute([$ticket_id, $user_id, $mensaje, $es_interno]);
            return ['success' => $ok, 'message' => $ok ? 'Comentario guardado.' : 'Error al guardar.'];
        } catch (PDOException $e) {
            error_log('TicketService::addComentario — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /* ─── ELIMINAR ──────────────────────────────────────────────────── */

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tickets WHERE id = ?");
            $ok = $stmt->execute([$id]);
            return ['success' => $ok, 'message' => $ok ? 'Ticket eliminado.' : 'Error al eliminar.'];
        } catch (PDOException $e) {
            error_log('TicketService::delete — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /* ─── AUXILIARES ────────────────────────────────────────────────── */

    /** Lista de servicios activos para el selector de proyecto */
    public function getServiciosActivos()
    {
        try {
            $stmt = $this->db->query("
                SELECT cs.id, cs.nombre_proyecto, cs.tipo_servicio, c.nombre_empresa
                FROM cliente_servicios cs
                JOIN clientes c ON cs.cliente_id = c.id
                WHERE cs.estado = 'activo'
                ORDER BY c.nombre_empresa, cs.nombre_proyecto
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /** Lista de admins para asignación */
    public function getAdmins()
    {
        try {
            $stmt = $this->db->query("SELECT id, username FROM users WHERE role = 'admin' ORDER BY username");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /** Lista de clientes */
    public function getClientes()
    {
        try {
            $stmt = $this->db->query("SELECT id, nombre_empresa FROM clientes ORDER BY nombre_empresa");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
