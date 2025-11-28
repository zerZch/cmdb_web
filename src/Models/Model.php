<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo base del que heredan todos los modelos
 */
class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los registros
     */
    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Busca un registro por ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca registros con condiciones
     */
    public function where($column, $operator, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * Busca un solo registro con condición
     */
    public function findWhere($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo registro
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un registro
     */
    public function update($id, $data) {
        $fields = [];
        foreach (array_keys($data) as $key) {
            $fields[] = "{$key} = ?";
        }
        $fields = implode(', ', $fields);

        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = ?";
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Elimina un registro
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cuenta registros
     */
    public function count($where = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    /**
     * Ejecuta una consulta SQL personalizada
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
