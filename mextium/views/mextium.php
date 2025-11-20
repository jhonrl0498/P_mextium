<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Incluir el modelo de usuario para obtener datos completos
require_once __DIR__ . '/../model/usuario_model.php';
require_once __DIR__ . '/../model/tienda_model.php';
require_once __DIR__ . '/../model/productos_model.php';

// Obtener datos del usuario si está logueado
$usuario = null;
$miTienda = null;
if (isset($_SESSION['user_id'])) {
    $model = new UsuarioModel();
    $usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);
    // Compatibilidad: asegurar que $usuario['rol_id'] esté presente
    if ($usuario && isset($usuario['rol_id'])) {
        $usuario['rol_id'] = $usuario['rol_id'];
    } elseif ($usuario && isset($usuario['rol'])) {
        $usuario['rol_id'] = $usuario['rol'];
    }
    $tiendaModel = new TiendaModel();
    $miTienda = $tiendaModel->obtenerTiendaPorUsuarioId($_SESSION['user_id']);
}
// (Redirección a verificación de cédula deshabilitada temporalmente)

// Obtener todas las tiendas y productos para mostrar en la página principal
$tiendaModel = new TiendaModel();
$tiendas = $tiendaModel->obtenerTiendasConVendedores();
$productosModel = new ProductosModel();
// Mostrar solo productos activos y con stock > 0
$productos = [];
try {
    $pdo = null;
    if (property_exists($productosModel, 'pdo')) {
        $ref = new ReflectionClass($productosModel);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $pdo = $prop->getValue($productosModel);
    }
    if ($pdo) {
        $stmt = $pdo->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.estado = 1 AND p.stock > 0 ORDER BY p.fecha_creacion DESC LIMIT 12");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $productos = [];
}

// --- CONTADOR DE VISITAS GLOBAL ---
$conexion = new mysqli("82.197.82.93", "u366162802_santiago", "vU7=5WEQXw", "u366162802_santiago");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$pagina = "principal"; // nombre identificador de la página

$resultado = $conexion->query("SELECT visitas FROM contador_visitas WHERE pagina='$pagina'");
if ($resultado->num_rows > 0) {
    $conexion->query("UPDATE contador_visitas SET visitas = visitas + 1 WHERE pagina='$pagina'");
    $fila = $resultado->fetch_assoc();
    $visitas = $fila['visitas'] + 1;
} else {
    $conexion->query("INSERT INTO contador_visitas (pagina, visitas) VALUES ('$pagina', 1)");
    $visitas = 1;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ffffff;
            --secondary-color: #ffc107;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, #2d44aa 0%, #2d44aa 100%);
            --gradient-secondary: linear-gradient(135deg, #13469d 0%, #13469d 100%);
            --shadow-card: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }

        /* Header Moderno */
        .navbar-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-modern.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .logo-brand {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--gradient-primary);
            color: white !important;
            transform: translateY(-2px);
        }

        .btn-primary-gradient {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Hero Section Ultra Moderno */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #378ef2 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23a)"/><circle cx="800" cy="300" r="100" fill="url(%23a)"/><circle cx="400" cy="700" r="200" fill="url(%23a)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }

        .search-hero {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .search-hero .form-control {
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
        }

        .search-hero .btn {
            border-radius: 50px;
            padding: 0.8rem 2rem;
            background: var(--gradient-secondary);
            border: none;
            color: white;
            font-weight: 600;
        }

        /* Estadísticas Animadas */
        .stats-section {
            background: white;
            padding: 4rem 0;
            margin-top: -100px;
            position: relative;
            z-index: 3;
        }

        .stats-card {
            text-align: center;
            padding: 2rem;
            border-radius: 20px;
            background: white;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Categorías Modernas */
        .categories-section {
            padding: 5rem 0;
            background: var(--light-color);
        }

        .category-card {
            text-align: center;
            padding: 0;
            border-radius: 20px;
            background: white;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 220px;
            overflow: hidden;
        }

        .category-card h4 {
            color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            margin-bottom: 0;
            text-align: left;
            width: 100%;
            padding: 0.7rem 1.2rem 0.3rem 1.2rem;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 3;
        }

        .category-card .category-title-spacer {
            height: 2.7rem;
        }

        .category-card:hover {
            transform: translateY(-10px) scale(1.04);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            filter: brightness(1.08);
        }

        .category-clickable {
            user-select: none;
        }

        /* Productos Destacados */
        .products-section {
            padding: 5rem 0;
        }

        .product-card {
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 250px;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }

        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--gradient-secondary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #212529;
            /* Negro Bootstrap */
        }

        /* Footer Moderno */
        .footer-modern {
            background: var(--dark-color);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-brand {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            transform: translateY(-3px) scale(1.1);
        }

        /* Logo Hero Mejorado */
        .logo-hero-container {
            position: relative;
            padding: 3rem;
        }

        .logo-hero-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(166, 203, 246, 0.3) 0%, rgba(166, 203, 246, 0.3) 50%, transparent 70%);
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        .logo-hero {
            max-height: 350px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(0 15px 40px rgba(28, 110, 204, 0.4));
            transition: all 0.4s ease;
            position: relative;
            z-index: 2;
        }

        .logo-hero:hover {
            transform: scale(1.08) rotate(3deg);
            filter: drop-shadow(0 20px 50px rgba(0, 114, 245, 0.6));
        }

        @keyframes pulse {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.4;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.15);
                opacity: 0.7;
            }
        }

        /* Responsive para el logo */
        @media (max-width: 992px) {
            .logo-hero {
                max-height: 250px;
            }

            .logo-hero-container::before {
                width: 350px;
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .logo-hero {
                max-height: 180px;
            }

            .logo-hero-container::before {
                width: 250px;
                height: 250px;
            }

            .logo-hero-container {
                padding: 2rem;
            }
        }

        @media (max-width: 576px) {
            .logo-hero {
                max-height: 140px;
            }

            .logo-hero-container::before {
                width: 200px;
                height: 200px;
            }

            .logo-hero-container {
                padding: 1.5rem;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .stats-section {
                margin-top: -50px;
            }
        }

        /* Animaciones personalizadas */
        .bounce-in {
            animation: bounceIn 1s ease-out;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Dropdown del usuario */
        .dropdown-menu {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            min-width: 250px;
        }

        .dropdown-header {
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 1rem;
            margin-bottom: 0;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .dropdown-item:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateX(5px);
        }

        .dropdown-item.text-danger:hover {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            opacity: 0.1;
        }

        /* Botón del usuario */
        .btn-outline-primary.dropdown-toggle {
            border-color: rgba(95, 170, 255, 0.3);
            color: var(--dark-color);
        }

        .btn-outline-primary.dropdown-toggle:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
        }

        /* Responsive para el nombre del usuario */
        @media (max-width: 768px) {
            .dropdown-menu {
                min-width: 200px;
            }
        }

        /* Botón de categoría completamente rediseñado */
        .category-action {
            margin-top: 1.5rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }

        .category-card:hover .category-action {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-category-enter {
            background: transparent;
            border: 2px solid #e9ecef;
            color: var(--dark-color);
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-width: 140px;
        }

        /* Efecto de fondo animado */
        .btn-category-enter::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1;
        }

        .btn-category-enter:hover::before {
            left: 0;
        }

        /* Contenido del botón */
        .btn-category-enter span,
        .btn-category-enter i {
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .btn-category-enter:hover {
            border-color: #667eea;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
        }

        .btn-category-enter:hover span {
            letter-spacing: 0.5px;
        }

        .btn-category-enter:hover i {
            transform: translateX(4px) rotate(15deg);
        }

        .btn-category-enter:active {
            transform: translateY(-1px) scale(1.02);
            transition: all 0.1s ease;
        }

        /* Variantes de colores para diferentes categorías */
        .category-card[data-category="moda"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #ff6b9d, #c44569);
        }

        .category-card[data-category="moda"]:hover .btn-category-enter {
            border-color: #ff6b9d;
        }

        .category-card[data-category="tecnologia"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #4fc3f7, #29b6f6);
        }

        .category-card[data-category="tecnologia"]:hover .btn-category-enter {
            border-color: #4fc3f7;
        }

        .category-card[data-category="hogar"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #81c784, #66bb6a);
        }

        .category-card[data-category="hogar"]:hover .btn-category-enter {
            border-color: #81c784;
        }

        .category-card[data-category="deportes"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #ffb74d, #ffa726);
        }

        .category-card[data-category="deportes"]:hover .btn-category-enter {
            border-color: #ffb74d;
        }

        .category-card[data-category="belleza"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #f48fb1, #ec407a);
        }

        .category-card[data-category="belleza"]:hover .btn-category-enter {
            border-color: #f48fb1;
        }

        .category-card[data-category="libros"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #a1887f, #8d6e63);
        }

        .category-card[data-category="libros"]:hover .btn-category-enter {
            border-color: #a1887f;
        }

        .category-card[data-category="juguetes"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #ffcc02, #ffc107);
        }

        .category-card[data-category="juguetes"]:hover .btn-category-enter {
            border-color: #ffcc02;
        }

        .category-card[data-category="automotriz"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #90a4ae, #78909c);
        }

        .category-card[data-category="automotriz"]:hover .btn-category-enter {
            border-color: #90a4ae;
        }

        .category-card[data-category="jardin"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #8bc34a, #689f38);
        }

        .category-card[data-category="jardin"]:hover .btn-category-enter {
            border-color: #8bc34a;
        }

        .category-card[data-category="mascotas"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #ff7043, #f4511e);
        }

        .category-card[data-category="mascotas"]:hover .btn-category-enter {
            border-color: #ff7043;
        }

        .category-card[data-category="arte"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #ab47bc, #8e24aa);
        }

        .category-card[data-category="arte"]:hover .btn-category-enter {
            border-color: #ab47bc;
        }

        .category-card[data-category="musica"]:hover .btn-category-enter::before {
            background: linear-gradient(135deg, #7e57c2, #5e35b1);
        }

        .category-card[data-category="musica"]:hover .btn-category-enter {
            border-color: #7e57c2;
        }

        /* Efecto de ondas al hacer click */
        .btn-category-enter {
            position: relative;
        }

        .btn-category-enter::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
            z-index: 2;
        }

        .btn-category-enter:active::after {
            width: 200px;
            height: 200px;
        }

        /* Mejora en las categorías para mejor integración */
        .categories-section .category-card {
            height: 300px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .categories-section .category-card:hover {
            border-color: rgba(102, 126, 234, 0.2);
            transform: translateY(-8px) scale(1.02);
        }

        /* Animación de entrada mejorada */
        .carousel-item.active .category-action {
            animation: slideInFromBottom 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes slideInFromBottom {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.8);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Responsive mejorado */
        @media (max-width: 768px) {
            .btn-category-enter {
                padding: 0.7rem 1.5rem;
                font-size: 0.85rem;
                min-width: 120px;
            }

            .categories-section .category-card {
                height: 280px;
            }
        }

        @media (max-width: 576px) {
            .btn-category-enter {
                padding: 0.6rem 1.2rem;
                font-size: 0.8rem;
                min-width: 100px;
                gap: 0.5rem;
            }

            .categories-section .category-card {
                height: 260px;
            }
        }

        /* Efecto de brillo sutil */
        .btn-category-enter:hover {
            box-shadow:
                0 8px 25px rgba(102, 126, 234, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Estado focus para accesibilidad */
        .btn-category-enter:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        .animated-pulse {
            animation: pulse 1.2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0.0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.0);
            }
        }
    </style>
</head>

<body>
    <!-- Navbar Moderno -->
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top">
        <div class="container">
            <a class="logo-brand" href="#home">Mextium</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/vendedores/catalogo_vendedores.php">Vendedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/Support Center/contactos.php">Contacto</a>
                    </li>
                    <?php if (isset($usuario['rol_id']) && $usuario['rol_id'] == 3): ?>
                        <li class="nav-item">
                            <a href="administrador/dashboard.php" class="btn btn-warning d-flex align-items-center fw-bold" style="background: linear-gradient(90deg, #2d44aa 0%, #13469d 100%); color: #fff; border: none; font-size: 1.1rem; letter-spacing: 1px; margin-left: 10px; gap: 6px;">
                                <i class="fas fa-crown"></i> ADMINISTRACIÓN DE MEXTIUM
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <!-- Carrito de compras -->
                    <a href="../views/productos/carrito.php" class="btn position-relative btn-outline-primary me-2" id="cartBtn" style="border-radius:50%;width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </a>
                    <?php if (isset($_SESSION['user_id']) && $usuario): ?>
                        <!-- Usuario logueado -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i>
                                <span class="d-none d-md-inline">
                                    <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                                </span>
                                <span class="d-md-none">Mi Cuenta</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <h6 class="dropdown-header">
                                        <i class="fas fa-user me-2"></i>
                                        <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                                    </h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="../views/usuarios/profile.php">
                                        <i class="fas fa-user-edit me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-item" style="white-space:normal;">
                                        <i class="fas fa-map-marker-alt me-2"></i>Mi Dirección
                                        <div style="font-weight:400;font-size:0.97em;color:#666;margin-left:1.7em;margin-top:2px;">
                                            <?php echo isset($usuario['direccion']) && $usuario['direccion'] ? htmlspecialchars($usuario['direccion']) : 'No registrada'; ?>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="./envios/mis_pedidos.php">
                                        <i class="fas fa-box me-2"></i>Mis Pedidos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-history me-2"></i>Historial de Compras
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-bell me-2"></i>Notificaciones
                                    </a>
                                </li>
                                <!-- Verificación de cédula deshabilitada temporalmente -->
                                <?php if ($usuario['rol_id'] == 2): // Vendedor 
                                ?>
                                    <li>
                                        <a class="dropdown-item" href="../mis_productos.php">
                                            <i class="fas fa-store me-2"></i>Mis Productos
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($usuario['rol_id'] == 1): // Admin 
                                ?>

                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="../views/usuarios/configuracion.php">
                                        <i class="fas fa-cog me-2"></i>Configuración
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="../views/usuarios/logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Usuario no logueado -->
                        <a href="usuarios/inicio_sesion.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                        <a href="usuarios/registro.php" class="btn btn-primary-gradient">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section Ultra Moderno -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-right">
                        <h1 class="hero-title">compra fácil, recibe rápido</h1>
                        <p class="hero-subtitle">Descubre productos únicos, conecta con vendedores increíbles y vive una experiencia de compra revolucionaria.</p>

                        <div class="search-hero">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="¿Qué estás buscando hoy?">
                                <button class="btn" type="button">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="#categories" class="btn btn-primary-gradient btn-lg">
                                <i class="fas fa-rocket"></i> Explorar Ahora
                            </a>
                            <?php if (isset($_SESSION['user_id']) && $usuario && $miTienda): ?>
                                <a href="../views/vendedores/mi_tienda.php" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-store"></i> Entrar a mi tienda
                                </a>
                            <?php else: ?>
                                <a href="../views/vendedores/registro_tienda.php" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-store"></i> Vender Aquí
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


                <!-- Removed duplicate 'Ver más productos' button from hero/banner section -->
            </div>
    </section>



    <!-- Categorías Modernas con Carrusel -->
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Explora por Categorías</h2>
                <p class="lead text-muted">Encuentra exactamente lo que necesitas</p>
            </div>

            <!-- Carrusel de Categorías -->
            <div class="categories-carousel-container" data-aos="fade-up" data-aos-delay="200">
                <div id="categoriesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="8000">
                    <div class="carousel-inner">
                        <!-- Slide 1 -->
                        <div class="carousel-item active">
                            <div class="row g-4">
                                <div class="col-lg-3 col-md-6">
                                    <div class="category-card category-clickable" data-category="moda" onclick="verCategoria('moda')" style="background-image: url('/mextium/public/categorias/ropa.png'); background-size:cover; background-position:center; min-height:220px; color:white; position:relative; cursor:pointer;">
                                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.35);z-index:1;border-radius:20px;"></div>
                                        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
                                            <h4 style="font-size:2rem;font-weight:700;letter-spacing:1px;">Moda</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="category-card category-clickable" data-category="tecnologia" onclick="verCategoria('tecnologia')" style="background-image: url('/mextium/public/categorias/tecnología.png'); background-size:cover; background-position:center; min-height:220px; color:white; position:relative; cursor:pointer;">
                                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.35);z-index:1;border-radius:20px;"></div>
                                        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
                                            <h4 style="font-size:2rem;font-weight:700;letter-spacing:1px;">Tecnología</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="category-card category-clickable" data-category="hogar" onclick="verCategoria('hogar')" style="background-image: url('/mextium/mextium/public/categorias/hogar.jpg'); background-size:cover; background-position:center; min-height:220px; color:white; position:relative; cursor:pointer;">
                                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.35);z-index:1;border-radius:20px;"></div>
                                        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
                                            <h4 style="font-size:2rem;font-weight:700;letter-spacing:1px;">Hogar</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="category-card category-clickable" data-category="deportes" onclick="verCategoria('deportes')" style="background-image: url('/mextium/mextium/public/categorias/deportes.jpg'); background-size:cover; background-position:center; min-height:220px; color:white; position:relative; cursor:pointer;">
                                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.35);z-index:1;border-radius:20px;"></div>
                                        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
                                            <h4 style="font-size:2rem;font-weight:700;letter-spacing:1px;">Deportes</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Controles del carrusel -->
                <!--
                    <button class="carousel-control-prev" type="button" data-bs-target="#categoriesCarousel" data-bs-slide="prev">
                        <div class="carousel-control-icon">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#categoriesCarousel" data-bs-slide="next">
                        <div class="carousel-control-icon">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                    -->
            </div>

            <!-- Navegación rápida -->
            <div class="categories-navigation mt-4 text-center">
                <div class="btn-group" role="group" aria-label="Navegación de categorías">
                    <button type="button" class="btn btn-outline-primary" onclick="goToSlide(0)">
                        <i class="fas fa-home me-2"></i>Más populares
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="goToSlide(1)">
                        <i class="fas fa-star me-2"></i>Novedades
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="goToSlide(2)">
                        <i class="fas fa-heart me-2"></i>Recomendados
                    </button>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Productos Destacados -->
    <section id="products" class="products-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Productos Destacados</h2>
                <p class="lead text-muted">Los más populares de nuestra comunidad</p>
            </div>
            <div class="row g-4">
                <?php if (empty($productos)): ?>
                    <div class="col-12 text-center text-muted">No hay productos destacados en este momento.</div>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-lg-4 col-md-6" data-aos="flip-left">
                            <div class="product-card">
                                <div class="product-image" style="width: 100%; height: 220px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 16px; overflow: hidden; border: 1px solid #e0e0e0;">
                                    <?php if (!empty($producto['imagen']) && file_exists(__DIR__ . '/../' . ltrim($producto['imagen'], '/'))): ?>
                                        <img src="<?= '../' . ltrim($producto['imagen'], '/') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <img src="../public/no-image.png" alt="Sin imagen" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    <?php endif; ?>
                                    <div class="product-badge">
                                        <?= $producto['destacado'] ? 'Destacado' : 'Nuevo' ?>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h5><?= htmlspecialchars($producto['nombre']) ?></h5>
                                    <p class="text-muted mb-1">Categoría: <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?></p>
                                    <p class="mb-1"><span class="fw-bold">Precio:</span> <span class="product-price">
                                            <?php
                                            $precio = $producto['precio'];
                                            if ($precio === null || $precio === '' || strtolower($precio) === 'null') {
                                                echo 'No disponible';
                                            } else {
                                                $precioNum = floatval($precio);
                                                echo '$' . number_format($precioNum, 2);
                                            }
                                            ?>
                                        </span></p>
                                    <p class="mb-1"><span class="fw-bold">Stock:</span> <?= htmlspecialchars($producto['stock']) ?></p>
                                    <?php
                                    $nombreTienda = '';
                                    if (!empty($producto['vendedor_id'])) {
                                        $tiendaTmp = (new TiendaModel())->obtenerTiendaPorUsuarioId($producto['vendedor_id']);
                                        if ($tiendaTmp && !empty($tiendaTmp['nombre_tienda'])) {
                                            $nombreTienda = $tiendaTmp['nombre_tienda'];
                                        }
                                    }
                                    ?>
                                    <?php if ($nombreTienda): ?>
                                        <p class="mb-1"><span class="fw-bold">Vendedor:</span> <?= htmlspecialchars($nombreTienda) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($producto['fecha_creacion'])): ?>
                                        <p class="mb-1"><span class="fw-bold">Publicado:</span> <?= date('d/m/Y', strtotime($producto['fecha_creacion'])) ?></p>
                                    <?php endif; ?>

                                    <!-- Detalles técnicos y visuales -->
                                    <div class="mb-1 text-secondary" style="font-size:0.97em;">
                                        <?php if (!empty($producto['marca'])): ?>
                                            <span class="me-2"><i class="fas fa-industry"></i> <b>Marca:</b> <?= htmlspecialchars($producto['marca']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($producto['modelo'])): ?>
                                            <span class="me-2"><i class="fas fa-barcode"></i> <b>Modelo:</b> <?= htmlspecialchars($producto['modelo']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($producto['color'])): ?>
                                            <span class="me-2"><i class="fas fa-palette"></i> <b>Color:</b> <?= htmlspecialchars($producto['color']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($producto['material'])): ?>
                                            <span class="me-2"><i class="fas fa-cube"></i> <b>Material:</b> <?= htmlspecialchars($producto['material']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-1 text-secondary" style="font-size:0.97em;">
                                        <?php if (!empty($producto['peso'])): ?>
                                            <span class="me-2"><i class="fas fa-weight-hanging"></i> <b>Peso:</b> <?= htmlspecialchars($producto['peso']) . ' ' . htmlspecialchars($producto['peso_unidad']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($producto['largo']) || !empty($producto['ancho']) || !empty($producto['alto'])): ?>
                                            <span class="me-2"><i class="fas fa-ruler-combined"></i> <b>Dimensiones:</b> <?= htmlspecialchars($producto['largo']) ?>x<?= htmlspecialchars($producto['ancho']) ?>x<?= htmlspecialchars($producto['alto']) ?> <?= htmlspecialchars($producto['dimensiones_unidad']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($producto['volumen'])): ?>
                                            <span class="me-2"><i class="fas fa-cube"></i> <b>Volumen:</b> <?= htmlspecialchars($producto['volumen']) . ' ' . htmlspecialchars($producto['volumen_unidad']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    // Mostrar especificaciones técnicas si existen y son JSON válido
                                    if (!empty($producto['especificaciones_tecnicas'])) {
                                        $espec = json_decode($producto['especificaciones_tecnicas'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($espec)) {
                                            echo '<div class="mb-1 text-secondary" style="font-size:0.97em;"><b>Especificaciones técnicas:</b><ul style="margin-bottom:0;">';
                                            foreach ($espec as $key => $val) {
                                                echo '<li><b>' . htmlspecialchars($key) . ':</b> ' . htmlspecialchars(is_array($val) ? json_encode($val) : $val) . '</li>';
                                            }
                                            echo '</ul></div>';
                                        }
                                    }
                                    ?>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <button class="btn-comprar btn btn-primary-gradient" data-producto-id="<?= htmlspecialchars($producto['id']) ?>">
                                            <i class="fas fa-cart-plus"></i> Comprar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="#" class="btn btn-primary-gradient btn-lg px-5"><i class="fas fa-plus"></i> Ver más productos</a>
            </div>
        </div>
    </section>

    <!-- Tiendas Destacadas -->
    <section id="featured-stores" class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold">Tiendas Destacadas</h2>
                <p class="lead text-muted">Conoce a nuestros mejores vendedores</p>
            </div>
            <style>
                /* Tienda destacada: bordes más redondeados y sin badge */
                .featured-store-card {
                    min-height: 370px;
                    max-width: 340px;
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: flex-start;
                    background: #fff;
                    border-radius: 48px;
                    box-shadow: 0 8px 32px rgba(80, 120, 180, 0.13), 0 2px 8px rgba(80, 80, 80, 0.07);
                    margin-left: auto;
                    margin-right: auto;
                    margin-bottom: 2.2rem;
                    border: 1.5px solid #e3eaff;
                    overflow: hidden;
                    position: relative;
                    transition: all 0.35s cubic-bezier(.17, .67, .83, .67);
                }

                .featured-store-card:hover {
                    transform: translateY(-10px) scale(1.03) rotate(-1deg);
                    box-shadow: 0 24px 60px rgba(45, 68, 170, 0.18), 0 2px 12px rgba(106, 130, 251, 0.13);
                    border-color: #6a82fb;
                }

                .featured-store-card .product-image {
                    width: 100%;
                    height: 180px;
                    background: linear-gradient(135deg, #e3eaff 0%, #fff 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                    border-top-left-radius: 48px;
                    border-top-right-radius: 48px;
                    overflow: hidden;
                }

                .featured-store-card .product-info {
                    flex: 1 1 auto;
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: flex-start;
                    padding: 1.1rem 1.2rem 1.2rem 1.2rem;
                }

                .featured-store-card .store-title {
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

                .featured-store-card .store-desc {
                    font-size: 0.98rem;
                    color: #6a6a7a;
                    margin-bottom: 0.7rem;
                    min-height: 2.1em;
                    max-height: 2.1em;
                    line-height: 1.05em;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    text-align: center;
                }
            </style>
            <div class="row g-4 justify-content-center">
                <?php if (empty($tiendas)): ?>
                    <div class="col-12 text-center text-muted">No hay tiendas destacadas en este momento.</div>
                <?php else: ?>
                    <?php foreach ($tiendas as $tienda): ?>
                        <div class="col-lg-4 col-md-6" data-aos="flip-left">
                            <div class="featured-store-card">
                                <div class="product-image">
                                    <?php if (!empty($tienda['imagen']) && file_exists(__DIR__ . '/../' . ltrim($tienda['imagen'], '/'))): ?>
                                        <img src="<?= '../' . ltrim($tienda['imagen'], '/') ?>" alt="<?= htmlspecialchars($tienda['tienda_nombre'] ?? $tienda['nombre'] ?? 'Tienda') ?>" style="width:100%;height:100%;object-fit:cover;">
                                    <?php else: ?>
                                        <img src="../public/no-image.png" alt="Sin imagen" style="width:100%;height:100%;object-fit:cover;">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info text-center">
                                    <div class="store-title"><?= htmlspecialchars($tienda['nombre_tienda'] ?? $tienda['tienda_nombre'] ?? $tienda['nombre'] ?? 'Tienda') ?></div>
                                    <?php if (!empty($tienda['verificado'])): ?>
                                        <div class="badge badge-verified mb-2" style="background:linear-gradient(90deg,#00c853,#b2ff59);color:#fff;font-weight:700;border-radius:12px;padding:0.25em 1em;font-size:0.98rem;display:inline-block;">Tienda verificada <i class="fas fa-check-circle ms-1"></i></div>
                                    <?php else: ?>
                                        <div class="badge badge-unverified mb-2" style="background:linear-gradient(90deg,#bdbdbd,#e57373);color:#fff;font-weight:700;border-radius:12px;padding:0.25em 1em;font-size:0.98rem;display:inline-block;">Tienda no verificada <i class="fas fa-times-circle ms-1"></i></div>
                                    <?php endif; ?>
                                    <div class="text-muted mb-2" style="font-size:0.98rem;">
                                        <?= htmlspecialchars($tienda['ciudad'] ?? $tienda['categoria_principal'] ?? 'General') ?>
                                    </div>
                                    <?php if (!empty($tienda['descripcion_tienda'])): ?>
                                        <div class="store-desc"> <?= htmlspecialchars($tienda['descripcion_tienda']) ?> </div>
                                    <?php else: ?>
                                        <div class="store-desc">&nbsp;</div>
                                    <?php endif; ?>
                                    <a href="../views/vendedores/tienda_detalle.php?id=<?= urlencode($tienda['vendedor_id'] ?? $tienda['id']) ?>" class="btn btn-outline-primary rounded-pill px-4"><i class="fas fa-store"></i> Ver Tienda</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="#" class="btn btn-outline-primary btn-lg px-5"><i class="fas fa-store"></i> Ver más tiendas</a>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5" style="background: var(--gradient-primary);">
        <div class="container text-center" data-aos="zoom-in">
            <h2 class="display-5 fw-bold text-white mb-3">¿Listo para empezar?</h2>
            <p class="lead text-white mb-4">Únete a miles de usuarios que ya disfrutan de Mextium</p>
            <a href="../views/usuarios/registro.php" class="btn btn-light btn-lg px-5">
                <i class="fas fa-rocket"></i> Comenzar Ahora
            </a>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="row">
                <!-- Tarjeta de visitas -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">
                            <?php echo $visitas; ?>
                        </div>
                        <p>Visualizacion de la pagina</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Moderno -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">Mextium</div>
                    <p class="text-muted">El marketplace que conecta personas, productos y experiencias únicas.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Marketplace</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#">Categorías</a></li>
                        <li><a href="#">Productos</a></li>
                        <li><a href="#">Vendedores</a></li>
                        <li><a href="#">Ofertas</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Soporte</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="../views/Support Center/contactos.php">Centro de Ayuda</a></li>
                        <li><a href="../views/Support Center/contactos.php">Contacto</a></li>
                        <li><a href="../views/Support Center/faq.php">FAQ</a></li>
                        <li><a href="../views/Support Center/terminos.php" target="_blank">Términos y Condiciones</a></li>
                        <li><a href="../views/Support Center/condiciones.php" target="_blank">Condiciones Generales de Uso</a></li>
                        <li><a href="../views/Support Center/politicas_tratamiento_datos.php" target="_blank">Política de Tratamiento de Datos</a></li>
                        <li><a href="../views/Support Center/politica_cookies.php" target="_blank">Política de Cookies</a></li>
                        <li><a href="../views/Support Center/politica_devoluciones.php" target="_blank">Política de Devoluciones y Garantías</a></li>
                        <li><a href="../views/Support Center/aviso_privacidad.php" target="_blank">Aviso de Privacidad</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Empresa</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#">Acerca de</a></li>
                        <li><a href="#">Carreras</a></li>
                        <li><a href="#">Prensa</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Legal</h5>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#">Términos</a></li>
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Cookies</a></li>
                        <li><a href="#">Licencias</a></li>
                    </ul>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

            <div class="text-center">
                <p class="text-muted">&copy; 2025 Mextium. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
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
                        .then(function(data) {
                            mostrarNotificacion(data.message, data.success);
                        })
                        .catch(function() {
                            mostrarNotificacion('Error al agregar al carrito', false);
                        });
                });
            });
        });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inicializar AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-modern');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Counter animation
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString() +
                    (element.textContent.includes('K') ? 'K+' :
                        element.textContent.includes('%') ? '%' : '+');
            }, 20);
        }

        // Trigger counter animation when stats section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stats-number');
                    counters.forEach(counter => {
                        const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                        animateCounter(counter, target);
                    });
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(document.querySelector('.stats-section'));

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Función para ir a un slide específico del carrusel
        function goToSlide(slideIndex) {
            const carousel = new bootstrap.Carousel(document.getElementById('categoriesCarousel'));
            carousel.to(slideIndex);
        }

        // Función para ver categoría
        function verCategoria(categoria) {
            // Redirigir a la página de la categoría (ruta correcta)
            window.location.href = `productos/categoria.php?categoria=${encodeURIComponent(categoria)}`;
        }

        // Función para mostrar mensaje elegante (temporal)
        function mostrarMensajeCategoria(categoria) {
            // Crear modal elegante
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.style.zIndex = '9999';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                        <div class="modal-body text-center p-5">
                            <div class="mb-4">
                                <div style="width: 80px; height: 80px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="fas fa-rocket text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h3 class="mb-3" style="color: var(--dark-color);">¡Próximamente!</h3>
                            <p class="text-muted mb-4">La sección de <strong>${categoria}</strong> estará disponible muy pronto con cientos de productos increíbles.</p>
                            <button type="button" class="btn btn-primary-gradient px-4" data-bs-dismiss="modal">
                                <i class="fas fa-check me-2"></i>Entendido
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();

            // Eliminar modal del DOM cuando se cierre
            modal.addEventListener('hidden.bs.modal', () => {
                document.body.removeChild(modal);
            });
        }

        // Efecto de entrada para botones cuando se cambia de slide
        document.getElementById('categoriesCarousel').addEventListener('slide.bs.carousel', function() {
            // Ocultar botones del slide actual
            const currentSlide = this.querySelector('.carousel-item.active');
            const buttons = currentSlide.querySelectorAll('.category-action');
            buttons.forEach(button => {
                button.style.opacity = '0';
                button.style.transform = 'translateY(20px)';
            });
        });

        document.getElementById('categoriesCarousel').addEventListener('slid.bs.carousel', function() {
            // Mostrar botones del nuevo slide con delay
            const currentSlide = this.querySelector('.carousel-item.active');
            const buttons = currentSlide.querySelectorAll('.category-action');
            buttons.forEach((button, index) => {
                setTimeout(() => {
                    button.style.opacity = '1';
                    button.style.transform = 'translateY(0)';
                }, (index + 1) * 100);
            });
        });

        // Agregar efecto de pulso aleatorio a algunos botones
        setInterval(() => {
            const buttons = document.querySelectorAll('.btn-category-enter');
            const randomButton = buttons[Math.floor(Math.random() * buttons.length)];
            if (randomButton && !randomButton.matches(':hover')) {
                randomButton.classList.add('pulse');
                setTimeout(() => {
                    randomButton.classList.remove('pulse');
                }, 2000);
            }
        }, 5000);
        // Carrito de compras demo (localStorage)
        function getCartCount() {
            let cart = JSON.parse(localStorage.getItem('mextium_cart') || '[]');
            return cart.length;
        }

        function updateCartCount() {
            document.getElementById('cartCount').textContent = getCartCount();
        }
        updateCartCount();
        // Demo: abrir modal carrito
        document.getElementById('cartBtn').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarCarritoModal();
        });

        function mostrarCarritoModal() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.style.zIndex = '9999';
            let cart = JSON.parse(localStorage.getItem('mextium_cart') || '[]');
            let itemsHtml = cart.length ? cart.map(item => `<li class='list-group-item d-flex justify-content-between align-items-center'>${item}<span class='badge bg-primary rounded-pill'>1</span></li>`).join('') : '<li class="list-group-item text-center text-muted">El carrito está vacío</li>';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-shopping-cart me-2"></i>Carrito de Compras</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group mb-3">${itemsHtml}</ul>
                            <button type="button" class="btn btn-primary-gradient w-100" data-bs-dismiss="modal">Ir a Pagar</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            modal.addEventListener('hidden.bs.modal', () => {
                document.body.removeChild(modal);
            });
        }
    </script>
</body>

</html>