<?php
// Configuración de la base de datos

require_once __DIR__ . '/../model/usuario_model.php';

class UsuarioController {
    private $model;
    
    public function __construct() {
        $this->model = new UsuarioModel();
        
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Procesar registro de usuario
     */
    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respuesta(false, 'Método no permitido');
        }

        // (reCAPTCHA deshabilitado para pruebas)

        // Sanitizar datos (ajustar nombres para que coincidan con el formulario)
        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['celular'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'cedula' => trim($_POST['numero_documento'] ?? ''),
            'rol_id' => intval($_POST['rol_id'] ?? 0)
        ];

        // Validar confirmación de contraseña
        if ($datos['password'] !== $datos['confirm_password']) {
            return $this->respuesta(false, 'Las contraseñas no coinciden');
        }

        // Validar datos
        $errores = $this->model->validarDatosRegistro($datos);
        if (!empty($errores)) {
            return $this->respuesta(false, implode(', ', $errores));
        }

        // Registrar usuario
        $resultado = $this->model->registrarUsuario($datos);

        if ($resultado['success']) {
            // Limpiar sesión anterior y crear nueva para el usuario registrado
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['registro_exitoso'] = true;
            $_SESSION['user_id'] = $resultado['user_id'];
            // Redirigir a la cuenta principal tras registro
            header('Location: /mextium/views/mextium.php');
            exit;
        } else {
            return $this->respuesta(false, $resultado['message']);
        }
    }
    
    /**
     * Procesar inicio de sesión
     */
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respuesta(false, 'Método no permitido');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return $this->respuesta(false, 'Por favor, completa todos los campos');
        }
        
        $resultado = $this->model->iniciarSesion($email, $password);
        
        if ($resultado['success']) {
            // Establecer variables de sesión
            $_SESSION['user_id'] = $resultado['user']['id'];
            $_SESSION['user_name'] = $resultado['user']['nombre'];
            $_SESSION['user_email'] = $resultado['user']['email'];
            $_SESSION['user_rol'] = $resultado['user']['rol_id'];
            $_SESSION['user_rol_nombre'] = $resultado['user']['rol_nombre'];
            $_SESSION['logged_in'] = true;
            
            return $this->respuesta(true, $resultado['message'], [
                'redirect' => '../index.php',
                'user' => $resultado['user']
            ]);
        } else {
            return $this->respuesta(false, $resultado['message']);
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function cerrarSesion() {
        session_destroy();
        header('Location: /');
        exit();
    }
    
    /**
     * Obtener perfil del usuario actual
     */
    public function obtenerPerfil() {
        if (!$this->usuarioLogueado()) {
            return $this->respuesta(false, 'Usuario no autenticado');
        }
        
        $usuario = $this->model->obtenerUsuarioPorId($_SESSION['user_id']);
        
        if ($usuario) {
            unset($usuario['password']); // No devolver contraseña
            return $this->respuesta(true, 'Perfil obtenido', ['user' => $usuario]);
        } else {
            return $this->respuesta(false, 'Usuario no encontrado');
        }
    }
    
    /**
     * Actualizar perfil
     */
    public function actualizarPerfil() {
        if (!$this->usuarioLogueado()) {
            return $this->respuesta(false, 'Usuario no autenticado');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respuesta(false, 'Método no permitido');
        }
        
        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? '')
        ];
        
        $resultado = $this->model->actualizarPerfil($_SESSION['user_id'], $datos);
        
        if ($resultado['success']) {
            // Actualizar datos de sesión
            $_SESSION['user_name'] = $datos['nombre'];
        }
        
        return $resultado;
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword() {
        if (!$this->usuarioLogueado()) {
            return $this->respuesta(false, 'Usuario no autenticado');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respuesta(false, 'Método no permitido');
        }
        
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNuevo = $_POST['password_nuevo'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        if (empty($passwordActual) || empty($passwordNuevo) || empty($passwordConfirm)) {
            return $this->respuesta(false, 'Completa todos los campos');
        }
        
        if ($passwordNuevo !== $passwordConfirm) {
            return $this->respuesta(false, 'Las contraseñas nuevas no coinciden');
        }
        
        if (strlen($passwordNuevo) < 6) {
            return $this->respuesta(false, 'La nueva contraseña debe tener al menos 6 caracteres');
        }
        
        return $this->model->cambiarPassword($_SESSION['user_id'], $passwordActual, $passwordNuevo);
    }
    
    /**
     * Obtener roles disponibles
     */
    public function obtenerRoles() {
        return $this->model->obtenerRoles();
    }
    
    /**
     * Verificar si usuario está logueado
     */
    public function usuarioLogueado() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']);
    }
    
    /**
     * Verificar si usuario es administrador
     */
    public function esAdministrador() {
        return $this->usuarioLogueado() && $_SESSION['user_rol'] == 3;
    }
    
    /**
     * Verificar si usuario es vendedor
     */
    public function esVendedor() {
        return $this->usuarioLogueado() && ($_SESSION['user_rol'] == 2 || $_SESSION['user_rol'] == 3);
    }
    
    /**
     * Middleware para rutas protegidas
     */
    public function requiereLogin() {
        if (!$this->usuarioLogueado()) {
            header('Location: ../usuarios/inicio_sesion.php');
            exit();
        }
    }
    
    /**
     * Middleware para rutas de administrador
     */
    public function requiereAdmin() {
        $this->requiereLogin();
        if (!$this->esAdministrador()) {
            header('Location: ../index.php?error=no_autorizado');
            exit();
        }
    }
    
    /**
     * Respuesta estándar
     */
    private function respuesta($success, $message, $data = []) {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }
    
    /**
     * Procesar solicitudes AJAX
     */
    public function procesarAjax() {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        
        switch ($action) {
            case 'registro':
                $response = $this->procesarRegistro();
                break;
            case 'login':
                $response = $this->procesarLogin();
                break;
            case 'perfil':
                $response = $this->obtenerPerfil();
                break;
            case 'actualizar_perfil':
                $response = $this->actualizarPerfil();
                break;
            case 'cambiar_password':
                $response = $this->cambiarPassword();
                break;
            case 'logout':
                $this->cerrarSesion();
                break;
            default:
                $response = $this->respuesta(false, 'Acción no válida');
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// Procesar solicitudes si se llama directamente
if (basename($_SERVER['PHP_SELF']) === 'usuario_controller.php') {
    $controller = new UsuarioController();
    $controller->procesarAjax();
}
?>