<?php
session_start();

// Incluir el formulario de inicio de sesión
include('login.php');

// Mostrar mensajes de error si existen
if (isset($_SESSION["login_error"])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let loginError = '" . $_SESSION["login_error"] . "';
                if (loginError.includes('email')) {
                    document.getElementById('email-error').className = 'text-danger';
                    document.getElementById('email-error').textContent = loginError;
                    document.getElementById('e-icon').className = 'fas fa-exclamation-triangle text-danger';
                    document.getElementById('email-error').style.display = 'block'; // Mostrar mensaje de error
                }
                if (loginError.includes('contraseña')) {
                    document.getElementById('password-error').className = 'text-danger';
                    document.getElementById('password-error').textContent = loginError;
                    document.getElementById('p-icon').className = 'fas fa-exclamation-triangle text-danger';
                    document.getElementById('password-error').style.display = 'block'; // Mostrar mensaje de error
                }
            });
          </script>";
    unset($_SESSION["login_error"]); // Limpiar el error de sesión
}
?>