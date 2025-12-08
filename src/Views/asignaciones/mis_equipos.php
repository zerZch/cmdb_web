<!-- Vista: src/Views/asignaciones/mis_equipos.php -->
<!-- Dashboard del colaborador para ver sus equipos asignados -->

<div class="page-header mb-4">
    <h2><i class="fas fa-briefcase me-2"></i>Mis Equipos Asignados</h2>
    <p class="text-muted">Equipos tecnológicos bajo tu responsabilidad</p>
</div>

<!-- Tarjetas de Resumen -->
<div class="row g-4 mb-4">
    <!-- Total de Equipos -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Equipos Asignados</p>
                        <h3 class="mb-0 fw-bold text-primary">
                            <?= count($equiposAsignados) ?>
                        </h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-laptop fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Días con Equipos -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Promedio de Días</p>
                        <h3 class="mb-0 fw-bold text-info">
                            <?php
                            if (!empty($equiposAsignados)) {
                                $totalDias = 0;
                                foreach ($equiposAsignados as $eq) {
                                    $fecha = new DateTime($eq['fecha_asignacion']);
                                    $hoy = new DateTime();
                                    $totalDias += $fecha->diff($hoy)->days;
                                }
                                echo round($totalDias / count($equiposAsignados));
                            } else {
                                echo '0';
                            }
                            ?>
                        </h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-calendar-alt fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solicitudes Pendientes -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Solicitudes Activas</p>
                        <h3 class="mb-0 fw-bold text-warning">
                            <?= $totalSolicitudes ?? 0 ?>
                        </h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-clipboard-list fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Devoluciones Realizadas -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Devoluciones</p>
                        <h3 class="mb-0 fw-bold text-success">
                            <?= $totalDevoluciones ?? 0 ?>
                        </h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-undo fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Equipos Asignados -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-laptop-code me-2"></i>Equipos Bajo Mi Responsabilidad</h5>
    </div>
    <div class="card-body">
        <?php if (empty($equiposAsignados)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No tienes equipos asignados actualmente.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($equiposAsignados as $equipo): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <!-- Imagen del Equipo -->
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <?php if (!empty($equipo['foto'])): ?>
                                    <img src="<?= htmlspecialchars($equipo['foto']) ?>" 
                                         alt="<?= htmlspecialchars($equipo['equipo_nombre']) ?>"
                                         class="img-fluid"
                                         style="max-height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-laptop fa-4x text-muted"></i>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <!-- Nombre del Equipo -->
                                <h6 class="card-title fw-bold">
                                    <?= htmlspecialchars($equipo['equipo_nombre']) ?>
                                </h6>

                                <!-- Serie -->
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-barcode me-1"></i>
                                    <?= htmlspecialchars($equipo['numero_serie'] ?? 'Sin serie') ?>
                                </p>

                                <!-- Categoría -->
                                <p class="mb-2">
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($equipo['categoria']) ?>
                                    </span>
                                </p>

                                <!-- Fecha de Asignación -->
                                <p class="small text-muted mb-2">
                                    <i class="fas fa-calendar me-1"></i>
                                    Asignado: <?= date('d/m/Y', strtotime($equipo['fecha_asignacion'])) ?>
                                </p>

                                <!-- Días con el equipo -->
                                <p class="small text-muted mb-3">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php
                                    $fecha = new DateTime($equipo['fecha_asignacion']);
                                    $hoy = new DateTime();
                                    $dias = $fecha->diff($hoy)->days;
                                    echo $dias . ' día' . ($dias != 1 ? 's' : '') . ' contigo';
                                    ?>
                                </p>

                                <!-- Observaciones -->
                                <?php if (!empty($equipo['observaciones'])): ?>
                                    <div class="alert alert-warning small mb-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= htmlspecialchars($equipo['observaciones']) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Botones de Acción -->
                                <div class="d-grid gap-2">
                                    <a href="index.php?route=equipos&action=show&id=<?= $equipo['equipo_id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Ver Detalles
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-warning btn-solicitar-devolucion"
                                            data-asignacion-id="<?= $equipo['asignacion_id'] ?>"
                                            data-equipo-nombre="<?= htmlspecialchars($equipo['equipo_nombre']) ?>">
                                        <i class="fas fa-undo me-1"></i>Solicitar Devolución
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Historial de Devoluciones -->
<?php if (!empty($historialDevoluciones)): ?>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Devoluciones</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Equipo</th>
                        <th>Fecha Asignación</th>
                        <th>Fecha Devolución</th>
                        <th>Días de Uso</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historialDevoluciones as $devolucion): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($devolucion['equipo_nombre']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($devolucion['numero_serie'] ?? 'S/N') ?></small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($devolucion['fecha_asignacion'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($devolucion['fecha_devolucion'])) ?></td>
                            <td>
                                <?php
                                $fechaAsig = new DateTime($devolucion['fecha_asignacion']);
                                $fechaDev = new DateTime($devolucion['fecha_devolucion']);
                                $dias = $fechaAsig->diff($fechaDev)->days;
                                echo $dias . ' días';
                                ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($devolucion['motivo_devolucion'] ?? 'No especificado') ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Información Importante -->
<div class="card mt-4 border-info">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Importante</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-info"><i class="fas fa-shield-alt me-2"></i>Responsabilidades</h6>
                <ul class="small">
                    <li>Cuidar el equipo asignado y mantenerlo en buen estado</li>
                    <li>Reportar cualquier daño o mal funcionamiento inmediatamente</li>
                    <li>No compartir el equipo con personas no autorizadas</li>
                    <li>Devolver el equipo cuando ya no sea necesario</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-info"><i class="fas fa-exclamation-triangle me-2"></i>En caso de Problemas</h6>
                <ul class="small">
                    <li>Reporta inmediatamente cualquier daño al equipo</li>
                    <li>No intentes reparaciones por tu cuenta</li>
                    <li>Contacta al departamento de TI para soporte técnico</li>
                    <li>Guarda el equipo en un lugar seguro cuando no lo uses</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Solicitar Devolución
    $('.btn-solicitar-devolucion').on('click', function() {
        const asignacionId = $(this).data('asignacion-id');
        const equipoNombre = $(this).data('equipo-nombre');
        
        Swal.fire({
            title: 'Solicitar Devolución',
            html: `
                <p>¿Deseas solicitar la devolución de:</p>
                <p class="fw-bold">${equipoNombre}</p>
                <div class="form-group text-start mt-3">
                    <label class="form-label">Motivo de la devolución:</label>
                    <textarea id="motivoDevolucion" class="form-control" rows="3" 
                              placeholder="Ejemplo: Ya no lo necesito, cambio de proyecto, etc."></textarea>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-2"></i>Solicitar Devolución',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const motivo = document.getElementById('motivoDevolucion').value;
                if (!motivo || motivo.trim().length < 5) {
                    Swal.showValidationMessage('Por favor, proporciona un motivo válido (mínimo 5 caracteres)');
                    return false;
                }
                return motivo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar solicitud
                $.ajax({
                    url: 'index.php?route=asignaciones&action=solicitarDevolucion',
                    method: 'POST',
                    data: {
                        asignacion_id: asignacionId,
                        motivo: result.value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '¡Solicitud Enviada!',
                                text: 'Tu solicitud de devolución ha sido registrada. El administrador la revisará pronto.',
                                icon: 'success',
                                confirmButtonColor: '#1B3C53'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'No se pudo registrar la solicitud', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error al procesar tu solicitud', 'error');
                    }
                });
            }
        });
    });
});
</script>

<style>
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.card-img-top {
    border-bottom: 1px solid rgba(0,0,0,0.1);
}
</style>