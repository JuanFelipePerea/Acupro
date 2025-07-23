<?php
// CREATE ----------
function crearCita($conexion, $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $hora_inicio, $cod_estudiante = NULL, $num_acudiente = NULL)
{
    // Validamos los datos según el tipo
    if ($tipo == 'estudiante') {
        if (!$cod_estudiante) {
            $_SESSION['error'] = "Para citas de estudiantes, debe proporcionar un código de estudiante.";
            return false;
        }

        // Verificar que el estudiante existe
        $sql_check = "SELECT cod_estudiante FROM estudiantes WHERE cod_estudiante = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $cod_estudiante);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['error'] = "El código de estudiante proporcionado no existe.";
            return false;
        }
        $stmt_check->close();

        // Para citas de estudiantes, el num_acudiente debe ser NULL
        $num_acudiente = NULL;
    }

    if ($tipo == 'acudiente') {
        if (!$num_acudiente) {
            $_SESSION['error'] = "Para citas de acudientes, debe proporcionar un número de acudiente.";
            return false;
        }

        // Verificar que el acudiente existe
        $sql_check = "SELECT num_acudiente FROM acudientes WHERE num_acudiente = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $num_acudiente);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['error'] = "El número de acudiente proporcionado no existe.";
            return false;
        }
        $stmt_check->close();

        // Para citas de acudientes, verificamos si tiene un estudiante asociado
        $sql_student = "SELECT cod_estudiante FROM acudientes WHERE num_acudiente = ?";
        $stmt_student = $conexion->prepare($sql_student);
        $stmt_student->bind_param("i", $num_acudiente);
        $stmt_student->execute();
        $result = $stmt_student->get_result();
        $row = $result->fetch_assoc();

        // Si el acudiente tiene un estudiante asociado, lo asignamos a la cita
        if ($row && $row['cod_estudiante']) {
            $cod_estudiante = $row['cod_estudiante'];
        } else {
            $cod_estudiante = NULL;
        }
        $stmt_student->close();
    }

    $sql = "INSERT INTO citas (fecha, motivo, acc_docente, nom_docente, descripcion, tipo, hora_inicio, cod_estudiante, num_acudiente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        return false;
    }

    $stmt->bind_param("sssssssii", $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $hora_inicio, $cod_estudiante, $num_acudiente);

    if ($stmt->execute()) {
        $stmt->close();
        $_SESSION['mensaje'] = "Cita creada con éxito";
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $_SESSION['error'] = "Error al crear cita: " . $error;
        return false;
    }
}

// UPDATE ----------
function actualizarCita($conexion, $num_cita, $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $hora_inicio, $cod_estudiante = NULL, $num_acudiente = NULL)
{
    // Validamos los datos según el tipo
    if ($tipo == 'estudiante') {
        if (!$cod_estudiante) {
            $_SESSION['error'] = "Para citas de estudiantes, debe proporcionar un código de estudiante.";
            return false;
        }

        // Verificar que el estudiante existe
        $sql_check = "SELECT cod_estudiante FROM estudiantes WHERE cod_estudiante = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $cod_estudiante);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['error'] = "El código de estudiante proporcionado no existe.";
            return false;
        }
        $stmt_check->close();

        // Para citas de estudiantes, el num_acudiente debe ser NULL
        $num_acudiente = NULL;
    }

    if ($tipo == 'acudiente') {
        if (!$num_acudiente) {
            $_SESSION['error'] = "Para citas de acudientes, debe proporcionar un número de acudiente.";
            return false;
        }

        // Verificar que el acudiente existe
        $sql_check = "SELECT num_acudiente FROM acudientes WHERE num_acudiente = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $num_acudiente);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['error'] = "El número de acudiente proporcionado no existe.";
            return false;
        }
        $stmt_check->close();

        // Para citas de acudientes, verificamos si tiene un estudiante asociado
        $sql_student = "SELECT cod_estudiante FROM acudientes WHERE num_acudiente = ?";
        $stmt_student = $conexion->prepare($sql_student);
        $stmt_student->bind_param("i", $num_acudiente);
        $stmt_student->execute();
        $result = $stmt_student->get_result();
        $row = $result->fetch_assoc();

        // Si el acudiente tiene un estudiante asociado, lo asignamos a la cita
        if ($row && $row['cod_estudiante']) {
            $cod_estudiante = $row['cod_estudiante'];
        } else {
            $cod_estudiante = NULL;
        }
        $stmt_student->close();
    }

    $sql = "UPDATE citas SET fecha=?, motivo=?, acc_docente=?, nom_docente=?, descripcion=?, tipo=?, hora_inicio=?, cod_estudiante=?, num_acudiente=? WHERE num_cita=?";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        return false;
    }

    $stmt->bind_param("sssssssiis", $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $hora_inicio, $cod_estudiante, $num_acudiente, $num_cita);

    if ($stmt->execute()) {
        $stmt->close();
        $_SESSION['mensaje'] = "Cita actualizada con éxito";
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $_SESSION['error'] = "Error al actualizar cita: " . $error;
        return false;
    }
}

