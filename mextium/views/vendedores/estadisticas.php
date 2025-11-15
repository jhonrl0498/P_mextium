<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../usuarios/inicio_sesion.php');
    exit();
}
require_once __DIR__ . '/../../model/productos_model.php';
require_once __DIR__ . '/../../model/categorias_model.php';
require_once __DIR__ . '/../../model/conexion.php';

$vendedor_id = $_SESSION['user_id'];
$productosModel = new ProductosModel();
$categoriasModel = new CategoriasModel();

// Productos del vendedor
$productos = $productosModel->obtenerProductosPorVendedor($vendedor_id);
$total_productos = count($productos);

// Ventas e ingresos (simulación: contar productos vendidos y sumar precios)
$ventas = 0;
$ingresos = 0;
$ventas_mes = ["Ene"=>0,"Feb"=>0,"Mar"=>0,"Abr"=>0,"May"=>0,"Jun"=>0,"Jul"=>0,"Ago"=>0,"Sep"=>0,"Oct"=>0,"Nov"=>0,"Dic"=>0];
$top_categorias = [];

// Consulta real de ventas por producto (requiere tabla ordenes_productos y ordenes)
try {
    $stmt = $pdo->prepare("SELECT p.categoria_id, c.nombre as categoria, MONTH(o.fecha) as mes, SUM(op.cantidad) as total_vendidos, SUM(op.cantidad * op.precio_unitario) as total_ingresos
        FROM ordenes_productos op
        JOIN productos p ON op.producto_id = p.id
        JOIN categorias c ON p.categoria_id = c.id
        JOIN ordenes o ON op.orden_id = o.id
        WHERE p.vendedor_id = ?
        GROUP BY p.categoria_id, mes
    ");
    $stmt->execute([$vendedor_id]);
    $cat_ventas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ventas += $row['total_vendidos'];
        $ingresos += $row['total_ingresos'];
        // Por mes
        $mes = intval($row['mes']);
        $meses = [1=>"Ene",2=>"Feb",3=>"Mar",4=>"Abr",5=>"May",6=>"Jun",7=>"Jul",8=>"Ago",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dic"];
        $ventas_mes[$meses[$mes]] += $row['total_vendidos'];
        // Por categoría
        if (!isset($cat_ventas[$row['categoria']])) $cat_ventas[$row['categoria']] = 0;
        $cat_ventas[$row['categoria']] += $row['total_vendidos'];
    }
    $top_categorias = $cat_ventas;
} catch(Exception $e) {
    // Si falla, mostrar todo en cero
    $ventas = 0;
    $ingresos = 0;
    $ventas_mes = ["Ene"=>0,"Feb"=>0,"Mar"=>0,"Abr"=>0,"May"=>0,"Jun"=>0,"Jul"=>0,"Ago"=>0,"Sep"=>0,"Oct"=>0,"Nov"=>0,"Dic"=>0];
    $top_categorias = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Ventas - Mextium</title>
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
        .stats-container {
            max-width: 1100px;
            margin: 2.5rem auto;
        }
        .stats-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2.2rem;
            text-align: center;
        }
        .card-stats {
            background: #fff;
            border-radius: 22px;
            box-shadow: var(--shadow-card);
            padding: 2rem 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .card-stats .stat-value {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--primary-color);
        }
        .card-stats .stat-label {
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 1.1rem;
        }
        .chart-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: var(--shadow-card);
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 900px) {
            .stats-container { padding: 0 0.5rem; }
        }
        @media (max-width: 600px) {
            .stats-title { font-size: 1.3rem; }
            .card-stats, .chart-card { padding: 1.2rem 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="container stats-container">
        <div class="stats-title">
            <i class="fas fa-chart-pie me-2"></i>Estadísticas de tu Tienda
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card-stats">
                    <div class="stat-value"><?= $ventas ?></div>
                    <div class="stat-label">Ventas totales</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stats">
                    <div class="stat-value"><?= $total_productos ?></div>
                    <div class="stat-label">Productos publicados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stats">
                    <div class="stat-value">$<?= number_format($ingresos,0) ?></div>
                    <div class="stat-label">Ingresos estimados</div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5 class="mb-3 text-center" style="color:var(--secondary-color)"><i class="fas fa-chart-pie me-2"></i>Ventas por Categoría</h5>
                    <canvas id="pieCategorias" height="220"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h5 class="mb-3 text-center" style="color:var(--secondary-color)"><i class="fas fa-chart-bar me-2"></i>Ventas por Mes</h5>
                    <canvas id="barVentasMes" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pie chart de categorías
        const pieCategorias = document.getElementById('pieCategorias').getContext('2d');
        new Chart(pieCategorias, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($top_categorias)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($top_categorias)) ?>,
                    backgroundColor: [
                        '#3A5AFF', '#4F6FE8', '#5FAAFF', '#A3B8F8'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: true, position: 'bottom' }
                }
            }
        });
        // Bar chart de ventas por mes
        const barVentasMes = document.getElementById('barVentasMes').getContext('2d');
        new Chart(barVentasMes, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($ventas_mes)) ?>,
                datasets: [{
                    label: 'Ventas',
                    data: <?= json_encode(array_values($ventas_mes)) ?>,
                    backgroundColor: 'rgba(58,90,255,0.7)',
                    borderRadius: 8,
                    maxBarThickness: 38
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
