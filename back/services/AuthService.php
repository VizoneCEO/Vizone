<?php
require_once BACK_PATH . 'config/database.php';

/**
 * Servicio de Autenticación y Gestión de Usuarios
 */
class AuthService
{

    private $db;

    public function __construct()
    {
        // Obtener la instancia de PDO
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Intenta autenticar a un usuario con nombre de usuario y contraseña.
     * 
     * @param string $username
     * @param string $password Contraseña en texto plano
     * @return bool|array Retorna falso si falla, o el array del usuario si es exitoso.
     */
    public function login($username, $password)
    {
        try {
            // Buscar el usuario por username
            $stmt = $this->db->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Si el usuario existe y la contraseña (en texto plano) coincide con el hash almacenado
            if ($user && password_verify($password, $user['password_hash'])) {

                // Iniciar sesión segura en PHP
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Guardar datos en la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // Forzar cambio si la contraseña es la predeterminada "password123"
                if ($password === 'password123') {
                    $_SESSION['must_change_password'] = true;
                } else {
                    $_SESSION['must_change_password'] = false;
                }

                return $user; // Éxito
            }

            return false; // Credenciales inválidas

        } catch (PDOException $e) {
            // Loggear el error si es necesario
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios de la base de datos (Para el dashboard)
     * 
     * @return array
     */
    public function getAllUsers()
    {
        try {
            $stmt = $this->db->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Users Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un usuario específico por su ID.
     */
    public function getUserById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, username, role FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("GetUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     */
    public function createUser($username, $password, $role)
    {
        try {
            // Verificar si el username ya existe
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'El nombre de usuario ya está en uso.'];
            }

            // Hashear contraseña y guardar
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $this->db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $result = $insert->execute([$username, $hash, $role]);

            if ($result) {
                $insertedId = $this->db->lastInsertId();
                return ['success' => true, 'message' => 'Usuario creado correctamente.', 'id' => $insertedId];
            }

            return ['success' => false, 'message' => 'Error al guardar.'];
        } catch (PDOException $e) {
            error_log("CreateUser Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /**
     * Actualiza un usuario existente. Si el password viene vacío, no se cambia.
     */
    public function updateUser($id, $username, $password, $role)
    {
        try {
            // Verificar si el nuevo username choca con el de otro usuario
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El nombre de usuario ya está tomado por otro.'];
            }

            if (!empty($password)) {
                // Actualizar todo, incluido password
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $update = $this->db->prepare("UPDATE users SET username = ?, password_hash = ?, role = ? WHERE id = ?");
                $result = $update->execute([$username, $hash, $role, $id]);
            } else {
                // Actualizar solo nombre y rol
                $update = $this->db->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
                $result = $update->execute([$username, $role, $id]);
            }

            return ['success' => $result, 'message' => $result ? 'Usuario actualizado.' : 'No se realizaron cambios.'];
        } catch (PDOException $e) {
            error_log("UpdateUser Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /**
     * Elimina un usuario por su ID, con protección para no auto-eliminarse.
     */
    public function deleteUser($id, $currentUserId)
    {
        if ($id == $currentUserId) {
            return ['success' => false, 'message' => 'No puedes eliminar tu propia cuenta mientras estás conectado.'];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Usuario eliminado correctamente.'];
        } catch (PDOException $e) {
            error_log("DeleteUser Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al eliminar.'];
        }
    }

    /**
     * Cierra la sesión activa.
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Destruir la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir la sesión final
        session_destroy();
    }

    /**
     * Verifica si hay una sesión activa.
     * 
     * @return bool
     */
    public function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Resetea la contraseña de un usuario a 'password123'
     */
    public function resetPassword($user_id)
    {
        try {
            $hash = password_hash('password123', PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$hash, $user_id]);
            return ['success' => $result, 'message' => 'Contraseña reestablecida a temporal.'];
        } catch (PDOException $e) {
            error_log("ResetPassword Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }

    /**
     * Actualiza solo la contraseña del usuario (cambio forzado)
     */
    public function forceUpdatePassword($user_id, $newPassword)
    {
        try {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $result = $stmt->execute([$hash, $user_id]);
            return ['success' => $result, 'message' => 'Contraseña actualizada correctamente.'];
        } catch (PDOException $e) {
            error_log("ForceUpdatePassword Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos.'];
        }
    }
}
?>