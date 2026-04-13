<?php
$array = ['id' => 1, 'tipo_pago' => 'unico'];
echo htmlspecialchars(json_encode($array), ENT_QUOTES, 'UTF-8');
?>
