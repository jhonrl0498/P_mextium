
<?php
session_start();
require_once __DIR__ . '/../../model/conexion.php';

$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$pedido = null;
$envio = null;
if ($usuario_id) {
	// Obtener el último pedido realizado
	$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 1");
	$stmt->execute([$usuario_id]);
	$pedido = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($pedido) {
		// Obtener datos de envío
		$stmtEnvio = $pdo->prepare("SELECT * FROM envios WHERE pedido_id = ? LIMIT 1");
		$stmtEnvio->execute([$pedido['id']]);
		$envio = $stmtEnvio->fetch(PDO::FETCH_ASSOC);
		// Convertir fecha a formato local solo con día/mes/año
		$fecha_local = null;
		if (!empty($pedido['fecha'])) {
			$dt = new DateTime($pedido['fecha'], new DateTimeZone('UTC'));
			$dt->setTimezone(new DateTimeZone('America/Mexico_City'));
			$fecha_local = $dt->format('d/m/Y');
		}
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Factura de tu compra - Mextium</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		body { 
			background: linear-gradient(120deg, #5FAAFF 0%, #2d44aa 100%); 
			min-height: 100vh; 
			font-family: 'Inter', system-ui, -apple-system, sans-serif;
		}
		.factura-container { 
			max-width: 800px; 
			margin: 3rem auto; 
			background: #fff; 
			border-radius: 22px; 
			box-shadow: 0 8px 32px rgba(95,170,255,0.18); 
			padding: 2.5rem 2rem; 
		}
		.factura-title { 
			font-weight: 900; 
			color: #2563eb; 
			margin-bottom: 2rem; 
			letter-spacing: 1px; 
			text-shadow: 0 2px 8px #5faaff33; 
		}
		.factura-subtitle {
			color: #2563eb;
			font-size: 1.2rem;
			font-weight: 700;
			border-bottom: 2px solid #e6effd;
			padding-bottom: 0.5rem;
		}
		.factura-content { 
			font-size: 1.08rem; 
			color: #222; 
		}
		.factura-label { 
			color: #1976d2; 
			font-weight: 600; 
		}
		.detalles-pago {
			background: #f8faff;
			border-radius: 12px;
			padding: 1.25rem;
		}
		.metodo-pago {
			background: #f8faff;
			border-radius: 12px;
			padding: 1rem;
			display: flex;
			align-items: center;
			margin-bottom: 1rem;
		}
		.border-top {
			border-color: #e6effd !important;
		}
		.btn-descargar, .btn-enviar { 
			background: linear-gradient(90deg, #2563eb 0%, #5FAAFF 100%); 
			color: #fff; 
			border: none; 
			font-weight: 600; 
			border-radius: 1.5em; 
			padding: 0.8em 2em; 
			margin-right: 1em;
			transition: all 0.3s ease;
		}
		.btn-descargar:hover, .btn-enviar:hover { 
			background: linear-gradient(90deg, #5FAAFF 0%, #2563eb 100%); 
			color: #fff;
			transform: translateY(-2px);
		}
		@media (max-width: 768px) {
			.factura-container {
				margin: 1rem;
				padding: 1.5rem 1rem;
			}
			.detalles-pago, .metodo-pago {
				padding: 1rem;
			}
		}
	</style>
</head>
<body>
	<div class="factura-container">
		<h2 class="factura-title"><i class="fas fa-file-invoice-dollar me-2"></i>Factura de tu compra</h2>
		<?php if (!$pedido): ?>
			<div class="alert alert-info text-center">No se encontró ninguna compra reciente.</div>
		<?php else: ?>
		<div class="factura-content" id="factura-content">
			<div class="mb-4">
				<h5 class="factura-subtitle mb-3"><i class="fas fa-receipt me-2"></i>Detalles de la Compra</h5>
				<div><span class="factura-label">Folio:</span> <?= htmlspecialchars($pedido['folio']) ?></div>
				<div><span class="factura-label">Fecha:</span> <?= htmlspecialchars($fecha_local ?? $pedido['fecha']) ?></div>
				<div><span class="factura-label">Productos:</span> <?= htmlspecialchars($pedido['productos']) ?></div>
			</div>

			<div class="mb-4">
				<h5 class="factura-subtitle mb-3"><i class="fas fa-calculator me-2"></i>Detalles de Pago</h5>
				<div class="detalles-pago">
					<div class="row mb-2">
						<div class="col-7"><span class="factura-label">Subtotal productos:</span></div>
						<div class="col-5 text-end">$<?= number_format($pedido['subtotal'] ?? ($pedido['total'] / 1.19), 2) ?></div>
					</div>
					<div class="row mb-2">
						<div class="col-7"><span class="factura-label">IVA (19%):</span></div>
						<div class="col-5 text-end">$<?= number_format(($pedido['total'] * 0.19) / 1.19, 2) ?></div>
					</div>
					<?php if (isset($pedido['costo_envio'])): ?>
					<div class="row mb-2">
						<div class="col-7"><span class="factura-label">Costo de envío:</span></div>
						<div class="col-5 text-end">$<?= number_format($pedido['costo_envio'], 2) ?></div>
					</div>
					<?php endif; ?>
					<div class="row mt-2 border-top pt-2">
						<div class="col-7"><span class="factura-label">Total:</span></div>
						<div class="col-5 text-end fw-bold text-primary">$<?= number_format($pedido['total'], 2) ?></div>
					</div>
				</div>
			</div>

			<div class="mb-4">
				<h5 class="factura-subtitle mb-3"><i class="fas fa-credit-card me-2"></i>Método de Pago</h5>
				<div class="metodo-pago">
					<img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/logo-mercadopago.png" alt="Mercado Pago" style="height: 25px;" class="me-2">
					<span>Mercado Pago</span>
				</div>
				<div><span class="factura-label">Estatus:</span> <?= htmlspecialchars($pedido['estatus']) ?></div>
			</div>

			<?php if ($envio): ?>
			<div class="mb-4">
				<h5 class="factura-subtitle mb-3"><i class="fas fa-shipping-fast me-2"></i>Datos de Envío</h5>
			<div><b>Destinatario:</b> <?= htmlspecialchars($envio['nombre_destinatario']) ?></div>
			<div><b>Dirección:</b> <?= htmlspecialchars($envio['direccion']) ?></div>
			<div><b>Teléfono:</b> <?= htmlspecialchars($envio['telefono']) ?></div>
			<div><b>Ciudad:</b> <?= htmlspecialchars($envio['ciudad']) ?></div>
			<div><b>Estado:</b> <?= htmlspecialchars($envio['estado']) ?></div>
			<div><b>Código Postal:</b> <?= htmlspecialchars($envio['codigo_postal']) ?></div>
			<?php endif; ?>
		</div>
		<div class="mt-4 d-flex flex-wrap gap-2">
			<button class="btn-descargar" id="btnDescargarPDF"><i class="fas fa-download me-1"></i>Descargar PDF</button>
			<button class="btn-enviar" id="btnEnviarFactura"><i class="fas fa-envelope me-1"></i>Enviar por correo</button>
		</div>
		<div id="factura-msg" class="mt-3"></div>
		<?php endif; ?>
		<div class="text-center mt-4">
			<a href="/mextium/views/envios/mis_pedidos.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Volver a mis pedidos</a>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
	<script>
	document.getElementById('btnDescargarPDF')?.addEventListener('click', function() {
		var doc = new window.jspdf.jsPDF();
		var blue1 = '#1976d2';
		var blue2 = '#5FAAFF';
		doc.setFillColor(255,255,255);
		doc.rect(0, 0, 210, 297, 'F');
		doc.setFont('times', 'bold');
		doc.setFontSize(20);
		doc.setTextColor(25, 118, 210);
		doc.text('Factura de compra', 105, 35, {align: 'center'});
		doc.setFont('times', 'normal');
		doc.setFontSize(14);
		doc.setTextColor(44, 62, 80);
		doc.text('Mextium', 105, 45, {align: 'center'});
		var factura = document.getElementById('factura-content');
		var productos = factura.querySelectorAll('div')[2]?.textContent.replace('Productos:', '').trim() || '';
		var total = factura.querySelectorAll('div')[3]?.textContent.replace('Total:', '').trim() || '';
		var vendedor = <?= json_encode($nombre_tienda) ?>;
		var estado = 'Pendiente';
		var fecha = factura.querySelectorAll('div')[1]?.textContent.replace('Fecha:', '').trim() || '';
		var y = 65;
		doc.setFont('times', 'bold');
		doc.setFontSize(14);
		doc.setTextColor(25, 118, 210);
		doc.text('Balance General de Compra', 105, y, {align: 'center'});
		y += 12;
		doc.setFont('times', 'normal');
		doc.setFontSize(12);
		doc.setTextColor(44, 62, 80);
		doc.text('Fecha: ' + (fecha || '-'), 105, y, {align: 'center'});
		y += 12;
		var tableY = y;
		var colX = [40, 120, 180];
		doc.setFont('times', 'bold');
		doc.setTextColor(25, 118, 210);
		doc.text('Producto', colX[0], tableY);
		doc.text('Precio', colX[1], tableY);
		doc.text('Estado', colX[2], tableY);
		doc.setLineWidth(0.5);
		doc.setDrawColor(25, 118, 210);
		doc.line(20, tableY+2, 190, tableY+2);
		doc.setFont('times', 'normal');
		doc.setTextColor(44, 62, 80);
		var productosArr = productos ? productos.split(',') : [];
		var rowY = tableY + 10;
		if (productosArr.length === 0 || !productosArr[0].trim()) {
			doc.text('No hay productos', 105, rowY, {align: 'center'});
			rowY += 10;
		} else {
			productosArr.forEach(function(prod, idx) {
				var prodText = prod ? prod.trim() : '-';
				var precioText = total ? total : '-';
				var estadoText = estado ? estado : '-';
				doc.text(prodText, colX[0], rowY);
				doc.text(precioText, colX[1], rowY);
				doc.text(estadoText, colX[2], rowY);
				rowY += 10;
			});
		}
		y = rowY + 10;
		var envio = factura.querySelector('.mb-2');
		if (envio) {
			doc.setFont('times', 'bold');
			doc.setTextColor(25, 118, 210);
			doc.text('Datos de Envío', 105, y, {align: 'center'});
			y += 10;
			var envioDatos = factura.querySelectorAll('div');
			for (var i = 6; i < envioDatos.length; i++) {
				var txt = envioDatos[i].textContent || '-';
				doc.setFont('times', 'normal');
				doc.setTextColor(44, 62, 80);
				doc.text(txt, 105, y, {align: 'center'});
				y += 10;
			}
		}
		doc.setFont('times', 'italic');
		doc.setFontSize(12);
		doc.setTextColor(25, 118, 210);
		doc.text('Gracias por tu compra en Mextium', 105, 285, {align: 'center'});
		doc.save('factura_mextium.pdf');
	});

	document.getElementById('btnEnviarFactura')?.addEventListener('click', function() {
		var doc = new window.jspdf.jsPDF();
		// ...genera el PDF igual que en btnDescargarPDF...
		// Colores base
		var blue1 = '#1976d2';
		var blue2 = '#5FAAFF';
		doc.setFillColor(255,255,255);
		doc.rect(0, 0, 210, 297, 'F');
		doc.setFont('times', 'bold');
		doc.setFontSize(20);
		doc.setTextColor(25, 118, 210);
		doc.text('Factura de compra', 105, 35, {align: 'center'});
		doc.setFont('times', 'normal');
		doc.setFontSize(14);
		doc.setTextColor(44, 62, 80);
		doc.text('Mextium', 105, 45, {align: 'center'});
		var factura = document.getElementById('factura-content');
		var productos = factura.querySelectorAll('div')[2]?.textContent.replace('Productos:', '').trim() || '';
		var total = factura.querySelectorAll('div')[3]?.textContent.replace('Total:', '').trim() || '';
		var vendedor = <?= json_encode($nombre_tienda) ?>;
		var estado = 'Pendiente';
		var fecha = factura.querySelectorAll('div')[1]?.textContent.replace('Fecha:', '').trim() || '';
		var y = 65;
		doc.setFont('times', 'bold');
		doc.setFontSize(14);
		doc.setTextColor(25, 118, 210);
		doc.text('Balance General de Compra', 105, y, {align: 'center'});
		y += 12;
		doc.setFont('times', 'normal');
		doc.setFontSize(12);
		doc.setTextColor(44, 62, 80);
		doc.text('Fecha: ' + fecha, 105, y, {align: 'center'});
		y += 12;
		var tableY = y;
		var colX = [40, 120, 180];
		doc.setFont('times', 'bold');
		doc.setTextColor(25, 118, 210);
		doc.text('Producto', colX[0], tableY);
		doc.text('Precio', colX[1], tableY);
		doc.text('Estado', colX[2], tableY);
		doc.setLineWidth(0.5);
		doc.setDrawColor(25, 118, 210);
		doc.line(20, tableY+2, 190, tableY+2);
		doc.setFont('times', 'normal');
		doc.setTextColor(44, 62, 80);
		var productosArr = productos.split(',');
		var rowY = tableY + 10;
		productosArr.forEach(function(prod, idx) {
			doc.text(prod.trim(), colX[0], rowY);
			doc.text(total, colX[1], rowY);
			doc.text(estado, colX[2], rowY);
			rowY += 10;
		});
		y = rowY + 10;
		var envio = factura.querySelector('.mb-2');
		if (envio) {
			doc.setFont('times', 'bold');
			doc.setTextColor(25, 118, 210);
			doc.text('Datos de Envío', 105, y, {align: 'center'});
			y += 10;
			var envioDatos = factura.querySelectorAll('div');
			for (var i = 6; i < envioDatos.length; i++) {
				var txt = envioDatos[i].textContent;
				doc.setFont('times', 'normal');
				doc.setTextColor(44, 62, 80);
				doc.text(txt, 105, y, {align: 'center'});
				y += 10;
			}
		}
		doc.setFont('times', 'italic');
		doc.setFontSize(12);
		doc.setTextColor(25, 118, 210);
		doc.text('Gracias por tu compra en Mextium', 105, 285, {align: 'center'});
		// Obtener el PDF en base64
		var pdfBase64 = doc.output('datauristring');
		var xhr = new XMLHttpRequest();
		xhr.open('POST', '/mextium/views/productos/enviar_factura.php', true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				var msg = document.getElementById('factura-msg');
				if (xhr.status === 200) {
					msg.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Factura enviada correctamente.</span>';
				} else {
					msg.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Error al enviar la factura.</span>';
				}
			}
		};
		xhr.send('pdf=' + encodeURIComponent(pdfBase64));
	});
	</script>
</body>
</html>
