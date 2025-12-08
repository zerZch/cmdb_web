<!-- Vista: src/Views/reportes/asignaciones_activas.php -->
<!-- Reporte "Quién Tiene Qué" - Asignaciones Activas -->

<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-user-tag me-2"></i>Reporte: Quién Tiene Qué</h2>
            <p class="text-muted mb-0">Listado de equipos asignados actualmente a colaboradores</p>
        </div>
        <div>
            <button type="button" class="btn btn-success me-2" onclick="exportarExcel()">
                <i class="fas fa-file-excel me-2"></i>Exportar Excel
            </button>
            <button type="button" class="btn btn-danger me-2" onclick="exportarPDF()">
                <i class="fas fa-file-pdf me-2"></i>Exportar PDF
            </button>
            <a href="index.php?route=reportes" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<!-- Estadísticas Rápidas -->
<div class="row g-4 mb-4">
    <!-- Total Asignaciones -->
    <div class="col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
                <h3 class="mb-0 fw-bold text-primary"><?= count($asignaciones) ?></h3>
                <p class="text-muted small mb-0">Asignaciones Activas</p>
            </div>
        </div>
    </div>

    <!-- Colaboradores con Equipos -->
    <div class="col-md-3">
        <div class="card border-info h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-user-check fa-3x text-info"></i>
                </div>
                <h3 class="mb-0 fw-bold text-info">
                    <?= count(array_unique(array_column($asignaciones, 'colaborador_id'))) ?>
                </h3>
                <p class="text-muted small mb-0">Colaboradores</p>
            </div>
        </div>
    </div>

    <!-- Equipos en Uso -->
    <div class="col-md-3">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-laptop fa-3x text-success"></i>
                </div>
                <h3 class="mb-0 fw-bold text-success">
                    <?= count(array_unique(array_column($asignaciones, 'equipo_id'))) ?>
                </h3>
                <p class="text-muted small mb-0">Equipos en Uso</p>
            </div>
        </div>
    </div>

    <!-- Días Promedio -->
    <div class="col-md-3">
        <div class="card border-warning h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fas fa-calendar-alt fa-3x text-warning"></i>
                </div>
                <h3 class="mb-0 fw-bold text-warning">
                    <?php
                    if (!empty($asignaciones)) {
                        $totalDias = 0;
                        foreach ($asignaciones as $asig) {
                            $fecha = new DateTime($asig['fecha_asignacion']);
                            $hoy = new DateTime();
                            $totalDias += $fecha->diff($hoy)->days;
                        }
                        echo round($totalDias / count($asignaciones));
                    } else {
                        echo '0';
                    }
                    ?>
                </h3>
                <p class="text-muted small mb-0">Días Promedio</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="route" value="reportes">
            <input type="hidden" name="action" value="asignacionesActivas">
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Departamento</label>
                    <select name="departamento" class="form-select">
                        <option value="">Todos los departamentos</option>
                        <option value="TI">TI</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Finanzas">Finanzas</option>
                        <option value="Recursos Humanos">Recursos Humanos</option>
                        <option value="Administración">Administración</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría de Equipo</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Asignaciones -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detalle de Asignaciones Activas</h5>
    </div>
    <div class="card-body">
        <?php if (empty($asignaciones)): ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                <p class="mb-0">No hay asignaciones activas en este momento</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaAsignaciones">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Colaborador</th>
                            <th>Departamento</th>
                            <th>Equipo</th>
                            <th>Categoría</th>
                            <th>Número Serie</th>
                            <th>Fecha Asignación</th>
                            <th>Días</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        <?php foreach ($asignaciones as $asig): ?>
                            <tr>
                                <td><?= $contador++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            <?= strtoupper(substr($asig['colaborador_nombre'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($asig['colaborador_nombre'] . ' ' . $asig['colaborador_apellido']) ?></strong>
                                            <?php if (!empty($asig['colaborador_email'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($asig['colaborador_email']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($asig['departamento'] ?? 'Sin departamento') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($asig['equipo_nombre']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($asig['marca'] ?? '') ?> <?= htmlspecialchars($asig['modelo'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($asig['categoria']) ?>
                                    </span>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($asig['numero_serie'] ?? 'N/A') ?></code>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($asig['fecha_asignacion'])) ?>
                                    <br><small class="text-muted"><?= date('H:i', strtotime($asig['fecha_asignacion'])) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $fecha = new DateTime($asig['fecha_asignacion']);
                                    $hoy = new DateTime();
                                    $dias = $fecha->diff($hoy)->days;
                                    
                                    $badgeClass = 'secondary';
                                    if ($dias > 180) $badgeClass = 'danger';
                                    elseif ($dias > 90) $badgeClass = 'warning';
                                    elseif ($dias > 30) $badgeClass = 'info';
                                    else $badgeClass = 'success';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= $dias ?> días
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?= htmlspecialchars($asig['ubicacion'] ?? 'No especificada') ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="index.php?route=equipos&action=show&id=<?= $asig['equipo_id'] ?>" 
                                           class="btn btn-sm btn-info"
                                           title="Ver Equipo">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?route=colaboradores&action=ver&id=<?= $asig['colaborador_id'] ?>" 
                                           class="btn btn-sm btn-primary"
                                           title="Ver Colaborador">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <?php if (hasRole('admin')): ?>
                                            <a href="index.php?route=asignaciones&action=devolver&id=<?= $asig['id'] ?>" 
                                               class="btn btn-sm btn-warning"
                                               title="Registrar Devolución">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Alertas de Asignaciones Prolongadas -->
<?php
$asignacionesProlongadas = array_filter($asignaciones, function($asig) {
    $fecha = new DateTime($asig['fecha_asignacion']);
    $hoy = new DateTime();
    return $fecha->diff($hoy)->days > 180; // Más de 6 meses
});
?>

<?php if (!empty($asignacionesProlongadas)): ?>
<div class="card border-warning mt-4">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Asignaciones Prolongadas (Más de 6 Meses)
        </h6>
    </div>
    <div class="card-body">
        <p class="small mb-3">Las siguientes asignaciones llevan más de 6 meses activas. Se recomienda revisar si el colaborador aún necesita el equipo:</p>
        <div class="list-group">
            <?php foreach ($asignacionesProlongadas as $asig): ?>
                <?php
                $fecha = new DateTime($asig['fecha_asignacion']);
                $hoy = new DateTime();
                $dias = $fecha->diff($hoy)->days;
                ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($asig['colaborador_nombre'] . ' ' . $asig['colaborador_apellido']) ?></strong>
                            - <?= htmlspecialchars($asig['equipo_nombre']) ?>
                        </div>
                        <span class="badge bg-danger"><?= $dias ?> días</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // DataTable
    $('#tablaAsignaciones').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 25,
        order: [[6, 'desc']], // Ordenar por fecha
        dom: 'Bfrtip',
        buttons: []
    });
});

function exportarExcel() {
    // Convertir tabla a CSV
    const table = document.getElementById('tablaAsignaciones');
    let csv = [];
    
    // Encabezados
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Datos
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach((td, index) => {
            // Saltar columna de acciones
            if (index < 9) {
                row.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
            }
        });
        csv.push(row.join(','));
    });
    
    // Descargar
    const blob = new Blob(['\ufeff' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'asignaciones_activas_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}

function exportarPDF() {
    window.print();
}
</script>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

@media print {
    .btn, .card-header, .page-header .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
    }
}
</style>