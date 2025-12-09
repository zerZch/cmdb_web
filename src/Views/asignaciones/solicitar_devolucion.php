<div class="page-header">
    <h2><i class="fas fa-undo me-2"></i>Solicitar Devolución de Equipo</h2>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Información del Equipo a Devolver</h5>
            </div>
            <div class="card-body">
                <!-- Información del Equipo -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Equipo:</strong> <?= e($equipo['nombre']) ?></p>
                        <p><strong>Marca/Modelo:</strong> <?= e($equipo['marca'] . ' ' . $equipo['modelo']) ?></p>
                        <p><strong>Número de Serie:</strong> <?= e($equipo['numero_serie']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha de Asignación:</strong> <?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></p>
                        <p><strong>Estado Actual:</strong> <span class="badge bg-success">Asignado</span></p>
                    </div>
                </div>

                <hr>

                <!-- Formulario de Solicitud -->
                <form method="POST" action="index.php?route=asignaciones&action=guardarSolicitudDevolucion">
                    <input type="hidden" name="asignacion_id" value="<?= $asignacion['id'] ?>">

                    <div class="mb-3">
                        <label for="motivo" class="form-label">
                            <i class="fas fa-question-circle me-1"></i>Motivo de la Devolución <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="motivo" name="motivo" required>
                            <option value="">-- Seleccione un motivo --</option>
                            <option value="traslado">Traslado de ubicación/departamento</option>
                            <option value="salida">Salida de la empresa (Renuncia/Despido)</option>
                            <option value="mal_estado">Equipo en mal estado / No funciona correctamente</option>
                            <option value="fin_proyecto">Fin de proyecto</option>
                            <option value="otro">Otro motivo</option>
                        </select>
                        <small class="text-muted">
                            Selecciona el motivo por el cual deseas devolver el equipo.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">
                            <i class="fas fa-comment-alt me-1"></i>Observaciones
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="4"
                                  placeholder="Describe el motivo de la devolución o el estado del equipo (opcional)"></textarea>
                        <small class="text-muted">
                            Si el equipo tiene algún problema, descríbelo aquí.
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Una vez enviada la solicitud, un administrador deberá validar la devolución
                        y recibirá físicamente el equipo. El equipo pasará a revisión técnica antes de ser reasignado.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?route=asignaciones&action=misEquipos" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane me-1"></i>Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
