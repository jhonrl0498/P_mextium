<?php
// Debug: Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
// tienda_detalle.php - Detalle de tienda
// Suponiendo que recibe ?id= en la URL
session_start();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div style="padding:2rem;color:red;font-weight:bold;">ID de tienda no válido.</div>';
    exit;
}
$productos = [];

echo '<!-- Debug: Requiring models... -->';
require_once __DIR__ . '/../../model/tienda_model.php';
require_once __DIR__ . '/../../model/usuario_model.php';
require_once __DIR__ . '/../../model/productos_model.php';
echo '<!-- Debug: Models required. -->';

$tiendaModel = new TiendaModel();
echo '<!-- Debug: TiendaModel instantiated. -->';
$usuarioModel = new UsuarioModel();
echo '<!-- Debug: UsuarioModel instantiated. -->';
$productosModel = new ProductosModel();
echo '<!-- Debug: ProductosModel instantiated. -->';

$tienda = null;
$propietario = '';
try {
    $tienda = $tiendaModel->obtenerTiendaPorId($id);
    if ($tienda && isset($tienda['usuario_id'])) {
        $usuario = $usuarioModel->obtenerUsuarioPorId($tienda['usuario_id']);
        if ($usuario && isset($usuario['nombre'])) {
            $propietario = $usuario['nombre'] . (isset($usuario['apellido']) ? ' ' . $usuario['apellido'] : '');
        } else {
            $propietario = 'Usuario #' . $tienda['usuario_id'];
        }
    }
} catch (Throwable $e) {
    echo '<div style="padding:2rem;color:red;font-weight:bold;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}
if (!$tienda) {
    echo '<div style="padding:2rem;color:red;font-weight:bold;">Tienda no encontrada.</div>';
    exit;
}

// Validar que la conexión PDO esté inicializada
$pdo = null;
if (property_exists($productosModel, 'pdo')) {
    $ref = new ReflectionClass($productosModel);
    $prop = $ref->getProperty('pdo');
    $prop->setAccessible(true);
    $pdo = $prop->getValue($productosModel);
}
if (!$pdo) {
    echo '<div style="padding:2rem;color:red;font-weight:bold;">Error: No se pudo conectar a la base de datos (PDO nulo en ProductosModel).</div>';
    exit;
}

if ($tienda && isset($tienda['id'])) {
    try {
        $productos = $productosModel->obtenerProductosPorVendedor($tienda['id']);
        if (empty($productos) && isset($tienda['usuario_id'])) {
            $productos = $productosModel->obtenerProductosPorVendedor($tienda['usuario_id']);
        }
    } catch (Throwable $e) {
        $productos = [];
    }
}

if ($tienda && isset($tienda['usuario_id'])) {
    // Incrementar visitas de la tienda
    try {
        $stmt = $pdo->prepare("UPDATE vendedores SET visitas_tienda = visitas_tienda + 1 WHERE usuario_id = ?");
        $stmt->execute([$tienda['usuario_id']]);

        // Recuperar el contador actualizado
        $stmt2 = $pdo->prepare("SELECT visitas_tienda FROM vendedores WHERE usuario_id = ?");
        $stmt2->execute([$tienda['usuario_id']]);
        $contadorVisitas = $stmt2->fetchColumn();
    } catch (Throwable $e) {
        echo '<div style="padding:2rem;color:orange;">Excepción: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tienda['nombre_tienda'] ?? 'Tienda'); ?> - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
        }

        .tienda-header {
            background: linear-gradient(135deg, #5FAAFF 0%, #4A90E2 100%);
            color: white;
            padding: 2.5rem 0 2rem 0;
            text-align: center;
            position: relative;
        }

        .tienda-header .store-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(95, 170, 255, 0.18);
            border: 4px solid #fff;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }

        .tienda-header .verified {
            display: inline-block;
            background: linear-gradient(135deg, #00d4aa, #00c4a7);
            color: #fff;
            border-radius: 50px;
            padding: 0.3rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .tienda-header .unverified {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        }

        .tienda-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .tienda-header .owner {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e0eaff;
        }

        .tienda-header .desc {
            font-size: 1.05rem;
            color: #f8f9fa;
            margin-top: 1rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .tienda-info {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(95, 170, 255, 0.10);
            margin: -2rem auto 2rem auto;
            max-width: 1200px;
            width: 100%;
            padding: 1.1rem 1.1rem 0.7rem 1.1rem;
            position: relative;
            z-index: 2;
        }

        .tienda-info .row>div {
            margin-bottom: 0.7rem;
        }

        .tienda-info .info-label {
            color: #4A90E2;
            font-weight: 600;
            font-size: 0.98rem;
            margin-bottom: 0.1rem;
        }

        .tienda-info .info-value {
            color: #222;
            font-weight: 500;
            font-size: 0.97rem;
        }

        @media (max-width: 600px) {
            .tienda-header h1 {
                font-size: 1.3rem;
            }

            .tienda-header .desc {
                font-size: 0.98rem;
            }

            .tienda-info {
                padding: 1.2rem 0.7rem 1rem 0.7rem;
            }
        }

        .tienda-info-row {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: stretch;
            gap: 1.2rem;
            padding: 0.5rem 0;
        }

        .min-width-info {
            min-width: 140px;
            flex: 1 1 0;
            max-width: 200px;
        }

        @media (max-width: 900px) {
            .tienda-info-row {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .min-width-info {
                flex: 1 1 180px;
                max-width: 100%;
            }
        }

        @media (max-width: 600px) {
            .tienda-info-row {
                flex-direction: column;
                gap: 0.2rem;
            }

            .min-width-info {
                min-width: 0;
                max-width: 100%;
            }
        }

        /* PRODUCT CARD COMPACT STYLE */
        .product-card {
            background: #fff;
            border-radius: 28px;
            max-width: 320px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            min-height: 410px;
            max-height: 410px;
            box-shadow: 0 8px 32px rgba(80, 120, 180, 0.13), 0 2px 8px rgba(80, 80, 80, 0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 0 1.2rem 0;
            border: none;
            transition: box-shadow 0.28s, transform 0.22s;
        }

        .product-card:hover {
            box-shadow: 0 16px 48px rgba(95, 170, 255, 0.18), 0 4px 16px rgba(80, 80, 80, 0.13);
            transform: translateY(-7px) scale(1.035);
        }

        .product-card .card-img-top {
            width: 92%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            object-fit: contain;
            border-radius: 20px;
            margin-top: 0.7rem;
            margin-bottom: 1.1rem;
            box-shadow: 0 4px 18px rgba(95, 170, 255, 0.10);
            border: 2px solid #e0e0e0;
            background: #f5f5f5;
            transition: filter 0.2s, box-shadow 0.2s;
        }

        .product-card:hover .card-img-top {
            filter: brightness(1.09) saturate(1.13);
            box-shadow: 0 8px 32px rgba(95, 170, 255, 0.18);
        }

        .product-card .card-body {
            padding: 0 1.2rem 0 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1 1 auto;
            width: 100%;
        }

        .product-card .card-title {
            font-size: 1.22rem;
            font-weight: 800;
            color: #1a2236;
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
            text-align: center;
            min-height: 2.7em;
            max-height: 2.7em;
            line-height: 1.35em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card .card-text {
            font-size: 1.01rem;
            color: #6a6a7a;
            margin-bottom: 1.1rem;
            flex: 1 1 auto;
            text-align: center;
            min-height: 2.1em;
            max-height: 2.1em;
            line-height: 1.05em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card .product-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            width: 100%;
        }

        .product-card .product-price {
            font-weight: 900;
            color: #2d44aa;
            font-size: 1.25rem;
            letter-spacing: 0.01em;
            text-align: center;
        }

        .product-card .btn-comprar {
            width: 90%;
            font-size: 1.07rem;
            font-weight: 700;
            padding: 0.6rem 0;
            background: linear-gradient(90deg, #2d44aa 0%, #5FAAFF 100%);
            color: #fff;
            border: none;
            border-radius: 30px;
            box-shadow: 0 2px 8px rgba(95, 170, 255, 0.13);
            transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
            outline: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5em;
        }

        .product-card .btn-comprar i {
            font-size: 1.18em;
        }

        .product-card .btn-comprar:hover,
        .product-card .btn-comprar:focus {
            background: linear-gradient(90deg, #5FAAFF 0%, #2d44aa 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px rgba(95, 170, 255, 0.18);
        }
    </style>
</head>

<body>
    <div class="tienda-header">
        <div class="card text-white bg-primary"
            style="position: absolute; top: 10px; right: 10px; width: 200px;">
            <div class="card-header">Visitas a la Tienda</div>
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo htmlspecialchars($contadorVisitas); ?>
                </h5>
            </div>
        </div>
        <img class="store-img" src="<?php echo !empty($tienda['imagen']) ? ('/mextium/' . ltrim($tienda['imagen'], '/')) : '../no-image.png'; ?>" alt="Imagen de la tienda">
        <h1>
            <?php echo htmlspecialchars($tienda['nombre_tienda']); ?>
        </h1>
        <?php if (!empty($tienda['verificado'])): ?>
            <div class="badge badge-verified mb-2" style="background:linear-gradient(90deg,#2d44aa,#5FAAFF);color:#fff;font-weight:700;border-radius:12px;padding:0.25em 1em;font-size:1.05rem;display:inline-block;margin-bottom:1.1rem;">
                <i class="fas fa-check-circle me-1"></i> Tienda verificada
            </div>
        <?php else: ?>
            <div class="badge badge-unverified mb-2" style="background:linear-gradient(90deg,#b0bec5,#90caf9);color:#2d44aa;font-weight:700;border-radius:12px;padding:0.25em 1em;font-size:1.05rem;display:inline-block;margin-bottom:1.1rem;">
                <i class="fas fa-times-circle me-1"></i> Tienda no verificada
            </div>
        <?php endif; ?>
        <div class="owner"><i class="fas fa-user"></i> <?php echo htmlspecialchars($propietario); ?></div>
        <?php if (!empty($tienda['descripcion_tienda'])): ?>
            <div class="desc"><i class="fas fa-info-circle me-1"></i> <?php echo nl2br(htmlspecialchars($tienda['descripcion_tienda'])); ?></div>
        <?php endif; ?>
    </div>
    <div class="tienda-info">
        <div class="tienda-info-row">
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-map-marker-alt"></i> Dirección</div>
                <div class="info-value"><?php echo htmlspecialchars($tienda['direccion']); ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-city"></i> Ciudad</div>
                <div class="info-value"><?php echo htmlspecialchars($tienda['ciudad']); ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                <div class="info-value"><?php echo htmlspecialchars($tienda['email'] ?? 'N/D'); ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-phone"></i> Teléfono</div>
                <div class="info-value"><?php echo htmlspecialchars($tienda['telefono'] ?? 'N/D'); ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-calendar-alt"></i> Apertura</div>
                <div class="info-value"><?php echo !empty($tienda['fecha_creacion']) ? date('d M Y', strtotime($tienda['fecha_creacion'])) : 'N/D'; ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-star"></i> Calificación</div>
                <div class="info-value"><?php echo isset($tienda['calificacion_promedio']) ? number_format($tienda['calificacion_promedio'], 1) : 'N/D'; ?></div>
            </div>
            <div class="min-width-info">
                <div class="info-label"><i class="fas fa-shopping-cart"></i> Ventas</div>
                <div class="info-value"><?php echo isset($tienda['total_ventas']) ? number_format($tienda['total_ventas']) : 'N/D'; ?></div>
            </div>
        </div>
    </div>

    <!-- Productos de la tienda (ejemplo) -->
    <section class="container my-4">
        <h2 class="mb-4 text-center" style="font-weight:800;color:#4A90E2;">Productos destacados</h2>
        <div class="row justify-content-center">
            <?php if (empty($productos)): ?>
                <div class="col-12 text-center text-muted">Esta tienda aún no tiene productos registrados.</div>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-4">
                        <div class="product-card">
                            <div style="width:92%;height:180px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:20px;border:2px solid #e0e0e0;margin-top:0.7rem;margin-bottom:1.1rem;">
                                <?php if (!empty($producto['imagen']) && file_exists(__DIR__ . '/../../' . ltrim($producto['imagen'], '/'))): ?>
                                    <img src="<?= '../../' . ltrim($producto['imagen'], '/') ?>" style="max-width:100%;max-height:100%;object-fit:contain;" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                                <?php else: ?>
                                    <img src="../../public/no-image.png" style="max-width:100%;max-height:100%;object-fit:contain;" alt="Sin imagen">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                <div class="product-footer">
                                    <span class="product-price">$<?= number_format($producto['precio'], 2) ?></span>
                                    <button class="btn-comprar" data-producto-id="<?= htmlspecialchars($producto['id']) ?>"><i class="fas fa-cart-plus"></i>Comprar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <div class="text-center mb-4">
        <a href="catalogo_vendedores.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Volver al catálogo</a>
    </div>
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notificación flotante
        function mostrarNotificacion(mensaje, exito = true) {
            let notif = document.createElement('div');
            notif.textContent = mensaje;
            notif.style.position = 'fixed';
            notif.style.top = '30px';
            notif.style.right = '30px';
            notif.style.zIndex = 9999;
            notif.style.background = exito ? 'linear-gradient(90deg,#00d4aa,#5FAAFF)' : '#ff6b6b';
            notif.style.color = '#fff';
            notif.style.padding = '1rem 2rem';
            notif.style.borderRadius = '30px';
            notif.style.boxShadow = '0 4px 18px rgba(95,170,255,0.13)';
            notif.style.fontWeight = '700';
            notif.style.fontSize = '1.05rem';
            notif.style.opacity = '0.97';
            document.body.appendChild(notif);
            setTimeout(() => {
                notif.style.transition = 'opacity 0.5s';
                notif.style.opacity = '0';
                setTimeout(() => notif.remove(), 500);
            }, 1800);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-comprar').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let productoId = this.getAttribute('data-producto-id');
                    fetch('/mextium/controller/carrito_agregar_controller.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'producto_id=' + encodeURIComponent(productoId) + '&cantidad=1'
                        })
                        .then(res => res.json())
                        .then(data => {
                            mostrarNotificacion(data.message, data.success);
                        })
                        .catch(() => mostrarNotificacion('Error al agregar al carrito', false));
                });
            });
        });
    </script>
    </style>

</body>

</html>