<?php
session_start();

// Incluir el modelo directamente (no el controlador desde dentro del controlador)
require_once __DIR__ . '/../model/usuario_model.php';

$mensaje = '';
$tipo_mensaje = 'danger';

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Instanciar el modelo directamente
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
                $_SESSION['user_rol_nombre'] = $resultado['user']['rol_nombre'];
                $_SESSION['rol_id'] = $resultado['user']['rol_id']; // Para compatibilidad con dashboard.php
                $_SESSION['logged_in'] = true;
                
                // Login exitoso - redirigir
                $_SESSION['mensaje_exito'] = 'Bienvenido de vuelta, ' . $_SESSION['user_name'];
                header('Location: ../views/index.php');
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

// Devolver respuesta como JSON si es una petición AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => isset($resultado) && $resultado['success'],
        'message' => $mensaje ?: ($resultado['message'] ?? 'Error desconocido'),
        'redirect' => isset($resultado) && $resultado['success'] ? '../views/index.php' : null
    ]);
    exit();
}
?>