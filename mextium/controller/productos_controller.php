<?php
require_once __DIR__ . '/../model/productos_model.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagenPath = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'producto_' . time() . '_' . rand(1000,9999) . '.' . $ext;
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
        'nombre'        => $_POST['nombre'] ?? '',
        'descripcion'   => $_POST['descripcion'] ?? '',
        'precio'        => $_POST['precio'] ?? 0,
        'imagen'        => $imagenPath,
        'stock'         => $_POST['stock'] ?? 0,
        'categoria_id'  => $_POST['categoria_id'] ?? '',
        'vendedor_id'   => $_POST['vendedor_id'] ?? ($_SESSION['user_id'] ?? null),
        'destacado'     => isset($_POST['destacado']) ? 1 : 0,
        'estado'        => $_POST['estado'] ?? 'activo',
        'peso' => $_POST['peso'] ?? null,
        'peso_unidad' => $_POST['peso_unidad'] ?? null,
        'largo' => $_POST['largo'] ?? null,
        'ancho' => $_POST['ancho'] ?? null,
        'alto' => $_POST['alto'] ?? null,
        'dimensiones_unidad' => $_POST['dimensiones_unidad'] ?? null,
        'volumen' => $_POST['volumen'] ?? null,
        'volumen_unidad' => $_POST['volumen_unidad'] ?? null,
        'material' => $_POST['material'] ?? null,
        'color' => $_POST['color'] ?? null,
        'marca' => $_POST['marca'] ?? null,
        'modelo' => $_POST['modelo'] ?? null,
        'codigo_barras' => $_POST['codigo_barras'] ?? null,
        'sku' => $_POST['sku'] ?? null,
        'garantia_meses' => $_POST['garantia_meses'] ?? null,
        'origen_pais' => $_POST['origen_pais'] ?? null,
        'condicion' => $_POST['condicion'] ?? null,
        'tags' => $_POST['tags'] ?? null,
        'especificaciones_tecnicas' => $_POST['especificaciones_tecnicas'] ?? null,
        'instrucciones_uso' => $_POST['instrucciones_uso'] ?? null,
        'ingredientes' => $_POST['ingredientes'] ?? null,
        'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? null,
        'temperatura_almacenamiento' => $_POST['temperatura_almacenamiento'] ?? null,
        'fragil' => isset($_POST['fragil']) ? 1 : 0,
        'requiere_refrigeracion' => isset($_POST['requiere_refrigeracion']) ? 1 : 0,
        'edad_minima' => $_POST['edad_minima'] ?? null,
        'edad_maxima' => $_POST['edad_maxima'] ?? null,
        'genero' => $_POST['genero'] ?? null,
        'talla' => $_POST['talla'] ?? null,
        'sistema_talla' => $_POST['sistema_talla'] ?? null
    ];

    $productosModel = new ProductosModel();
    $resultado = $productosModel->registrarProducto($datos);

    if ($resultado['success']) {
        header('Location: ../views/vendedores/mi_tienda.php?producto=exito');
        exit;
    } else {
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:sans-serif;padding:2rem;"><h2 style="color:red;">Error al registrar el producto</h2><p>' . htmlspecialchars($resultado['message']) . '</p><a href="../views/productos/agregar_producto.php">Volver</a></body></html>';
        exit;
    }
}
