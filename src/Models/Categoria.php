<?php

namespace App\Models;

/**
 * Modelo de Categoría
 */
class Categoria extends Model
{
    protected $table = 'categorias';

    /**
     * Obtiene todas las categorías activas
     */
    public function getActivas() {
        return $this->where('estado', '=', 'activa');
    }

    /**
     * Verifica si un nombre de categoría ya existe
     */
    public function nombreExists($nombre, $exceptId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = ?";
        $params = [$nombre];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();

        return $result['total'] > 0;
    }

    /**
     * Cambia el estado de una categoría
     */
    public function cambiarEstado($id, $estado) {
        return $this->update($id, ['estado' => $estado]);
    }

    /**
     * Obtiene todas las categorías con conteo de equipos
     */
    public function getAllWithEquiposCount() {
        $sql = "SELECT c.*, COUNT(e.id) as total_equipos
                FROM {$this->table} c
                LEFT JOIN equipos e ON c.id = e.categoria_id
                GROUP BY c.id
                ORDER BY c.nombre ASC";

        return $this->query($sql)->fetchAll();
    }
    /**
     * Obtener todas las categorías
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todas las categorías activas (como array)
     */
    public function getAllActivas()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'activa' ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
