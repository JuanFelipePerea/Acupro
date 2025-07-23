<?php
require("header.php");
require_once("conexion.php");

$nombreUsuario = htmlspecialchars(
    isset($_SESSION["guest"]) && $_SESSION["guest"] === true
        ? $_SESSION["guest_name"]
        : (is_array($_SESSION["usuario"]) ? $_SESSION["usuario"][0] : $_SESSION["usuario"])
);

include_once("sidebar.php");

// Crear una instancia de la barra lateral
$sidebar = new Sidebar($conexion, $nombreUsuario);
$sidebar->render();


// config.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "acupro";

// Database connection
$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Initialize variables
$messages = ['estudiante' => '', 'acudiente' => ''];
$errors = ['estudiante' => '', 'acudiente' => ''];
$edit_mode = ['estudiante' => false, 'acudiente' => false];
$estudiante = ['cod_estudiante' => '', 'nombres' => '', 'apellidos' => '', 'edad' => '', 'n_grado' => ''];
$acudiente = ['num_acudiente' => '', 'nom_acudiente' => '', 'telefono' => '', 'relacion' => '', 'cod_estudiante' => ''];

// Helper Functions
function obtenerEstudiantes($conexion)
{
    $sql = "SELECT cod_estudiante, nombres, apellidos FROM estudiantes ORDER BY apellidos, nombres";
    $result = $conexion->query($sql);
    return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function handleDelete($conexion, $table, $id_field, $id, &$message, &$error)
{
    $sql = "DELETE FROM $table WHERE $id_field = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = ucfirst($table) . " eliminado correctamente.";
    } else {
        $error = "Error al eliminar el " . $table . ": " . $conexion->error;
    }
    $stmt->close();
}

function fetchRecord($conexion, $table, $id_field, $id, &$record, &$edit_mode, &$error)
{
    $sql = "SELECT * FROM $table WHERE $id_field = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        $edit_mode = true;
    } else {
        $error = "No se encontró el " . $table . ".";
    }
    $stmt->close();
}

