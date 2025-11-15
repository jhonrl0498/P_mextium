<?php
// Cambiar las rutas (usar barras normales / en lugar de \)
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailModel {
    private $config;
    
    public function __construct() {
        $this->config = include __DIR__ . '/../config/email_config.php';
    }
    
    /**
     * Enviar email de recuperación de contraseña
     */
    public function enviarEmailRecuperacion($email, $token, $nombreUsuario = '') {
        try {
            $mail = new PHPMailer(true);
            
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL para puerto 465
            $mail->Port = $this->config['smtp_port'];
            $mail->CharSet = 'UTF-8';
            
            // Configuración del email
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($email, $nombreUsuario);
            $mail->addReplyTo($this->config['reply_to'], 'No Reply');
            
            // Contenido del email
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de Contraseña - Mextium';
            
            // URL para el enlace de recuperación
            $resetUrl = "https://mextium.com/mextium/views/usuarios/resetear_contraseña.php?token=" . $token;
            
            $mail->Body = $this->plantillaEmailRecuperacion($nombreUsuario, $resetUrl, $token);
            $mail->AltBody = $this->plantillaEmailTexto($nombreUsuario, $resetUrl);
            
            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Email enviado exitosamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al enviar email: ' . $mail->ErrorInfo
            ];
        }
    }
    
    /**
     * Plantilla HTML para email de recuperación
     */
    private function plantillaEmailRecuperacion($nombre, $url, $token) {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recuperación de Contraseña</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #5FAAFF, #4A90E2); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; background: #5FAAFF; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
                .btn:hover { background: #4A90E2; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Mextium</h1>
                    <h2>Recuperación de Contraseña</h2>
                </div>
                <div class='content'>
                    <h3>¡Hola" . ($nombre ? " $nombre" : "") . "!</h3>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Mextium.</p>
                    
                    <p>Si fuiste tú quien solicitó este cambio, haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$url' class='btn'>Restablecer mi Contraseña</a>
                    </div>
                    
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p style='background: #e9ecef; padding: 10px; border-radius: 5px; word-break: break-all;'>$url</p>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong>
                        <ul>
                            <li>Este enlace expirará en <strong>1 hora</strong> por seguridad</li>
                            <li>Solo puedes usar este enlace una vez</li>
                            <li>Si no solicitaste este cambio, ignora este correo</li>
                        </ul>
                    </div>
                    
                    <p>Si tienes problemas con el enlace, contacta a nuestro equipo de soporte.</p>
                    
                    <p><strong>Token de verificación:</strong> <code>$token</code></p>
                </div>
                <div class='footer'>
                    <p>Este correo fue enviado automáticamente desde Mextium<br>
                    Si no solicitaste este cambio, puedes ignorar este mensaje</p>
                    <p>&copy; " . date('Y') . " Mextium. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Plantilla de texto plano
     */
    private function plantillaEmailTexto($nombre, $url) {
        return "
        MEXTIUM - Recuperación de Contraseña
        
        Hola" . ($nombre ? " $nombre" : "") . ",
        
        Recibimos una solicitud para restablecer la contraseña de tu cuenta.
        
        Para crear una nueva contraseña, visita este enlace:
        $url
        
        IMPORTANTE:
        - Este enlace expira en 1 hora
        - Solo puedes usarlo una vez
        - Si no solicitaste este cambio, ignora este correo
        
        Saludos,
        Equipo Mextium
        ";
    }
    
    /**
     * Enviar email de notificación de guía/envío
     */
    public function enviarGuiaEnvio($email, $nombreUsuario, $tracking, $pdf = null, $extra = []) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->config['smtp_port'];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($email, $nombreUsuario);
            $mail->addReplyTo($this->config['reply_to'], 'No Reply');
            $mail->isHTML(true);
            $mail->Subject = 'Tu guía de envío - Mextium';
            $destino = isset($extra['destino']) ? $extra['destino'] : '';
            $mail->Body = "<h2>¡Tu compra ya tiene guía de envío!</h2>"
                . "<p><b>Tracking:</b> " . htmlspecialchars($tracking) . "</p>"
                . ($pdf ? ("<p><a href='" . htmlspecialchars($pdf) . "' target='_blank'>Descargar etiqueta PDF</a></p>") : "")
                . ($destino ? ("<p><b>Destino:</b> " . htmlspecialchars($destino) . "</p>") : "")
                . "<p>Gracias por comprar en Mextium.</p>";
            $mail->AltBody = "Tu compra ya tiene guía de envío.\nTracking: $tracking\n" . ($pdf ? "Etiqueta: $pdf\n" : "") . ($destino ? "Destino: $destino\n" : "") . "Gracias por comprar en Mextium.";
            $mail->send();
            return [ 'success' => true, 'message' => 'Email enviado exitosamente' ];
        } catch (Exception $e) {
            return [ 'success' => false, 'message' => 'Error al enviar email: ' . $mail->ErrorInfo ];
        }
    }
}
?>