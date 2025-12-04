<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-boxes me-2"></i>Inventario por Categoría</h2>
            <p class="text-muted mb-0">Listado de equipos agrupados por categoría con totales, disponibles y asignados</p>
        </div>
        <div>
            <a href="index.php?route=reportes" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Resumen General -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Equipos</p>
                        <h3 class="mb-0 fw-bold"><?= number_format($datosReporte['totales']['total_equipos']) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-laptop fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Disponibles</p>
                        <h3 class="mb-0 fw-bold text-success"><?= number_format($datosReporte['totales']['disponibles']) ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Asignados</p>
                        <h3 class="mb-0 fw-bold text-info"><?= number_format($datosReporte['totales']['asignados']) ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-user-check fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Valor Total</p>
                        <h3 class="mb-0 fw-bold text-warning">$<?= number_format($datosReporte['totales']['valor_total'], 2) ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Detallada -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detalle por Categoría</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="tableInventario">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Categoría</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Disponibles</th>
                        <th class="text-center">Asignados</th>
                        <th class="text-center">En Mantenimiento</th>
                        <th class="text-center">Dañado</th>
                        <th class="text-end">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($datosReporte['categorias'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No hay datos para mostrar</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $contador = 1; ?>
                        <?php foreach ($datosReporte['categorias'] as $cat): ?>
                            <tr>
                                <td><?= $contador++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($cat['categoria']) ?></strong>
                                    <?php if (!empty($cat['categoria_descripcion'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cat['categoria_descripcion']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6"><?= $cat['total_equipos'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success fs-6"><?= $cat['disponibles'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info fs-6"><?= $cat['asignados'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning fs-6"><?= $cat['en_revision'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6"><?= $cat['descarte'] ?></span>
                                </td>
                                <td class="text-end">
                                    <strong>$<?= number_format($cat['valor_total'], 2) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Fila de Totales -->
                        <tr class="table-secondary fw-bold">
                            <td colspan="2" class="text-end">TOTALES:</td>
                            <td class="text-center"><?= number_format($datosReporte['totales']['total_equipos']) ?></td>
                            <td class="text-center"><?= number_format($datosReporte['totales']['disponibles']) ?></td>
                            <td class="text-center"><?= number_format($datosReporte['totales']['asignados']) ?></td>
                            <td class="text-center"><?= number_format($datosReporte['totales']['en_revision']) ?></td>
                            <td class="text-center"><?= number_format($datosReporte['totales']['descarte']) ?></td>
                            <td class="text-end">$<?= number_format($datosReporte['totales']['valor_total'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script de Exportación -->
<script>
function exportarExcel() {
    const categorias = <?= json_encode($datosReporte['categorias']) ?>;
    const totales = <?= json_encode($datosReporte['totales']) ?>;
    
    if (!categorias || categorias.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    
    let csv = [];
    
    csv.push(['REPORTE DE INVENTARIO POR CATEGORÍA']);
    csv.push(['Generado el: ' + new Date().toLocaleString('es-PA')]);
    csv.push([]);
    
    csv.push(['RESUMEN GENERAL']);
    csv.push(['Total de Equipos:', totales.total_equipos]);
    csv.push(['Equipos Disponibles:', totales.disponibles]);
    csv.push(['Equipos Asignados:', totales.asignados]);
    csv.push(['Equipos en Mantenimiento:', totales.en_revision]);
    csv.push(['Equipos Dañados:', totales.descarte]);
    csv.push(['Valor Total:', '$' + parseFloat(totales.valor_total).toFixed(2)]);
    csv.push([]);
    
    csv.push(['#', 'Categoría', 'Descripción', 'Total', 'Disponibles', 'Asignados', 'Mantenimiento', 'Dañado', 'Valor Total']);
    
    categorias.forEach((cat, index) => {
        csv.push([
            index + 1,
            cat.categoria,
            cat.categoria_descripcion || '',
            cat.total_equipos,
            cat.disponibles,
            cat.asignados,
            cat.en_revision,
            cat.descarte,
            '$' + parseFloat(cat.valor_total).toFixed(2)
        ]);
    });
    
    csv.push([]);
    csv.push(['', 'TOTALES:', '', totales.total_equipos, totales.disponibles, totales.asignados, totales.en_revision, totales.descarte, '$' + parseFloat(totales.valor_total).toFixed(2)]);
    
    const csvContent = csv.map(row => row.join(',')).join('\n');
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'inventario_categoria_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.badge {
    padding: 0.5em 0.75em;
}
</style>