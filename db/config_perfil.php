<?php
// config_perfil.php
session_start();

include 'modal_invitado.php';

$guestControl = new GuestAccessControl($conexion);

// Verificación de sesión activa
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Importación de archivos necesarios
require_once 'conexion.php';
require_once 'header.php';

// Obtener datos del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$query = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesamiento del formulario de actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_perfil'])) {
        $nuevo_usuario = trim($_POST['usuario']);
        $nuevo_email = trim($_POST['email']);
        $password_actual = isset($_POST['password_actual']) ? $_POST['password_actual'] : '';
        $nueva_password = isset($_POST['nueva_password']) ? $_POST['nueva_password'] : '';
        $confirmar_password = isset($_POST['confirmar_password']) ? $_POST['confirmar_password'] : '';

        // Validar que el nombre de usuario y email no estén vacíos
        if (empty($nuevo_usuario) || empty($nuevo_email)) {
            $mensaje = "El nombre de usuario y el correo electrónico son obligatorios.";
            $tipo_mensaje = "danger";
        } else {
            // Verificar si se desea cambiar la contraseña
            if (!empty($password_actual) || !empty($nueva_password) || !empty($confirmar_password)) {
                // Verificar la contraseña actual
                if (password_verify($password_actual, $usuario['password'])) {
                    // Validar que la nueva contraseña y la confirmación coincidan
                    if ($nueva_password === $confirmar_password) {
                        // Actualizar usuario con nueva contraseña
                        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                        
                        $query_update = "UPDATE usuarios SET usuario = ?, email = ?, password = ? WHERE id_usuario = ?";
                        $stmt_update = $conexion->prepare($query_update);
                        $stmt_update->bind_param("sssi", $nuevo_usuario, $nuevo_email, $password_hash, $id_usuario);
                        
                        if ($stmt_update->execute()) {
                            $mensaje = "Perfil actualizado correctamente con nueva contraseña.";
                            $tipo_mensaje = "success";
                            
                            // Actualizar datos en la sesión
                            $_SESSION['usuario'] = $nuevo_usuario;
                            
                            // Refrescar los datos del usuario
                            $stmt->execute();
                            $resultado = $stmt->get_result();
                            $usuario = $resultado->fetch_assoc();
                        } else {
                            $mensaje = "Error al actualizar el perfil: " . $conexion->error;
                            $tipo_mensaje = "danger";
                        }
                        $stmt_update->close();
                    } else {
                        $mensaje = "La nueva contraseña y la confirmación no coinciden.";
                        $tipo_mensaje = "danger";
                    }
                } else {
                    $mensaje = "La contraseña actual es incorrecta.";
                    $tipo_mensaje = "danger";
                }
            } else {
                // Actualizar usuario sin cambiar la contraseña
                $query_update = "UPDATE usuarios SET usuario = ?, email = ? WHERE id_usuario = ?";
                $stmt_update = $conexion->prepare($query_update);
                $stmt_update->bind_param("ssi", $nuevo_usuario, $nuevo_email, $id_usuario);
                
                if ($stmt_update->execute()) {
                    $mensaje = "Perfil actualizado correctamente.";
                    $tipo_mensaje = "success";
                    
                    // Actualizar datos en la sesión
                    $_SESSION['usuario'] = $nuevo_usuario;
                    
                    // Refrescar los datos del usuario
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    $usuario = $resultado->fetch_assoc();
                } else {
                    $mensaje = "Error al actualizar el perfil: " . $conexion->error;
                    $tipo_mensaje = "danger";
                }
                $stmt_update->close();
            }
        }
    }

    // Procesamiento de eliminación de usuarios
    if (isset($_POST['delete_user']) && isset($_POST['user_id_to_delete'])) {
        $user_id_to_delete = $_POST['user_id_to_delete'];
        if ($user_id_to_delete != $id_usuario) { // No permitir eliminar al usuario actual
            $query_delete = "DELETE FROM usuarios WHERE id_usuario = ?";
            $stmt_delete = $conexion->prepare($query_delete);
            $stmt_delete->bind_param("i", $user_id_to_delete);
            if ($stmt_delete->execute()) {
                $mensaje = "Usuario eliminado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al eliminar el usuario: " . $conexion->error;
                $tipo_mensaje = "danger";
            }
            $stmt_delete->close();
        } else {
            $mensaje = "No puedes eliminar tu propio usuario.";
            $tipo_mensaje = "danger";
        }
    }
    
    // Procesamiento de edición de usuarios
    if (isset($_POST['edit_other_user'])) {
        $user_id_to_edit = $_POST['user_id_to_edit'];
        $nuevo_nombre_usuario = trim($_POST['edit_username']);
        $nuevo_email_usuario = trim($_POST['edit_email']);
        $nueva_password_usuario = trim($_POST['edit_password']);
        
        // Validar que el nombre de usuario y email no estén vacíos
        if (empty($nuevo_nombre_usuario) || empty($nuevo_email_usuario)) {
            $mensaje = "El nombre de usuario y el correo electrónico son obligatorios.";
            $tipo_mensaje = "danger";
        } else {
            // Verificar si se desea cambiar la contraseña
            if (!empty($nueva_password_usuario)) {
                // Actualizar usuario con nueva contraseña
                $password_hash = password_hash($nueva_password_usuario, PASSWORD_DEFAULT);
                
                $query_update = "UPDATE usuarios SET usuario = ?, email = ?, password = ? WHERE id_usuario = ?";
                $stmt_update = $conexion->prepare($query_update);
                $stmt_update->bind_param("sssi", $nuevo_nombre_usuario, $nuevo_email_usuario, $password_hash, $user_id_to_edit);
            } else {
                // Actualizar usuario sin cambiar la contraseña
                $query_update = "UPDATE usuarios SET usuario = ?, email = ? WHERE id_usuario = ?";
                $stmt_update = $conexion->prepare($query_update);
                $stmt_update->bind_param("ssi", $nuevo_nombre_usuario, $nuevo_email_usuario, $user_id_to_edit);
            }
            
            if ($stmt_update->execute()) {
                $mensaje = "Usuario actualizado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al actualizar el usuario: " . $conexion->error;
                $tipo_mensaje = "danger";
            }
            $stmt_update->close();
        }
    }
}

