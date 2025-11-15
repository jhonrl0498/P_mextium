<?php
// views/vendedores/mextium_tienda.php
session_start();
// Aquí podrías obtener datos de la tienda y usuario si lo deseas
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda | Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #378ef2 100%);
            font-family: 'Inter', sans-serif;
        }
        .tienda-header {
            background: linear-gradient(135deg, #fff 60%, #e3eaff 100%);
            border-radius: 0 0 40px 40px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.13);
            padding: 3rem 1.5rem 2.2rem 1.5rem;
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .tienda-header::before {
            content: '';
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #e3eaff 0%, transparent 70%);
            z-index: 0;
        }
        .tienda-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #fff;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            background: #f8f9fa;
        }
        .tienda-nombre {
            font-size: 2.5rem;
            font-weight: 900;
            color: #2d44aa;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        .tienda-descripcion {
            color: #444;
            font-size: 1.15rem;
            margin-bottom: 1.2rem;
            position: relative;
            z-index: 1;
        }
        .tienda-actions .btn {
            border-radius: 50px;
            font-weight: 700;
            margin: 0 0.5rem 0.5rem 0;
            padding: 0.8rem 2.2rem;
            font-size: 1.08rem;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.08);
            transition: all 0.2s;
        }
        .tienda-actions .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #378ef2 100%);
            border: none;
        }
        .tienda-actions .btn-primary:hover {
            background: linear-gradient(135deg, #378ef2 0%, #667eea 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
        }
        .tienda-actions .btn-outline-primary {
            border: 2px solid #667eea;
            color: #2d44aa;
            background: #fff;
        }
        .tienda-actions .btn-outline-primary:hover {
            background: #e3eaff;
            color: #2d44aa;
            transform: translateY(-2px) scale(1.04);
        }
        .tienda-actions .btn-outline-danger {
            border: 2px solid #ff6b81;
            color: #ff6b81;
            background: #fff;
        }
        .tienda-actions .btn-outline-danger:hover {
            background: #ffdde2;
            color: #c82333;
            transform: translateY(-2px) scale(1.04);
        }
        .productos-section {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.13);
            padding: 2.2rem 1.5rem;
            margin-bottom: 2.5rem;
        }
        .productos-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #2d44aa;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }
        .producto-card {
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
            overflow: hidden;
            background: #f8f9fa;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.3s, transform 0.3s;
            position: relative;
        }
        .producto-card:hover {
            box-shadow: 0 12px 32px rgba(44, 62, 80, 0.18);
            transform: translateY(-4px) scale(1.03);
        }
        .producto-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #e3eaff;
        }
        .producto-info {
            padding: 1.1rem 1rem 0.8rem 1rem;
        }
        .producto-nombre {
            font-size: 1.13rem;
            font-weight: 800;
            color: #2d44aa;
        }
        .producto-precio {
            font-size: 1.13rem;
            font-weight: 800;
            color: #378ef2;
        }
        @media (max-width: 768px) {
            .tienda-header {
                padding: 2rem 0.5rem 1.5rem 0.5rem;
            }
            .productos-section {
                padding: 1rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tienda-header mt-4">
            <img src="../no-image.png" alt="Logo tienda" class="tienda-logo">
            <div class="tienda-nombre">Nombre de la Tienda</div>
            <div class="tienda-descripcion">Descripción breve de la tienda. Aquí puedes poner el eslogan o información relevante.</div>
            <div class="row justify-content-center mb-3">
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="bg-white rounded-4 shadow-sm p-3 h-100 text-center">
                                <div class="mb-2" style="font-size:2rem;color:#378ef2;"><i class="fas fa-chart-line"></i></div>
                                <div style="font-size:1.2rem;font-weight:700;">Estadísticas</div>
                                <div class="text-muted" style="font-size:0.95rem;">Ventas, visitas y más</div>
                                <a href="#" class="stretched-link"></a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white rounded-4 shadow-sm p-3 h-100 text-center">
                                <div class="mb-2" style="font-size:2rem;color:#ffb347;"><i class="fas fa-box"></i></div>
                                <div style="font-size:1.2rem;font-weight:700;">Mis Ventas</div>
                                <div class="text-muted" style="font-size:0.95rem;">Pedidos y entregas</div>
                                <a href="#" class="stretched-link"></a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white rounded-4 shadow-sm p-3 h-100 text-center">
                                <div class="mb-2" style="font-size:2rem;color:#2d44aa;"><i class="fas fa-star"></i></div>
                                <div style="font-size:1.2rem;font-weight:700;">Reseñas</div>
                                <div class="text-muted" style="font-size:0.95rem;">Opiniones de clientes</div>
                                <a href="#" class="stretched-link"></a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white rounded-4 shadow-sm p-3 h-100 text-center">
                                <div class="mb-2" style="font-size:2rem;color:#ff6b81;"><i class="fas fa-headset"></i></div>
                                <div style="font-size:1.2rem;font-weight:700;">Soporte</div>
                                <div class="text-muted" style="font-size:0.95rem;">Ayuda y contacto</div>
                                <a href="#" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tienda-actions mb-2">
                <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Producto</a>
                <a href="#" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Editar Tienda</a>
                <a href="#" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-tags"></i> Categorías</a>
                <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-cog"></i> Configuración</a>
                <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-percentage"></i> Promociones</a>
                <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-users"></i> Clientes</a>
            </div>
        </div>
        <div class="productos-section">
            <div class="productos-title">Mis Productos</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="producto-card">
                        <img src="../no-image.png" class="producto-img" alt="Producto">
                        <div class="producto-info">
                            <div class="producto-nombre">Producto de ejemplo</div>
                            <div class="producto-precio">$0.00</div>
                        </div>
                    </div>
                </div>
                <!-- Más productos aquí -->
            </div>
        </div>
    </div>
</body>
</html>
