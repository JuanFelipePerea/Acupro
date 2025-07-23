<?php
session_start();

// Función para verificar si la sesión es válida
function verificarSesion() {
    // Si la sesión está iniciada correctamente
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        
        // Si es un invitado, verificar si su tiempo ha expirado
        if (isset($_SESSION["guest"]) && $_SESSION["guest"] === true) {
            $tiempo_transcurrido = time() - $_SESSION["start_time"];
            
            if ($tiempo_transcurrido > 5) { // 1 minuto (60 segundos)
                session_unset();    // Limpiar todos los datos de la sesión
                session_destroy();  // Destruir la sesión
                header("location: ../db/login-registro/login.php");
                exit;
            }
        }
    } else {
        // Si no está logueado, redirigir al login
        header("location: ../db/login-registro/login.php");
        exit;
    }
}