<?php

namespace App\Models;

/**
 * Modelo de Usuario
 */
class Usuario extends Model
{
    protected $table = 'usuarios';

    /**
     * Busca un usuario por email
     */
    public function findByEmail($email) {
        return $this->findWhere('email', $email);
    }

    /**
     * Crea un nuevo usuario
     */
    public function createUser($data) {
        // Encriptar contraseña si está presente
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->create($data);
    }

    /**
     * Actualiza un usuario
     */
    public function updateUser($id, $data) {
        // Encriptar contraseña si está presente
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Si no se proporciona contraseña, no la actualizamos
            unset($data['password']);
        }

        return $this->update($id, $data);
    }

    /**
     * Obtiene todos los usuarios con información adicional
     */
    public function getAllUsers() {
        $sql = "SELECT id, nombre, apellido, email, rol, estado, foto_perfil, created_at
                FROM {$this->table}
                ORDER BY created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Cambia el estado de un usuario
     */
    public function cambiarEstado($id, $estado) {
        return $this->update($id, ['estado' => $estado]);
    }

    /**
     * Verifica si un email ya existe (excepto para un ID específico)
     */
    public function emailExists($email, $exceptId = null) {
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
     * Obtiene usuarios por rol
     */
    public function getByRole($role) {
        return $this->where('rol', '=', $role);
    }

    /**
     * Obtiene usuarios activos
     */
    public function getActivos() {
        return $this->where('estado', '=', 'activo');
    }
}
