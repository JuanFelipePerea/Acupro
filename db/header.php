<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir funciones
require_once 'functions.php';

// Comprobar autenticación
$autenticado = estaAutenticado();
$nombre_usuario = obtenerNombreUsuario();
$rol_usuario = obtenerRolUsuario();

// Obtener datos para notificaciones
$notificaciones_info = generarHTMLNotificaciones();
$contador_notificaciones = $notificaciones_info['contador'];
$html_notificaciones = $notificaciones_info['html'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
</head>

<body>
    <!-- Navbar mejorado con efecto glassmorphism -->
    <nav class="navbar navbar-expand-lg custom-navbar fixed-top">
        <div class="container">
            <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-journal-check"></i>ACUPRO
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-three-dots text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../db/login-registro/login.php">
                            <i class="bi bi-house-door me-1"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="read.php">
                            <i class="bi bi-calendar-check me-1"></i> Citas
                            <?php if (contarCitasPendientes() > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?php echo contarCitasPendientes(); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="estudiantes.php">
                            <i class="bi bi-people me-1"></i> Estudiantes
                            <?php if (contarEstudiantes() > 0): ?>
                                <span class="badge bg-primary rounded-pill"><?php echo contarEstudiantes(); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>

                <?php if ($autenticado): ?>
                    <!-- Dropdown de notificaciones -->
                    <div class="dropdown me-2">
                        <button class="user-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false" aria-label="Notificaciones">
                            <i class="bi bi-bell"></i>
                            <?php if ($contador_notificaciones > 0): ?>
                                <span class="notification-indicator"></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end dropdown-notifications" aria-labelledby="notificationDropdown">
                            <div class="user-header">
                                <h6>Notificaciones</h6>
                                <p>Tienes <?php echo $contador_notificaciones; ?> notificaciones nuevas</p>
                            </div>

                            <?php echo $html_notificaciones; ?>

                            <a href="todas_notificaciones.php" class="all-notifications">Ver todas las notificaciones</a>
                        </div>
                    </div>

                    <!-- Botón de usuario con dropdown mejorado -->
                    <div class="dropdown user-dropdown">
                        <button class="user-btn" type="button" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false" aria-label="Perfil de usuario">
                            <i class="bi bi-person"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <div class="user-header">
                                <div class="user-avatar">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h6><?php echo htmlspecialchars($nombre_usuario); ?></h6>
                                <p><?php echo htmlspecialchars($rol_usuario); ?></p>
                            </div>
                            <li><a class="dropdown-item" href="config_perfil.php">
                                    <i class="bi bi-person-circle"></i> Mi perfil
                                </a></li>

                            <li><a class="dropdown-item" href="correos.php">
                                    <i class="bi bi-envelope"></i> Mensajes
                                    <span class="badge bg-primary float-end"><?php echo rand(0, 5); // Simulado, reemplazar con función real 
                                                                                ?></span>
                                </a></li>

                            <li><a class="dropdown-item" href="mis_citas.php">
                                    <i class="bi bi-calendar-check"></i> Mis citas
                                </a></li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li><a class="dropdown-item" href="config_perfil.php">
                                    <i class="bi bi-gear"></i> Configuración
                                </a></li>

                            <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-question-circle"></i> Ayuda
                                </a></li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li><a class="dropdown-item" href="../db/login-registro/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Enlaces para iniciar sesión/registrarse -->
                    <div class="d-flex">
                        <a href="../db/login-registro/login.php" class="btn btn-outline-light me-2">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                        </a>
                        <a href="../db/login-registro/registro.php" class="btn btn-light">
                            <i class="bi bi-person-plus"></i> Registrarse
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal para empujar el contenido debajo del navbar fijo -->
    <div class="content-wrapper">
        <!-- Aquí va el contenido específico de cada página -->
    </div>

    <script>
        // Cerrar el navbar collapse cuando se hace clic en un enlace (en móviles)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            const navbarCollapse = document.querySelector('.navbar-collapse');

            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });

            // Añadir efecto de desplazamiento en el navbar
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.custom-navbar');
                if (window.scrollY > 10) {
                    navbar.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.2)';
                    navbar.style.backdropFilter = 'blur(15px)';
                    navbar.style.background = 'rgba(63, 30, 95, 0.9)';
                } else {
                    navbar.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
                    navbar.style.backdropFilter = 'blur(10px)';
                    navbar.style.background = 'rgba(63, 30, 95, 0.85)';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>