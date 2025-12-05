<div class="page-header">
    <h2><i class="fas fa-search me-2"></i>Seleccionar Equipo para Ver Historial</h2>
    <p class="text-muted">Busca y selecciona un equipo para ver su historial completo de movimientos</p>
</div>

<!-- Buscador Rápido -->
<div class="row mb-4">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">
                    <i class="fas fa-search me-2 text-primary"></i>Búsqueda Rápida
                </h5>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">
                        <i class="fas fa-barcode"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="busquedaRapida"
                           placeholder="Buscar por nombre, serie, modelo, marca..."
                           autofocus>
                    <button class="btn btn-primary" type="button" onclick="buscarEquipo()">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    Escribe al menos 3 caracteres para iniciar la búsqueda
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Equipos -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Todos los Equipos
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable" id="tablaEquipos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Equipo</th>
                        <th>Serie</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong>#<?= $equipo['id'] ?></strong></td>
                        <td>
                            <div>
                                <strong><?= e($equipo['nombre']) ?></strong>
                                <?php if (!empty($equipo['marca'])): ?>
                                <br><small class="text-muted"><?= e($equipo['marca']) ?> - <?= e($equipo['modelo'] ?? '') ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($equipo['numero_serie'])): ?>
                                <code><?= e($equipo['numero_serie']) ?></code>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                <?= e($equipo['categoria_nombre']) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $estadoBadge = [
                                'disponible' => 'success',
                                'asignado' => 'info',
                                'dañado' => 'danger',
                                'mantenimiento' => 'warning',
                                'dado_de_baja' => 'dark',
                                'donado' => 'secondary'
                            ];
                            $badgeClass = $estadoBadge[$equipo['estado']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <?= ucfirst($equipo['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($equipo['estado'] === 'asignado' && !empty($equipo['colaborador_nombre'])): ?>
                                <i class="fas fa-user me-1"></i>
                                <?= e($equipo['colaborador_nombre']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?route=reportes&action=historialEquipo&id=<?= $equipo['id'] ?>" 
                               class="btn btn-sm btn-primary"
                               title="Ver Historial">
                                <i class="fas fa-history me-1"></i>Ver Historial
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tarjetas de Estadísticas Rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3><?= count($equipos) ?></h3>
                <p class="mb-0">Total Equipos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3><?= count(array_filter($equipos, fn($e) => $e['estado'] === 'disponible')) ?></h3>
                <p class="mb-0">Disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3><?= count(array_filter($equipos, fn($e) => $e['estado'] === 'asignado')) ?></h3>
                <p class="mb-0">Asignados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3><?= count(array_filter($equipos, fn($e) => in_array($e['estado'], ['dañado', 'mantenimiento']))) ?></h3>
                <p class="mb-0">En Revisión</p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // DataTable
    const table = $('#tablaEquipos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'desc']]
    });

    // Búsqueda rápida
    $('#busquedaRapida').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Enter para buscar
    $('#busquedaRapida').on('keypress', function(e) {
        if (e.which === 13) {
            buscarEquipo();
        }
    });
});

function buscarEquipo() {
    const termino = $('#busquedaRapida').val();
    
    if (termino.length < 3) {
        Swal.fire({
            title: 'Búsqueda Insuficiente',
            text: 'Por favor ingresa al menos 3 caracteres para buscar',
            icon: 'info',
            confirmButtonColor: '#1B3C53'
        });
        return;
    }

    // El DataTable ya está filtrando automáticamente
    // Pero podemos hacer un scroll al resultado si hay solo uno
    const table = $('#tablaEquipos').DataTable();
    const info = table.page.info();
    
    if (info.recordsDisplay === 1) {
        Swal.fire({
            title: 'Equipo Encontrado',
            text: '¿Desea ver el historial de este equipo?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#1B3C53',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ver historial',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Obtener el ID del equipo visible
                const row = table.row(':eq(0)', {order: 'current'}).node();
                const link = $(row).find('a').attr('href');
                window.location.href = link;
            }
        });
    } else if (info.recordsDisplay === 0) {
        Swal.fire({
            title: 'Sin Resultados',
            text: 'No se encontraron equipos que coincidan con tu búsqueda',
            icon: 'warning',
            confirmButtonColor: '#1B3C53'
        });
    }
}
</script>

<style>
/* Estilos personalizados */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.input-group-lg .form-control {
    font-size: 1.1rem;
}

/* Highlight en búsqueda */
.dataTables_filter {
    display: none;
}

/* Mejorar apariencia de tabla */
#tablaEquipos tbody tr {
    cursor: pointer;
}

#tablaEquipos tbody tr:hover {
    background-color: rgba(27, 60, 83, 0.05);
}
</style>
