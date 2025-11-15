<?php
session_start();
// Procesar envío AJAX del formulario de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['email'], $_POST['asunto'], $_POST['mensaje'])) {
    $to = 'soporte@mextium.com';
    $nombre = strip_tags($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
    $telefono = isset($_POST['telefono']) ? strip_tags($_POST['telefono']) : '';
    $categoria = isset($_POST['categoria']) ? strip_tags($_POST['categoria']) : '';
    $asunto = strip_tags($_POST['asunto']);
    $mensaje = strip_tags($_POST['mensaje']);
    if (!$nombre || !$email || !$asunto || !$mensaje) {
        http_response_code(400);
        echo json_encode(['success'=>false, 'msg'=>'Faltan datos obligatorios.']);
        exit;
    }
    $subject = "[Contacto Web] $asunto";
    $body = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\nCategoría: $categoria\n\nMensaje:\n$mensaje";
    $headers = "From: $nombre <$email>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
    $ok = mail($to, $subject, $body, $headers);
    if ($ok) {
        echo json_encode(['success'=>true]);
    } else {
        http_response_code(500);
        echo json_encode(['success'=>false, 'msg'=>'No se pudo enviar el correo.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Contacto - Mextium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Paleta de azules personalizada */
            --primary-color: #2176FF; /* Azul principal */
            --secondary-color: #33A1FD; /* Azul claro */
            --accent-color: #0056b3; /* Azul oscuro */
            --dark-color: #1B263B;
            --light-color: #EAF6FF;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --gradient-primary: linear-gradient(135deg, #2176FF 0%, #33A1FD 100%);
            --gradient-accent: linear-gradient(135deg, #0056b3 0%, #2176FF 100%);
            --gradient-success: linear-gradient(135deg, #51cf66, #40c057);
            --gradient-info: linear-gradient(135deg, #33A1FD, #2176FF);
            --shadow-card: 0 15px 35px rgba(33, 118, 255, 0.15);
            --shadow-hover: 0 20px 40px rgba(33, 118, 255, 0.25);
            --shadow-light: 0 5px 15px rgba(33, 118, 255, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Fondo azul claro degradado en todo el body */
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #EAF6FF 0%, #B3D8FF 100%);
            min-height: 100vh;
            color: var(--dark-color);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(95, 170, 255, 0.1);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .btn-outline-primary {
            border-color: rgba(95, 170, 255, 0.3);
            color: var(--primary-color);
            transition: all 0.3s ease;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-accent);
            color: white;
            padding: 8rem 0 6rem;
            position: relative;
            overflow: hidden;
            margin-top: 80px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 200"><path d="M0,100 C300,200 700,0 1000,100 L1000,200 L0,200 Z" fill="rgba(255,255,255,0.1)"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 3rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 3rem;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
        }

        .hero-stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }

        /* Contact Methods Section */
        .contact-methods {
            /* Fondo blanco para destacar el container de métodos de contacto */
            padding: 5rem 0;
            background: #fff;
            position: relative;
            margin-top: -3rem;
            border-radius: 30px 30px 0 0;
            box-shadow: var(--shadow-light);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .contact-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .contact-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .contact-description {
            color: var(--dark-color);
            opacity: 0.7;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .contact-action {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .contact-action:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Contact Form Section */
        .contact-form-section {
            /* Fondo azul claro para la sección del formulario */
            padding: 5rem 0;
            background: linear-gradient(135deg, #EAF6FF 0%, #33A1FD 100%);
        }

        .form-container {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: var(--shadow-card);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid rgba(95, 170, 255, 0.15);
            border-radius: 15px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-height: 60px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
        }

        .form-select {
            border: 2px solid rgba(95, 170, 255, 0.15);
            border-radius: 15px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-height: 60px;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(95, 170, 255, 0.25);
        }

        .btn-submit {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(95, 170, 255, 0.4);
        }

        /* FAQ Section */
        .faq-section {
            padding: 5rem 0;
            background: white;
        }

        .faq-item {
            background: var(--light-color);
            border-radius: 15px;
            margin-bottom: 1rem;
            border: 1px solid rgba(95, 170, 255, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: var(--shadow-light);
        }

        .faq-question {
            background: transparent;
            border: none;
            padding: 1.5rem;
            width: 100%;
            text-align: left;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: rgba(95, 170, 255, 0.05);
        }

        .faq-answer {
            padding: 0 1.5rem 1.5rem;
            color: var(--dark-color);
            opacity: 0.8;
            line-height: 1.6;
            display: none;
        }

        .faq-answer.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* Office Hours */
        .office-hours {
            background: var(--gradient-info);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .hour-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .hour-day {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .hour-time {
            opacity: 0.9;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                gap: 1.5rem;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .hours-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Success Message */
        .success-message {
            background: var(--gradient-success);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .success-message.show {
            display: flex;
            animation: fadeInUp 0.5s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../mextium.php">
                <i class="fas fa-cube me-2"></i>Mextium
            </a>
            <div class="d-flex">
                <a href="../mextium.php" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <i class="fas fa-headset me-3"></i>Centro de Contacto
                </h1>
                <p class="hero-subtitle">
                    Estamos aquí para ayudarte. Contáctanos a través de cualquier canal disponible 
                    y nuestro equipo te responderá lo antes posible.
                </p>
                
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number">24/7</span>
                        <span class="hero-stat-label">Soporte Disponible</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">&lt;2h</span>
                        <span class="hero-stat-label">Tiempo de Respuesta</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">98%</span>
                        <span class="hero-stat-label">Satisfacción Cliente</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Methods -->
    <section class="contact-methods">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">¿Cómo Prefieres Contactarnos?</h2>
                <p class="section-subtitle">Elige el canal que más te convenga</p>
            </div>

            <div class="contact-grid">
                <!-- WhatsApp -->
                <div class="contact-card fade-in">
                    <div class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3 class="contact-title">WhatsApp</h3>
                    <p class="contact-description">
                        Respuesta inmediata a través de nuestro WhatsApp Business. 
                        Ideal para consultas rápidas y soporte técnico.
                    </p>
                    <a href="https://wa.me/573217162317" class="contact-action" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        Chatear Ahora
                    </a>
                </div>

                <!-- Email -->
                <div class="contact-card fade-in" style="animation-delay: 0.1s;">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="contact-title">Correo Electrónico</h3>
                    <p class="contact-description">
                        Para consultas detalladas, reportes o solicitudes formales. 
                        Te responderemos en menos de 24 horas.
                    </p>
                    <a href="mailto:soporte@mextium.com" class="contact-action">
                        <i class="fas fa-envelope"></i>
                        Enviar Email
                    </a>
                </div>

                <!-- Teléfono -->
                <div class="contact-card fade-in" style="animation-delay: 0.2s;">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="contact-title">Teléfono</h3>
                    <p class="contact-description">
                        Habla directamente con nuestro equipo de soporte. 
                        Disponible de lunes a viernes de 9:00 AM a 6:00 PM.
                    </p>
                    <a href="tel:+573217162317" class="contact-action">
                        <i class="fas fa-phone"></i>
                        Llamar Ahora
                    </a>
                </div>

                <!-- Chat en Vivo -->
                <div class="contact-card fade-in" style="animation-delay: 0.3s;">
                    <div class="contact-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="contact-title">Chat en Vivo</h3>
                    <p class="contact-description">
                        Chatea en tiempo real con nuestros agentes de soporte. 
                        Disponible durante horario de oficina.
                    </p>
                    <button class="contact-action" onclick="iniciarChat()">
                        <i class="fas fa-comments"></i>
                        Iniciar Chat
                    </button>
                </div>

                <!-- Centro de Ayuda -->
                <div class="contact-card fade-in" style="animation-delay: 0.4s;">
                    <div class="contact-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 class="contact-title">Centro de Ayuda</h3>
                    <p class="contact-description">
                        Encuentra respuestas a las preguntas más frecuentes 
                        y tutoriales paso a paso.
                    </p>
                    <a href="#faq" class="contact-action">
                        <i class="fas fa-book"></i>
                        Ver FAQ
                    </a>
                </div>

                <!-- Soporte Técnico -->
                <div class="contact-card fade-in" style="animation-delay: 0.5s;">
                    <div class="contact-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="contact-title">Soporte Técnico</h3>
                    <p class="contact-description">
                        Para problemas técnicos específicos, errores del sistema 
                        o configuración de cuenta.
                    </p>
                    <a href="#contact-form" class="contact-action">
                        <i class="fas fa-ticket-alt"></i>
                        Crear Ticket
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-form-section" id="contact-form">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Envíanos un Mensaje</h2>
                <p class="section-subtitle">Completa el formulario y nos pondremos en contacto contigo</p>
            </div>

            <div class="form-container">
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i>
                    ¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.
                </div>

                <form id="contactForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu nombre" required>
                                <label for="nombre">
                                    <i class="fas fa-user me-2"></i>Nombre Completo
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Tu email" required>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Correo Electrónico
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Tu teléfono">
                                <label for="telefono">
                                    <i class="fas fa-phone me-2"></i>Teléfono (Opcional)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="soporte">Soporte Técnico</option>
                                    <option value="ventas">Consultas de Ventas</option>
                                    <option value="facturacion">Facturación</option>
                                    <option value="cuenta">Problemas de Cuenta</option>
                                    <option value="sugerencia">Sugerencias</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <label for="categoria">
                                    <i class="fas fa-tags me-2"></i>Categoría
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto del mensaje" required>
                        <label for="asunto">
                            <i class="fas fa-heading me-2"></i>Asunto
                        </label>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control" id="mensaje" name="mensaje" placeholder="Tu mensaje" style="height: 150px" required></textarea>
                        <label for="mensaje">
                            <i class="fas fa-comment me-2"></i>Mensaje
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="privacidad" required>
                        <label class="form-check-label" for="privacidad">
                            Acepto la <a href="#" class="text-primary">Política de Privacidad</a> y el procesamiento de mis datos personales.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane me-2"></i>
                        Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Office Hours -->
    <section class="office-hours">
        <div class="container">
            <h2 class="text-center mb-4">
                <i class="fas fa-clock me-2"></i>Horarios de Atención
            </h2>
            <p class="text-center mb-4 opacity-75">
                Nuestro equipo está disponible en los siguientes horarios
            </p>

            <div class="hours-grid">
                <div class="hour-item">
                    <div class="hour-day">Lunes - Viernes</div>
                    <div class="hour-time">9:00 AM - 6:00 PM</div>
                </div>
                <div class="hour-item">
                    <div class="hour-day">Sábados</div>
                    <div class="hour-time">10:00 AM - 2:00 PM</div>
                </div>
                <div class="hour-item">
                    <div class="hour-day">Domingos</div>
                    <div class="hour-time">Cerrado</div>
                </div>
                <div class="hour-item">
                    <div class="hour-day">Emergencias</div>
                    <div class="hour-time">24/7 por WhatsApp</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Preguntas Frecuentes</h2>
                <p class="section-subtitle">Encuentra respuestas rápidas a las dudas más comunes</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(1)">
                            ¿Cómo puedo crear una cuenta en Mextium?
                            <i class="fas fa-chevron-down" id="icon-1"></i>
                        </button>
                        <div class="faq-answer" id="answer-1">
                            Para crear una cuenta, haz clic en "Registrarse" en la parte superior de la página, 
                            completa el formulario con tus datos y verifica tu correo electrónico. ¡Es muy fácil y rápido!
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(2)">
                            ¿Cuáles son los métodos de pago disponibles?
                            <i class="fas fa-chevron-down" id="icon-2"></i>
                        </button>
                        <div class="faq-answer" id="answer-2">
                            Aceptamos tarjetas de crédito y débito (Visa, MasterCard, American Express), 
                            PayPal, transferencias bancarias y pago en efectivo en tiendas de conveniencia.
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(3)">
                            ¿Cómo puedo rastrear mi pedido?
                            <i class="fas fa-chevron-down" id="icon-3"></i>
                        </button>
                        <div class="faq-answer" id="answer-3">
                            Una vez confirmado tu pedido, recibirás un número de seguimiento por email. 
                            También puedes consultar el estado en la sección "Mis Pedidos" de tu cuenta.
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(4)">
                            ¿Cuál es la política de devoluciones?
                            <i class="fas fa-chevron-down" id="icon-4"></i>
                        </button>
                        <div class="faq-answer" id="answer-4">
                            Tienes 30 días para devolver productos en perfecto estado. 
                            El proceso es gratuito y puedes iniciarlo desde tu cuenta o contactándonos directamente.
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(5)">
                            ¿Cómo me convierto en vendedor?
                            <i class="fas fa-chevron-down" id="icon-5"></i>
                        </button>
                        <div class="faq-answer" id="answer-5">
                            Para ser vendedor en Mextium, completa el formulario de registro como vendedor, 
                            proporciona la documentación requerida y espera la verificación de nuestro equipo.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FAQ Toggle
        function toggleFaq(id) {
            const answer = document.getElementById(`answer-${id}`);
            const icon = document.getElementById(`icon-${id}`);
            
            // Cerrar otras respuestas
            for (let i = 1; i <= 5; i++) {
                if (i !== id) {
                    document.getElementById(`answer-${i}`).classList.remove('show');
                    document.getElementById(`icon-${i}`).style.transform = 'rotate(0deg)';
                }
            }
            
            // Toggle actual
            answer.classList.toggle('show');
            if (answer.classList.contains('show')) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Contact Form
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);
            fetch(window.location.pathname, {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.success) {
                    document.getElementById('successMessage').classList.add('show');
                    form.reset();
                    document.getElementById('successMessage').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    setTimeout(() => {
                        document.getElementById('successMessage').classList.remove('show');
                    }, 5000);
                } else {
                    alert(resp.msg || 'No se pudo enviar el mensaje.');
                }
            })
            .catch(() => {
                alert('Error de conexión. Intenta más tarde.');
            });
        });

        // Iniciar Chat (simulado)
        function iniciarChat() {
            alert('¡Chat en vivo próximamente! Mientras tanto, puedes contactarnos por WhatsApp.');
        }

        // Animaciones al scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observar elementos con animación
        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        // Smooth scroll para enlaces ancla
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>