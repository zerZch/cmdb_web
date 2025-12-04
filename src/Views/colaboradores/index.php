<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-users me-2"></i>Gestión de Colaboradores</h2>
    <a href="index.php?route=colaboradores&action=crear" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Colaborador
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nombre Completo</th>
                    <th>Cédula</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Equipos Asignados</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colaboradores as $colaborador): ?>
                <tr>
                    <td><?= $colaborador['id'] ?></td>
                    <td>
                        <?php if (!empty($colaborador['foto_perfil'])): ?>
                            <img src="<?= e($colaborador['foto_perfil']) ?>" 
                                 alt="Foto" 
                                 class="rounded-circle" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= e($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?></strong>
                        <?php if ($colaborador['email']): ?>
                            <br><small class="text-muted"><?= e($colaborador['email']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= e($colaborador['cedula'] ?? '-') ?></td>
                    <td><?= e($colaborador['cargo'] ?? '-') ?></td>
                    <td><?= e($colaborador['departamento'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-info">
                            <?= $colaborador['total_equipos'] ?> equipos
                        </span>
                    </td>
                    <td>
                        <?php if ($colaborador['estado'] === 'activo'): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php elseif ($colaborador['estado'] === 'inactivo'): ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Suspendido</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="index.php?route=colaboradores&action=ver&id=<?= $colaborador['id'] ?>"
                               class="btn btn-sm btn-info"
                               title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="index.php?route=colaboradores&action=editar&id=<?= $colaborador['id'] ?>"
                               class="btn btn-sm btn-warning"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if ($colaborador['estado'] === 'activo'): ?>
                                <button type="button"
                                        class="btn btn-sm btn-secondary btn-cambiar-estado"
                                        data-id="<?= $colaborador['id'] ?>"
                                        data-estado="inactivo"
                                        title="Desactivar">
                                    <i class="fas fa-ban"></i>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        class="btn btn-sm btn-success btn-cambiar-estado"
                                        data-id="<?= $colaborador['id'] ?>"
                                        data-estado="activo"
                                        title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>

                            <?php if ($colaborador['total_equipos'] == 0): ?>
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-eliminar"
                                        data-id="<?= $colaborador['id'] ?>"
                                        data-nombre="<?= e($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?>"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php else: ?>
                                <button type="button"
                                        class="btn btn-sm btn-secondary"
                                        disabled
                                        title="No se puede eliminar (tiene equipos asignados)">
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
        const accion = estado === 'activo' ? 'activar' : 'desactivar';

        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Desea ${accion} este colaborador?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1B3C53',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=colaboradores&action=cambiarEstado', {
                    id: id,
                    estado: estado
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#1B3C53'
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
            html: `¿Desea eliminar al colaborador <strong>${nombre}</strong>?<br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('index.php?route=colaboradores&action=eliminar', {
                    id: id
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Eliminado',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#1B3C53'
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
