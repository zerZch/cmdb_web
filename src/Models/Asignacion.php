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

        // ==========================================================
        // CORRECCIÓN: Llamar al método updateEstado() del modelo Equipo
        // ==========================================================
        $equipoModel->updateEstado($data['equipo_id'], 'asignado');

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
    public function devolverEquipo(int $asignacionId, string $observaciones, string $estadoFinalEquipo): bool
{
    // Comenzar transacción (Recomendado para asegurar que ambas queries se ejecuten)
    $this->db->beginTransaction();

    try {
        // 1. Actualizar el registro de ASIGNACIÓN (Paso CLAVE para el historial)
        $sqlAsignacion = "UPDATE asignaciones SET 
                            estado = 'inactiva',
                            fecha_devolucion = NOW(),
                            observaciones = :observaciones 
                          WHERE id = :id AND estado = 'activa'";
        
        $stmtAsignacion = $this->db->prepare($sqlAsignacion);
        $stmtAsignacion->execute([
            'observaciones' => $observaciones,
            'id' => $asignacionId
        ]);
        
        // Obtener el equipo_id de la asignación para el siguiente paso
        $asignacion = $this->findById($asignacionId); 
        $equipoId = $asignacion['equipo_id'] ?? null;

        if (!$equipoId) {
            throw new \Exception("Equipo ID no encontrado para la asignación.");
        }

        // 2. Actualizar el estado del EQUIPO
        $sqlEquipo = "UPDATE equipos SET estado = :estadoFinalEquipo WHERE id = :equipoId";
        $stmtEquipo = $this->db->prepare($sqlEquipo);
        $stmtEquipo->execute([
            'estadoFinalEquipo' => $estadoFinalEquipo,
            'equipoId' => $equipoId
        ]);

        // Confirmar los cambios
        $this->db->commit();
        return true;

    } catch (\Exception $e) {
        // Revertir si algo falla
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        // Puedes loggear el error $e->getMessage()
        throw new \Exception("Fallo en la transacción de devolución: " . $e->getMessage());
    }
}
public function findById(int $id): ?array
{
    $sql = "SELECT * FROM asignaciones WHERE id = :id"; // Asume que la tabla se llama 'asignaciones'

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ?: null;

    } catch (\PDOException $e) {
        throw new \Exception("Error al buscar asignación por ID: " . $e->getMessage());
    }
}

    /**
     * LISTADO DE ASIGNACIONES ACTIVAS
     */
    public function getAsignacionesActivas()
    {
        $sql = "SELECT 
                        a.*,
                        e.nombre      AS equipo_nombre,
                        e.numero_serie AS numero_serie,
                        e.marca,
                        e.modelo,
                        c.nombre      AS colaborador_nombre,
                        c.apellido    AS colaborador_apellido,
                        c.departamento,
                        c.ubicacion,
                        u.nombre      AS responsable_nombre
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
    // --- MÉTODO 1: getAsignacionesPorColaborador ---
public function getAsignacionesPorColaborador($colaboradorId)
{
    // ... Tu definición de $sql ...
    $sql = "SELECT 
                a.id, a.equipo_id, a.fecha_asignacion, a.observaciones,
                e.nombre AS equipo_nombre, 
                e.numero_serie
            FROM asignaciones a
            JOIN equipos e ON a.equipo_id = e.id
            WHERE a.colaborador_id = :colaboradorId 
            AND a.estado = 'activa' 
            ORDER BY a.fecha_asignacion DESC";

    try {
        $sql = "SELECT 
                    a.id, a.equipo_id, a.fecha_asignacion, a.observaciones,
                    e.nombre AS equipo_nombre, 
                    e.numero_serie
                FROM asignaciones a
                JOIN equipos e ON a.equipo_id = e.id
                WHERE a.colaborador_id = :colaboradorId 
                AND a.estado = 'activa' 
                ORDER BY a.fecha_asignacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['colaboradorId' => $colaboradorId]);
        
        // ¡CAMBIO CLAVE: \PDO::FETCH_ASSOC!
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        throw new \Exception("Error al obtener equipos asignados: " . $e->getMessage());
    }
} // <--- ¡ASEGÚRATE DE QUE ESTA LLAVE DE CIERRE EXISTA!

/**
 * Obtiene el historial (asignaciones inactivas) de un colaborador. 
 * Utilizado en "Historial de Asignaciones".
 * @param int $colaboradorId
 * @return array
 */
public function getHistorialPorColaborador($colaboradorId)
{
    $sql = "SELECT 
                a.id, a.fecha_asignacion, a.fecha_devolucion, 
                a.observaciones, /* <-- CORRECCIÓN: Usamos observaciones en lugar de motivo_devolucion */
                e.nombre AS equipo_nombre, 
                e.numero_serie
            FROM asignaciones a
            JOIN equipos e ON a.equipo_id = e.id
            WHERE a.colaborador_id = :colaboradorId 
            AND a.estado = 'inactiva' 
            ORDER BY a.fecha_devolucion DESC";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['colaboradorId' => $colaboradorId]);
        
        // ¡CAMBIO CLAVE: \PDO::FETCH_ASSOC!
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        // El error es capturado aquí, pero lo cambiamos a un mensaje más genérico
        throw new \Exception("Error al obtener historial de asignaciones: " . $e->getMessage());
    }
}
public function ver()
{
    $this->requireAuth();
    $asignacionId = $_GET['id'] ?? null;

    if (!$asignacionId) {
        setFlashMessage('Error', 'ID de asignación no proporcionado.', 'error');
        redirectTo('index.php?route=asignaciones&action=misEquipos');
    }

    // 1. Obtener la asignación. Esto ahora debería funcionar con el nuevo findById()
    $asignacion = $this->asignacionModel->findById($asignacionId); 
    
    if (!$asignacion || empty($asignacion['equipo_id'])) {
        setFlashMessage('Error', 'Asignación o Equipo asociado no encontrado.', 'error');
        redirectTo('index.php?route=asignaciones&action=misEquipos');
    }
    
    $equipoId = $asignacion['equipo_id'];
    
    // 2. Redirigir a la vista de detalles del Equipo
    // El error 404 debería resolverse si la ruta 'equipos&action=ver' funciona.
    redirectTo('index.php?route=equipos&action=ver&id=' . $equipoId);
}

    /**
     * HISTORIAL COMPLETO
     */
    public function getHistorialCompleto()
    {
        $sql = "SELECT 
                        a.*,
                        e.nombre      AS equipo_nombre,
                        e.numero_serie,
                        c.nombre      AS colaborador_nombre,
                        c.apellido    AS colaborador_apellido,
                        u.nombre      AS responsable_nombre
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
                        COUNT(*)                                      AS total_asignaciones,
                        SUM(CASE WHEN estado = 'activa'   THEN 1 ELSE 0 END) AS activas,
                        SUM(CASE WHEN estado = 'devuelta' THEN 1 ELSE 0 END) AS devueltas,
                        COUNT(DISTINCT colaborador_id)                    AS colaboradores_con_equipos,
                        COUNT(DISTINCT equipo_id)                         AS equipos_asignados_alguna_vez
                    FROM {$this->table}";

        return $this->query($sql)->fetch();
    }
}