<?php

namespace App\Models;

/**
 * Modelo de Baja de Equipos
 * Integrante 3 - Módulo de Bajas con Criterio Técnico (Requisito de Rúbrica)
 */
class Baja extends Model
{
    protected $table = 'bajas_equipos';

    /**
     * Registra una nueva baja de equipo
     * IMPORTANTE: El campo criterio_tecnico es OBLIGATORIO por rúbrica
     */
    public function registrarBaja($data)
    {
        // Validación obligatoria del criterio técnico
        if (empty($data['criterio_tecnico'])) {
            throw new \Exception('El criterio técnico es obligatorio para registrar una baja');
        }

        // Verificar que el equipo exista y no esté ya dado de baja
        $equipoModel = new Equipo();
        $equipo = $equipoModel->find($data['equipo_id']);

        if (!$equipo) {
            throw new \Exception('El equipo no existe');
        }

        if ($equipo['estado'] === 'dado_de_baja') {
            throw new \Exception('El equipo ya está dado de baja');
        }

        if ($equipo['estado'] === 'donado') {
            throw new \Exception('El equipo ya fue donado, no puede darse de baja');
        }

        // Crear el registro de baja
        $bajaId = $this->create($data);

        // El trigger se encarga de actualizar el estado del equipo y registrar en historial
        
        return $bajaId;
    }

    /**
     * Obtiene todas las bajas con información del equipo
     */
    public function getAllWithEquipos()
    {
        $sql = "SELECT 
                    b.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    c.nombre as categoria,
                    u.nombre as responsable_nombre,
                    u.apellido as responsable_apellido,
                    a.nombre as aprobador_nombre
                FROM {$this->table} b
                INNER JOIN equipos e ON b.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN usuarios u ON b.usuario_responsable_id = u.id
                LEFT JOIN usuarios a ON b.aprobado_por = a.id
                ORDER BY b.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene bajas pendientes de aprobación
     */
    public function getPendientes()
    {
        return $this->where('estado', '=', 'pendiente');
    }

    /**
     * Aprueba una baja
     */
    public function aprobar($bajaId, $usuarioId)
    {
        return $this->update($bajaId, [
            'estado' => 'aprobada',
            'aprobado_por' => $usuarioId,
            'fecha_aprobacion' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Rechaza una baja
     */
    public function rechazar($bajaId, $usuarioId)
    {
        return $this->update($bajaId, [
            'estado' => 'rechazada',
            'aprobado_por' => $usuarioId,
            'fecha_aprobacion' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marca una baja como ejecutada
     */
    public function marcarEjecutada($bajaId)
    {
        return $this->update($bajaId, ['estado' => 'ejecutada']);
    }

    /**
     * Obtiene bajas por motivo
     */
    public function getByMotivo($motivo)
    {
        return $this->where('motivo_baja', '=', $motivo);
    }

    /**
     * Obtiene bajas en un rango de fechas
     */
    public function getByRangoFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT 
                    b.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as categoria
                FROM {$this->table} b
                INNER JOIN equipos e ON b.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE b.fecha_baja BETWEEN ? AND ?
                ORDER BY b.fecha_baja DESC";

        return $this->query($sql, [$fechaInicio, $fechaFin])->fetchAll();
    }

    /**
     * Obtiene estadísticas de bajas
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN estado = 'ejecutada' THEN 1 ELSE 0 END) as ejecutadas,
                    SUM(valor_residual) as valor_residual_total
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }

    /**
     * Obtiene bajas agrupadas por motivo
     */
    public function getPorMotivo()
    {
        $sql = "SELECT 
                    motivo_baja,
                    COUNT(*) as total,
                    SUM(valor_residual) as valor_total
                FROM {$this->table}
                GROUP BY motivo_baja
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene bajas agrupadas por método de disposición
     */
    public function getPorMetodoDisposicion()
    {
        $sql = "SELECT 
                    metodo_disposicion,
                    COUNT(*) as total
                FROM {$this->table}
                WHERE metodo_disposicion IS NOT NULL
                GROUP BY metodo_disposicion
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Busca bajas por número de acta
     */
    public function findByActa($numeroActa)
    {
        return $this->findWhere('numero_acta', $numeroActa);
    }

    /**
     * Obtiene el detalle completo de una baja
     */
    public function getDetalleCompleto($bajaId)
    {
        $sql = "SELECT 
                    b.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    e.descripcion as equipo_descripcion,
                    e.fecha_adquisicion,
                    e.valor_adquisicion,
                    c.nombre as categoria,
                    u.nombre as responsable_nombre,
                    u.apellido as responsable_apellido,
                    u.email as responsable_email,
                    a.nombre as aprobador_nombre,
                    a.apellido as aprobador_apellido
                FROM {$this->table} b
                INNER JOIN equipos e ON b.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN usuarios u ON b.usuario_responsable_id = u.id
                LEFT JOIN usuarios a ON b.aprobado_por = a.id
                WHERE b.id = ?";

        $stmt = $this->query($sql, [$bajaId]);
        return $stmt->fetch();
    }

    /**
     * Obtiene bajas recientes (últimos 30 días)
     */
    public function getRecientes($limite = 10)
    {
        $sql = "SELECT 
                    b.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as categoria
                FROM {$this->table} b
                INNER JOIN equipos e ON b.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE b.fecha_baja >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY b.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Calcula el valor total de bajas en un período
     */
    public function getValorTotalPeriodo($año, $mes = null)
    {
        $sql = "SELECT 
                    SUM(valor_residual) as valor_total,
                    COUNT(*) as total_bajas
                FROM {$this->table}
                WHERE YEAR(fecha_baja) = ?";

        $params = [$año];

        if ($mes) {
            $sql .= " AND MONTH(fecha_baja) = ?";
            $params[] = $mes;
        }

        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Verifica si un equipo ya tiene una baja registrada
     */
    public function equipoTieneBaja($equipoId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE equipo_id = ?";
        $stmt = $this->query($sql, [$equipoId]);
        $result = $stmt->fetch();

        return $result['total'] > 0;
    }
}
