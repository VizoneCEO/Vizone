<?php

/**
 * Clase Router
 * 
 * Analiza la URL solicitada y ejecuta el controlador y método correspondientes.
 */
class Router {
    
    // Almacena las rutas registradas
    private $routes = [];

    /**
     * Añade una ruta al enrutador.
     * 
     * @param string $url La ruta amigable (ej. '/', '/about')
     * @param string $controller El nombre del controlador (ej. 'HomeController')
     * @param string $action El nombre del método a ejecutar (ej. 'index')
     */
    public function add($url, $controller, $action) {
        // Para manejo avanzado, podrías usar expresiones regulares aquí.
        // Por simplicidad, usamos coincidencias exactas.
        $this->routes[$url] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Despacha la solicitud actual.
     * Encuentra el controlador y ejecuta la acción si la ruta coincide.
     * 
     * @param string $url La URL actual a despachar
     */
    public function dispatch($url) {
        
        // Quitar la barra final si existe (excepto si es la raíz '/')
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }

        // Buscar si la URL existe en nuestras rutas registradas
        if (array_key_exists($url, $this->routes)) {
            
            $route = $this->routes[$url];
            $controllerName = $route['controller'];
            $actionName = $route['action'];

            // Verificar si el archivo del controlador existe
            $controllerFile = BACK_PATH . 'controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                // Instanciar el controlador
                if (class_exists($controllerName)) {
                    $controller_object = new $controllerName();
                    
                    // Comprobar si el método existe en el controlador
                    if (is_callable([$controller_object, $actionName])) {
                        // Ejecutar el método
                        $controller_object->$actionName();
                    } else {
                        $this->render404("Método $actionName no encontrado en el controlador $controllerName.");
                    }
                } else {
                     $this->render404("Clase controlador $controllerName no encontrada.");
                }
            } else {
                 $this->render404("Archivo del controlador $controllerName no encontrado.");
            }

        } else {
            // Ruta no encontrada - Mostrar 404
            $this->render404("La página que buscas no existe: " . htmlspecialchars($url));
        }
    }

    /**
     * Renderiza una página de error 404 simple.
     */
    private function render404($message = "Página no encontrada") {
        header("HTTP/1.0 404 Not Found");
        echo "<div style='font-family: sans-serif; padding: 2rem; text-align: center; color: #333;'>";
        echo "<h1>404 - No Encontrado</h1>";
        echo "<p>{$message}</p>";
        echo "<a href='/vizone/'>Volver al inicio</a>";
        echo "</div>";
    }
}
?>
