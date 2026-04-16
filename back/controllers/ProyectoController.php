<?php
require_once BACK_PATH . 'services/ProyectoService.php';
require_once BACK_PATH . 'services/AuthService.php';

class ProyectoController
{
    private $proyectoService;
    private $authService;

    public function __construct()
    {
        $this->proyectoService = new ProyectoService();
        $this->authService = new AuthService();
    }

    /**
     * Vista pública: Portafolio de proyectos
     */
    public function index()
    {
        $proyectos = $this->proyectoService->getAll();
        
        $pageTitle = "Proyectos Terminados | Vizone";
        // Render frontend estándar de Vizone
        require_once FRONT_PATH . 'generales/header.php';
        require_once FRONT_PATH . 'proyectos/index.php';
        require_once FRONT_PATH . 'generales/footer.php';
    }

    // ==========================================
    // PANEL DE ADMINISTRACIÓN CRUD (DASHBOARD)
    // ==========================================

    /**
     * Auxiliar para renderizar la vista administrativa dentro del Dashboard Layout subyacente
     */
    private function renderDashboardView($viewFile, $data = [])
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $data['activeUsername'] = $_SESSION['username'] ?? 'Usuario';
        $data['activeRole'] = $_SESSION['role'] ?? 'admin';
        extract($data);
        
        $viewContent = FRONT_PATH . 'dashboard/' . $viewFile;
        require_once FRONT_PATH . 'dashboard/layout.php';
    }

    /**
     * Renderiza la tabla CRUD en el panel Dashboard
     */
    public function adminIndex()
    {
        $this->requireAdmin();

        $proyectos = $this->proyectoService->getAll();

        $this->renderDashboardView('proyectos.php', [
            'pageTitle' => 'Admin | Proyectos',
            'proyectos' => $proyectos,
            'activeModule' => 'proyectos'
        ]);
    }

    /**
     * Guarda (Crea o Edita) un proyecto vía AJAX POST
     */
    public function save()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['id'] ?? null;
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagen_url = trim($_POST['imagen_url'] ?? '');
        $link_proyecto = trim($_POST['link_proyecto'] ?? '');
        $fecha_terminado = trim($_POST['fecha_terminado'] ?? date('Y-m-d'));

        if (empty($titulo)) {
            return $this->jsonResponse(false, 'El título es obligatorio.');
        }

        if (!empty($id)) {
            $result = $this->proyectoService->update($id, $titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado);
        } else {
            $result = $this->proyectoService->create($titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado);
        }

        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Elimina un proyecto vía AJAX POST
     */
    public function delete()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $id = $_POST['id'] ?? null;

        if (empty($id)) {
            return $this->jsonResponse(false, 'ID no proporcionado.');
        }

        $result = $this->proyectoService->delete($id);
        return $this->jsonResponse($result['success'], $result['message']);
    }

    /**
     * Refresca las capturas de pantalla buscando los enlaces de los proyectos
     */
    public function refreshScreenshots()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Método no permitido.');
        }

        $proyectos = $this->proyectoService->getAll();
        $updatedCount = 0;

        foreach ($proyectos as $proyecto) {
            $url = $proyecto['link_proyecto'];
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                
                // Microlink API gratis para generar imagen
                $apiUrl = "https://api.microlink.io?url=" . urlencode($url) . "&screenshot=true&meta=false";
                
                $context = stream_context_create(['http' => ['timeout' => 15]]);
                $response = @file_get_contents($apiUrl, false, $context);
                
                if ($response) {
                    $json = json_decode($response, true);
                    if (isset($json['status']) && $json['status'] === 'success' && !empty($json['data']['screenshot']['url'])) {
                        
                        $imgUrl = $json['data']['screenshot']['url'];
                        $imgData = @file_get_contents($imgUrl, false, $context);
                        
                        if ($imgData) {
                            $filename = 'screenshot_proj_' . $proyecto['id'] . '_' . time() . '.png';
                            $savePath = FRONT_PATH . 'assets/proyectos/' . $filename;
                            
                            // Borrar la anterior
                            if (!empty($proyecto['imagen_url']) && strpos($proyecto['imagen_url'], '/assets/proyectos/') !== false) {
                                $oldFile = FRONT_PATH . 'assets/proyectos/' . basename($proyecto['imagen_url']);
                                if (file_exists($oldFile)) {
                                    @unlink($oldFile);
                                }
                            }

                            if (file_put_contents($savePath, $imgData)) {
                                $publicUrl = (defined('BASE_URL') ? BASE_URL : '/') . 'front/assets/proyectos/' . $filename;
                                $this->proyectoService->update(
                                    $proyecto['id'], 
                                    $proyecto['titulo'], 
                                    $proyecto['descripcion'], 
                                    $publicUrl, 
                                    $proyecto['link_proyecto'], 
                                    $proyecto['fecha_terminado']
                                );
                                $updatedCount++;
                            }
                        }
                    }
                }
            }
        }

        return $this->jsonResponse(true, "Se actualizaron $updatedCount capturas correctamente.");
    }

    // ==========================================
    // MIDDLEWARE Y UTILIDADES
    // ==========================================
    
    private function requireAdmin()
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /vizone/login');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['role'] ?? '') !== 'admin') {
            $this->jsonResponse(false, 'No autorizado. Solo administradores.');
            exit;
        }
    }

    private function jsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }
}
?>
