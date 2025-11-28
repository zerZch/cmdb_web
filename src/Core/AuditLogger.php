<?php

namespace App\Core;

use App\Core\Database;
use App\Core\SecurityManager;
use PDO;

/**
 * Clase AuditLogger - Registro de auditoría para cumplimiento normativo
 * Registra todos los accesos y eventos importantes del sistema
 * Cumple con requisitos de Ley 81 de Panamá y buenas prácticas de seguridad
 */
class AuditLogger
{
    private Database $db;
    private const TABLE_NAME = 'logs_acceso';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registra un intento de acceso (exitoso o fallido)
     *
     * @param string|null $usuario Email o username del usuario
     * @param bool $exitoso Si el login fue exitoso
     * @param string|null $motivo Motivo del fallo si aplica
     * @param array $metadata Datos adicionales
     * @return bool True si se registró correctamente
     */
    public function logAccess(
        ?string $usuario,
        bool $exitoso,
        ?string $motivo = null,
        array $metadata = []
    ): bool {
        $ip = SecurityManager::getClientIp();
        $userAgent = SecurityManager::getUserAgent();
        $pais = $this->getCountryFromIp($ip);
        $fingerprint = SecurityManager::generateDeviceFingerprint();

        // Metadata adicional
        $allMetadata = array_merge($metadata, [
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''
        ]);

        $metadataJson = json_encode($allMetadata);
        $exitosoInt = $exitoso ? 1 : 0;

        $sql = "INSERT INTO " . self::TABLE_NAME . "
                (usuario, ip_address, user_agent, pais, fingerprint, exitoso, motivo, metadata, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([
            $usuario,
            $ip,
            $userAgent,
            $pais,
            $fingerprint,
            $exitosoInt,
            $motivo,
            $metadataJson
        ]);
    }

    /**
     * Registra un login exitoso
     *
     * @param string $usuario Email o username
     * @param int|null $userId ID del usuario
     * @param array $metadata Datos adicionales
     * @return bool
     */
    public function logSuccessfulLogin(string $usuario, ?int $userId = null, array $metadata = []): bool
    {
        if ($userId) {
            $metadata['user_id'] = $userId;
        }

        return $this->logAccess($usuario, true, null, $metadata);
    }

    /**
     * Registra un login fallido
     *
     * @param string $usuario Email o username
     * @param string $motivo Razón del fallo
     * @param array $metadata Datos adicionales
     * @return bool
     */
    public function logFailedLogin(string $usuario, string $motivo, array $metadata = []): bool
    {
        return $this->logAccess($usuario, false, $motivo, $metadata);
    }

    /**
     * Registra un logout
     *
     * @param string $usuario Email o username
     * @param int|null $userId ID del usuario
     * @return bool
     */
    public function logLogout(string $usuario, ?int $userId = null): bool
    {
        $metadata = [
            'action' => 'logout',
            'user_id' => $userId
        ];

        return $this->logAccess($usuario, true, 'logout', $metadata);
    }

    /**
     * Obtiene el país desde una IP usando API gratuita
     * Implementación básica - se puede mejorar con APIs premium
     *
     * @param string $ip Dirección IP
     * @return string Código de país (ISO 2 letras) o 'Unknown'
     */
    private function getCountryFromIp(string $ip): string
    {
        // IPs locales o privadas
        if (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            $ip === '0.0.0.0' ||
            filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
        ) {
            return 'Local';
        }

        // Para producción, usar una API de geolocalización
        // Ejemplo: ip-api.com, ipapi.co, geoip-db.com, etc.
        // Por ahora retornamos Unknown para no hacer requests externos
        return 'Unknown';

        // Ejemplo de implementación con ip-api.com (descomentary usar en producción):
        /*
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
            if ($response) {
                $data = json_decode($response, true);
                return $data['countryCode'] ?? 'Unknown';
            }
        } catch (\Exception $e) {
            // Error en la API, continuar
        }
        return 'Unknown';
        */
    }

    /**
     * Obtiene logs de acceso con filtros
     *
     * @param array $filters Filtros: usuario, exitoso, fecha_inicio, fecha_fin, ip
     * @param int $limit Límite de resultados
     * @param int $offset Offset para paginación
     * @return array Lista de logs
     */
    public function getLogs(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if (isset($filters['usuario'])) {
            $where[] = "usuario LIKE ?";
            $params[] = "%{$filters['usuario']}%";
        }

        if (isset($filters['exitoso'])) {
            $where[] = "exitoso = ?";
            $params[] = $filters['exitoso'] ? 1 : 0;
        }

        if (isset($filters['ip'])) {
            $where[] = "ip_address = ?";
            $params[] = $filters['ip'];
        }

        if (isset($filters['fecha_inicio'])) {
            $where[] = "created_at >= ?";
            $params[] = $filters['fecha_inicio'];
        }

        if (isset($filters['fecha_fin'])) {
            $where[] = "created_at <= ?";
            $params[] = $filters['fecha_fin'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT * FROM " . self::TABLE_NAME . "
                $whereClause
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($params);

        $logs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Decodificar metadata JSON
            if (!empty($row['metadata'])) {
                $row['metadata'] = json_decode($row['metadata'], true);
            }
            $logs[] = $row;
        }

        return $logs;
    }

    /**
     * Obtiene estadísticas de accesos
     *
     * @param int $days Últimos X días
     * @return array Estadísticas
     */
    public function getStatistics(int $days = 7): array
    {
        $sql = "SELECT
                    COUNT(*) as total_accesos,
                    SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) as exitosos,
                    SUM(CASE WHEN exitoso = 0 THEN 1 ELSE 0 END) as fallidos,
                    COUNT(DISTINCT usuario) as usuarios_unicos,
                    COUNT(DISTINCT ip_address) as ips_unicas
                FROM " . self::TABLE_NAME . "
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$days]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_accesos' => (int)($result['total_accesos'] ?? 0),
            'exitosos' => (int)($result['exitosos'] ?? 0),
            'fallidos' => (int)($result['fallidos'] ?? 0),
            'usuarios_unicos' => (int)($result['usuarios_unicos'] ?? 0),
            'ips_unicas' => (int)($result['ips_unicas'] ?? 0),
            'periodo' => "$days días"
        ];
    }

    /**
     * Obtiene accesos por día (para gráficos)
     *
     * @param int $days Últimos X días
     * @return array Datos agrupados por día
     */
    public function getAccessesByDay(int $days = 30): array
    {
        $sql = "SELECT
                    DATE(created_at) as fecha,
                    COUNT(*) as total,
                    SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) as exitosos,
                    SUM(CASE WHEN exitoso = 0 THEN 1 ELSE 0 END) as fallidos
                FROM " . self::TABLE_NAME . "
                WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY fecha ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$days]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Detecta actividad sospechosa (múltiples fallos desde misma IP)
     *
     * @param int $threshold Número de fallos que activa alerta
     * @param int $minutes Ventana de tiempo en minutos
     * @return array IPs sospechosas
     */
    public function detectSuspiciousActivity(int $threshold = 5, int $minutes = 60): array
    {
        $seconds = $minutes * 60;

        $sql = "SELECT
                    ip_address,
                    COUNT(*) as intentos_fallidos,
                    MAX(created_at) as ultimo_intento,
                    GROUP_CONCAT(DISTINCT usuario) as usuarios_intentados
                FROM " . self::TABLE_NAME . "
                WHERE exitoso = 0
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
                GROUP BY ip_address
                HAVING intentos_fallidos >= ?
                ORDER BY intentos_fallidos DESC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$seconds, $threshold]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Limpia logs antiguos (mantenimiento de BD)
     *
     * @param int $days Mantener logs de los últimos X días
     * @return int Número de registros eliminados
     */
    public function cleanOldLogs(int $days = 90): int
    {
        $sql = "DELETE FROM " . self::TABLE_NAME . "
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$days]);

        return $stmt->rowCount();
    }

    /**
     * Exporta logs a formato CSV para auditorías
     *
     * @param array $filters Filtros
     * @param string|null $filename Nombre del archivo
     * @return string Path del archivo generado
     */
    public function exportToCsv(array $filters = [], ?string $filename = null): string
    {
        $logs = $this->getLogs($filters, 10000, 0); // Máximo 10k registros

        $filename = $filename ?? 'audit_log_' . date('Y-m-d_His') . '.csv';
        $filepath = __DIR__ . '/../../exports/' . $filename;

        // Crear directorio si no existe
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Headers CSV
        fputcsv($file, ['ID', 'Usuario', 'IP', 'País', 'Exitoso', 'Motivo', 'Fecha/Hora', 'User Agent']);

        // Datos
        foreach ($logs as $log) {
            fputcsv($file, [
                $log['id'],
                $log['usuario'],
                $log['ip_address'],
                $log['pais'],
                $log['exitoso'] ? 'Sí' : 'No',
                $log['motivo'] ?? '-',
                $log['created_at'],
                $log['user_agent']
            ]);
        }

        fclose($file);

        return $filepath;
    }
}
