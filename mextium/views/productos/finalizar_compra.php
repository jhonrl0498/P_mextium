<?php
// finalizar_compra.php
// Procesa la compra y muestra confirmación elegante
session_start();
require_once __DIR__ . '/../../model/productos_model.php';
$productosModel = new ProductosModel();
// Si quieres agregar un costo de envío fijo, puedes definirlo aquí:
$costo_envio = null; // Ejemplo: $costo_envio = 15000;
$productosModel = new ProductosModel();
require_once __DIR__ . '/../../vendor/autoload.php'; // MercadoPago SDK
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
$productosModel = new ProductosModel();
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos = [];
$total = 0;
$preference = null;

if (!empty($carrito)) {
    // Validar usuario logueado
    if (!isset($_SESSION['usuario_id']) && isset($_SESSION['user_id'])) {
        $_SESSION['usuario_id'] = $_SESSION['user_id'];
    }
    if (!isset($_SESSION['usuario_id'])) {
        echo '<div class="alert alert-danger text-center mt-5">Debes iniciar sesión para finalizar la compra.</div>';
        echo '<div class="text-center mt-3"><a href="/mextium/views/usuarios/inicio_sesion.php" class="btn btn-primary">Iniciar sesión</a></div>';
        exit;
    }
    $ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pdo = null;
    if (property_exists($productosModel, 'pdo')) {
        $ref = new ReflectionClass($productosModel);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $pdo = $prop->getValue($productosModel);
    }
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Calcular el total real y obtener el vendedor
    $items = [];
    $vendedor_id = null;
    $productos_pedido = [];
    $subtotal_productos = 0;
    $porcentaje_iva = 0.19; // IVA 19%
    $peso_total = 0;
    $localidad_vendedor = null;
    $localidad_comprador = $_POST['localidad'] ?? null;
    foreach ($productos as $prod) {
        $cantidad = $carrito[$prod['id']];
        $subtotal = $prod['precio'] * $cantidad;
        $subtotal_productos += $subtotal;
        $peso_total += ($prod['peso'] ?? 0) * $cantidad;
        $items[] = [
            'id' => (string)$prod['id'],
            'title' => $prod['nombre'],
            'quantity' => $cantidad,
            'unit_price' => floatval($prod['precio']),
            'currency_id' => 'COP',
        ];
        $productos_pedido[] = $prod['nombre'] . ' x' . $cantidad;
        if (!$vendedor_id && !empty($prod['vendedor_id'])) {
            $vendedor_id = $prod['vendedor_id'];
        }
        if (!$localidad_vendedor && !empty($prod['localidad'])) {
            $localidad_vendedor = $prod['localidad'];
        }
    }

    // Calcular costo de envío según peso
    $costo_envio = 0;
    if ($peso_total <= 0.5) {
        $costo_envio = 7000 - 4000;
    } elseif ($peso_total <= 1) {
        $costo_envio = 9000 - 4000;
    } elseif ($peso_total <= 2) {
        $costo_envio = 11000 - 4000;
    } elseif ($peso_total <= 3) {
        $costo_envio = 13000 - 4000;
    } elseif ($peso_total <= 5) {
        $costo_envio = 16000 - 4000;
    } elseif ($peso_total <= 8) {
        $costo_envio = 19000 - 4000;
    } elseif ($peso_total <= 12) {
        $costo_envio = 24000 - 4000;
    } elseif ($peso_total <= 15) {
        $costo_envio = 29000 - 4000;
    } elseif ($peso_total <= 20) {
        $costo_envio = 35000 - 4000;
    } else {
        $costo_envio = 35000 - 4000;
    }

    // Ajuste por localidad
    $multiplicador_localidad = 1.25;
    if ($localidad_vendedor && $localidad_comprador && $localidad_vendedor === $localidad_comprador) {
        $multiplicador_localidad = 1.10;
    }
    $costo_envio = round($costo_envio * $multiplicador_localidad);

    $iva = round($subtotal_productos * $porcentaje_iva);
    $total = floatval($subtotal_productos) + floatval($iva) + floatval($costo_envio);
    // Agregar desglose de IVA y envío como items informativos para MercadoPago
    $items[] = [
        'id' => 'envio',
        'title' => 'Costo de envío',
        'quantity' => 1,
        'unit_price' => floatval($costo_envio),
        'currency_id' => 'COP',
    ];
    $items[] = [
        'id' => 'iva',
        'title' => 'IVA (19%)',
        'quantity' => 1,
        'unit_price' => floatval($iva),
        'currency_id' => 'COP',
    ];

    // Insertar pedido y datos de envío en la base de datos
    if (isset($_SESSION['usuario_id']) && $total > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/../../model/conexion.php';
        $folio = strtoupper(uniqid('MX'));
        $fecha = date('Y-m-d H:i:s');
        $estatus = 'Pendiente';
        $productos_str = implode(', ', $productos_pedido);
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, folio, fecha, total, estatus, productos) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['usuario_id'],
            $folio,
            $fecha,
            $total,
            $estatus,
            $productos_str
        ]);
        $pedido_id = $pdo->lastInsertId();
        // Guardar datos de envío SOLO si no existe ya para este pedido
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM envios WHERE pedido_id = ?");
        $stmtCheck->execute([$pedido_id]);
        $existeEnvio = $stmtCheck->fetchColumn();
        if (!$existeEnvio) {
            $nombre_destinatario = trim($_POST['nombre_destinatario'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $ciudad = trim($_POST['ciudad'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            $codigo_postal = trim($_POST['codigo_postal'] ?? '');
            $stmtEnvio = $pdo->prepare("INSERT INTO envios (pedido_id, nombre_destinatario, direccion, telefono, ciudad, estado, codigo_postal) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtEnvio->execute([
                $pedido_id,
                $nombre_destinatario,
                $direccion,
                $telefono,
                $ciudad,
                $estado,
                $codigo_postal
            ]);
        }
    }
    // El total ya incluye el costo de envío, no sumar nuevamente

    // Obtener el token de Mercado Pago del vendedor
    $token_vendedor = null;
    if ($vendedor_id) {
        require_once __DIR__ . '/../../model/tienda_model.php';
        $tiendaModel = new TiendaModel();
        $tienda = $tiendaModel->obtenerTiendaPorUsuarioId($vendedor_id);
    // ...
        if ($tienda && !empty($tienda['mercado_pago_token'])) {
            $token_vendedor = $tienda['mercado_pago_token'];
        }
    }

    $error_mp = null;
    // Definir variable debug para evitar warning
    $debug = false;
    if ($token_vendedor) {
        // Configurar el token de acceso del vendedor
        MercadoPagoConfig::setAccessToken($token_vendedor);
        $client = new PreferenceClient();
        try {
            $request = [
                'items' => $items,
                'purpose' => 'wallet_purchase',
                'back_urls' => [
                    'success' => 'https://mextium.com/mextium/views/productos/mercadopago_success.php',
                    'failure' => 'https://mextium.com/mextium/views/productos/mercadopago_failure.php',
                    'pending' => 'https://mextium.com/mextium/views/productos/mercadopago_pending.php',
                ],
                'auto_return' => 'approved',
            ];
            if ($debug) {
                echo '<div class="alert alert-info text-start" style="font-size:0.95em;max-width:480px;margin:0 auto 1.5rem auto;overflow:auto;">';
                echo '<b>Request enviado a MercadoPago:</b><br><pre>' . htmlspecialchars(print_r($request, true)) . '</pre>';
                echo '</div>';
            }
            $preference = $client->create($request);
        } catch (MPApiException $e) {
            $error_mp = '<b>Mercado Pago error:</b> ' . $e->getMessage();
            $apiResponse = $e->getApiResponse();
            if ($apiResponse) {
                $error_mp .= '<br><b>Status code:</b> ' . $apiResponse->getStatusCode();
                $error_mp .= '<br><b>Response (print_r):</b> <pre style="white-space:pre-wrap;max-width:100%;overflow:auto;">' . print_r($apiResponse->getContent(), true) . '</pre>';
                $error_mp .= '<br><b>Response (json_encode):</b> <pre style="white-space:pre-wrap;max-width:100%;overflow:auto;">' . htmlspecialchars(json_encode($apiResponse->getContent(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
            }
        } catch (Exception $e) {
            $error_mp = '<b>Error general:</b> ' . $e->getMessage();
        }
    } else {
        $error_mp = 'El vendedor no tiene configurado su token de Mercado Pago. No es posible procesar el pago.';
    }
}
?>
<div class="container container-finalizar my-5">
    <div class="row">
        <!-- Título Principal -->
        <div class="col-12 text-center mb-4">
            <h2 class="display-6 fw-bold text-primary">Finalizar Compra</h2>
            <p class="text-muted">Estás a un paso de completar tu pedido</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Columna de Productos -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header border-0 bg-white pt-4 px-4">
                    <h4 class="mb-0"><i class="fas fa-box-open text-primary me-2"></i>Productos</h4>
                </div>
                <div class="card-body px-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 d-flex justify-content-between align-items-center">
                            <span>Subtotal</span>
                            <span class="fw-bold">$<?= number_format($subtotal_productos, 0, ',', '.') ?></span>
                        </div>
                        <div class="list-group-item border-0 d-flex justify-content-between align-items-center">
                            <span>IVA (19%)</span>
                            <span class="fw-bold">$<?= number_format($iva, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Envío -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header border-0 bg-white pt-4 px-4">
                    <h4 class="mb-0"><i class="fas fa-truck text-warning me-2"></i>Información de Envío</h4>
                </div>
                <div class="card-body px-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Peso total:</span>
                            <span class="fw-bold"><?= number_format($peso_total, 2, ',', '.') ?> kg</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Tarifa base:</span>
                            <span class="fw-bold">$<?= number_format(($costo_envio / $multiplicador_localidad), 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Multiplicador:</span>
                            <span class="fw-bold"><?= $multiplicador_localidad ?> <?= ($multiplicador_localidad == 1.10) ? '(Misma localidad)' : '(Otra localidad)' ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total envío:</span>
                            <span class="fw-bold text-warning">$<?= number_format($costo_envio, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Pago -->
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-header border-0 bg-white pt-4 px-4">
                    <h4 class="mb-0"><i class="fas fa-wallet text-success me-2"></i>Resumen de Pago</h4>
                </div>
                <div class="card-body px-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 mb-0">Total a pagar:</span>
                            <span class="h4 mb-0 text-success fw-bold">$<?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <div class="small text-muted text-center mb-4">
                            Incluye IVA y gastos de envío
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="<?= htmlspecialchars($preference->init_point ?? '#') ?>" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="fas fa-lock me-2"></i>Pagar Ahora
                        </a>
                        <small class="text-muted d-block">
                            <i class="fas fa-shield-alt me-1"></i>
                            Pago seguro a través de Mercado Pago
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Finalizada - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    body {
        background-color: #f7f9fc;
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    .container-finalizar {
        max-width: 1400px;
    }
    .card {
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }
    .text-primary {
        color: #2563eb !important;
    }
    .text-warning {
        color: #f59e0b !important;
    }
    .text-success {
        color: #10b981 !important;
    }
    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        padding: 1rem;
        font-weight: 600;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }
    .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
        transform: translateY(-2px);
    }
    .list-group-item {
        padding: 1rem 0;
        margin-bottom: 0.5rem;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .display-6 {
        font-weight: 800;
        background: linear-gradient(120deg, #2563eb, #10b981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    hr {
        opacity: 0.1;
        margin: 1.5rem 0;
    }
    @media (max-width: 991.98px) {
        .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
        .container-finalizar {
            margin-top: 1rem !important;
        }
    }
    @media (max-width: 767.98px) {
        .display-6 {
            font-size: 1.75rem;
        }
        .card-header {
            padding-top: 1.5rem !important;
        }
        .card-body {
            padding: 1rem !important;
        }
    }
    </style>
</head>
<body>
    <div class="finalizar-container">
    <?php 
    // DEBUG: Mostrar datos de comprador y vendedor para depuración
    // (Debug eliminado)
    ?>

    <?php if(isset($error_mp)): ?>
        <div class="alert alert-warning mt-4">
            <?php if (strpos($error_mp, 'No puedes pagarte a ti mismo') !== false): ?>
                <b>¡Atención!</b><br>
                MercadoPago no permite que el comprador y el vendedor sean la misma persona o tengan datos personales asociados.<br>
                Por favor, intenta la compra con una cuenta de MercadoPago diferente a la del vendedor.
            <?php else: ?>
                Ocurrió un error al crear la preferencia de pago:<br><?= $error_mp ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/mextium/views/productos/carrito.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Volver al carrito
            </a>
        </div>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>
