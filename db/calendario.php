<?php
ob_start();
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Si es un invitado, verificar el tiempo
    if (isset($_SESSION["guest"]) && $_SESSION["guest"] === true) {
        $time_elapsed = time() - $_SESSION["start_time"];

        // Si han pasado más de 5 minutos, redirigir al login
        if ($time_elapsed > 300) { // 5 minutos = 300 segundos (corregido de 5 a 300)
            session_destroy(); // Eliminar sesión de invitado
            header("location: ../db/login-registro/login.php");
            exit;
        }
    } else {
        header("location: ../db/login-registro/login.php");
        exit;
    }
}
require_once("conexion.php");
require_once("funcitas.php");
require_once("header.php"); // Incluir el header mejorado

// Set user name based on session type
$nombreUsuario = htmlspecialchars(
    isset($_SESSION["guest"]) && $_SESSION["guest"] === true
        ? $_SESSION["guest_name"]
        : $_SESSION["usuario"]
);

include_once("sidebar.php");

// Crear una instancia de la barra lateral
$sidebar = new Sidebar($conexion, $nombreUsuario);
$sidebar->render();


// Obtener mes y año actuales (o los seleccionados)
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('n'));
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));

// Nombres de los meses en español
$nombresMeses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

// Calcular mes anterior y siguiente
$mesAnterior = ($mes == 1) ? 12 : $mes - 1;
$anioAnterior = ($mes == 1) ? $anio - 1 : $anio;

$mesSiguiente = ($mes == 12) ? 1 : $mes + 1;
$anioSiguiente = ($mes == 12) ? $anio + 1 : $anio;

// Calcular días del mes y primer día de la semana
$primerDia = date('w', strtotime("$anio-$mes-01"));
$diasEnMes = date('t', strtotime("$anio-$mes-01"));

// Obtener todas las citas del mes actual
$sql = "SELECT * FROM citas WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? ORDER BY fecha ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $mes, $anio);
$stmt->execute();
$citas = $stmt->get_result();

// Organizar citas por día
$citasPorDia = [];
while ($cita = $citas->fetch_assoc()) {
    $dia = date('j', strtotime($cita['fecha']));
    if (!isset($citasPorDia[$dia])) {
        $citasPorDia[$dia] = [];
    }
    $citasPorDia[$dia][] = $cita;
}

// Procesar la creación de citas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear'])) {
    $fecha = $_POST['fecha'] . ' ' . $_POST['hora_inicio'] . ':00';
    $motivo = $_POST['motivo'];
    $acc_docente = $_POST['acc_docente'];
    $nom_docente = $_POST['nom_docente'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $cod_estudiante = $_POST['tipo'] == 'estudiante' ? ($_POST['cod_estudiante'] ?? null) : null;
    $num_acudiente = $_POST['tipo'] == 'acudiente' ? ($_POST['num_acudiente'] ?? null) : null;

    // Crear la cita
    crearCita($conexion, $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $_POST['hora_inicio'], $cod_estudiante, $num_acudiente);

    // Redireccionar al calendario
    $mesRedireccion = date('n', strtotime($fecha));
    $anioRedireccion = date('Y', strtotime($fecha));
    header("Location: calendario.php?mes=$mesRedireccion&anio=$anioRedireccion");
    exit;
}

// Eliminar cita
if (isset($_GET['delete'])) {
    $idCita = intval($_GET['delete']);
    eliminarCita($conexion, $idCita);
    header("Location: calendario.php?mes=$mes&anio=$anio");
    exit;
}

// Obtener detalles de una cita específica si se solicita
$citaSeleccionada = null;
if (isset($_GET['cita'])) {
    $idCita = intval($_GET['cita']);
    $sql = "SELECT c.*, e.nombres, e.apellidos, e.n_grado, 
           a.nom_acudiente, a.telefono, a.relacion
           FROM citas c
           LEFT JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
           LEFT JOIN acudientes a ON c.num_acudiente = a.num_acudiente
           WHERE c.num_cita = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idCita);
    $stmt->execute();
    $citaSeleccionada = $stmt->get_result()->fetch_assoc();
}

