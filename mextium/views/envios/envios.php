<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: inicio_sesion.php');
    exit();
}
require_once __DIR__ . '/../../controller/guia_envio_controller.php';
$orden_id = $_GET['orden_id'] ?? null;
$controller = new GuiaEnvioController();
$guias = $orden_id ? $controller->obtenerGuiasPorOrden($orden_id) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envíos de tu compra - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3A5AFF;
            --secondary-color: #274BDB;
            --accent-color: #4F6FE8;
            --dark-color: #1A237E;
            --background-color: #EAF1FF;
            --shadow-card: 0 8px 32px rgba(58,90,255,0.10);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background-color);
            min-height: 100vh;
            color: var(--dark-color);
        }
        .envios-container {
            max-width: 700px;
            margin: 3rem auto;
        }
        .envios-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
        }
        .envio-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: var(--shadow-card);
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 8px solid var(--primary-color);
        }
        .envio-label {
            color: var(--secondary-color);
            font-weight: 700;
        }
        .envio-tracking {
            font-size: 1.1rem;
            color: var(--primary-color);
            font-weight: 700;
        }
        .envio-link {
            color: var(--accent-color);
            text-decoration: underline;
        }
        @media (max-width: 700px) {
            .envios-container { padding: 0 0.5rem; }
            .envios-title { font-size: 1.3rem; }
            .envio-card { padding: 1.2rem 0.7rem; }
        }
    </style>
</head>
<body>
    <div class="container envios-container">
        <div class="envios-title">
            <i class="fas fa-truck me-2"></i>Envíos de tu compra
        </div>
        <?php if ($guias && count($guias) > 0): ?>
            <?php foreach ($guias as $guia): ?>
                <div class="envio-card mb-4">
                    <div class="mb-2 envio-label">Tracking:</div>
                    <div class="envio-tracking mb-2"><?= htmlspecialchars($guia['tracking']) ?: 'No disponible' ?></div>
                    <?php if (!empty($guia['label_url'])): ?>
                        <a href="<?= htmlspecialchars($guia['label_url']) ?>" class="envio-link" target="_blank"><i class="fas fa-file-pdf me-1"></i>Ver etiqueta de envío</a>
                    <?php endif; ?>
                    <?php if (!empty($guia['datos_envio'])): ?>
                        <div class="mt-3 small text-muted"><b>Datos de envío:</b> <?= htmlspecialchars($guia['datos_envio']) ?></div>
                    <?php endif; ?>
                    <div class="mt-3 text-end text-secondary small">Creado: <?= htmlspecialchars($guia['fecha_creacion']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No hay envíos registrados para esta orden.</div>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="../mextium.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Volver al inicio</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
