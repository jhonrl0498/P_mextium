<?php
class CategoriasModel {
    private $pdo;
    public function __construct() {
        if (file_exists(__DIR__ . '/database.php')) {
            require_once __DIR__ . '/database.php';
            if (class_exists('Database')) {
                $this->pdo = Database::getInstance()->getConnection();
            }
        }
        if (!$this->pdo) {
            require_once __DIR__ . '/conexion.php';
            global $pdo;
            if (isset($pdo) && $pdo instanceof PDO) {
                $this->pdo = $pdo;
            }
        }
        if (!$this->pdo) {
            echo '<pre style="color:red;">Error: No se pudo inicializar la conexión a la base de datos en CategoriasModel.</pre>';
        }
    }

    public function obtenerTodas() {
        try {
            $stmt = $this->pdo->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
