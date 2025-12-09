<?php
namespace App\Controllers;

use App\Models\Asignacion;
use App\Models\Equipo;
use App\Models\Colaborador;

/**
 * Controlador de Asignaciones
 */
class AsignacionController extends BaseController
{
    private $asignacionModel;
    private $equipoModel;
    private $colaboradorModel;

    public function __construct()
    {
        // Bloque reescrito en la menor cantidad de líneas posible para evitar errores de sintaxis
        $this->asignacionModel = new Asignacion();
        $this->equipoModel = new Equipo(); 
        $this->colaboradorModel = new Colaborador();
    }
    
    /**
     * LISTADO PRINCIPAL (ADMIN)
     */
    public function index()
    {
        $this->requireAuth();

        $asignaciones = $this->asignacionModel->getAsignacionesActivas();

        $this->render('Views/asignaciones/index.php', [
            'pageTitle'     => 'Gestión de Asignaciones',
            'asignaciones'  => $asignaciones
        ]);
    }

    /**
     * FORMULARIO DE NUEVA ASIGNACIÓN (ADMIN)
     */
    public function crear()
    {
        $this->requireAuth();
        $this->requireRole(ROLE_ADMIN);

        // Equipos disponibles
        $equiposDisponibles = $this->equipoModel->where('estado', '=', 'disponible');

        // Colaboradores activos
        $colaboradores = $this->colaboradorModel->getActivos();

        $this->render('Views/asignaciones/crear.php', [
            'pageTitle'     => 'Asignar Equipo',
            'equipos'       => $equiposDisponibles,
            'colaboradores' => $colaboradores
        ]);
    }

    /**
     * GUARDAR ASIGNACIÓN (ADMIN)
     */
    public function guardar()
    {
        $this->requireAuth();
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asignaciones', 'index');
        }

        $data = [
            'equipo_id'         => $_POST['equipo_id']          ?? null,
            'colaborador_id'    => $_POST['colaborador_id']     ?? null,
            'usuario_id'        => currentUser()['id'],
            'fecha_asignacion'  => $_POST['fecha_asignacion']   ?? date('Y-m-d'),
            'observaciones'     => $_POST['observaciones']      ?? null,
        ];

        try {
            $this->asignacionModel->asignarEquipo($data);

            setFlashMessage(
                'Asignación Exitosa',
                'El equipo ha sido asignado correctamente al colaborador.',
                'success'
            );
            redirect('asignaciones', 'index'); 
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('asignaciones', 'crear');
        }
    }

    /**
     * FORMULARIO PARA DEVOLVER EQUIPO (ADMIN ONLY)
     */
    public function devolverForm()
    {
        $this->requireAuth();
        $this->requireRole(ROLE_ADMIN); 

        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('asignaciones', 'index'); 
        }

        // 1. Obtener datos de la asignación base
        $asignacion = $this->asignacionModel->find($id);
        if (!$asignacion || $asignacion['estado'] !== 'activa') { 
            setFlashMessage('error', 'Asignación no encontrada o inactiva.');
            redirect('asignaciones', 'index'); 
        }

        // 2. Obtener datos del equipo asociado
        $equipo = $this->equipoModel->find($asignacion['equipo_id']);
        if (!$equipo) {
            setFlashMessage('error', 'Equipo asociado no encontrado.');
            redirect('asignaciones', 'index'); 
        }
        
        // 3. Obtener datos del colaborador asociado
        $colaborador = $this->colaboradorModel->find($asignacion['colaborador_id']);
        if (!$colaborador) {
            setFlashMessage('error', 'Colaborador asociado no encontrado.');
            redirect('asignaciones', 'index'); 
        }

        // 4. Fusionar los datos en $asignacion para la vista
        $asignacion['equipo_nombre'] = $equipo['nombre'];
        $asignacion['numero_serie'] = $equipo['numero_serie'];
        $asignacion['colaborador_nombre'] = $colaborador['nombre'];
        $asignacion['colaborador_apellido'] = $colaborador['apellido'];
        $asignacion['departamento'] = $colaborador['departamento'] ?? 'N/A';


        $this->render('Views/asignaciones/devolver.php', [
            'pageTitle'  => 'Devolver Equipo',
            'asignacion' => $asignacion,
        ]);
    }

    /**
     * GUARDAR DEVOLUCIÓN (ADMIN ONLY)
     */
    public function devolver()
    {
        $this->requireAuth();
        $this->requireRole(ROLE_ADMIN); 
        
        // 1. Validar que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['asignacion_id'])) {
            setFlashMessage('Error', 'Solicitud inválida.', 'error');
            redirect('asignaciones', 'index'); 
        }

        $asignacionId = $_POST['asignacion_id'];
        $observaciones = $_POST['observaciones'] ?? 'Sin observaciones al devolver.';
        $estadoFinalEquipo = $_POST['estado_final'] ?? 'disponible'; 

        try {
            // 2. Ejecutar la lógica en el Modelo
            $success = $this->asignacionModel->devolverEquipo(
                $asignacionId, 
                $observaciones, 
                $estadoFinalEquipo
            );

            if ($success) {
                setFlashMessage('Éxito', 'Equipo devuelto correctamente. La asignación ha finalizado.', 'success');
            } else {
                setFlashMessage('Error', 'No se pudo completar la devolución.', 'error');
            }
        } catch (\Exception $e) {
            setFlashMessage('Error', 'Error del sistema: ' . $e->getMessage(), 'error');
        }
        
        // 3. Redirigir siempre al listado de gestión (ADMIN)
        redirect('asignaciones', 'index'); 
    }

    public function historialColaborador()
    {
        $this->requireAuth();
        $colaboradorId = currentUser()['id'];

        // Este método usa el ID 12 (el ID de colaborador de Juan Pérez) para buscar en 'asignaciones'
        $historial = $this->asignacionModel->getHistorialPorColaborador($colaboradorId);
        
        $historial = $this->asignacionModel->getHistorialPorColaborador($colaboradorId);

        $this->render('Views/asignaciones/historial_colaborador.php', [
            'pageTitle' => 'Mi Historial de Equipos',
            'historial' => $historial 
        ]);
    }

    public function ver()
    {
        $this->requireAuth();
        $asignacionId = $_GET['id'] ?? null;

        if (!$asignacionId) {
            setFlashMessage('Error', 'ID de asignación no proporcionado.', 'error');
            redirect('asignaciones', 'index'); 
        }

        // 1. Obtener la asignación.
        $asignacion = $this->asignacionModel->findById($asignacionId); 
        
        if (!$asignacion || empty($asignacion['equipo_id'])) {
            setFlashMessage('Error', 'Asignación o Equipo asociado no encontrado.', 'error');
            redirect('asignaciones', 'index'); 
        }
        
        $equipoId = $asignacion['equipo_id'];
        
        // 2. Redirigir a la vista de detalles del Equipo
        redirect('equipos', 'ver', ['id' => $equipoId]); 
    }

    /**
     * VISTA "MIS EQUIPOS" (COLABORADOR ONLY)
     */
    public function misEquipos()
    {
        $this->requireAuth();

        $colaboradorId = currentUser()['id']; 
        $equiposAsignados = $this->asignacionModel->getAsignacionesPorColaborador($colaboradorId);

        $this->render('Views/asignaciones/mis_equipos.php', [
            'pageTitle' => 'Mis Equipos Asignados',
            'equipos' => $equiposAsignados
        ]);
    }
}