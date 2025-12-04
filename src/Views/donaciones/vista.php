<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-hand-holding-heart me-2"></i>Detalle de Donación</h2>
            <p class="text-muted mb-0">Registro #<?= $donacion['id'] ?></p>
        </div>
        <a href="index.php?route=donaciones" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
        </a>
    </div>
</div>

<!-- Información de la Donación -->
<div class="row g-4">
    <!-- Card Principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Información de la Donación
                </h5>
            </div>
            <div class="card-body">
                <!-- Información del Equipo -->
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-laptop me-2"></i>Equipo Donado
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-tag me-2 text-muted"></i>Nombre:</strong>
                            <p class="mb-0"><?= e($donacion['equipo_nombre']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-barcode me-2 text-muted"></i>Serie:</strong>
                            <p class="mb-0"><?= e($donacion['equipo_serie'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-folder me-2 text-muted"></i>Categoría:</strong>
                            <p class="mb-0"><?= e($donacion['categoria_nombre']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-flag me-2 text-muted"></i>Estado:</strong>
                            <p class="mb-0">
                                <span class="badge bg-success">Donado</span>
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Información de la Entidad -->
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-building me-2"></i>Entidad Beneficiada
                    </h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <strong><i class="fas fa-landmark me-2 text-muted"></i>Nombre de la Entidad:</strong>
                            <p class="mb-0 fs-5 text-dark"><?= e($donacion['entidad_beneficiada']) ?></p>
                        </div>
                        <?php if (!empty($donacion['contacto_entidad'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user me-2 text-muted"></i>Persona de Contacto:</strong>
                            <p class="mb-0"><?= e($donacion['contacto_entidad']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($donacion['telefono_entidad'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2 text-muted"></i>Teléfono:</strong>
                            <p class="mb-0"><?= e($donacion['telefono_entidad']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($donacion['email_entidad'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong>
                            <p class="mb-0">
                                <a href="mailto:<?= e($donacion['email_entidad']) ?>">
                                    <?= e($donacion['email_entidad']) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($donacion['direccion_entidad'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>Dirección:</strong>
                            <p class="mb-0"><?= e($donacion['direccion_entidad']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Detalles de la Donación -->
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>Detalles de la Donación
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-day me-2 text-muted"></i>Fecha de Donación:</strong>
                            <p class="mb-0"><?= date('d/m/Y', strtotime($donacion['fecha_donacion'])) ?></p>
                        </div>
                        
                        <?php if (!empty($donacion['valor_donacion'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-dollar-sign me-2 text-muted"></i>Valor Estimado:</strong>
                            <p class="mb-0">$<?= number_format($donacion['valor_donacion'], 2) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-tie me-2 text-muted"></i>Registrado por:</strong>
                            <p class="mb-0"><?= e($donacion['usuario_nombre']) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-clock me-2 text-muted"></i>Fecha de Registro:</strong>
                            <p class="mb-0"><?= date('d/m/Y H:i', strtotime($donacion['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <?php if (!empty($donacion['observaciones'])): ?>
                <hr>
                <div class="mb-3">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-comment-alt me-2"></i>Observaciones
                    </h6>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= nl2br(e($donacion['observaciones'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Certificado -->
                <?php if (!empty($donacion['certificado_donacion'])): ?>
                <hr>
                <div class="mb-3">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-certificate me-2"></i>Certificado de Donación
                    </h6>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                        <div>
                            <p class="mb-1"><strong>Archivo adjunto:</strong></p>
                            <a href="<?= BASE_URL ?>uploads/donaciones/<?= e($donacion['certificado_donacion']) ?>" 
                               class="btn btn-sm btn-danger" 
                               target="_blank">
                                <i class="fas fa-download me-2"></i>Descargar Certificado
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar - Acciones -->
    <div class="col-lg-4">
        <!-- Estado -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Estado
                </h6>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-hand-holding-heart fa-4x text-success mb-3"></i>
                <h5 class="text-success mb-2">Donación Completada</h5>
                <p class="text-muted mb-0">
                    El equipo fue donado exitosamente a la entidad beneficiada.
                </p>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Acciones
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Generar Certificado -->
                    <button class="btn btn-primary" onclick="generarCertificado()">
                        <i class="fas fa-certificate me-2"></i>Generar Certificado
                    </button>

                    <!-- Imprimir -->
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>

                    <!-- Exportar a PDF -->
                    <button class="btn btn-danger" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                    </button>

                    <hr>

                    <!-- Volver -->
                    <a href="index.php?route=donaciones" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>Cronología
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($donacion['created_at'])) ?>
                            </small>
                            <p class="mb-0">Donación registrada</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">
                                <?= date('d/m/Y', strtotime($donacion['fecha_donacion'])) ?>
                            </small>
                            <p class="mb-0">Equipo entregado a la entidad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para Timeline -->
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    padding-left: 10px;
}

/* Estilos de Impresión */
@media print {
    .card-header {
        background: white !important;
        color: black !important;
    }
    
    .btn, .sidebar, .header {
        display: none !important;
    }
    
    .col-lg-4 {
        display: none !important;
    }
    
    .col-lg-8 {
        width: 100% !important;
    }
}
</style>

<!-- Scripts -->
<script>
function generarCertificado() {
    Swal.fire({
        title: 'Generar Certificado',
        text: '¿Desea generar el certificado de donación?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1B3C53',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la lógica para generar el certificado
            window.location.href = 'index.php?route=donaciones&action=generarCertificado&id=<?= $donacion['id'] ?>';
        }
    });
}

function exportarPDF() {
    window.print();
}
</script>
