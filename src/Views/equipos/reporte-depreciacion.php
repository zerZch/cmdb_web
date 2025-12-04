<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?route=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?route=equipos">Equipos</a></li>
        <li class="breadcrumb-item active">Reporte de Depreciación</li>
    </ol>
</nav>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-line me-2"></i>Reporte de Depreciación de Equipos</h2>
    <div>
        <button type="button" class="btn btn-success btn-sm" onclick="exportarExcel()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <a href="index.php?route=equipos" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-filter"></i> Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="route" value="equipos">
            <input type="hidden" name="action" value="reporte-depreciacion">
            
            <div class="row">
                <!-- Categoría -->
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-control">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= (isset($filtros['categoria_id']) && $filtros['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Estado -->
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="disponible" <?= (isset($filtros['estado']) && $filtros['estado'] == 'disponible') ? 'selected' : '' ?>>Disponible</option>
                        <option value="asignado" <?= (isset($filtros['estado']) && $filtros['estado'] == 'asignado') ? 'selected' : '' ?>>Asignado</option>
                        <option value="mantenimiento" <?= (isset($filtros['estado']) && $filtros['estado'] == 'mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
                        <option value="dañado" <?= (isset($filtros['estado']) && $filtros['estado'] == 'dañado') ? 'selected' : '' ?>>Dañado</option>
                    </select>
                </div>
                
                <!-- Año -->
                <div class="col-md-3">
                    <label class="form-label">Año de Adquisición</label>
                    <select name="ano" class="form-control">
                        <option value="">Todos los años</option>
                        <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                            <option value="<?= $year ?>" 
                                    <?= (isset($filtros['ano']) && $filtros['ano'] == $year) ? 'selected' : '' ?>>
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <!-- Botón -->
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resumen Estadístico -->
<?php
$totalCosto = 0;
$totalDepreciacion = 0;
$totalValorLibro = 0;

if (!empty($equipos)) {
    foreach ($equipos as $eq) {
        $totalCosto += $eq['costo_adquisicion'] ?? 0;
        $totalDepreciacion += $eq['depreciacion_acumulada'] ?? 0;
        $totalValorLibro += $eq['valor_libro'] ?? 0;
    }
}
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <small class="text-muted">Total Equipos</small>
                <h3 class="text-primary"><?= count($equipos) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <small class="text-muted">Costo Total</small>
                <h3 class="text-success">$<?= number_format($totalCosto, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <small class="text-muted">Dep. Acumulada</small>
                <h3 class="text-danger">$<?= number_format($totalDepreciacion, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <small class="text-muted">Valor en Libros</small>
                <h3 class="text-info">$<?= number_format($totalValorLibro, 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-table"></i> Detalle de Depreciación
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($equipos)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No hay equipos que cumplan con los filtros seleccionados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Equipo</th>
                            <th>Categoría</th>
                            <th>Fecha Adq.</th>
                            <th class="text-end">Costo</th>
                            <th class="text-center">Vida Útil</th>
                            <th class="text-end">Dep. Mensual</th>
                            <th class="text-end">Dep. Acumulada</th>
                            <th class="text-end">Valor Libro</th>
                            <th class="text-center">% Dep.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipos as $eq): ?>
                            <tr>
                                <td>
                                    <a href="index.php?route=equipos&action=show&id=<?= $eq['id'] ?>">
                                        <?= e($eq['codigo_inventario']) ?>
                                    </a>
                                </td>
                                <td>
                                    <strong><?= e($eq['marca']) ?></strong>
                                    <?= e($eq['modelo']) ?>
                                    <br>
                                    <small class="text-muted"><?= e($eq['numero_serie']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= e($eq['categoria']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($eq['fecha_adquisicion'])) ?></td>
                                <td class="text-end">$<?= number_format($eq['costo_adquisicion'], 2) ?></td>
                                <td class="text-center"><?= $eq['vida_util_anos'] ?> años</td>
                                <td class="text-end">$<?= number_format($eq['depreciacion_mensual'], 2) ?></td>
                                <td class="text-end text-danger">
                                    <strong>$<?= number_format($eq['depreciacion_acumulada'], 2) ?></strong>
                                </td>
                                <td class="text-end text-success">
                                    <strong>$<?= number_format($eq['valor_libro'], 2) ?></strong>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?= $eq['porcentaje_depreciado'] >= 100 ? 'bg-danger' : 'bg-primary' ?>" 
                                             style="width: <?= min($eq['porcentaje_depreciado'], 100) ?>%">
                                            <?= number_format($eq['porcentaje_depreciado'], 1) ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">TOTALES:</td>
                            <td class="text-end">$<?= number_format($totalCosto, 2) ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-end text-danger">$<?= number_format($totalDepreciacion, 2) ?></td>
                            <td class="text-end text-success">$<?= number_format($totalValorLibro, 2) ?></td>
                            <td class="text-center">
                                <?= $totalCosto > 0 ? number_format(($totalDepreciacion / $totalCosto) * 100, 2) : 0 ?>%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function exportarExcel() {
    const tabla = document.querySelector('table');
    if (!tabla) {
        Swal.fire('Error', 'No hay datos para exportar', 'error');
        return;
    }
    
    let csv = [];
    const rows = tabla.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push(col.innerText.replace(/,/g, ''));
        });
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'reporte_depreciacion_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}
</script>