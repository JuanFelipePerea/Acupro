<?php

$conexion = mysqli_connect("localhost", "root", "", "acupro");

if (!$conexion) {
    echo "<script>alert('D:');</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/estilos.css">
</head>

</html>