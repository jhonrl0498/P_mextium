<?php
session_start();
require_once __DIR__ . '/../../controller/usuario_controller.php';

$mensaje = '';
$tipo_mensaje = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new UsuarioController();
        $resultado = $controller->procesarRegistro();
        if ($resultado['success']) {
            $_SESSION['mensaje_exito'] = $resultado['message'];
            header('Location: ../index.php');
            exit();
        } else {
            $mensaje = $resultado['message'];
            $tipo_mensaje = 'danger';
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
    <title>Registro - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #667eea 100%); min-height: 100vh; }
        .registro-container { background: #fff; border-radius: 25px; box-shadow: 0 10px 30px rgba(95,170,255,0.2); padding: 3rem; max-width: 600px; margin: 2rem auto; }
        .logo-brand { font-size: 2.5rem; font-weight: 800; color: #4A90E2; text-decoration: none; display: inline-block; }
        .registro-title { color: #2C3E50; font-weight: 600; font-size: 1.5rem; margin-bottom: 0.5rem; }
        .form-floating { margin-bottom: 1.5rem; }
        .form-control, .form-select { border-radius: 15px; }
        .required { color: #dc3545; }
    </style>
</head>
<body>
    <div class="registro-container">
        <div class="text-center mb-4">
            <a href="../index.php" class="logo-brand">Mextium</a>
            <h2 class="registro-title">Crear Nueva Cuenta</h2>
        </div>
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombres" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        <label for="nombre">Nombres <span class="required">*</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellidos" required value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>">
                        <label for="apellido">Apellidos <span class="required">*</span></label>
                    </div>
                </div>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <label for="email">Correo Electrónico <span class="required">*</span></label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="celular" name="celular" placeholder="Celular" required value="<?php echo isset($_POST['celular']) ? htmlspecialchars($_POST['celular']) : ''; ?>">
                        <label for="celular">Celular <span class="required">*</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Tipo de documento</option>
                            <option value="CC" <?php echo (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de ciudadanía</option>
                            <option value="CE" <?php echo (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cédula de extranjería</option>
                            <option value="NIT" <?php echo (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] == 'NIT') ? 'selected' : ''; ?>>NIT</option>
                            <option value="TI" <?php echo (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de identidad</option>
                        </select>
                        <label for="tipo_documento">Tipo de documento <span class="required">*</span></label>
                    </div>
                </div>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="numero_documento" name="numero_documento" placeholder="Número de documento" required value="<?php echo isset($_POST['numero_documento']) ? htmlspecialchars($_POST['numero_documento']) : ''; ?>">
                <label for="numero_documento">Número de documento <span class="required">*</span></label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección completa" required value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>">
                <label for="direccion">Dirección Completa <span class="required">*</span></label>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="form-floating">
                        <select class="form-select" id="localidad" name="localidad" required>
                            <option value="">Selecciona tu localidad</option>
                            <option value="Usaquén">Usaquén</option>
                            <option value="Chapinero">Chapinero</option>
                            <option value="Santa Fe">Santa Fe</option>
                            <option value="San Cristóbal">San Cristóbal</option>
                            <option value="Usme">Usme</option>
                            <option value="Tunjuelito">Tunjuelito</option>
                            <option value="Bosa">Bosa</option>
                            <option value="Kennedy">Kennedy</option>
                            <option value="Fontibón">Fontibón</option>
                            <option value="Engativá">Engativá</option>
                            <option value="Suba">Suba</option>
                            <option value="Barrios Unidos">Barrios Unidos</option>
                            <option value="Teusaquillo">Teusaquillo</option>
                            <option value="Los Mártires">Los Mártires</option>
                            <option value="Antonio Nariño">Antonio Nariño</option>
                            <option value="Puente Aranda">Puente Aranda</option>
                            <option value="La Candelaria">La Candelaria</option>
                            <option value="Rafael Uribe Uribe">Rafael Uribe Uribe</option>
                            <option value="Ciudad Bolívar">Ciudad Bolívar</option>
                            <option value="Sumapaz">Sumapaz</option>
                        </select>
                        <label for="localidad">Localidad <span class="required">*</span></label>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad" value="Bogotá" required>
                        <label for="ciudad">Ciudad <span class="required">*</span></label>
                    </div>
                </div>
            </div>
            <div class="form-floating">
                <select class="form-select" id="rol_id" name="rol_id" required>
                    <option value="">Selecciona el tipo de cuenta</option>
                    <option value="1" <?php echo (isset($_POST['rol_id']) && $_POST['rol_id'] == '1') ? 'selected' : ''; ?>>Comprador</option>
                    <option value="2" <?php echo (isset($_POST['rol_id']) && $_POST['rol_id'] == '2') ? 'selected' : ''; ?>>Vendedor</option>
                    <option value="3" <?php echo (isset($_POST['rol_id']) && $_POST['rol_id'] == '3') ? 'selected' : ''; ?>>Administrador</option>
                </select>
                <label for="rol_id">Tipo de Cuenta <span class="required">*</span></label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required minlength="8">
                        <label for="password">Contraseña <span class="required">*</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required minlength="8">
                        <label for="confirm_password">Confirmar Contraseña <span class="required">*</span></label>
                    </div>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="acepta_terminos" name="acepta_terminos" required>
                <label class="form-check-label" for="acepta_terminos">
                    Acepto los <a href="../Support Center/terminos.php" target="_blank">Términos y Condiciones</a>, la <a href="../Support Center/politicas_tratamiento_datos.php" target="_blank">Política de Tratamiento de Datos</a> y el <a href="../Support Center/aviso_privacidad.php" target="_blank">Aviso de Privacidad</a>
                </label>
            </div>
            <button type="submit" class="btn btn-primary-gradient w-100 mb-3">
                <i class="fas fa-user-plus me-2"></i>Crear Mi Cuenta en Mextium
            </button>
            <a href="../mextium.php" class="btn btn-secondary-outline w-100">
                <i class="fas fa-arrow-left me-2"></i>Regresar al Inicio
            </a>
        </form>
        <div class="login-link text-center mt-3">
            <p>¿Ya tienes una cuenta registrada? <a href="inicio_sesion.php">Inicia Sesión Aquí</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
