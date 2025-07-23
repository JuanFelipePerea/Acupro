<?php
ob_start();
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../db/login-registro/login.php");
    exit;
}
require("conexion.php");
require("header.php");
require("funcitas.php");

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


// Handle delete
if (isset($_GET['delete'])) {
    eliminarCita($conexion, $_GET['delete']);
    header("Location: read.php");
    exit;
}

// Handle create
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear'])) {
    $fecha = $_POST['fecha'] . ' ' . $_POST['hora_inicio'] . ':00';
    $params = [
        'fecha' => $fecha,
        'motivo' => $_POST['motivo'],
        'acc_docente' => $_POST['acc_docente'],
        'nom_docente' => $_POST['nom_docente'],
        'descripcion' => $_POST['descripcion'],
        'tipo' => $_POST['tipo'],
        'hora_inicio' => $_POST['hora_inicio'],
        'cod_estudiante' => $_POST['tipo'] == 'estudiante' ? ($_POST['cod_estudiante'] ?? null) : null,
        'num_acudiente' => $_POST['tipo'] == 'acudiente' ? ($_POST['num_acudiente'] ?? null) : null
    ];
    crearCita($conexion, ...array_values($params));
    header("Location: read.php");
    exit;
}

// Handle edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar'])) {
    $params = [
        'num_cita' => $_POST['num_cita'],
        'fecha' => $_POST['fecha'] . ' ' . $_POST['hora_inicio'] . ':00',
        'motivo' => $_POST['motivo'],
        'acc_docente' => $_POST['acc_docente'],
        'nom_docente' => $_POST['nom_docente'],
        'descripcion' => $_POST['descripcion'],
        'tipo' => $_POST['tipo'],
        'hora_inicio' => $_POST['hora_inicio'],
        'cod_estudiante' => $_POST['tipo'] == 'estudiante' ? ($_POST['cod_estudiante'] ?? null) : null,
        'num_acudiente' => $_POST['tipo'] == 'acudiente' ? ($_POST['num_acudiente'] ?? null) : null
    ];
    actualizarCita($conexion, ...array_values($params));
    header("Location: read.php");
    exit;
}

// Fetch data
$estudiantes = obtenerEstudiantes($conexion);
$acudientes = obtenerAcudientes($conexion);

/**
 * Función modificada para obtener citas activas usando funciones de funcitas.php
 */
function obtenerCitasActivasPorTipo($conexion, $tipo = null)
{
    $fechaActual = date("Y-m-d");

    $sql = "SELECT c.*, e.nombres, e.apellidos, e.n_grado, 
           a.nom_acudiente, a.telefono, a.relacion, 
           DATE(c.fecha) AS fecha_only, TIME(c.fecha) AS hora_only
           FROM citas c
           LEFT JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante
           LEFT JOIN acudientes a ON c.num_acudiente = a.num_acudiente
           WHERE DATE(c.fecha) >= ?";

    if ($tipo) {
        $sql .= " AND c.tipo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $fechaActual, $tipo);
    } else {
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $fechaActual);
    }

    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Citas Activas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="read.css">
</head>

