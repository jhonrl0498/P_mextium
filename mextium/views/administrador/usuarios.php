<?php
require_once __DIR__ . '/../../model/conexion.php';
$usuarios = $pdo->query('SELECT * FROM usuarios')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Usuarios - Administración Mextium</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
	<h2 class="mb-4 text-center">Listado de Usuarios</h2>
	<div class="table-responsive">
		<table class="table table-striped table-hover align-middle">
			<thead class="table-dark">
				<tr>
					<th>ID</th>
					<th>Nombre</th>
					<th>Apellido</th>
					<th>Email</th>
					<th>Teléfono</th>
					<th>Dirección</th>
					<th>Cédula</th>
					<th>Rol</th>
					<th>Fecha registro</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($usuarios as $u): ?>
			<tr>
				<td><?= htmlspecialchars($u['id']) ?></td>
				<td><?= htmlspecialchars($u['nombre']) ?></td>
				<td><?= htmlspecialchars($u['apellido']) ?></td>
				<td><?= htmlspecialchars($u['email']) ?></td>
				<td><?= htmlspecialchars($u['telefono']) ?></td>
				<td><?= htmlspecialchars($u['direccion']) ?></td>
				<td><?= htmlspecialchars($u['cedula']) ?></td>
				<td><?= htmlspecialchars($u['rol_id']) ?></td>
				<td><?= htmlspecialchars($u['fecha_registro']) ?></td>
				<td>
					<button class="btn btn-warning btn-sm mb-1 btn-suspender" data-id="<?= htmlspecialchars($u['id']) ?>"><i class="fas fa-user-slash"></i> Suspender</button>
					<button class="btn btn-success btn-sm mb-1 btn-activar-usuario" data-id="<?= htmlspecialchars($u['id']) ?>"><i class="fas fa-user-check"></i> Activar</button>
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
// Acción AJAX para suspender usuario
document.querySelectorAll('.btn-suspender').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas suspender este usuario?')) return;
		const id = this.getAttribute('data-id');
		fetch('usuarios_suspender.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				this.closest('tr').remove();
			} else {
				alert('No se pudo suspender el usuario.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});

// Acción AJAX para activar usuario
document.querySelectorAll('.btn-activar-usuario').forEach(btn => {
	btn.addEventListener('click', function() {
		if (!confirm('¿Seguro que deseas activar este usuario?')) return;
		const id = this.getAttribute('data-id');
		fetch('usuarios_activar.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: 'id=' + encodeURIComponent(id)
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				alert('Usuario activado correctamente.');
				// Opcional: podrías recargar la página o actualizar el estado visualmente
			} else {
				alert('No se pudo activar el usuario.');
			}
		})
		.catch(() => alert('Error de conexión.'));
	});
});
</script>
</body>
</html>
