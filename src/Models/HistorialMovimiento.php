<?php

namespace App\Models;

class HistorialMovimiento extends Model
{
    protected $table = 'historial_movimientos';

    /**
     * Registrar movimiento manual o desde controlador.
     */
    public function registrarMovimiento($data)
    {
        // Normalizar datos
        $equipoId      = $data['equipo_id']      ?? null;
        $colaboradorId = $data['colaborador_id'] ?? null;
        $usuarioId     = $data['usuario_id']     ?? null;
        $tipo          = $data['tipo_movimiento'] ?? 'movimiento';
        $estadoAnt     = $data['estado_anterior'] ?? null;
        $estadoNuevo   = $data['estado_nuevo']    ?? null;
        $obs           = $data['observaciones']   ?? null;

        $sql = "INSERT INTO historial_movimientos 
                    (equipo_id, colaborador_id, usuario_id, tipo_movimiento, 
                     estado_anterior, estado_nuevo, observaciones, created_at)
                VALUES 
                    (:equipo_id, :colaborador_id, :usuario_id, :tipo_movimiento,
                     :estado_anterior, :estado_nuevo, :observaciones, NOW())";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':equipo_id'       => $equipoId,
            ':colaborador_id'  => $colaboradorId,
            ':usuario_id'      => $usuarioId,
            ':tipo_movimiento' => $tipo,
            ':estado_anterior' => $estadoAnt,
            ':estado_nuevo'    => $estadoNuevo,
            ':observaciones'   => $obs
        ]);
    }

    /**
     * Historial de un equipo
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

    public function getByTipo($tipo)
    {
        return $this->where('tipo_movimiento', '=', $tipo);
    }

    public function getByRangoFechas($inicio, $fin)
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

        return $this->query($sql, [$inicio, $fin])->fetchAll();
    }

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

        return $this->query($sql, [$equipoId])->fetch();
    }
}
