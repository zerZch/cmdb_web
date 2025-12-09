<div class="page-header">
    <h2><i class="fas fa-clipboard-list me-2"></i>Solicitudes de Devolución Pendientes</h2>
    <p class="text-muted">Revisa y valida las solicitudes de devolución de equipos enviadas por los colaboradores.</p>
</div>

<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-hourglass-half me-2"></i>Solicitudes Pendientes de Validación
            <?php if (!empty($solicitudes)): ?>
                <span class="badge bg-warning text-dark"><?= count($solicitudes) ?></span>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($solicitudes)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                No hay solicitudes de devolución pendientes en este momento.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover dataTable" id="tablaSolicitudesPendientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Colaborador</th>
                            <th>Equipo</th>
                            <th>Número de Serie</th>
                            <th>Fecha Solicitud</th>
                            <th>Motivo</th>
                            <th>Departamento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?= $solicitud['id'] ?></td>
                                <td>
                                    <strong><?= e($solicitud['colaborador_nombre']) ?></strong><br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i><?= e($solicitud['colaborador_email'] ?? 'N/A') ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= e($solicitud['equipo_nombre']) ?></strong><br>
                                    <small class="text-muted"><?= e($solicitud['marca'] . ' ' . $solicitud['modelo']) ?></small>
                                </td>
                                <td>
                                    <code><?= e($solicitud['numero_serie']) ?></code>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud_devolucion'])) ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php
                                        $diff = time() - strtotime($solicitud['fecha_solicitud_devolucion']);
                                        $hours = floor($diff / 3600);
                                        if ($hours < 1) {
                                            echo 'Hace ' . floor($diff / 60) . ' minutos';
                                        } elseif ($hours < 24) {
                                            echo 'Hace ' . $hours . ' horas';
                                        } else {
                                            echo 'Hace ' . floor($hours / 24) . ' días';
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $motivos = [
                                        'traslado' => '<span class="badge bg-info"><i class="fas fa-exchange-alt me-1"></i>Traslado</span>',
                                        'salida' => '<span class="badge bg-danger"><i class="fas fa-sign-out-alt me-1"></i>Salida</span>',
                                        'mal_estado' => '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Mal Estado</span>',
                                        'fin_proyecto' => '<span class="badge bg-secondary"><i class="fas fa-check me-1"></i>Fin Proyecto</span>',
                                        'otro' => '<span class="badge bg-secondary"><i class="fas fa-question me-1"></i>Otro</span>'
                                    ];
                                    echo $motivos[$solicitud['motivo_devolucion']] ?? '<span class="badge bg-secondary">N/A</span>';
                                    ?>
                                </td>
                                <td><?= e($solicitud['departamento'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="index.php?route=asignaciones&action=validarDevolucionForm&id=<?= $solicitud['id'] ?>"
                                       class="btn btn-sm btn-primary" title="Validar Devolución">
                                        <i class="fas fa-check-circle"></i> Validar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaSolicitudesPendientes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[4, 'asc']], // Ordenar por fecha de solicitud (más antigua primero)
        pageLength: 10
    });
});
</script>
