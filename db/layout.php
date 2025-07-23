<?php

$conexion = mysqli_connect("localhost", "root", "", "acupro");

if (!$conexion) {
    echo "<script>alert('Conexion Erronea');</script>";
    exit;
}

include_once ("header.php");

$nombreUsuario = htmlspecialchars(
    isset($_SESSION["guest"]) && $_SESSION["guest"] === true
        ? $_SESSION["guest_name"]
        : (is_array($_SESSION["usuario"]) ? $_SESSION["usuario"][0] : $_SESSION["usuario"])
);

include_once("sidebar.php");

// Crear una instancia de la barra lateral
$sidebar = new Sidebar($conexion, $nombreUsuario);
$sidebar->render();
