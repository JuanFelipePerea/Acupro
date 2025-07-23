<?php
// Contenido para crear_cita_modal.php
?>
<div class="modal fade" id="crearCitaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="read.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="fecha" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora</label>
                        <input type="time" class="form-control" name="hora_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <input type="text" class="form-control" name="motivo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documento del Docente: </label>
                        <input type="text" class="form-control" name="acc_docente" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Docente</label>
                        <input type="text" class="form-control" name="nom_docente" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" id="tipo-crear" name="tipo" onchange="mostrarCamposTipo('crear')" required>
                            <option value="">Seleccione</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="acudiente">Acudiente</option>
                        </select>
                    </div>
                    <div class="mb-3 form-group-dynamic" id="estudiante-crear" style="display:none;">
                        <label class="form-label">Estudiante</label>
                        <select class="form-select" name="cod_estudiante">
                            <option value="">Seleccione</option>
                            <?php
                            $estudiantes->data_seek(0);
                            while ($est = $estudiantes->fetch_assoc()) {
                                echo "<option value='{$est['cod_estudiante']}'>" . htmlspecialchars($est['nombre_completo']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3 form-group-dynamic" id="acudiente-crear" style="display:none;">
                        <label class="form-label">Acudiente</label>
                        <select class="form-select" name="num_acudiente">
                            <option value="">Seleccione</option>
                            <?php
                            $acudientes->data_seek(0);
                            while ($acu = $acudientes->fetch_assoc()) {
                                echo "<option value='{$acu['num_acudiente']}'>" . htmlspecialchars($acu['nom_acudiente']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>