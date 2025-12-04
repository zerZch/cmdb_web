<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-trash-alt me-2"></i>Gestión de Bajas de Equipos</h2>
    <a href="index.php?route=bajas&action=crear" class="btn btn-danger">
        <i class="fas fa-plus me-2"></i>Registrar Baja
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha Baja</th>
                    <th>Equipo</th>
                    <th>Categoría</th>
                    <th>Motivo</th>
                    <th>Valor Residual</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bajas as $baja): ?>
                <tr>
                    <td><?= $baja['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($baja['fecha_baja'])) ?></td>
                    <td>
                        <strong><?= e($baja['equipo_nombre']) ?></strong><br>
                        <small class="text-muted"><?= e($baja['numero_serie']) ?></small>
                    </td>
                    <td><span class="badge bg-secondary"><?= e($baja['categoria']) ?></span></td>
                    <td>
                        <span class="badge bg-<?= 
                            $baja['motivo_baja'] === 'obsolescencia' ? 'warning' : 
                            ($baja['motivo_baja'] === 'daño_irreparable' ? 'danger' : 'info') 
                        ?>">
                            <?= e(ucfirst(str_replace('_', ' ', $baja['motivo_baja']))) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($baja['valor_residual']): ?>
                            $<?= number_format($baja['valor_residual'], 2) ?>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $baja['estado'] === 'pendiente' ? 'warning' : 
                            ($baja['estado'] === 'aprobada' ? 'success' : 
                            ($baja['estado'] === 'rechazada' ? 'danger' : 'info')) 
                        ?>">
                            <?= e(ucfirst($baja['estado'])) ?>
                        </span>
                    </td>
                    <td><?= e($baja['responsable_nombre'] . ' ' . $baja['responsable_apellido']) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="index.php?route=bajas&action=ver&id=<?= $baja['id'] ?>"
                               class="btn btn-sm btn-info"
                               title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </a>

                            <?php if (hasRole(ROLE_ADMIN) && $baja['estado'] === 'pendiente'): ?>
                                <button type="button"
                                        class="btn btn-sm btn-success btn-aprobar"
                                        data-id="<?= $baja['id'] ?>"
                                        title="Aprobar">
                                    <i class="fas fa-check"></i>
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-danger btn-rechazar"
                                        data-id="<?= $baja['id'] ?>"
                                        title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Aprobar baja
    $('.btn-aprobar').click(function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Aprobar esta baja?',
            text: 'El equipo será marcado como dado de baja definitivamente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=bajas&action=aprobar', {
                    id: id
                }, function(response) {
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
    });

    // Rechazar baja
    $('.btn-rechazar').click(function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Rechazar esta baja?',
            text: 'La solicitud de baja será rechazada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=bajas&action=rechazar', {
                    id: id
                }, function(response) {
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
    });
});
</script>