// DROP ----------
function eliminarCita($conexion, $num_cita)
{
    // Primero verificamos si existe la cita
    $sql_check = "SELECT num_cita FROM citas WHERE num_cita = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $num_cita);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "La cita no existe o ya ha sido eliminada.";
        return false;
    }
    $stmt_check->close();

    $sql = "DELETE FROM citas WHERE num_cita=?";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        return false;
    }

    $stmt->bind_param("i", $num_cita);

    if ($stmt->execute()) {
        $stmt->close();
        $_SESSION['mensaje'] = "Cita eliminada con éxito";
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $_SESSION['error'] = "Error al eliminar cita: " . $error;
        return false;
    }
}

// READ (just SELECT) ----------
function obtenerCitas($conexion)
{
    $sql = "SELECT c.*, 
            e.nombres, e.apellidos, e.n_grado,
            a.nom_acudiente, a.telefono, a.relacion
            FROM citas c
            LEFT JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
            LEFT JOIN acudientes a ON c.num_acudiente = a.num_acudiente
            ORDER BY c.fecha DESC";
    return $conexion->query($sql);
}

function obtenerCitaPorId($conexion, $num_cita)
{
    $sql = "SELECT * FROM citas WHERE num_cita = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $num_cita);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
}

function obtenerCitasEstudiantes($conexion)
{
    $sql = "SELECT c.*, e.nombres, e.apellidos, e.n_grado
            FROM citas c
            INNER JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
            WHERE c.tipo = 'estudiante'
            ORDER BY c.fecha DESC";
    return $conexion->query($sql);
}

function obtenerCitasAcudientes($conexion)
{
    $sql = "SELECT c.*, a.nom_acudiente, a.telefono, a.relacion, e.nombres as nombre_estudiante, e.apellidos as apellido_estudiante
            FROM citas c
            INNER JOIN acudientes a ON c.num_acudiente = a.num_acudiente
            LEFT JOIN estudiantes e ON a.cod_estudiante = e.cod_estudiante
            WHERE c.tipo = 'acudiente'
            ORDER BY c.fecha DESC";
    return $conexion->query($sql);
}

// Funciones auxiliares para obtener datos de estudiantes y acudientes
function obtenerEstudiantes($conexion)
{
    $sql = "SELECT cod_estudiante, CONCAT(nombres, ' ', apellidos) as nombre_completo, n_grado FROM estudiantes ORDER BY nombres";
    return $conexion->query($sql);
}

function obtenerAcudientes($conexion)
{
    $sql = "SELECT a.num_acudiente, a.nom_acudiente, a.telefono, a.relacion, a.cod_estudiante, 
            CONCAT(e.nombres, ' ', e.apellidos) as nombre_estudiante
            FROM acudientes a
            LEFT JOIN estudiantes e ON a.cod_estudiante = e.cod_estudiante
            ORDER BY a.nom_acudiente";
    return $conexion->query($sql);
}

// Obtener detalles de estudiante o acudiente según su ID
function obtenerDatosEstudiante($conexion, $cod_estudiante)
{
    $sql = "SELECT e.cod_estudiante, e.nombres, e.apellidos, e.edad, e.n_grado
            FROM estudiantes e
            WHERE e.cod_estudiante = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $cod_estudiante);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $stmt->close();
    return $datos;
}

