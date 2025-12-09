<?php 
// Vista de Detalle de Equipo (equipos/ver.php)
/** @var array $equipo */
/** @var string $pageTitle */

// Nota: El array $equipo debe contener los campos del equipo (Marca, Modelo, etc.)
// obtenidos del findById en el EquipoController.
?>
<div class="container-fluid">
    <h1><?= $pageTitle ?? 'Detalle del Equipo' ?></h1>

    <?php if (!empty($equipo)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= $equipo['nombre'] ?? 'Equipo Desconocido' ?> (Serie: <?= $equipo['numero_serie'] ?? 'N/A' ?>)
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h3>Información Básica</h3>
                    <p><strong>ID:</strong> <?= $equipo['id'] ?></p>
                    <p><strong>Marca:</strong> <?= $equipo['marca'] ?? 'N/A' ?></p>
                    <p><strong>Modelo:</strong> <?= $equipo['modelo'] ?? 'N/A' ?></p>
                    <p><strong>Número de Serie:</strong> <?= $equipo['numero_serie'] ?? 'N/A' ?></p>
                    <p><strong>Estado Actual:</strong> <span class="badge badge-primary"><?= $equipo['estado'] ?? 'N/A' ?></span></p>
                </div>
                <div class="col-md-6">
                    <h3>Datos Financieros y Trazabilidad</h3>
                    <p><strong>Costo:</strong> $<?= number_format($equipo['costo'] ?? 0, 2) ?></p>
                    <p><strong>Fecha de Compra:</strong> <?= $equipo['fecha_compra'] ?? 'N/A' ?></p>
                    <p><strong>Categoría ID:</strong> <?= $equipo['categoria_id'] ?? 'N/A' ?></p>
                    <p><strong>Último Colaborador Asignado:</strong> [Datos de Asignación]</p> 
                </div>
            </div>
            
            <hr>
            <a href="index.php?route=asignaciones&action=misEquipos" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Volver a Mis Equipos Asignados
            </a>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-warning">No se encontró información para este equipo.</div>
    <?php endif; ?>
</div>