<div class="page-header">
    <h2><i class="fas fa-clipboard-list me-2"></i>Mis Solicitudes de Equipos</h2>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="index.php?route=necesidades&action=crear" class="btn btn-primary">
        <i class="fas fa-plus-circle me-1"></i> Nueva solicitud
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Tipo Equipo</th>
                        <th>Urgencia</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No has registrado ninguna solicitud de equipo.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $sol): ?>
                            <tr>
                                <td><?= $sol['id'] ?></td>
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
                                    <a href="index.php?route=necesidades&action=ver&id=<?= $sol['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Ver Detalles">
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