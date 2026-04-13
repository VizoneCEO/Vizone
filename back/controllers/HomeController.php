<?php

/**
 * HomeController
 * 
 * Controlador principal para la página de inicio (Landing Page)
 */
class HomeController
{

    /**
     * Acción por defecto del controlador: Mostrar la página principal.
     */
    public function index()
    {

        // 1. Obtener la información del Servicio
        // Incluimos el servicio necesario
        require_once BACK_PATH . 'services/HomeService.php';

        // Instanciamos el servicio y pedimos la data
        $homeService = new HomeService();
        $serviciosDestacados = $homeService->obtenerPilaresNegocio();

        // 2. Preparar los datos para la Vista
        // Aquí puedes definir variables que la vista utilizará 
        $pageTitle = 'Vizone Web | Transformación Digital';
        $heroTitle = 'Transformamos el trabajo manual en eficiencia digital.';
        $heroSubtitle = 'Desarrollo de software, automatización inteligente y soluciones integrales de infraestructura IT para escalar tu negocio.';

        // 3. Renderizar (Incluir) la Vista
        // Pasamos los datos indirectamente ya que el archivo incluido tendrá acceso a estas variables en el ámbito local
        $this->renderVista('index/home.php', [
            'pageTitle' => $pageTitle,
            'heroTitle' => $heroTitle,
            'heroSubtitle' => $heroSubtitle,
            'servicios' => $serviciosDestacados
        ]);
    }

    /**
     * Método auxiliar para cargar vistas
     * Extrae las variables del array asociativo haciéndolas disponibles en el scope de la vista
     * 
     * @param string $vistaFile Ruta del archivo de vista relativo a la carpeta front/
     * @param array $data Variables a pasar a la vista
     */
    private function renderVista($vistaFile, $data = [])
    {

        // Verifica que el archivo exista
        $fullPath = FRONT_PATH . $vistaFile;

        if (file_exists($fullPath)) {
            // Extraer el array asociativo a variables (ej. ['titulo'=>1] -> $titulo = 1)
            extract($data);

            // Requerir el Header común
            require_once FRONT_PATH . 'generales/header.php';

            // Requerir la vista específica
            require_once $fullPath;

            // Requerir el Footer común
            require_once FRONT_PATH . 'generales/footer.php';
        } else {
            // Si la vista no existe, podríamos lanzar un error o cargar vista 404
            echo "Error: Vista '$vistaFile' no encontrada en '$fullPath'";
        }
    }
}
?>