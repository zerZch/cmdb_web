<?php

namespace App\Models;

/**
 * Modelo de Colaborador (Empleados de la empresa)
 * Integrante 3 - Módulo de Colaboradores
 */
class Colaborador extends Model
{
    protected $table = 'colaboradores';

    /**
     * Busca un colaborador por cédula
     */
    public function findByCedula($cedula)
    {
        return $this->findWhere('cedula', $cedula);
    }

    /**
     * Busca un colaborador por email
     */
    public function findByEmail($email)
    {
        return $this->findWhere('email', $email);
    }

    /**
     * Obtiene todos los colaboradores activos
     */
    public function getActivos()
    {
        return $this->where('estado', '=', 'activo');
    }

    /**
     * Obtiene colaboradores por departamento
     */
    public function getByDepartamento($departamento)
    {
        return $this->where('departamento', '=', $departamento);
    }

    /**
     * Obtiene todos los colaboradores con información de equipos asignados
     */
    public function getAllWithEquipos()
    {
        $sql = "SELECT 
                    c.*,
                    COUNT(DISTINCT a.id) as total_equipos,
                    GROUP_CONCAT(DISTINCT e.nombre SEPARATOR ', ') as equipos_asignados
                FROM {$this->table} c
                LEFT JOIN asignaciones a ON c.id = a.colaborador_id AND a.estado = 'activa'
                LEFT JOIN equipos e ON a.equipo_id = e.id
                GROUP BY c.id
                ORDER BY c.created_at DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Crea un nuevo colaborador con validación de email/cédula única
     */
    public function createColaborador($data)
    {
        // Validar que cédula no exista (si se proporciona)
        if (!empty($data['cedula']) && $this->cedulaExists($data['cedula'])) {
            throw new \Exception('La cédula ya está registrada');
        }

        // Validar que email no exista (si se proporciona)
        if (!empty($data['email']) && $this->emailExists($data['email'])) {
            throw new \Exception('El email ya está registrado');
        }

        return $this->create($data);
    }

    /**
     * Actualiza un colaborador con validación
     */
    public function updateColaborador($id, $data)
    {
        // Validar cédula única (excepto el actual)
        if (!empty($data['cedula']) && $this->cedulaExists($data['cedula'], $id)) {
            throw new \Exception('La cédula ya está registrada');
        }

        // Validar email único (excepto el actual)
        if (!empty($data['email']) && $this->emailExists($data['email'], $id)) {
            throw new \Exception('El email ya está registrado');
        }

        return $this->update($id, $data);
    }

    /**
     * Verifica si una cédula ya existe
     */
    public function cedulaExists($cedula, $exceptId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE cedula = ?";
        $params = [$cedula];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();

        return $result['total'] > 0;
    }

    /**
     * Verifica si un email ya existe
     */
    public function emailExists($email, $exceptId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();

        return $result['total'] > 0;
    }

    /**
     * Cambia el estado de un colaborador
     */
    public function cambiarEstado($id, $estado)
    {
        return $this->update($id, ['estado' => $estado]);
    }

    /**
     * Obtiene los equipos asignados actualmente a un colaborador
     */
    public function getEquiposAsignados($colaboradorId)
    {
        $sql = "SELECT 
                    e.*,
                    c.nombre as categoria,
                    a.fecha_asignacion,
                    a.observaciones as obs_asignacion
                FROM equipos e
                INNER JOIN asignaciones a ON e.id = a.equipo_id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                WHERE a.colaborador_id = ?
                AND a.estado = 'activa'
                ORDER BY a.fecha_asignacion DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * Obtiene el historial completo de un colaborador
     */
    public function getHistorial($colaboradorId)
    {
        $sql = "SELECT 
                    h.*,
                    e.nombre as equipo_nombre,
                    e.numero_serie,
                    u.nombre as usuario_responsable
                FROM historial_movimientos h
                INNER JOIN equipos e ON h.equipo_id = e.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.colaborador_id = ?
                ORDER BY h.created_at DESC";

        return $this->query($sql, [$colaboradorId])->fetchAll();
    }

    /**
     * Verifica si un colaborador puede ser eliminado
     */
    public function puedeEliminar($colaboradorId)
    {
        // No se puede eliminar si tiene equipos asignados actualmente
        $sql = "SELECT COUNT(*) as total 
                FROM asignaciones 
                WHERE colaborador_id = ? 
                AND estado = 'activa'";

        $stmt = $this->query($sql, [$colaboradorId]);
        $result = $stmt->fetch();

        return $result['total'] == 0;
    }

    /**
     * Obtiene estadísticas de colaboradores
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos,
                    SUM(CASE WHEN estado = 'suspendido' THEN 1 ELSE 0 END) as suspendidos,
                    COUNT(DISTINCT departamento) as departamentos
                FROM {$this->table}";

        return $this->query($sql)->fetch();
    }

    /**
     * Obtiene colaboradores agrupados por departamento
     */
    public function getPorDepartamento()
    {
        $sql = "SELECT 
                    departamento,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos
                FROM {$this->table}
                WHERE departamento IS NOT NULL
                GROUP BY departamento
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }

    /**
     * Busca colaboradores por término (nombre, apellido, cédula, email)
     */
    public function buscar($termino)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombre LIKE ? 
                OR apellido LIKE ? 
                OR cedula LIKE ?
                OR email LIKE ?
                ORDER BY nombre ASC";

        $searchTerm = "%{$termino}%";
        return $this->query($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm])->fetchAll();
    }

    /**
     * Obtiene los colaboradores con más equipos asignados
     */
    public function getTopConEquipos($limite = 10)
    {
        $sql = "SELECT 
                    c.*,
                    COUNT(a.id) as total_equipos
                FROM {$this->table} c
                INNER JOIN asignaciones a ON c.id = a.colaborador_id
                WHERE a.estado = 'activa'
                AND c.estado = 'activo'
                GROUP BY c.id
                ORDER BY total_equipos DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
}
