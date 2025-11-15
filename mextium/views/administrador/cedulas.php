<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
    header('Location: ../mextium.php');
    exit();
}
require_once __DIR__ . '/../../model/usuario_model.php';
$model = new UsuarioModel();
$ref = new ReflectionClass($model);
$prop = $ref->getProperty('pdo');
$prop->setAccessible(true);
$pdo = $prop->getValue($model);
$cedulas = [];
try {
    $stmt = $pdo->prepare("SELECT n.*, u.nombre, u.apellido FROM notificaciones n LEFT JOIN usuarios u ON n.usuario_id = u.id WHERE n.tipo = 'cedula_verificacion' ORDER BY n.fecha DESC");
    $stmt->execute();
    $cedulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cédulas para Verificación - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-id-card me-2"></i>Cédulas enviadas para verificación</h2>
    <?php if (!empty($cedulas)): ?>
        <ul class="list-group mb-4">
            <?php foreach ($cedulas as $ced): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?= htmlspecialchars($ced['nombre'] . ' ' . $ced['apellido']) ?></strong> envió su cédula.<br>
                        <small class="text-muted">Fecha: <?= htmlspecialchars($ced['fecha']) ?></small>
                        <?php
                        $cedula_img = null;
                        $pattern = __DIR__ . '/../../cedulas/cedula_' . $ced['usuario_id'] . '_*.{jpg,jpeg,png,webp}';
                        $files = glob($pattern, GLOB_BRACE);
                        if (!empty($files)) {
                            $cedula_img = '/mextium/cedulas/' . basename($files[0]);
                        }
                        ?>
                        <?php if ($cedula_img): ?>
                            <div class="mt-2">
                                <a href="<?= $cedula_img ?>" target="_blank" class="btn btn-outline-info btn-sm">Ver imagen de cédula</a>
                            </div>
                        <?php endif; ?>
                    </span>
                    <?php if ($ced['leido'] == 0): ?>
                        <form method="post" style="display:inline;" action="cedulas.php">
                            <input type="hidden" name="notif_id" value="<?= $ced['id'] ?>">
                            <input type="hidden" name="usuario_id" value="<?= $ced['usuario_id'] ?>">
                            <button type="submit" name="accion" value="verificar" class="btn btn-success btn-sm">Verificar</button>
                            <button type="submit" name="accion" value="anular" class="btn btn-danger btn-sm ms-2">Anular</button>
                        </form>
                        <span class="badge bg-warning text-dark ms-2">Pendiente</span>
                    <?php elseif ($ced['leido'] == 2): ?>
                        <span class="badge bg-danger">Anulada</span>
                    <?php else: ?>
                        <span class="badge bg-success">Verificada</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No hay cédulas enviadas para verificación.</div>
    <?php endif; ?>
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Volver al panel</a>
    </div>
</div>
<?php
// Procesar verificación/anulación desde el panel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'], $_POST['usuario_id'], $_POST['accion'])) {
    $notif_id = intval($_POST['notif_id']);
    $usuario_id = intval($_POST['usuario_id']);
    $accion = $_POST['accion'];
    if ($accion === 'verificar') {
        $stmt = $pdo->prepare("UPDATE notificaciones SET leido = 1 WHERE id = ?");
        $stmt->execute([$notif_id]);
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'activo', fecha_actualizacion = NOW() WHERE id = ?");
        $stmt->execute([$usuario_id]);
    } elseif ($accion === 'anular') {
        $stmt = $pdo->prepare("UPDATE notificaciones SET leido = 2 WHERE id = ?");
        $stmt->execute([$notif_id]);
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'suspendido', fecha_actualizacion = NOW() WHERE id = ?");
        $stmt->execute([$usuario_id]);
    }
    header('Location: cedulas.php');
    exit;
}
?>
</body>
</html>
