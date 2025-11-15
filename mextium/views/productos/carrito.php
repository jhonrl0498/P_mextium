<?php
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
    // Obtener detalles de los productos en el carrito
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
    <title>Carrito de Compras - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: 'Inter', sans-serif; }
        .carrito-container { max-width: 800px; margin: 2.5rem auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(95,170,255,0.10); padding: 2rem; }
    .carrito-title { font-weight: 800; color: #2d44aa; margin-bottom: 2rem; }
    .carrito-table th, .carrito-table td { vertical-align: middle; }
    .carrito-img { width: 70px; height: 70px; object-fit: cover; border-radius: 12px; background: #e3eaff; }
    .carrito-total { font-size: 1.3rem; font-weight: 700; color: #2d44aa; }
    .btn-vaciar { background: linear-gradient(90deg, #5FAAFF 0%, #2d44aa 100%); color: #fff; border-radius: 30px; font-weight: 600; border: none; }
    .btn-vaciar:hover { background: linear-gradient(90deg, #2d44aa 0%, #5FAAFF 100%); }
    .btn-pagar { background: linear-gradient(90deg, #2d44aa 0%, #5FAAFF 100%); color: #fff; border-radius: 30px; font-weight: 700; border: none; }
    .btn-pagar:hover { background: linear-gradient(90deg, #5FAAFF 0%, #2d44aa 100%); }
    .btn-outline-primary { border-color: #2d44aa; color: #2d44aa; }
    .btn-outline-primary:hover { background: #2d44aa; color: #fff; }
    </style>
</head>
<body>
    <div class="carrito-container">
        <h2 class="carrito-title"><i class="fas fa-shopping-cart me-2"></i>Carrito de Compras</h2>
        <?php if (empty($carrito) || empty($productos)): ?>
            <div class="alert alert-info text-center">Tu carrito está vacío.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table carrito-table align-middle">
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
                        <?php foreach ($productos as $prod): ?>
                            <?php $cantidad = $carrito[$prod['id']]; $subtotal = $prod['precio'] * $cantidad; $total += $subtotal; ?>
                            <tr>
                                <td><img src="<?= !empty($prod['imagen']) ? ('/mextium/' . ltrim($prod['imagen'], '/')) : '/mextium/public/no-image.png' ?>" class="carrito-img" alt="<?= htmlspecialchars($prod['nombre']) ?>"></td>
                                <td><?= htmlspecialchars($prod['nombre']) ?></td>
                                <td>$<?= number_format($prod['precio'], 2, '.', '') ?></td>
                                <td>
                                    <div class="input-group input-group-sm justify-content-center" style="max-width: 150px;">
                                        <button class="btn btn-outline-secondary btn-cantidad-menos" type="button" data-producto-id="<?= $prod['id'] ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control text-center cantidad-input" value="<?= $cantidad ?>" data-producto-id="<?= $prod['id'] ?>" style="max-width: 40px;" readonly>
                                        <button class="btn btn-outline-secondary btn-cantidad-mas" type="button" data-producto-id="<?= $prod['id'] ?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-borrar-producto ms-2" type="button" title="Eliminar producto" data-producto-id="<?= $prod['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>$<?= number_format($subtotal, 2, '.', '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="carrito-total" id="carrito-total">Total: $<?= number_format($total, 2, '.', '') ?></span>
                <div>
                    <a href="vaciar_carrito.php" class="btn btn-vaciar me-2" id="btn-vaciar-carrito"><i class="fas fa-trash"></i> Vaciar carrito</a>
                    <a href="procesar_compra.php" class="btn btn-pagar"><i class="fas fa-credit-card"></i> Procesar compra</a>
                </div>
            </div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="../mextium.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Seguir comprando</a>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(function() {
    // Sumar/restar cantidad
    $('.btn-cantidad-mas, .btn-cantidad-menos').click(function() {
        var btn = $(this);
        var productoId = btn.data('producto-id');
        var accion = btn.hasClass('btn-cantidad-mas') ? 'sumar' : 'restar';
        $.post('carrito_actualizar.php', { producto_id: productoId, accion: accion }, function(resp) {
            try { resp = JSON.parse(resp); } catch(e) { resp = {ok:false}; }
            if (resp.ok) {
                var input = $('.cantidad-input[data-producto-id="'+productoId+'"]');
                if (resp.cantidad > 0) {
                    input.val(resp.cantidad);
                    // Actualizar subtotal
                    var fila = input.closest('tr');
                    var precioStr = fila.find('td').eq(2).text().replace('$', '');
                    var precio = Number(precioStr);
                    var cantidad = Number(resp.cantidad);
                    var subtotal = precio * cantidad;
                    fila.find('td').eq(4).text('$' + subtotal.toFixed(2));
                } else {
                    // Eliminar fila si cantidad es 0
                    input.closest('tr').remove();
                }
                actualizarTotal();
                if ($('.carrito-table tbody tr').length === 0) {
                    location.reload();
                }
            }
        });
    });

    // Borrar producto por unidad
    $('.btn-borrar-producto').click(function() {
        var btn = $(this);
        var productoId = btn.data('producto-id');
        $.post('carrito_eliminar.php', { producto_id: productoId }, function(resp) {
            try { resp = JSON.parse(resp); } catch(e) { resp = {ok:false}; }
            if (resp.ok) {
                btn.closest('tr').remove();
                actualizarTotal();
                if ($('.carrito-table tbody tr').length === 0) {
                    location.reload();
                }
            }
        });
    });

    // Actualizar total
    function actualizarTotal() {
        var total = 0;
        $('.carrito-table tbody tr').each(function() {
            var subtotalStr = $(this).find('td').eq(4).text().replace('$', '');
            var subtotal = Number(subtotalStr);
            total += subtotal || 0;
        });
        $('#carrito-total').text('Total: $' + total.toFixed(2));
    }
});
</script>
</body>
</html>
