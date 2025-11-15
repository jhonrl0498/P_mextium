<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../model/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare('UPDATE vendedores SET estado = ? WHERE id = ?');
    $ok = $stmt->execute(['activo', $id]);
    echo json_encode(['success' => $ok]);
    exit;
}
echo json_encode(['success' => false]);
