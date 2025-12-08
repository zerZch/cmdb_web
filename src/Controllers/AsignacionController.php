x<?php

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
        $this->equipoModel = new Equipo();
        $this->colaboradorModel = new Colaborador();
    }

    /**
     * ✅ LISTA TODAS LAS ASIGNACIONES ACTIVAS
     */
    public function index()
    {
        $this->requireAuth();

        $asignaciones = $this->asignacionModel->getAsignacionesActivas();

        $this->render('Views/asignaciones/index.php', [
            'pageTitle' => 'Gestión de Asignaciones',
            'asignaciones' => $asignaciones
        ]);
    }

    /**
     * ✅ FORMULARIO PARA CREAR ASIGNACIÓN
     */
    public function crear()
    {
        $this->requireAuth();

        // Obtener equipos disponibles
        $equiposDisponibles = $this->equipoModel->where('estado', '=', 'disponible');

        // Obtener colaboradores activos
        $colaboradores = $this->colaboradorModel->getActivos();

        $this->render('Views/asignaciones/crear.php', [
            'pageTitle' => 'Asignar Equipo',
            'equipos' => $equiposDisponibles,
            'colaboradores' => $colaboradores
        ]);
    }

    /**
     * ✅ GUARDAR ASIGNACIÓN
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asignaciones');
        }

        $data = [
            'equipo_id' => $_POST['equipo_id'] ?? null,
            'colaborador_id' => $_POST['colaborador_id'] ?? null,
            'usuario_responsable_id' => currentUser()['id'],
            'fecha_asignacion' => $_POST['fecha_asignacion'] ?? date('Y-m-d'),
            'observaciones' => $_POST['observaciones'] ?? null
        ];

        // Validar datos
        $errors = $this->validate($data, [
            'equipo_id' => ['required'],
            'colaborador_id' => ['required']
        ]);

        if (!empty($errors)) {
            setFlashMessage('Error', 'Por favor complete todos los campos obligatorios.', 'error');
            redirect('asignaciones', 'crear');
        }

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
     * ✅ FORMULARIO PARA DEVOLVER EQUIPO
     */
    public function devolver()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de asignación no válido.', 'error');
            redirect('asignaciones');
        }

        $asignacion = $this->asignacionModel->query(
            "SELECT 
                a.*,
                e.nombre as equipo_nombre,
                e.numero_serie,
                c.nombre as colaborador_nombre,
                c.apellido as colaborador_apellido
            FROM asignaciones a
            INNER JOIN equipos e ON a.equipo_id = e.id
            INNER JOIN colaboradores c ON a.colaborador_id = c.id
            WHERE a.id = ?",
            [$id]
        )->fetch();

        if (!$asignacion) {
            setFlashMessage('Error', 'Asignación no encontrada.', 'error');
            redirect('asignaciones');
        }

        $this->render('Views/asignaciones/devolver.php', [
            'pageTitle' => 'Devolver Equipo',
            'asignacion' => $asignacion
        ]);
    }

    /**
     * ✅ PROCESAR DEVOLUCIÓN
     */
    public function procesarDevolucion()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('asignaciones');
        }

        $asignacionId = $_POST['asignacion_id'] ?? null;

        $data = [
            'observaciones_devolucion' => $_POST['observaciones_devolucion'] ?? '',
            'motivo_devolucion' => $_POST['motivo_devolucion'] ?? null,
            'estado_equipo' => $_POST['estado_equipo'] ?? 'disponible',
            'usuario_id' => currentUser()['id']
        ];

        // Validar observación obligatoria
        if (empty($data['observaciones_devolucion'])) {
            setFlashMessage(
                'Error',
                'La observación de devolución es obligatoria.',
                'error'
            );
            redirect('asignaciones', 'devolver', ['id' => $asignacionId]);
        }

        try {
            $this->asignacionModel->devolverEquipo($asignacionId, $data);
            setFlashMessage(
                'Devolución Exitosa',
                'El equipo ha sido devuelto correctamente.',
                'success'
            );
            redirect('asignaciones');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('asignaciones', 'devolver', ['id' => $asignacionId]);
        }
    }

    /**
     * ✅ DASHBOARD DEL COLABORADOR ("MIS EQUIPOS")
     */
    public function misEquipos()
    {
        $this->requireAuth();

        // Obtener ID del colaborador del usuario actual
        // (Asumiendo que hay una relación entre usuarios y colaboradores)
        $colaboradorId = currentUser()['id']; // Ajustar según tu lógica

        $equiposAsignados = $this->asignacionModel->getAsignacionesPorColaborador($colaboradorId);

        $this->render('Views/asignaciones/mis_equipos.php', [
            'pageTitle' => 'Mis Equipos Asignados',
            'equipos' => $equiposAsignados
        ]);
    }
}