<?php
// test_conexion_hosting.php
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
    echo "<h2 style='color:green'>¡Conexión exitosa a la base de datos!</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    echo "<b>Tablas encontradas:</b><ul>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error de Conexión PDO:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
