<?php

namespace App\Controllers;

use App\Core\AuthService;
use App\Core\Validator;
use App\Core\SecurityManager;
use App\Core\RateLimiter;
use App\Core\AuditLogger;

/**
 * Controlador de Autenticación con Seguridad Avanzada
 * Implementa todas las validaciones y protecciones de seguridad profesionales
 */
class AuthController extends BaseController
{
    private AuthService $authService;
    private RateLimiter $rateLimiter;
    private AuditLogger $auditLogger;

    public function __construct()
    {
        // Inicializar servicios
        $this->authService = new AuthService();
        $this->rateLimiter = new RateLimiter();
        $this->auditLogger = new AuditLogger();

        // Headers de seguridad
        SecurityManager::setSecurityHeaders();
    }

    /**
     * Muestra el formulario de login
     */
    public function index()
    {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->authService->isAuthenticated()) {
            redirect('dashboard');
        }

        // Generar CSRF token para el formulario
        $csrfToken = SecurityManager::getCsrfToken();

        $this->render('Views/auth/login.php', [
            'pageTitle' => 'Iniciar Sesión',
            'csrf_token' => $csrfToken
        ], false);
    }

    /**
     * Procesa el formulario de login con validaciones completas de seguridad
     */
    public function login()
    {
        // 1. VALIDACIÓN DE MÉTODO HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            SecurityManager::logSecurityEvent('invalid_method', [
                'method' => $_SERVER['REQUEST_METHOD'],
                'expected' => 'POST'
            ]);
            redirect('login');
        }

        // 2. DETECCIÓN DE BOTS
        if (SecurityManager::isBot()) {
            SecurityManager::logSecurityEvent('bot_detected', [
                'user_agent' => SecurityManager::getUserAgent()
            ]);
            http_response_code(403);
            die('Acceso denegado');
        }

        // 3. VALIDACIÓN DE CSRF TOKEN
        try {
            SecurityManager::verifyCsrfToken();
        } catch (\Exception $e) {
            SecurityManager::logSecurityEvent('csrf_token_invalid', [
                'ip' => SecurityManager::getClientIp(),
                'message' => $e->getMessage()
            ]);
            setFlashMessage('Error de Seguridad', 'Token de seguridad inválido. Por favor, intente nuevamente.', 'error');
            redirect('login');
        }

        // 4. OBTENER Y SANITIZAR DATOS
        $email = Validator::cleanInput($_POST['email'] ?? '', 'email');
        $password = $_POST['password'] ?? ''; // No sanitizar password, solo trim
        $password = trim($password);

        // 5. VALIDACIONES BÁSICAS
        $validator = new Validator([
            'email' => $email,
            'password' => $password
        ]);

        if (!$validator->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:6', 'max:128']
        ])) {
            $this->auditLogger->logFailedLogin(
                $email,
                'Validación fallida: ' . $validator->getFirstError()
            );

            setFlashMessage('Error de Validación', $validator->getFirstError(), 'error');
            redirect('login');
        }

        // 6. RATE LIMITING POR EMAIL
        $emailCheck = $this->rateLimiter->checkAttempts($email, 'email');
        if ($emailCheck['blocked']) {
            $remainingTime = RateLimiter::formatRemainingTime($emailCheck['remaining_time']);

            $this->auditLogger->logFailedLogin(
                $email,
                "Cuenta bloqueada temporalmente ({$emailCheck['attempts']} intentos)"
            );

            setFlashMessage(
                'Cuenta Bloqueada',
                "Demasiados intentos fallidos. Por favor, espere $remainingTime antes de intentar nuevamente.",
                'error'
            );
            redirect('login');
        }

        // 7. RATE LIMITING POR IP
        $ip = SecurityManager::getClientIp();
        $ipCheck = $this->rateLimiter->checkAttempts($ip, 'ip');
        if ($ipCheck['blocked']) {
            $remainingTime = RateLimiter::formatRemainingTime($ipCheck['remaining_time']);

            SecurityManager::logSecurityEvent('ip_blocked', [
                'ip' => $ip,
                'attempts' => $ipCheck['attempts'],
                'email_attempted' => $email
            ]);

            $this->auditLogger->logFailedLogin(
                $email,
                "IP bloqueada temporalmente ({$ipCheck['attempts']} intentos)"
            );

            setFlashMessage(
                'Acceso Bloqueado',
                "Demasiados intentos desde esta ubicación. Espere $remainingTime.",
                'error'
            );
            redirect('login');
        }

        // 8. VERIFICAR RATE LIMIT GLOBAL
        if ($this->rateLimiter->exceedsGlobalRateLimit($ip, 20, 3600)) {
            SecurityManager::logSecurityEvent('global_rate_limit_exceeded', [
                'ip' => $ip,
                'email' => $email
            ]);

            http_response_code(429); // Too Many Requests
            setFlashMessage(
                'Límite Excedido',
                'Demasiadas solicitudes. Por favor, intente más tarde.',
                'error'
            );
            redirect('login');
        }

        // 9. DELAY PROGRESIVO (DIFICULTA ATAQUES DE FUERZA BRUTA)
        $attemptCount = $this->rateLimiter->getAttemptsCount($email, 'email');
        SecurityManager::progressiveDelay($attemptCount, 3);

        // 10. INTENTAR AUTENTICACIÓN
        $loginResult = $this->authService->attemptLogin($email, $password);

        // 11. MANEJAR RESULTADO
        if ($loginResult['success']) {
            // ✅ LOGIN EXITOSO

            // Limpiar intentos fallidos
            $this->rateLimiter->clearAttempts($email, 'email');
            $this->rateLimiter->clearAttempts($ip, 'ip');

            // Registrar acceso exitoso en auditoría
            $this->auditLogger->logSuccessfulLogin($email, $loginResult['user_id'], [
                'rol' => $loginResult['rol'],
                'dispositivo' => SecurityManager::generateDeviceFingerprint()
            ]);

            // Verificar consistencia de IP (opcional pero recomendado)
            $_SESSION['user_ip'] = $ip;
            $_SESSION['last_activity'] = time();

            // Redirigir según rol
            if ($loginResult['rol'] === ROLE_ADMIN) {
                redirect('dashboard');
            } else {
                redirect('dashboard'); // Colaboradores también van al dashboard
            }

        } else {
            // ❌ LOGIN FALLIDO

            // Registrar intento fallido para rate limiting
            $this->rateLimiter->recordAttempt($email, 'email', $ip, [
                'motivo' => $loginResult['reason']
            ]);
            $this->rateLimiter->recordAttempt($ip, 'ip', null, [
                'email' => $email,
                'motivo' => $loginResult['reason']
            ]);

            // Registrar en auditoría
            $this->auditLogger->logFailedLogin($email, $loginResult['reason']);

            // Log de seguridad si es sospechoso
            if ($attemptCount >= 3) {
                SecurityManager::logSecurityEvent('multiple_failed_attempts', [
                    'email' => $email,
                    'ip' => $ip,
                    'attempts' => $attemptCount + 1,
                    'reason' => $loginResult['reason']
                ]);
            }

            // Mensaje genérico (NO revelar si el usuario existe o no)
            setFlashMessage(
                'Error de Autenticación',
                'Credenciales incorrectas. Por favor, verifique su email y contraseña.',
                'error'
            );
            redirect('login');
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Obtener datos antes de cerrar sesión
        $email = $_SESSION['user_email'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Registrar el logout en auditoría
        if ($email) {
            $this->auditLogger->logLogout($email, $userId);
        }

        // Cerrar sesión
        $this->authService->logout();

        setFlashMessage(
            'Sesión Cerrada',
            'Ha cerrado sesión correctamente.',
            'success'
        );

        redirect('login');
    }

    /**
     * Middleware: Verifica timeout de sesión
     * Se debe llamar en cada request autenticado
     */
    public static function checkSessionTimeout()
    {
        if (SecurityManager::checkSessionTimeout(1800)) { // 30 minutos
            session_destroy();
            setFlashMessage(
                'Sesión Expirada',
                'Su sesión ha expirado por inactividad. Por favor, inicie sesión nuevamente.',
                'warning'
            );
            redirect('login');
        }
    }

    /**
     * Middleware: Verifica consistencia de IP
     * Previene session hijacking
     */
    public static function checkIpConsistency()
    {
        if (!SecurityManager::checkIpConsistency()) {
            SecurityManager::logSecurityEvent('ip_change_detected', [
                'session_ip' => $_SESSION['user_ip'] ?? 'unknown',
                'current_ip' => SecurityManager::getClientIp(),
                'user' => $_SESSION['user_email'] ?? 'unknown'
            ]);

            session_destroy();
            setFlashMessage(
                'Alerta de Seguridad',
                'Se detectó un cambio en su dirección IP. Por seguridad, debe iniciar sesión nuevamente.',
                'warning'
            );
            redirect('login');
        }
    }
}
