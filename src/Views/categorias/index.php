<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-tags me-2"></i>Gestión de Categorías</h2>
    <a href="index.php?route=categorias&action=crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Categoría
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Total Equipos</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?= $categoria['id'] ?></td>
                    <td>
                        <strong><i class="fas fa-tag me-2"></i><?= e($categoria['nombre']) ?></strong>
                    </td>
                    <td><?= e($categoria['descripcion'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-info"><?= $categoria['total_equipos'] ?> equipos</span>
                    </td>
                    <td>
                        <?php if ($categoria['estado'] === 'activa'): ?>
                            <span class="badge bg-success">Activa</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($categoria['created_at'])) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="index.php?route=categorias&action=editar&id=<?= $categoria['id'] ?>"
                               class="btn btn-sm btn-info"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if ($categoria['estado'] === 'activa'): ?>
                                <button type="button"
                                        class="btn btn-sm btn-warning btn-cambiar-estado"
                                        data-id="<?= $categoria['id'] ?>"
                                        data-estado="inactiva"
                                        title="Desactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        class="btn btn-sm btn-success btn-cambiar-estado"
                                        data-id="<?= $categoria['id'] ?>"
                                        data-estado="activa"
                                        title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>

                            <?php if ($categoria['total_equipos'] == 0): ?>
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-eliminar"
                                        data-id="<?= $categoria['id'] ?>"
                                        data-nombre="<?= e($categoria['nombre']) ?>"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        class="btn btn-sm btn-secondary"
                                        disabled
                                        title="No se puede eliminar (tiene equipos asociados)">
                                    <i class="fas fa-lock"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cambiar estado
    $('.btn-cambiar-estado').click(function() {
        const id = $(this).data('id');
        const estado = $(this).data('estado');
        const accion = estado === 'activa' ? 'activar' : 'desactivar';

        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Desea ${accion} esta categoría?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=categorias&action=cambiarEstado', {
                    id: id,
                    estado: estado
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#667eea'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                });
            }
        });
    });

    // Eliminar
    $('.btn-eliminar').click(function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');

        Swal.fire({
            title: '¿Está seguro?',
            html: `¿Desea eliminar la categoría <strong>${nombre}</strong>?<br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=categorias&action=eliminar', {
                    id: id
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Eliminada',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#667eea'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                });
            }
        });
    });
});
</script>
