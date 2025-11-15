<?php
// Vista para mostrar historial de guías de envío del vendedor logueado
session_start();
require_once __DIR__ . '/../../model/guia_envio_model.php';
require_once __DIR__ . '/../../model/database.php';
$pdo = Database::getInstance()->getConnection();

// Suponiendo que el vendedor está logueado y su id está en $_SESSION['vendedor_id']
$vendedor_id = isset($_SESSION['vendedor_id']) ? $_SESSION['vendedor_id'] : 0;
if (!$vendedor_id) {
    echo '<div class="alert alert-danger">Debes iniciar sesión como vendedor para ver tu historial de envíos.</div>';
    exit;
}
// Buscar órdenes del vendedor (ajusta según tu modelo de órdenes)
$stmt = $pdo->prepare("SELECT id FROM ordenes WHERE vendedor_id = ?");
$stmt->execute([$vendedor_id]);
$ordenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
$guias = [];
if ($ordenes) {
    $in = str_repeat('?,', count($ordenes) - 1) . '?';
    $stmt2 = $pdo->prepare("SELECT * FROM guias_envio WHERE orden_id IN ($in) ORDER BY fecha_creacion DESC");
    $stmt2->execute($ordenes);
    $guias = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Guías de Envío (Vendedor)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Guías de Envío de Mis Ventas</h2>
    <?php if (empty($guias)): ?>
        <div class="alert alert-info">No tienes guías de envío registradas.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover mt-4">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Número de Guía</th>
                    <th>PDF</th>
                    <th>Ver Detalles</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($guias as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g['fecha_creacion']) ?></td>
                    <td><?= htmlspecialchars($g['tracking']) ?></td>
                    <td><?php if ($g['label_url']): ?><a href="<?= htmlspecialchars($g['label_url']) ?>" target="_blank">Descargar</a><?php endif; ?></td>
                    <td><button class="btn btn-sm btn-info" onclick="alert('<?= htmlspecialchars($g['datos_envio']) ?>')">Ver</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
