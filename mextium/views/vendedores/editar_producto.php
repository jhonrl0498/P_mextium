<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../usuarios/inicio_sesion.php');
    exit();
}
require_once __DIR__ . '/../../model/productos_model.php';
require_once __DIR__ . '/../../model/categorias_model.php';

$id = $_GET['id'] ?? null;
$model = new ProductosModel();
$categoriasModel = new CategoriasModel();
$producto = $id ? $model->obtenerProductosPorVendedor($_SESSION['user_id']) : null;
$categorias = $categoriasModel->obtenerTodas();

// Buscar el producto correcto del vendedor
$producto = $producto ? array_filter($producto, fn($p) => $p['id'] == $id) : [];
$producto = $producto ? array_values($producto)[0] : null;
if (!$producto) {
    echo '<div class="alert alert-danger">Producto no encontrado o no tienes permisos.</div>';
    exit();
}
$mensaje = '';
$tipo_mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $nueva_imagen = $_FILES['nueva_imagen'] ?? null;
    $imagen = $producto['imagen'];
    // Procesar imagen si se sube una nueva
    if ($nueva_imagen && $nueva_imagen['tmp_name']) {
        $ext = strtolower(pathinfo($nueva_imagen['name'], PATHINFO_EXTENSION));
        $nombre_archivo = 'uploads/producto_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $destino = __DIR__ . '/../../' . $nombre_archivo;
        if (move_uploaded_file($nueva_imagen['tmp_name'], $destino)) {
            $imagen = $nombre_archivo;
        }
    }
    // Obtener PDO correctamente
    $pdo = null;
    if (property_exists($model, 'pdo')) {
        $ref = new ReflectionClass($model);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $pdo = $prop->getValue($model);
    }
    if ($pdo) {
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, stock=?, categoria_id=?, fecha_actualizacion=NOW() WHERE id=? AND vendedor_id=?");
        $ok = $stmt->execute([
            $nombre,
            $descripcion,
            $precio,
            $imagen,
            $stock,
            $categoria_id,
            $producto['id'],
            $_SESSION['user_id']
        ]);
        if ($ok) {
            $mensaje = 'Producto actualizado correctamente';
            $tipo_mensaje = 'success';
            // Refrescar datos
            $producto['nombre'] = $nombre;
            $producto['descripcion'] = $descripcion;
            $producto['precio'] = $precio;
            $producto['stock'] = $stock;
            $producto['categoria_id'] = $categoria_id;
            $producto['imagen'] = $imagen;
        } else {
            $mensaje = 'Error al actualizar el producto';
            $tipo_mensaje = 'danger';
        }
    } else {
        $mensaje = 'No se pudo conectar a la base de datos.';
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Mextium</title>
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
        .edit-container {
            max-width: 600px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            padding: 2.7rem 2.2rem 2.2rem 2.2rem;
            border: 1.5px solid #e3eafc;
        }
        .edit-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-label {
            font-weight: 700;
            color: var(--secondary-color);
        }
        .form-control {
            border-radius: 16px;
            border: 2px solid #e3eafc;
            background: #f8fbff;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(58,90,255,0.04);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.18rem rgba(58,90,255,0.13);
        }
        .btn-primary-gradient {
            background: var(--primary-color);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 16px;
            padding: 0.85rem 2.2rem;
            font-size: 1.1rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .btn-primary-gradient:hover {
            box-shadow: 0 8px 24px rgba(58,90,255,0.18);
            transform: translateY(-2px);
        }
        @media (max-width: 700px) {
            .edit-container {
                padding: 1.2rem 0.5rem;
            }
            .edit-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-container mt-5">
            <div class="edit-title">
                <i class="fas fa-pen-to-square me-2"></i>Editar Producto
            </div>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="precio">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($producto['stock']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="categoria_id">Categoría</label>
                    <select class="form-control" id="categoria_id" name="categoria_id" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="imagen">Imagen actual</label><br>
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="../<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual" style="max-width:120px;max-height:80px;border-radius:8px;box-shadow:0 2px 8px #3A5AFF22;">
                    <?php else: ?>
                        <span class="text-muted">Sin imagen</span>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="nueva_imagen">Cambiar imagen</label>
                    <input type="file" class="form-control" id="nueva_imagen" name="nueva_imagen" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary-gradient w-100 mb-2">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="productos.php" class="btn btn-outline-primary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Volver a mis productos
                </a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
