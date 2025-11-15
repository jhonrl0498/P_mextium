<?php
session_start();
require_once __DIR__ . '/../model/tienda_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/usuarios/inicio_sesion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tienda_id = $_POST['tienda_id'] ?? null;
    $nombre_tienda = $_POST['nombre_tienda'] ?? '';
    $descripcion_tienda = $_POST['descripcion_tienda'] ?? '';
    $categoria_principal = $_POST['categoria_principal'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $mercado_pago_token = $_POST['mercado_pago_token'] ?? null;

    // Manejo de imagen
    $imagenPath = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'tienda_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $directorioUploads = __DIR__ . '/../uploads/';
        if (!is_dir($directorioUploads)) {
            mkdir($directorioUploads, 0777, true);
        }
        $destino = $directorioUploads . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $imagenPath = 'uploads/' . $nombreArchivo;
        }
    }

    $datos = [
        'id' => $tienda_id,
        'nombre_tienda' => $nombre_tienda,
        'descripcion_tienda' => $descripcion_tienda,
        'categoria_principal' => $categoria_principal,
        'direccion' => $direccion,
        'ciudad' => $ciudad,
        'mercado_pago_token' => $mercado_pago_token,
        'imagen' => $imagenPath
    ];

    $tiendaModel = new TiendaModel();
    $resultado = $tiendaModel->actualizarTienda($datos);

    if ($resultado['success']) {
        header('Location: ../views/vendedores/mi_tienda.php?actualizacion=exito');
        exit;
    } else {
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:sans-serif;padding:2rem;"><h2 style="color:red;">Error al actualizar la tienda</h2><p>' . htmlspecialchars($resultado['message']) . '</p><a href="../views/vendedores/editar_tienda.php">Volver a editar</a></body></html>';
        exit;
    }
}