function generateViewModal($type, $id, $data, $fields, $title, $icon)
{
?>
    <div class="modal fade" id="ver<?php echo ucfirst($type) . $id; ?>" tabindex="-1" aria-labelledby="ver<?php echo ucfirst($type) . $id; ?>Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ver<?php echo ucfirst($type) . $id; ?>Label"><i class="fas <?php echo $icon; ?> me-2"></i><?php echo $title; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-container mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas <?php echo $icon; ?>" style="font-size: 40px;"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($data[$fields['name']]); ?></h4>
                        <span class="badge bg-primary"><?php echo ucfirst($type); ?></span>
                    </div>
                    <?php foreach ($fields['display'] as $label => $key): ?>
                        <div class="student-info mb-3">
                            <span class="info-label"><?php echo $label; ?>:</span>
                            <span class="info-value"><?php echo htmlspecialchars($data[$key] ?: '<span class="text-muted">Sin asignar</span>'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php
}

function generateEditModal($type, $id, $data, $fields, $title, $icon, $estudiantes_lista = null)
{
?>
    <div class="modal fade" id="editar<?php echo ucfirst($type) . $id; ?>" tabindex="-1" aria-labelledby="editar<?php echo ucfirst($type) . $id; ?>Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editar<?php echo ucfirst($type) . $id; ?>Label"><i class="fas <?php echo $icon; ?> me-2"></i><?php echo $title; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="<?php echo $fields['id']; ?>" value="<?php echo $id; ?>">
                        <?php foreach ($fields['inputs'] as $name => $label): ?>
                            <?php if ($name == 'cod_estudiante' && $estudiantes_lista): ?>
                                <div class="mb-3">
                                    <label for="<?php echo $name . $id; ?>" class="form-label"><?php echo $label; ?></label>
                                    <select class="form-select" id="<?php echo $name . $id; ?>" name="<?php echo $name; ?>">
                                        <option value="">Seleccione un estudiante</option>
                                        <?php foreach ($estudiantes_lista as $estudiante): ?>
                                            <option value="<?php echo $estudiante['cod_estudiante']; ?>" <?php echo $data[$name] == $estudiante['cod_estudiante'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($estudiante['apellidos'] . ' ' . $estudiante['nombres']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="<?php echo $name . $id; ?>" class="form-label"><?php echo $label; ?></label>
                                    <input type="<?php echo $name == 'edad' ? 'number' : 'text'; ?>" class="form-control" id="<?php echo $name . $id; ?>" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($data[$name]); ?>" <?php echo $name != 'n_grado' ? 'required' : ''; ?> <?php echo $name == 'edad' ? 'min="1"' : ''; ?>>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancelar</button>
                        <button type="submit" name="<?php echo $type == 'estudiante' ? 'actualizar' : 'actualizar_acudiente'; ?>" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}

// Handle GET requests (Delete/Edit)
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    handleDelete($conexion, 'estudiantes', 'cod_estudiante', $_GET['eliminar'], $messages['estudiante'], $errors['estudiante']);
}
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    fetchRecord($conexion, 'estudiantes', 'cod_estudiante', $_GET['editar'], $estudiante, $edit_mode['estudiante'], $errors['estudiante']);
}
if (isset($_GET['eliminar_acudiente']) && is_numeric($_GET['eliminar_acudiente'])) {
    handleDelete($conexion, 'acudientes', 'num_acudiente', $_GET['eliminar_acudiente'], $messages['acudiente'], $errors['acudiente']);
}
if (isset($_GET['editar_acudiente']) && is_numeric($_GET['editar_acudiente'])) {
    fetchRecord($conexion, 'acudientes', 'num_acudiente', $_GET['editar_acudiente'], $acudiente, $edit_mode['acudiente'], $errors['acudiente']);
}

// Handle POST requests (Create/Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear'])) {
        $estudiante = [
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'edad' => (int)$_POST['edad'],
            'n_grado' => trim($_POST['n_grado'])
        ];
        if (empty($estudiante['nombres']) || empty($estudiante['apellidos']) || $estudiante['edad'] <= 0) {
            $errors['estudiante'] = "Complete todos los campos obligatorios correctamente.";
        } else {
            $sql = "INSERT INTO estudiantes (nombres, apellidos, edad, n_grado) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssis", $estudiante['nombres'], $estudiante['apellidos'], $estudiante['edad'], $estudiante['n_grado']);
            if ($stmt->execute()) {
                $messages['estudiante'] = "Estudiante registrado correctamente.";
                $estudiante = ['cod_estudiante' => '', 'nombres' => '', 'apellidos' => '', 'edad' => '', 'n_grado' => ''];
            } else {
                $errors['estudiante'] = "Error al registrar el estudiante: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    if (isset($_POST['actualizar']) && !empty($_POST['cod_estudiante'])) {
        $estudiante = [
            'cod_estudiante' => $_POST['cod_estudiante'],
            'nombres' => trim($_POST['nombres']),
            'apellidos' => trim($_POST['apellidos']),
            'edad' => (int)$_POST['edad'],
            'n_grado' => trim($_POST['n_grado'])
        ];
        if (empty($estudiante['nombres']) || empty($estudiante['apellidos']) || $estudiante['edad'] <= 0) {
            $errors['estudiante'] = "Complete todos los campos obligatorios correctamente.";
        } else {
            $sql = "UPDATE estudiantes SET nombres = ?, apellidos = ?, edad = ?, n_grado = ? WHERE cod_estudiante = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssisi", $estudiante['nombres'], $estudiante['apellidos'], $estudiante['edad'], $estudiante['n_grado'], $estudiante['cod_estudiante']);
            if ($stmt->execute()) {
                $messages['estudiante'] = "Estudiante actualizado correctamente.";
                $edit_mode['estudiante'] = false;
                $estudiante = ['cod_estudiante' => '', 'nombres' => '', 'apellidos' => '', 'edad' => '', 'n_grado' => ''];
            } else {
                $errors['estudiante'] = "Error al actualizar el estudiante: " . $conexion->error;
            }
            $stmt->close();
        }
    }
    if (isset($_POST['crear_acudiente'])) {
        $acudiente = [
            'nom_acudiente' => trim($_POST['nom_acudiente']),
            'telefono' => trim($_POST['telefono']),
            'relacion' => trim($_POST['relacion']),
            'cod_estudiante' => $_POST['cod_estudiante'] ?: null
        ];
        if (empty($acudiente['nom_acudiente']) || empty($acudiente['telefono']) || empty($acudiente['relacion'])) {
            $errors['acudiente'] = "Complete todos los campos obligatorios del acudiente.";
        } else {
            $sql = "INSERT INTO acudientes (nom_acudiente, telefono, relacion, cod_estudiante) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $acudiente['nom_acudiente'], $acudiente['telefono'], $acudiente['relacion'], $acudiente['cod_estudiante']);
            if ($stmt->execute()) {
                $messages['acudiente'] = "Acudiente registrado correctamente.";
                $acudiente = ['num_acudiente' => '', 'nom_acudiente' => '', 'telefono' => '', 'relacion' => '', 'cod_estudiante' => ''];
            } else {
                $errors['acudiente'] = "Error al registrar el acudiente: " . $conexion->error;
            }
            $stmt->close();
        }
    }
    if (isset($_POST['actualizar_acudiente']) && !empty($_POST['num_acudiente'])) {
        $acudiente = [
            'num_acudiente' => $_POST['num_acudiente'],
            'nom_acudiente' => trim($_POST['nom_acudiente']),
            'telefono' => trim($_POST['telefono']),
            'relacion' => trim($_POST['relacion']),
            'cod_estudiante' => $_POST['cod_estudiante'] ?: null
        ];
        if (empty($acudiente['nom_acudiente']) || empty($acudiente['telefono']) || empty($acudiente['relacion'])) {
            $errors['acudiente'] = "Complete todos los campos obligatorios del acudiente.";
        } else {
            $sql = "UPDATE acudientes SET nom_acudiente = ?, telefono = ?, relacion = ?, cod_estudiante = ? WHERE num_acudiente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssii", $acudiente['nom_acudiente'], $acudiente['telefono'], $acudiente['relacion'], $acudiente['cod_estudiante'], $acudiente['num_acudiente']);
            if ($stmt->execute()) {
                $messages['acudiente'] = "Acudiente actualizado correctamente.";
                $edit_mode['acudiente'] = false;
                $acudiente = ['num_acudiente' => '', 'nom_acudiente' => '', 'telefono' => '', 'relacion' => '', 'cod_estudiante' => ''];
            } else {
                $errors['acudiente'] = "Error al actualizar el acudiente: " . $conexion->error;
            }
            $stmt->close();
        }
    }
}

