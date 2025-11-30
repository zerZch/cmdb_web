<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-clipboard-list me-2"></i>Reporte de Inventario Completo</h2>
        <p class="text-muted mb-0">Listado detallado de todos los equipos del sistema</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?route=reportes&action=exportarInventarioCsv" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Exportar CSV
        </a>
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print me-2"></i>Imprimir
        </button>
    </div>
</div>

<!-- Estadísticas Generales -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Total Equipos
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #1B3C53; font-size: 36px;">
                            <?= $estadisticas['total'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Disponibles
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #28a745; font-size: 36px;">
                            <?= $estadisticas['disponibles'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Asignados
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #17a2b8; font-size: 36px;">
                            <?= $estadisticas['asignados'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Dañados
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #dc3545; font-size: 36px;">
                            <?= $estadisticas['danados'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Inventario -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Detalle del Inventario
        </h5>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped dataTable" id="tablaInventario">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>N° Serie</th>
                    <th>Marca/Modelo</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Condición</th>
                    <th>Ubicación</th>
                    <th>Asignado A</th>
                    <th>Valor</th>
                    <th>Fecha Adq.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventario as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><strong><?= e($item['nombre']) ?></strong></td>
                    <td><?= e($item['numero_serie'] ?? 'N/A') ?></td>
                    <td>
                        <?= e($item['marca'] ?? 'N/A') ?><br>
                        <small class="text-muted"><?= e($item['modelo'] ?? 'N/A') ?></small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            <?= e($item['categoria']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $item['estado'] === 'disponible' ? 'success' : 
                            ($item['estado'] === 'asignado' ? 'info' : 
                            ($item['estado'] === 'dañado' ? 'danger' : 
                            ($item['estado'] === 'mantenimiento' ? 'warning' : 'secondary'))) 
                        ?>">
                            <?= e(ucfirst($item['estado'])) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($item['condicion']): ?>
                            <span class="badge bg-<?= 
                                $item['condicion'] === 'excelente' ? 'success' : 
                                ($item['condicion'] === 'bueno' ? 'info' : 
                                ($item['condicion'] === 'regular' ? 'warning' : 'danger')) 
                            ?>">
                                <?= e(ucfirst($item['condicion'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($item['ubicacion'] ?? 'No especificada') ?></td>
                    <td>
                        <?php if ($item['asignado_a']): ?>
                            <i class="fas fa-user me-1"></i>
                            <?= e($item['asignado_a']) ?>
                            <?php if ($item['departamento_asignado']): ?>
                                <br><small class="text-muted"><?= e($item['departamento_asignado']) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['valor_adquisicion']): ?>
                            $<?= number_format($item['valor_adquisicion'], 2) ?>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['fecha_adquisicion']): ?>
                            <?= date('d/m/Y', strtotime($item['fecha_adquisicion'])) ?>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?route=reportes&action=historialEquipo&equipo_id=<?= $item['id'] ?>" 
                           class="btn btn-sm btn-info"
                           title="Ver Historial">
                            <i class="fas fa-history"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Información de Generación -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Reporte generado:</strong> <?= date('d/m/Y H:i:s') ?>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    <strong>Generado por:</strong> <?= e(currentUser()['nombre'] . ' ' . currentUser()['apellido']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .page-header .d-flex:last-child, .stat-icon {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 11px;
    }
    
    .badge {
        border: 1px solid currentColor;
    }
}
</style>

<script>
$(document).ready(function() {
    // Configurar DataTable con opciones de exportación
    $('#tablaInventario').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 25,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
