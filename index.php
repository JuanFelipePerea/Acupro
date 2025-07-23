<?php
// index.php - Punto de entrada principal del sistema
session_start();

// Obtener la ruta base del proyecto
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
if ($basePath === '/') {
    $basePath = '';
}

// Verificar si hay una ruta específica solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$route = str_replace($basePath, '', $requestUri);
$route = trim($route, '/');

// Si no hay ruta específica, redirigir según el estado de sesión
if (empty($route) || $route == 'index.php') {
    // Redirigir al dashboard o página de login
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
        header('Location: ' . $basePath . '/db/index.php');
        exit;
    } else {
        header('Location: ' . $basePath . '/public_perspective/index.php');
        exit;
    }
} else {
    // Delegar al router para rutas específicas
    include 'router.php';
}
?>