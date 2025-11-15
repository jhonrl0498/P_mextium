<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../model/usuario_model.php';
require_once __DIR__ . '/../../model/tienda_model.php';

header('Content-Type: application/json');


// Conexión directa (usa la misma lógica que los modelos)
require_once __DIR__ . '/../../model/conexion.php';

// Total usuarios
$totalUsuarios = 0;
try {
    $row = $pdo->query('SELECT COUNT(*) as total FROM usuarios')->fetch();
    $totalUsuarios = $row ? (int)$row['total'] : 0;
} catch (Exception $e) {}

// Total tiendas
$totalTiendas = 0;
try {
    $row = $pdo->query('SELECT COUNT(*) as total FROM vendedores')->fetch();
    $totalTiendas = $row ? (int)$row['total'] : 0;
} catch (Exception $e) {}

// Total productos
$totalProductos = 0;
try {
    $row = $pdo->query('SELECT COUNT(*) as total FROM productos')->fetch();
    $totalProductos = $row ? (int)$row['total'] : 0;
} catch (Exception $e) {}

// Total categorías
$totalCategorias = 0;
try {
    $row = $pdo->query('SELECT COUNT(*) as total FROM categorias')->fetch();
    $totalCategorias = $row ? (int)$row['total'] : 0;
} catch (Exception $e) {}

// Total reportes (dummy, puedes cambiar por tu lógica real)
$totalReportes = 0;

// Respuesta
echo json_encode([
    'usuarios' => $totalUsuarios,
    'tiendas' => $totalTiendas,
    'productos' => $totalProductos,
    'categorias' => $totalCategorias,
    'reportes' => $totalReportes
]);
