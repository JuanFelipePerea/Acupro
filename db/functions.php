<?php


// Conexión a la base de datos
function conectarDB()
{
    $host = "localhost";
    $usuario = "root";
    $password = "";
    $bd = "acupro";

    $conexion = new mysqli($host, $usuario, $password, $bd);

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Establecer charset
    $conexion->set_charset("utf8mb4");

    return $conexion;
}

// Obtener información del usuario actual
function obtenerInfoUsuario($id_usuario)
{
    $conexion = conectarDB();
    $id_usuario = $conexion->real_escape_string($id_usuario);

    $sql = "SELECT usuario, email FROM usuarios WHERE id_usuario = '$id_usuario'";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $conexion->close();
        return $usuario;
    }

    $conexion->close();
    return null;
}

// Contar notificaciones nuevas (citas recientes)
function contarNotificacionesNuevas()
{
    $conexion = conectarDB();

    // Obtener citas de los últimos 2 días
    $sql = "SELECT COUNT(*) as total FROM citas WHERE fecha >= NOW() - INTERVAL 2 DAY";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $conexion->close();
        return $fila['total'];
    }

    $conexion->close();
    return 0;
}

// Obtener las últimas notificaciones (citas y mensajes recientes)
function obtenerNotificaciones($limite = 4)
{
    $conexion = conectarDB();
    $limite = (int)$limite;

    // Obtener las últimas citas
    $sql = "SELECT num_cita, motivo, fecha, tipo, NOW() as tiempo_actual FROM citas 
            ORDER BY fecha DESC LIMIT $limite";
    $resultado = $conexion->query($sql);

    $notificaciones = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($cita = $resultado->fetch_assoc()) {
            // Calcular tiempo transcurrido
            $fecha_cita = strtotime($cita['fecha']);
            $fecha_actual = strtotime($cita['tiempo_actual']);
            $diferencia = $fecha_actual - $fecha_cita;

            if ($diferencia < 3600) {
                $tiempo = round($diferencia / 60) . "m"; // Minutos
            } elseif ($diferencia < 86400) {
                $tiempo = round($diferencia / 3600) . "h"; // Horas
            } else {
                $tiempo = round($diferencia / 86400) . "d"; // Días
            }

            // Es una cita futura
            if ($fecha_cita > $fecha_actual) {
                $tipo = "programada";
                $icono = "bi-calendar-event";
                $titulo = "Cita programada";
                if ($cita['tipo'] == 'estudiante') {
                    $mensaje = "Nueva cita de estudiante para el " . date("d/m/Y", $fecha_cita) . " a las " . date("H:i", $fecha_cita);
                } else {
                    $mensaje = "Nueva cita de acudiente para el " . date("d/m/Y", $fecha_cita) . " a las " . date("H:i", $fecha_cita);
                }
            } else {
                $tipo = "realizada";
                $icono = "bi-check-circle";
                $titulo = "Cita realizada";
                $mensaje = "Cita de " . $cita['tipo'] . " completada con motivo: " . $cita['motivo'];
            }

            $notificaciones[] = [
                'id' => $cita['num_cita'],
                'tipo' => $tipo,
                'icono' => $icono,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tiempo' => "Hace " . $tiempo,
                'leida' => false
            ];
        }
    }

    $conexion->close();
    return $notificaciones;
}

// Obtener el número total de estudiantes
function contarEstudiantes()
{
    $conexion = conectarDB();

    $sql = "SELECT COUNT(*) as total FROM estudiantes";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $conexion->close();
        return $fila['total'];
    }

    $conexion->close();
    return 0;
}

// Obtener el número de citas pendientes
function contarCitasPendientes()
{
    $conexion = conectarDB();

    $sql = "SELECT COUNT(*) as total FROM citas WHERE fecha >= NOW()";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $conexion->close();
        return $fila['total'];
    }

    $conexion->close();
    return 0;
}

// Generar el HTML para las notificaciones
function generarHTMLNotificaciones()
{
    $notificaciones = obtenerNotificaciones();
    $contador = count($notificaciones);
    $html = '';

    foreach ($notificaciones as $notificacion) {
        $clase_leida = $notificacion['leida'] ? '' : 'unread';

        $html .= '<div class="notification-item ' . $clase_leida . ' d-flex p-3">
                    <div class="notification-icon me-3">
                        <i class="bi ' . $notificacion['icono'] . '"></i>
                    </div>
                    <div class="notification-content">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">' . $notificacion['titulo'] . '</span>
                            <span class="notification-time">' . $notificacion['tiempo'] . '</span>
                        </div>
                        <p class="mb-0 text-muted">' . $notificacion['mensaje'] . '</p>
                    </div>
                </div>';
    }

    return [
        'contador' => $contador,
        'html' => $html
    ];
}

// Verificar si el usuario está autenticado
function estaAutenticado()
{
    // Iniciar sesión si no está activa
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si es un usuario normal autenticado
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        return true;
    }

    // Verificar si es un invitado con tiempo válido
    if (isset($_SESSION["guest"]) && $_SESSION["guest"] === true) {
        $tiempo_transcurrido = time() - $_SESSION["start_time"];

        // Si el tiempo de sesión de invitado es válido (menos de 5 minutos)
        if ($tiempo_transcurrido <= 300) { // 300 segundos = 5 minutos
            return true;
        } else {
            // Tiempo expirado, eliminar sesión de invitado
            session_destroy();
            return false;
        }
    }

    // No está autenticado (ni como usuario ni como invitado válido)
    return false;
}


/**
 * Obtener el nombre de usuario de la sesión actual
 * @return string Nombre del usuario o 'Usuario' si no hay sesión
 */
function obtenerNombreUsuario()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['id_usuario'])) {
        $conexion = conectarDB();

        if (!$conexion) {
            return 'Usuario';
        }

        // Preparar consulta
        $stmt = $conexion->prepare("SELECT usuario FROM usuarios WHERE id_usuario = ?");

        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $conexion->error);
            $conexion->close();
            return 'Usuario';
        }

        // Vincular parámetros
        $stmt->bind_param("i", $_SESSION['id_usuario']);

        // Ejecutar la consulta
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            $stmt->close();
            $conexion->close();
            return 'Usuario';
        }

        // Obtener el resultado
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $stmt->close();
            $conexion->close();
            return 'Usuario';
        }

        // Obtener el nombre de usuario
        $fila = $resultado->fetch_assoc();
        $nombreUsuario = $fila['usuario'];

        $stmt->close();
        $conexion->close();

        return $nombreUsuario;
    }

    return 'Usuario';
}

/**
 * Determinar el rol del usuario actual
 * @return string Rol del usuario ('Administrador', 'Docente', 'Secretario', 'Profesional' o 'Invitado')
 */
function obtenerRolUsuario()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id_usuario'])) {
        return 'Invitado';
    }

    $conexion = conectarDB();

    if (!$conexion) {
        return 'Invitado';
    }

    $id_usuario = $_SESSION['id_usuario'];
    $email = '';

    // Primero obtenemos el email del usuario
    $stmt = $conexion->prepare("SELECT email FROM usuarios WHERE id_usuario = ?");

    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $conexion->error);
        $conexion->close();
        return 'Invitado';
    }

    $stmt->bind_param("i", $id_usuario);

    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta: " . $stmt->error);
        $stmt->close();
        $conexion->close();
        return 'Invitado';
    }

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $email = $fila['email'];
    }

    $stmt->close();
}
