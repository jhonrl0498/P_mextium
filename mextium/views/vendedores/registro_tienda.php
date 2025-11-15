<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../usuarios/inicio_sesion.php?redirect=registro_tienda');
    exit;
}
$usuario_id = $_SESSION['user_id'];

// Obtener categorías desde la base de datos
require_once __DIR__ . '/../../model/categorias_model.php';
$catModel = new CategoriasModel();
$categorias = $catModel->obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Tienda - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #f9f9f9ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23a)"/><circle cx="800" cy="300" r="100" fill="url(%23a)"/><circle cx="400" cy="700" r="200" fill="url(%23a)"/></svg>');
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        .register-container {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(95,170,255,0.18);
            padding: 3rem 2rem;
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 2;
            overflow: hidden;
            animation: fadeInUp 1s;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px);}
            100% { opacity: 1; transform: translateY(0);}
        }
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 3;
        }
        .logo-brand {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #5FAAFF 0%, #4A90E2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            text-decoration: none;
            display: inline-block;
            animation: pulse-logo 2s ease-in-out infinite;
        }
        @keyframes pulse-logo {
            0%, 100% { transform: scale(1);}
            50% { transform: scale(1.05);}
        }
        .register-title {
            color: #2C3E50;
            font-weight: 700;
            font-size: 1.7rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .register-subtitle {
            color: #666;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-floating {
            margin-bottom: 1.3rem;
            position: relative;
            z-index: 3;
        }
        .form-control {
            border: 2px solid rgba(95,170,255,0.18);
            border-radius: 15px;
            padding: 1.1rem;
            font-size: 1rem;
            background: rgba(255,255,255,0.85);
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #5FAAFF;
            box-shadow: 0 0 0 0.2rem rgba(95,170,255,0.18);
            background: #fff;
        }
        .form-label {
            color: #13469d;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .btn-primary-gradient {
            background: linear-gradient(135deg, #5FAAFF 0%, #4A90E2 100%);
            border: none;
            color: white;
            padding: 1.1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
            position: relative;
            z-index: 3;
            overflow: hidden;
        }
        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #4A90E2 0%, #5FAAFF 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(95,170,255,0.18);
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            position: relative;
            z-index: 3;
        }
        .back-link a {
            color: #5FAAFF;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .back-link a:hover {
            text-decoration: underline;
            color: #13469d;
        }
        @media (max-width: 576px) {
            .register-container {
                padding: 1.5rem 0.5rem;
            }
            .logo-brand {
                font-size: 2rem;
            }
            .register-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container" data-aos="zoom-in">
        <div class="logo-section">
            <a href="../mextium.php" class="logo-brand">Mextium</a>
            <div>
                <i class="fas fa-store fa-2x text-primary mb-2"></i>
            </div>
            <h2 class="register-title">Registro de Tienda</h2>
            <p class="register-subtitle">Crea tu tienda y comienza a vender en minutos</p>
        </div>
    <form method="POST" action="../../controller/tienda_controller.php" enctype="multipart/form-data">
            <?php if ($usuario_id): ?>
                <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_id); ?>">
            <?php endif; ?>
            <!-- Campos ocultos requeridos por el backend -->
            <input type="hidden" name="ciudad" value="Bogotá D.C.">
            <input type="hidden" name="categoria_principal" value="General">
            <input type="hidden" name="departamento_id" value="1">
            <div class="form-floating">
                <input type="text" class="form-control" id="nombre_tienda" name="nombre_tienda" placeholder="Nombre de la Tienda" required>
                <label for="nombre_tienda"><i class="fas fa-store me-2"></i>Nombre de la Tienda</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="propietario" name="propietario" placeholder="Nombre del Propietario" required>
                <label for="propietario"><i class="fas fa-user me-2"></i>Nombre del Propietario</label>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                <label for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
            </div>
            <div class="form-floating">
                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" required>
                <label for="telefono"><i class="fas fa-phone me-2"></i>Teléfono</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
                <label for="direccion"><i class="fas fa-map-marker-alt me-2"></i>Dirección</label>
            </div>
            <div class="form-floating">
                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción de la Tienda" style="height: 80px"></textarea>
                <label for="descripcion"><i class="fas fa-info-circle me-2"></i>Descripción de la Tienda</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="localidad" name="localidad" required>
                    <option value="">Selecciona la localidad</option>
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
                <label for="localidad"><i class="fas fa-map-pin me-2"></i>Localidad <span class="required">*</span></label>
            </div>
                <!-- Eliminada inclusión de _categorias_select.php, no se requiere para localidad -->
            <div class="form-floating">
                <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecciona una categoría principal</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="categoria_id"><i class="fas fa-tags me-2"></i>Categoría Principal <span class="required">*</span></label>
            </div>
            <div class="form-floating">
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <label for="imagen"><i class="fas fa-image me-2"></i>Imagen de la Tienda (opcional)</label>
            </div>
            <button type="submit" class="btn btn-primary-gradient mb-2 mt-2">
                <i class="fas fa-user-plus me-2"></i>Registrar Tienda
            </button>
        </form>
        <div class="back-link">
            <a href="../mextium.php"><i class="fas fa-arrow-left me-1"></i>Volver al inicio</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nombre_tienda').focus();
        });
    </script>
</body>
</html>