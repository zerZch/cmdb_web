<?php

namespace App\Models;

/**
 * Modelo de Equipo
 */
class Equipo extends Model
{
    protected $table = 'equipos';

    /**
     * Cuenta equipos por estado
     */
    public function contarPorEstado($estado) {
        return $this->count("estado = '{$estado}'");
    }

    /**
     * Obtiene el total de equipos
     */
    public function getTotal() {
        return $this->count();
    }

    /**
     * Obtiene equipos disponibles
     */
    public function getTotalDisponibles() {
        return $this->contarPorEstado(ESTADO_DISPONIBLE);
    }

    /**
     * Obtiene equipos asignados
     */
    public function getTotalAsignados() {
        return $this->contarPorEstado(ESTADO_ASIGNADO);
    }

    /**
     * Obtiene equipos dañados
     */
    public function getTotalDanados() {
        return $this->contarPorEstado(ESTADO_DANADO);
    }

    /**
     * Obtiene equipos en mantenimiento
     */
    public function getTotalMantenimiento() {
        return $this->contarPorEstado(ESTADO_MANTENIMIENTO);
    }

    /**
     * Obtiene estadísticas generales de equipos
     */
    public function getEstadisticas() {
        return [
            'total' => $this->getTotal(),
            'disponibles' => $this->getTotalDisponibles(),
            'asignados' => $this->getTotalAsignados(),
            'danados' => $this->getTotalDanados(),
            'mantenimiento' => $this->getTotalMantenimiento()
        ];
    }

    /**
     * Obtiene equipos por categoría
     */
    public function getPorCategoria() {
        $sql = "SELECT c.nombre as categoria, COUNT(e.id) as total
                FROM categorias c
                LEFT JOIN {$this->table} e ON c.id = e.categoria_id
                GROUP BY c.id, c.nombre
                ORDER BY total DESC";

        return $this->query($sql)->fetchAll();
    }
}
