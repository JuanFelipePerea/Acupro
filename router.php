<?php
// router.php - Enrutador principal del sistema
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

// Obtener la ruta base del proyecto
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
if ($basePath === '/') {
    $basePath = '';
}

// Obtener la ruta solicitada
$route = isset($_GET['route']) ? $_GET['route'] : '';
$route = trim($route, '/');

// Si la ruta está vacía, redirigir al index
if (empty($route)) {
    header('Location: ' . $basePath . '/');
    exit;
}

// Parsear la ruta
$segments = explode('/', $route);
$controller = $segments[0];
$action = isset($segments[1]) ? $segments[1] : 'index';

// Función para verificar autenticación
function requireAuth() {
    global $basePath;
    if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
        header('Location: ' . $basePath . '/public_perspective/index.php');
        exit;
    }
}

// Función para redirigir de forma segura
function redirectTo($file, $permanent = false) {
    global $basePath;
    if (file_exists($file)) {
        $statusCode = $permanent ? 301 : 302;
        http_response_code($statusCode);
        header("Location: " . $basePath . "/$file");
        exit;
    }
    return false;
}

// Enrutamiento principal
switch ($controller) {
    case 'login':
    case 'auth':
        redirectTo('login-registro/index.html');
        break;
        
    case 'registro':
        redirectTo('db/registro.php');
        break;
        
    case 'dashboard':
        requireAuth();
        redirectTo('db/index.php');
        break;
        
    case 'estudiantes':
        requireAuth();
        if ($action === 'perfil' && isset($segments[2])) {
            // URL como: /estudiantes/perfil/123
            header("Location: " . $basePath . "/modales_perfil.php?id=" . urlencode($segments[2]));
            exit;
        }
        redirectTo('db/estudiantes.php');
        break;
        
    case 'calendario':
        requireAuth();
        redirectTo('db/calendario.php');
        break;
        
    case 'historial':
        requireAuth();
        redirectTo('db/historial.php');
        break;
        
    case 'footer':
        redirectTo('db/footer.php');
        break;
        
    case 'header':
        redirectTo('db/header.php');
        break;
        
    case 'read':
        requireAuth();
        redirectTo('db/read.php');
        break;
        
    case 'correos':
        requireAuth();
        redirectTo('db/correos.php');
        break;

    case 'logout':
        session_destroy();
        header('Location: ' . $basePath . 'db/login-registro/logout.php');
        exit;
        break;
        
    default:
        // Intentar encontrar un archivo con el nombre del controlador
        $possibleFiles = [
            $controller . '.php',
            $controller . '.html'
        ];
        
        $found = false;
        foreach ($possibleFiles as $file) {
            if (file_exists($file)) {
                header("Location: " . $basePath . "/$file");
                exit;
            }
        }
        
        if (!$found) {
            // Redirigir al archivo 404.php en la carpeta db
            http_response_code(404);
            if (file_exists('db/404.php')) {
                header('Location: ' . $basePath . '/db/404.php');
                exit;
            } else {
                // Fallback si no existe el archivo 404.php
                echo '<!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Página no encontrada - ACUPRO</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; margin: 50px; }
                        .error-container { max-width: 600px; margin: 0 auto; }
                        h1 { color: #e74c3c; }
                        a { color: #3498db; text-decoration: none; }
                        a:hover { text-decoration: underline; }
                    </style>
                </head>
                <body>
                    <div class="error-container">
                        <h1>404 - Página no encontrada</h1>
                        <p>La página que buscas no existe o ha sido movida.</p>
                        <p><a href="' . $basePath . '/">Volver al inicio</a></p>
                    </div>
                </body>
                </html>';
            }
        }
        break;
}
?>