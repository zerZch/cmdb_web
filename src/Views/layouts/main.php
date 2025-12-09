<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistema CMDB - Gestión de Inventario de Equipos">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= APP_NAME ?></title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
          crossorigin="anonymous">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" />

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<!-- ============================================
     SIDEBAR MEJORADO
     ============================================ -->
<aside class="sidebar" id="sidebar">
    <!-- Logo/Brand -->
    <div class="sidebar-brand">
        <i class="fas fa-server"></i>
        <h4><?= APP_NAME ?></h4>
        <small>Versión <?= APP_VERSION ?></small>
    </div>

    <nav class="sidebar-menu" role="navigation" aria-label="Menú principal">
    
    <a href="index.php?route=dashboard"
       class="<?= ($_GET['route'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
    </a>

    <?php if (hasRole(ROLE_ADMIN)): ?>
    
    <hr class="sidebar-divider">

    <a href="index.php?route=colaboradores"
        class="<?= ($_GET['route'] ?? '') === 'colaboradores' ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>Colaboradores</span>
    </a>

    <a href="index.php?route=equipos"
       class="<?= ($_GET['route'] ?? '') === 'equipos' ? 'active' : '' ?>">
        <i class="fas fa-laptop"></i>
        <span>Equipos</span>
    </a>

    <a href="index.php?route=categorias"
       class="<?= ($_GET['route'] ?? '') === 'categorias' ? 'active' : '' ?>">
        <i class="fas fa-tags"></i>
        <span>Categorías</span>
    </a>

    <a href="index.php?route=bajas"
       class="<?= ($_GET['route'] ?? '') === 'bajas' ? 'active' : '' ?>">
        <i class="fas fa-trash-alt"></i>
        <span>Bajas</span>
    </a>

    <a href="index.php?route=donaciones"
       class="<?= ($_GET['route'] ?? '') === 'donaciones' ? 'active' : '' ?>">
        <i class="fas fa-hand-holding-heart"></i>
        <span>Donaciones</span>
    </a>

    <a href="index.php?route=asignaciones"
       class="<?= ($_GET['route'] ?? '') === 'asignaciones' ? 'active' : '' ?>">
        <i class="fas fa-exchange-alt"></i>
        <span>Asignaciones</span>
    </a>

    <a href="index.php?route=necesidades"
       class="<?= ($_GET['route'] ?? '') === 'necesidades' ? 'active' : '' ?>">
        <i class="fas fa-clipboard-check"></i>
        <span>Solicitudes</span>
    </a>

    <hr class="sidebar-divider">

    <?php endif; ?>
    <a href="index.php?route=reportes"
       class="<?= ($_GET['route'] ?? '') === 'reportes' ? 'active' : '' ?>">
        <i class="fas fa-chart-bar"></i>
        <span>Reportes</span>
    </a>

    <?php if (hasRole(ROLE_ADMIN)): ?>
        <a href="index.php?route=usuarios"
           class="<?= ($_GET['route'] ?? '') === 'usuarios' ? 'active' : '' ?>">
            <i class="fas fa-user-shield"></i>
            <span>Administración</span>
        </a>
    <?php endif; ?>

    <?php if (!hasRole(ROLE_ADMIN)): ?> 

        <a href="index.php?route=asignaciones&action=misEquipos"
           class="<?= (($_GET['route'] ?? '') === 'asignaciones' && ($_GET['action'] ?? '') === 'misEquipos') ? 'active' : '' ?>">
            <i class="fas fa-desktop"></i>
            <span>Mis Equipos</span>
        </a>

        <a href="index.php?route=asignaciones&action=historialColaborador"
           class="<?= (($_GET['route'] ?? '') === 'asignaciones' && ($_GET['action'] ?? '') === 'historialColaborador') ? 'active' : '' ?>">
            <i class="fas fa-history"></i>
            <span>Historial de Asignaciones</span>
        </a>

        <a href="index.php?route=necesidades&action=misSolicitudes"
           class="<?= (($_GET['route'] ?? '') === 'necesidades' && ($_GET['action'] ?? '') === 'misSolicitudes') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Mis Solicitudes</span>
        </a>
    
    <?php endif; ?>
    <hr class="sidebar-divider">

    <a href="#" onclick="event.preventDefault(); confirmLogout();" class="logout-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Cerrar Sesión</span>
    </a>
</nav>
</aside>
    <!-- ============================================
         CONTENIDO PRINCIPAL
         ============================================ -->
    <main class="main-content">
        <!-- Header/Navbar -->
        <header class="header">
            <div class="header-title">
                <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>

            <!-- Menú de Usuario -->
            <div class="user-menu">
                <div class="user-info">
                    <div class="name">
                        <?= e(currentUser()['nombre'] . ' ' . currentUser()['apellido']) ?>
                    </div>
                    <div class="role">
                        <?= e(ucfirst(currentUser()['rol'])) ?>
                    </div>
                </div>

                <!-- Avatar del Usuario -->
                <div class="user-avatar" title="<?= e(currentUser()['nombre']) ?>">
                    <?= strtoupper(substr(currentUser()['nombre'], 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- Área de Contenido -->
        <div class="content-area">
            <?= $content ?>
        </div>
    </main>

    <!-- ============================================
         SCRIPTS
         ============================================ -->

    <!-- jQuery 3.7 -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
            integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
            crossorigin="anonymous"></script>

    <!-- Bootstrap 5.3 Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Scripts Personalizados -->
    <script>
        // ============================================
        // CONFIGURACIÓN DE DATATABLES
        // ============================================
        $(document).ready(function() {
            // Configuración global de DataTables
            if ($.fn.DataTable) {
                $('.dataTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                    order: [[0, 'desc']]
                });
            }
        });

        // ============================================
        // FUNCIÓN DE CERRAR SESIÓN
        // ============================================
        function confirmLogout() {
            Swal.fire({
                title: '¿Cerrar Sesión?',
                text: '¿Estás seguro que deseas cerrar tu sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1B3C53',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Sí, cerrar sesión',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Cerrando sesión...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirigir a logout
                    window.location.href = 'index.php?route=logout&action=logout';
                }
            });
        }

        // ============================================
        // MENSAJES FLASH CON SWEETALERT2
        // ============================================
        <?php
        $flash = getFlashMessage();
        if ($flash):
        ?>
        Swal.fire({
            title: '<?= addslashes($flash['title']) ?>',
            text: '<?= addslashes($flash['text']) ?>',
            icon: '<?= $flash['icon'] ?>',
            confirmButtonColor: '#1B3C53',
            confirmButtonText: 'Aceptar',
            timer: 3000,
            timerProgressBar: true
        });
        <?php endif; ?>

        // ============================================
        // TOOLTIPS DE BOOTSTRAP
        // ============================================
        $(document).ready(function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // ============================================
        // ANIMACIÓN DE ENTRADA DE CONTENIDO
        // ============================================
        $(document).ready(function() {
            $('.content-area').addClass('fade-in');
        });

        // ============================================
        // PREVENIR DOBLE SUBMIT EN FORMULARIOS
        // ============================================
        $('form').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    </script>

    <!-- Scripts adicionales específicos de cada página -->
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
</body>
</html>