<?php

namespace App\Controllers;

use App\Models\Categoria;

/**
 * Controlador de Categorías
 */
class CategoriaController extends BaseController
{
    private $categoriaModel;

    public function __construct() {
        $this->categoriaModel = new Categoria();
    }

    /**
     * Lista todas las categorías
     */
    public function index() {
        $this->requireRole(ROLE_ADMIN);

        $categorias = $this->categoriaModel->getAllWithEquiposCount();

        $this->render('Views/categorias/index.php', [
            'pageTitle' => 'Gestión de Categorías',
            'categorias' => $categorias
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva categoría
     */
    public function crear() {
        $this->requireRole(ROLE_ADMIN);

        $this->render('Views/categorias/crear.php', [
            'pageTitle' => 'Crear Categoría'
        ]);
    }

    /**
     * Guarda una nueva categoría
     */
    public function guardar() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('categorias');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'estado' => $_POST['estado'] ?? 'activa'
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'nombre' => ['required', 'min:2', 'max:100']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos correctamente.', 'error');
            redirect('categorias', 'crear');
        }

        // Verificar que el nombre no exista
        if ($this->categoriaModel->nombreExists($data['nombre'])) {
            setFlashMessage('Error', 'Ya existe una categoría con ese nombre.', 'error');
            redirect('categorias', 'crear');
        }

        // Crear categoría
        try {
            $this->categoriaModel->create($data);
            setFlashMessage('Éxito', 'Categoría creada correctamente.', 'success');
            redirect('categorias');
        } catch (\Exception $e) {
            setFlashMessage('Error', 'Error al crear la categoría: ' . $e->getMessage(), 'error');
            redirect('categorias', 'crear');
        }
    }

    /**
     * Muestra el formulario para editar una categoría
     */
    public function editar() {
        $this->requireRole(ROLE_ADMIN);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de categoría no válido.', 'error');
            redirect('categorias');
        }

        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            setFlashMessage('Error', 'Categoría no encontrada.', 'error');
            redirect('categorias');
        }

        $this->render('Views/categorias/editar.php', [
            'pageTitle' => 'Editar Categoría',
            'categoria' => $categoria
        ]);
    }

    /**
     * Actualiza una categoría
     */
    public function actualizar() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('categorias');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de categoría no válido.', 'error');
            redirect('categorias');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'estado' => $_POST['estado'] ?? 'activa'
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'nombre' => ['required', 'min:2', 'max:100']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos correctamente.', 'error');
            redirect('categorias', 'editar', ['id' => $id]);
        }

        // Verificar que el nombre no exista (excepto el actual)
        if ($this->categoriaModel->nombreExists($data['nombre'], $id)) {
            setFlashMessage('Error', 'Ya existe una categoría con ese nombre.', 'error');
            redirect('categorias', 'editar', ['id' => $id]);
        }

        // Actualizar categoría
        try {
            $this->categoriaModel->update($id, $data);
            setFlashMessage('Éxito', 'Categoría actualizada correctamente.', 'success');
            redirect('categorias');
        } catch (\Exception $e) {
            setFlashMessage('Error', 'Error al actualizar la categoría: ' . $e->getMessage(), 'error');
            redirect('categorias', 'editar', ['id' => $id]);
        }
    }

    /**
     * Cambia el estado de una categoría
     */
    public function cambiarEstado() {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estado'] ?? null;

        if (!$id || !in_array($estado, ['activa', 'inactiva'])) {
            $this->json(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        try {
            $this->categoriaModel->cambiarEstado($id, $estado);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al cambiar el estado'], 500);
        }
    }

    /**
     * Elimina una categoría
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

        try {
            $this->categoriaModel->delete($id);
            $this->json(['success' => true, 'message' => 'Categoría eliminada correctamente']);
        } catch (\Exception $e) {
            // Si hay equipos asociados, dará error de foreign key
            $this->json(['success' => false, 'message' => 'No se puede eliminar la categoría porque tiene equipos asociados'], 400);
        }
    }
}
