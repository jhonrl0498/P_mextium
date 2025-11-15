<?php
require_once __DIR__ . '/conexion.php';

if (isset($pdo) && $pdo) {
    echo "¡Conexión exitosa!";
} else {
    echo "No se pudo conectar a la base de datos.";
}
