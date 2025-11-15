<?php

$host = "82.197.82.93";
$db = "u366162802_mextium";
$user = "u366162802_santiago";
$pass = "vU7=5WEQXw";
$charset = "utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (\PDOException $e) {
    echo "<pre>Error de Conexión PDO: " . $e->getMessage() . "</pre>";
    die();
}



