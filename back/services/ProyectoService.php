<?php
require_once BACK_PATH . 'config/database.php';

/**
 * Servicio de Proyectos
 * Maneja operaciones CRUD sobre la tabla 'proyectos'
 */
class ProyectoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los proyectos, ordenados por fecha de finalización (más recientes primero)
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM proyectos ORDER BY fecha_terminado DESC, id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProyectoService getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un proyecto por ID
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ProyectoService getById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo proyecto
     */
    public function create($titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO proyectos (titulo, descripcion, imagen_url, link_proyecto, fecha_terminado) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado]);
            return ['success' => $result, 'message' => $result ? 'Proyecto creado exitosamente.' : 'No se pudo crear el proyecto.'];
        } catch (PDOException $e) {
            error_log("ProyectoService create Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /**
     * Actualiza un proyecto existente
     */
    public function update($id, $titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado)
    {
        try {
            $stmt = $this->db->prepare("UPDATE proyectos SET titulo = ?, descripcion = ?, imagen_url = ?, link_proyecto = ?, fecha_terminado = ? WHERE id = ?");
            $result = $stmt->execute([$titulo, $descripcion, $imagen_url, $link_proyecto, $fecha_terminado, $id]);
            return ['success' => $result, 'message' => $result ? 'Proyecto actualizado exitosamente.' : 'No hubo cambios o falló la actualización.'];
        } catch (PDOException $e) {
            error_log("ProyectoService update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /**
     * Elimina un proyecto por ID
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM proyectos WHERE id = ?");
            $result = $stmt->execute([$id]);
            return ['success' => $result, 'message' => $result ? 'Proyecto eliminado correctamente.' : 'Fallo al eliminar.'];
        } catch (PDOException $e) {
            error_log("ProyectoService delete Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al eliminar.'];
        }
    }
}
?>
