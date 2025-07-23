<?php
require('../conexion.php');

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, ingrese su email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "El formato del email no es válido.";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id_usuario, usuario, password FROM usuarios WHERE email = ?";

        if ($stmt = mysqli_prepare($conexion, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id_usuario, $usuario, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id_usuario"] = $id_usuario;
                            $_SESSION["email"] = $email;
                            $_SESSION["usuario"] = $usuario;

                            header("location: ../index.php");
                            exit;
                        } else {
                            $password_err = "La contraseña es incorrecta *";
                        }
                    }
                } else {
                    $email_err = "No se encontró ninguna cuenta con ese email *";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    if (!empty($email_err) || !empty($password_err)) {
        $_SESSION["login_error"] = $email_err . " " . $password_err;
        header("location: ../login-registro/login_errors.php");
        exit;
    }
}
?>