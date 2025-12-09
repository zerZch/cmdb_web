<div class="page-header">
    <h2><i class="fas fa-chart-pie me-2"></i>Reporte de Equipos por Estado</h2>
    <p class="text-muted">Distribución y valor total de equipos según su estado actual.</p>
</div>

<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Resultados del Reporte
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($reporte)): ?>
            <div class="alert alert-info">
                No se encontraron equipos activos para generar este reporte.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered dataTable" id="tablaReporteEstado">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Total de Equipos</th>
                            <th>Valor Total (Adquisición)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $granTotalEquipos = 0;
                        $granTotalValor = 0;
                        foreach ($reporte as $item): 
                            $granTotalEquipos += $item['total_equipos'];
                            $granTotalValor += $item['valor_total_estado'];
                            
                            // Definir la clase de la insignia (Badge) según el estado
                            $badgeClass = [
                                'disponible' => 'success',
                                'asignado' => 'info',
                                'dañado' => 'danger',
                                'mantenimiento' => 'warning',
                                'donado' => 'secondary'
                            ][strtolower($item['estado'])] ?? 'secondary';
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= ucfirst($item['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= number_format($item['total_equipos']) ?></strong>
                            </td>
                            <td>
                                <?= '$' . number_format($item['valor_total_estado'], 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TOTAL GENERAL</th>
                            <th><?= number_format($granTotalEquipos) ?></th>
                            <th><?= '$' . number_format($granTotalValor, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    var tabla = $('#tablaReporteEstado');
    // 1. Verificar si la tabla ya ha sido inicializada como DataTables
    if ($.fn.DataTable.isDataTable('#tablaReporteEstado')) {
        // 2. Si es una DataTables, destruir la instancia existente.
        $('#tablaReporteEstado').DataTable().destroy();
    }
    
    // 3. Inicializar DataTables
    $('#tablaReporteEstado').DataTable({
        retrieve: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[1, 'desc']] // Ordenar por Total de Equipos descendente
    });
});
</script>