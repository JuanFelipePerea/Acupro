<?php
// Contenido para m_editar_cita.php
?>
<div class="modal fade" id="editarCitaModal<?php echo $row['num_cita']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Cita: <?php echo htmlspecialchars($row['nom_docente']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="read.php" method="POST">
                    <input type="hidden" name="num_cita" value="<?php echo htmlspecialchars($row['num_cita']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="fecha" value="<?php echo htmlspecialchars($row['fecha_only']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora</label>
                        <input type="time" class="form-control" name="hora_inicio" value="<?php echo htmlspecialchars($row['hora_only']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <input type="text" class="form-control" name="motivo" value="<?php echo htmlspecialchars($row['motivo']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documento del Docente: </label>
                        <input type="text" class="form-control" name="acc_docente" value="<?php echo htmlspecialchars($row['acc_docente']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Docente</label>
                        <input type="text" class="form-control" name="nom_docente" value="<?php echo htmlspecialchars($row['nom_docente']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" id="tipo-editar-<?php echo $row['num_cita']; ?>" name="tipo" onchange="mostrarCamposTipo('editar-<?php echo $row['num_cita']; ?>')" required>
                            <option value="estudiante" <?php echo $row['tipo'] == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                            <option value="acudiente" <?php echo $row['tipo'] == 'acudiente' ? 'selected' : ''; ?>>Acudiente</option>
                        </select>
                    </div>
                    <div class="mb-3 form-group-dynamic" id="estudiante-editar-<?php echo $row['num_cita']; ?>" style="display:<?php echo $row['tipo'] == 'estudiante' ? 'block' : 'none'; ?>;">
                        <label class="form-label">Estudiante</label>
                        <select class="form-select" name="cod_estudiante">
                            <option value="">Seleccione</option>
                            <?php
                            $estudiantes->data_seek(0);
                            while ($est = $estudiantes->fetch_assoc()) {
                                echo "<option value='{$est['cod_estudiante']}' " . ($row['cod_estudiante'] == $est['cod_estudiante'] ? 'selected' : '') . ">" . htmlspecialchars($est['nombre_completo']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3 form-group-dynamic" id="acudiente-editar-<?php echo $row['num_cita']; ?>" style="display:<?php echo $row['tipo'] == 'acudiente' ? 'block' : 'none'; ?>;">
                        <label class="form-label">Acudiente</label>
                        <select class="form-select" name="num_acudiente">
                            <option value="">Seleccione</option>
                            <?php
                            $acudientes->data_seek(0);
                            while ($acu = $acudientes->fetch_assoc()) {
                                echo "<option value='{$acu['num_acudiente']}' " . ($row['num_acudiente'] == $acu['num_acudiente'] ? 'selected' : '') . ">" . htmlspecialchars($acu['nom_acudiente']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>