<body>
    <main class="container mt-4">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensaje'];
                                                unset($_SESSION['mensaje']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="read.php" class="input-group">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar citas...">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <h2>Lista de Citas Activas</h2>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearCitaModal"><i class="fas fa-plus"></i> Crear Cita</button>
                <a href="historial.php" class="btn btn-outline-secondary ms-2"><i class="fas fa-history"></i> Historial</a>
                <a href="calendario.php" class="btn btn-outline-primary ms-2"><i class="fas fa-calendar"></i> Calendario</a>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><button class="nav-link active text-dark" data-bs-toggle="tab" data-bs-target="#estudiantes">Estudiantes</button></li>
            <li class="nav-item"><button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#acudientes">Acudientes</button></li>
        </ul>

        <div class="tab-content">
            <?php
            $tabs = ['estudiantes' => 'estudiante', 'acudientes' => 'acudiente'];
            foreach ($tabs as $tab => $tipo) {
                echo "<div class='tab-pane fade " . ($tab == 'estudiantes' ? 'show active' : '') . "' id='$tab'>";

                // Usamos la función modificada para obtener citas activas
                $result = obtenerCitasActivasPorTipo($conexion, $tipo);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $isEstudiante = $tipo == 'estudiante';
            ?>
                        <div class="student-card">
                            <div class="avatar"><i class="fas fa-<?php echo $isEstudiante ? 'user' : 'user-friends'; ?>"></i></div>
                            <div class="details">
                                <h4><?php echo htmlspecialchars($row['nom_docente']); ?></h4>
                                <?php if ($isEstudiante && isset($row['nombres']) && isset($row['apellidos'])): ?>
                                    <p><strong>Estudiante:</strong> <?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?></p>
                                    <?php if (isset($row['n_grado'])): ?>
                                        <p><strong>Grado:</strong> <?php echo htmlspecialchars($row['n_grado']); ?></p>
                                    <?php endif; ?>
                                <?php elseif (!$isEstudiante && isset($row['nom_acudiente'])): ?>
                                    <p><strong>Acudiente:</strong> <?php echo htmlspecialchars($row['nom_acudiente']); ?></p>
                                    <?php if (isset($row['telefono'])): ?>
                                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($row['telefono']); ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($row['relacion'])): ?>
                                        <p><strong>Relación:</strong> <?php echo htmlspecialchars($row['relacion']); ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <p>Documento del Docente: <?php echo htmlspecialchars($row['acc_docente']); ?></p>
                                <p class="<?php echo $row['fecha_only'] == date("Y-m-d") ? 'fecha-hoy' : 'fecha-proxima'; ?>">
                                    Fecha: <?php echo htmlspecialchars($row['fecha_only']) . ($row['fecha_only'] == date("Y-m-d") ? ' (HOY)' : ''); ?>
                                </p>
                                <p>Hora: <?php echo htmlspecialchars($row['hora_only']); ?></p>
                                <p>Motivo: <?php echo htmlspecialchars($row['motivo']); ?></p>
                                <p>Descripción: <?php echo substr(htmlspecialchars($row['descripcion']), 0, 50); ?>...</p>
                            </div>
                            <div class="actions">
                                <button class="btn" style="background-color: #6f42c1; color: #f1f1f1;" data-bs-toggle="modal" data-bs-target="#verCitaModal<?php echo $row['num_cita']; ?>"><i class="fas fa-eye"></i> Ver</button>
                                <button class="btn" style="background-color: #6f42c1; color: #f1f1f1;" data-bs-toggle="modal" data-bs-target="#editarCitaModal<?php echo $row['num_cita']; ?>"><i class="fas fa-edit"></i> Editar</button>
                                <a href="read.php?delete=<?php echo $row['num_cita']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta cita?');"><i class="fas fa-trash"></i> Eliminar</a>
                            </div>
                        </div>

                        <!-- Ver Modal -->
                        <?php include('modales/m_ver_cita.php'); ?>

                        <!-- Editar Modal -->
                        <?php include('modales/m_editar_cita.php'); ?>
            <?php
                    }
                } else {
                    echo "<div class='alert alert-info mt-3'>No hay citas de " . $tipo . "s activas.</div>";
                }

                echo "</div>";
            }
            ?>
        </div>
    </main>

    <!-- Modal Crear Cita -->
    <?php include('modales/m_crear_cita.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function mostrarCamposTipo(prefijo) {
            const tipo = document.getElementById(`tipo-${prefijo}`).value;
            document.getElementById(`estudiante-${prefijo}`).style.display = tipo === 'estudiante' ? 'block' : 'none';
            document.getElementById(`acudiente-${prefijo}`).style.display = tipo === 'acudiente' ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar todos los selectores de tipo
            const editPrefixes = Array.from(document.querySelectorAll('select[id^="tipo-editar-"]'))
                .map(el => el.id.replace('tipo-editar-', 'editar-'));

            ['crear', ...editPrefixes].forEach(id => {
                const el = document.getElementById(`tipo-${id}`);
                if (el) el.dispatchEvent(new Event('change'));
            });
        });
    </script>
</body>

</html>

<?php $conexion->close(); ?>