function obtenerDatosAcudiente($conexion, $num_acudiente)
{
    $sql = "SELECT a.num_acudiente, a.nom_acudiente, a.telefono, a.relacion, a.cod_estudiante,
            CONCAT(e.nombres, ' ', e.apellidos) as nombre_estudiante
            FROM acudientes a
            LEFT JOIN estudiantes e ON a.cod_estudiante = e.cod_estudiante
            WHERE a.num_acudiente = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $num_acudiente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $stmt->close();
    return $datos;
}

// Función para eliminar citas asociadas a un estudiante
function eliminarCitasEstudiante($conexion, $cod_estudiante)
{
    // Debido a las restricciones de clave foránea con ON DELETE CASCADE,
    // no es necesario eliminar manualmente las citas al eliminar un estudiante
    // Sin embargo, mantenemos esta función por compatibilidad con el código existente

    // Verificamos si el estudiante existe
    $sql_check = "SELECT cod_estudiante FROM estudiantes WHERE cod_estudiante = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $cod_estudiante);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "El estudiante no existe.";
        return false;
    }
    $stmt_check->close();

    $sql = "DELETE FROM citas WHERE tipo = 'estudiante' AND cod_estudiante = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Error al preparar la consulta para eliminar citas del estudiante: " . $conexion->error;
        return false;
    }
    $stmt->bind_param("i", $cod_estudiante);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Citas del estudiante eliminadas correctamente.";
        $stmt->close();
        return true;
    } else {
        $_SESSION['error'] = "Error al eliminar citas del estudiante: " . $stmt->error;
        $stmt->close();
        return false;
    }
}

// Función para eliminar citas asociadas a un acudiente
function eliminarCitasAcudiente($conexion, $num_acudiente)
{
    // Debido a las restricciones de clave foránea con ON DELETE CASCADE,
    // no es necesario eliminar manualmente las citas al eliminar un acudiente
    // Sin embargo, mantenemos esta función por compatibilidad con el código existente

    // Verificamos si el acudiente existe
    $sql_check = "SELECT num_acudiente FROM acudientes WHERE num_acudiente = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $num_acudiente);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "El acudiente no existe.";
        return false;
    }
    $stmt_check->close();

    $sql = "DELETE FROM citas WHERE tipo = 'acudiente' AND num_acudiente = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        $_SESSION['error'] = "Error al preparar la consulta para eliminar citas del acudiente: " . $conexion->error;
        return false;
    }
    $stmt->bind_param("i", $num_acudiente);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Citas del acudiente eliminadas correctamente.";
        $stmt->close();
        return true;
    } else {
        $_SESSION['error'] = "Error al eliminar citas del acudiente: " . $stmt->error;
        $stmt->close();
        return false;
    }
}

// Función para verificar si un docente existe
function verificarDocente($conexion, $nom_docente)
{
    $sql = "SELECT nom_docente FROM docente WHERE nom_docente = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("s", $nom_docente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $existe = $resultado->num_rows > 0;
    $stmt->close();
    return $existe;
}

// Función para buscar citas por fecha
function buscarCitasPorFecha($conexion, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT c.*, 
            e.nombres, e.apellidos, e.n_grado,
            a.nom_acudiente, a.telefono, a.relacion
            FROM citas c
            LEFT JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
            LEFT JOIN acudientes a ON c.num_acudiente = a.num_acudiente
            WHERE c.fecha BETWEEN ? AND ?
            ORDER BY c.fecha ASC";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    return $stmt->get_result();
}

// Función para buscar citas por docente
function buscarCitasPorDocente($conexion, $nom_docente)
{
    $sql = "SELECT c.*, 
            e.nombres, e.apellidos, e.n_grado,
            a.nom_acudiente, a.telefono, a.relacion
            FROM citas c
            LEFT JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
            LEFT JOIN acudientes a ON c.num_acudiente = a.num_acudiente
            WHERE c.nom_docente = ?
            ORDER BY c.fecha DESC";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("s", $nom_docente);
    $stmt->execute();
    return $stmt->get_result();
}
