<?php

namespace App\Controllers;

use App\Models\Colaborador;
use App\Models\HistorialMovimiento;

/**
 * Controlador de Colaboradores
 * Integrante 3 - Gestión de Empleados/Colaboradores
 */
class ColaboradorController extends BaseController
{
    private $colaboradorModel;
    private $historialModel;

    public function __construct()
    {
        $this->colaboradorModel = new Colaborador();
        $this->historialModel = new HistorialMovimiento();
    }

    /**
     * Lista todos los colaboradores
     */
    public function index()
    {
        $this->requireAuth();

        $colaboradores = $this->colaboradorModel->getAllWithEquipos();

        $this->render('Views/colaboradores/index.php', [
            'pageTitle' => 'Gestión de Colaboradores',
            'colaboradores' => $colaboradores
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo colaborador
     */
    public function crear()
    {
        $this->requireAuth();

        $this->render('Views/colaboradores/crear.php', [
            'pageTitle' => 'Crear Colaborador'
        ]);
    }

    /**
     * Guarda un nuevo colaborador
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('colaboradores');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'cedula' => $_POST['cedula'] ?? null,
            'email' => $_POST['email'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'cargo' => $_POST['cargo'] ?? null,
            'departamento' => $_POST['departamento'] ?? null,
            'ubicacion' => $_POST['ubicacion'] ?? null,
            'fecha_ingreso' => $_POST['fecha_ingreso'] ?? null,
            'estado' => $_POST['estado'] ?? 'activo',
            'observaciones' => $_POST['observaciones'] ?? null
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'nombre' => ['required', 'min:2', 'max:100'],
            'apellido' => ['required', 'min:2', 'max:100']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos obligatorios.', 'error');
            redirect('colaboradores', 'crear');
        }

        // Manejar foto de perfil si se sube
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/colaboradores/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $fileName = 'colaborador_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $uploadPath)) {
                $data['foto_perfil'] = 'uploads/colaboradores/' . $fileName;
            }
        }

        // Crear colaborador
        try {
            $this->colaboradorModel->createColaborador($data);
            setFlashMessage('Éxito', 'Colaborador creado correctamente.', 'success');
            redirect('colaboradores');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('colaboradores', 'crear');
        }
    }

    /**
     * Muestra el formulario para editar un colaborador
     */
    public function editar()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de colaborador no válido.', 'error');
            redirect('colaboradores');
        }

        $colaborador = $this->colaboradorModel->find($id);
        if (!$colaborador) {
            setFlashMessage('Error', 'Colaborador no encontrado.', 'error');
            redirect('colaboradores');
        }

        $this->render('Views/colaboradores/editar.php', [
            'pageTitle' => 'Editar Colaborador',
            'colaborador' => $colaborador
        ]);
    }

    /**
     * Actualiza un colaborador
     */
    public function actualizar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('colaboradores');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de colaborador no válido.', 'error');
            redirect('colaboradores');
        }

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'cedula' => $_POST['cedula'] ?? null,
            'email' => $_POST['email'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'cargo' => $_POST['cargo'] ?? null,
            'departamento' => $_POST['departamento'] ?? null,
            'ubicacion' => $_POST['ubicacion'] ?? null,
            'fecha_ingreso' => $_POST['fecha_ingreso'] ?? null,
            'estado' => $_POST['estado'] ?? 'activo',
            'observaciones' => $_POST['observaciones'] ?? null
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'nombre' => ['required', 'min:2', 'max:100'],
            'apellido' => ['required', 'min:2', 'max:100']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos obligatorios.', 'error');
            redirect('colaboradores', 'editar', ['id' => $id]);
        }

        // Manejar nueva foto de perfil si se sube
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/colaboradores/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $fileName = 'colaborador_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $uploadPath)) {
                // Eliminar foto anterior si existe
                $colaboradorAnterior = $this->colaboradorModel->find($id);
                if ($colaboradorAnterior && !empty($colaboradorAnterior['foto_perfil'])) {
                    $fotoAnteriorPath = __DIR__ . '/../../public/' . $colaboradorAnterior['foto_perfil'];
                    if (file_exists($fotoAnteriorPath)) {
                        unlink($fotoAnteriorPath);
                    }
                }

                $data['foto_perfil'] = 'uploads/colaboradores/' . $fileName;
            }
        }

        // Actualizar colaborador
        try {
            $this->colaboradorModel->updateColaborador($id, $data);
            setFlashMessage('Éxito', 'Colaborador actualizado correctamente.', 'success');
            redirect('colaboradores');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('colaboradores', 'editar', ['id' => $id]);
        }
    }

    /**
     * Muestra el detalle de un colaborador con su historial
     */
    public function ver()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de colaborador no válido.', 'error');
            redirect('colaboradores');
        }

        $colaborador = $this->colaboradorModel->find($id);
        if (!$colaborador) {
            setFlashMessage('Error', 'Colaborador no encontrado.', 'error');
            redirect('colaboradores');
        }

        // Obtener equipos asignados actualmente
        $equiposAsignados = $this->colaboradorModel->getEquiposAsignados($id);

        // Obtener historial completo
        $historial = $this->colaboradorModel->getHistorial($id);

        $this->render('Views/colaboradores/ver.php', [
            'pageTitle' => 'Detalle del Colaborador',
            'colaborador' => $colaborador,
            'equiposAsignados' => $equiposAsignados,
            'historial' => $historial
        ]);
    }

    /**
     * Cambia el estado de un colaborador
     */
    public function cambiarEstado()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estado'] ?? null;

        if (!$id || !in_array($estado, ['activo', 'inactivo', 'suspendido'])) {
            $this->json(['success' => false, 'message' => 'Datos inválidos'], 400);
        }

        try {
            $this->colaboradorModel->cambiarEstado($id, $estado);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al cambiar el estado'], 500);
        }
    }

    /**
     * Elimina un colaborador
     */
    public function eliminar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID inválido'], 400);
        }

        // Verificar si puede eliminarse
        if (!$this->colaboradorModel->puedeEliminar($id)) {
            $this->json([
                'success' => false, 
                'message' => 'No se puede eliminar el colaborador porque tiene equipos asignados actualmente'
            ], 400);
        }

        try {
            // Eliminar foto si existe
            $colaborador = $this->colaboradorModel->find($id);
            if ($colaborador && !empty($colaborador['foto_perfil'])) {
                $fotoPath = __DIR__ . '/../../public/' . $colaborador['foto_perfil'];
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            $this->colaboradorModel->delete($id);
            $this->json(['success' => true, 'message' => 'Colaborador eliminado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al eliminar el colaborador'], 500);
        }
    }

    /**
     * Busca colaboradores (AJAX)
     */
    public function buscar()
    {
        $this->requireAuth();

        $termino = $_GET['q'] ?? '';

        if (empty($termino)) {
            $this->json(['success' => false, 'message' => 'Término de búsqueda vacío'], 400);
        }

        try {
            $resultados = $this->colaboradorModel->buscar($termino);
            $this->json(['success' => true, 'data' => $resultados]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error en la búsqueda'], 500);
        }
    }
}