// Días de la semana en español
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// Fecha actual para resaltar el día de hoy
$fechaActual = date('Y-m-d');

// Obtener listas frescas de estudiantes y acudientes para los formularios
$estudiantes_list = obtenerEstudiantes($conexion);
$acudientes_list = obtenerAcudientes($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos del calendario */
        :root {
            --primary-color: #6f42c1;
            --primary-hover: #5a32a3;
            --secondary-color: #f8f9fa;
            --border-color: #dee2e6;
            --text-color: #495057;
            --light-text: #6c757d;
            --accent-color: #d1c4e9;
            --today-color: #e8f4ff;
            --has-events-color: #f8f8ff;
            --inactive-color: #adb5bd;
        }

        .calendar-container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .calendar-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .calendar-header a:hover {
            transform: scale(1.05);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-bottom: 1px solid var(--border-color);
        }

        .calendar-day-header {
            text-align: center;
            padding: 10px;
            font-weight: bold;
            background-color: var(--secondary-color);
            border-bottom: 1px solid var(--border-color);
        }

        .calendar-day {
            min-height: 100px;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 5px;
            position: relative;
            transition: all 0.2s ease;
        }

        .calendar-day:hover {
            background-color: var(--accent-color);
        }

        .calendar-day:nth-child(7n) {
            border-right: none;
        }

        .calendar-day-number {
            font-weight: bold;
            text-align: right;
            margin-bottom: 5px;
            font-size: 14px;
            color: var(--light-text);
        }

        .calendar-day.today {
            background-color: var(--today-color);
        }

        .calendar-day.has-events {
            background-color: var(--has-events-color);
        }

        .event-dot {
            height: 8px;
            width: 8px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .event-item {
            margin-bottom: 5px;
            font-size: 12px;
            padding: 3px 6px;
            border-radius: 4px;
            background-color: #e9ecef;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-color);
            transition: transform 0.2s ease;
        }

        .event-item:hover {
            background-color: var(--accent-color);
            transform: translateX(2px);
        }

        .event-item.estudiante {
            border-left: 3px solid var(--primary-color);
        }

        .event-item.acudiente {
            border-left: 3px solid #fd7e14;
        }

        .calendar-day.inactive {
            background-color: var(--secondary-color);
            color: var(--inactive-color);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .add-event-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-size: 12px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .add-event-btn:hover {
            transform: scale(1.2);
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="container mt-4 mb-4">
        <!-- Mensajes de éxito o error -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Contenedor del calendario -->
        <div class="calendar-container">
            <div class="calendar-header">
                <a href="?mes=<?php echo $mesAnterior; ?>&anio=<?php echo $anioAnterior; ?>" class="text-white">
                    <i class="fas fa-chevron-left"></i> <?php echo $nombresMeses[$mesAnterior]; ?>
                </a>
                <div><?php echo $nombresMeses[$mes] . " " . $anio; ?></div>
                <a href="?mes=<?php echo $mesSiguiente; ?>&anio=<?php echo $anioSiguiente; ?>" class="text-white">
                    <?php echo $nombresMeses[$mesSiguiente]; ?> <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="calendar-grid">
                <?php foreach ($diasSemana as $dia): ?>
                    <div class="calendar-day-header"><?php echo $dia; ?></div>
                <?php endforeach; ?>

                <?php
                // Ajustar el primer día de la semana (asumiendo que lunes es el día 0)
                $primerDia = ($primerDia == 0) ? 6 : $primerDia - 1;

                // Agregar días vacíos al principio
                for ($i = 0; $i < $primerDia; $i++) {
                    echo '<div class="calendar-day inactive"></div>';
                }

                // Agregar los días del mes
                for ($dia = 1; $dia <= $diasEnMes; $dia++) {
                    $fechaCalendario = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
                    $esHoy = ($fechaActual === $fechaCalendario) ? 'today' : '';
                    $tieneCitas = isset($citasPorDia[$dia]) ? 'has-events' : '';

                    echo '<div class="calendar-day ' . $esHoy . ' ' . $tieneCitas . '">';
                    echo '<div class="calendar-day-number">' . $dia . '</div>';

                    // Mostrar citas para este día
                    if (isset($citasPorDia[$dia])) {
                        foreach ($citasPorDia[$dia] as $cita) {
                            $citaId = intval($cita['num_cita']);
                            $citaMotivo = htmlspecialchars(substr($cita['motivo'], 0, 15)) .
                                (strlen($cita['motivo']) > 15 ? '...' : '');

                            echo '<div class="event-item ' . htmlspecialchars($cita['tipo']) . '" ' .
                                'onclick="window.location.href=\'calendario.php?mes=' . $mes .
                                '&anio=' . $anio . '&cita=' . $citaId . '\'">';
                            echo '<span class="event-dot"></span>' . $citaMotivo;
                            echo '</div>';
                        }
                    }

                    // Botón para agregar evento
                    echo '<div class="add-event-btn" onclick="agregarCita(\'' . $fechaCalendario . '\')">+</div>';
                    echo '</div>';
                }

                // Agregar días vacíos al final para completar la última semana
                $celdasRestantes = 7 - (($primerDia + $diasEnMes) % 7);
                if ($celdasRestantes < 7) {
                    for ($i = 0; $i < $celdasRestantes; $i++) {
                        echo '<div class="calendar-day inactive"></div>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between mt-3">
            <a href="read.php" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> Ver Lista de Citas
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearCitaModal">
                <i class="fas fa-plus"></i> Crear Nueva Cita
            </button>
        </div>
    </div>

    <!-- Modal para crear cita -->
    <div class="modal fade" id="crearCitaModal" tabindex="-1" aria-labelledby="crearCitaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearCitaModalLabel">Crear Nueva Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="calendario.php" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora_inicio" class="form-label">Hora</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <input type="text" class="form-control" id="motivo" name="motivo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="acc_docente" class="form-label">Identificación del Docente</label>
                            <input type="text" class="form-control" id="acc_docente" name="acc_docente" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom_docente" class="form-label">Nombre del Docente</label>
                            <input type="text" class="form-control" id="nom_docente" name="nom_docente" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Cita</label>
                            <select class="form-select" id="tipo" name="tipo" required onchange="mostrarCamposTipo()">
                                <option value="estudiante">Estudiante</option>
                                <option value="acudiente">Acudiente</option>
                            </select>
                        </div>

                        <!-- Campos específicos para estudiantes -->
                        <div id="estudiante-crear" class="mb-3">
                            <label for="cod_estudiante" class="form-label">Estudiante</label>
                            <select class="form-select" id="cod_estudiante" name="cod_estudiante" required>
                                <option value="">Seleccionar estudiante...</option>
                                <?php
                                // Verifica que hay estudiantes antes de intentar iterar
                                if ($estudiantes_list && $estudiantes_list->num_rows > 0) {
                                    // Resetea el puntero por si acaso
                                    mysqli_data_seek($estudiantes_list, 0);
                                    while ($est = $estudiantes_list->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($est['cod_estudiante']) . "'>" .
                                            htmlspecialchars($est['nombre_completo']) . "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay estudiantes disponibles</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Campos específicos para acudientes -->
                        <div id="acudiente-crear" class="mb-3" style="display: none;">
                            <label for="num_acudiente" class="form-label">Acudiente</label>
                            <select class="form-select" id="num_acudiente" name="num_acudiente">
                                <option value="">Seleccionar acudiente...</option>
                                <?php
                                // Verifica que hay acudientes antes de intentar iterar
                                if ($acudientes_list && $acudientes_list->num_rows > 0) {
                                    // Resetea el puntero por si acaso
                                    mysqli_data_seek($acudientes_list, 0);
                                    while ($acu = $acudientes_list->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($acu['num_acudiente']) . "'>" .
                                            htmlspecialchars($acu['nom_acudiente']) . "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No hay acudientes disponibles</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="crear" class="btn btn-primary">Crear Cita</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles de cita -->
    <?php if ($citaSeleccionada): ?>
        <div class="modal fade" id="verCitaModal" tabindex="-1" aria-labelledby="verCitaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verCitaModalLabel">Detalles de la Cita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5><?php echo htmlspecialchars($citaSeleccionada['motivo']); ?></h5>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($citaSeleccionada['fecha'])); ?></p>
                        <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($citaSeleccionada['fecha'])); ?></p>
                        <p><strong>Nombre docente:</strong> <?php echo htmlspecialchars($citaSeleccionada['nom_docente']); ?></p>
                        <p><strong>Identificación docente:</strong> <?php echo htmlspecialchars($citaSeleccionada['acc_docente']); ?></p>
                        <p><strong>Tipo:</strong> <?php echo ucfirst(htmlspecialchars($citaSeleccionada['tipo'])); ?></p>

                        <?php if ($citaSeleccionada['tipo'] == 'estudiante' && isset($citaSeleccionada['nombres'])): ?>
                            <p><strong>Estudiante:</strong> <?php echo htmlspecialchars($citaSeleccionada['nombres'] . ' ' . $citaSeleccionada['apellidos']); ?></p>
                            <?php if (isset($citaSeleccionada['n_grado'])): ?>
                                <p><strong>Grado:</strong> <?php echo htmlspecialchars($citaSeleccionada['n_grado']); ?></p>
                            <?php endif; ?>
                        <?php elseif ($citaSeleccionada['tipo'] == 'acudiente' && isset($citaSeleccionada['nom_acudiente'])): ?>
                            <p><strong>Acudiente:</strong> <?php echo htmlspecialchars($citaSeleccionada['nom_acudiente']); ?></p>
                            <?php if (isset($citaSeleccionada['telefono'])): ?>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($citaSeleccionada['telefono']); ?></p>
                            <?php endif; ?>
                            <?php if (isset($citaSeleccionada['relacion'])): ?>
                                <p><strong>Relación:</strong> <?php echo htmlspecialchars($citaSeleccionada['relacion']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="mb-3">
                            <strong>Descripción:</strong>
                            <p class="mt-2 p-2 bg-light rounded"><?php echo nl2br(htmlspecialchars($citaSeleccionada['descripcion'])); ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="?mes=<?php echo $mes; ?>&anio=<?php echo $anio; ?>&delete=<?php echo $citaSeleccionada['num_cita']; ?>"
                            class="btn btn-danger"
                            onclick="return confirm('¿Estás seguro de eliminar esta cita?');">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <a href="editar_cita.php?id=<?php echo $citaSeleccionada['num_cita']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Scripts JavaScript -->
    <script>
        // Función para pre-rellenar la fecha en el modal de crear cita
        function agregarCita(fecha) {
            document.getElementById('fecha').value = fecha;
            const modal = new bootstrap.Modal(document.getElementById('crearCitaModal'));
            modal.show();
        }

        // Mostrar automáticamente el modal de detalles si hay una cita seleccionada
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($citaSeleccionada): ?>
                const modal = new bootstrap.Modal(document.getElementById('verCitaModal'));
                modal.show();
            <?php endif; ?>

            // Inicializar mostrarCamposTipo para el formulario de crear
            mostrarCamposTipo();
        });

        // Función para mostrar/ocultar campos según el tipo de cita
        function mostrarCamposTipo() {
            const tipo = document.getElementById('tipo').value;
            document.getElementById('estudiante-crear').style.display = tipo === 'estudiante' ? 'block' : 'none';
            document.getElementById('acudiente-crear').style.display = tipo === 'acudiente' ? 'block' : 'none';
            
            // Ajustar validación required según el tipo seleccionado
            if (tipo === 'estudiante') {
                document.getElementById('cod_estudiante').setAttribute('required', '');
                document.getElementById('num_acudiente').removeAttribute('required');
            } else {
                document.getElementById('num_acudiente').setAttribute('required', '');
                document.getElementById('cod_estudiante').removeAttribute('required');
            }
        }
    </script>

  
<!-- Asegurarse de que se carguen los scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
            crossorigin="anonymous"></script>
</body>
</html>

<?php
// Cerrar conexión a la base de datos
$conexion->close();
?>ts de