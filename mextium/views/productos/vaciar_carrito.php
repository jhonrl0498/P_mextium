<?php
// vaciar_carrito.php
session_start();
unset($_SESSION['carrito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito Vacío - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
    body { background: linear-gradient(120deg, #2d44aa 0%, #5FAAFF 100%); min-height: 100vh; font-family: 'Inter', sans-serif; }
    .vaciar-container { max-width: 500px; margin: 4rem auto; background: #fff; border-radius: 28px; box-shadow: 0 8px 32px rgba(80,120,180,0.13); padding: 2.5rem 2rem; text-align: center; }
    .vaciar-icon { font-size: 4rem; color: #2d44aa; margin-bottom: 1.2rem; }
    .vaciar-title { font-weight: 900; color: #2d44aa; margin-bottom: 1rem; }
    .vaciar-msg { font-size: 1.1rem; color: #4A90E2; margin-bottom: 2rem; }
    .btn-volver { background: linear-gradient(90deg, #2d44aa 0%, #5FAAFF 100%); color: #fff; border-radius: 30px; font-weight: 700; padding: 0.7rem 2.2rem; border: none; }
    .btn-volver:hover { background: linear-gradient(90deg, #5FAAFF 0%, #2d44aa 100%); color: #fff; }
        @media (max-width: 600px) {
            .vaciar-container { padding: 1.2rem 0.5rem; }
            .vaciar-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
    <div class="vaciar-container">
        <div class="vaciar-icon"><i class="fas fa-trash-alt"></i></div>
        <h2 class="vaciar-title">Carrito vaciado</h2>
        <div class="vaciar-msg">Tu carrito ha sido vaciado correctamente.</div>
        <a href="../mextium.php" class="btn btn-volver"><i class="fas fa-home me-1"></i> Volver al inicio</a>
    </div>
</body>
</html>
