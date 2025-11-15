<?php

require_once __DIR__ . '/../../model/usuario_model.php';

$model = new UsuarioModel();
$reflection = new ReflectionMethod($model, 'actualizarPerfil');

echo "<h3>Información del método actualizarPerfil:</h3>";
echo "<p>Número de parámetros: " . $reflection->getNumberOfParameters() . "</p>";
echo "<p>Parámetros requeridos: " . $reflection->getNumberOfRequiredParameters() . "</p>";

echo "<h4>Parámetros:</h4>";
foreach ($reflection->getParameters() as $param) {
    echo "- " . $param->getName();
    if ($param->isOptional()) {
        echo " (opcional)";
    }
    echo "<br>";
}
?>