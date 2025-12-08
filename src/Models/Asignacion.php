<?php

namespace App\Models;

/**
 * Modelo de Asignación de Equipos
 * Integrante 4 - Asignaciones y Devoluciones
 */
class Asignacion extends Model
{
    protected $table = 'asignaciones';

    /**
     * ✅ ASIGNAR EQUIPO A COLABORADOR
     * - Cambia el estado del equipo a "asignado"
     * - Registra el movimiento en historial
     */
    public function asignarEquipo($data)
    {
        // Validaciones
        if (empty($data['equipo_id']) || empty($data['colaborador_id'])) {
            throw new \Exception('Equipo y colaborador son obligatorios');
        }

        // Verificar que el equipo esté disponible
        $equipoModel = new Equipo();
        $equipo = $equipoModel->find($data['equipo_id']);

        if (!$equipo) {
            throw new \Exception('El equipo no existe');
        }

        if ($equipo['estado'] !== 'disponible') {
            throw new \Exception('El equipo no está disponible para asignación (Estado: ' . $equipo['estado'] . ')');
        }

        // Crear asignación
        $asignacionId = $this->create([
            'equipo_id' => $data['equipo_id'],
            'colaborador_id' => $data['colaborador_id'],
            'usuario_responsable_id' => $data['usuario_responsable_id'],
            'fecha_asignacion' => $data['fecha_asignacion'] ?? date('Y-m-d'),
            'estado' => 'activa',
            'observaciones_asignacion' => $data['observaciones'] ?? null
        ]);

        // Cambiar estado del equipo a "asignado"
        $equipoModel->update($data['equipo_id'], ['estado' => 'asignado']);

        // Registrar en historial
        $historialModel = new HistorialMovimiento();
        $historialModel->registrarMovimiento([
            'equipo_id' => $data['equipo_id'],
            'colaborador_id' => $data['colaborador_id'],
            'usuario_id' => $data['usuario_responsable_id'],
            'tipo_movimiento' => 'asignacion',
            'estado_anterior' => 'disponible',
            'estado_nuevo' => 'asignado',
            'observaciones' => $data['observaciones'] ?? 'Asignación de equipo'
        ]);

        return $asignacionId;
    }

    /**
     * ✅ DEVOLVER EQUIPO
     * - Observación de devolución es OBLIGATORIA
     * - Cambia el estado del equipo según lo indicado
     */
    public function devolverEquipo($asignacionId, $data)
    {
        // Validar observación obligatoria
        if (empty($data['observaciones_devolucion'])) {
            throw new \Exception('La observación de devolución es obligatoria');
        }

        // Obtener asignación
        $asignacion = $this->find($asignacionId);
        if (!$asignacion) {
            throw new \Exception('Asignación no encontrada');
        }

        if ($asignacion['estado'] !== 'activa') {
            throw new \Exception('Esta asignación ya fue devuelta o cancelada');
        }

        // Actualizar asignación
        $this->update($asignacionId, [
            'estado' => 'devuelta',
            'fecha_devolucion' => date('Y-m-d'),
            'observaciones_devolucion' => $data['observaciones_devolucion'],
            'motivo_devolucion' => $data['motivo_devolucion'] ?? null
        ]);

        // Cambiar estado del equipo
        $nuevoEstado = $data['estado_equipo'] ?? 'disponible';
        $equipoModel = new Equipo();
        $equipoModel->update($asignacion['equipo_id'], ['estado' => $nuevoEstado]);

        // Registrar en historial
        $historialModel = new HistorialMovimiento();
        $historialModel->registrarMovimiento([
            'equipo_id' => $asignacion['equipo_id'],
            'colaborador_id' => $asignacion['colaborador_id'],
            'usuario_id' => $data['usuario_id'],
            'tipo_movimiento' => 'devolucion',
            'estado_anterior' => 'asignado',
            'estado_nuevo' => $nuevoEstado,
            'observaciones' => $data['observaciones_devolucion']
        ]);

        return true;
    }

    /**
     * Obtiene todas las asignaciones activas
     */
    public function getAsignacionesActivas()
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    c.nombre as colaborador_nombre,
                    c.apellido as colaborador_apellido,
                    c.departamento,
                    c.ubicacion,
                    cat.nombre as categoria,
                    u.nombre as responsable_nombre
                FROM {$this->table} a
                INNER JOIN equipos e ON a.equipo_id = e.id
                INNER JOIN colaboradores c ON a.colaborador_id = c.id
                LEFT JOIN categorias cat ON e.categoria_id = cat.id
                LEFT JOIN usuarios u ON a.usuario_responsable_id = u.id
                WHERE a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene asignaciones de un colaborador específico
     */
    public function getAsignacionesPorColaborador($colaboradorId)
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    e.foto,
                    cat.nombre as categoria
                FROM {$this->table} a
                INNER JOIN equipos e ON a.equipo_id = e.id
                LEFT JOIN categorias cat ON e.categoria_id = cat.id
                WHERE a.colaborador_id = ? AND a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * Obtiene el historial completo de asignaciones
     */
    public function getHistorialCompleto()
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as colaborador_nombre,
                    c.apellido as colaborador_apellido,
                    u.nombre as responsable_nombre
                FROM {$this->table} a
                INNER JOIN equipos e ON a.equipo_id = e.id
                INNER JOIN colaboradores c ON a.colaborador_id = c.id
                LEFT JOIN usuarios u ON a.usuario_responsable_id = u.id
                ORDER BY a.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene estadísticas de asignaciones
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total_asignaciones,
                    SUM(CASE WHEN estado = 'activa' THEN 1 ELSE 0 END) as activas,
                    SUM(CASE WHEN estado = 'devuelta' THEN 1 ELSE 0 END) as devueltas,
                    COUNT(DISTINCT colaborador_id) as colaboradores_con_equipos,
                    COUNT(DISTINCT equipo_id) as equipos_asignados_alguna_vez
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }
}