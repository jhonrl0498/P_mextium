<?php

session_start();

require_once __DIR__ . '/../../model/usuario_model.php';

$mensaje = '';
$tipo_mensaje = 'danger';
$token = $_GET['token'] ?? '';
$tokenValido = false;

// Verificar token al cargar la página
if (!empty($token)) {
    $model = new UsuarioModel();
    $validacion = $model->validarTokenRecuperacion($token);
    $tokenValido = $validacion['valido'];
    
    if (!$tokenValido) {
        $mensaje = 'El enlace de recuperación es inválido o ha expirado. Solicita uno nuevo.';
        $tipo_mensaje = 'danger';
    }
}

// Procesar formulario de nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido) {
    try {
        $nuevaPassword = $_POST['password'] ?? '';
        $confirmarPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($nuevaPassword) || empty($confirmarPassword)) {
            $mensaje = 'Por favor, completa todos los campos';
            $tipo_mensaje = 'danger';
        } elseif (strlen($nuevaPassword) < 6) {
            $mensaje = 'La contraseña debe tener al menos 6 caracteres';
            $tipo_mensaje = 'danger';
        } elseif ($nuevaPassword !== $confirmarPassword) {
            $mensaje = 'Las contraseñas no coinciden';
            $tipo_mensaje = 'danger';
        } else {
            $resultado = $model->resetearContraseñaConToken($token, $nuevaPassword);
            
            if ($resultado['success']) {
                $mensaje = $resultado['message'];
                $tipo_mensaje = 'success';
                $tokenValido = false; // Desactivar formulario
            } else {
                $mensaje = $resultado['message'];
                $tipo_mensaje = 'danger';
            }
        }
    } catch (Exception $e) {
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
    <title>Restablecer Contraseña - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #667eea 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .reset-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .btn-primary { 
            background: linear-gradient(135deg, #5FAAFF, #4A90E2); 
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(95, 170, 255, 0.3);
        }
        .form-control { 
            border-radius: 15px; 
            padding: 1rem;
            border: 2px solid rgba(95, 170, 255, 0.2);
        }
        .form-control:focus {
            border-color: #5FAAFF;
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
        }
        .alert { 
            border-radius: 15px;
            border: none;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
        }
        .alert-success {
            background: linear-gradient(135deg, #51cf66, #40c057);
            color: white;
        }
        .logo-brand {
            background: linear-gradient(135deg, #5FAAFF, #4A90E2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="text-center mb-4">
            <h1 class="h2 logo-brand">🔐 Mextium</h1>
            <h2>Crear Nueva Contraseña</h2>
            <p class="text-muted">Ingresa tu nueva contraseña segura</p>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($tokenValido && $tipo_mensaje !== 'success'): ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Nueva Contraseña
                </label>
                <input type="password" class="form-control" id="password" name="password" 
                       required minlength="6" placeholder="Mínimo 6 caracteres">
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Confirmar Contraseña
                </label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       required minlength="6" placeholder="Repite tu contraseña">
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-save me-2"></i>Actualizar Contraseña
            </button>
        </form>
        <?php else: ?>
            <div class="text-center">
                <a href="inicio_sesion.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Ir al Inicio de Sesión
                </a>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="inicio_sesion.php" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Volver al Inicio de Sesión
            </a>
        </div>
    </div>

    <script>
        // Validación en tiempo real
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
                this.style.borderColor = '#dc3545';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '#5FAAFF';
            }
        });
        
        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.focus();
            }
        });
    </script>
</body>
</html>