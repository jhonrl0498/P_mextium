<?php
session_start();
require_once __DIR__ . '/../../model/tienda_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/usuarios/inicio_sesion.php');
    exit;
}
    // Eliminado bloqueo y mensaje de verificación de cédula

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
    <title>Panel de Vendedor - Mi Tienda | Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(135deg, #2d44aa 0%, #4A90E2 100%);
            color: #fff;
            width: 240px;
            min-width: 200px;
            padding: 2.5rem 1.2rem 1.2rem 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: relative;
        }
        .sidebar .logo {
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 2.5rem;
            letter-spacing: 1px;
        }
        .sidebar .nav-link {
            color: #e0eaff;
            font-weight: 600;
            font-size: 1.08rem;
            margin-bottom: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: background 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.13);
            color: #fff;
            text-decoration: none;
        }
        .sidebar .logout {
            margin-top: auto;
            color: #ffb3b3;
        }
        .main-content {
            flex: 1;
            padding: 2.5rem 2.5vw 2.5rem 2.5vw;
        }
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
        }
        .dashboard-header h1 {
            font-size: 2.1rem;
            font-weight: 800;
            color: #2d44aa;
            margin: 0;
        }
        .dashboard-header .welcome {
            font-size: 1.1rem;
            color: #4A90E2;
            font-weight: 600;
        }
        .tienda-card {
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.08);
            background: #fff;
            padding: 2rem 2.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }
        .tienda-img {
            width: 120px;
            height: 120px;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
        }
        .badge-estado {
            font-size: 1rem;
            padding: 0.5em 1em;
            border-radius: 50px;
        }
        .stats-row {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .stats-box {
            background: #f1f5f9;
            border-radius: 15px;
            padding: 1.2rem 1.5rem;
            text-align: center;
            min-width: 120px;
            flex: 1 1 0;
        }
        .stats-box h4 {
            margin: 0;
            font-weight: 700;
            color: #2563eb;
            font-size: 1.3rem;
        }
        .stats-box p {
            margin: 0;
            color: #64748b;
            font-size: 0.98rem;
        }
        .quick-actions {
            display: flex;
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }
        .quick-actions .btn {
            font-weight: 600;
            font-size: 1.05rem;
            border-radius: 12px;
            padding: 0.7rem 1.5rem;
        }
        @media (max-width: 1100px) {
            .tienda-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.2rem;
                padding: 1.2rem 1rem;
            }
            .main-content {
                padding: 1.2rem 0.5rem;
            }
        }
        @media (max-width: 900px) {
            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-width: unset;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1.2rem;
            }
            .sidebar .logo {
                margin-bottom: 0;
            }
            .sidebar .logout {
                margin-top: 0;
            }
        }
        @media (max-width: 600px) {
            .dashboard-header h1 {
                font-size: 1.2rem;
            }
            .tienda-img {
                width: 80px;
                height: 80px;
            }
            .stats-row {
                flex-direction: column;
                gap: 0.7rem;
            }
            .quick-actions {
                flex-direction: column;
                gap: 0.7rem;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <nav class="sidebar">
        <div class="logo mb-4"><i class="fas fa-store"></i> Mextium</div>
        <a href="mi_tienda.php" class="nav-link active"><i class="fas fa-home"></i> Panel</a>
        <a href="editar_tienda.php" class="nav-link"><i class="fas fa-edit"></i> Editar Tienda</a>
        <a href="agregar_producto.php" class="nav-link"><i class="fas fa-plus"></i> Agregar Producto</a>
        <a href="mis_productos.php" class="nav-link"><i class="fas fa-box"></i> Mis Productos</a>
        <a href="estadisticas.php" class="nav-link"><i class="fas fa-chart-bar"></i> Estadísticas</a>
        <a href="../../views/usuarios/logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </nav>
    <main class="main-content">
        <div class="dashboard-header">
            <h1>¡Bienvenido, <?php echo htmlspecialchars($tienda['nombre_tienda']); ?>!</h1>
            <div class="welcome">Panel de control de tu tienda</div>
        </div>
        <div class="tienda-card">
            <img src="<?php echo !empty($tienda['imagen']) ? ('/mextium/' . ltrim($tienda['imagen'], '/')) : '/mextium/public/no-image.png'; ?>" class="tienda-img me-4" alt="Imagen de la tienda">
            <div style="flex:1;">
                <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($tienda['nombre_tienda'] ?? 'Nombre de la Tienda'); ?></h2>
                <span class="badge bg-success badge-estado">
                    <?php echo ucfirst($tienda['estado_tienda'] ?? 'activa'); ?>
                </span>
                <p class="mt-3 text-muted"><?php echo htmlspecialchars($tienda['descripcion_tienda'] ?? 'Descripción de la tienda...'); ?></p>
                <div class="stats-row">
                    <div class="stats-box">
                        <h4><?php echo (int)($tienda['total_ventas'] ?? 0); ?></h4>
                        <p>Ventas</p>
                    </div>
                    <div class="stats-box">
                        <h4><?php echo number_format($tienda['calificacion_promedio'] ?? 0, 1); ?> <i class="fas fa-star text-warning"></i></h4>
                        <p>Calificación</p>
                    </div>
                    <div class="stats-box">
                        <h4><?php echo htmlspecialchars($tienda['ciudad'] ?? 'Ciudad'); ?></h4>
                        <p>Ciudad</p>
                    </div>
                    <div class="stats-box">
                        <h4><?php echo htmlspecialchars($tienda['categoria_principal'] ?? 'Categoría'); ?></h4>
                        <p>Categoría</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="quick-actions mb-4">
            <a href="../productos/agregar_producto.php" class="btn btn-success"><i class="fas fa-plus"></i> Agregar Producto</a>
            <a href="mis_productos.php" class="btn btn-outline-primary"><i class="fas fa-box"></i> Ver mis productos</a>
            <a href="estadisticas.php" class="btn btn-outline-info"><i class="fas fa-chart-bar"></i> Ver estadísticas</a>
            <a href="editar_tienda.php" class="btn btn-outline-secondary"><i class="fas fa-edit"></i> Editar tienda</a>
        </div>
        <!-- Aquí puedes agregar más widgets, gráficos o tablas del dashboard -->
    </main>
</div>
</body>
</html>