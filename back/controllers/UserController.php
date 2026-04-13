<?php
require_once BACK_PATH . 'services/AuthService.php';

/**
 * UserController
 * 
 * Gestiona el CRUD (Create, Update, Delete) de los Usuarios.
 * Solo es accesible vía POST mediante solicitudes de administradores logueados.
 */
class UserController
{

    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();

        // Middleware genérico: Asegurar que esté logueado
        if (!$this->authService->isLoggedIn()) {
            $this->jsonResponse(false, 'No autorizado. Por favor, inicie sesión.');
        }

        // (Opcional) Podemos verificar aquí que el role sea 'admin' si queremos permisos finos
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SESSION['role'] !== 'admin') {
            $this->jsonResponse(false, 'Solo los administradores pueden gestionar usuarios.');
        }
    }

    /**
     * Guarda un usuario.
     * Si viene un 'user_id' por POST, actualiza. Si no, crea uno nuevo.
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['user_id'] ?? null;
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'admin';

        // Validaciones básicas
        if (empty($username)) {
            return $this->jsonResponse(false, 'El nombre de usuario es obligatorio.');
        }

        if (empty($id) && empty($password)) {
            // Es un nuevo usuario, la contraseña es obligatoria
            return $this->jsonResponse(false, 'La contraseña es obligatoria para usuarios nuevos.');
        }

        if (!empty($id)) {
            // Actualización
            $result = $this->authService->updateUser($id, $username, $password, $role);
        } else {
            // Creación
            $result = $this->authService->createUser($username, $password, $role);
        }

        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Elimina un usuario por su ID.
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['id'] ?? null;

        if (empty($id)) {
            return $this->jsonResponse(false, 'ID de usuario no proporcionado.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $currentLoggedId = $_SESSION['user_id'];

        $result = $this->authService->deleteUser($id, $currentLoggedId);

        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Helper para devolver respuestas tipo JSON al frontend (para AJAX/Fetch)
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