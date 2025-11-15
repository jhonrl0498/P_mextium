<?php
// reportes.php - Detalle de reportes (dummy)
header('Content-Type: application/json');
// Aquí puedes poner la lógica real de reportes si tienes una tabla
$reportes = [
    ['id' => 1, 'tipo' => 'Soporte', 'descripcion' => 'Ejemplo de reporte', 'fecha' => '2025-08-08'],
    // ...
];
echo json_encode($reportes);
