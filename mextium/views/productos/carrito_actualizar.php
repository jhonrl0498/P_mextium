<?php
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    if ($producto_id > 0 && isset($_SESSION['carrito'][$producto_id])) {
        if ($accion === 'sumar') {
            $_SESSION['carrito'][$producto_id]++;
        } elseif ($accion === 'restar') {
            $_SESSION['carrito'][$producto_id]--;
            if ($_SESSION['carrito'][$producto_id] <= 0) {
                unset($_SESSION['carrito'][$producto_id]);
            }
        }
    }
    echo json_encode(['ok' => true, 'cantidad' => $_SESSION['carrito'][$producto_id] ?? 0]);
    exit;
}
echo json_encode(['ok' => false]);
exit;
