<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
    <a href="index.php?route=usuarios&action=crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td>
                        <strong><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong>
                    </td>
                    <td><?= e($usuario['email']) ?></td>
                    <td>
                        <?php if ($usuario['rol'] === 'admin'): ?>
                            <span class="badge bg-danger">Administrador</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Colaborador</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($usuario['estado'] === 'activo'): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="index.php?route=usuarios&action=editar&id=<?= $usuario['id'] ?>"
                               class="btn btn-sm btn-info"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if ($usuario['estado'] === 'activo'): ?>
                                <button type="button"
                                        class="btn btn-sm btn-warning btn-cambiar-estado"
                                        data-id="<?= $usuario['id'] ?>"
                                        data-estado="inactivo"
                                        title="Inactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        class="btn btn-sm btn-success btn-cambiar-estado"
                                        data-id="<?= $usuario['id'] ?>"
                                        data-estado="activo"
                                        title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>

                            <?php if ($usuario['id'] != currentUser()['id']): ?>
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-eliminar"
                                        data-id="<?= $usuario['id'] ?>"
                                        data-nombre="<?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
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
        const accion = estado === 'activo' ? 'activar' : 'inactivar';

        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Desea ${accion} este usuario?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=usuarios&action=cambiarEstado', {
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
            html: `¿Desea eliminar al usuario <strong>${nombre}</strong>?<br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=usuarios&action=eliminar', {
                    id: id
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Eliminado',
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
