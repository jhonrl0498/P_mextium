<?php
require_once __DIR__ . '/../../model/conexion.php';
$productos = $pdo->query('SELECT * FROM productos')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Productos - Administración Mextium</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
	<h2 class="mb-4 text-center">Listado de Productos</h2>
	<div class="table-responsive">
		<table class="table table-striped table-hover align-middle">
			<thead class="table-dark">
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Descripción</th>
					<th>Precio</th>
					<th>Stock</th>
					<th>Categoría</th>
					<th>Vendedor</th>
					<th>Destacado</th>
					<th>Estado</th>
					<th>Fecha creación</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($productos as $p): ?>
			<tr>
				<td><?= htmlspecialchars($p['id']) ?></td>
				<td><?= htmlspecialchars($p['nombre']) ?></td>
				<td><?= htmlspecialchars($p['descripcion']) ?></td>
				<td>$<?= number_format($p['precio'], 2) ?></td>
				<td><?= htmlspecialchars($p['stock']) ?></td>
				<td><?= htmlspecialchars($p['categoria_id']) ?></td>
				<td><?= htmlspecialchars($p['vendedor_id']) ?></td>
				<td><?= $p['destacado'] ? 'Sí' : 'No' ?></td>
				<td><?= htmlspecialchars($p['estado']) ?></td>
				<td><?= htmlspecialchars($p['fecha_creacion']) ?></td>
				<td>
					<button class="btn btn-warning btn-sm mb-1 btn-desactivar" data-id="<?= htmlspecialchars($p['id']) ?>"><i class="fas fa-ban"></i> Desactivar</button>
					<button class="btn btn-success btn-sm mb-1 btn-activar-producto" data-id="<?= htmlspecialchars($p['id']) ?>"><i class="fas fa-check"></i> Activar</button>
				</td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="text-center mt-4">
		<a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Volver al dashboard</a>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Acción AJAX para desactivar producto
document.querySelectorAll('.btn-desactivar').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas desactivar este producto?')) return;
		const id = this.getAttribute('data-id');
		fetch('productos_desactivar.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				// Oculta la fila del producto
				this.closest('tr').remove();
			} else {
				alert('No se pudo desactivar el producto.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});

// Acción AJAX para activar producto
document.querySelectorAll('.btn-activar-producto').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas activar este producto?')) return;
		const id = this.getAttribute('data-id');
		fetch('productos_activar.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				alert('Producto activado correctamente.');
				// Opcional: podrías recargar la página o actualizar el estado visualmente
			} else {
				alert('No se pudo activar el producto.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});
</script>
</body>
</html>
