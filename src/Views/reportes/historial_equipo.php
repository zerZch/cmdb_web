<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-history me-2"></i>Historial del Equipo</h2>
            <p class="text-muted mb-0">Trazabilidad Completa - Ciclo de Vida del Activo</p>
        </div>
        <a href="index.php?route=reportes&action=historialEquipo" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<!-- Información del Equipo -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-laptop me-2"></i>Información del Equipo</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Nombre:</strong><br>
                <?= e($equipo['nombre']) ?>
            </div>
            <div class="col-md-3">
                <strong>Número de Serie:</strong><br>
                <?= e($equipo['numero_serie'] ?? 'N/A') ?>
            </div>
            <div class="col-md-3">
                <strong>Marca/Modelo:</strong><br>
                <?= e($equipo['marca'] ?? 'N/A') ?> / <?= e($equipo['modelo'] ?? 'N/A') ?>
            </div>
            <div class="col-md-3">
                <strong>Estado Actual:</strong><br>
                <span class="badge bg-<?= 
                    $equipo['estado'] === 'disponible' ? 'success' : 
                    ($equipo['estado'] === 'asignado' ? 'info' : 
                    ($equipo['estado'] === 'dañado' ? 'danger' : 'secondary')) 
                ?>">
                    <?= e(ucfirst($equipo['estado'])) ?>
                </span>
            </div>
        </div>

        <?php if ($ultimoMovimiento): ?>
        <hr>
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Último Movimiento:</strong> 
            <?= e(ucfirst($ultimoMovimiento['tipo_movimiento'])) ?> 
            el <?= date('d/m/Y H:i', strtotime($ultimoMovimiento['created_at'])) ?>
            <?php if ($ultimoMovimiento['usuario_nombre']): ?>
                por <?= e($ultimoMovimiento['usuario_nombre']) ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Timeline de Movimientos (Trazabilidad Completa) -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-stream me-2"></i>
            Línea de Tiempo - Trazabilidad Completa
            <span class="badge bg-secondary"><?= count($historial) ?> movimientos registrados</span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($historial)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No hay movimientos registrados para este equipo.
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($historial as $index => $movimiento): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-<?= $movimiento['badge_color'] ?>">
                            <i class="fas <?= $movimiento['icon'] ?>"></i>
                        </div>
                        
                        <div class="timeline-content">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">
                                            <span class="badge bg-<?= $movimiento['badge_color'] ?>">
                                                <?= e(ucfirst($movimiento['tipo_movimiento'])) ?>
                                            </span>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($movimiento['created_at'])) ?>
                                        </small>
                                    </div>

                                    <div class="row">
                                        <?php if ($movimiento['estado_anterior'] || $movimiento['estado_nuevo']): ?>
                                        <div class="col-md-6">
                                            <small class="text-muted">Cambio de Estado:</small><br>
                                            <?php if ($movimiento['estado_anterior']): ?>
                                                <span class="badge bg-secondary"><?= e($movimiento['estado_anterior']) ?></span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                            <?php endif; ?>
                                            <?php if ($movimiento['estado_nuevo']): ?>
                                                <span class="badge bg-primary"><?= e($movimiento['estado_nuevo']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($movimiento['usuario_nombre']): ?>
                                        <div class="col-md-6">
                                            <small class="text-muted">Responsable:</small><br>
                                            <i class="fas fa-user me-1"></i>
                                            <?= e($movimiento['usuario_nombre'] . ' ' . $movimiento['usuario_apellido']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($movimiento['colaborador_nombre']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Colaborador:</small><br>
                                        <i class="fas fa-user-tie me-1"></i>
                                        <?= e($movimiento['colaborador_nombre']) ?>
                                        <?php if ($movimiento['colaborador_departamento']): ?>
                                            <small class="text-muted">(<?= e($movimiento['colaborador_departamento']) ?>)</small>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($movimiento['observaciones']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Observaciones:</small><br>
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-comment-alt me-1"></i>
                                            <?= e($movimiento['observaciones']) ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Botones de Acción -->
<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex gap-2">
            <a href="index.php?route=reportes&action=exportarHistorialPdf&equipo_id=<?= $equipo['id'] ?>" 
               class="btn btn-danger"
               target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
            </a>
            
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>
</div>

<style>
/* Estilos de Timeline */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #1B3C53, #456882);
}

.timeline-item {
    position: relative;
    padding-left: 80px;
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 15px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 1;
}

.timeline-content {
    position: relative;
}

@media print {
    .btn, .page-header a {
        display: none !important;
    }
    
    .timeline-item {
        page-break-inside: avoid;
    }
}
</style>
