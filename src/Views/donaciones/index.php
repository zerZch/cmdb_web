<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-hand-holding-heart me-2"></i>Gestión de Donaciones</h2>
    <a href="index.php?route=donaciones&action=crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Registrar Donación
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Equipo</th>
                    <th>Categoría</th>
                    <th>Entidad Beneficiada</th>
                    <th>Tipo Entidad</th>
                    <th>Valor</th>
                    <th>Condición</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donaciones as $donacion): ?>
                <tr>
                    <td><?= $donacion['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($donacion['fecha_donacion'])) ?></td>
                    <td>
                        <strong><?= e($donacion['equipo_nombre']) ?></strong><br>
                        <small class="text-muted"><?= e($donacion['numero_serie']) ?></small>
                    </td>
                    <td><span class="badge bg-secondary"><?= e($donacion['categoria']) ?></span></td>
                    <td>
                        <strong><?= e($donacion['entidad_beneficiada']) ?></strong>
                    </td>
                    <td>
                        <?php if ($donacion['tipo_entidad']): ?>
                            <span class="badge bg-info">
                                <?= e(ucfirst($donacion['tipo_entidad'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($donacion['valor_donacion']): ?>
                            $<?= number_format($donacion['valor_donacion'], 2) ?>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $donacion['condicion_equipo'] === 'excelente' ? 'success' : 
                            ($donacion['condicion_equipo'] === 'bueno' ? 'info' : 
                            ($donacion['condicion_equipo'] === 'regular' ? 'warning' : 'secondary')) 
                        ?>">
                            <?= e(ucfirst($donacion['condicion_equipo'])) ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?route=donaciones&action=ver&id=<?= $donacion['id'] ?>"
                           class="btn btn-sm btn-info"
                           title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
