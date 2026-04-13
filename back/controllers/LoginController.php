<?php
require_once BACK_PATH . 'services/AuthService.php';

/**
 * LoginController
 * 
 * Gestiona la visualización del formulario de inicio de sesión
 * y el procesamiento de credenciales.
 */
class LoginController
{

    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Muestra la vista de Login.
     */
    public function index()
    {
        // Redirigir al dashboard si ya está logueado
        if ($this->authService->isLoggedIn()) {
            header('Location: /vizone/dashboard');
            exit;
        }

        // Obtener posibles mensajes de error (desde la sesión)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null;
        unset($_SESSION['login_error']); // Limpiar el error después de leerlo

        // Incluimos la vista de login directamente sin el main header/footer 
        // para tener libertad total de diseño (pantalla completa Apple Style).
        require_once FRONT_PATH . 'login/index.php';
    }

    /**
     * Procesa los datos del formulario de Login (POST).
     */
    public function auth()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->redirectWithError('Por favor, ingresa usuario y contraseña.');
                return;
            }

            // Intentar autenticación
            $user = $this->authService->login($username, $password);

            if ($user) {
                // Éxito -> Redirigir a panel de administración
                header('Location: /vizone/dashboard');
                exit;
            } else {
                // Fallo -> Redirigir de vuelta al form con error
                $this->redirectWithError('Usuario o contraseña incorrectos.');
            }
        } else {
            // Si acceden a /auth por GET accidentalmente
            header('Location: /vizone/login');
            exit;
        }
    }

    /**
     * Cierra la sesión y redirige al login.
     */
    public function logout()
    {
        // 1. Cierra sesión en el servicio
        $this->authService->logout();

        // 2. Redirige al login usando ruta absoluta
        header('Location: /vizone/login');
        exit;
    }

    /**
     * Helper para manejar el redirect con mensaje de error
     */
    private function redirectWithError($message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['login_error'] = $message;
        header('Location: /vizone/login');
        exit;
    }
}
?>