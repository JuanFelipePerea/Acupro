<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcuPro - Tu agenda escolar, inteligente y especializada</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <a href="#" class="logo">AcuPro</a>
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#que-es">¿Qué es?</a></li>
                <li><a href="#como-funciona">¿Cómo funciona?</a></li>
                <li><a href="#beneficios">Beneficios</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="../db/login-registro/login.php" class="nav-cta-button">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="container">
            <div class="hero-content">
                <h1>AcuPro: Tu agenda escolar</h1>
                <p>La plataforma completa para organizar citas, comunicarte con estudiantes y padres, y gestionar tu agenda académica de manera profesional y eficiente.</p>
                <a href="../db/login-registro/login.php" class="cta-button">¡Comienza Ahora!</a>
            </div>
        </div>
    </section>

    <!-- What is AcuPro -->
    <section class="section about" id="que-es">
        <div class="container">
            <h2 class="section-title">Una solución pensada para psicólogos escolares</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>AcuPro no es una plataforma de solicitudes ni depende de terceros. <strong>Tú tienes el control total.</strong> Como psicólogo escolar, organizas todo desde tu perfil personal de manera autónoma.</p>
                    <br>
                    <p>Diseñado específicamente para el contexto educativo, AcuPro entiende las necesidades únicas del acompañamiento emocional estudiantil y te proporciona las herramientas exactas que necesitas para ser más eficiente y organizado.</p>
                    <br>
                    <p>Es como tener un <strong>mapa organizado de relaciones y citas</strong> encapsulado en la palma de tu mano, donde puedes visualizar con claridad todo el contexto de acompañamiento emocional de tus estudiantes.</p>
                </div>
                <div class="about-visual">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section how-it-works" id="como-funciona">
        <div class="container">
            <h2 class="section-title">Tan personal como tu propia agenda, pero mejor</h2>
            <p class="section-subtitle">AcuPro simplifica tu trabajo diario con funcionalidades intuitivas y especializadas</p>
            
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h3>Crear citas fácilmente</h3>
                    <p>Programa citas con estudiantes y acudientes en segundos. Interfaz intuitiva diseñada para tu flujo de trabajo.</p>
                </div>
                
                <div class="step-card animate-on-scroll">
                    <div class="step-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Base de datos de estudiantes</h3>
                    <p>Accede rápidamente a la información de todos tus estudiantes. Todo organizado y al alcance de un clic.</p>
                </div>
                
                <div class="step-card animate-on-scroll">
                    <div class="step-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Notificaciones automáticas</h3>
                    <p>Envía correos de recordatorio automáticamente. Nunca más tendrás que preocuparte por las ausencias.</p>
                </div>
                
                <div class="step-card animate-on-scroll">
                    <div class="step-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Historial inteligente</h3>
                    <p>Revisa el historial de citas con filtros avanzados. Mantén el seguimiento perfecto de cada caso.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section class="section benefits" id="beneficios">
        <div class="container">
            <h2 class="section-title">¿Por qué AcuPro?</h2>
            <p class="section-subtitle">Descubre cómo AcuPro transformará tu práctica profesional</p>
            
            <div class="benefits-grid">
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Ahorro de tiempo significativo</h3>
                        <p>Reduce el tiempo dedicado a tareas administrativas y enfócate en lo que realmente importa: tus estudiantes.</p>
                    </div>
                </div>
                
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Organización mental clara</h3>
                        <p>Visualiza toda la información de manera organizada y toma decisiones más informadas sobre cada caso.</p>
                    </div>
                </div>
                
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Automatización inteligente</h3>
                        <p>Los recordatorios y notificaciones se envían automáticamente, eliminando errores humanos.</p>
                    </div>
                </div>
                
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Acceso desde cualquier dispositivo</h3>
                        <p>Trabaja desde tu computadora, tablet o móvil. Tu información siempre disponible cuando la necesites.</p>
                    </div>
                </div>
                
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Mejora en la productividad</h3>
                        <p>Atiende más casos con mayor calidad. AcuPro optimiza tu tiempo para maximizar tu impacto.</p>
                    </div>
                </div>
                
                <div class="benefit-item animate-on-scroll">
                    <div class="benefit-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Historial inteligente y filtrable</h3>
                        <p>Encuentra cualquier información en segundos con filtros avanzados y búsqueda intuitiva.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta">
        <div class="container">
            <h2>Tú piensas en tus estudiantes. AcuPro piensa en ti.</h2>
            <p>Únete a los psicólogos escolares que ya están transformando su práctica profesional</p>
            <a href="../db/login-registro/login.php" class="cta-button-white">¡Empieza Tu Transformación!</a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>AcuPro</h3>
                    <p>La herramienta especializada para psicólogos escolares que buscan excelencia en el acompañamiento estudiantil.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Enlaces</h3>
                    <a href="#">Sobre AcuPro</a>
                    <a href="#">Política de Privacidad</a>
                    <a href="#">Términos de Uso</a>
                    <a href="#">Soporte</a>
                </div>
                
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <a href="mailto:contacto@acupro.edu.co">contacto@acupro.edu.co</a>
                    <a href="#">Centro de Ayuda</a>
                    <a href="#">Comunidad</a>
                </div>
                
                <div class="footer-section">
                    <h3>Síguenos</h3>
                    <a href="#">LinkedIn</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 AcuPro. Todos los derechos reservados. Diseñado para psicólogos escolares con pasión.</p>
            </div>
        </div>
    </footer>
<script src="index.js"></script>
</body>
</html>