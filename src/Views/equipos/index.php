<!-- Gestión de Equipos -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-laptop me-2"></i>Gestión de Equipos</h2>
    <div>
        <?php if (hasRole('admin')): ?>
            <!-- BOTÓN DE IMPORTACIÓN MASIVA (NUEVO) -->
            <a href="index.php?route=equipos&action=importar" class="btn btn-success me-2">
                <i class="fas fa-file-upload me-2"></i>Importar Equipos
            </a>
            <a href="index.php?route=equipos&action=create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Equipo
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas -->
<div class="row g-4 mb-4">
    <!-- Total de Equipos -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Total de Equipos
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #1B3C53;">
                            <?= $estadisticas['total'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disponibles -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Disponibles
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #28a745;">
                            <?= $estadisticas['disponibles'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asignados -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Asignados
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #17a2b8;">
                            <?= $estadisticas['asignados'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Valor Total -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px;">
                            Valor Total
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #ffc107;">
                            $<?= number_format($estadisticas['valor_total'] ?? 0, 2) ?>
                        </h2>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ffc107, #ffcd39);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <?php if (empty($equipos)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No hay equipos registrados. 
                <?php if (hasRole('admin')): ?>
                    <a href="index.php?route=equipos&action=create">Crear el primer equipo</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>Marca/Modelo</th>
                        <th>Serie</th>
                        <th>Fecha Adq.</th>
                        <th>Costo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <tr>
                            <td class="text-center">
                                <?php if (!empty($equipo['foto'])): ?>
                                    <img src="<?= $equipo['foto'] ?>" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= e($equipo['codigo_inventario']) ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= e($equipo['categoria_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= e($equipo['marca']) ?></strong><br>
                                <small class="text-muted"><?= e($equipo['modelo']) ?></small>
                            </td>
                            <td>
                                <small><?= e($equipo['numero_serie']) ?></small>
                            </td>
                            <td>
                                <?= $equipo['fecha_adquisicion'] ? date('d/m/Y', strtotime($equipo['fecha_adquisicion'])) : 'N/A' ?>
                            </td>
                            <td>
                                $<?= number_format($equipo['costo_adquisicion'] ?? 0, 2) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= 
                                    $equipo['estado'] === 'disponible' ? 'success' : 
                                    ($equipo['estado'] === 'asignado' ? 'info' : 
                                    ($equipo['estado'] === 'mantenimiento' ? 'warning' : 
                                    ($equipo['estado'] === 'dañado' ? 'danger' : 'secondary'))) 
                                ?>">
                                    <?= e(ucfirst($equipo['estado'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?route=equipos&action=show&id=<?= $equipo['id'] ?>" 
                                   class="btn btn-sm btn-info me-1"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if (hasRole('admin')): ?>
                                    <a href="index.php?route=equipos&action=edit&id=<?= $equipo['id'] ?>" 
                                       class="btn btn-sm btn-warning me-1"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <?php if ($equipo['estado'] !== 'asignado'): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger btn-delete" 
                                                data-id="<?= $equipo['id'] ?>"
                                                data-nombre="<?= e($equipo['marca'] . ' ' . $equipo['modelo']) ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Eliminar equipo?',
            html: `¿Estás seguro de eliminar el equipo <strong>${nombre}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?route=equipos&action=delete&id=' + id;
            }
        });
    });
});
</script>