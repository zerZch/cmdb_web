<?php

namespace App\Models;

/**
 * Modelo de Historial de Movimientos
 * Integrante 3 - Trazabilidad Completa de Equipos (Requisito de Rúbrica)
 */
class HistorialMovimiento extends Model
{
    protected $table = 'historial_movimientos';

    /**
     * Registra un movimiento manualmente (los triggers automatizan la mayoría)
     */
    public function registrarMovimiento($data)
    {
        // Validar datos mínimos
        if (empty($data['equipo_id']) || empty($data['tipo_movimiento'])) {
            throw new \Exception('Equipo y tipo de movimiento son obligatorios');
        }

        return $this->create($data);
    }

    /**
     * Obtiene el historial completo de un equipo específico
     * Este es el método principal para cumplir el requisito de trazabilidad
     */
    public function getHistorialEquipo($equipoId)
    {
        $sql = "SELECT 
                    h.*,
                    u.nombre as usuario_nombre,
                    u.apellido as usuario_apellido,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre,
                    c.departamento as colaborador_departamento
                FROM {$this->table} h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE h.equipo_id = ?
                ORDER BY h.created_at DESC";

        return $this->query($sql, [$equipoId])->fetchAll();
    }

    /**
     * Obtiene el historial de un colaborador específico
     */
    public function getHistorialColaborador($colaboradorId)
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    u.nombre as usuario_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.colaborador_id = ?
                ORDER BY h.created_at DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * Obtiene movimientos por tipo
     */
    public function getByTipo($tipo)
    {
        return $this->where('tipo_movimiento', '=', $tipo);
    }

    /**
     * Obtiene movimientos en un rango de fechas
     */
    public function getByRangoFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE h.created_at BETWEEN ? AND ?
                ORDER BY h.created_at DESC";

        return $this->query($sql, [$fechaInicio, $fechaFin])->fetchAll();
    }

    /**
     * Obtiene los últimos movimientos (timeline general)
     */
    public function getRecientes($limite = 50)
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                ORDER BY h.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene estadísticas de movimientos
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total_movimientos,
                    COUNT(DISTINCT equipo_id) as equipos_con_movimientos,
                    COUNT(DISTINCT colaborador_id) as colaboradores_involucrados,
                    SUM(CASE WHEN tipo_movimiento = 'asignacion' THEN 1 ELSE 0 END) as total_asignaciones,
                    SUM(CASE WHEN tipo_movimiento = 'devolucion' THEN 1 ELSE 0 END) as total_devoluciones,
                    SUM(CASE WHEN tipo_movimiento = 'baja' THEN 1 ELSE 0 END) as total_bajas,
                    SUM(CASE WHEN tipo_movimiento = 'donacion' THEN 1 ELSE 0 END) as total_donaciones,
                    SUM(CASE WHEN tipo_movimiento = 'mantenimiento' THEN 1 ELSE 0 END) as total_mantenimientos
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }

    /**
     * Obtiene movimientos agrupados por tipo
     */
    public function getPorTipo()
    {
        $sql = "SELECT 
                    tipo_movimiento,
                    COUNT(*) as total,
                    COUNT(DISTINCT equipo_id) as equipos_afectados
                FROM {$this->table}
                GROUP BY tipo_movimiento
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene la línea de tiempo completa de un equipo (formato visual)
     */
    public function getTimelineEquipo($equipoId)
    {
        $sql = "SELECT 
                    h.*,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre,
                    CASE 
                        WHEN h.tipo_movimiento = 'compra' THEN 'success'
                        WHEN h.tipo_movimiento = 'asignacion' THEN 'info'
                        WHEN h.tipo_movimiento = 'devolucion' THEN 'warning'
                        WHEN h.tipo_movimiento = 'baja' THEN 'danger'
                        WHEN h.tipo_movimiento = 'donacion' THEN 'primary'
                        WHEN h.tipo_movimiento = 'mantenimiento' THEN 'secondary'
                        ELSE 'dark'
                    END as badge_color,
                    CASE 
                        WHEN h.tipo_movimiento = 'compra' THEN 'fa-shopping-cart'
                        WHEN h.tipo_movimiento = 'asignacion' THEN 'fa-user-plus'
                        WHEN h.tipo_movimiento = 'devolucion' THEN 'fa-undo'
                        WHEN h.tipo_movimiento = 'baja' THEN 'fa-trash-alt'
                        WHEN h.tipo_movimiento = 'donacion' THEN 'fa-hand-holding-heart'
                        WHEN h.tipo_movimiento = 'mantenimiento' THEN 'fa-wrench'
                        ELSE 'fa-circle'
                    END as icon
                FROM {$this->table} h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE h.equipo_id = ?
                ORDER BY h.created_at ASC";

        return $this->query($sql, [$equipoId])->fetchAll();
    }

    /**
     * Obtiene el último movimiento de un equipo
     */
    public function getUltimoMovimiento($equipoId)
    {
        $sql = "SELECT 
                    h.*,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE h.equipo_id = ?
                ORDER BY h.created_at DESC
                LIMIT 1";

        $stmt = $this->query($sql, [$equipoId]);
        return $stmt->fetch();
    }

    /**
     * Obtiene movimientos de hoy
     */
    public function getMovimientosHoy()
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE DATE(h.created_at) = CURDATE()
                ORDER BY h.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene movimientos de esta semana
     */
    public function getMovimientosSemana()
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE YEARWEEK(h.created_at) = YEARWEEK(CURDATE())
                ORDER BY h.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene movimientos de este mes
     */
    public function getMovimientosMes()
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE YEAR(h.created_at) = YEAR(CURDATE())
                AND MONTH(h.created_at) = MONTH(CURDATE())
                ORDER BY h.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Busca en el historial por término
     */
    public function buscar($termino)
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    u.nombre as usuario_nombre,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre
                FROM {$this->table} h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN colaboradores c ON h.colaborador_id = c.id
                WHERE e.nombre LIKE ?
                OR e.numero_serie LIKE ?
                OR h.observaciones LIKE ?
                OR CONCAT(c.nombre, ' ', c.apellido) LIKE ?
                ORDER BY h.created_at DESC";

        $searchTerm = "%{$termino}%";
        return $this->query($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm])->fetchAll();
    }

    /**
     * Cuenta cuántos movimientos ha tenido un equipo
     */
    public function contarMovimientos($equipoId)
    {
        return $this->count("equipo_id = {$equipoId}");
    }

    /**
     * Obtiene los equipos con más movimientos (más activos)
     */
    public function getEquiposMasActivos($limite = 10)
    {
        $sql = "SELECT 
                    e.id,
                    e.nombre,
                    e.numero_serie,
                    COUNT(h.id) as total_movimientos,
                    MAX(h.created_at) as ultimo_movimiento
                FROM equipos e
                INNER JOIN {$this->table} h ON e.id = h.equipo_id
                GROUP BY e.id
                ORDER BY total_movimientos DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
}
