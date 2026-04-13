<?php
require_once BACK_PATH . 'services/ClienteService.php';
require_once BACK_PATH . 'services/AuthService.php';

/**
 * ClienteController
 * 
 * Gestiona las operaciones CRUD y lógia de negocio para el módulo de Clientes.
 */
class ClienteController
{

    private $clienteService;
    private $authService;

    public function __construct()
    {
        $this->clienteService = new ClienteService();
        $this->authService = new AuthService();

        // Middleware genérico: Asegurar que esté logueado y sea admin
        if (!$this->authService->isLoggedIn()) {
            $this->jsonResponse(false, 'No autorizado. Por favor, inicie sesión.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SESSION['role'] !== 'admin') {
            $this->jsonResponse(false, 'Solo los administradores pueden gestionar clientes.');
        }
    }

    /**
     * Guarda un cliente nuevo.
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $datos = [
            'username' => trim($_POST['username'] ?? ''),
            'nombre_empresa' => trim($_POST['nombre_empresa'] ?? ''),
            'contacto_principal' => trim($_POST['contacto_principal'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        // Validaciones básicas
        if (empty($datos['username']) || empty($datos['nombre_empresa'])) {
            return $this->jsonResponse(false, 'El nombre de usuario y nombre de empresa son obligatorios.');
        }

        $result = $this->clienteService->createCliente($datos);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Guarda un servicio nuevo para un cliente
     */
    public function saveService()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $datos = [
            'cliente_id' => $_POST['cliente_id'] ?? null,
            'nombre_proyecto' => trim($_POST['nombre_proyecto'] ?? ''),
            'tipo_servicio' => $_POST['tipo_servicio'] ?? 'Otro',
            'tipo_pago' => $_POST['tipo_pago'] ?? 'unico',
            'costo_total' => $_POST['costo_total'] ?? 0,
            'pago_inicial' => $_POST['pago_inicial'] ?? 0,
            'mensualidad_financiamiento' => $_POST['mensualidad_financiamiento'] ?? 0,
            'meses_financiamiento' => $_POST['meses_financiamiento'] ?? 0,
            'es_recurrente' => $_POST['es_recurrente'] ?? 0,
            'frecuencia_pago' => $_POST['frecuencia_pago'] ?? 'ninguno',
            'fecha_proximo_pago' => (!empty($_POST['fecha_proximo_pago'])) ? $_POST['fecha_proximo_pago'] : null
        ];

        if (empty($datos['cliente_id']) || empty($datos['nombre_proyecto'])) {
            return $this->jsonResponse(false, 'El ID del cliente y el nombre del proyecto son obligatorios.');
        }

        $result = $this->clienteService->addServicio($datos);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Guarda un documento para el cliente
     */
    public function saveDocument()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $cliente_id = $_POST['cliente_id'] ?? null;
        $file = $_FILES['documento'] ?? null;

        if (empty($cliente_id) || empty($file)) {
            return $this->jsonResponse(false, 'El ID del cliente y el archivo son obligatorios.');
        }

        $result = $this->clienteService->addDocumento($cliente_id, $file);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Elimina un proyecto/servicio por su ID
     */
    public function deleteService()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return $this->jsonResponse(false, 'No autorizado.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'ID no proporcionado.');
        }

        $result = $this->clienteService->deleteServicio($id);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Actualiza un servicio/proyecto por su ID
     */
    public function updateService()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return $this->jsonResponse(false, 'No autorizado.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'ID de servicio no proporcionado.');
        }

        $datos = [
            'id' => $id,
            'cliente_id' => $_POST['cliente_id'] ?? null,
            'nombre_proyecto' => trim($_POST['nombre_proyecto'] ?? ''),
            'tipo_servicio' => $_POST['tipo_servicio'] ?? 'Otro',
            'tipo_pago' => $_POST['tipo_pago'] ?? 'unico',
            'costo_total' => $_POST['costo_total'] ?? 0,
            'pago_inicial' => $_POST['pago_inicial'] ?? 0,
            'mensualidad_financiamiento' => $_POST['mensualidad_financiamiento'] ?? 0,
            'meses_financiamiento' => $_POST['meses_financiamiento'] ?? 0,
            'es_recurrente' => $_POST['es_recurrente'] ?? 0,
            'frecuencia_pago' => $_POST['frecuencia_pago'] ?? 'ninguno',
            'fecha_proximo_pago' => (!empty($_POST['fecha_proximo_pago'])) ? $_POST['fecha_proximo_pago'] : null
        ];

        if (empty($datos['cliente_id']) || empty($datos['nombre_proyecto'])) {
            return $this->jsonResponse(false, 'El ID del cliente y el nombre del proyecto son obligatorios.');
        }

        $result = $this->clienteService->updateServicio($datos);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Guarda un registro de pago asociado a un servicio
     */
    public function savePago()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return $this->jsonResponse(false, 'No autorizado.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $datos = [
            'servicio_id' => $_POST['servicio_id'] ?? null,
            'monto_pagado' => $_POST['monto_pagado'] ?? 0,
            'fecha_pago' => $_POST['fecha_pago'] ?? date('Y-m-d'),
            'metodo_pago' => $_POST['metodo_pago'] ?? 'Transferencia',
            'referencia' => trim($_POST['referencia'] ?? '')
        ];

        if (empty($datos['servicio_id']) || empty($datos['monto_pagado'])) {
            return $this->jsonResponse(false, 'El servicio y el monto son obligatorios.');
        }

        $result = $this->clienteService->addPago($datos);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Elimina un registro de pago
     */
    public function deletePago()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return $this->jsonResponse(false, 'No autorizado.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['pago_id'] ?? null;
        if (!$id) {
            return $this->jsonResponse(false, 'ID de pago no proporcionado.');
        }

        $result = $this->clienteService->deletePago($id);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Helper para devolver respuestas tipo JSON
     */
    private function jsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
?>