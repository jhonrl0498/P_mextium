<?php
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
    header('Location: dashboard.php');
    exit();
}
require_once __DIR__ . '/../../model/conexion.php';

// Agregar categoría
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre !== '') {
        $stmt = $pdo->prepare('INSERT INTO categorias (nombre) VALUES (?)');
        if ($stmt->execute([$nombre])) {
            $msg = '<div class="alert alert-success">Categoría agregada correctamente.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Error al agregar la categoría.</div>';
        }
    } else {
        $msg = '<div class="alert alert-warning">El nombre es obligatorio.</div>';
    }
}
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Administración Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section { background: #f8fafc; border-radius: 16px; box-shadow: 0 2px 12px rgba(44,68,170,0.07); padding: 2rem; }
        .table-section { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(44,68,170,0.07); padding: 2rem; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center">Gestión de Categorías</h2>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="form-section mb-4">
                <h5 class="mb-3"><i class="fas fa-plus-circle me-1"></i>Agregar nueva categoría</h5>
                <?= $msg ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la categoría <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Guardar</button>
                </form>
            </div>
            <a href="dashboard.php" class="btn btn-secondary w-100"><i class="fas fa-arrow-left me-1"></i>Volver al dashboard</a>
        </div>
        <div class="col-lg-8">
            <div class="table-section">
                <h5 class="mb-3"><i class="fas fa-tags me-1"></i>Categorías registradas</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categorias as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['id']) ?></td>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
