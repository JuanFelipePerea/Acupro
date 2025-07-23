<?php
session_start();

// Si ya hay una sesión iniciada, redirigir al index

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}


// Generar un ID único para el invitado

$guestName = $_POST['name'] ?? 'Invitado' . rand(1000, 9999);
$_SESSION["loggedin"] = true;
$_SESSION["guest"] = true;
$_SESSION["guest_name"] = $guestName;


// Definir la duración de la sesión (1 minuto)

$_SESSION['start_time'] = time();


// Redirigir al usuario al índice

header("location: ../index.php");
exit;