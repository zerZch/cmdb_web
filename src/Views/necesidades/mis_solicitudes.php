<div class="page-header">
    <h2><i class="fas fa-clipboard-list me-2"></i>Mis Solicitudes</h2>
</div>

<div class="mb-3">
    <a href="index.php?route=necesidades&action=crear" class="btn btn-primary">
        <i class="fas fa-plus-circle me-1"></i> Nueva solicitud
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($solicitudes)): ?>
            <p class="text-muted mb-0">Aún no has registrado solicitudes.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Tipo equipo</th>
                            <th>Urgencia</th>
                            <th>Estado</th>
                            <th>Fecha solicitud</th>
                            <th>Respuesta / Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $sol): ?>
                            <tr>
                                <td><?= $sol['id'] ?></td>
                                <td><?= e($sol['categoria_nombre'] ?? 'N/A') ?></td>
                                <td><?= e($sol['tipo_equipo'] ?? 'No especificado') ?></td>
                                <td>
                                    <?php
                                    $urgenciaBadge = [
                                        'alta'   => 'danger',
                                        'normal' => 'warning',
                                        'baja'   => 'secondary'
                                    ];
                                    $urg = $sol['urgencia'] ?? 'normal';
                                    ?>
                                    <span class="badge bg-<?= $urgenciaBadge[$urg] ?? 'secondary' ?>">
                                        <?= ucfirst($urg) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $estadoBadge = [
                                        'pendiente'  => 'warning',
                                        'aprobada'   => 'success',
                                        'rechazada'  => 'danger',
                                        'completada' => 'info'
                                    ];
                                    $estado = $sol['estado'] ?? 'pendiente';
                                    ?>
                                    <span class="badge bg-<?= $estadoBadge[$estado] ?? 'secondary' ?>">
                                        <?= ucfirst($estado) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= !empty($sol['fecha_solicitud']) 
                                            ? date('d/m/Y', strtotime($sol['fecha_solicitud'])) 
                                            : '-' ?>
                                </td>
                                <td>
                                    <?php if (!empty($sol['observaciones_admin'])): ?>
                                        <small class="text-muted">
                                            <?= e($sol['observaciones_admin']) ?>
                                            <?php if (!empty($sol['admin_nombre'])): ?>
                                                <br><strong>Atendido por:</strong> <?= e($sol['admin_nombre']) ?>
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">
                                            <?= $estado === 'pendiente' 
                                                ? 'En espera de revisión'
                                                : 'Sin comentarios' ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
