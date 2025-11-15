<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/productos_model.php';

if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$producto_id = intval($_POST['producto_id']);
$cantidad = intval($_POST['cantidad']);
if ($producto_id <= 0 || $cantidad < 1) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$productosModel = new ProductosModel();
$producto = $productosModel->obtenerProductoPorId($producto_id);
if (!$producto) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
    exit;
}

$stock = intval($producto['stock']);
if ($cantidad > $stock) {
    echo json_encode(['success' => false, 'message' => 'No hay suficiente stock disponible.']);
    exit;
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$_SESSION['carrito'][$producto_id] = $cantidad;

// Calcular subtotal y total
$subtotal = $producto['precio'] * $cantidad;
$total = 0;
foreach ($_SESSION['carrito'] as $pid => $cant) {
    $prod = $productosModel->obtenerProductoPorId($pid);
    if ($prod) {
        $total += $prod['precio'] * $cant;
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Cantidad actualizada.',
    'subtotal' => number_format($subtotal, 2),
    'total' => number_format($total, 2),
    'stock' => $stock
]);
