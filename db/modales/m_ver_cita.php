<?php
// Estos archivos se incluirán en la carpeta modales para reducir el tamaño de read.php

// Contenido para ver_cita_modal.php
?>
<div class="modal fade" id="verCitaModal<?php echo $row['num_cita']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Cita: <?php echo htmlspecialchars($row['nom_docente']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                <p><strong>Fecha:</strong> <span class="<?php echo $row['fecha_only'] == date("Y-m-d") ? 'text-primary' : ''; ?>">
                        <?php echo htmlspecialchars($row['fecha_only']) . ($row['fecha_only'] == date("Y-m-d") ? ' (HOY)' : ''); ?>
                    </span></p>
                <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_only']); ?></p>
                <p><strong>Motivo:</strong> <?php echo htmlspecialchars($row['motivo']); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p><strong>Documento del Docente: :</strong> <?php echo htmlspecialchars($row['acc_docente']); ?></p>
                <p><strong>Docente:</strong> <?php echo htmlspecialchars($row['nom_docente']); ?></p>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($row['tipo']); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>