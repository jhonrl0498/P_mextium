<?php
// views/vendedores/entrar.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión Vendedor | Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #378ef2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
        }
        .login-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #2d44aa;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #2d44aa;
        }
        .form-control {
            border-radius: 50px;
            padding: 0.9rem 1.2rem;
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #2d44aa 0%, #378ef2 100%);
            color: #fff;
            border: none;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #378ef2 0%, #2d44aa 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.03);
        }
        .icon-section {
            font-size: 2.5rem;
            margin-bottom: 1.2rem;
            color: #378ef2;
            text-align: center;
        }
        @media (max-width: 500px) {
            .login-container {
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="icon-section">
            <i class="fas fa-store"></i>
        </div>
        <div class="login-title">Iniciar Sesión Vendedor</div>
        <form method="post" action="./mextium_tienda.php">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <button type="submit" class="btn-login mt-2">
                <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
            </button>
        </form>
        <div class="text-center mt-3">
            <a href="registro_tienda.php" class="text-decoration-none" style="color:#2d44aa;font-weight:600;">¿No tienes tienda? Regístrate aquí</a>
        </div>
    </div>
</body>
</html>
