<div class="page-header">
    <h2><i class="fas fa-users me-2"></i>Reporte de Equipos por Colaborador</h2>
    <p class="text-muted">Muestra la lista de colaboradores y el total de equipos asignados a cada uno.</p>
</div>

<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>Reporte Detallado
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($reporte)): ?>
            <div class="alert alert-info">
                No se encontraron colaboradores con equipos asignados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered dataTable" id="tablaReporteColaborador">
                    <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Departamento</th>
                            <th>Ubicación</th>
                            <th>Total Equipos Asignados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $granTotalEquipos = 0;
                        foreach ($reporte as $item): 
                            $granTotalEquipos += $item['total_equipos_asignados'];
                        ?>
                        <tr>
                            <td>
                                <i class="fas fa-user-circle me-1 text-primary"></i>
                                <strong><?= e($item['colaborador_nombre']) ?></strong>
                            </td>
                            <td>
                                <?= e($item['departamento_nombre']) ?>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= e($item['ubicacion']) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">
                                    <?= number_format($item['total_equipos_asignados']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">TOTAL DE EQUIPOS ASIGNADOS:</th>
                            <th class="text-center"><?= number_format($granTotalEquipos) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaReporteColaborador').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[3, 'desc']] // Ordenar por Total Equipos Asignados
    });
});
</script>