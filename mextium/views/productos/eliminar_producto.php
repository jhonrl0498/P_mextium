<?php
// eliminar_producto.php
session_start();
require_once __DIR__ . '/../../model/productos_model.php';
$productosModel = new ProductosModel();

// Validar que se recibe el ID por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	echo '<div class="alert alert-danger">ID de producto no válido.</div>';
	echo '<a href="/mextium/views/productos/listado_productos.php" class="btn btn-primary mt-3">Volver al listado</a>';
	exit;
}
$id = intval($_GET['id']);

// Intentar eliminar el producto
$exito = $productosModel->eliminarProductoPorId($id);

if ($exito) {
	echo '<div class="alert alert-success">Producto eliminado correctamente.</div>';
} else {
	echo '<div class="alert alert-danger">No se pudo eliminar el producto. Puede que no exista o haya un error.</div>';
}
echo '<a href="/mextium/views/vendedores/mis_productos.php" class="btn btn-primary mt-3">Volver a mis productos</a>';

// Opcional: Redirigir automáticamente después de 2 segundos
echo '<script>setTimeout(function(){ window.location.href = "/mextium/views/vendedores/mis_productos.php"; }, 2000);</script>';
?>
