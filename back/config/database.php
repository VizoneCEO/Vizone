<?php
/**
 * Configuración y conexión centralizada a la base de datos.
 * Utiliza el patrón Singleton para asegurar solo una conexión activa y PDO para seguridad (evitar inyección SQL).
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private $host = 'localhost';
    private $db = 'vizone';
    private $username = 'root';
    private $password = '';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos
            PDO::ATTR_EMULATE_PREPARES => false,                  // Usar prepared statements reales
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (\PDOException $e) {
            // En un entorno de producción, nunca mostrar el error real, sino guardarlo en logs.
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Obtiene la única instancia de la conexión a la base de datos (Singleton).
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Devuelve el objeto PDO para ejecutar consultas.
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    // Evitar clonación
    private function __clone()
    {
    }
}
?>