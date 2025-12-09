<div class="page-header">
    <h2><i class="fas fa-history me-2"></i>Mi Historial de Equipos</h2>
</div>

<p class="text-muted">Registro de todos los equipos que te han sido asignados y devueltos.</p>



<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Serie</th>
                        <th>Asignado el</th>
                        <th>Devuelto el</th>
                        <th>Motivo Devolución</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historial)): ?>
                        <?php foreach ($historial as $registro): ?>
                            <tr>
                                <td><?= e($registro['equipo_nombre']) ?></td>
                                <td><?= e($registro['numero_serie']) ?></td>
                                <td><?= date('d/m/Y', strtotime($registro['fecha_asignacion'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($registro['fecha_devolucion'])) ?></td>
                                <td><?= e($registro['motivo_devolucion'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de historial de equipos anteriores.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>