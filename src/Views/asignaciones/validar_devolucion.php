<div class="page-header">
    <h2><i class="fas fa-check-circle me-2"></i>Validar Devolución de Equipo</h2>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Información de la Solicitud</h5>
            </div>
            <div class="card-body">
                <!-- Información del Colaborador -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-user me-2"></i>Colaborador</h6>
                        <p><strong>Nombre:</strong> <?= e($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?></p>
                        <p><strong>Departamento:</strong> <?= e($colaborador['departamento'] ?? 'N/A') ?></p>
                        <p><strong>Email:</strong> <?= e($colaborador['email'] ?? 'N/A') ?></p>
                        <p><strong>Teléfono:</strong> <?= e($colaborador['telefono'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-laptop me-2"></i>Equipo a Devolver</h6>
                        <p><strong>Equipo:</strong> <?= e($equipo['nombre']) ?></p>
                        <p><strong>Marca/Modelo:</strong> <?= e($equipo['marca'] . ' ' . $equipo['modelo']) ?></p>
                        <p><strong>Número de Serie:</strong> <code><?= e($equipo['numero_serie']) ?></code></p>
                        <p><strong>Estado Actual:</strong> <span class="badge bg-warning"><?= e($equipo['estado']) ?></span></p>
                    </div>
                </div>

                <hr>

                <!-- Información de la Solicitud -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Detalles de la Solicitud</h6>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha de Asignación:</strong> <?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></p>
                        <p><strong>Fecha de Solicitud:</strong> <?= date('d/m/Y H:i', strtotime($asignacion['fecha_solicitud_devolucion'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Motivo de Devolución:</strong>
                            <?php
                            $motivos = [
                                'traslado' => '<span class="badge bg-info">Traslado</span>',
                                'salida' => '<span class="badge bg-danger">Salida de la Empresa</span>',
                                'mal_estado' => '<span class="badge bg-warning text-dark">Mal Estado</span>',
                                'fin_proyecto' => '<span class="badge bg-secondary">Fin de Proyecto</span>',
                                'otro' => '<span class="badge bg-secondary">Otro</span>'
                            ];
                            echo $motivos[$asignacion['motivo_devolucion']] ?? '<span class="badge bg-secondary">N/A</span>';
                            ?>
                        </p>
                    </div>
                    <div class="col-12">
                        <p><strong>Observaciones del Colaborador:</strong></p>
                        <div class="alert alert-light">
                            <?= nl2br(e($asignacion['observaciones'] ?? 'Sin observaciones')) ?>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Formulario de Validación -->
                <form method="POST" action="index.php?route=asignaciones&action=procesarValidacion" id="formValidacion">
                    <input type="hidden" name="asignacion_id" value="<?= $asignacion['id'] ?>">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="estado_equipo" class="form-label">
                                <i class="fas fa-clipboard-list me-1"></i>Estado del Equipo Recibido <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="estado_equipo" name="estado_equipo" required>
                                <option value="en_revision" selected>En Revisión (Default)</option>
                                <option value="disponible">Disponible (Si está en buen estado)</option>
                                <option value="dañado">Dañado (Si tiene problemas)</option>
                                <option value="mantenimiento">Mantenimiento (Requiere reparación)</option>
                            </select>
                            <small class="text-muted">
                                Selecciona el estado en el que recibes el equipo.
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-user-check me-1"></i>Usuario que Recibe
                            </label>
                            <input type="text" class="form-control" value="<?= e(currentUser()['nombre'] . ' ' . currentUser()['apellido']) ?>" disabled>
                            <small class="text-muted">
                                Tú serás registrado como el usuario que valida esta devolución.
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="form-label">
                            <i class="fas fa-comment-alt me-1"></i>Observaciones de Validación <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="4" required
                                  placeholder="Describe el estado del equipo recibido, si tiene daños, piezas faltantes, etc."></textarea>
                        <small class="text-muted">
                            Describe las condiciones en que se recibe el equipo.
                        </small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Al validar la devolución:
                        <ul class="mb-0 mt-2">
                            <li>La asignación se marcará como <strong>devuelta</strong></li>
                            <li>El equipo cambiará al estado seleccionado</li>
                            <li>Se registrará en el historial de movimientos</li>
                            <li>El equipo podrá ser reasignado después de la revisión técnica</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="index.php?route=asignaciones&action=devolucionesPendientes" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                        <div>
                            <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#modalRechazar">
                                <i class="fas fa-times-circle me-1"></i>Rechazar Solicitud
                            </button>
                            <button type="submit" name="accion" value="validar" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i>Validar y Recibir Equipo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1" aria-labelledby="modalRechazarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRechazarLabel">
                    <i class="fas fa-times-circle me-2"></i>Rechazar Solicitud de Devolución
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?route=asignaciones&action=procesarValidacion">
                <div class="modal-body">
                    <input type="hidden" name="asignacion_id" value="<?= $asignacion['id'] ?>">
                    <input type="hidden" name="accion" value="rechazar">

                    <p class="text-danger">
                        <strong>¿Estás seguro de rechazar esta solicitud?</strong>
                    </p>
                    <p>El colaborador deberá seguir usando el equipo o solicitar la devolución nuevamente.</p>

                    <div class="mb-3">
                        <label for="observaciones_rechazo" class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="observaciones_rechazo" name="observaciones" rows="3" required
                                  placeholder="Explica por qué se rechaza la solicitud..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
