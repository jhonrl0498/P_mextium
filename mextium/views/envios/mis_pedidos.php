<?php
session_start();
require_once __DIR__ . '/../../model/conexion.php';

// Suponiendo que el id del usuario está en $_SESSION['usuario_id']
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$pedidos = [];
if ($usuario_id) {
	$stmt = $pdo->prepare("SELECT id, folio, fecha, total, estatus, productos FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
	$stmt->execute([$usuario_id]);
	$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mis Pedidos - Mextium</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		.btn-mextium-redirect {
			display: inline-block;
			background: linear-gradient(90deg, #2563eb 0%, #5FAAFF 100%);
			color: #fff;
			border: none;
			font-weight: 800;
			border-radius: 2em;
			padding: 1em 2.5em;
			font-size: 1.25rem;
			box-shadow: 0 4px 24px #2563eb33;
			letter-spacing: 1px;
			margin-top: 1.5em;
			transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
		}
		.btn-mextium-redirect:hover {
			background: linear-gradient(90deg, #5FAAFF 0%, #2563eb 100%);
			color: #fff;
			box-shadow: 0 8px 32px #2563eb44;
			transform: translateY(-2px) scale(1.04);
		}
		body {
			background: linear-gradient(120deg, #fff 0%, #fff 100%);
			font-family: 'Inter', sans-serif;
			min-height: 100vh;
		}
		.pedidos-container {
			max-width: 1000px;
			margin: 2.5rem auto;
			background: #fff;
			border-radius: 22px;
			box-shadow: 0 8px 32px rgba(95,170,255,0.18);
			padding: 2.5rem 2rem 2rem 2rem;
			position: relative;
		}
		.pedidos-title {
			font-weight: 900;
			color: #2563eb;
			margin-bottom: 2.5rem;
			letter-spacing: 1px;
			text-shadow: 0 2px 8px #5faaff33;
		}
		.table-pedidos th {
			background: linear-gradient(90deg, #2563eb 0%, #5FAAFF 100%);
			color: #fff;
			border: none;
			font-size: 1.08rem;
			letter-spacing: 0.5px;
		}
		.table-pedidos td {
			background: #f4f8ff;
			border: none;
			vertical-align: middle;
		}
		.badge-pagado, .badge-enviado, .badge-cancelado {
			font-size: 1rem;
			padding: 0.5em 1.1em;
			border-radius: 1.5em;
			font-weight: 700;
			letter-spacing: 0.5px;
			box-shadow: 0 2px 8px #5faaff22;
		}
		.badge-pagado { background: #2563eb; color: #fff; }
		.badge-enviado { background: #5FAAFF; color: #fff; }
		.badge-cancelado { background: #e74c3c; color: #fff; }
		.btn-detalle-pedido {
			background: linear-gradient(90deg, #2563eb 0%, #5FAAFF 100%);
			color: #fff;
			border: none;
			font-weight: 600;
			border-radius: 1.5em;
			padding: 0.4em 1.2em;
			transition: background 0.2s;
		}
		.btn-detalle-pedido:hover {
			background: linear-gradient(90deg, #5FAAFF 0%, #2563eb 100%);
			color: #fff;
		}
		.input-group-text.bg-primary {
			background: #2563eb !important;
		}
		.form-select {
			min-width: 160px;
			border-radius: 1.5em;
		}
		.table-pedidos thead th {
			border-top-left-radius: 12px;
			border-top-right-radius: 12px;
		}
		@media (max-width: 900px) {
			.pedidos-container { padding: 1.2rem 0.2rem; }
		}
		@media (max-width: 600px) {
			.pedidos-title { font-size: 1.3rem; }
			.table-pedidos th, .table-pedidos td { font-size: 0.97rem; }
			.btn-detalle-pedido { font-size: 0.95rem; padding: 0.3em 0.8em; }
		}
		/* Modal */
		.modal-pedido-bg {
			display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
			background: rgba(37,99,235,0.18); align-items: center; justify-content: center;
		}
		.modal-pedido {
			background: #fff; border-radius: 18px; box-shadow: 0 8px 32px #2563eb33;
			max-width: 420px; width: 95vw; padding: 2.2rem 1.5rem 1.5rem 1.5rem; position: relative;
			animation: modalShow 0.25s;
		}
		@keyframes modalShow { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
		.modal-pedido-close {
			position: absolute; top: 1.1rem; right: 1.1rem; background: none; border: none; font-size: 1.5rem; color: #2563eb; cursor: pointer;
		}
		.modal-pedido-title { font-weight: 800; color: #2563eb; margin-bottom: 1.2rem; font-size: 1.2rem; }
		.modal-pedido-info { margin-bottom: 0.7rem; }
		.modal-pedido-info strong { color: #2d44aa; }
	</style>
</head>
<body>
	<div class="pedidos-container">
		<h2 class="pedidos-title"><i class="fas fa-box-open me-2"></i>Mis Pedidos</h2>
		<?php if (empty($pedidos)): ?>
			<div class="alert alert-info text-center">No tienes pedidos registrados.</div>
		<?php else: ?>
		<div class="table-responsive">
			<table class="table table-pedidos align-middle">
				<thead>
					<tr>
						<th>Folio</th>
						<th>Fecha</th>
						<th>Productos</th>
						<th>Total</th>
						<th>Estatus</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($pedidos as $p): ?>
					<tr data-folio="<?= htmlspecialchars($p['folio']) ?>" data-fecha="<?= htmlspecialchars($p['fecha']) ?>" data-estatus="<?= htmlspecialchars($p['estatus']) ?>">
						<td><strong><?= htmlspecialchars($p['folio']) ?></strong></td>
						<td><?= htmlspecialchars($p['fecha']) ?></td>
						<td><?= htmlspecialchars($p['productos']) ?></td>
						<td>$<?= number_format($p['total'], 2) ?></td>
						<td>
							<?php if ($p['estatus'] === 'Pagado'): ?>
								<span class="badge badge-pagado">Pagado</span>
							<?php elseif ($p['estatus'] === 'Enviado'): ?>
								<span class="badge badge-enviado">Enviado</span>
							<?php else: ?>
								<span class="badge badge-cancelado">Cancelado</span>
							<?php endif; ?>
						</td>
						<td>
							<button class="btn btn-sm btn-primary btn-detalle-pedido" data-id="<?= $p['id'] ?>"><i class="fas fa-eye"></i> Detalle</button>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
	</div>
	<div class="text-center mt-4">
		<button onclick="window.location.href='/mextium/views/mextium.php'" type="button" class="btn-mextium-redirect">
			Ir a Mextium
		</button>
	</div>
<div class="mt-4 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
		<!-- Filtros eliminados -->
</div>
</body>
<!-- Modal Detalle Pedido -->
<div class="modal-pedido-bg" id="modalPedidoBg">
	<div class="modal-pedido" id="modalPedido">
		<button class="modal-pedido-close" id="cerrarModalPedido" title="Cerrar">&times;</button>
		<div class="modal-pedido-title"><i class="fas fa-receipt me-2"></i>Detalle del Pedido</div>
		<div class="modal-pedido-info" id="modalPedidoInfo">
			<!-- Aquí se cargan los datos -->
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
	// Filtros eliminados
	// Modal detalle pedido
	$('.btn-detalle-pedido').on('click', function() {
		var fila = $(this).closest('tr');
		var folio = fila.find('td').eq(0).text();
		var fecha = fila.find('td').eq(1).text();
		var productos = fila.find('td').eq(2).text();
		var total = fila.find('td').eq(3).text();
		var estatus = fila.find('td').eq(4).text();
		var html = '';
		html += '<div class="modal-pedido-info"><strong>Folio:</strong> ' + folio + '</div>';
		html += '<div class="modal-pedido-info"><strong>Fecha:</strong> ' + fecha + '</div>';
		html += '<div class="modal-pedido-info"><strong>Productos:</strong> ' + productos + '</div>';
		html += '<div class="modal-pedido-info"><strong>Total:</strong> ' + total + '</div>';
		html += '<div class="modal-pedido-info"><strong>Estatus:</strong> ' + estatus + '</div>';
		$('#modalPedidoInfo').html(html);
		$('#modalPedidoBg').fadeIn(120);
	});
	$('#cerrarModalPedido, #modalPedidoBg').on('click', function(e) {
		if (e.target === this) {
			$('#modalPedidoBg').fadeOut(120);
		}
	});
	$('#modalPedido').on('click', function(e) {
		e.stopPropagation();
	});
// Eliminar cierre extra de función para que el script funcione
</script>
</html>
</body>
</html>
