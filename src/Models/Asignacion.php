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
     * ASIGNAR EQUIPO A COLABORADOR
     */
    public function asignarEquipo($data)
    {
        // Validaciones básicas
        if (empty($data['equipo_id']) || empty($data['colaborador_id'])) {
            throw new \Exception('Equipo y colaborador son obligatorios');
        }

        // Verificar equipo
        $equipoModel = new Equipo();
        $equipo      = $equipoModel->find($data['equipo_id']);

        if (!$equipo) {
            throw new \Exception('El equipo no existe');
        }

        if ($equipo['estado'] !== 'disponible') {
            throw new \Exception(
                'El equipo no está disponible para asignación (Estado: ' . $equipo['estado'] . ')'
            );
        }

        // Crear registro en "asignaciones"
        $asignacionId = $this->create([
            'equipo_id'        => $data['equipo_id'],
            'colaborador_id'   => $data['colaborador_id'],
            'usuario_id'       => $data['usuario_id'], // coincide con la columna real
            'fecha_asignacion' => $data['fecha_asignacion'] ?? date('Y-m-d'),
            'estado'           => 'activa',
            'observaciones'    => $data['observaciones'] ?? null
        ]);

        // Cambiar estado del equipo
        $equipoModel->update($data['equipo_id'], ['estado' => 'asignado']);

        // Registrar en historial
        $historialModel = new HistorialMovimiento();
        $historialModel->registrarMovimiento([
            'equipo_id'        => $data['equipo_id'],
            'colaborador_id'   => $data['colaborador_id'],
            'usuario_id'       => $data['usuario_id'],
            'tipo_movimiento'  => 'asignacion',
            'estado_anterior'  => 'disponible',
            'estado_nuevo'     => 'asignado',
            'observaciones'    => $data['observaciones'] ?? 'Asignación de equipo'
        ]);

        return $asignacionId;
    }

    /**
     * DEVOLVER EQUIPO
     */
    public function devolverEquipo($asignacionId, $data)
    {
        if (empty($data['observaciones_devolucion'])) {
            throw new \Exception('La observación de devolución es obligatoria');
        }

        $asignacion = $this->find($asignacionId);
        if (!$asignacion) {
            throw new \Exception('Asignación no encontrada');
        }

        if ($asignacion['estado'] !== 'activa') {
            throw new \Exception('Esta asignación ya fue devuelta o cancelada');
        }

        $textoObs = $data['observaciones_devolucion'] ?? '';
        if (!empty($data['motivo_devolucion'])) {
            $textoObs .= "\nMotivo: " . $data['motivo_devolucion'];
        }

        // Actualizar asignación
        $this->update($asignacionId, [
            'estado'           => 'devuelta',
            'fecha_devolucion' => date('Y-m-d'),
            'observaciones'    => $textoObs
        ]);

        // Cambiar estado del equipo
        $nuevoEstado  = $data['estado_equipo'] ?? 'disponible';
        $equipoModel  = new Equipo();
        $equipoModel->update($asignacion['equipo_id'], ['estado' => $nuevoEstado]);

        // Registrar en historial
        $historialModel = new HistorialMovimiento();
        $historialModel->registrarMovimiento([
            'equipo_id'        => $asignacion['equipo_id'],
            'colaborador_id'   => $asignacion['colaborador_id'],
            'usuario_id'       => $data['usuario_id'],
            'tipo_movimiento'  => 'devolucion',
            'estado_anterior'  => 'asignado',
            'estado_nuevo'     => $nuevoEstado,
            'observaciones'    => $textoObs
        ]);

        return true;
    }

    /**
     * LISTADO DE ASIGNACIONES ACTIVAS
     */
    public function getAsignacionesActivas()
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre       AS equipo_nombre,
                    e.numero_serie AS numero_serie,
                    e.marca,
                    e.modelo,
                    c.nombre       AS colaborador_nombre,
                    c.apellido     AS colaborador_apellido,
                    c.departamento,
                    c.ubicacion,
                    u.nombre       AS responsable_nombre
                FROM {$this->table} a
                INNER JOIN equipos       e ON a.equipo_id      = e.id
                INNER JOIN colaboradores c ON a.colaborador_id = c.id
                LEFT  JOIN usuarios      u ON a.usuario_id     = u.id
                WHERE a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * ASIGNACIONES POR COLABORADOR
     */
    public function getAsignacionesPorColaborador($colaboradorId)
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre       AS equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    e.foto
                FROM {$this->table} a
                INNER JOIN equipos e ON a.equipo_id = e.id
                WHERE a.colaborador_id = ? AND a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * HISTORIAL COMPLETO
     */
    public function getHistorialCompleto()
    {
        $sql = "SELECT 
                    a.*,
                    e.nombre       AS equipo_nombre,
                    e.numero_serie,
                    c.nombre       AS colaborador_nombre,
                    c.apellido     AS colaborador_apellido,
                    u.nombre       AS responsable_nombre
                FROM {$this->table} a
                INNER JOIN equipos       e ON a.equipo_id      = e.id
                INNER JOIN colaboradores c ON a.colaborador_id = c.id
                LEFT JOIN usuarios       u ON a.usuario_id     = u.id
                ORDER BY a.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * ESTADÍSTICAS
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*)                                        AS total_asignaciones,
                    SUM(CASE WHEN estado = 'activa'   THEN 1 ELSE 0 END) AS activas,
                    SUM(CASE WHEN estado = 'devuelta' THEN 1 ELSE 0 END) AS devueltas,
                    COUNT(DISTINCT colaborador_id)                  AS colaboradores_con_equipos,
                    COUNT(DISTINCT equipo_id)                       AS equipos_asignados_alguna_vez
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }
}
