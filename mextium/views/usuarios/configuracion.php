<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: inicio_sesion.php');
    exit();
}
require_once __DIR__ . '/../../model/usuario_model.php';
$model = new UsuarioModel();
$usuario = $model->obtenerUsuarioPorId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Cuenta - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3A5AFF;
            --secondary-color: #274BDB;
            --accent-color: #4F6FE8;
            --dark-color: #1A237E;
            --light-color: #F8F9FA;
            --background-color: #3A5AFF; /* azul rey claro sólido */
            --shadow-card: 0 8px 32px rgba(58,90,255,0.10);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--background-color);
            min-height: 100vh;
            color: var(--dark-color);
        }
        .config-container {
            max-width: 650px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            padding: 2.7rem 2.2rem 2.2rem 2.2rem;
            border: 1.5px solid #e3eafc;
        }
        .config-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2.2rem;
            text-align: center;
            letter-spacing: -1px;
        }
        .config-section {
            margin-bottom: 1.5rem;
        }
        .config-label {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.4rem;
            display: block;
        }
        .form-control {
            border-radius: 16px;
            border: 2px solid #e3eafc;
            background: #f8fbff;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(58,90,255,0.04);
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.18rem rgba(58,90,255,0.13);
        }
        .btn-primary-gradient {
            background: var(--primary-color);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 16px;
            padding: 0.85rem 2.2rem;
            font-size: 1.1rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .btn-primary-gradient:hover {
            box-shadow: 0 8px 24px rgba(58,90,255,0.18);
            transform: translateY(-2px);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 16px;
            font-weight: 700;
            background: transparent;
            transition: background 0.2s, color 0.2s;
        }
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .config-icon {
            font-size: 2.2rem;
            color: var(--secondary-color);
            margin-bottom: 0.7rem;
        }
        @media (max-width: 700px) {
            .config-container {
                padding: 1.2rem 0.5rem;
            }
            .config-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="config-container mt-5">
            <div class="config-title">
                <i class="fas fa-cog config-icon"></i><br>Configuración de Cuenta
            </div>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="nombre"><i class="fas fa-user me-2"></i>Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="apellido"><i class="fas fa-user me-2"></i>Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="telefono"><i class="fas fa-phone me-2"></i>Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="cedula"><i class="fas fa-id-card me-2"></i>Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" value="<?= htmlspecialchars($usuario['cedula'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 config-section">
                        <label class="config-label" for="direccion"><i class="fas fa-map-marker-alt me-2"></i>Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>">
                    </div>
                </div>
                <div class="config-section">
                    <label class="config-label" for="notificaciones"><i class="fas fa-bell me-2"></i>Notificaciones</label>
                    <select class="form-control" id="notificaciones" name="notificaciones">
                        <option value="1">Recibir todas</option>
                        <option value="0">Solo importantes</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary-gradient w-100 mb-2">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="profile.php" class="btn btn-outline-primary w-100">
                    <i class="fas fa-arrow-left me-2"></i>Volver a mi perfil
                </a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
