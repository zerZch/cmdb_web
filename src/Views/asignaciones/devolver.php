<div class="page-header">
    <h2><i class="fas fa-undo me-2"></i>Devolver Equipo</h2>
</div>

<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Información de la Asignación</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Equipo:</strong> <?= e($asignacion['equipo_nombre']) ?></p>
                <p><strong>Número de Serie:</strong> <?= e($asignacion['numero_serie']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Colaborador:</strong> <?= e($asignacion['colaborador_nombre'] . ' ' . $asignacion['colaborador_apellido']) ?></p>
                <p><strong>Fecha Asignación:</strong> <?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?route=asignaciones&action=devolver">
            <input type="hidden" name="asignacion_id" value="<?= $asignacion['id'] ?>">

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Importante:</strong> La observación de devolución es <strong>obligatoria</strong>.
                Debe incluir el estado del equipo y cualquier incidencia.
            </div>

            <div class="mb-3">
                <label for="motivo_devolucion" class="form-label">Motivo de Devolución</label>
                <select class="form-select" id="motivo_devolucion" name="motivo_devolucion">
                    <option value="">-- Seleccionar --</option>
                    <option value="fin_proyecto">Fin de Proyecto</option>
                    <option value="cambio_puesto">Cambio de Puesto</option>
                    <option value="equipo_dañado">Equipo Dañado</option>
                    <option value="upgrade">Upgrade de Equipo</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="estado_equipo" class="form-label">
                    Estado del Equipo al Devolverlo <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="estado_equipo" name="estado_equipo" required>
                    <option value="disponible">Disponible (buen estado)</option>
                    <option value="mantenimiento">En Mantenimiento</option>
                    <option value="dañado">Dañado</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="observaciones_devolucion" class="form-label">
                    Observaciones de Devolución <span class="text-danger">* OBLIGATORIO</span>
                </label>
                <textarea class="form-control"
                          id="observaciones_devolucion"
                          name="observaciones_devolucion"
                          rows="5"
                          required
                          placeholder="Describa el estado del equipo, cualquier daño, desgaste, o incidencia..."></textarea>
                <small class="text-muted">Mínimo 20 caracteres</small>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?route=asignaciones" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-undo me-2"></i>Procesar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validar longitud de observación
    $('form').on('submit', function(e) {
        const obs = $('#observaciones_devolucion').val();
        if (obs.length < 20) {
            e.preventDefault();
            Swal.fire({
                title: 'Observación Insuficiente',
                text: 'La observación debe tener al menos 20 caracteres.',
                icon: 'warning',
                confirmButtonColor: '#1B3C53'
            });
            return false;
        }
    });
});
</script>