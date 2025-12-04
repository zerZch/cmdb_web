<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-trash-alt me-2"></i>Detalle de Baja</h2>
    <a href="index.php?route=bajas" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="row">
    <!-- Información del Equipo -->
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-laptop me-2"></i>Equipo Dado de Baja</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="40%"><strong>Nombre:</strong></td>
                        <td><?= e($baja['equipo_nombre']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Número de Serie:</strong></td>
                        <td><?= e($baja['numero_serie'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Marca/Modelo:</strong></td>
                        <td><?= e($baja['marca']) ?> / <?= e($baja['modelo']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Categoría:</strong></td>
                        <td><span class="badge bg-secondary"><?= e($baja['categoria']) ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha Adquisición:</strong></td>
                        <td>
                            <?php if ($baja['fecha_adquisicion']): ?>
                                <?= date('d/m/Y', strtotime($baja['fecha_adquisicion'])) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Valor Original:</strong></td>
                        <td>
                            <?php if ($baja['valor_adquisicion']): ?>
                                $<?= number_format($baja['valor_adquisicion'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Información de la Baja -->
        <div class="card mb-3">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Baja</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="40%"><strong>Fecha de Baja:</strong></td>
                        <td><?= date('d/m/Y', strtotime($baja['fecha_baja'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Motivo:</strong></td>
                        <td>
                            <span class="badge bg-info">
                                <?= e(ucfirst(str_replace('_', ' ', $baja['motivo_baja']))) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Valor Residual:</strong></td>
                        <td>
                            <?php if ($baja['valor_residual']): ?>
                                $<?= number_format($baja['valor_residual'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Método Disposición:</strong></td>
                        <td>
                            <?php if ($baja['metodo_disposicion']): ?>
                                <?= e(ucfirst($baja['metodo_disposicion'])) ?>
                            <?php else: ?>
                                <span class="text-muted">No especificado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($baja['empresa_disposicion']): ?>
                    <tr>
                        <td><strong>Empresa Disposición:</strong></td>
                        <td><?= e($baja['empresa_disposicion']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($baja['numero_acta']): ?>
                    <tr>
                        <td><strong>Número de Acta:</strong></td>
                        <td><code><?= e($baja['numero_acta']) ?></code></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Criterio Técnico y Estado -->
    <div class="col-lg-6">
        <!-- CRITERIO TÉCNICO (Requisito de Rúbrica) -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Criterio Técnico (Obligatorio)
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Justificación Técnica:</strong>
                </div>
                <div class="p-3 bg-light rounded">
                    <?= nl2br(e($baja['criterio_tecnico'])) ?>
                </div>
            </div>
        </div>

        <!-- Estado de Aprobación -->
        <div class="card mb-3">
            <div class="card-header bg-<?= 
                $baja['estado'] === 'pendiente' ? 'warning' : 
                ($baja['estado'] === 'aprobada' ? 'success' : 
                ($baja['estado'] === 'rechazada' ? 'danger' : 'info')) 
            ?> text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Estado de Aprobación</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3>
                        <span class="badge bg-<?= 
                            $baja['estado'] === 'pendiente' ? 'warning' : 
                            ($baja['estado'] === 'aprobada' ? 'success' : 
                            ($baja['estado'] === 'rechazada' ? 'danger' : 'info')) 
                        ?>" style="font-size: 1.5rem;">
                            <?= e(strtoupper($baja['estado'])) ?>
                        </span>
                    </h3>
                </div>

                <?php if ($baja['aprobador_nombre']): ?>
                <hr>
                <p class="mb-2">
                    <strong>Aprobado/Rechazado por:</strong><br>
                    <?= e($baja['aprobador_nombre'] . ' ' . $baja['aprobador_apellido']) ?>
                </p>
                <p class="mb-0">
                    <strong>Fecha de Aprobación:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($baja['fecha_aprobacion'])) ?>
                </p>
                <?php endif; ?>

                <?php if (hasRole(ROLE_ADMIN) && $baja['estado'] === 'pendiente'): ?>
                <hr>
                <div class="d-grid gap-2">
                    <button type="button" 
                            class="btn btn-success"
                            onclick="aprobarBaja(<?= $baja['id'] ?>)">
                        <i class="fas fa-check me-2"></i>Aprobar Baja
                    </button>
                    <button type="button" 
                            class="btn btn-danger"
                            onclick="rechazarBaja(<?= $baja['id'] ?>)">
                        <i class="fas fa-times me-2"></i>Rechazar Baja
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Responsable -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Responsable</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Nombre:</strong><br>
                    <?= e($baja['responsable_nombre'] . ' ' . $baja['responsable_apellido']) ?>
                </p>
                <p class="mb-0">
                    <strong>Email:</strong><br>
                    <a href="mailto:<?= e($baja['responsable_email']) ?>">
                        <?= e($baja['responsable_email']) ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Observaciones -->
        <?php if ($baja['observaciones']): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Observaciones</h5>
            </div>
            <div class="card-body">
                <?= nl2br(e($baja['observaciones'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function aprobarBaja(id) {
    Swal.fire({
        title: '¿Aprobar esta Baja?',
        text: 'Esta acción aprobará la baja del equipo',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('index.php?route=bajas&action=aprobar', { id: id }, function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Aprobada',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#1B3C53'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }, 'json');
        }
    });
}

function rechazarBaja(id) {
    Swal.fire({
        title: '¿Rechazar esta Baja?',
        text: 'Esta acción rechazará la solicitud de baja',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('index.php?route=bajas&action=rechazar', { id: id }, function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Rechazada',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#1B3C53'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }, 'json');
        }
    });
}
</script>
