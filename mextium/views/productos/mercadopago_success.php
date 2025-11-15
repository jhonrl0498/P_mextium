<?php
session_start();

// Aquí puedes mostrar un mensaje de éxito y redirigir a donde quieras

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando tu envío...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loader {
            border: 8px solid #e3eafc;
            border-top: 8px solid #1976d2;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(13, 71, 161, 0.15);
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 420px;
            margin: 0 auto;
        }
        .success {
            color: #1976d2;
            font-weight: 700;
            font-size: 1.3rem;
        }
        .fail {
            color: #d32f2f;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .btn-blue {
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-blue:hover {
            background: #0d47a1;
        }
    </style>
</head>
<body>
<div class="card text-center">
    <div class="loader"></div>
    <div class="success">¡Pago exitoso!<br>Estamos generando tu factura...</div>
    <div class="mt-2" id="redirect-msg">Serás redirigido automáticamente a tu factura.</div>
    <div class="mt-3">
    <button class="btn btn-blue" onclick="window.location.href='factura.php'">Ver factura</button>
    </div>
</div>
<script>
    // Fallback visual si la redirección tarda más de 6 segundos
    setTimeout(function() {
        document.getElementById('redirect-msg').innerHTML = 'Si no eres redirigido, haz clic en el botón para ver tu factura.';
    window.location.href = 'factura.php';
    }, 6000);
</script>
</body>
</html>
