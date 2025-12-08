<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-exchange-alt me-2"></i>Gestión de Asignaciones</h2>
    <a href="index.php?route=asignaciones&action=crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Asignación
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Equipo</th>
                    <th>Colaborador</th>
                    <th>Departamento</th>
                    <th>Fecha Asignación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion): ?>
                <tr>
                    <td><?= $asignacion['id'] ?></td>
                    <td>
                        <strong><?= e($asignacion['equipo_nombre']) ?></strong><br>
                        <small class="text-muted"><?= e($asignacion['numero_serie']) ?></small>
                    </td>
                    <td>
                        <?= e($asignacion['colaborador_nombre'] . ' ' . $asignacion['colaborador_apellido']) ?>
                    </td>
                    <td><?= e($asignacion['departamento'] ?? 'N/A') ?></td>
                    <td><?= date('d/m/Y', strtotime($asignacion['fecha_asignacion'])) ?></td>
                    <td>
                        <span class="badge bg-info">Activa</span>
                    </td>
                    <td>
                        <a href="index.php?route=asignaciones&action=devolver&id=<?= $asignacion['id'] ?>"
                           class="btn btn-sm btn-warning"
                           title="Devolver Equipo">
                            <i class="fas fa-undo"></i> Devolver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>