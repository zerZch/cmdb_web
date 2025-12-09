<div class="page-header">
    <h2><i class="fas fa-desktop me-2"></i>Mis Equipos Asignados</h2>
</div>

<p class="text-muted">Lista de equipos que tienes actualmente asignados.</p>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>ID Asignación</th>
                        <th>Equipo</th>
                        <th>Número de Serie</th>
                        <th>Fecha Asignación</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipos)): ?>
                        <?php foreach ($equipos as $equipo): 
                            // Nota: Los datos de equipo y serie deben venir del JOIN en el modelo.
                        ?>
                            <tr>
                                <td><?= $equipo['id'] ?></td>
                                <td><strong><?= e($equipo['equipo_nombre']) ?></strong></td>
                                <td><?= e($equipo['numero_serie']) ?></td>
                                <td><?= date('d/m/Y', strtotime($equipo['fecha_asignacion'])) ?></td>
                                <td><?= e(substr($equipo['observaciones'] ?? 'N/A', 0, 50)) . '...' ?></td>
                                <td>
                                    <a href="index.php?route=equipos&action=ver&id=<?= $equipo['equipo_id'] ?>" 
                                       class="btn btn-sm btn-info" title="Ver Equipo">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No tienes equipos activos asignados en este momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>