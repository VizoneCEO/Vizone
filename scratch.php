<?php
define('BACK_PATH', __DIR__ . '/back/');
require_once BACK_PATH . 'config/database.php';
require_once BACK_PATH . 'services/ClienteService.php';
$db = Database::getInstance()->getConnection();
$service = new ClienteService($db);
$stmt = $db->query("SELECT * FROM cliente_servicios WHERE tipo_pago = 'varios'");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $chk = $db->prepare("SELECT COUNT(*) FROM servicio_amortizacion WHERE servicio_id = ?");
    $chk->execute([$row['id']]);
    if ($chk->fetchColumn() == 0) {
        $service->updateServicio($row);
        echo "Amortizacion generada para id a traves de update: " . $row['id'] . "\n";
    }
}
echo "Done.\n";
