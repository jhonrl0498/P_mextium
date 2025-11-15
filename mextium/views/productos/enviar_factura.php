<?php
// enviar_factura.php
// Recibe el PDF en base64 por POST y lo envía por correo como adjunto
session_start();
require_once __DIR__ . '/../../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pdf'])) {
    $pdfBase64 = $_POST['pdf'];
    $destinatario = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@mextium.com';
    $asunto = 'Factura de tu compra en Mextium';
    $mensaje = '<h2>Factura de tu compra</h2>Adjunto encontrarás el PDF de tu factura.';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com '; // Cambia por tu host SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'soporte@mextium.com'; // Cambia por tu usuario SMTP
        $mail->Password = 'Js140906Hc.o'; // Cambia por tu contraseña SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('soporte@mextium.com', 'Mextium');
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        // Adjuntar PDF
        $pdfData = explode(',', $pdfBase64, 2);
        $pdfContent = isset($pdfData[1]) ? base64_decode($pdfData[1]) : base64_decode($pdfData[0]);
        $mail->addStringAttachment($pdfContent, 'factura_mextium.pdf', 'base64', 'application/pdf');
        $mail->send();
        http_response_code(200);
        echo 'Factura enviada correctamente.';
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error al enviar la factura: ' . $mail->ErrorInfo;
    }
    exit;
}
http_response_code(400);
echo 'Solicitud inválida.';
