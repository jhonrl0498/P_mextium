<?php
// guia_envio_api.php
// Ejemplo genérico para crear guía y rastrear envíos con una API externa

class GuiaEnvioAPI {
    private $api_url;
    private $api_key;

    public function __construct($api_url, $api_key) {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
    }

    // Crear guía de envío
    public function crearGuia($datos_envio) {
        $endpoint = $this->api_url . '/crear-guia'; // Cambia según la API real
        $response = $this->callAPI($endpoint, $datos_envio);
        return $response;
    }

    // Consultar rastreo de envío
    public function rastrearEnvio($numero_guia) {
        $endpoint = $this->api_url . '/rastreo/' . urlencode($numero_guia); // Cambia según la API real
        $response = $this->callAPI($endpoint, [], 'GET');
        return $response;
    }

    // Función genérica para llamar a la API
    private function callAPI($url, $data = [], $method = 'POST') {
        $ch = curl_init();
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'status' => $httpcode,
            'response' => json_decode($result, true)
        ];
    }
}

// Ejemplo de uso:
// $api = new GuiaEnvioAPI('https://api.envios.com', 'TU_API_KEY');
// $guia = $api->crearGuia($datos_envio);
// $rastreo = $api->rastrearEnvio('NUMERO_GUIA');
