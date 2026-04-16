<?php

require_once BACK_PATH . 'services/TicketService.php';

class TicketController
{
    private $ticketService;

    public function __construct()
    {
        $this->ticketService = new TicketService();

        // Middleware: requiere sesión iniciada
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /* ─── HELPERS ───────────────────────────────────────────────────── */

    private function jsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->jsonResponse(false, 'No autorizado.');
        }
    }

    /* ─── VISTA PRINCIPAL ───────────────────────────────────────────── */

    public function index()
    {
        $filtros = [
            'tipo'       => $_GET['tipo']       ?? '',
            'estado'     => $_GET['estado']     ?? '',
            'cliente_id' => $_GET['cliente_id'] ?? '',
        ];

        $tickets          = $this->ticketService->getAll($filtros);
        $kpis             = $this->ticketService->getKpis();
        $serviciosActivos = $this->ticketService->getServiciosActivos();
        $admins           = $this->ticketService->getAdmins();
        $clientes         = $this->ticketService->getClientes();
        $filtrosActivos   = $filtros;

        $pageTitle   = 'Tickets y Soporte · Vizone';
        $activeModule = 'tickets';
        $activeRole   = $_SESSION['role'] ?? 'admin';
        $activeUsername = $_SESSION['username'] ?? '';

        $viewContent = FRONT_PATH . 'dashboard/tickets.php';
        require_once FRONT_PATH . 'dashboard/layout.php';
    }

    /* ─── CREAR TICKET ──────────────────────────────────────────────── */

    public function save()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $datos = [
            'tipo'             => $_POST['tipo']             ?? '',
            'titulo'           => trim($_POST['titulo']      ?? ''),
            'descripcion'      => trim($_POST['descripcion'] ?? ''),
            'servicio_id'      => $_POST['servicio_id']      ?? null,
            'proyecto_externo' => trim($_POST['proyecto_externo'] ?? ''),
            'cliente_id'       => $_POST['cliente_id']       ?? null,
            'prioridad'        => $_POST['prioridad']        ?? 'media',
            'estado'           => 'abierto',
            'fecha_inicio'     => $_POST['fecha_inicio']     ?? null,
            'fecha_limite'     => $_POST['fecha_limite']     ?? null,
            'porcentaje'       => $_POST['porcentaje']       ?? 0,
            'asignado_a'       => $_POST['asignado_a']       ?? null,
        ];

        if (empty($datos['tipo']) || empty($datos['titulo'])) {
            return $this->jsonResponse(false, 'Tipo y título son obligatorios.');
        }

        $result = $this->ticketService->create($datos);
        return $this->jsonResponse($result['success'], $result['message'], ['id' => $result['id'] ?? null]);
    }

    /* ─── ACTUALIZAR TICKET ─────────────────────────────────────────── */

    public function update()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['ticket_id'] ?? null;
        if (!$id) return $this->jsonResponse(false, 'ID de ticket no proporcionado.');

        $datos = [
            'tipo'             => $_POST['tipo']             ?? 'soporte',
            'titulo'           => trim($_POST['titulo']      ?? ''),
            'descripcion'      => trim($_POST['descripcion'] ?? ''),
            'servicio_id'      => $_POST['servicio_id']      ?? null,
            'proyecto_externo' => trim($_POST['proyecto_externo'] ?? ''),
            'cliente_id'       => $_POST['cliente_id']       ?? null,
            'prioridad'        => $_POST['prioridad']        ?? 'media',
            'estado'           => $_POST['estado']           ?? 'abierto',
            'fecha_inicio'     => $_POST['fecha_inicio']     ?? null,
            'fecha_limite'     => $_POST['fecha_limite']     ?? null,
            'porcentaje'       => $_POST['porcentaje']       ?? 0,
            'asignado_a'       => $_POST['asignado_a']       ?? null,
        ];

        $result = $this->ticketService->update($id, $datos);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /* ─── CAMBIO RÁPIDO DE ESTADO ───────────────────────────────────── */

    public function updateEstado()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id         = $_POST['ticket_id'] ?? null;
        $estado     = $_POST['estado']    ?? null;
        $porcentaje = isset($_POST['porcentaje']) ? (int)$_POST['porcentaje'] : null;

        if (!$id || !$estado) return $this->jsonResponse(false, 'Datos incompletos.');

        $result = $this->ticketService->updateEstado($id, $estado, $porcentaje);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /* ─── COMENTARIOS ───────────────────────────────────────────────── */

    public function saveComentario()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $user_id    = $_SESSION['user_id'] ?? null;
        $ticket_id  = $_POST['ticket_id']  ?? null;
        $mensaje    = trim($_POST['mensaje']    ?? '');
        $es_interno = (int)($_POST['es_interno'] ?? 0);

        if (!$ticket_id || empty($mensaje)) {
            return $this->jsonResponse(false, 'Datos incompletos para el comentario.');
        }

        $result = $this->ticketService->addComentario($ticket_id, $user_id, $mensaje, $es_interno);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /* ─── ELIMINAR ──────────────────────────────────────────────────── */

    public function delete()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['ticket_id'] ?? null;
        if (!$id) return $this->jsonResponse(false, 'ID no proporcionado.');

        $result = $this->ticketService->delete($id);
        return $this->jsonResponse($result['success'], $result['message']);
    }
}
