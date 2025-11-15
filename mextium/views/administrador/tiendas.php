<?php
require_once __DIR__ . '/../../model/conexion.php';
$tiendas = $pdo->query('SELECT * FROM vendedores')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tiendas - Administración Mextium</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
	<h2 class="mb-4 text-center">Listado de Tiendas</h2>
	<div class="table-responsive">
		<table class="table table-striped table-hover align-middle">
			<thead class="table-dark">
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Descripción</th>
					<th>Imagen</th>
					<th>Ciudad</th>
					<th>Usuario</th>
					<th>Fecha creación</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($tiendas as $t): ?>
			<tr>
				<td><?= htmlspecialchars($t['id']) ?></td>
				<td><?= htmlspecialchars($t['nombre_tienda']) ?></td>
				<td><?= htmlspecialchars($t['descripcion_tienda']) ?></td>
				<td><?php if ($t['imagen']): ?><img src="../<?= htmlspecialchars($t['imagen']) ?>" alt="Imagen" style="max-width:60px;max-height:40px;object-fit:cover;"><?php endif; ?></td>
				<td><?= htmlspecialchars($t['ciudad']) ?></td>
				<td><?= htmlspecialchars($t['usuario_id']) ?></td>
				<td><?= htmlspecialchars($t['fecha_creacion']) ?></td>
				<td>
					<button class="btn btn-warning btn-sm mb-1 btn-suspender-tienda" data-id="<?= htmlspecialchars($t['id']) ?>"><i class="fas fa-store-slash"></i> Suspender</button>
					<button class="btn btn-success btn-sm mb-1 btn-activar-tienda" data-id="<?= htmlspecialchars($t['id']) ?>"><i class="fas fa-store"></i> Activar</button>
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
// Acción AJAX para suspender tienda
document.querySelectorAll('.btn-suspender-tienda').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas suspender esta tienda?')) return;
		const id = this.getAttribute('data-id');
		fetch('tiendas_suspender.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				this.closest('tr').remove();
			} else {
				alert('No se pudo suspender la tienda.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});

// Acción AJAX para activar tienda
document.querySelectorAll('.btn-activar-tienda').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas activar esta tienda?')) return;
		const id = this.getAttribute('data-id');
		fetch('tiendas_activar.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				alert('Tienda activada correctamente.');
				// Opcional: podrías recargar la página o actualizar el estado visualmente
			} else {
				alert('No se pudo activar la tienda.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});
</script>
</body>
</html>
