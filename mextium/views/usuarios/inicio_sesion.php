<?php
session_start();

$mensaje = '';
$tipo_mensaje = 'danger';

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Incluir el modelo directamente
        require_once __DIR__ . '/../../model/usuario_model.php';
        
        $model = new UsuarioModel();
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $mensaje = 'Por favor, completa todos los campos';
        } else {
            $resultado = $model->iniciarSesion($email, $password);
            
            if ($resultado['success']) {
                // Establecer variables de sesión
                $_SESSION['user_id'] = $resultado['user']['id'];
                $_SESSION['user_name'] = $resultado['user']['nombre'];
                $_SESSION['user_email'] = $resultado['user']['email'];
                $_SESSION['user_rol'] = $resultado['user']['rol_id'];
                $_SESSION['rol_id'] = $resultado['user']['rol_id']; // Compatibilidad para admin
                $_SESSION['user_rol_nombre'] = $resultado['user']['rol_nombre'];
                $_SESSION['logged_in'] = true;
                // Login exitoso - redirigir
                $_SESSION['mensaje_exito'] = 'Bienvenido de vuelta, ' . $_SESSION['user_name'];
                header('Location: ../mextium.php');
                exit();
            } else {
                // Error en login
                $mensaje = $resultado['message'];
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
    <title>Iniciar Sesión - Mextium</title>
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
            --gradient-secondary: linear-gradient(135deg, #667eea 0%, #ffffffff 100%);
            --shadow-card: 0 15px 35px rgba(95, 170, 255, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #f9f9f9ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        body::before {
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
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-card);
            padding: 3rem;
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(95, 170, 255, 0.1), transparent);
            border-radius: 50%;
            animation: rotate 8s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 3;
        }
        
        .logo-brand {
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            text-decoration: none;
            display: inline-block;
            animation: pulse-logo 2s ease-in-out infinite;
        }
        
        @keyframes pulse-logo {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .login-title {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: #666;
            font-size: 1rem;
        }
        
        .welcome-back {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
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
            transition: all 0.3s ease;
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
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            z-index: 4;
            transition: all 0.3s ease;
        }
        
        .password-toggle:hover {
            transform: translateY(-50%) scale(1.1);
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            z-index: 3;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            position: relative;
            z-index: 3;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .register-link a:hover {
            text-decoration: underline;
            transform: translateX(5px);
            display: inline-block;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
            position: relative;
            z-index: 3;
        }
        
        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        .social-login {
            position: relative;
            z-index: 3;
        }
        
        .btn-social {
            border: 2px solid #ddd;
            background: white;
            color: #666;
            padding: 0.8rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .btn-social:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .btn-google:hover {
            border-color: #db4437;
            color: #db4437;
        }
        
        .btn-facebook:hover {
            border-color: #3b5998;
            color: #3b5998;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .logo-brand {
                font-size: 2.5rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem 1rem;
            }
            
            .logo-brand {
                font-size: 2rem;
            }
            
            .form-floating {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container" data-aos="zoom-in">
        <div class="logo-section">
            <a href="../mextium.php" class="logo-brand">Mextium</a>
            <h2 class="login-title">¡<span class="welcome-back">Bienvenido</span> de vuelta!</h2>
            <p class="login-subtitle">Accede a tu cuenta para continuar</p>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>" data-aos="shake">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" novalidate>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="correo@ejemplo.com" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
            </div>
            
            <div class="form-floating position-relative">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Tu contraseña" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Contraseña</label>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="forgot-password">
                <a href="olvidaste_contrasena.php"><i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?</a>
            </div>
            
            <button type="submit" class="btn btn-primary-gradient mb-3 mt-3">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
            </button>
            
            <a href="../mextium.php" class="btn btn-secondary-outline">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
            </a>
        </form>
        

        
        <div class="register-link">
            <p>¿No tienes una cuenta? <a href="registro.php"><i class="fas fa-user-plus me-1"></i>Regístrate aquí</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.nextElementSibling.querySelector('i');
            
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

        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Validación de email en tiempo real
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Por favor, ingresa un correo electrónico válido');
            } else {
                this.setCustomValidity('');
            }
        });

        // Efectos de entrada
        setTimeout(() => {
            document.querySelector('.login-container').style.transform = 'scale(1)';
        }, 100);
    </script>
</body>
</html>