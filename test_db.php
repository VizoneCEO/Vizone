<?php
require_once __DIR__ . '/back/config/database.php';
$db = Database::getInstance()->getConnection();

try {
    $db->exec("CREATE TABLE IF NOT EXISTS cliente_pagos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        servicio_id INT NOT NULL,
        monto_pagado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        fecha_pago DATE NOT NULL,
        metodo_pago VARCHAR(50) DEFAULT 'Transferencia',
        referencia VARCHAR(255) DEFAULT NULL,
        comprobante_url VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (servicio_id) REFERENCES cliente_servicios(id) ON DELETE CASCADE
    )");
    echo "Table cliente_pagos created successfully.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
