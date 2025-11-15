<?php
session_start();
// Compatibilidad: si no existe rol_id pero sí user_rol, usarlo
if (!isset($_SESSION['rol_id']) && isset($_SESSION['user_rol'])) {
    $_SESSION['rol_id'] = $_SESSION['user_rol'];
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
    header('Location: ../mextium.php');
    exit();
}

// Aquí puedes incluir modelos para estadísticas, usuarios, tiendas, productos, etc.
// require_once ...

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e9ecef 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .admin-header {
            background: linear-gradient(90deg, #2d44aa 0%, #13469d 100%);
            color: #fff;
            padding: 2rem 0 1.2rem 0;
            text-align: center;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 24px rgba(44,68,170,0.10);
        }
        .admin-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .admin-header .fa-crown {
            color: #ffd700;
            margin-right: 10px;
        }
        .admin-nav {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .admin-card {
            border-radius: 18px;
            box-shadow: 0 6px 24px rgba(44,68,170,0.08);
            transition: transform 0.2s;
        }
        .admin-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 32px rgba(44,68,170,0.13);
        }
        .admin-icon {
            font-size: 2.5rem;
            color: #2d44aa;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .admin-header h1 {
                font-size: 1.5rem;
            }
            .admin-nav .btn {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <i class="fas fa-crown"></i> <h1 class="d-inline">ADMINISTRACIÓN DE MEXTIUM</h1>
        <p class="mt-2 mb-0">Panel principal de gestión y monitoreo</p>
    </div>
    <div class="container admin-nav text-center">
        <a href="usuarios.php" class="btn btn-outline-primary m-2"><i class="fas fa-users me-1"></i>Usuarios</a>
        <a href="tiendas.php" class="btn btn-outline-success m-2"><i class="fas fa-store me-1"></i>Tiendas</a>
        <a href="productos.php" class="btn btn-outline-warning m-2"><i class="fas fa-boxes me-1"></i>Productos</a>
        <a href="reportes.php" class="btn btn-outline-danger m-2"><i class="fas fa-chart-line me-1"></i>Reportes</a>
    <!-- <a href="cedulas.php" class="btn btn-outline-warning m-2"><i class="fas fa-id-card me-1"></i>Cédulas</a> -->
        <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3): ?>
            <a href="categorias.php" class="btn btn-outline-info m-2"><i class="fas fa-tags me-1"></i>Categorías</a>
        <?php endif; ?>
    </div>
    <div class="container mt-4">
        <div class="row mb-2">
            <!-- Eliminado el alert de cédulas pendientes -->
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-10 col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <div class="card admin-card text-center p-4 w-100">
                    <div class="admin-icon"><i class="fas fa-users"></i></div>
                    <h5 class="fw-bold">Usuarios</h5>
                    <p class="display-6">--</p>
                </div>
            </div>
            <div class="col-10 col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <div class="card admin-card text-center p-4 w-100">
                    <div class="admin-icon"><i class="fas fa-store"></i></div>
                    <h5 class="fw-bold">Tiendas</h5>
                    <p class="display-6">--</p>
                </div>
            </div>
            <div class="col-10 col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <div class="card admin-card text-center p-4 w-100">
                    <div class="admin-icon"><i class="fas fa-box"></i></div>
                    <h5 class="fw-bold">Productos</h5>
                    <p class="display-6">--</p>
                </div>
            </div>
            <div class="col-10 col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <div class="card admin-card text-center p-4 w-100">
                    <div class="admin-icon"><i class="fas fa-tags"></i></div>
                    <h5 class="fw-bold">Categorías</h5>
                    <p class="display-6">--</p>
                </div>
            </div>
            <div class="col-10 col-sm-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <div class="card admin-card text-center p-4 w-100">
                    <div class="admin-icon"><i class="fas fa-chart-line"></i></div>
                    <h5 class="fw-bold">Reportes</h5>
                    <p class="display-6">--</p>
                </div>
            </div>
            <!-- Tarjeta de cédulas pendiente eliminada -->
        </div>
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="../mextium.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Cargar datos reales en las cards del dashboard solo cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    fetch('dashboard_data.php')
        .then(res => res.json())
        .then(data => {
            const cards = document.querySelectorAll('.admin-card .display-6');
            if (cards[0]) cards[0].textContent = data.usuarios ?? '--';
            if (cards[1]) cards[1].textContent = data.tiendas ?? '--';
            if (cards[2]) cards[2].textContent = data.productos ?? '--';
            if (cards[3]) cards[3].textContent = data.categorias ?? '--';
            if (cards[4]) cards[4].textContent = data.reportes ?? '--';
        })
        .catch(err => {
            // Si hay error, mostrar mensaje de advertencia pero no sobrescribir los valores
            const container = document.querySelector('.container.admin-nav');
            if (container) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-warning mt-3';
                alert.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No se pudieron cargar los datos en tiempo real.';
                container.parentNode.insertBefore(alert, container.nextSibling);
            }
        });
});
</script>
?>
</body>
</html>
