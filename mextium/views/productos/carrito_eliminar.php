<?php
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    if ($producto_id > 0 && isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
        echo json_encode(['ok' => true]);
        exit;
    }
}
echo json_encode(['ok' => false]);
exit;
