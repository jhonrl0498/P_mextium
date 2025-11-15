<?php

echo "<h3>Verificando archivos PHPMailer:</h3>";

$files = [
    'C:\xampp\htdocs\mextium\vendor\phpmailer\src\PHPMailer.php',
    'C:\xampp\htdocs\mextium\vendor\phpmailer\src\SMTP.php',
    'C:\xampp\htdocs\mextium\vendor\phpmailer\src\Exception.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ " . basename($file) . " - ENCONTRADO<br>";
    } else {
        echo "❌ " . basename($file) . " - NO ENCONTRADO<br>";
        echo "Buscando en: $file<br><br>";
    }
}
?>