<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../mextium.php');
    exit();
}
require_once __DIR__ . '/../../model/productos_model.php';
$productosModel = new ProductosModel();
$user_id = $_SESSION['user_id'];
$productos = $productosModel->obtenerProductosPorVendedor($user_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e9ecef 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .productos-header {
            background: linear-gradient(90deg, #2d44aa 0%, #13469d 100%);
            color: #fff;
            padding: 2rem 0 1.2rem 0;
            text-align: center;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 24px rgba(44,68,170,0.10);
        }
        .productos-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .table-responsive {
            margin-top: 2rem;
        }
        .btn-agregar {
            background: #2d44aa;
            color: #fff;
            font-weight: 600;
        }
        .btn-agregar:hover {
            background: #1a2c6b;
            color: #fff;
        }
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        @media (max-width: 768px) {
            .productos-header h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="productos-header">
        <h1><i class="fas fa-boxes"></i> Mis Productos</h1>
        <p class="mt-2 mb-0">Administra y visualiza tus productos publicados</p>
    </div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="../productos/agregar_producto.php" class="btn btn-agregar"><i class="fas fa-plus me-1"></i>Agregar producto</a>
            <a href="../mextium.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Volver al inicio</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white rounded shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Peso</th>
                        <th>Dimensiones</th>
                        <th>Color</th>
                        <th>Marca</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr><td colspan="11" class="text-center text-muted">No tienes productos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <?php
                                $img = trim($producto['imagen'] ?? '');
                                if ($img && file_exists(__DIR__ . '/../../' . ltrim($img, '/'))) {
                                    $img_url = '../../' . ltrim($img, '/');
                                } else {
                                    $img_url = '../../public/no-image.png';
                                }
                                ?>
                                <img src="<?= htmlspecialchars($img_url) ?>" class="producto-img" alt="">
                            </td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td><?= htmlspecialchars($producto['categoria']) ?></td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($producto['stock']) ?></td>
                            <td><?= ($producto['peso'] ? htmlspecialchars($producto['peso']) . ' ' . htmlspecialchars($producto['peso_unidad']) : '-') ?></td>
                            <td><?= ($producto['largo'] || $producto['ancho'] || $producto['alto'] ? htmlspecialchars($producto['largo']) . 'x' . htmlspecialchars($producto['ancho']) . 'x' . htmlspecialchars($producto['alto']) . ' ' . htmlspecialchars($producto['dimensiones_unidad']) : '-') ?></td>
                            <td><?= htmlspecialchars($producto['color'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($producto['marca'] ?? '-') ?></td>
                            <td>
                                <?php if (($producto['activo'] ?? 1) == 1): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="../productos/eliminar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar este producto?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
