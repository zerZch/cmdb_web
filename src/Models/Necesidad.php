<?php
// src/Models/Necesidad.php

namespace App\Models;

class Necesidad extends Model
{
    protected $table = 'necesidades';

    /**
     * Crear nueva solicitud
     */
    public function crearSolicitud($data)
    {
        return $this->create([
            'colaborador_id' => $data['colaborador_id'],
            'categoria_id'   => $data['categoria_id'],
            'tipo_equipo'    => $data['tipo_equipo'] ?? null,
            'justificacion'  => $data['justificacion'],
            'urgencia'       => $data['urgencia'] ?? 'normal',
            'estado'         => 'pendiente',
            'fecha_solicitud'=> date('Y-m-d')
        ]);
    }

    /**
     * Obtener todas las solicitudes con información completa
     */
    public function getAllConDetalles()
    {
        $sql = "SELECT 
                    n.*,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre,
                    c.departamento,
                    c.cargo,
                    cat.nombre as categoria_nombre,
                    u.nombre as admin_nombre
                FROM {$this->table} n
                INNER JOIN colaboradores c ON n.colaborador_id = c.id
                LEFT JOIN categorias cat ON n.categoria_id = cat.id
                LEFT JOIN usuarios u ON n.administrador_id = u.id
                ORDER BY 
                    CASE n.estado 
                        WHEN 'pendiente' THEN 1
                        WHEN 'aprobada' THEN 2
                        WHEN 'rechazada' THEN 3
                        WHEN 'completada' THEN 4
                    END,
                    n.urgencia DESC,
                    n.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener solicitudes pendientes
     */
    public function getPendientes()
    {
        $sql = "SELECT 
                    n.*,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre,
                    c.departamento,
                    cat.nombre as categoria_nombre
                FROM {$this->table} n
                INNER JOIN colaboradores c ON n.colaborador_id = c.id
                LEFT JOIN categorias cat ON n.categoria_id = cat.id
                WHERE n.estado = 'pendiente'
                ORDER BY 
                    FIELD(n.urgencia, 'alta', 'normal', 'baja'),
                    n.created_at ASC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener solicitudes de un colaborador
     */
    public function getPorColaborador($colaboradorId)
    {
        $sql = "SELECT 
                    n.*,
                    cat.nombre as categoria_nombre,
                    u.nombre as admin_nombre
                FROM {$this->table} n
                LEFT JOIN categorias cat ON n.categoria_id = cat.id
                LEFT JOIN usuarios u ON n.administrador_id = u.id
                WHERE n.colaborador_id = ?
                ORDER BY n.created_at DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * Aprobar solicitud
     */
    public function aprobar($id, $adminId, $observaciones = null)
    {
        return $this->update($id, [
            'estado'           => 'aprobada',
            'administrador_id' => $adminId,
            'fecha_respuesta'  => date('Y-m-d'),
            'observaciones_admin' => $observaciones
        ]);
    }

    /**
     * Rechazar solicitud
     */
    public function rechazar($id, $adminId, $motivo)
    {
        return $this->update($id, [
            'estado'           => 'rechazada',
            'administrador_id' => $adminId,
            'fecha_respuesta'  => date('Y-m-d'),
            'observaciones_admin' => $motivo
        ]);
    }

    /**
     * Marcar como completada (cuando se asigna el equipo)
     */
    public function completar($id, $equipoId = null)
    {
        return $this->update($id, [
            'estado'         => 'completada',
            'equipo_asignado_id' => $equipoId,
            'fecha_completada' => date('Y-m-d')
        ]);
    }

    /**
     * Obtener estadísticas
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN urgencia = 'alta' AND estado = 'pendiente' THEN 1 ELSE 0 END) as urgentes
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }

    /**
     * Obtener solicitudes por estado
     */
    public function getPorEstado($estado)
    {
        $sql = "SELECT 
                    n.*,
                    CONCAT(c.nombre, ' ', c.apellido) as colaborador_nombre,
                    c.departamento,
                    cat.nombre as categoria_nombre
                FROM {$this->table} n
                INNER JOIN colaboradores c ON n.colaborador_id = c.id
                LEFT JOIN categorias cat ON n.categoria_id = cat.id
                WHERE n.estado = ?
                ORDER BY n.created_at DESC";

        return $this->query($sql, [$estado])->fetchAll();
    }
}