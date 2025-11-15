<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /mextium/views/usuarios/inicio_sesion.php');
    exit;
}
require_once __DIR__ . '/../../model/usuario_model.php';
$model = new UsuarioModel();
$usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);

// Validar si ya está verificado
if ($usuario['estado'] === 'activo') {
    header('Location: verificar_cedula.php?ya_verificado=1');
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = '';
    $frontal_ok = isset($_FILES['cedula_img_frontal']) && $_FILES['cedula_img_frontal']['error'] === UPLOAD_ERR_OK;
    $reverso_ok = isset($_FILES['cedula_img_reverso']) && $_FILES['cedula_img_reverso']['error'] === UPLOAD_ERR_OK;
    if (!$frontal_ok || !$reverso_ok) {
        $mensaje = 'Error al subir ambas imágenes. Intenta de nuevo.';
    } else {
        $img_frontal = $_FILES['cedula_img_frontal'];
        $img_reverso = $_FILES['cedula_img_reverso'];
        $permitidas = ['jpg','jpeg','png','webp'];
        $ext_frontal = strtolower(pathinfo($img_frontal['name'], PATHINFO_EXTENSION));
        $ext_reverso = strtolower(pathinfo($img_reverso['name'], PATHINFO_EXTENSION));
        if (!in_array($ext_frontal, $permitidas) || !in_array($ext_reverso, $permitidas)) {
            $mensaje = 'Formato de imagen no permitido.';
        } else {
            $destino_frontal = __DIR__ . '/../../cedulas/cedula_' . $_SESSION['user_id'] . '_frontal_' . time() . '.' . $ext_frontal;
            $destino_reverso = __DIR__ . '/../../cedulas/cedula_' . $_SESSION['user_id'] . '_reverso_' . time() . '.' . $ext_reverso;
            $ok_frontal = move_uploaded_file($img_frontal['tmp_name'], $destino_frontal);
            $ok_reverso = move_uploaded_file($img_reverso['tmp_name'], $destino_reverso);
            if ($ok_frontal && $ok_reverso) {
                // Aquí iría la integración con OCR para extraer datos de la cédula
                // Simulación: se aprueba manualmente
                // Actualizar estado del usuario a "pendiente" (o mantenerlo si ya está)
                $ref = new ReflectionClass($model);
                $prop = $ref->getProperty('pdo');
                $prop->setAccessible(true);
                $pdo = $prop->getValue($model);
                // Guardar notificación si no existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND tipo = 'cedula_verificacion' AND leido = 0");
                $stmt->execute([$_SESSION['user_id']]);
                if ($stmt->fetchColumn() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO notificaciones (tipo, mensaje, usuario_id, fecha, leido) VALUES (?, ?, ?, NOW(), 0)");
                    $mensaje_notif = "El usuario {$usuario['nombre']} {$usuario['apellido']} ha enviado su cédula (frontal y reverso) para verificación.";
                    $stmt->execute(['cedula_verificacion', $mensaje_notif, $_SESSION['user_id']]);
                }
                $mensaje = '¡Cédula enviada correctamente! Tu cuenta será verificada pronto.';
            } else {
                $mensaje = 'No se pudo guardar ambas imágenes.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Verificación de Cédula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:600px;margin:3rem auto;">
    <h2 class="mb-4 text-center">Verificación de Cédula</h2>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($mensaje) ?></div>
        <?php if (isset($destino_frontal) && file_exists($destino_frontal)): ?>
            <div class="text-center mt-3">
                <p class="mb-2">Vista previa de tu cédula (frontal):</p>
                <img src="<?= '/mextium/cedulas/' . basename($destino_frontal) ?>" alt="Cédula frontal" style="max-width:100%;height:auto;border-radius:12px;border:2px solid #1976d2;box-shadow:0 2px 12px rgba(44,68,170,0.10);">
            </div>
        <?php endif; ?>
        <?php if (isset($destino_reverso) && file_exists($destino_reverso)): ?>
            <div class="text-center mt-3">
                <p class="mb-2">Vista previa de tu cédula (reverso):</p>
                <img src="<?= '/mextium/cedulas/' . basename($destino_reverso) ?>" alt="Cédula reverso" style="max-width:100%;height:auto;border-radius:12px;border:2px solid #1976d2;box-shadow:0 2px 12px rgba(44,68,170,0.10);">
            </div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="../mextium.php" class="btn btn-outline-secondary">Volver al inicio</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
