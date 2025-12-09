<?php 
// Vista de Detalle de Solicitud (necesidades/ver.php)
/** @var array $necesidad */
/** @var string $pageTitle */

?>
<div class="container-fluid">
    <h1><?= $pageTitle ?? 'Detalle de Solicitud' ?></h1>

    <?php if (!empty($necesidad)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Solicitud #<?= $necesidad['id'] ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha de Solicitud:</strong> <?= $necesidad['fecha_solicitud'] ?? 'N/A' ?></p>
                    <p><strong>Tipo de Equipo:</strong> <?= $necesidad['tipo_equipo'] ?? 'N/A' ?></p>
                    <p><strong>Urgencia:</strong> <?= $necesidad['urgencia'] ?? 'N/A' ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Estado:</strong> <span class="badge badge-info"><?= $necesidad['estado'] ?? 'N/A' ?></span></p>
                    <p><strong>Categoría (Si aplica):</strong> <?= $necesidad['categoria_nombre'] ?? 'N/A' ?></p>
                    <p><strong>Detalles Adicionales:</strong> <?= $necesidad['detalles'] ?? 'Sin detalles proporcionados' ?></p>
                </div>
            </div>
            <a href="index.php?route=necesidades&action=misSolicitudes" class="btn btn-secondary mt-4">
                <i class="fas fa-arrow-left"></i> Volver a Mis Solicitudes
            </a>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-warning">Solicitud no encontrada.</div>
    <?php endif; ?>
</div>