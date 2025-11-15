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

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $contraseña_actual = $_POST['contraseña_actual'] ?? '';
        $nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
        $confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

        // Validaciones
        if (empty($contraseña_actual) || empty($nueva_contraseña) || empty($confirmar_contraseña)) {
            $mensaje = 'Todos los campos son obligatorios';
            $tipo_mensaje = 'danger';
        } elseif (strlen($nueva_contraseña) < 6) {
            $mensaje = 'La nueva contraseña debe tener al menos 6 caracteres';
            $tipo_mensaje = 'danger';
        } elseif ($nueva_contraseña !== $confirmar_contraseña) {
            $mensaje = 'Las nuevas contraseñas no coinciden';
            $tipo_mensaje = 'danger';
        } else {
            // Usar el método que ya existe y funciona
            $resultado = $model->cambiarPassword($_SESSION['user_id'], $contraseña_actual, $nueva_contraseña);
            
            if ($resultado['success']) {
                $mensaje = $resultado['message'];
                $tipo_mensaje = 'success';
            } else {
                $mensaje = $resultado['message'];
                $tipo_mensaje = 'danger';
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error del sistema. Intenta más tarde.';
        $tipo_mensaje = 'danger';
        error_log("Error en cambio de contraseña: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Mextium</title>
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
            --gradient-danger: linear-gradient(135deg, #dc3545, #c82333);
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Partículas de fondo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-particles 8s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 6px; height: 6px; top: 20%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 8px; height: 8px; top: 60%; left: 80%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 4px; height: 4px; top: 80%; left: 20%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 10px; height: 10px; top: 40%; left: 90%; animation-delay: 6s; }

        @keyframes float-particles {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
        }

        /* Container principal */
        .password-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.8s ease-out;
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

        /* Header */
        .password-header {
            background: var(--gradient-primary);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .password-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .security-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 2;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
        }

        .security-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .header-title {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            position: relative;
            z-index: 2;
        }

        /* Contenido */
        .password-content {
            padding: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid rgba(95, 170, 255, 0.15);
            border-radius: 15px;
            padding: 1.2rem;
            padding-right: 3rem;
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

        /* Botón de mostrar/ocultar contraseña */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            z-index: 3;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--secondary-color);
            transform: translateY(-50%) scale(1.1);
        }

        /* Indicador de fortaleza */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: var(--danger-color); width: 25%; }
        .strength-fair { background: var(--warning-color); width: 50%; }
        .strength-good { background: var(--primary-color); width: 75%; }
        .strength-strong { background: var(--success-color); width: 100%; }

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

        .btn-secondary-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.875rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary-outline:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        /* Alertas */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: var(--gradient-success);
            color: white;
        }

        .alert-danger {
            background: var(--gradient-danger);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .password-header {
                padding: 2rem 1.5rem;
            }
            
            .password-content {
                padding: 2rem 1.5rem;
            }
            
            .security-icon {
                width: 80px;
                height: 80px;
            }
            
            .security-icon i {
                font-size: 2rem;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
        }

        /* Efectos adicionales */
        .form-floating.focused .form-control {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
        }

        .requirement {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
            padding-left: 1rem;
            transition: all 0.3s ease;
        }

        .requirement.met {
            color: var(--success-color);
        }

        .requirement i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Partículas de fondo -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="password-container">
        <!-- Header -->
        <div class="password-header">
            <div class="security-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="header-title">Cambiar Contraseña</h1>
            <p class="header-subtitle">Mantén tu cuenta segura con una contraseña fuerte</p>
        </div>

        <!-- Contenido -->
        <div class="password-content">
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="passwordForm">
                <!-- Contraseña actual -->
                <div class="form-floating">
                    <input type="password" class="form-control" id="contraseña_actual" 
                           name="contraseña_actual" placeholder="Contraseña actual" required>
                    <label for="contraseña_actual">
                        <i class="fas fa-lock me-2"></i>Contraseña Actual
                    </label>
                    <button type="button" class="password-toggle" onclick="togglePassword('contraseña_actual')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <!-- Nueva contraseña -->
                <div class="form-floating">
                    <input type="password" class="form-control" id="nueva_contraseña" 
                           name="nueva_contraseña" placeholder="Nueva contraseña" required minlength="6">
                    <label for="nueva_contraseña">
                        <i class="fas fa-key me-2"></i>Nueva Contraseña
                    </label>
                    <button type="button" class="password-toggle" onclick="togglePassword('nueva_contraseña')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="password-strength">
                        <div class="password-strength-fill" id="strengthBar"></div>
                    </div>
                    <div id="strengthText" class="requirement">
                        <i class="fas fa-info-circle"></i>Mínimo 6 caracteres
                    </div>
                </div>

                <!-- Confirmar contraseña -->
                <div class="form-floating">
                    <input type="password" class="form-control" id="confirmar_contraseña" 
                           name="confirmar_contraseña" placeholder="Confirmar contraseña" required minlength="6">
                    <label for="confirmar_contraseña">
                        <i class="fas fa-check-double me-2"></i>Confirmar Nueva Contraseña
                    </label>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmar_contraseña')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div id="matchText" class="requirement">
                        <i class="fas fa-info-circle"></i>Las contraseñas deben coincidir
                    </div>
                </div>

                <!-- Requisitos de seguridad -->
                <div class="mb-3">
                    <small class="text-muted">
                        <strong>Requisitos de seguridad:</strong>
                    </small>
                    <div class="requirement" id="req-length">
                        <i class="fas fa-times-circle"></i>Al menos 6 caracteres
                    </div>
                    <div class="requirement" id="req-upper">
                        <i class="fas fa-times-circle"></i>Al menos una mayúscula
                    </div>
                    <div class="requirement" id="req-lower">
                        <i class="fas fa-times-circle"></i>Al menos una minúscula
                    </div>
                    <div class="requirement" id="req-number">
                        <i class="fas fa-times-circle"></i>Al menos un número
                    </div>
                </div>

                <!-- Botones -->
                <button type="submit" class="btn btn-primary-gradient">
                    <i class="fas fa-save me-2"></i>Actualizar Contraseña
                </button>
                
                <a href="profile.php" class="btn-secondary-outline">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Perfil
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling.nextElementSibling;
            const icon = toggle.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validación de fortaleza de contraseña
        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Longitud
            if (password.length >= 6) strength += 1;
            if (password.length >= 8) strength += 1;
            
            // Mayúsculas
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Minúsculas
            if (/[a-z]/.test(password)) strength += 1;
            
            // Números
            if (/[0-9]/.test(password)) strength += 1;
            
            // Caracteres especiales
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            return Math.min(strength, 4);
        }

        // Actualizar indicador de fortaleza
        document.getElementById('nueva_contraseña').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            const strength = checkPasswordStrength(password);
            
            // Limpiar clases
            strengthBar.className = 'password-strength-fill';
            
            if (password.length === 0) {
                strengthText.innerHTML = '<i class="fas fa-info-circle"></i>Mínimo 6 caracteres';
                return;
            }
            
            switch (strength) {
                case 0:
                case 1:
                    strengthBar.classList.add('strength-weak');
                    strengthText.innerHTML = '<i class="fas fa-exclamation-triangle"></i>Muy débil';
                    break;
                case 2:
                    strengthBar.classList.add('strength-fair');
                    strengthText.innerHTML = '<i class="fas fa-minus-circle"></i>Débil';
                    break;
                case 3:
                    strengthBar.classList.add('strength-good');
                    strengthText.innerHTML = '<i class="fas fa-check-circle"></i>Buena';
                    strengthText.classList.add('met');
                    break;
                case 4:
                    strengthBar.classList.add('strength-strong');
                    strengthText.innerHTML = '<i class="fas fa-shield-alt"></i>Muy fuerte';
                    strengthText.classList.add('met');
                    break;
            }

            // Actualizar requisitos
            updateRequirements(password);
        });

        // Actualizar requisitos de seguridad
        function updateRequirements(password) {
            const requirements = {
                'req-length': password.length >= 6,
                'req-upper': /[A-Z]/.test(password),
                'req-lower': /[a-z]/.test(password),
                'req-number': /[0-9]/.test(password)
            };

            Object.keys(requirements).forEach(req => {
                const element = document.getElementById(req);
                const icon = element.querySelector('i');
                
                if (requirements[req]) {
                    element.classList.add('met');
                    icon.classList.remove('fa-times-circle');
                    icon.classList.add('fa-check-circle');
                } else {
                    element.classList.remove('met');
                    icon.classList.remove('fa-check-circle');
                    icon.classList.add('fa-times-circle');
                }
            });
        }

        // Validar coincidencia de contraseñas
        document.getElementById('confirmar_contraseña').addEventListener('input', function() {
            const password = document.getElementById('nueva_contraseña').value;
            const confirm = this.value;
            const matchText = document.getElementById('matchText');
            
            if (confirm.length === 0) {
                matchText.innerHTML = '<i class="fas fa-info-circle"></i>Las contraseñas deben coincidir';
                matchText.classList.remove('met');
                return;
            }
            
            if (password === confirm) {
                matchText.innerHTML = '<i class="fas fa-check-circle"></i>Las contraseñas coinciden';
                matchText.classList.add('met');
            } else {
                matchText.innerHTML = '<i class="fas fa-times-circle"></i>Las contraseñas no coinciden';
                matchText.classList.remove('met');
            }
        });

        // Efectos de focus
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Validación del formulario
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const nuevaPassword = document.getElementById('nueva_contraseña').value;
            const confirmarPassword = document.getElementById('confirmar_contraseña').value;
            
            if (nuevaPassword !== confirmarPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (nuevaPassword.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
        });
    </script>
</body>
</html>