// Include sidebar
include_once("sidebar.php");

// Crear una instancia de la barra lateral
$nombreUsuario = htmlspecialchars($_SESSION["usuario"]);
$sidebar = new Sidebar($conexion, $nombreUsuario);

// Obtener lista de usuarios
$query_users = "SELECT id_usuario, usuario, email FROM usuarios WHERE id_usuario != ?";
$stmt_users = $conexion->prepare($query_users);
$stmt_users->bind_param("i", $id_usuario);
$stmt_users->execute();
$result_users = $stmt_users->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Perfil - ACUPRO</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="config_perfil.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="app-container">
        <?php $sidebar->render(); ?>

        <main class="main-content">
            <header class="content-header">
                <div class="welcome-section">
                    <h1>Configuración de <span class="brand-text">Perfil</span></h1>
                    <p class="subtitle">Administra tu información personal y preferencias</p>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn action-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-shield-lock"></i> Cambiar Contraseña
                    </button>
                    <button type="button" class="btn action-btn" data-bs-toggle="modal" data-bs-target="#manageUsersModal">
                        <i class="bi bi-people"></i> Gestionar Usuarios
                    </button>
                </div>
            </header>

            <!-- Alertas de mensajes -->
            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($usuario['usuario'], 0, 1)); ?>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h3 class="profile-name"><?php echo htmlspecialchars($usuario['usuario']); ?></h3>
                            <p class="profile-email"><?php echo htmlspecialchars($usuario['email']); ?></p>
                            <div class="d-grid gap-2">
                                <button class="btn action-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="bi bi-pencil"></i> Editar perfil
                                </button>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <p class="stat-value"><?php echo $usuario['id_usuario']; ?></p>
                                <p class="stat-label">ID Usuario</p>
                            </div>
                            <div class="stat-item">
                                <p class="stat-value"><?php 
                                // Obtener citas del usuario (ejemplo)
                                $query_citas = "SELECT COUNT(*) as total FROM citas";
                                $result_citas = $conexion->query($query_citas);
                                echo $result_citas ? $result_citas->fetch_assoc()['total'] : '0';
                                ?></p>
                                <p class="stat-label">Citas</p>
                            </div>
                            <div class="stat-item">
                                <p class="stat-value"><?php echo date('d/m/y'); ?></p>
                                <p class="stat-label">Último acceso</p>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="bi bi-shield-check"></i> Seguridad de la cuenta
                        </div>
                        <div class="info-card-body">
                            <div class="security-option">
                                <div>
                                    <h6 class="mb-0">Contraseña</h6>
                                    <small class="text-muted">********</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <div class="security-option">
                                <div>
                                    <h6 class="mb-0">Email verificado</h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($usuario['email']); ?></small>
                                </div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i>
                                </span>
                            </div>
                            <div class="security-option">
                                <div>
                                    <h6 class="mb-0">Verificación en dos pasos</h6>
                                    <small class="text-muted">No activado</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twoFactorSwitch">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h2 class="section-title"><i class="bi bi-person-badge"></i> Datos Personales</h2>
                    
                    <div class="info-card mb-4">
                        <div class="info-card-header">
                            <i class="bi bi-person-fill"></i> Información de Usuario
                        </div>
                        <div class="info-card-body">
                            <form method="POST" action="">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="usuario" class="form-label">Nombre de Usuario</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" name="actualizar_perfil" class="btn action-btn">
                                        <i class="bi bi-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <h2 class="section-title"><i class="bi bi-clock-history"></i> Actividad Reciente</h2>
                    
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="bi bi-activity"></i> Historial de Actividades
                        </div>
                        <div class="info-card-body">
                            <?php
                            // Consulta para obtener citas recientes (podrías personalizar esto según tus necesidades)
                            $query_citas = "SELECT * FROM citas ORDER BY fecha DESC LIMIT 5";
                            $resultado_citas = $conexion->query($query_citas);
                            
                            if ($resultado_citas && $resultado_citas->num_rows > 0) {
                                while ($cita = $resultado_citas->fetch_assoc()) {
                            ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h6 class="activity-title"><?php echo htmlspecialchars($cita['motivo']); ?></h6>
                                        <p class="activity-time">
                                            <i class="bi bi-clock"></i> <?php echo date('d/m/Y H:i', strtotime($cita['fecha'])); ?>
                                        </p>
                                    </div>
                                    <span class="badge <?php echo strtotime($cita['fecha']) < time() ? 'badge-completed' : 'badge-pending'; ?>">
                                        <?php echo strtotime($cita['fecha']) < time() ? 'Completada' : 'Pendiente'; ?>
                                    </span>
                                </div>
                            <?php
                                }
                            } else {
                            ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                    <p class="mt-3">No hay actividad reciente</p>
                                    <a href="read.php?openModal=crearCita" class="btn btn-sm action-btn">
                                        <i class="bi bi-plus-circle"></i> Agendar una cita
                                    </a>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php 
    // Incluir modales externos
    include_once("modales_perfil.php"); 
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript para manejar los modales
        document.addEventListener('DOMContentLoaded', function() {
            // Modal para editar usuario
            var editUserModal = document.getElementById('editUserModal');
            if (editUserModal) {
                editUserModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var userId = button.getAttribute('data-user-id');
                    var username = button.getAttribute('data-username');
                    var email = button.getAttribute('data-email');
                    
                    var userIdField = editUserModal.querySelector('#user_id_to_edit');
                    var usernameField = editUserModal.querySelector('#edit_username');
                    var emailField = editUserModal.querySelector('#edit_email');
                    
                    userIdField.value = userId;
                    usernameField.value = username;
                    emailField.value = email;
                });

                // Ensure backdrop is removed when modal is hidden
                editUserModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }
            
            // Modal para eliminar usuario
            var deleteUserModal = document.getElementById('deleteUserModal');
            if (deleteUserModal) {
                deleteUserModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var userId = button.getAttribute('data-user-id');
                    var username = button.getAttribute('data-username');
                    
                    var userIdField = deleteUserModal.querySelector('#user_id_to_delete');
                    var usernameSpan = deleteUserModal.querySelector('#username_to_delete');
                    
                    userIdField.value = userId;
                    usernameSpan.textContent = username;
                });

                // Ensure backdrop is removed when modal is hidden
                deleteUserModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }

            // Modal para cambiar contraseña
            var changePasswordModal = document.getElementById('changePasswordModal');
            if (changePasswordModal) {
                changePasswordModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }

            // Modal para editar perfil
            var editProfileModal = document.getElementById('editProfileModal');
            if (editProfileModal) {
                editProfileModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }

            // Modal para gestionar usuarios
            var manageUsersModal = document.getElementById('manageUsersModal');
            if (manageUsersModal) {
                manageUsersModal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }
        });
    </script>
</body>
</html>

<?php
// Cierre de conexión
$stmt->close();
$stmt_users->close();
$conexion->close();
?>