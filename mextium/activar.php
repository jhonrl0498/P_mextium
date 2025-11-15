<?php
// activar.php: Endpoint para activar la cuenta de usuario por token
require_once __DIR__ . '/model/usuario_model.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo '<h2>Token de activación no proporcionado.</h2>';
    exit;
}


$model = new UsuarioModel();
$usuario = $model->obtenerUsuarioPorTokenActivacion($token);

if (!$usuario) {
    echo '<h2>Token inválido o usuario no encontrado.</h2>';
    exit;
}

if ($usuario['estado'] === 'activo') {
    echo '<h2>Tu cuenta ya está activada.</h2>';
    exit;
}

if ($model->activarUsuarioPorId($usuario['id'])) {
    echo '<h2>¡Cuenta activada exitosamente!</h2><p>Ya puedes iniciar sesión.</p>';
} else {
    echo '<h2>Error al activar la cuenta.</h2>';
}
?>
