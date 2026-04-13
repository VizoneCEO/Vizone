<?php
/**
 * Script para inicializar la base de datos "vizone" y el usuario administrador.
 * Ejecutar este archivo desde el navegador (http://localhost/vizone/init_db.php) una sola vez.
 */

$host = 'localhost';
$user = 'root'; // Usuario por defecto de XAMPP
$pass = '';     // Contraseña por defecto vacía en XAMPP

try {
    // 1. Conectar a MySQL principal (sin seleccionar DB aún)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Crear la base de datos 'vizone' si no existe
    $sql = "CREATE DATABASE IF NOT EXISTS vizone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "<h3>Base de datos 'vizone' creada (o ya existía).</h3>";

    // 3. Conectar a la base de datos recién creada
    $pdo = new PDO("mysql:host=$host;dbname=vizone;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Crear la tabla 'users'
    $sqlTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlTable);
    echo "<h3>Tabla 'users' creada.</h3>";

    // 5. Insertar el usuario admin por defecto (admin / ledesma29)
    $username = 'admin';
    $password = 'ledesma29';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() == 0) {
        // Insertar si no existe
        $insert = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $insert->execute([$username, $hash]);
        echo "<h3>Usuario '$username' creado exitosamente con contraseña '$password'.</h3>";
    } else {
        echo "<h3>El usuario '$username' ya existe. Omitiendo creación.</h3>";
    }

    echo "<hr><h3>Nuevas Tablas CRM:</h3>";

    // 6. Crear tabla 'clientes' (Vinculada 1 a 1 casi siempre con users)
    $sqlClientes = "
    CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        nombre_empresa VARCHAR(150) NOT NULL,
        contacto_principal VARCHAR(150) NULL,
        telefono VARCHAR(50) NULL,
        email VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sqlClientes);
    echo "<h3>Tabla 'clientes' creada.</h3>";

    // 7. Crear tabla 'cliente_servicios' (Desarrollos, Financiamientos y Mantenimiento)
    $sqlServicios = "
    CREATE TABLE IF NOT EXISTS cliente_servicios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        nombre_proyecto VARCHAR(200) NOT NULL,
        
        costo_total_desarrollo DECIMAL(12,2) DEFAULT 0.00,
        pago_inicial DECIMAL(12,2) DEFAULT 0.00,
        
        mensualidad_financiamiento DECIMAL(12,2) DEFAULT 0.00,
        meses_financiamiento INT DEFAULT 0,
        
        costo_mantenimiento DECIMAL(12,2) DEFAULT 0.00,
        frecuencia_mantenimiento ENUM('mensual', 'anual', 'unico', 'ninguno') DEFAULT 'ninguno',
        
        estado ENUM('activo', 'pausado', 'finalizado') DEFAULT 'activo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlServicios);
    echo "<h3>Tabla 'cliente_servicios' creada.</h3>";

    // 8. Crear tabla 'cliente_documentos' (Manuales, READMEs)
    $sqlDocs = "
    CREATE TABLE IF NOT EXISTS cliente_documentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        nombre_archivo VARCHAR(255) NOT NULL,
        nombre_original VARCHAR(255) NOT NULL,
        ruta_fisica VARCHAR(500) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlDocs);
    echo "<h3>Tabla 'cliente_documentos' creada.</h3>";

    echo "<hr><a href='/vizone/login'>Ir al Login</a>";

} catch (PDOException $e) {
    die("ERROR DB: " . $e->getMessage());
}
?>