// Fetch data
$result_estudiantes = $conexion->query("SELECT * FROM estudiantes ORDER BY apellidos, nombres");
$result_acudientes = $conexion->query("SELECT a.*, CONCAT(e.nombres, ' ', e.apellidos) as nombre_estudiante 
                                       FROM acudientes a 
                                       LEFT JOIN estudiantes e ON a.cod_estudiante = e.cod_estudiante 
                                       ORDER BY a.nom_acudiente");
$estudiantes_lista = obtenerEstudiantes($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico - Gestión de Estudiantes y Acudientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="estudiantes.css">
</head>

<body>
    <div class="container-custom">
        <h1 class="text-center page-title mb-5"><i class="fas fa-school me-2"></i>Sistema de Gestión Académica</h1>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="estudiantes-tab" data-bs-toggle="tab" data-bs-target="#estudiantes" type="button" role="tab" aria-controls="estudiantes" aria-selected="true">
                    <i class="fas fa-user-graduate me-2"></i>Estudiantes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="acudientes-tab" data-bs-toggle="tab" data-bs-target="#acudientes" type="button" role="tab" aria-controls="acudientes" aria-selected="false">
                    <i class="fas fa-user-friends me-2"></i>Acudientes
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Estudiantes Tab -->
            <div class="tab-pane fade show active" id="estudiantes" role="tabpanel" aria-labelledby="estudiantes-tab">
                <?php if ($messages['estudiante']): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $messages['estudiante']; ?></div>
                <?php endif; ?>
                <?php if ($errors['estudiante']): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['estudiante']; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-list-alt me-2"></i>Listado de Estudiantes</h2>
                    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#crearEstudianteModal"><i class="fas fa-plus me-2"></i>Nuevo Estudiante</button>
                </div>

                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="buscarEstudiante" placeholder="Buscar estudiantes...">
                </div>

                <div class="row">
                    <?php if ($result_estudiantes->num_rows > 0): ?>
                        <?php while ($row = $result_estudiantes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="student-card">
                                    <div class="student-header">
                                        <div class="avatar-container"><i class="fas fa-user-graduate"></i></div>
                                        <div>
                                            <h4 class="mb-0"><?php echo htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']); ?></h4>
                                            <small>ID: <?php echo htmlspecialchars($row['cod_estudiante']); ?></small>
                                        </div>
                                    </div>
                                    <div class="student-details">
                                        <div class="student-info"><span class="info-label">Edad:</span><span class="info-value"><?php echo htmlspecialchars($row['edad']); ?> años</span></div>
                                        <div class="student-info"><span class="info-label">Grado:</span><span class="info-value"><?php echo $row['n_grado'] ? htmlspecialchars($row['n_grado']) : '<span class="text-muted">Sin asignar</span>'; ?></span></div>
                                    </div>
                                    <div class="student-actions">
                                        <button class="btn btn-view" data-bs-toggle="modal" data-bs-target="#verEstudiante<?php echo $row['cod_estudiante']; ?>"><i class="fas fa-eye me-1"></i> Ver</button>
                                        <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editarEstudiante<?php echo $row['cod_estudiante']; ?>"><i class="fas fa-edit me-1"></i> Editar</button>
                                        <a href="?eliminar=<?php echo $row['cod_estudiante']; ?>" class="btn btn-delete" onclick="return confirm('¿Está seguro de eliminar este estudiante?')"><i class="fas fa-trash me-1"></i> Eliminar</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            generateViewModal('estudiante', $row['cod_estudiante'], $row, [
                                'name' => 'nombres',
                                'display' => [
                                    'Código' => 'cod_estudiante',
                                    'Edad' => 'edad',
                                    'Grado' => 'n_grado'
                                ]
                            ], 'Detalles del Estudiante', 'fa-user-graduate');
                            generateEditModal('estudiante', $row['cod_estudiante'], $row, [
                                'id' => 'cod_estudiante',
                                'inputs' => [
                                    'nombres' => 'Nombres',
                                    'apellidos' => 'Apellidos',
                                    'edad' => 'Edad',
                                    'n_grado' => 'Grado'
                                ]
                            ], 'Editar Estudiante', 'fa-edit');
                            ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i>No hay estudiantes registrados.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acudientes Tab -->
            <div class="tab-pane fade" id="acudientes" role="tabpanel" aria-labelledby="acudientes-tab">
                <?php if ($messages['acudiente']): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $messages['acudiente']; ?></div>
                <?php endif; ?>
                <?php if ($errors['acudiente']): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $errors['acudiente']; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="fas fa-user-friends me-2"></i>Listado de Acudientes</h2>
                    <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#crearAcudienteModal"><i class="fas fa-plus me-2"></i>Nuevo Acudiente</button>
                </div>

                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="buscarAcudiente" placeholder="Buscar acudientes...">
                </div>

                <div class="row">
                    <?php if ($result_acudientes->num_rows > 0): ?>
                        <?php while ($row = $result_acudientes->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="guardian-card">
                                    <div class="guardian-header">
                                        <div class="avatar-container"><i class="fas fa-user-friends"></i></div>
                                        <div>
                                            <h4 class="mb-0"><?php echo htmlspecialchars($row['nom_acudiente']); ?></h4>
                                            <small>ID: <?php echo htmlspecialchars($row['num_acudiente']); ?></small>
                                        </div>
                                    </div>
                                    <div class="student-details">
                                        <div class="student-info"><span class="info-label">Teléfono:</span><span class="info-value"><?php echo htmlspecialchars($row['telefono']); ?></span></div>
                                        <div class="student-info"><span class="info-label">Relación:</span><span class="info-value"><?php echo htmlspecialchars($row['relacion']); ?></span></div>
                                        <div class="student-info"><span class="info-label">Estudiante:</span><span class="info-value"><?php echo $row['nombre_estudiante'] ? htmlspecialchars($row['nombre_estudiante']) : '<span class="text-muted">Sin asignar</span>'; ?></span></div>
                                    </div>
                                    <div class="student-actions">
                                        <button class="btn btn-view" data-bs-toggle="modal" data-bs-target="#verAcudiente<?php echo $row['num_acudiente']; ?>"><i class="fas fa-eye me-1"></i> Ver</button>
                                        <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editarAcudiente<?php echo $row['num_acudiente']; ?>"><i class="fas fa-edit me-1"></i> Editar</button>
                                        <a href="?eliminar_acudiente=<?php echo $row['num_acudiente']; ?>" class="btn btn-delete" onclick="return confirm('¿Está seguro de eliminar este acudiente?')"><i class="fas fa-trash me-1"></i> Eliminar</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            generateViewModal('acudiente', $row['num_acudiente'], $row, [
                                'name' => 'nom_acudiente',
                                'display' => [
                                    'Código' => 'num_acudiente',
                                    'Teléfono' => 'telefono',
                                    'Relación' => 'relacion',
                                    'Estudiante' => 'nombre_estudiante'
                                ]
                            ], 'Detalles del Acudiente', 'fa-user-friends');
                            generateEditModal('acudiente', $row['num_acudiente'], $row, [
                                'id' => 'num_acudiente',
                                'inputs' => [
                                    'nom_acudiente' => 'Nombre',
                                    'telefono' => 'Teléfono',
                                    'relacion' => 'Relación',
                                    'cod_estudiante' => 'Estudiante'
                                ]
                            ], 'Editar Acudiente', 'fa-edit', $estudiantes_lista);
                            ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i>No hay acudientes registrados.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Crear Estudiante Modal -->
        <div class="modal fade" id="crearEstudianteModal" tabindex="-1" aria-labelledby="crearEstudianteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearEstudianteModalLabel"><i class="fas fa-plus me-2"></i>Nuevo Estudiante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($estudiante['nombres']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($estudiante['apellidos']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edad" class="form-label">Edad</label>
                                <input type="number" class="form-control" id="edad" name="edad" value="<?php echo htmlspecialchars($estudiante['edad']); ?>" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="n_grado" class="form-label">Grado</label>
                                <input type="text" class="form-control" id="n_grado" name="n_grado" value="<?php echo htmlspecialchars($estudiante['n_grado']); ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancelar</button>
                            <button type="submit" name="crear" class="btn btn-primary"><i class="fas fa-save me-1"></i> Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Crear Acudiente Modal -->
        <div class="modal fade" id="crearAcudienteModal" tabindex="-1" aria-labelledby="crearAcudienteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearAcudienteModalLabel"><i class="fas fa-plus me-2"></i>Nuevo Acudiente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nom_acudiente" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nom_acudiente" name="nom_acudiente" value="<?php echo htmlspecialchars($acudiente['nom_acudiente']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($acudiente['telefono']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="relacion" class="form-label">Relación</label>
                                <input type="text" class="form-control" id="relacion" name="relacion" value="<?php echo htmlspecialchars($acudiente['relacion']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="cod_estudiante" class="form-label">Estudiante</label>
                                <select class="form-select" id="cod_estudiante" name="cod_estudiante">
                                    <option value="">Seleccione un estudiante</option>
                                    <?php foreach ($estudiantes_lista as $estudiante): ?>
                                        <option value="<?php echo $estudiante['cod_estudiante']; ?>" <?php echo $acudiente['cod_estudiante'] == $estudiante['cod_estudiante'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($estudiante['apellidos'] . ' ' . $estudiante['nombres']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancelar</button>
                            <button type="submit" name="crear_acudiente" class="btn btn-primary"><i class="fas fa-save me-1"></i> Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupSearch(inputId, cardClass) {
            $(inputId).on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $(cardClass).filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        }
        setupSearch('#buscarEstudiante', '.student-card');
        setupSearch('#buscarAcudiente', '.guardian-card');
    </script>
</body>

</html>

<?php $conexion->close(); ?>