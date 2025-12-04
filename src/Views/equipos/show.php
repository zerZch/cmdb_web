<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?route=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?route=equipos">Equipos</a></li>
        <li class="breadcrumb-item active"><?= e($equipo['codigo_inventario']) ?></li>
    </ol>
</nav>

<!-- Encabezado con acciones -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <i class="fas fa-laptop text-primary me-2"></i>
        <?= e($equipo['marca'] . ' ' . $equipo['modelo']) ?>
    </h3>
    <div>
        <?php if (empty($equipo['codigo_qr'])): ?>
            <button type="button" class="btn btn-info btn-sm me-2" id="btnGenerateQR">
                <i class="fas fa-qrcode me-1"></i>Generar QR
            </button>
        <?php endif; ?>
        
        <?php if (hasRole('admin')): ?>
            <a href="index.php?route=equipos&action=edit&id=<?= $equipo['id'] ?>" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
        <?php endif; ?>
        
        <a href="index.php?route=equipos" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row">
    <!-- COLUMNA IZQUIERDA -->
    <div class="col-lg-8">
        
        <!-- Información General -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Información General
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-muted">Código de Inventario:</strong><br>
                            <span class="h5 text-primary"><?= e($equipo['codigo_inventario']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-muted">Categoría:</strong><br>
                            <span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                <?= e($equipo['categoria_nombre']) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-muted">Marca:</strong><br>
                            <?= e($equipo['marca']) ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-muted">Modelo:</strong><br>
                            <?= e($equipo['modelo']) ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong class="text-muted">Número de Serie:</strong><br>
                            <code><?= e($equipo['numero_serie']) ?></code>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-muted">Estado:</strong><br>
                            <?php
                            $estadoBadge = [
                                'disponible' => 'success',
                                'asignado' => 'info',
                                'mantenimiento' => 'warning',
                                'dañado' => 'danger',
                                'dado_de_baja' => 'dark'
                            ];
                            $badgeClass = $estadoBadge[$equipo['estado']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                <?= ucfirst($equipo['estado']) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong class="text-muted">Ubicación:</strong><br>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            <?= e($equipo['ubicacion'] ?? 'No especificada') ?>
                        </p>
                    </div>
                </div>

                <?php if (!empty($equipo['descripcion'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-0">
                                <strong class="text-muted">Descripción:</strong><br>
                                <?= nl2br(e($equipo['descripcion'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información Financiera y Depreciación -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-dollar-sign"></i> Información Financiera
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-2">Fecha Adquisición</small>
                            <strong>
                                <?php 
                                if (!empty($equipo['fecha_adquisicion'])) {
                                    echo date('d/m/Y', strtotime($equipo['fecha_adquisicion']));
                                } else {
                                    echo '<span class="text-muted">No especificada</span>';
                                }
                                ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-2">Costo Adquisición</small>
                            <strong class="text-success">$<?= number_format($equipo['costo_adquisicion'] ?? 0, 2) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-2">Vida Útil</small>
                            <strong><?= $equipo['vida_util_anos'] ?? 5 ?> años</strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-2">Valor Residual</small>
                            <strong>$<?= number_format($equipo['valor_residual'] ?? 0, 2) ?></strong>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-line text-primary"></i> Cálculo de Depreciación
                </h6>
                
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">Meses Transcurridos</small>
                            <h5 class="mb-0 text-info"><?= $depreciacion['meses_transcurridos'] ?? 0 ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">Dep. Mensual</small>
                            <h5 class="mb-0 text-warning">$<?= number_format($depreciacion['depreciacion_mensual'] ?? 0, 2) ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">Dep. Acumulada</small>
                            <h5 class="mb-0 text-danger">$<?= number_format($depreciacion['depreciacion_acumulada'] ?? 0, 2) ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-2">Valor en Libros</small>
                            <h5 class="mb-0 text-success">$<?= number_format($depreciacion['valor_libro'] ?? 0, 2) ?></h5>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Porcentaje Depreciado</span>
                        <strong class="<?= ($depreciacion['completamente_depreciado'] ?? false) ? 'text-danger' : 'text-primary' ?>">
                            <?= number_format($depreciacion['porcentaje_depreciado'] ?? 0, 2) ?>%
                        </strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar <?= ($depreciacion['completamente_depreciado'] ?? false) ? 'bg-danger' : 'bg-primary' ?>" 
                             role="progressbar" 
                             style="width: <?= min($depreciacion['porcentaje_depreciado'] ?? 0, 100) ?>%">
                            <?= number_format($depreciacion['porcentaje_depreciado'] ?? 0, 1) ?>%
                        </div>
                    </div>
                    
                    <?php if ($depreciacion['completamente_depreciado'] ?? false): ?>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Equipo completamente depreciado.</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

        <!-- COLUMNA DERECHA -->
<div class="col-lg-4">
    
    <!-- Foto del Equipo -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-image"></i> Fotografía
            </h5>
        </div>
        <div class="card-body text-center">
            <?php if (!empty($equipo['foto'])): ?>
                <img src="<?= $equipo['foto'] ?>" 
                     alt="Foto del equipo" 
                     class="img-fluid rounded shadow-sm"
                     style="max-height: 300px; width: 100%; object-fit: cover;">
            <?php else: ?>
                <div class="py-5">
                    <i class="fas fa-camera fa-5x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Sin fotografía</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 🆕 Código QR -->
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-qrcode"></i> Código QR
            </h5>
        </div>
        <div class="card-body text-center" id="qrContainer">
            <?php if (!empty($equipo['codigo_qr'])): ?>
                <!-- QR ya generado -->
                <img src="<?= $equipo['codigo_qr'] ?>" 
                     alt="Código QR del equipo" 
                     class="img-fluid rounded shadow-sm mb-3"
                     style="max-width: 250px;">
                
                <div class="d-grid gap-2">
                    <a href="<?= $equipo['codigo_qr'] ?>" 
                       download="QR_<?= e($equipo['numero_serie']) ?>.png" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i>Descargar QR
                    </a>
                    
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Imprimir QR
                    </button>
                    
                    <?php if (hasRole('admin')): ?>
                        <button type="button" class="btn btn-warning btn-sm" id="btnRegenerateQR">
                            <i class="fas fa-sync-alt me-1"></i>Regenerar QR
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Escanea este código para ver los detalles del equipo
                    </small>
                </div>
            <?php else: ?>
                <!-- QR no generado -->
                <div class="py-4">
                    <i class="fas fa-qrcode fa-5x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Código QR no generado</p>
                    
                    <?php if (hasRole('admin')): ?>
                        <button type="button" class="btn btn-info" id="btnGenerateQR2">
                            <i class="fas fa-qrcode me-1"></i>Generar Código QR
                        </button>
                    <?php else: ?>
                        <p class="text-muted small">Solo administradores pueden generar códigos QR</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

        

    </div>
</div>

<!-- ✅ SCRIPT SOLO UNA VEZ AL FINAL -->
<script>
// Verificar que jQuery esté cargado
if (typeof jQuery === 'undefined') {
    console.error('jQuery no está cargado. Asegúrate de que esté en el layout principal.');
} else {
    $(document).ready(function() {
        // Generar código QR
        $('#btnGenerateQR, #btnGenerateQR2').on('click', function() {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Generando...');
            
            $.ajax({
                url: 'index.php?route=equipos&action=generateQR&id=<?= $equipo['id'] ?>',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Código QR generado!',
                            text: 'El código QR se ha creado exitosamente',
                            confirmButtonText: 'Ver QR'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudo generar el código QR'
                        });
                        btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', {xhr, status, error});
                    console.error('Respuesta:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar el código QR. Revisa la consola para más detalles.'
                    });
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
}
</script>

<style>
@media print {
    body * { visibility: hidden; }
    #qrContainer, #qrContainer * { visibility: visible; }
    #qrContainer {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
    .btn { display: none !important; }
}
</style>