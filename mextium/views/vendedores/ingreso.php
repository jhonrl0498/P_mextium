<?php
// views/vendedores/ingreso.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso Vendedores | Mextium</title>
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
        .ingreso-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .ingreso-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #2d44aa;
        }
        .ingreso-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
            border: none;
            transition: all 0.3s;
        }
        .btn-registrarse {
            background: linear-gradient(135deg, #ffb347 0%, #ffcc33 100%);
            color: #212529;
        }
        .btn-registrarse:hover {
            background: linear-gradient(135deg, #ffcc33 0%, #ffb347 100%);
            color: #212529;
            transform: translateY(-2px) scale(1.03);
        }
        .btn-iniciar {
            background: linear-gradient(135deg, #2d44aa 0%, #378ef2 100%);
            color: #fff;
        }
        .btn-iniciar:hover {
            background: linear-gradient(135deg, #378ef2 0%, #2d44aa 100%);
            color: #fff;
            transform: translateY(-2px) scale(1.03);
        }
        .icon-section {
            font-size: 2.5rem;
            margin-bottom: 1.2rem;
            color: #378ef2;
        }
        @media (max-width: 500px) {
            .ingreso-container {
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="ingreso-container">
        <div class="icon-section">
            <i class="fas fa-store"></i>
        </div>
        <div class="ingreso-title">Bienvenido vendedor</div>
        <button class="ingreso-btn btn-registrarse" onclick="window.location.href='registro_tienda.php'">
            <i class="fas fa-user-plus me-2"></i> Registrarse (si no tienes cuenta)
        </button>
        <button class="ingreso-btn btn-iniciar" onclick="window.location.href='entrar.php'">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar sesión (si ya tienes tienda)
        </button>
    </div>
</body>
</html>
