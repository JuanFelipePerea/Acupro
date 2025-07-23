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

// Manejar eliminación de citas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cita'])) {
    $numCita = intval($_POST['num_cita']);
    
    // Verificar que la cita existe y es del pasado
    $sqlVerificar = "SELECT fecha FROM citas WHERE num_cita = ? AND fecha < CURDATE()";
    $stmtVerificar = $conexion->prepare($sqlVerificar);
    $stmtVerificar->bind_param("i", $numCita);
    $stmtVerificar->execute();
    $resultado = $stmtVerificar->get_result();
    
    if ($resultado->num_rows > 0) {
        // Eliminar la cita
        $sqlEliminar = "DELETE FROM citas WHERE num_cita = ?";
        $stmtEliminar = $conexion->prepare($sqlEliminar);
        $stmtEliminar->bind_param("i", $numCita);
        
        if ($stmtEliminar->execute()) {
            $mensaje = "Cita eliminada exitosamente del historial.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al eliminar la cita.";
            $tipoMensaje = "danger";
        }
    } else {
        $mensaje = "No se puede eliminar la cita. Solo se pueden eliminar citas pasadas.";
        $tipoMensaje = "warning";
    }
}

// Initialize variables for date filtering
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Function to get historical appointments with date filtering (SOLO CITAS PASADAS)
function obtenerHistorialCitas($conexion, $tipo = '', $fechaInicio = '', $fechaFin = '', $search = '')
{
    $conditions = [];
    $params = [];
    $types = "";

    // MODIFICACIÓN: Solo mostrar citas pasadas
    $sql = "SELECT * FROM citas WHERE fecha < CURDATE()";

    // Add tipo filter if specified
    if (!empty($tipo)) {
        $conditions[] = "tipo = ?";
        $params[] = $tipo;
        $types .= "s";
    }

    // Add date range filter if specified
    if (!empty($fechaInicio)) {
        $conditions[] = "fecha >= ?";
        $params[] = $fechaInicio;
        $types .= "s";
    }

    if (!empty($fechaFin)) {
        $conditions[] = "fecha <= ?";
        $params[] = $fechaFin;
        $types .= "s";
    }

    // Add search condition if search query exists
    if (!empty($search)) {
        $conditions[] = "(nom_docente LIKE ? OR acc_docente LIKE ? OR motivo LIKE ? OR descripcion LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "ssss";
    }

    // Add conditions to SQL
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    // Order by date, newest first
    $sql .= " ORDER BY fecha DESC";

    $stmt = $conexion->prepare($sql);

    // Bind parameters if there are any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Get all appointment types for filter options
function obtenerTiposCitas($conexion)
{
    $sql = "SELECT DISTINCT tipo FROM citas ORDER BY tipo";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Get appointment history based on filters
$tipoFiltro = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$historialCitas = obtenerHistorialCitas($conexion, $tipoFiltro, $fechaInicio, $fechaFin, $searchQuery);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ñam - Historial de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="historial.css">
</head>

<body>
    <main>
        <div class="container mt-4">
            <!-- Mostrar mensajes de confirmación/error -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-3">
                <h2>Historial de Citas</h2>
                <div>
                    <a href="read.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Citas Activas
                    </a>
                    <a href="calendario.php" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-calendar"></i> Ver Calendario
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filter-form">
                <form method="GET" action="historial.php" class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fechaInicio); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fechaFin); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo de Cita</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="estudiante" <?php echo ($tipoFiltro == 'estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                            <option value="acudiente" <?php echo ($tipoFiltro == 'acudiente') ? 'selected' : ''; ?>>Acudiente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre, motivo..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="historial.php" class="btn btn-outline-secondary ms-2">Limpiar Filtros</a>
                    </div>
                </form>
            </div>

            <!-- Resultados -->
            <div class="historial-list">
                <?php
                if ($historialCitas->num_rows > 0) {
                    while ($row = $historialCitas->fetch_assoc()) {
                        $fechaCita = new DateTime($row['fecha']);
                        $fechaHoy = new DateTime(date('Y-m-d'));
                        
                        // Solo mostrar citas pasadas, así que todas serán "cita-pasada"
                        $claseCita = "cita-pasada";
                        $estadoCita = "Pasada";

                        echo "<div class='cita-card $claseCita'>";
                        echo "<div class='avatar'><i class='fas fa-user-" . ($row['tipo'] == 'estudiante' ? 'graduate' : 'tie') . "'></i></div>";
                        echo "<div class='details'>";
                        echo "<div class='d-flex justify-content-between'>";
                        echo "<h4>" . htmlspecialchars($row['nom_docente']) . "</h4>";
                        echo "<span class='badge " . ($row['tipo'] == 'estudiante' ? 'badge-estudiante' : 'badge-acudiente') . " text-white'>" . ucfirst(htmlspecialchars($row['tipo'])) . "</span>";
                        echo "</div>";
                        echo "<div class='cita-fecha'>";
                        echo "<i class='fas fa-calendar-alt me-1'></i> " . date('d/m/Y', strtotime($row['fecha']));
                        echo " <span class='badge bg-secondary'>" . $estadoCita . "</span>";
                        echo "</div>";
                        echo "<p class='mt-2'><strong>Motivo:</strong> " . htmlspecialchars($row['motivo']) . "</p>";
                        echo "<p class='mt-1'><strong>Descripción:</strong> " . substr(htmlspecialchars($row['descripcion']), 0, 100) . (strlen($row['descripcion']) > 100 ? "..." : "") . "</p>";
                        echo "</div>";
                        echo "<div class='actions'>";
                        echo "<button class='btn' style='background-color: #6f42c1; color: #f1f1f1;' data-bs-toggle='modal' data-bs-target='#verCitaModal" . $row['num_cita'] . "'><i class='fas fa-eye'></i> Ver</button>";
                        echo "<button class='btn btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#eliminarCitaModal" . $row['num_cita'] . "'><i class='fas fa-trash'></i> Eliminar</button>";
                        echo "</div>";
                        echo "</div>";

                        // Modal para VER la cita
                        echo "<div class='modal fade' id='verCitaModal" . $row['num_cita'] . "' tabindex='-1' aria-labelledby='verCitaModalLabel" . $row['num_cita'] . "' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<h5 class='modal-title' id='verCitaModalLabel" . $row['num_cita'] . "'>Detalles de la Cita: " . htmlspecialchars($row['nom_docente']) . "</h5>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Fecha:</strong> " . date('d/m/Y', strtotime($row['fecha']));
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Tipo de Cita:</strong> " . ucfirst(htmlspecialchars($row['tipo']));
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Estado:</strong> " . $estadoCita;
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Motivo:</strong> " . htmlspecialchars($row['motivo']);
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Descripción:</strong> <p>" . htmlspecialchars($row['descripcion']) . "</p>";
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Identificación del Docente:</strong> " . htmlspecialchars($row['acc_docente']);
                        echo "</div>";
                        echo "<div class='mb-3'>";
                        echo "<strong>Nombre del Docente:</strong> " . htmlspecialchars($row['nom_docente']);
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<a href='read.php?cita=" . $row['num_cita'] . "' class='btn btn-primary'>Editar Cita</a>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                        // Modal para ELIMINAR la cita
                        echo "<div class='modal fade' id='eliminarCitaModal" . $row['num_cita'] . "' tabindex='-1' aria-labelledby='eliminarCitaModalLabel" . $row['num_cita'] . "' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<h5 class='modal-title' id='eliminarCitaModalLabel" . $row['num_cita'] . "'>Confirmar Eliminación</h5>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        echo "<p>¿Estás seguro de que deseas eliminar esta cita del historial?</p>";
                        echo "<div class='alert alert-warning'>";
                        echo "<strong><i class='fas fa-exclamation-triangle me-2'></i>Advertencia:</strong> Esta acción no se puede deshacer.";
                        echo "</div>";
                        echo "<div class='card'>";
                        echo "<div class='card-body'>";
                        echo "<h6 class='card-title'>" . htmlspecialchars($row['nom_docente']) . "</h6>";
                        echo "<p class='card-text'><strong>Fecha:</strong> " . date('d/m/Y', strtotime($row['fecha'])) . "</p>";
                        echo "<p class='card-text'><strong>Motivo:</strong> " . htmlspecialchars($row['motivo']) . "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<form method='POST' action='historial.php' style='display: inline;'>";
                        echo "<input type='hidden' name='num_cita' value='" . $row['num_cita'] . "'>";
                        echo "<button type='submit' name='eliminar_cita' class='btn btn-danger'><i class='fas fa-trash'></i> Sí, Eliminar</button>";
                        echo "</form>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='alert alert-info mt-3'>";
                    echo "<i class='fas fa-info-circle me-2'></i> No se encontraron citas pasadas con los filtros seleccionados.";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para validar fechas
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');

            fechaInicio.addEventListener('change', function() {
                if (fechaFin.value && fechaInicio.value > fechaFin.value) {
                    alert('La fecha de inicio no puede ser posterior a la fecha de fin');
                    fechaInicio.value = fechaFin.value;
                }
            });

            fechaFin.addEventListener('change', function() {
                if (fechaInicio.value && fechaInicio.value > fechaFin.value) {
                    alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                    fechaFin.value = fechaInicio.value;
                }
            });
        });
    </script>
</body>

</html>