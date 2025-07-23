<?php
// modales_perfil.php - Modales para la configuración de perfil
?>

<!-- Modal para Cambiar Contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-shield-lock"></i> Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_actual">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="nueva_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> La contraseña debe tener al menos 8 caracteres
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_password" class="form-label">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmar_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-shield-check"></i>
                        <strong>Consejo de seguridad:</strong> Usa una combinación de letras mayúsculas, minúsculas, números y símbolos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" name="actualizar_perfil" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="bi bi-person-gear"></i> Editar Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="profile-avatar-large">
                            <?php echo strtoupper(substr($usuario['usuario'], 0, 1)); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_usuario" class="form-label">Nombre de Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="edit_usuario" name="usuario" 
                                   value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="edit_email" name="email" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Nota:</strong> Los cambios se aplicarán inmediatamente después de guardar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" name="actualizar_perfil" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Gestionar Usuarios -->
<div class="modal fade" id="manageUsersModal" tabindex="-1" aria-labelledby="manageUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageUsersModalLabel">
                    <i class="bi bi-people"></i> Gestionar Usuarios
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Lista de Usuarios Registrados</h6>
                    <span class="badge bg-primary">
                        <?php echo $result_users->num_rows; ?> usuarios
                    </span>
                </div>
                
                <?php if ($result_users->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="bi bi-hash"></i> ID</th>
                                    <th><i class="bi bi-person"></i> Usuario</th>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <th><i class="bi bi-gear"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Resetear el puntero del resultado
                                $result_users->data_seek(0);
                                while($user = $result_users->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?php echo $user['id_usuario']; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small me-2">
                                                <?php echo strtoupper(substr($user['usuario'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($user['usuario']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                    data-user-id="<?php echo $user['id_usuario']; ?>"
                                                    data-username="<?php echo htmlspecialchars($user['usuario']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                    data-user-id="<?php echo $user['id_usuario']; ?>"
                                                    data-username="<?php echo htmlspecialchars($user['usuario']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <p class="mt-3">No hay otros usuarios registrados</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="bi bi-person-gear"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="user_id_to_edit" name="user_id_to_edit">
                    
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Nombre de Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="edit_username" name="edit_username" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="edit_email" name="edit_email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nueva Contraseña (opcional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="edit_password" name="edit_password" 
                                   placeholder="Dejar vacío para mantener la contraseña actual">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="edit_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Solo completa este campo si deseas cambiar la contraseña
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> Los cambios se aplicarán inmediatamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" name="edit_other_user" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="user_id_to_delete" name="user_id_to_delete">
                    
                    <div class="text-center mb-4">
                        <i class="bi bi-person-x fs-1 text-danger"></i>
                    </div>
                    
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle"></i> ¡Atención!
                        </h6>
                        <p class="mb-0">
                            Estás a punto de eliminar permanentemente al usuario 
                            <strong><span id="username_to_delete"></span></strong>.
                        </p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i>
                        <strong>Esta acción no se puede deshacer.</strong> 
                        Todos los datos asociados a este usuario se eliminarán permanentemente.
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                        <label class="form-check-label" for="confirmDelete">
                            Entiendo que esta acción es irreversible
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" name="delete_user" class="btn btn-danger" id="deleteUserBtn" disabled>
                        <i class="bi bi-trash"></i> Eliminar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                targetInput.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });
    
    // Confirm delete checkbox
    const confirmDeleteCheckbox = document.getElementById('confirmDelete');
    const deleteUserBtn = document.getElementById('deleteUserBtn');
    
    if (confirmDeleteCheckbox && deleteUserBtn) {
        confirmDeleteCheckbox.addEventListener('change', function() {
            deleteUserBtn.disabled = !this.checked;
        });
    }
    
    // Reset confirm delete when modal is hidden
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('hidden.bs.modal', function() {
            if (confirmDeleteCheckbox) {
                confirmDeleteCheckbox.checked = false;
            }
            if (deleteUserBtn) {
                deleteUserBtn.disabled = true;
            }
        });
    }
    
    // Password validation
    const newPassword = document.getElementById('nueva_password');
    const confirmPassword = document.getElementById('confirmar_password');
    
    if (newPassword && confirmPassword) {
        function validatePasswords() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    }
});
</script>