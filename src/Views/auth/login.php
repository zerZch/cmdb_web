<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ?? 'Login' ?> - <?= APP_NAME ?></title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ============================================
           VARIABLES Y CONFIGURACIÓN GLOBAL
           ============================================ */
        :root {
            --color-primary: #1B3C53;
            --color-secondary: #234C6A;
            --color-tertiary: #456882;
            --color-background: #E3E3E3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--color-background);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* ============================================
           BLOBS DECORATIVOS (Estilo Mangools)
           ============================================ */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        .blob-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, var(--color-secondary), var(--color-tertiary));
            bottom: -150px;
            right: -150px;
            animation-delay: 7s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, var(--color-tertiary), var(--color-secondary));
            top: 50%;
            right: -100px;
            animation-delay: 14s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -30px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        /* ============================================
           CONTENEDOR PRINCIPAL
           ============================================ */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        /* ============================================
           CARD DE LOGIN
           ============================================ */
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(27, 60, 83, 0.15);
            padding: 48px 40px;
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
           LOGO Y TÍTULO
           ============================================ */
        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo i {
            font-size: 48px;
            color: var(--color-primary);
            margin-bottom: 16px;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .login-logo h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--color-primary);
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 16px;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 32px;
        }

        /* ============================================
           FORMULARIO
           ============================================ */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-tertiary);
            font-size: 18px;
            z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e0e7ed;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-weight: 500;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(27, 60, 83, 0.1);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        /* ============================================
           BOTÓN DE LOGIN
           ============================================ */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 24px;
            box-shadow: 0 4px 14px rgba(27, 60, 83, 0.3);
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(27, 60, 83, 0.4);
            background: linear-gradient(135deg, var(--color-secondary), var(--color-tertiary));
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-right: 8px;
        }

        /* ============================================
           LINKS
           ============================================ */
        .form-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e0e7ed;
        }

        .form-link {
            color: var(--color-tertiary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .form-link:hover {
            color: var(--color-primary);
            text-decoration: none;
        }

        .form-link i {
            margin-right: 6px;
        }

        /* ============================================
           FOOTER CON PRODUCTOS
           ============================================ */
        .login-footer {
            margin-top: 20px;
            text-align: center;
        }

        .products-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-bottom: 12px;
            padding: 0;
            list-style: none;
        }

        .products-list li {
            display: inline-flex;
            align-items: center;
            color: var(--color-primary);
            font-size: 13px;
            font-weight: 600;
        }

        .products-list li i {
            margin-right: 6px;
            color: var(--color-tertiary);
            font-size: 14px;
        }

        .copyright {
            color: #6c757d;
            font-size: 13px;
            margin-top: 8px;
        }

        /* ============================================
           INFO DE CREDENCIALES
           ============================================ */
        .credentials-box {
            background: linear-gradient(135deg, rgba(27, 60, 83, 0.05), rgba(35, 76, 106, 0.05));
            border: 2px solid rgba(27, 60, 83, 0.1);
            border-radius: 16px;
            padding: 20px;
            margin-top: 24px;
        }

        .credentials-box h6 {
            color: var(--color-primary);
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .credentials-box h6 i {
            margin-right: 8px;
            color: var(--color-tertiary);
        }

        .credential-item {
            background: white;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .credential-item:last-child {
            margin-bottom: 0;
        }

        .credential-badge {
            background: var(--color-primary);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credential-data {
            flex: 1;
            margin-left: 12px;
            font-family: 'Courier New', monospace;
            color: var(--color-primary);
            font-weight: 600;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 576px) {
            .login-card {
                padding: 36px 28px;
            }

            .login-logo h1 {
                font-size: 24px;
            }

            .login-subtitle {
                font-size: 15px;
            }

            .form-links {
                flex-direction: column;
                gap: 12px;
            }

            .products-list {
                flex-direction: column;
                gap: 8px;
            }

            .blob {
                filter: blur(60px);
            }
        }

        /* ============================================
           ANIMACIONES ADICIONALES
           ============================================ */
        .fade-in {
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Blobs decorativos en las esquinas -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Contenedor principal del login -->
    <div class="login-wrapper">
        <!-- Logo y título -->
        <div class="login-logo">
            <i class="fas fa-server"></i>
            <h1><?= APP_NAME ?></h1>
            <p class="login-subtitle">Good to see you again</p>
        </div>

        <!-- Card de login -->
        <div class="login-card">
            <!-- Formulario de login -->
            <form id="loginForm" method="POST" action="index.php?route=login&action=login" novalidate>
                <!-- CSRF Token (protección contra ataques) -->
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                <!-- Campo de Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="your@email.com"
                               required
                               autocomplete="email"
                               autofocus>
                    </div>
                </div>

                <!-- Campo de Contraseña -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               required
                               autocomplete="current-password">
                    </div>
                </div>

                <!-- Botón de login -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>

                <!-- Links de navegación -->
                <div class="form-links">
                    <a href="#" class="form-link" onclick="event.preventDefault(); showRegisterInfo();">
                        <i class="fas fa-user-plus"></i>
                        Don't have account?
                    </a>
                    <a href="#" class="form-link" onclick="event.preventDefault(); showForgotPassword();">
                        <i class="fas fa-key"></i>
                        Forgot password?
                    </a>
                </div>
            </form>

            <!-- Box de credenciales de prueba -->
            <div class="credentials-box">
                <h6>
                    <i class="fas fa-info-circle"></i>
                    Test Credentials
                </h6>
                <div class="credential-item">
                    <span class="credential-badge">Admin</span>
                    <span class="credential-data">admin@cmdb.com / admin123</span>
                </div>
                <div class="credential-item">
                    <span class="credential-badge">User</span>
                    <span class="credential-data">colaborador@cmdb.com / colab123</span>
                </div>
            </div>
        </div>

        <!-- Footer con productos -->
        <div class="login-footer fade-in">
            <ul class="products-list">
                <li><i class="fas fa-server"></i> Inventory</li>
                <li><i class="fas fa-users"></i> Users</li>
                <li><i class="fas fa-tags"></i> Categories</li>
                <li><i class="fas fa-chart-line"></i> Analytics</li>
            </ul>
            <p class="copyright">
                © <?= date('Y') ?> <?= APP_NAME ?> v<?= APP_VERSION ?> - All rights reserved
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ============================================
        // VALIDACIÓN DEL FORMULARIO
        // ============================================
        (function() {
            'use strict';

            const form = document.getElementById('loginForm');

            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();

                // Validaciones
                let isValid = true;
                let errorMessage = '';

                // Validar email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    isValid = false;
                    errorMessage = 'Email address is required';
                } else if (!emailRegex.test(email)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }

                // Validar contraseña
                if (!password) {
                    isValid = false;
                    errorMessage = 'Password is required';
                }

                if (!isValid) {
                    Swal.fire({
                        title: 'Validation Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#1B3C53',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Signing in...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar formulario
                    form.submit();
                }
            }, false);
        })();

        // ============================================
        // MENSAJES FLASH
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
            confirmButtonText: 'OK'
        });
        <?php endif; ?>

        // ============================================
        // FUNCIONES ADICIONALES
        // ============================================

        // Prevenir espacios en email
        document.getElementById('email').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\s/g, '');
        });

        // Función para mostrar info de registro
        function showRegisterInfo() {
            Swal.fire({
                title: 'Registration',
                html: '<p>To create a new account, please contact your system administrator.</p><p class="text-muted mt-3"><small>Only authorized administrators can create user accounts.</small></p>',
                icon: 'info',
                confirmButtonColor: '#1B3C53',
                confirmButtonText: 'Understood'
            });
        }

        // Función para forgot password
        function showForgotPassword() {
            Swal.fire({
                title: 'Forgot Password?',
                html: '<p>To reset your password, please contact your system administrator.</p><p class="text-muted mt-3"><small>For security reasons, password resets must be handled by administrators.</small></p>',
                icon: 'info',
                confirmButtonColor: '#1B3C53',
                confirmButtonText: 'OK'
            });
        }

        // Doble click para mostrar/ocultar contraseña
        document.getElementById('password').addEventListener('dblclick', function() {
            const type = this.type === 'password' ? 'text' : 'password';
            this.type = type;

            // Volver a password después de 2 segundos
            if (type === 'text') {
                setTimeout(() => {
                    this.type = 'password';
                }, 2000);
            }
        });

        // Animación de entrada para elementos
        window.addEventListener('load', function() {
            document.querySelectorAll('.fade-in').forEach((el, index) => {
                el.style.animationDelay = (index * 0.1) + 's';
            });
        });
    </script>
</body>
</html>
