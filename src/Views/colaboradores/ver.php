<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-user me-2"></i>Detalle del Colaborador</h2>
        <div class="d-flex gap-2">
            <a href="index.php?route=colaboradores&action=editar&id=<?= $colaborador['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="index.php?route=colaboradores" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Información Personal -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Información Personal</h5>
            </div>
            <div class="card-body text-center">
                <?php if (!empty($colaborador['foto_perfil'])): ?>
                    <img src="<?= e($colaborador['foto_perfil']) ?>" 
                         alt="Foto" 
                         class="rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 150px; height: 150px; font-size: 60px;">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>

                <h4><?= e($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?></h4>
                
                <span class="badge bg-<?= 
                    $colaborador['estado'] === 'activo' ? 'success' : 
                    ($colaborador['estado'] === 'inactivo' ? 'secondary' : 'warning') 
                ?> fs-6 mb-3">
                    <?= e(ucfirst($colaborador['estado'])) ?>
                </span>

                <hr>

                <div class="text-start">
                    <?php if ($colaborador['cedula']): ?>
                    <p class="mb-2">
                        <strong><i class="fas fa-id-card me-2"></i>Cédula:</strong><br>
                        <?= e($colaborador['cedula']) ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($colaborador['email']): ?>
                    <p class="mb-2">
                        <strong><i class="fas fa-envelope me-2"></i>Email:</strong><br>
                        <a href="mailto:<?= e($colaborador['email']) ?>"><?= e($colaborador['email']) ?></a>
                    </p>
                    <?php endif; ?>

                    <?php if ($colaborador['telefono']): ?>
                    <p class="mb-2">
                        <strong><i class="fas fa-phone me-2"></i>Teléfono:</strong><br>
                        <?= e($colaborador['telefono']) ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($colaborador['fecha_ingreso']): ?>
                    <p class="mb-0">
                        <strong><i class="fas fa-calendar me-2"></i>Fecha Ingreso:</strong><br>
                        <?= date('d/m/Y', strtotime($colaborador['fecha_ingreso'])) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Información Laboral</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Cargo:</strong><br>
                        <?= e($colaborador['cargo'] ?? 'No especificado') ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <strong>Departamento:</strong><br>
                        <?= e($colaborador['departamento'] ?? 'No especificado') ?>
                    </div>

                    <div class="col-md-12 mb-3">
                        <strong>Ubicación:</strong><br>
                        <i class="fas fa-map-marker-alt me-2"></i><?= e($colaborador['ubicacion'] ?? 'No especificada') ?>
                    </div>

                    <?php if ($colaborador['observaciones']): ?>
                    <div class="col-md-12">
                        <strong>Observaciones:</strong><br>
                        <div class="alert alert-info">
                            <?= nl2br(e($colaborador['observaciones'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Equipos Asignados -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-laptop me-2"></i>Equipos Asignados Actualmente
            <span class="badge bg-info"><?= count($equiposAsignados) ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($equiposAsignados)): ?>
            <div class="alert alert-warning mb-0">
                <i class="fas fa-info-circle me-2"></i>
                No tiene equipos asignados actualmente.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Categoría</th>
                            <th>Número Serie</th>
                            <th>Fecha Asignación</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equiposAsignados as $equipo): ?>
                        <tr>
                            <td><strong><?= e($equipo['nombre']) ?></strong></td>
                            <td><span class="badge bg-secondary"><?= e($equipo['categoria']) ?></span></td>
                            <td><?= e($equipo['numero_serie'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y', strtotime($equipo['fecha_asignacion'])) ?></td>
                            <td><?= e($equipo['obs_asignacion'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Historial de Movimientos -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>Historial de Movimientos
            <span class="badge bg-secondary"><?= count($historial) ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($historial)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                No hay movimientos registrados para este colaborador.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Equipo</th>
                            <th>Responsable</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $mov): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $mov['tipo_movimiento'] === 'asignacion' ? 'info' : 
                                    ($mov['tipo_movimiento'] === 'devolucion' ? 'warning' : 'secondary') 
                                ?>">
                                    <?= e(ucfirst($mov['tipo_movimiento'])) ?>
                                </span>
                            </td>
                            <td><?= e($mov['equipo_nombre']) ?></td>
                            <td><?= e($mov['usuario_responsable'] ?? 'Sistema') ?></td>
                            <td><?= e($mov['observaciones'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
