<div class="page-header">
    <h2><i class="fas fa-clipboard-list me-2"></i>Solicitudes de Equipos</h2>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>

    <div class="d-flex gap-2">
        <!-- Botón: Mis solicitudes (para probar como colaborador) -->
        <a href="index.php?route=necesidades&action=misSolicitudes" class="btn btn-outline-secondary">
            <i class="fas fa-list me-1"></i> Mis solicitudes
        </a>

        <!-- Botón: Nueva solicitud -->
        <a href="index.php?route=necesidades&action=crear" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Nueva solicitud
        </a>
    </div>
</div>


<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Pendientes</h5>
                <h2><?= $estadisticas['pendientes'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Aprobadas</h5>
                <h2><?= $estadisticas['aprobadas'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Urgentes</h5>
                <h2><?= $estadisticas['urgentes'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Completadas</h5>
                <h2><?= $estadisticas['completadas'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Solicitudes -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Colaborador</th>
                        <th>Categoría</th>
                        <th>Tipo Equipo</th>
                        <th>Urgencia</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay solicitudes registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $sol): ?>
                            <tr>
                                <td><?= $sol['id'] ?></td>
                                <td>
                                    <strong><?= e($sol['colaborador_nombre']) ?></strong><br>
                                    <small class="text-muted"><?= e($sol['departamento']) ?></small>
                                </td>
                                <td><?= e($sol['categoria_nombre'] ?? 'N/A') ?></td>
                                <td><?= e($sol['tipo_equipo'] ?? 'No especificado') ?></td>
                                <td>
                                    <?php
                                    $urgenciaBadge = [
                                        'alta' => 'danger',
                                        'normal' => 'warning',
                                        'baja' => 'secondary'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $urgenciaBadge[$sol['urgencia']] ?>">
                                        <?= ucfirst($sol['urgencia']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $estadoBadge = [
                                        'pendiente' => 'warning',
                                        'aprobada' => 'success',
                                        'rechazada' => 'danger',
                                        'completada' => 'info'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $estadoBadge[$sol['estado']] ?>">
                                        <?= ucfirst($sol['estado']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($sol['fecha_solicitud'])) ?></td>
                                <td>
                                    <?php if ($sol['estado'] === 'pendiente'): ?>
                                        <button class="btn btn-sm btn-success" 
                                                onclick="aprobarSolicitud(<?= $sol['id'] ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="rechazarSolicitud(<?= $sol['id'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="index.php?route=necesidades&action=ver&id=<?= $sol['id'] ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales -->
<!-- Modal Aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php?route=necesidades&action=aprobar">
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="solicitud_id" id="aprobar_id">
                    <div class="mb-3">
                        <label>Observaciones (opcional)</label>
                        <textarea class="form-control" name="observaciones" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php?route=necesidades&action=rechazar">
                <div class="modal-header">
                    <h5 class="modal-title">Rechazar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="solicitud_id" id="rechazar_id">
                    <div class="mb-3">
                        <label>Motivo de Rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="motivo" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function aprobarSolicitud(id) {
    document.getElementById('aprobar_id').value = id;
    new bootstrap.Modal(document.getElementById('modalAprobar')).show();
}

function rechazarSolicitud(id) {
    document.getElementById('rechazar_id').value = id;
    new bootstrap.Modal(document.getElementById('modalRechazar')).show();
}
</script>