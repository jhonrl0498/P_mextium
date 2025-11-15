<?php
// Forzar mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Incluir modelos necesarios
require_once __DIR__ . '/../../model/usuario_model.php';
require_once __DIR__ . '/../../model/tienda_model.php';
require_once __DIR__ . '/../../model/productos_model.php';

try {
    $tiendaModel = new TiendaModel();

    // Procesar búsqueda si existe
    $termino_busqueda = $_GET['buscar'] ?? '';
    if (!empty($termino_busqueda)) {
        $vendedores = $tiendaModel->buscarTiendas($termino_busqueda);
    } else {
        $vendedores = $tiendaModel->obtenerTiendasConVendedores();
    }

    // Buscar nombre del propietario y contar productos para cada tienda
    $usuarioModel = new UsuarioModel();
    $productosModel = new ProductosModel();
    foreach ($vendedores as &$v) {
        // Nombre del propietario
        if (isset($v['usuario_id'])) {
            $usuario = $usuarioModel->obtenerUsuarioPorId($v['usuario_id']);
            if ($usuario && isset($usuario['nombre'])) {
                $v['propietario'] = $usuario['nombre'] . (isset($usuario['apellido']) ? ' ' . $usuario['apellido'] : '');
            } else {
                $v['propietario'] = 'Usuario #' . $v['usuario_id'];
            }
        } else {
            $v['propietario'] = 'N/D';
        }
        // Contar productos reales
        $v['productos_count'] = 0;
        // Usar usuario_id como identificador para productos
        $usuario_id = isset($v['usuario_id']) ? $v['usuario_id'] : null;
        if ($usuario_id !== null) {
            $productos = $productosModel->obtenerProductosPorVendedor($usuario_id);
            if (is_array($productos)) {
                $v['productos_count'] = count($productos);
            } else {
                $v['productos_count'] = 0;
            }
        }
    }
    unset($v);

    // Obtener estadísticas
    $estadisticas = $tiendaModel->obtenerEstadisticas();
} catch (Throwable $e) {
    echo '<pre style="color:red;">Error fatal: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
    $vendedores = [];
    $estadisticas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Vendedores - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5FAAFF;
            --secondary-color: #4A90E2;
            --accent-color: #667eea;
            --dark-color: #2C3E50;
            --light-color: #F8F9FA;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --verified-color: #00d4aa;
            --gradient-primary: linear-gradient(135deg, #5FAAFF 0%, #4A90E2 100%);
            --gradient-accent: linear-gradient(135deg, #667eea 0%, #667eea 100%);
            --gradient-success: linear-gradient(135deg, #51cf66, #40c057);
            --gradient-verified: linear-gradient(135deg, #00d4aa, #00c4a7);
            --shadow-card: 0 15px 35px rgba(95, 170, 255, 0.15);
            --shadow-hover: 0 20px 40px rgba(95, 170, 255, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark-color);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(95, 170, 255, 0.1);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .btn-outline-primary {
            border-color: rgba(95, 170, 255, 0.3);
            color: var(--primary-color);
            transition: all 0.3s ease;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        /* Header Section */
        .page-header {
            background: #3578e5; /* azul sólido */
            color: #fff;
            padding: 4rem 0 5rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            display: none;
        }

        .page-header .container {
            position: relative;
            z-index: 2;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #fff;
            text-shadow: 1px 1px 8px rgba(30,60,120,0.18);
        }

        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
            color: #e3edff;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            padding: 2rem 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            margin-top: -3rem;
            z-index: 3;
            border-radius: 25px 25px 0 0;
        }

        .filter-input {
            border: 2px solid rgba(95, 170, 255, 0.15);
            border-radius: 15px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
        }

        /* Store Cards */
        .stores-grid {
            padding: 3rem 0;
        }

        .store-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .store-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .store-image {
            position: relative;
            height: 250px;
            overflow: hidden;
            background: var(--gradient-accent);
        }

        .store-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .store-card:hover .store-image img {
            transform: scale(1.05);
        }

        .store-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-accent);
            color: white;
            font-size: 3rem;
        }

        .verified-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--gradient-verified);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 212, 170, 0.3);
        }

        .unverified-badge {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        }

        .store-content {
            padding: 2rem;
        }

        .store-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .vendor-name {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .store-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-item {
            background: rgba(95, 170, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            text-align: center;
            flex: 1;
            min-width: 120px;
        }

        .stat-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--dark-color);
            opacity: 0.7;
        }

        .store-details {
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: rgba(95, 170, 255, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .schedule-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-open {
            background: rgba(40, 167, 69, 0.15);
            color: var(--success-color);
        }

        .status-closed {
            background: rgba(220, 53, 69, 0.15);
            color: var(--danger-color);
        }

        .store-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-enter-store {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            flex: 1;
            justify-content: center;
        }

        .btn-enter-store:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(95, 170, 255, 0.4);
            color: white;
        }

        .btn-contact {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 1rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
        }

        .btn-contact:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Loading Animation */
        .loading-animation {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(95, 170, 255, 0.3);
            border-left-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--dark-color);
            opacity: 0.7;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .store-stats {
                flex-direction: column;
            }
            
            .stat-item {
                min-width: auto;
            }
            
            .store-actions {
                flex-direction: column;
            }
            
            .btn-contact {
                min-width: auto;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.8s ease-out forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../mextium.php">
                <i class="fas fa-cube me-2"></i>Mextium
            </a>
            <div class="d-flex">
                <a href="../mextium.php" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="page-title fade-in">
                <i class="fas fa-store me-3"></i>Catálogo de Vendedores
            </h1>
            <p class="page-subtitle fade-in" style="animation-delay: 0.2s;">
                Descubre las mejores tiendas y productos de nuestros <?php echo isset($estadisticas['total_vendedores']) ? number_format($estadisticas['total_vendedores']) : (isset($estadisticas['total_tiendas']) ? number_format($estadisticas['total_tiendas']) : '0'); ?> vendedores
            </p>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form method="GET" action="">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 15px 0 0 15px; border: 2px solid rgba(95, 170, 255, 0.15);">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                            <input type="text" class="filter-input border-start-0" name="buscar" 
                                   value="<?php echo htmlspecialchars($termino_busqueda); ?>"
                                   placeholder="Buscar tiendas, vendedores o productos..." 
                                   style="border-radius: 0;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 15px 15px 0;">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Stores Grid -->
    <section class="stores-grid">
        <div class="container">
            <?php if (empty($vendedores)): ?>
                <div class="row">
                    <div class="empty-state w-100">
                        <i class="fas fa-search"></i>
                        <h3>No se encontraron vendedores</h3>
                        <p><?php echo !empty($termino_busqueda) ? 'Intenta con otros términos de búsqueda' : 'Aún no hay vendedores registrados'; ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row" id="storesContainer">
                    <?php foreach ($vendedores as $index => $vendedor): ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="store-card slide-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                                <!-- Imagen de la tienda -->
                                <div class="store-image">
                                    <?php if (!empty($vendedor['imagen'])): ?>
                                        <?php
                                            // Si la ruta no es absoluta, prepende el path base
                                            $imgSrc = $vendedor['imagen'];
                                            // Si es URL absoluta, no modificar
                                            if (strpos($imgSrc, 'http') === 0) {
                                                // No modificar
                                            } elseif (strpos($imgSrc, '/mextium/') === 0) {
                                                // Ya tiene el path correcto
                                            } elseif (strpos($imgSrc, '/uploads') === 0) {
                                                $imgSrc = '/mextium' . $imgSrc;
                                            } elseif (strpos($imgSrc, 'uploads') === 0) {
                                                $imgSrc = '/mextium/' . $imgSrc;
                                            } elseif (strpos($imgSrc, '/') === 0) {
                                                $imgSrc = '/mextium' . $imgSrc;
                                            } else {
                                                $imgSrc = '/mextium/' . ltrim($imgSrc, '/');
                                            }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars(isset($vendedor['tienda_nombre']) ? $vendedor['tienda_nombre'] : 'Tienda'); ?>">
                                    <?php else: ?>
                                        <div class="store-placeholder">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Badge de verificación -->
                                    <div class="verified-badge <?php echo !empty($vendedor['verificado']) ? '' : 'unverified-badge'; ?>">
                                        <i class="fas fa-<?php echo !empty($vendedor['verificado']) ? 'check-circle' : 'times-circle'; ?>"></i>
                                        <?php echo !empty($vendedor['verificado']) ? 'Verificado' : 'No Verificado'; ?>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <div class="store-content">
                                    <!-- Nombre de tienda -->
                                    <h3 class="store-name"><?php echo htmlspecialchars(isset($vendedor['nombre_tienda']) ? $vendedor['nombre_tienda'] : (isset($vendedor['tienda_nombre']) ? $vendedor['tienda_nombre'] : 'Tienda')); ?></h3>
                                    
                                    <!-- Nombre del vendedor -->
                                    <div class="vendor-name">
                                        <i class="fas fa-user"></i>
                                        Por <?php echo isset($vendedor['propietario']) ? htmlspecialchars($vendedor['propietario']) : (isset($vendedor['vendedor_nombre']) ? htmlspecialchars($vendedor['vendedor_nombre']) : 'Vendedor'); ?>
                                    </div>

                                    <!-- Estadísticas -->
                                    <div class="store-stats">
                                        <div class="stat-item">
                                            <span class="stat-number"><?php echo isset($vendedor['productos_count']) ? $vendedor['productos_count'] : 'N/D'; ?></span>
                                            <span class="stat-label">Productos</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number"><?php echo isset($vendedor['calificacion_promedio']) ? number_format($vendedor['calificacion_promedio'], 1) : 'N/D'; ?></span>
                                            <span class="stat-label">Rating</span>
                                        </div>
                                    </div>

                                    <!-- Detalles -->
                                    <div class="store-details">
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div>
                                                <strong>Apertura:</strong> <?php echo !empty($vendedor['fecha_creacion']) ? date('d M Y', strtotime($vendedor['fecha_creacion'])) : 'N/D'; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>
                                                <strong>Ciudad:</strong> <?php echo !empty($vendedor['ciudad']) ? htmlspecialchars($vendedor['ciudad']) : 'Bogotá D.C.'; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div>
                                                <strong>Dirección:</strong> <?php echo isset($vendedor['direccion']) ? htmlspecialchars($vendedor['direccion']) : 'N/D'; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Descripción de la tienda -->
                                    <?php if (!empty($vendedor['descripcion_tienda'])): ?>
                                        <div class="store-description mt-2" style="font-size:0.98rem; color:#444;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <?php echo nl2br(htmlspecialchars($vendedor['descripcion_tienda'])); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Acciones -->
                                    <div class="store-actions">
                                        <a href="tienda_detalle.php?id=<?php echo isset($vendedor['id']) ? $vendedor['id'] : (isset($vendedor['vendedor_id']) ? $vendedor['vendedor_id'] : '0'); ?>" class="btn-enter-store">
                                            <i class="fas fa-shopping-bag"></i>
                                            Entrar a Tienda
                                        </a>
                                        <button class="btn-contact" onclick="contactStore(<?php echo isset($vendedor['id']) ? $vendedor['id'] : (isset($vendedor['vendedor_id']) ? $vendedor['vendedor_id'] : 0); ?>)">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para contactar tienda
        function contactStore(storeId) {
            alert(`Contactando tienda del vendedor ${storeId}`);
        }

        // Animaciones al hacer scroll
        window.addEventListener('scroll', function() {
            const cards = document.querySelectorAll('.store-card');
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        });
    </script>
</body>
</html>