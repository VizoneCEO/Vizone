<?php
require_once BACK_PATH . 'services/AuthService.php';

/**
 * DashboardController
 * 
 * Controlador seguro. Solo accesible si el usuario tiene sesión activa.
 * Gestiona los diferentes módulos del panel (Usuarios, Clientes, Tickets).
 */
class DashboardController
{

    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();

        // --- MIDDLEWARE DE SEGURIDAD ---
        if (!$this->authService->isLoggedIn()) {
            header('Location: /vizone/login');
            exit;
        }
    }

    /**
     * Helper para renderizar una vista dentro del Layout del Dashboard
     */
    private function renderDashboardView($viewFile, $data = [])
    {
        // Añadimos las variables globales de sesión para el layout
        $data['activeUsername'] = $_SESSION['username'] ?? 'Usuario';
        $data['activeRole'] = $_SESSION['role'] ?? 'admin';

        // Extraemos variables para usarlas en las vistas
        extract($data);

        // El layout se encarga de incluir el sidebar y el header.
        // Espera una variable $viewContent que apunte al archivo a incluir.
        $viewContent = FRONT_PATH . 'dashboard/' . $viewFile;

        require_once FRONT_PATH . 'dashboard/layout.php';
    }

    /**
     * Módulo 1: Gestión de Usuarios (Default)
     */
    public function index()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
            header('Location: /vizone/dashboard/mi-portal');
            exit;
        }

        $users = $this->authService->getAllUsers();

        $this->renderDashboardView('usuarios.php', [
            'pageTitle' => 'Portal Vizone | Usuarios',
            'users' => $users,
            'activeModule' => 'usuarios'
        ]);
    }

    /**
     * Módulo 2: Gestión de Clientes
     */
    /**
     * Módulo 2: Gestión de Clientes (Lista)
     */
    public function clientes()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
            header('Location: /vizone/dashboard/mi-portal');
            exit;
        }

        require_once BACK_PATH . 'services/ClienteService.php';
        $clienteService = new ClienteService();
        $clientes = $clienteService->getAllClientes();

        $this->renderDashboardView('clientes.php', [
            'pageTitle' => 'Portal Vizone | Clientes',
            'clientes' => $clientes,
            'activeModule' => 'clientes'
        ]);
    }

    /**
     * Módulo 2.1: Perfil de Cliente Individual
     */
    public function clienteDetalles()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
            header('Location: /vizone/dashboard/mi-portal');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /vizone/dashboard/clientes');
            exit;
        }

        require_once BACK_PATH . 'services/ClienteService.php';
        $clienteService = new ClienteService();
        $cliente = $clienteService->getClienteById($id);

        if (!$cliente) {
            header('Location: /vizone/dashboard/clientes');
            exit;
        }

        $this->renderDashboardView('cliente_perfil.php', [
            'pageTitle' => 'Perfil Cliente | ' . htmlspecialchars($cliente['nombre_empresa']),
            'cliente' => $cliente,
            'activeModule' => 'clientes'
        ]);
    }

    /**
     * Módulo Cliente: Mi Portal (Solo Clientes)
     */
    public function miPortal()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header('Location: /vizone/dashboard');
            exit;
        }

        require_once BACK_PATH . 'services/ClienteService.php';
        $clienteService = new ClienteService();
        $cliente = $clienteService->getClienteByUserId($_SESSION['user_id']);

        if (!$cliente) {
            echo "<h2>Perfil de cliente no encontrado. Contacte con Vizone.</h2>";
            exit;
        }

        $this->renderDashboardView('mi_portal.php', [
            'pageTitle' => 'Mi Portal | ' . htmlspecialchars($cliente['nombre_empresa']),
            'cliente' => $cliente,
            'activeModule' => 'mi_portal'
        ]);
    }

    /**
     * Módulo 3: Soporte y Tickets
     */
    public function tickets()
    {
        // TODO: Crear TicketService y obtener datos reales
        $tickets = [];

        $this->renderDashboardView('tickets.php', [
            'pageTitle' => 'Portal Vizone | Tickets',
            'tickets' => $tickets,
            'activeModule' => 'tickets'
        ]);
    }

    /**
     * Módulo 4: Pagos Globales
     */
    public function pagos()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
            header('Location: /vizone/dashboard/mi-portal');
            exit;
        }

        require_once BACK_PATH . 'services/ClienteService.php';
        $clienteService = new ClienteService();
        $pagos = $clienteService->getAllPagosGlobal();
        $serviciosActivos = $clienteService->getAllServiciosActivos();

        $this->renderDashboardView('pagos.php', [
            'pageTitle' => 'Portal Vizone | Pagos Globales',
            'pagos' => $pagos,
            'serviciosActivos' => $serviciosActivos,
            'activeModule' => 'pagos'
        ]);
    }
}
?>