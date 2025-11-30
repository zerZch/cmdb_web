<div class="page-header">
    <h2><i class="fas fa-chart-bar me-2"></i>Reportes del Sistema</h2>
    <p class="text-muted">Acceda a los diferentes reportes e informes del sistema CMDB</p>
</div>

<!-- Reportes Principales -->
<div class="row g-4 mb-4">
    <!-- Reporte de Inventario -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-primary" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-boxes" style="font-size: 48px; color: #1B3C53;"></i>
                </div>
                <h5 class="card-title">Reporte de Inventario</h5>
                <p class="card-text text-muted">
                    Listado completo de todos los equipos registrados en el sistema con sus características y estado actual.
                </p>
                <a href="index.php?route=reportes&action=inventario" class="btn btn-primary">
                    <i class="fas fa-file-alt me-2"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Historial de Equipos -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-info" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-history" style="font-size: 48px; color: #17a2b8;"></i>
                </div>
                <h5 class="card-title">Historial de Equipos</h5>
                <p class="card-text text-muted">
                    Trazabilidad completa del ciclo de vida de los equipos con todos sus movimientos registrados.
                </p>
                <a href="index.php?route=reportes&action=historialEquipo" class="btn btn-info">
                    <i class="fas fa-clock me-2"></i>Ver Historial
                </a>
            </div>
        </div>
    </div>

    <!-- Equipos por Colaborador -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-success" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-users" style="font-size: 48px; color: #28a745;"></i>
                </div>
                <h5 class="card-title">Equipos por Colaborador</h5>
                <p class="card-text text-muted">
                    Reporte de asignaciones actuales mostrando qué colaborador tiene cada equipo.
                </p>
                <a href="index.php?route=reportes&action=equiposPorColaborador" class="btn btn-success">
                    <i class="fas fa-user-check me-2"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Reportes Secundarios -->
<div class="row g-4 mb-4">
    <!-- Movimientos Recientes -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-warning" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-exchange-alt" style="font-size: 48px; color: #ffc107;"></i>
                </div>
                <h5 class="card-title">Movimientos Recientes</h5>
                <p class="card-text text-muted">
                    Timeline de las últimas actividades y movimientos registrados en el sistema.
                </p>
                <a href="index.php?route=reportes&action=movimientosRecientes" class="btn btn-warning">
                    <i class="fas fa-stream me-2"></i>Ver Movimientos
                </a>
            </div>
        </div>
    </div>

    <!-- Reporte Consolidado -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-dark" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-chart-pie" style="font-size: 48px; color: #343a40;"></i>
                </div>
                <h5 class="card-title">Reporte Consolidado</h5>
                <p class="card-text text-muted">
                    Resumen general del sistema con estadísticas y métricas clave de todas las áreas.
                </p>
                <a href="index.php?route=reportes&action=consolidado" class="btn btn-dark">
                    <i class="fas fa-analytics me-2"></i>Ver Consolidado
                </a>
            </div>
        </div>
    </div>

    <!-- Actividad del Sistema -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-secondary" style="border-width: 2px;">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-chart-line" style="font-size: 48px; color: #6c757d;"></i>
                </div>
                <h5 class="card-title">Actividad Reciente</h5>
                <p class="card-text text-muted">
                    Dashboard de actividad general del sistema con timeline de eventos.
                </p>
                <a href="index.php?route=reportes&action=actividadReciente" class="btn btn-secondary">
                    <i class="fas fa-tasks me-2"></i>Ver Actividad
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Exportaciones Rápidas</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="index.php?route=reportes&action=exportarInventarioCsv" 
                   class="btn btn-success btn-lg w-100">
                    <i class="fas fa-file-excel me-2"></i>
                    Exportar Inventario (CSV)
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="index.php?route=bajas&action=exportarCsv" 
                   class="btn btn-danger btn-lg w-100">
                    <i class="fas fa-file-csv me-2"></i>
                    Exportar Bajas (CSV)
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="index.php?route=donaciones&action=exportarCsv" 
                   class="btn btn-info btn-lg w-100">
                    <i class="fas fa-file-download me-2"></i>
                    Exportar Donaciones (CSV)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Información -->
<div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Información sobre Reportes</h5>
    <p class="mb-0">
        Todos los reportes pueden ser exportados a diferentes formatos (CSV, PDF) para su análisis externo.
        Los datos se actualizan en tiempo real reflejando el estado actual del sistema.
    </p>
</div>
