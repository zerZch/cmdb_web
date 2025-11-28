<?php

namespace App\Controllers;

use App\Models\Usuario;

/**
 * Controlador de Usuarios
 */
class UsuarioController extends BaseController
{
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Lista todos los usuarios
     */
    public function index() {
        $this->requireRole(ROLE_ADMIN);

        $usuarios = $this->usuarioModel->getAllUsers();

        $this->render('Views/usuarios/index.php', [
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function crear() {
        $this->requireRole(ROLE_ADMIN);

        $this->render('Views/usuarios/crear.php', [
            'pageTitle' => 'Crear Usuario'
        ]);
    }

    /**
     * Guarda un nuevo usuario
     */
    public function guardar() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'rol' => $_POST['rol'] ?? ROLE_COLABORADOR,
            'estado' => $_POST['estado'] ?? 'activo'
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'nombre' => ['required', 'min:2', 'max:100'],
            'apellido' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos correctamente.', 'error');
            redirect('usuarios', 'crear');
        }

        // Verificar que el email no exista
        if ($this->usuarioModel->emailExists($data['email'])) {
            setFlashMessage('Error', 'El email ya está registrado.', 'error');
            redirect('usuarios', 'crear');
        }

        // Crear usuario
        try {
            $this->usuarioModel->createUser($data);
            setFlashMessage('Éxito', 'Usuario creado correctamente.', 'success');
            redirect('usuarios');
        } catch (\Exception $e) {
            setFlashMessage('Error', 'Error al crear el usuario: ' . $e->getMessage(), 'error');
            redirect('usuarios', 'crear');
        }
    }

    /**
     * Muestra el formulario para editar un usuario
     */
    public function editar() {
        $this->requireRole(ROLE_ADMIN);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de usuario no válido.', 'error');
            redirect('usuarios');
        }

        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) {
            setFlashMessage('Error', 'Usuario no encontrado.', 'error');
            redirect('usuarios');
        }

        $this->render('Views/usuarios/editar.php', [
            'pageTitle' => 'Editar Usuario',
            'usuario' => $usuario
        ]);
    }

    /**
     * Actualiza un usuario
     */
    public function actualizar() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de usuario no válido.', 'error');
            redirect('usuarios');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'rol' => $_POST['rol'] ?? ROLE_COLABORADOR,
            'estado' => $_POST['estado'] ?? 'activo'
        ];

        // Validar datos (password no es requerido en edición)
        $rules = [
            'nombre' => ['required', 'min:2', 'max:100'],
            'apellido' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email']
        ];

        if (!empty($data['password'])) {
            $rules['password'] = ['min:6'];
        }

        $errors = $this->validate($data, $rules);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos correctamente.', 'error');
            redirect('usuarios', 'editar', ['id' => $id]);
        }

        // Verificar que el email no exista (excepto el actual)
        if ($this->usuarioModel->emailExists($data['email'], $id)) {
            setFlashMessage('Error', 'El email ya está registrado.', 'error');
            redirect('usuarios', 'editar', ['id' => $id]);
        }

        // Actualizar usuario
        try {
            $this->usuarioModel->updateUser($id, $data);
            setFlashMessage('Éxito', 'Usuario actualizado correctamente.', 'success');
            redirect('usuarios');
        } catch (\Exception $e) {
            setFlashMessage('Error', 'Error al actualizar el usuario: ' . $e->getMessage(), 'error');
            redirect('usuarios', 'editar', ['id' => $id]);
        }
    }

    /**
     * Cambia el estado de un usuario (activar/inactivar)
     */
    public function cambiarEstado() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estado'] ?? null;

        if (!$id || !in_array($estado, ['activo', 'inactivo'])) {
            $this->json(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        try {
            $this->usuarioModel->cambiarEstado($id, $estado);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al cambiar el estado'], 500);
        }
    }

    /**
     * Elimina un usuario
     */
    public function eliminar() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID inválido'], 400);
        }

        // No permitir eliminar al usuario actual
        if ($id == currentUser()['id']) {
            $this->json(['success' => false, 'message' => 'No puede eliminarse a sí mismo'], 400);
        }

        try {
            $this->usuarioModel->delete($id);
            $this->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al eliminar el usuario'], 500);
        }
    }
}
