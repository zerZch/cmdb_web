<?php

namespace App\Core;

/**
 * Clase SecurityManager - Gestión de seguridad
 * Maneja CSRF tokens, configuración de sesiones seguras, y detección de amenazas
 */
class SecurityManager
{
    private const CSRF_TOKEN_NAME = 'csrf_token';
    private const CSRF_TOKEN_LENGTH = 32;

    /**
     * Genera un token CSRF y lo almacena en sesión
     *
     * @return string El token generado
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
        $_SESSION[self::CSRF_TOKEN_NAME] = $token;
        $_SESSION[self::CSRF_TOKEN_NAME . '_time'] = time();

        return $token;
    }

    /**
     * Obtiene el token CSRF actual o genera uno nuevo si no existe
     *
     * @return string El token CSRF
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION[self::CSRF_TOKEN_NAME])) {
            return self::generateCsrfToken();
        }

        // Token expirado (más de 1 hora)
        if (isset($_SESSION[self::CSRF_TOKEN_NAME . '_time'])) {
            $tokenAge = time() - $_SESSION[self::CSRF_TOKEN_NAME . '_time'];
            if ($tokenAge > 3600) {
                return self::generateCsrfToken();
            }
        }

        return $_SESSION[self::CSRF_TOKEN_NAME];
    }

    /**
     * Valida un token CSRF
     *
     * @param string $token El token a validar
     * @return bool True si el token es válido
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION[self::CSRF_TOKEN_NAME]) || empty($token)) {
            return false;
        }

        // Usar hash_equals para prevenir timing attacks
        return hash_equals($_SESSION[self::CSRF_TOKEN_NAME], $token);
    }

    /**
     * Valida el token CSRF del request actual o lanza excepción
     *
     * @throws \Exception Si el token es inválido
     */
    public static function verifyCsrfToken(): void
    {
        $token = $_POST[self::CSRF_TOKEN_NAME] ?? $_GET[self::CSRF_TOKEN_NAME] ?? null;

        if (!self::validateCsrfToken($token)) {
            http_response_code(403);
            throw new \Exception('Token CSRF inválido. Posible ataque detectado.');
        }
    }

    /**
     * Configura sesiones seguras con las mejores prácticas
     */
    public static function configureSecureSession(): void
    {
        // Configurar parámetros de sesión antes de session_start()
        ini_set('session.cookie_httponly', '1');   // No accesible por JavaScript
        ini_set('session.use_only_cookies', '1');  // Solo cookies, no query strings
        ini_set('session.cookie_samesite', 'Strict'); // Protección CSRF

        // Solo en producción con HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', '1'); // Solo por HTTPS
        }

        // Regenerar ID periódicamente
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_strict_mode', '1');

        // Tiempo de vida de la sesión (30 minutos)
        ini_set('session.gc_maxlifetime', '1800');
        ini_set('session.cookie_lifetime', '0'); // Se borra al cerrar navegador
    }

    /**
     * Verifica timeout de sesión por inactividad
     *
     * @param int $timeout Tiempo en segundos (default: 30 minutos)
     * @return bool True si la sesión ha expirado
     */
    public static function checkSessionTimeout(int $timeout = 1800): bool
    {
        if (!isset($_SESSION)) {
            return true;
        }

        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return false;
        }

        $inactive = time() - $_SESSION['last_activity'];

        if ($inactive > $timeout) {
            // Sesión expirada
            return true;
        }

        // Actualizar timestamp de última actividad
        $_SESSION['last_activity'] = time();
        return false;
    }

    /**
     * Verifica si la IP del usuario ha cambiado durante la sesión
     *
     * @return bool True si la IP ha cambiado (posible ataque)
     */
    public static function checkIpConsistency(): bool
    {
        if (!isset($_SESSION)) {
            return true;
        }

        $currentIp = self::getClientIp();

        if (!isset($_SESSION['user_ip'])) {
            $_SESSION['user_ip'] = $currentIp;
            return true;
        }

        return $_SESSION['user_ip'] === $currentIp;
    }

    /**
     * Obtiene la IP real del cliente (incluso detrás de proxies)
     *
     * @return string La IP del cliente
     */
    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Puede contener múltiples IPs, tomar la primera
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Validar que sea una IP válida
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = '0.0.0.0';
        }

        return $ip;
    }

    /**
     * Obtiene el User-Agent del cliente
     *
     * @return string User-Agent sanitizado
     */
    public static function getUserAgent(): string
    {
        return Validator::sanitize($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
    }

    /**
     * Genera un fingerprint único del dispositivo
     * Útil para detectar sesiones inusuales
     *
     * @return string Hash del fingerprint
     */
    public static function generateDeviceFingerprint(): string
    {
        $components = [
            self::getUserAgent(),
            self::getClientIp(),
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Previene ataques de XSS limpiando output
     *
     * @param mixed $data Datos a limpiar
     * @return mixed Datos limpiados
     */
    public static function escape($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }

        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $data;
    }

    /**
     * Verifica si la solicitud proviene de un bot conocido
     *
     * @return bool True si es un bot
     */
    public static function isBot(): bool
    {
        $userAgent = strtolower(self::getUserAgent());

        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python-requests', 'java', 'php', 'go-http-client'
        ];

        foreach ($botPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Añade delay progresivo basado en número de intentos
     * Útil para rate limiting manual
     *
     * @param int $attempts Número de intentos fallidos
     * @param int $maxDelay Delay máximo en segundos
     */
    public static function progressiveDelay(int $attempts, int $maxDelay = 5): void
    {
        if ($attempts <= 0) {
            return;
        }

        $delay = min($attempts, $maxDelay);
        sleep($delay);
    }

    /**
     * Registra un intento sospechoso en logs
     *
     * @param string $type Tipo de amenaza detectada
     * @param array $details Detalles adicionales
     */
    public static function logSecurityEvent(string $type, array $details = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'ip' => self::getClientIp(),
            'user_agent' => self::getUserAgent(),
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'details' => $details
        ];

        $logMessage = json_encode($logData) . PHP_EOL;

        $logFile = __DIR__ . '/../../logs/security.log';
        $logDir = dirname($logFile);

        // Crear directorio de logs si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Escribir en el archivo de log
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    /**
     * Headers de seguridad recomendados
     */
    public static function setSecurityHeaders(): void
    {
        // Prevenir clickjacking
        header('X-Frame-Options: DENY');

        // Prevenir MIME type sniffing
        header('X-Content-Type-Options: nosniff');

        // Habilitar protección XSS del navegador
        header('X-XSS-Protection: 1; mode=block');

        // Content Security Policy básico
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Permissions Policy (antes Feature-Policy)
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
}
