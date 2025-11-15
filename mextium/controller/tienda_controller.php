<?php
require_once __DIR__ . '/../model/tienda_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
		'usuario_id'           => $_POST['usuario_id'] ?? null,
		'nombre_tienda'        => $_POST['nombre_tienda'] ?? '',
		'descripcion_tienda'   => $_POST['descripcion'] ?? '',
		'categoria_principal'  => $_POST['categoria_principal'] ?? '',
		'direccion'            => $_POST['direccion'] ?? '',
		'ciudad'               => $_POST['ciudad'] ?? '',
		'departamento_id'      => $_POST['departamento_id'] ?? null,
		'verificado'           => 0,
		'calificacion_promedio'=> 0,
		'total_ventas'         => 0,
		'estado_tienda'        => 'pendiente',
		'fecha_aprobacion'     => null,
		'fecha_creacion'       => date('Y-m-d H:i:s'),
		'fecha_actualizacion'  => date('Y-m-d H:i:s'),
		'imagen'               => $imagenPath
	];

	$tiendaModel = new TiendaModel();
	$resultado = $tiendaModel->registrarTienda($datos);

	if ($resultado['success']) {
		// Redirigir al catálogo de vendedores si el registro fue exitoso
		header('Location: ../views/vendedores/catalogo_vendedores.php?registro=exito');
		exit;
	} else {
		// Mostrar mensaje de error simple en HTML
		echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:sans-serif;padding:2rem;"><h2 style="color:red;">Error al registrar la tienda</h2><p>' . htmlspecialchars($resultado['message']) . '</p><a href="../views/vendedores/registro_tienda.php">Volver al registro</a></body></html>';
		exit;
	}
}
