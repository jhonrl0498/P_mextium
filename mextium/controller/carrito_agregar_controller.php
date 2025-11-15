<?php
session_start();
header('Content-Type: application/json');

// Validar datos recibidos
if (!isset($_POST['producto_id'])) {
    echo json_encode(['success' => false, 'message' => 'Producto no especificado.']);
    exit;
}

$producto_id = intval($_POST['producto_id']);
if ($producto_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
    exit;
}

$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
if ($cantidad < 1) $cantidad = 1;

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Si el producto ya está en el carrito, sumar cantidad
if (isset($_SESSION['carrito'][$producto_id])) {
    $_SESSION['carrito'][$producto_id] += $cantidad;
} else {
    $_SESSION['carrito'][$producto_id] = $cantidad;
}

echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
