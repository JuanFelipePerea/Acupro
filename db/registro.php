<?php
ob_start();
require('conexion.php');
include('header.php');

// Inicializar variables
$username = $email = $password = "";
$username_err = $email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar nombre de usuario
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor ingrese un nombre de usuario.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validar email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese un email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Por favor ingrese un formato de email válido.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validar contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese una contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Si no hay errores, proceder con el registro
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        // Verificar si el usuario o email ya existen
        $sql_check = "SELECT id_usuario FROM usuarios WHERE usuario = ? OR email = ?";
        
        if ($stmt_check = mysqli_prepare($conexion, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
            
            if (mysqli_stmt_execute($stmt_check)) {
                mysqli_stmt_store_result($stmt_check);
                
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    // Determinar cuál ya existe (usuario o email)
                    $check_user = "SELECT id_usuario FROM usuarios WHERE usuario = ?";
                    $stmt_user = mysqli_prepare($conexion, $check_user);
                    mysqli_stmt_bind_param($stmt_user, "s", $username);
                    mysqli_stmt_execute($stmt_user);
                    mysqli_stmt_store_result($stmt_user);
                    
                    if (mysqli_stmt_num_rows($stmt_user) > 0) {
                        $username_err = "Este nombre de usuario ya está en uso.";
                    } else {
                        $email_err = "Este correo electrónico ya está registrado.";
                    }
                    
                    mysqli_stmt_close($stmt_user);
                } else {
                    // Insertar los datos en la base de datos
                    $sql_insert = "INSERT INTO usuarios (usuario, email, password) VALUES (?, ?, ?)";
                    
                    if ($stmt_insert = mysqli_prepare($conexion, $sql_insert)) {
                        // Crear un hash más seguro de la contraseña
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        mysqli_stmt_bind_param($stmt_insert, "sss", $username, $email, $hashed_password);
                        
                        if (mysqli_stmt_execute($stmt_insert)) {
                            // Registro exitoso
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    alert('Registro exitoso.');
                                    window.location.href = 'registro.php';
                                });
                            </script>";
                        } else {
                            error_log("Error al insertar usuario: " . mysqli_error($conexion));
                            echo "<script>alert('Ocurrió un error al registrar el usuario.');</script>";
                        }
                        
                        mysqli_stmt_close($stmt_insert);
                    } else {
                        error_log("Error en la preparación de la consulta: " . mysqli_error($conexion));
                        echo "<script>alert('Ocurrió un error al registrar el usuario.');</script>";
                    }
                }
            } else {
                error_log("Error en la ejecución de la consulta check: " . mysqli_error($conexion));
                echo "<script>alert('Ocurrió un error al verificar el usuario.');</script>";
            }
            
            mysqli_stmt_close($stmt_check);
        } else {
            error_log("Error en la preparación de la consulta check: " . mysqli_error($conexion));
            echo "<script>alert('Ocurrió un error en el sistema.');</script>";
        }
    }
}

// Obtener la lista de usuarios registrados con manejo de errores
$usuarios_registrados = array();
$sql_usuarios = "SELECT usuario, email FROM usuarios ORDER BY id_usuario DESC LIMIT 10";
$resultado = mysqli_query($conexion, $sql_usuarios);

if ($resultado) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $usuarios_registrados[] = $fila;
    }
    mysqli_free_result($resultado);
} else {
    error_log("Error al obtener usuarios: " . mysqli_error($conexion));
}

// Contar el total de usuarios
$total_usuarios = 0;
$sql_count = "SELECT COUNT(*) as total FROM usuarios";
$result_count = mysqli_query($conexion, $sql_count);

if ($result_count) {
    $row_count = mysqli_fetch_assoc($result_count);
    $total_usuarios = $row_count['total'];
    mysqli_free_result($result_count);
} else {
    error_log("Error al contar usuarios: " . mysqli_error($conexion));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAFSU5R8my9zMWr2LkXkdFGR4i5vZFhT6vLs6zZL/jqKwCKZxSi5TEqMs77+G" crossorigin="anonymous">
    <style>
        /* Estilos personalizados */
        body {
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container_all {
            display: flex;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .ctn-from {
            width: 65%;
            background: url('img/flower-bg.jpg') no-repeat;
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            position: relative;
        }

        .ctn-from::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.3));
            z-index: 1;
        }

        .form-content {
            position: relative;
            z-index: 2;
            max-width: 500px;
            margin-left: 50px;
        }

        .title {
            color: #2d2160;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 30px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #2d2160;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .msg-error {
            color: red;
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }

        .btn-primary {
            background-color: #2d2160;
            border-color: #2d2160;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #211851;
            border-color: #211851;
        }

        .text-footer {
            margin-top: 20px;
            display: block;
            color: #2d2160;
            font-size: 15px;
        }

        .text-footer a {
            color: #2d2160;
            font-weight: 600;
            text-decoration: none;
        }

        .text-footer a:hover {
            text-decoration: underline;
        }

        .aside {
            width: 35%;
            background-color: #4a2c91;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            color: white;
            overflow-y: auto;
        }

        .aside h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .total-usuarios {
            font-size: 18px;
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
        }

        .usuario-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .usuario-card:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .usuario-nombre {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .usuario-email {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }

        /* Añadir estilos responsivos para pantallas pequeñas */
        @media (max-width: 992px) {
            .container_all {
                flex-direction: column;
            }
            .ctn-from, .aside {
                width: 100%;
            }
            .form-content {
                margin-left: 0;
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    <div class="container_all">
        <div class="ctn-from">
            <div class="form-content">
                <h1 class="title">Registra un<br>nuevo usuario</h1>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                    <div class="form-group">
                        <label for="username">Nombre de usuario</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" class="<?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>">
                        <span class="msg-error"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>">
                        <span class="msg-error"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" class="<?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="msg-error"><?php echo $password_err; ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary">Registrarse</button>
                    
                    <span class="text-footer">¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></span>
                </form>
            </div>
        </div>

        <div class="aside">
            <h2>CUENTAS CREADAS:</h2>
            <div class="total-usuarios">
                Total de usuarios registrados: <strong><?php echo $total_usuarios; ?></strong>
            </div>

            <?php if (!empty($usuarios_registrados)): ?>
                <?php foreach ($usuarios_registrados as $usuario): ?>
                    <div class="usuario-card">
                        <div class="usuario-nombre"><?php echo htmlspecialchars($usuario['usuario']); ?></div>
                        <div class="usuario-email"><?php echo htmlspecialchars($usuario['email']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay usuarios registrados aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBTtJXwN59RDjdj/NZTRci5bJW+WnTMYX9EJFxiHVrfGJS6H" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyniUwwAZgrzD06kc/tftFf19TOAs2zVinnD/C7E91j9yyk5//jjpt/" crossorigin="anonymous"></script>
</body>

</html>
<?php
// Cerrar conexión a la base de datos
mysqli_close($conexion);
ob_end_flush();
?>