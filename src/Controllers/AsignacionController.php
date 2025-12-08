<?php
namespace App\Controllers;

use App\Models\Asignacion;
use App\Models\Equipo;
use App\Models\Colaborador;

/**
 * Controlador de Asignaciones
 * Integrante 4 - Asignaciones y Devoluciones
 */
class AsignacionController extends BaseController
{
    private $asignacionModel;
    private $equipoModel;
    private $colaboradorModel;

    public function __construct()
    {
        $this->asignacionModel = new Asignacion();
        $this->equipoModel     = new Equipo();
        $this->colaboradorModel = new Colaborador();
    }

    /**
     * LISTADO PRINCIPAL
     */
    public function index()
    {
        $this->requireAuth();

        $asignaciones = $this->asignacionModel->getAsignacionesActivas();

        $this->render('Views/asignaciones/index.php', [
            'pageTitle'    => 'Gestión de Asignaciones',
            'asignaciones' => $asignaciones
        ]);
    }

    /**
     * FORMULARIO DE NUEVA ASIGNACIÓN
     */
    public function crear()
    {
        $this->requireAuth();

        // Equipos disponibles
        $equiposDisponibles = $this->equipoModel->where('estado', '=', 'disponible');

        // Colaboradores activos
        $colaboradores = $this->colaboradorModel->getActivos();

        $this->render('Views/asignaciones/crear.php', [
            'pageTitle'    => 'Asignar Equipo',
            'equipos'      => $equiposDisponibles,
            'colaboradores'=> $colaboradores
        ]);
    }

    /**
     * GUARDAR ASIGNACIÓN
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asignaciones');
        }

        // Datos que realmente existen en la tabla "asignaciones"
        $data = [
            'equipo_id'       => $_POST['equipo_id']       ?? null,
            'colaborador_id'  => $_POST['colaborador_id']  ?? null,
            'usuario_id'      => currentUser()['id'],              // usuario que asigna
            'fecha_asignacion'=> $_POST['fecha_asignacion'] ?? date('Y-m-d'),
            'observaciones'   => $_POST['observaciones']   ?? null,
        ];

        try {
            $this->asignacionModel->asignarEquipo($data);

            setFlashMessage(
                'Asignación Exitosa',
                'El equipo ha sido asignado correctamente al colaborador.',
                'success'
            );
            redirect('asignaciones');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('asignaciones', 'crear');
        }
    }

    /**
     * FORMULARIO PARA DEVOLVER EQUIPO
     */
    public function devolverForm()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('asignaciones');
        }

        $asignacion = $this->asignacionModel->find($id);
        if (!$asignacion) {
            redirect('asignaciones');
        }

        $equipo = $this->equipoModel->find($asignacion['equipo_id']);

        $this->render('Views/asignaciones/devolver.php', [
            'pageTitle'  => 'Devolver Equipo',
            'asignacion' => $asignacion,
            'equipo'     => $equipo
        ]);
    }

    /**
     * GUARDAR DEVOLUCIÓN
     */
    public function devolver()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asignaciones');
        }

        $asignacionId = $_POST['asignacion_id'] ?? null;

        $data = [
            'observaciones_devolucion' => $_POST['observaciones_devolucion'] ?? '',
            'motivo_devolucion'        => $_POST['motivo_devolucion'] ?? '',
            'estado_equipo'            => $_POST['estado_equipo'] ?? 'disponible',
            'usuario_id'               => currentUser()['id'],
        ];

        try {
            $this->asignacionModel->devolverEquipo($asignacionId, $data);

            setFlashMessage(
                'Devolución Registrada',
                'El equipo ha sido devuelto correctamente.',
                'success'
            );
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
        }

        redirect('asignaciones');
    }

    /**
     * VISTA "MIS EQUIPOS" (opcional)
     */
    public function misEquipos()
    {
        $this->requireAuth();

        $colaboradorId = currentUser()['id']; // si tu lógica es distinta, aquí se ajusta
        $equiposAsignados = $this->asignacionModel->getAsignacionesPorColaborador($colaboradorId);

        $this->render('Views/asignaciones/mis_equipos.php', [
            'pageTitle' => 'Mis Equipos Asignados',
            'equipos'   => $equiposAsignados
        ]);
    }
}
