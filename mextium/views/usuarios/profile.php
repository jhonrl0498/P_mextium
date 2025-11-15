<?php

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: inicio_sesion.php");
    exit();
}

// Incluir el modelo de usuario
require_once __DIR__ . '/../../model/usuario_model.php';

$model = new UsuarioModel();
$usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);

if (!$usuario) {
    header("Location: inicio_sesion.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datos = [
            'id' => $_SESSION['user_id'],
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? '')
        ];

        // DEBUG TEMPORAL
        error_log("Datos enviados desde profile.php: " . print_r($datos, true));
        error_log("Session user_id: " . $_SESSION['user_id']);

        // Validaciones básicas
        if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email'])) {
            $mensaje = 'Los campos nombre, apellido y email son obligatorios';
            $tipo_mensaje = 'danger';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $mensaje = 'Por favor, ingresa un email válido';
            $tipo_mensaje = 'danger';
        } else {
            error_log("Llamando a actualizarPerfil...");
            $resultado = $model->actualizarPerfil($datos);
            error_log("Resultado de actualizarPerfil: " . ($resultado ? 'true' : 'false'));
            
            if ($resultado) {
                $mensaje = 'Perfil actualizado exitosamente';
                $tipo_mensaje = 'success';
                // Recargar datos del usuario
                $usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);
            } else {
                $mensaje = 'Error al actualizar el perfil';
                $tipo_mensaje = 'danger';
            }
        }
    } catch (Exception $e) {
        error_log("Excepción en profile.php: " . $e->getMessage());
        $mensaje = 'Error del sistema. Intenta más tarde.';
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2196F3;
            --secondary-color: #1565C0;
            --accent-color: #1976D2;
            --dark-color: #1A237E;
            --light-color: #F8F9FA;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --gradient-primary: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
            --gradient-accent: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            --gradient-success: linear-gradient(135deg, #51cf66, #40c057);
            --shadow-card: 0 15px 35px rgba(33, 150, 243, 0.15);
            --shadow-hover: 0 20px 40px rgba(33, 150, 243, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gradient-accent);
            min-height: 100vh;
            color: var(--dark-color);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(95, 170, 255, 0.1);
            padding: 1rem 0;
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
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        /* Container principal */
        .main-container {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }

        /* Tarjeta de perfil */
        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .profile-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }

        /* Header de la tarjeta */
        .profile-header {
            background: var(--gradient-primary);
            padding: 3rem 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(10px, -10px) rotate(120deg); }
            66% { transform: translate(-10px, 10px) rotate(240deg); }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 2;
        }

        .profile-avatar i {
            font-size: 3rem;
            color: white;
        }

        .profile-name {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .profile-role {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Contenido del perfil */
        .profile-content {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid rgba(95, 170, 255, 0.15);
            border-radius: 15px;
            padding: 1.2rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(248, 249, 250, 0.5);
            height: auto;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
            background: white;
            transform: translateY(-2px);
        }

        .form-label {
            color: var(--dark-color);
            font-weight: 600;
            padding-left: 0.5rem;
        }

        /* Botones */
        .btn-primary-gradient {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(95, 170, 255, 0.4);
            color: white;
        }

        .btn-secondary-gradient {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
            color: white;
        }

        /* Alertas */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .alert-success {
            background: var(--gradient-success);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        /* Estadísticas */
        .stats-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e3eafc;
            color: var(--primary-color);
            box-shadow: 0 4px 24px 0 rgba(33,150,243,0.07);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: var(--secondary-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                padding: 2rem 1rem 3rem;
            }
            
            .profile-content {
                padding: 1.5rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-avatar i {
                font-size: 2.5rem;
            }
            
            .profile-name {
                font-size: 1.5rem;
            }
            
            .btn-primary-gradient {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Animaciones */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
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

        .slide-in {
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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

    <!-- Contenido principal -->
    <div class="main-container">
        <div class="container">
            <!-- Estadísticas rápidas -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stats-card fade-in">
                        <div class="stats-number">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stats-label">Cuenta Activa</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stats-card fade-in" style="animation-delay: 0.1s;">
                        <div class="stats-number">
                            <?php echo ucfirst($usuario['rol_nombre'] ?? 'Usuario'); ?>
                        </div>
                        <div class="stats-label">Tipo de Cuenta</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stats-card fade-in" style="animation-delay: 0.2s;">
                        <div class="stats-number">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stats-label">Cuenta Segura</div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de perfil -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="profile-card slide-in">
                        <!-- Header -->
                        <div class="profile-header text-center">
                            <div class="profile-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h1 class="profile-name">
                                <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                            </h1>
                            <p class="profile-role">
                                <i class="fas fa-crown me-2"></i>
                                <?php echo ucfirst($usuario['rol_nombre'] ?? 'Usuario'); ?>
                            </p>
                        </div>

                        <!-- Contenido -->
                        <div class="profile-content">
                            <?php if (!empty($mensaje)): ?>
                                <div class="alert alert-<?php echo $tipo_mensaje; ?> fade-in">
                                    <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                                    <?php echo htmlspecialchars($mensaje); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
                                                   placeholder="Nombre" required>
                                            <label for="nombre">
                                                <i class="fas fa-user me-2"></i>Nombre
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                                   value="<?php echo htmlspecialchars($usuario['apellido']); ?>" 
                                                   placeholder="Apellido" required>
                                            <label for="apellido">
                                                <i class="fas fa-user me-2"></i>Apellido
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>" 
                                           placeholder="Email" required>
                                    <label for="email">
                                        <i class="fas fa-envelope me-2"></i>Correo Electrónico
                                    </label>
                                </div>

                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" 
                                           placeholder="Teléfono">
                                    <label for="telefono">
                                        <i class="fas fa-phone me-2"></i>Teléfono
                                    </label>
                                </div>

                                <div class="form-floating">
                                    <textarea class="form-control" id="direccion" name="direccion" 
                                              placeholder="Dirección" style="height: 100px"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                                    <label for="direccion">
                                        <i class="fas fa-map-marker-alt me-2"></i>Dirección
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <button type="submit" class="btn btn-primary-gradient">
                                            <i class="fas fa-save me-2"></i>Actualizar Perfil
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="cambiar_contraseña.php" class="btn btn-secondary-gradient w-100">
                                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efectos de focus en los inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Validación en tiempo real
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#5FAAFF';
            }
        });

        // Animaciones al cargar
        window.addEventListener('load', function() {
            document.querySelectorAll('.fade-in, .slide-in').forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>