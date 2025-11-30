<?php

namespace App\Models;

/**
 * Modelo de Donación de Equipos
 * Integrante 3 - Módulo de Donaciones a Entidades Externas
 */
class Donacion extends Model
{
    protected $table = 'donaciones_equipos';

    /**
     * Registra una nueva donación
     * IMPORTANTE: entidad_beneficiada es obligatorio
     */
    public function registrarDonacion($data)
    {
        // Validación obligatoria de entidad beneficiada
        if (empty($data['entidad_beneficiada'])) {
            throw new \Exception('La entidad beneficiada es obligatoria');
        }

        // Verificar que el equipo exista y no esté ya donado
        $equipoModel = new Equipo();
        $equipo = $equipoModel->find($data['equipo_id']);

        if (!$equipo) {
            throw new \Exception('El equipo no existe');
        }

        if ($equipo['estado'] === 'donado') {
            throw new \Exception('El equipo ya fue donado anteriormente');
        }

        if ($equipo['estado'] === 'dado_de_baja') {
            throw new \Exception('El equipo está dado de baja, no puede donarse');
        }

        if ($equipo['estado'] === 'asignado') {
            throw new \Exception('El equipo está asignado, debe devolverse antes de donarlo');
        }

        // Crear el registro de donación
        $donacionId = $this->create($data);

        // El trigger se encarga de actualizar el estado del equipo y registrar en historial
        
        return $donacionId;
    }

    /**
     * Obtiene todas las donaciones con información del equipo
     */
    public function getAllWithEquipos()
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    e.marca,
                    e.modelo,
                    c.nombre as categoria,
                    u.nombre as responsable_nombre,
                    u.apellido as responsable_apellido
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_responsable_id = u.id
                ORDER BY d.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene donaciones por entidad beneficiada
     */
    public function getByEntidad($entidad)
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as categoria
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE d.entidad_beneficiada LIKE ?
                ORDER BY d.fecha_donacion DESC";

        return $this->query($sql, ["%{$entidad}%"])->fetchAll();
    }

    /**
     * Obtiene donaciones por tipo de entidad
     */
    public function getByTipoEntidad($tipo)
    {
        return $this->where('tipo_entidad', '=', $tipo);
    }

    /**
     * Obtiene donaciones en un rango de fechas
     */
    public function getByRangoFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as categoria
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE d.fecha_donacion BETWEEN ? AND ?
                ORDER BY d.fecha_donacion DESC";

        return $this->query($sql, [$fechaInicio, $fechaFin])->fetchAll();
    }

    /**
     * Obtiene estadísticas de donaciones
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total_donaciones,
                    COUNT(DISTINCT entidad_beneficiada) as entidades_beneficiadas,
                    SUM(valor_donacion) as valor_total_donado,
                    AVG(valor_donacion) as valor_promedio
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }

    /**
     * Obtiene donaciones agrupadas por tipo de entidad
     */
    public function getPorTipoEntidad()
    {
        $sql = "SELECT 
                    tipo_entidad,
                    COUNT(*) as total,
                    SUM(valor_donacion) as valor_total
                FROM {$this->table}
                WHERE tipo_entidad IS NOT NULL
                GROUP BY tipo_entidad
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtiene las entidades que más donaciones han recibido
     */
    public function getTopEntidades($limite = 10)
    {
        $sql = "SELECT 
                    entidad_beneficiada,
                    tipo_entidad,
                    COUNT(*) as total_donaciones,
                    SUM(valor_donacion) as valor_total,
                    MAX(fecha_donacion) as ultima_donacion
                FROM {$this->table}
                GROUP BY entidad_beneficiada, tipo_entidad
                ORDER BY total_donaciones DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Busca donación por número de acta
     */
    public function findByActa($numeroActa)
    {
        return $this->findWhere('numero_acta', $numeroActa);
    }

    /**
     * Obtiene el detalle completo de una donación
     */
    public function getDetalleCompleto($donacionId)
    {
        $sql = "SELECT 
                    d.*,
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
                    u.email as responsable_email
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                LEFT JOIN usuarios u ON d.usuario_responsable_id = u.id
                WHERE d.id = ?";

        $stmt = $this->query($sql, [$donacionId]);
        return $stmt->fetch();
    }

    /**
     * Obtiene donaciones recientes (últimos 30 días)
     */
    public function getRecientes($limite = 10)
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    c.nombre as categoria
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE d.fecha_donacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY d.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene donaciones de un año específico
     */
    public function getByAno($año)
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    c.nombre as categoria
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE YEAR(d.fecha_donacion) = ?
                ORDER BY d.fecha_donacion DESC";

        return $this->query($sql, [$año])->fetchAll();
    }

    /**
     * Calcula el valor total donado en un período
     */
    public function getValorTotalPeriodo($año, $mes = null)
    {
        $sql = "SELECT 
                    SUM(valor_donacion) as valor_total,
                    COUNT(*) as total_donaciones
                FROM {$this->table}
                WHERE YEAR(fecha_donacion) = ?";

        $params = [$año];

        if ($mes) {
            $sql .= " AND MONTH(fecha_donacion) = ?";
            $params[] = $mes;
        }

        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Obtiene donaciones agrupadas por mes (para gráficos)
     */
    public function getDonacionesPorMes($año)
    {
        $sql = "SELECT 
                    MONTH(fecha_donacion) as mes,
                    COUNT(*) as total,
                    SUM(valor_donacion) as valor_total
                FROM {$this->table}
                WHERE YEAR(fecha_donacion) = ?
                GROUP BY MONTH(fecha_donacion)
                ORDER BY mes ASC";

        return $this->query($sql, [$año])->fetchAll();
    }

    /**
     * Busca donaciones por término (entidad, contacto, RUC)
     */
    public function buscar($termino)
    {
        $sql = "SELECT 
                    d.*,
                    e.nombre as equipo_nombre,
                    c.nombre as categoria
                FROM {$this->table} d
                INNER JOIN equipos e ON d.equipo_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE d.entidad_beneficiada LIKE ?
                OR d.ruc_entidad LIKE ?
                OR d.contacto_nombre LIKE ?
                OR d.contacto_email LIKE ?
                ORDER BY d.fecha_donacion DESC";

        $searchTerm = "%{$termino}%";
        return $this->query($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm])->fetchAll();
    }

    /**
     * Verifica si un equipo ya fue donado
     */
    public function equipoFueDonado($equipoId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE equipo_id = ?";
        $stmt = $this->query($sql, [$equipoId]);
        $result = $stmt->fetch();

        return $result['total'] > 0;
    }

    /**
     * Obtiene el certificado de donación (si existe)
     */
    public function getCertificado($donacionId)
    {
        $donacion = $this->find($donacionId);
        return $donacion['certificado_donacion'] ?? null;
    }
}
