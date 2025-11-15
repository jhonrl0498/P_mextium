<?php
require_once __DIR__ . '/../model/guia_envio_model.php';
require_once __DIR__ . '/../model/guia_envio_api.php';

class GuiaEnvioController {
    private $model;
    public function __construct() {
        $this->model = new GuiaEnvioModel();
    }
    // Crear guía/envío tras pago exitoso
    public function crearGuia($orden_id, $tracking, $label_url, $datos_envio = null) {
        return $this->model->crearGuia($orden_id, $tracking, $label_url, $datos_envio);
    }
    public function obtenerGuiasPorOrden($orden_id) {
        return $this->model->obtenerGuiasPorOrden($orden_id);
    }
    public function obtenerGuiaPorId($id) {
        return $this->model->obtenerGuiaPorId($id);
    }
}

// Configura la URL y el API Key de tu proveedor
$api_url = 'https://api.envios.com'; // Cambia por la URL real
$api_key = 'TU_API_KEY'; // Cambia por tu API Key real
$guiaAPI = new GuiaEnvioAPI($api_url, $api_key);

// Crear guía de envío (ejemplo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_guia') {
    $datos_envio = [
        'remitente' => $_POST['remitente'],
        'destinatario' => $_POST['destinatario'],
        'direccion' => $_POST['direccion'],
        'ciudad' => $_POST['ciudad'],
        'estado' => $_POST['estado'],
        'codigo_postal' => $_POST['codigo_postal'],
        'telefono' => $_POST['telefono'],
        'peso' => $_POST['peso'],
        'valor' => $_POST['valor'],
        // ...otros datos requeridos por la API
    ];
    $resultado = $guiaAPI->crearGuia($datos_envio);
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

// Consultar rastreo de envío (ejemplo)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['numero_guia'])) {
    $numero_guia = $_GET['numero_guia'];
    $resultado = $guiaAPI->rastrearEnvio($numero_guia);
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}
