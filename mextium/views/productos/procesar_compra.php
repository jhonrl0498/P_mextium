<?php
// procesar_compra.php
// Muestra el resumen de la compra antes de confirmar
session_start();
require_once __DIR__ . '/../../model/productos_model.php';
$productosModel = new ProductosModel();
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos = [];
$total = 0;
// Bloquear si el usuario está pendiente
require_once __DIR__ . '/../../model/usuario_model.php';
$usuario = null;
if (isset($_SESSION['user_id'])) {
    $model = new UsuarioModel();
    $usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);
}
// (Bloqueo por verificación de cédula deshabilitado temporalmente)

if (!empty($carrito)) {
    $ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pdo = null;
    if (property_exists($productosModel, 'pdo')) {
        $ref = new ReflectionClass($productosModel);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $pdo = $prop->getValue($productosModel);
    }
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Compra - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: 'Inter', sans-serif; }
        .compra-container { max-width: 800px; margin: 2.5rem auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(95,170,255,0.10); padding: 2rem; }
        .compra-title { font-weight: 800; color: #4A90E2; margin-bottom: 2rem; }
        .compra-table th, .compra-table td { vertical-align: middle; }
        .compra-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; background: #f8f9fa; }
        .compra-total { font-size: 1.2rem; font-weight: 700; color: #00b894; }
    </style>
</head>
<body>
    <div class="compra-container">
        <h2 class="compra-title"><i class="fas fa-credit-card me-2"></i>Resumen de Compra</h2>
        <?php if (empty($carrito) || empty($productos)): ?>
            <div class="alert alert-info text-center">Tu carrito está vacío.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table compra-table align-middle">
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
                        <?php $total = 0; foreach ($productos as $prod): $cantidad = $carrito[$prod['id']]; $subtotal = $prod['precio'] * $cantidad; $total += $subtotal; ?>
                        <tr>
                            <td><img src="<?= !empty($prod['imagen']) ? ('/mextium/' . ltrim($prod['imagen'], '/')) : '/mextium/public/no-image.png' ?>" class="compra-img" alt="<?= htmlspecialchars($prod['nombre']) ?>"></td>
                            <td><?= htmlspecialchars($prod['nombre']) ?></td>
                            <td>$<?= number_format($prod['precio'], 2) ?></td>
                            <td><?= $cantidad ?></td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <span class="compra-total">Total: $<?= number_format($total, 2) ?></span>
            </div>
            <div class="mt-4" style="max-width:500px;margin:0 auto;">
                <h4 class="mb-3" style="color:#1976d2;font-weight:700;">Datos de Envío</h4>
                <div class="card p-3 mb-3 shadow-sm">
                    <div class="mb-2"><span class="fw-bold">Ciudad:</span> Bogotá</div>
                    <div class="mb-2"><span class="fw-bold">Localidad:</span> <?= htmlspecialchars($usuario['localidad'] ?? 'No registrada') ?></div>
                    <div class="mb-2"><span class="fw-bold">Dirección:</span> <?= htmlspecialchars($usuario['direccion'] ?? 'No registrada') ?></div>
                </div>
                <div class="text-end">
                    <form action="finalizar_compra.php" method="post">
                        <button type="submit" class="btn btn-pagar"><i class="fas fa-check"></i> Confirmar y Pagar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="/mextium/views/productos/carrito.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Volver al carrito</a>
        </div>
    </div>
</body>
</html>
