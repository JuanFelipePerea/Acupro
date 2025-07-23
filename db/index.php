<?php
session_start();

// Define a constant to prevent direct access to included files
define('ACUPRO_INCLUDED', true);

require_once("conexion.php");
require_once("funcitas.php");

// Process delete operation if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (eliminarCita($conexion, $_GET['delete'])) {
        // Redirect to prevent resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=delete");
        exit;
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=delete");
        exit;
    }
}

// Process edit operation if submitted
if (isset($_POST['editar']) && isset($_POST['num_cita'])) {
    $num_cita = $_POST['num_cita'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $motivo = $_POST['motivo'];
    $descripcion = $_POST['descripcion'];
    $acc_docente = $_POST['acc_docente'];
    $nom_docente = $_POST['nom_docente'];
    $tipo = $_POST['tipo'];
    $cod_estudiante = ($tipo == 'estudiante' && !empty($_POST['cod_estudiante'])) ? $_POST['cod_estudiante'] : NULL;
    $num_acudiente = ($tipo == 'acudiente' && !empty($_POST['num_acudiente'])) ? $_POST['num_acudiente'] : NULL;

    if (actualizarCita($conexion, $num_cita, $fecha, $motivo, $acc_docente, $nom_docente, $descripcion, $tipo, $hora_inicio, $cod_estudiante, $num_acudiente)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=edit");
        exit;
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=edit");
        exit;
    }
}

$nombreUsuario = htmlspecialchars(
    isset($_SESSION["guest"]) && $_SESSION["guest"] === true
        ? $_SESSION["guest_name"]
        : (is_array($_SESSION["usuario"]) ? $_SESSION["usuario"][0] : $_SESSION["usuario"])
);

include_once("sidebar.php");

// Crear una instancia de la barra lateral
$sidebar = new Sidebar($conexion, $nombreUsuario);
$sidebar->render();

// Calculate statistics using database queries
// Total appointments
$sqlTotal = "SELECT COUNT(*) as total FROM citas";
$resultTotal = $conexion->query($sqlTotal);
$totalCitas = $resultTotal->fetch_assoc()['total'];

// Completed appointments (past dates)
$sqlCompletadas = "SELECT COUNT(*) as total FROM citas WHERE fecha < CURDATE()";
$resultCompletadas = $conexion->query($sqlCompletadas);
$citasCompletadas = $resultCompletadas->fetch_assoc()['total'];

// Today's appointments
$sqlHoy = "SELECT COUNT(*) as total FROM citas WHERE DATE(fecha) = CURDATE()";
$resultHoy = $conexion->query($sqlHoy);
$citasHoy = $resultHoy->fetch_assoc()['total'];

// Count departments
$sqlDepts = "SELECT COUNT(DISTINCT acc_docente) as total FROM citas WHERE acc_docente IS NOT NULL AND acc_docente != ''";
$resultDepts = $conexion->query($sqlDepts);
$totalDepts = $resultDepts->fetch_assoc()['total'];

// Fetch all appointments using the function from funcitas.php
$result = obtenerCitas($conexion);

// Get students and guardians for the edit modals
$estudiantes = obtenerEstudiantes($conexion);
$acudientes = obtenerAcudientes($conexion);

// Import header
require_once("header.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acupro - Panel de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .alert-success-custom {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        .alert-error-custom {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        .form-group-dynamic {
            transition: all 0.3s ease;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            background: linear-gradient(135deg, #6e80cf 0%, #4a63c7 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-close {
            filter: invert(1);
        }
    </style>
</head>

<body>
    <div class="app-container">
        <main class="main-content">
            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success-custom alert-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php 
                    switch($_GET['success']) {
                        case 'delete':
                            echo 'Cita eliminada correctamente.';
                            break;
                        case 'edit':
                            echo 'Cita actualizada correctamente.';
                            break;
                        default:
                            echo 'Operación realizada correctamente.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error-custom alert-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php 
                    switch($_GET['error']) {
                        case 'delete':
                            echo 'Error al eliminar la cita.';
                            break;
                        case 'edit':
                            echo 'Error al actualizar la cita.';
                            break;
                        default:
                            echo 'Ocurrió un error al procesar la operación.';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-success-custom alert-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error-custom alert-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <header class="content-header">
                <div class="welcome-section">
                    <h1>Bienvenido <?php echo $nombreUsuario; ?> al <span class="brand-text">Panel de Control</span></h1>
                    <p class="subtitle">Gestiona tus citas y compromisos académicos</p>
                </div>

                <div class="header-actions">
                    <a href="read.php?openModal=crearCita" class="btn action-btn">
                        <i class="bi bi-plus-circle"></i> Nueva Cita
                    </a>
                    <div class="notification-bell">
                        <i class="bi bi-bell"></i>
                        <?php if ($citasHoy > 0): ?>
                            <span class="notification-badge"><?php echo $citasHoy; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon upcoming"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3>Próximas</h3>
                        <p class="stat-number"><?php echo $totalCitas - $citasCompletadas; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon today"><i class="bi bi-calendar-day"></i></div>
                    <div class="stat-info">
                        <h3>Hoy</h3>
                        <p class="stat-number"><?php echo $citasHoy; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon completed"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-info">
                        <h3>Completadas</h3>
                        <p class="stat-number"><?php echo $citasCompletadas; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon departments"><i class="bi bi-building"></i></div>
                    <div class="stat-info">
                        <h3>Departamentos</h3>
                        <p class="stat-number"><?php echo $totalDepts; ?></p>
                    </div>
                </div>
            </div>

            <h2 class="section-title"><i class="bi bi-clock-history"></i> Próximas Citas</h2>

            <div class="card-container">
                <?php
                if ($result && $result->num_rows > 0) {
                    $cardGradients = [
                        'linear-gradient(135deg, #6e80cf 0%, #4a63c7 100%)',
                        'linear-gradient(135deg, #b48bdd 0%, #9b6fcf 100%)',
                        'linear-gradient(135deg, #3498db 0%, #2980b9 100%)',
                        'linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%)',
                        'linear-gradient(135deg, #3a7bd5 0%, #3a6073 100%)'
                    ];

                    $i = 0;
                    while ($row = $result->fetch_assoc()) {
                        $colorIndex = $i % count($cardGradients);
                        $fecha = date("d/m/Y", strtotime($row['fecha']));
                        $hora = isset($row['hora_inicio']) ? date("H:i", strtotime($row['hora_inicio'])) : "N/A";
                        
                        // Prepare data for the edit modal
                        $row['fecha_only'] = date("Y-m-d", strtotime($row['fecha']));
                        $row['hora_only'] = isset($row['hora_inicio']) ? date("H:i", strtotime($row['hora_inicio'])) : "";
                ?>
                        <div class="appointment-card" style="background: <?php echo $cardGradients[$colorIndex]; ?>">
                            <div class="card-header">
                                <h5 class="card-title"><i class="bi bi-calendar-check"></i> Cita programada</h5>
                                <span class="appointment-date"><?php echo $fecha; ?> <?php echo $hora; ?></span>
                            </div>
                            <div class="card-body">
                                <p class="docente-name">
                                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nom_docente']); ?>
                                </p>
                                <?php if (!empty($row['acc_docente'])): ?>
                                    <p class="docente-dept">
                                        <i class="bi bi-building"></i> <?php echo htmlspecialchars($row['acc_docente']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($row['motivo'])): ?>
                                    <p class="appointment-reason">
                                        <i class="bi bi-chat-left-text"></i> <?php echo htmlspecialchars(substr($row['motivo'], 0, 50)) . (strlen($row['motivo']) > 50 ? '...' : ''); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Show student or guardian info -->
                                <?php if ($row['tipo'] == 'estudiante' && !empty($row['nombres'])): ?>
                                    <p class="appointment-participant">
                                        <i class="bi bi-person-badge"></i> <?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?>
                                        <?php if (!empty($row['n_grado'])): ?>
                                            <small>(<?php echo htmlspecialchars($row['n_grado']); ?>)</small>
                                        <?php endif; ?>
                                    </p>
                                <?php elseif ($row['tipo'] == 'acudiente' && !empty($row['nom_acudiente'])): ?>
                                    <p class="appointment-participant">
                                        <i class="bi bi-person-hearts"></i> <?php echo htmlspecialchars($row['nom_acudiente']); ?>
                                        <?php if (!empty($row['relacion'])): ?>
                                            <small>(<?php echo htmlspecialchars($row['relacion']); ?>)</small>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <a href="read.php?id=<?php echo $row['num_cita']; ?>" class="btn card-btn">
                                    <i class="bi bi-eye"></i> Detalles
                                </a>
                                <div class="card-actions">
                                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editarCitaModal<?php echo $row['num_cita']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="index.php?delete=<?php echo $row['num_cita']; ?>"
                                        class="btn btn-sm btn-light"
                                        onclick="return confirm('¿Estás seguro de eliminar esta cita?\n\nDocente: <?php echo htmlspecialchars($row['nom_docente']); ?>\nFecha: <?php echo $fecha; ?> <?php echo $hora; ?>');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal for this appointment -->
                        <div class="modal fade" id="editarCitaModal<?php echo $row['num_cita']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-pencil-square me-2"></i>
                                            Editar Cita: <?php echo htmlspecialchars($row['nom_docente']); ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="index.php" method="POST">
                                            <input type="hidden" name="num_cita" value="<?php echo htmlspecialchars($row['num_cita']); ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="bi bi-calendar3"></i> Fecha</label>
                                                        <input type="date" class="form-control" name="fecha" value="<?php echo htmlspecialchars($row['fecha_only']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="bi bi-clock"></i> Hora</label>
                                                        <input type="time" class="form-control" name="hora_inicio" value="<?php echo htmlspecialchars($row['hora_only']); ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-chat-left-text"></i> Motivo</label>
                                                <input type="text" class="form-control" name="motivo" value="<?php echo htmlspecialchars($row['motivo']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-file-text"></i> Descripción</label>
                                                <textarea class="form-control" name="descripcion" rows="3" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="bi bi-card-text"></i> TI Docente</label>
                                                        <input type="text" class="form-control" name="acc_docente" value="<?php echo htmlspecialchars($row['acc_docente']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><i class="bi bi-person"></i> Docente</label>
                                                        <input type="text" class="form-control" name="nom_docente" value="<?php echo htmlspecialchars($row['nom_docente']); ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"><i class="bi bi-people"></i> Tipo de Cita</label>
                                                <select class="form-select" id="tipo-editar-<?php echo $row['num_cita']; ?>" name="tipo" onchange="mostrarCamposTipo('editar-<?php echo $row['num_cita']; ?>')" required>
                                                    <option value="estudiante" <?php echo $row['tipo'] == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                                                    <option value="acudiente" <?php echo $row['tipo'] == 'acudiente' ? 'selected' : ''; ?>>Acudiente</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 form-group-dynamic" id="estudiante-editar-<?php echo $row['num_cita']; ?>" style="display:<?php echo $row['tipo'] == 'estudiante' ? 'block' : 'none'; ?>;">
                                                <label class="form-label"><i class="bi bi-person-badge"></i> Estudiante</label>
                                                <select class="form-select" name="cod_estudiante">
                                                    <option value="">Seleccione un estudiante</option>
                                                    <?php
                                                    $estudiantes->data_seek(0);
                                                    while ($est = $estudiantes->fetch_assoc()) {
                                                        $selected = ($row['cod_estudiante'] == $est['cod_estudiante']) ? 'selected' : '';
                                                        echo "<option value='{$est['cod_estudiante']}' {$selected}>" . htmlspecialchars($est['nombre_completo']) . " - " . htmlspecialchars($est['n_grado']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="mb-3 form-group-dynamic" id="acudiente-editar-<?php echo $row['num_cita']; ?>" style="display:<?php echo $row['tipo'] == 'acudiente' ? 'block' : 'none'; ?>;">
                                                <label class="form-label"><i class="bi bi-person-hearts"></i> Acudiente</label>
                                                <select class="form-select" name="num_acudiente">
                                                    <option value="">Seleccione un acudiente</option>
                                                    <?php
                                                    $acudientes->data_seek(0);
                                                    while ($acu = $acudientes->fetch_assoc()) {
                                                        $selected = ($row['num_acudiente'] == $acu['num_acudiente']) ? 'selected' : '';
                                                        $studentInfo = !empty($acu['nombre_estudiante']) ? " (Estudiante: " . htmlspecialchars($acu['nombre_estudiante']) . ")" : "";
                                                        echo "<option value='{$acu['num_acudiente']}' {$selected}>" . htmlspecialchars($acu['nom_acudiente']) . " - " . htmlspecialchars($acu['relacion']) . $studentInfo . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                                <button type="submit" name="editar" class="btn btn-primary">
                                                    <i class="bi bi-check-circle"></i> Guardar Cambios
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                        $i++;
                    }
                } else {
                    ?>
                    <div class="no-appointments">
                        <i class="bi bi-calendar-x"></i>
                        <h3>No hay citas programadas</h3>
                        <p>Comienza agregando una nueva cita a tu calendario</p>
                        <a href="read.php?openModal=crearCita" class="btn action-btn">
                            <i class="bi bi-plus-circle"></i> Nueva Cita
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight current date's appointments
            const today = new Date().toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                })
                .replace(/\//g, '/');

            document.querySelectorAll('.appointment-date').forEach(function(element) {
                if (element.textContent.trim().includes(today)) {
                    element.closest('.appointment-card').classList.add('today-appointment');
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Function to show/hide fields based on appointment type
        function mostrarCamposTipo(suffix) {
            const tipoSelect = document.getElementById('tipo-' + suffix);
            const estudianteDiv = document.getElementById('estudiante-' + suffix);
            const acudienteDiv = document.getElementById('acudiente-' + suffix);
            
            if (tipoSelect.value === 'estudiante') {
                estudianteDiv.style.display = 'block';
                acudienteDiv.style.display = 'none';
                // Make student field required
                estudianteDiv.querySelector('select').required = true;
                acudienteDiv.querySelector('select').required = false;
            } else if (tipoSelect.value === 'acudiente') {
                estudianteDiv.style.display = 'none';
                acudienteDiv.style.display = 'block';
                // Make guardian field required
                estudianteDiv.querySelector('select').required = false;
                acudienteDiv.querySelector('select').required = true;
            }
        }

        // Initialize all edit modals when they open
        document.querySelectorAll('[id^="editarCitaModal"]').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                // Focus on the first input
                const firstInput = modal.querySelector('input[type="date"]');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    </script>
</body>

</html>

<?php
$conexion->close();
include("footer.php");
?>