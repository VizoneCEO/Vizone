<?php

/**
 * Front Controller (index.php)
 * 
 * Punto de entrada único de la aplicación.
 * Intercepta todas las peticiones web y las delega al enrutador (Router).
 */

// 1. Configuración de errores (solo para desarrollo, desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Definición de rutas base para facilitar inclusiones
define('BASE_PATH', __DIR__ . '/');
define('BACK_PATH', BASE_PATH . 'back/');
define('FRONT_PATH', BASE_PATH . 'front/');

// 3. Autocarga simple de clases (puedes usar Composer PSR-4 en un futuro)
spl_autoload_register(function ($class_name) {
    // Convertir namespace/clase a ruta de archivo si usaras namespaces
    // Por ahora, buscaremos en los directorios conocidos
    $directories = [
        'back/routers/',
        'back/controllers/',
        'back/services/'
    ];

    foreach ($directories as $directory) {
        $file = BASE_PATH . $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 4. Inicializar el Router
$router = new Router();

// 5. Definir las rutas de la aplicación

// --- Rutas Públicas Extras ---
$router->add('/', 'HomeController', 'index');
$router->add('/servicios', 'ServiciosController', 'index');
$router->add('/contacto', 'ContactoController', 'index');

// --- Rutas de Autenticación y Dashboard ---
$router->add('/login', 'LoginController', 'index');      // Muestra formulario de login
$router->add('/auth', 'LoginController', 'auth');        // Procesa el login (POST)
$router->add('/logout', 'LoginController', 'logout');    // Procesa el logout

// Modulos del Dashboard
$router->add('/dashboard', 'DashboardController', 'index');           // Muestra panel de usuarios (por defecto)
$router->add('/dashboard/usuarios', 'DashboardController', 'index');  // Muestra panel de usuarios
$router->add('/dashboard/clientes', 'DashboardController', 'clientes'); // Muestra panel de clientes
$router->add('/dashboard/tickets', 'DashboardController', 'tickets');   // Muestra panel de tickets
$router->add('/dashboard/pagos', 'DashboardController', 'pagos');       // Muestra panel de pagos globales
$router->add('/dashboard/mi-portal', 'DashboardController', 'miPortal'); // Dashboard para clientes

// API: CRUD Usuarios (Reciben POST)
$router->add('/dashboard/usuarios/save', 'UserController', 'save');
$router->add('/dashboard/usuarios/delete', 'UserController', 'delete');

// API: CRUD Clientes (Reciben POST)
$router->add('/dashboard/clientes/save', 'ClienteController', 'save');
$router->add('/dashboard/clientes/servicio/save', 'ClienteController', 'saveService');
$router->add('/dashboard/clientes/servicio/update', 'ClienteController', 'updateService');
$router->add('/dashboard/clientes/servicio/delete', 'ClienteController', 'deleteService');
$router->add('/dashboard/clientes/pagos/save', 'ClienteController', 'savePago');
$router->add('/dashboard/clientes/pagos/delete', 'ClienteController', 'deletePago');
$router->add('/dashboard/clientes/documento/save', 'ClienteController', 'saveDocument');
$router->add('/dashboard/cliente/detalles', 'DashboardController', 'clienteDetalles');

// 6. Obtener la URI actual (limpiando query strings si existen)
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si estás trabajando en una subcarpeta (ej. htdocs/vizone), ajusta la ruta base
$basepath = '/vizone';
if (strpos($url, $basepath) === 0) {
    $url = substr($url, strlen($basepath));
}

// Asegurarse de que la URL raíz sea '/' si está vacía tras limpiar
if (empty($url)) {
    $url = '/';
}

// 7. Despachar la petición
$router->dispatch($url);

?>