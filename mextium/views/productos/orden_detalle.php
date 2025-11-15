<?php
// orden_detalle.php como factura eleganta y descargable
session_start();
require_once __DIR__ . '/../../model/productos_model.php';
require_once __DIR__ . '/../../model/usuario_model.php';
$productosModel = new ProductosModel();
$usuarioModel = new UsuarioModel();
$orden_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$orden = null;
$productos = [];
$usuario = null;
if ($orden_id > 0) {
    require_once __DIR__ . '/../../model/database.php';
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE id = ?');
    $stmt->execute([$orden_id]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($orden) {
        $usuario = $usuarioModel->obtenerUsuarioPorId($orden['usuario_id']);
        $stmt = $pdo->prepare('SELECT op.*, p.nombre, p.imagen FROM ordenes_productos op JOIN productos p ON op.producto_id = p.id WHERE op.orden_id = ?');
        $stmt->execute([$orden_id]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
$esFactura = isset($_GET['factura']) && $_GET['factura'] == 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: 'Inter', sans-serif; }
        .factura-container { max-width: 900px; margin: 2.5rem auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(95,170,255,0.10); padding: 2rem; }
        .factura-title { font-weight: 800; color: #4A90E2; margin-bottom: 2rem; }
        .factura-table th, .factura-table td { vertical-align: middle; }
        .factura-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; background: #f8f9fa; }
        .factura-total { font-size: 1.2rem; font-weight: 700; color: #00b894; }
        .factura-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .factura-logo { font-size: 2rem; font-weight: bold; color: #4A90E2; }
        .factura-descargar { float: right; }
        @media (max-width: 600px) {
            .factura-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="factura-container" id="factura">
        <div class="factura-header">
            <span class="factura-logo"><i class="fas fa-file-invoice-dollar me-2"></i>Mextium</span>
            <?php if ($esFactura): ?>
            <button class="btn btn-success factura-descargar" onclick="window.print()"><i class="fas fa-download me-1"></i> Descargar comprobante</button>
            <?php endif; ?>
        </div>
        <h2 class="factura-title">Factura de compra</h2>
        <?php if (!$orden): ?>
            <div class="alert alert-warning text-center">Orden no encontrada.</div>
        <?php else: ?>
            <div class="mb-4">
                <strong>Factura #:</strong> <?= $orden['id'] ?> <br>
                <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha'])) ?> <br>
                <strong>Estado:</strong> <?= ucfirst($orden['estado']) ?> <br>
                <strong>Cliente:</strong> <?= $usuario ? htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) : 'N/A' ?>
            </div>
            <div class="table-responsive">
                <table class="table factura-table align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; foreach ($productos as $prod): $subtotal = $prod['precio'] * $prod['cantidad']; $total += $subtotal; ?>
                        <tr>
                            <td><img src="<?= !empty($prod['imagen']) ? ('/mextium/' . ltrim($prod['imagen'], '/')) : '/mextium/public/no-image.png' ?>" class="factura-img" alt="<?= htmlspecialchars($prod['nombre']) ?>"></td>
                            <td><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td>$<?= number_format($prod['precio'], 2) ?></td>
                            <td><?= $prod['cantidad'] ?></td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <span class="factura-total">Total: $<?= number_format($total, 2) ?></span>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="/mextium/mextium.php" class="btn btn-outline-primary"><i class="fas fa-home me-1"></i> Volver al inicio</a>
        </div>
    </div>
</body>
</html>
