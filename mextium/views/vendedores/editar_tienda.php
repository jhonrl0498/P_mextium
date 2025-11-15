<?php
session_start();
require_once __DIR__ . '/../../model/tienda_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/usuarios/inicio_sesion.php');
    exit;
}

$tiendaModel = new TiendaModel();
$tienda = $tiendaModel->obtenerTiendaPorUsuarioId($_SESSION['user_id']);

if (!$tienda) {
    echo '<div style="padding:2rem;color:red;font-weight:bold;max-width:600px;margin:2rem auto;">No tienes una tienda registrada. <a href="registro_tienda.php">Regístrala aquí</a>.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tienda | Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .edit-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.08);
            max-width: 600px;
            margin: 2.5rem auto;
            padding: 2.5rem 2rem 2rem 2rem;
        }
        .edit-title {
            font-weight: 800;
            color: #2d44aa;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #2d44aa;
        }
        .form-control, .form-select {
            border-radius: 12px;
            font-size: 1.05rem;
        }
        .img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
            margin-bottom: 1rem;
            background: #f1f5f9;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2d44aa 0%, #4A90E2 100%);
            border: none;
            font-weight: 700;
            border-radius: 12px;
        }
        .btn-outline-secondary {
            border-radius: 12px;
        }
        @media (max-width: 600px) {
            .edit-card {
                padding: 1.2rem 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="edit-card">
    <h2 class="edit-title"><i class="fas fa-edit me-2"></i>Editar Tienda</h2>
    <form action="../../controller/editar_tienda_controller.php" method="POST" enctype="multipart/form-data">
        <div class="text-center mb-3">
            <img id="imgPreview" src="<?php echo !empty($tienda['imagen']) ? ('/mextium/' . ltrim($tienda['imagen'], '/')) : '/mextium/public/no-image.png'; ?>" class="img-preview" alt="Imagen actual de la tienda">
        </div>
        <div class="mb-3">
        <div class="mb-3">
            <label for="mercado_pago_token" class="form-label">Token de Mercado Pago <span style="color:#888;font-weight:400;font-size:0.95em;">(para recibir pagos directos)</span></label>
            <input class="form-control" type="text" id="mercado_pago_token" name="mercado_pago_token" value="<?php echo htmlspecialchars($tienda['mercado_pago_token'] ?? ''); ?>" placeholder="Pega aquí tu token de acceso de Mercado Pago">
            <div class="form-text">Obtén tu token en <a href="https://www.mercadopago.com.co/developers/panel/credentials" target="_blank">Mercado Pago Developers</a>. Solo tú puedes ver y modificar este dato.</div>
        </div>
            <label for="imagen" class="form-label">Imagen de la tienda</label>
            <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)">
        </div>
        <div class="mb-3">
            <label for="nombre_tienda" class="form-label">Nombre de la tienda</label>
            <input class="form-control" type="text" id="nombre_tienda" name="nombre_tienda" value="<?php echo htmlspecialchars($tienda['nombre_tienda']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion_tienda" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion_tienda" name="descripcion_tienda" rows="3" required><?php echo htmlspecialchars($tienda['descripcion_tienda']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="categoria_principal" class="form-label">Categoría principal</label>
            <input class="form-control" type="text" id="categoria_principal" name="categoria_principal" value="<?php echo htmlspecialchars($tienda['categoria_principal']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input class="form-control" type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($tienda['direccion']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="ciudad" class="form-label">Ciudad</label>
            <input class="form-control" type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($tienda['ciudad']); ?>" required>
        </div>
        <input type="hidden" name="tienda_id" value="<?php echo $tienda['id']; ?>">
        <div class="d-flex justify-content-between mt-4">
            <a href="mi_tienda.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Guardar cambios</button>
        </div>
    </form>
</div>
<script>
function previewImage(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
