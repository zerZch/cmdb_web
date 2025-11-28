<!-- ============================================
     HEADER DEL DASHBOARD
     ============================================ -->
<div class="page-header">
    <h2><i class="fas fa-chart-line me-2"></i>Dashboard</h2>
    <p class="text-muted">
        Bienvenido de nuevo, <strong><?= e(currentUser()['nombre'] . ' ' . currentUser()['apellido']) ?></strong>
    </p>
</div>

<!-- ============================================
     TARJETAS DE ESTADÍSTICAS - FILA 1
     ============================================ -->
<div class="row g-4 mb-4">
    <!-- Total de Equipos -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                            Total de Equipos
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #1B3C53; font-size: 36px;">
                            <?= $estadisticas['total'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Disponibles -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                            Disponibles
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #28a745; font-size: 36px;">
                            <?= $estadisticas['disponibles'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Asignados -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                            Asignados
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #17a2b8; font-size: 36px;">
                            <?= $estadisticas['asignados'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Dañados -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                            Dañados
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #dc3545; font-size: 36px;">
                            <?= $estadisticas['danados'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     TARJETAS DE ESTADÍSTICAS - FILA 2
     ============================================ -->
<div class="row g-4 mb-4">
    <!-- Equipos en Mantenimiento -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                            Mantenimiento
                        </p>
                        <h2 class="mb-0 fw-bold" style="color: #ffc107; font-size: 36px;">
                            <?= $estadisticas['mantenimiento'] ?>
                        </h2>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (hasRole(ROLE_ADMIN)): ?>
        <!-- Total Usuarios -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card secondary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                                Total Usuarios
                            </p>
                            <h2 class="mb-0 fw-bold" style="color: #234C6A; font-size: 36px;">
                                <?= $totalUsuarios ?>
                            </h2>
                        </div>
                        <div class="stat-icon secondary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categorías -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted mb-2 fw-semibold" style="font-size: 13px; letter-spacing: 0.5px;">
                                Categorías
                            </p>
                            <h2 class="mb-0 fw-bold" style="color: #1B3C53; font-size: 36px;">
                                <?= $totalCategorias ?>
                            </h2>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ============================================
     GRÁFICOS
     ============================================ -->
<div class="row g-4 mb-4">
    <!-- Gráfico de Equipos por Categoría -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2" style="color: #1B3C53;"></i>
                    Equipos por Categoría
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="categoriaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Estado de Equipos -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-bar me-2" style="color: #1B3C53;"></i>
                    Estado de Equipos
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="estadoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     INFORMACIÓN DEL SISTEMA
     ============================================ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #1B3C53, #234C6A); color: white;">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Información del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong><i class="fas fa-desktop me-2" style="color: #1B3C53;"></i>Sistema:</strong>
                            <?= APP_NAME ?> v<?= APP_VERSION ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong><i class="fas fa-user me-2" style="color: #1B3C53;"></i>Usuario:</strong>
                            <?= e(currentUser()['nombre'] . ' ' . currentUser()['apellido']) ?>
                            <span class="badge bg-secondary ms-2"><?= e(ucfirst(currentUser()['rol'])) ?></span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-2">
                            <strong><i class="fas fa-calendar me-2" style="color: #1B3C53;"></i>Fecha:</strong>
                            <?= date('d/m/Y H:i:s') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     SCRIPTS DE GRÁFICOS (Chart.js)
     ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Configuración global de Chart.js
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = '#6c757d';

    // ============================================
    // GRÁFICO DE EQUIPOS POR CATEGORÍA
    // ============================================
    const categoriaData = {
        labels: [<?php echo implode(',', array_map(function($cat) {
            return "'" . addslashes($cat['categoria']) . "'";
        }, $equiposPorCategoria)); ?>],
        datasets: [{
            label: 'Equipos',
            data: [<?php echo implode(',', array_column($equiposPorCategoria, 'total')); ?>],
            backgroundColor: [
                '#1B3C53',  // Color primario
                '#234C6A',  // Color secundario
                '#456882',  // Color terciario
                '#28a745',  // Success
                '#17a2b8',  // Info
                '#ffc107',  // Warning
                '#dc3545',  // Danger
                '#6c757d'   // Secondary
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    };

    new Chart(document.getElementById('categoriaChart'), {
        type: 'doughnut',
        data: categoriaData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1B3C53',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // ============================================
    // GRÁFICO DE ESTADO DE EQUIPOS
    // ============================================
    const estadoData = {
        labels: ['Disponibles', 'Asignados', 'Dañados', 'Mantenimiento'],
        datasets: [{
            label: 'Cantidad de Equipos',
            data: [
                <?= $estadisticas['disponibles'] ?>,
                <?= $estadisticas['asignados'] ?>,
                <?= $estadisticas['danados'] ?>,
                <?= $estadisticas['mantenimiento'] ?>
            ],
            backgroundColor: [
                '#28a745',  // Success - Disponibles
                '#17a2b8',  // Info - Asignados
                '#dc3545',  // Danger - Dañados
                '#ffc107'   // Warning - Mantenimiento
            ],
            borderWidth: 0,
            borderRadius: 8,
            barThickness: 50
        }]
    };

    new Chart(document.getElementById('estadoChart'), {
        type: 'bar',
        data: estadoData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    },
                    grid: {
                        color: 'rgba(27, 60, 83, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1B3C53',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            }
        }
    });
</script>
