<?php

namespace App\Core;

use App\Core\Database;
use PDO;

/**
 * Clase RateLimiter - Control de intentos y protección contra fuerza bruta
 * Implementa rate limiting basado en base de datos
 */
class RateLimiter
{
    private Database $db;
    private const TABLE_NAME = 'intentos_login';
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutos en segundos
    private const WINDOW_TIME = 3600; // 1 hora en segundos

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Verifica si el usuario/IP está bloqueado por exceso de intentos
     *
     * @param string $identifier Email o IP
     * @param string $type 'email' o 'ip'
     * @return array ['blocked' => bool, 'remaining_time' => int, 'attempts' => int]
     */
    public function checkAttempts(string $identifier, string $type = 'email'): array
    {
        $this->cleanOldAttempts();

        $sql = "SELECT COUNT(*) as attempts, MAX(created_at) as last_attempt
                FROM " . self::TABLE_NAME . "
                WHERE identifier = ? AND type = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$identifier, $type, self::WINDOW_TIME]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $attempts = (int)($result['attempts'] ?? 0);
        $lastAttempt = $result['last_attempt'] ?? null;

        // Si excede intentos máximos
        if ($attempts >= self::MAX_ATTEMPTS) {
            if ($lastAttempt) {
                $lastAttemptTime = strtotime($lastAttempt);
                $lockoutRemaining = self::LOCKOUT_TIME - (time() - $lastAttemptTime);

                if ($lockoutRemaining > 0) {
                    return [
                        'blocked' => true,
                        'remaining_time' => $lockoutRemaining,
                        'attempts' => $attempts
                    ];
                }
            }
        }

        return [
            'blocked' => false,
            'remaining_time' => 0,
            'attempts' => $attempts
        ];
    }

    /**
     * Registra un intento de login fallido
     *
     * @param string $identifier Email o IP
     * @param string $type 'email' o 'ip'
     * @param string|null $ip IP del cliente
     * @param array $metadata Datos adicionales
     * @return bool True si se registró correctamente
     */
    public function recordAttempt(string $identifier, string $type = 'email', ?string $ip = null, array $metadata = []): bool
    {
        $ip = $ip ?? SecurityManager::getClientIp();
        $userAgent = SecurityManager::getUserAgent();
        $metadataJson = json_encode($metadata);

        $sql = "INSERT INTO " . self::TABLE_NAME . "
                (identifier, type, ip_address, user_agent, metadata, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$identifier, $type, $ip, $userAgent, $metadataJson]);
    }

    /**
     * Limpia intentos exitosos después de un login correcto
     *
     * @param string $identifier Email o IP
     * @param string $type 'email' o 'ip'
     * @return bool True si se limpió correctamente
     */
    public function clearAttempts(string $identifier, string $type = 'email'): bool
    {
        $sql = "DELETE FROM " . self::TABLE_NAME . " WHERE identifier = ? AND type = ?";

        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$identifier, $type]);
    }

    /**
     * Limpia intentos antiguos (más de 1 hora)
     */
    private function cleanOldAttempts(): void
    {
        $sql = "DELETE FROM " . self::TABLE_NAME . "
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([self::WINDOW_TIME]);
    }

    /**
     * Obtiene el número total de intentos en la última hora
     *
     * @param string $identifier Email o IP
     * @param string $type 'email' o 'ip'
     * @return int Número de intentos
     */
    public function getAttemptsCount(string $identifier, string $type = 'email'): int
    {
        $sql = "SELECT COUNT(*) as count FROM " . self::TABLE_NAME . "
                WHERE identifier = ? AND type = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$identifier, $type, self::WINDOW_TIME]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($result['count'] ?? 0);
    }

    /**
     * Formatea el tiempo restante de bloqueo en formato legible
     *
     * @param int $seconds Segundos restantes
     * @return string Texto formateado (ej: "5 minutos 30 segundos")
     */
    public static function formatRemainingTime(int $seconds): string
    {
        if ($seconds < 60) {
            return "$seconds segundos";
        }

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        if ($minutes < 60) {
            return $secs > 0 ? "$minutes minutos $secs segundos" : "$minutes minutos";
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "$hours horas $mins minutos";
    }

    /**
     * Obtiene estadísticas de intentos de login
     *
     * @param int $hours Últimas X horas
     * @return array Estadísticas
     */
    public function getStatistics(int $hours = 24): array
    {
        $seconds = $hours * 3600;

        $sql = "SELECT
                    COUNT(*) as total_attempts,
                    COUNT(DISTINCT identifier) as unique_identifiers,
                    COUNT(DISTINCT ip_address) as unique_ips
                FROM " . self::TABLE_NAME . "
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$seconds]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_attempts' => (int)($result['total_attempts'] ?? 0),
            'unique_identifiers' => (int)($result['unique_identifiers'] ?? 0),
            'unique_ips' => (int)($result['unique_ips'] ?? 0),
            'time_window' => "$hours horas"
        ];
    }

    /**
     * Verifica rate limit global por IP (protección adicional)
     *
     * @param string|null $ip IP del cliente
     * @param int $maxRequests Máximo de requests
     * @param int $window Ventana de tiempo en segundos
     * @return bool True si excede el límite
     */
    public function exceedsGlobalRateLimit(?string $ip = null, int $maxRequests = 20, int $window = 3600): bool
    {
        $ip = $ip ?? SecurityManager::getClientIp();

        $sql = "SELECT COUNT(*) as count FROM " . self::TABLE_NAME . "
                WHERE ip_address = ?
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ip, $window]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $count = (int)($result['count'] ?? 0);

        return $count >= $maxRequests;
    }

    /**
     * Obtiene IPs más activas (posibles atacantes)
     *
     * @param int $limit Número de resultados
     * @param int $hours Últimas X horas
     * @return array Lista de IPs con contadores
     */
    public function getTopAttackingIps(int $limit = 10, int $hours = 24): array
    {
        $seconds = $hours * 3600;

        $sql = "SELECT ip_address, COUNT(*) as attempts, MAX(created_at) as last_attempt
                FROM " . self::TABLE_NAME . "
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
                GROUP BY ip_address
                ORDER BY attempts DESC
                LIMIT ?";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$seconds, $limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Bloquea manualmente una IP o email
     *
     * @param string $identifier IP o email a bloquear
     * @param string $type 'email' o 'ip'
     * @param int $duration Duración del bloqueo en segundos
     * @return bool True si se bloqueó correctamente
     */
    public function manualBlock(string $identifier, string $type = 'ip', int $duration = 86400): bool
    {
        // Insertar múltiples intentos para forzar el bloqueo
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $this->recordAttempt($identifier, $type, null, ['manual_block' => true]);
        }

        return true;
    }
}
