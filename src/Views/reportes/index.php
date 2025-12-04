<div class="page-header">
    <h2><i class="fas fa-chart-bar me-2"></i>Centro de Reportes</h2>
    <p class="text-muted">Acceda a los diferentes reportes e informes del sistema de inventario</p>
</div>

<!-- Reportes Esenciales -->
<div class="row g-4 mb-4">
    <!-- 1. Inventario por Categoría -->
    <div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm hover-card">
        <div class="card-body text-center">
            <div class="mb-3">
                <div class="report-icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-boxes fa-2x text-primary"></i>
                </div>
            </div>
            <h5 class="card-title fw-bold">Inventario por Categoría</h5>
            <p class="card-text text-muted small">
                Listado de equipos agrupados por categoría con totales, disponibles y asignados. Exportable a Excel.
            </p>
            <div class="d-grid gap-2">
                <a href="index.php?route=reportes&action=inventarioPorCategoria" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>
    <!-- 2. Depreciación -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="report-icon-wrapper bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-chart-line fa-2x text-warning"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold">Depreciación de Equipos</h5>
                <p class="card-text text-muted small">
                    Análisis de valor actual vs valor de compra. Alertas de equipos próximos a depreciarse completamente.
                </p>
                <div class="d-grid gap-2">
                    <a href="index.php?route=equipos&action=reporte-depreciacion" class="btn btn-warning">
                        <i class="fas fa-eye me-2"></i>Ver Reporte
                    </a>
                    <a href="index.php?route=equipos&action=reporte-depreciacion&export=excel" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Equipos por Colaborador -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="report-icon-wrapper bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user-tag fa-2x text-success"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold">Equipos por Colaborador</h5>
                <p class="card-text text-muted small">
                    Reporte de asignaciones actuales con estado, fecha de asignación y ubicación del colaborador.
                </p>
                <div class="d-grid gap-2">
                    <a href="index.php?route=reportes&action=equiposPorColaborador" class="btn btn-success">
                        <i class="fas fa-eye me-2"></i>Ver Reporte
                    </a>
                    <a href="index.php?route=reportes&action=equiposPorColaborador&export=excel" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Historial de Movimientos -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="report-icon-wrapper bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-history fa-2x text-info"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold">Historial de Movimientos</h5>
                <p class="card-text text-muted small">
                    Trazabilidad completa de equipos: asignaciones, cambios de estado, observaciones técnicas.
                </p>
                <div class="d-grid gap-2">
                    <a href="index.php?route=reportes&action=historialEquipo" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>Ver Historial
                    </a>
                    <a href="index.php?route=reportes&action=historialEquipo&export=excel" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Equipos por Estado -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="report-icon-wrapper bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-tasks fa-2x text-secondary"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold">Equipos por Estado</h5>
                <p class="card-text text-muted small">
                    Control de inventario por estado: disponibles, asignados, en revisión, descarte y donados.
                </p>
                <div class="d-grid gap-2">
                    <a href="index.php?route=reportes&action=equiposPorEstado" class="btn btn-secondary">
                        <i class="fas fa-eye me-2"></i>Ver Reporte
                    </a>
                    <a href="index.php?route=reportes&action=equiposPorEstado&export=excel" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Ejecutivo (opcional) -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-card border-primary">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="report-icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-chart-pie fa-2x text-primary"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold">Dashboard Ejecutivo</h5>
                <p class="card-text text-muted small">
                    Vista consolidada con KPIs principales, gráficos y métricas clave del sistema.
                </p>
                <div class="d-grid">
                    <a href="index.php?route=dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Ir al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen Rápido -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de Reportes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-sync text-primary fa-2x me-3"></i>
                            <div>
                                <strong>Datos en Tiempo Real</strong>
                                <p class="text-muted small mb-0">Todos los reportes reflejan el estado actual del sistema</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-excel text-success fa-2x me-3"></i>
                            <div>
                                <strong>Exportación a Excel</strong>
                                <p class="text-muted small mb-0">Descarga reportes en formato Excel para análisis externo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-filter text-info fa-2x me-3"></i>
                            <div>
                                <strong>Filtros Avanzados</strong>
                                <p class="text-muted small mb-0">Filtra por fecha, categoría, estado y más opciones</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
.hover-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    border-color: rgba(var(--bs-primary-rgb), 0.3);
}

.report-icon-wrapper {
    transition: all 0.3s ease;
}

.hover-card:hover .report-icon-wrapper {
    transform: scale(1.1);
}

.card-title {
    color: #1B3C53;
}
</style>