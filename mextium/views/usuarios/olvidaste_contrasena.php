<?php
session_start();

// Incluir modelos
require_once __DIR__ . '/../../model/usuario_model.php';
require_once __DIR__ . '/../../model/email_model.php';

$mensaje = '';
$tipo_mensaje = 'danger';

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $mensaje = 'Por favor, ingresa tu correo electrónico';
            $tipo_mensaje = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = 'Por favor, ingresa un correo electrónico válido';
            $tipo_mensaje = 'danger';
        } else {
            $model = new UsuarioModel();
            $emailModel = new EmailModel();

            // Verificar si el email existe
            $usuario = $model->obtenerUsuarioPorEmail($email);

            if ($usuario) {
                // Generar token de recuperación
                $token = bin2hex(random_bytes(32));
                $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Intenta guardar el token
                $tokenGuardado = $model->crearTokenRecuperacion($email, $token, $expiracion);

                if ($tokenGuardado) {
                    // Intenta enviar el email
                    $envio = $emailModel->enviarEmailRecuperacion($email, $token, $usuario['nombre'] ?? '');

                    if ($envio['success']) {
                        $mensaje = 'Te hemos enviado un correo con instrucciones para restablecer tu contraseña.';
                        $tipo_mensaje = 'success';
                    } else {
                        $mensaje = 'No se pudo enviar el correo: ' . $envio['message'];
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = 'No se pudo guardar el token de recuperación.';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = 'No existe una cuenta con ese correo electrónico.';
                $tipo_mensaje = 'danger';
            }
        }
    } catch (Exception $e) {
        $mensaje = 'Error del sistema: ' . $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5FAAFF;
            --secondary-color: #4A90E2;
            --dark-color: #2C3E50;
            --light-color: #F8F9FA;
            --gradient-primary: linear-gradient(135deg, #5FAAFF 0%, #4A90E2 100%);
            --gradient-secondary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --shadow-card: 0 15px 35px rgba(95, 170, 255, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #ffffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="300" cy="300" r="200" fill="url(%23a)"/><circle cx="700" cy="200" r="150" fill="url(%23a)"/><circle cx="500" cy="800" r="250" fill="url(%23a)"/></svg>');
            animation: float 25s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-15px) rotate(90deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
            75% { transform: translateY(-20px) rotate(270deg); }
        }
        
        .recovery-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-card);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }
        
        .recovery-container::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -30%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(95, 170, 255, 0.1), transparent);
            border-radius: 50%;
            animation: pulse-bg 4s ease-in-out infinite;
        }
        
        .recovery-container::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -30%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(103, 126, 234, 0.1), transparent);
            border-radius: 50%;
            animation: pulse-bg 4s ease-in-out infinite reverse;
        }
        
        @keyframes pulse-bg {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.6; }
        }
        
        .logo-section {
            text-align: center;
            position: relative;
            z-index: 3;
        }
        
        .recovery-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: bounce-icon 2s ease-in-out infinite;
        }
        
        .recovery-icon i {
            font-size: 2rem;
            color: white;
        }
        
        @keyframes bounce-icon {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .logo-brand {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .recovery-title {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .recovery-subtitle {
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 3;
        }
        
        .form-control {
            border: 2px solid rgba(95, 170, 255, 0.2);
            border-radius: 15px;
            padding: 1.2rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
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
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-primary-gradient {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 1.2rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            z-index: 3;
            overflow: hidden;
        }
        
        .btn-primary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        
        .btn-primary-gradient:hover::before {
            left: 100%;
        }
        
        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(95, 170, 255, 0.4);
            background: var(--gradient-secondary);
        }
        
        .btn-secondary-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            position: relative;
            z-index: 3;
        }
        
        .btn-secondary-outline:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(95, 170, 255, 0.3);
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 3;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #51cf66, #40c057);
            color: white;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #ffd43b, #fab005);
            color: #495057;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 2rem;
            position: relative;
            z-index: 3;
        }
        
        .back-to-login a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-to-login a:hover {
            text-decoration: underline;
            transform: translateX(-5px);
        }
        
        .security-info {
            background: rgba(95, 170, 255, 0.1);
            border: 1px solid rgba(95, 170, 255, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            position: relative;
            z-index: 3;
        }
        
        .security-info h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .security-info p {
            color: #666;
            font-size: 0.85rem;
            margin: 0;
            line-height: 1.4;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .recovery-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .logo-brand {
                font-size: 2rem;
            }
            
            .recovery-title {
                font-size: 1.5rem;
            }
            
            .recovery-icon {
                width: 60px;
                height: 60px;
            }
            
            .recovery-icon i {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .recovery-container {
                padding: 1.5rem 1rem;
            }
            
            .form-floating {
                margin-bottom: 1rem;
            }
        }
        
        /* Animación de éxito */
        .success-animation {
            animation: success-bounce 0.6s ease-out;
        }
        
        @keyframes success-bounce {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="recovery-container" data-aos="zoom-in">
        <div class="logo-section">
            <div class="recovery-icon">
                <i class="fas fa-key"></i>
            </div>
            <a href="../mextium.php" class="logo-brand">Mextium</a>
            <h2 class="recovery-title">¿Olvidaste tu contraseña?</h2>
            <p class="recovery-subtitle">No te preocupes, te ayudamos a recuperar el acceso a tu cuenta</p>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> <?php echo $tipo_mensaje === 'success' ? 'success-animation' : ''; ?>" data-aos="fade-in">
                <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?> me-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($tipo_mensaje !== 'success'): ?>
        <form method="POST" action="">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="correo@ejemplo.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
            </div>
            
            <button type="submit" class="btn btn-primary-gradient mb-3">
                <i class="fas fa-paper-plane me-2"></i>Enviar Enlace de Recuperación
            </button>
            
            <a href="inicio_sesion.php" class="btn btn-secondary-outline">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio de Sesión
            </a>
        </form>
        <?php else: ?>
            <div class="text-center">
                <a href="inicio_sesion.php" class="btn btn-primary-gradient">
                    <i class="fas fa-sign-in-alt me-2"></i>Volver al Inicio de Sesión
                </a>
            </div>
        <?php endif; ?>
        
        <div class="back-to-login">
            <a href="../mextium.php">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
        </div>
        
        <div class="security-info">
            <h6><i class="fas fa-shield-alt me-2"></i>Información de Seguridad</h6>
            <p>El enlace de recuperación expirará en 1 hora por tu seguridad. Si no recibes el correo, revisa tu carpeta de spam.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Auto-focus en el campo email
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.focus();
            }
        });

        // Validación de email en tiempo real
        document.getElementById('email')?.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Por favor, ingresa un correo electrónico válido');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>