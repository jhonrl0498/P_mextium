<?php
// Modelo para gestionar guías/envíos
require_once __DIR__ . '/database.php';
class GuiaEnvioModel {
    private $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    // Crear una nueva guía/envío
    public function crearGuia($orden_id, $tracking, $label_url, $datos_envio = null) {
        $stmt = $this->pdo->prepare("INSERT INTO guias_envio (orden_id, tracking, label_url, datos_envio, fecha_creacion) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $orden_id,
            $tracking,
            $label_url,
            $datos_envio ? json_encode($datos_envio) : null
        ]);
        return $this->pdo->lastInsertId();
    }
    // Obtener guías por orden
    public function obtenerGuiasPorOrden($orden_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM guias_envio WHERE orden_id = ?");
        $stmt->execute([$orden_id]);
        return $stmt->fetchAll();
    }
    // Obtener guía por ID
    public function obtenerGuiaPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM guias_envio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
