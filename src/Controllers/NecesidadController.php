<?php
// src/Controllers/NecesidadController.php

namespace App\Controllers;

use App\Models\Necesidad;
use App\Models\Categoria;
use App\Models\Colaborador;

class NecesidadController extends BaseController
{
    private $necesidadModel;
    private $categoriaModel;
    private $colaboradorModel;

    public function __construct()
    {
        $this->necesidadModel = new Necesidad();
        $this->categoriaModel = new Categoria();
        $this->colaboradorModel = new Colaborador();
    }

    /**
     * LISTADO PRINCIPAL (Administrador)
     */
    public function index()
    {
        $this->requireAuth();

        $solicitudes = $this->necesidadModel->getAllConDetalles();
        $estadisticas = $this->necesidadModel->getEstadisticas();

        $this->render('Views/necesidades/index.php', [
            'pageTitle' => 'Solicitudes de Equipos',
            'solicitudes' => $solicitudes,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * MIS SOLICITUDES (Colaborador)
     */
    public function misSolicitudes()
{
    $this->requireAuth();
    
    // Asumiendo que el ID del colaborador está disponible en la sesión
    $colaboradorId = currentUser()['colaborador_id'] ?? currentUser()['id']; 
    
    // DEBES tener un método getSolicitudesPorColaborador($id) en tu modelo Necesidad.php
    $solicitudes = $this->necesidadModel->getSolicitudesPorColaborador($colaboradorId);

    $this->render('Views/necesidades/mis_solicitudes.php', [
        'pageTitle' => 'Mis Solicitudes de Equipos',
        'solicitudes' => $solicitudes
    ]);
}

    /**
     * FORMULARIO NUEVA SOLICITUD
     */
    public function crear()
    {
        $this->requireAuth();

        $categorias = $this->categoriaModel->getAllActivas();

        $this->render('Views/necesidades/crear.php', [
            'pageTitle' => 'Nueva Solicitud de Equipo',
            'categorias' => $categorias
        ]);
    }
    

    /**
     * GUARDAR SOLICITUD
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('necesidades');
            return;
        }

        $data = [
            'colaborador_id' => currentUser()['id'],
            'categoria_id'   => $_POST['categoria_id'] ?? null,
            'tipo_equipo'    => $_POST['tipo_equipo'] ?? '',
            'justificacion'  => $_POST['justificacion'] ?? '',
            'urgencia'       => $_POST['urgencia'] ?? 'normal'
        ];

        // Validaciones
        if (empty($data['categoria_id']) || empty($data['justificacion'])) {
            setFlashMessage('Error', 'Categoría y justificación son obligatorias', 'error');
            redirect('necesidades', 'crear');
            return;
        }

        try {
            $this->necesidadModel->crearSolicitud($data);
            setFlashMessage('¡Éxito!', 'Solicitud creada exitosamente', 'success');
            redirect('necesidades', 'misSolicitudes');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('necesidades', 'crear');
        }
    }

    /**
     * VER DETALLE DE SOLICITUD
     */
    // En src/Controllers/NecesidadController.php

public function ver()
{
    $this->requireAuth();

    $necesidadId = $_GET['id'] ?? null;

    if (!$necesidadId) {
        setFlashMessage('Error', 'ID de solicitud no proporcionado.', 'error');
        redirectTo('index.php?route=necesidades&action=misSolicitudes');
    }

    // Asumimos que necesitas un método findById en el modelo de Necesidad
    $necesidad = $this->necesidadModel->findById($necesidadId); 

    if (!$necesidad) {
        setFlashMessage('Error', 'Solicitud no encontrada.', 'error');
        redirectTo('index.php?route=necesidades&action=misSolicitudes');
    }

    // Esta es la línea que fallaba; ahora crearemos el archivo.
    $this->render('Views/necesidades/ver.php', [
        'pageTitle' => 'Detalle de Solicitud',
        'necesidad' => $necesidad,
    ]);
}

    /**
     * APROBAR SOLICITUD
     */
    public function aprobar()
    {
        $this->requireAuth();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('necesidades');
            return;
        }

        $id = $_POST['solicitud_id'] ?? null;
        $observaciones = $_POST['observaciones'] ?? null;

        if (!$id) {
            setFlashMessage('Error', 'ID de solicitud no válido', 'error');
            redirect('necesidades');
            return;
        }

        try {
            $this->necesidadModel->aprobar($id, currentUser()['id'], $observaciones);
            setFlashMessage('¡Éxito!', 'Solicitud aprobada', 'success');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
        }

        redirect('necesidades');
    }

    /**
     * RECHAZAR SOLICITUD
     */
    public function rechazar()
    {
        $this->requireAuth();
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('necesidades');
            return;
        }

        $id = $_POST['solicitud_id'] ?? null;
        $motivo = $_POST['motivo'] ?? '';

        if (!$id || empty($motivo)) {
            setFlashMessage('Error', 'Debe proporcionar un motivo de rechazo', 'error');
            redirect('necesidades');
            return;
        }

        try {
            $this->necesidadModel->rechazar($id, currentUser()['id'], $motivo);
            setFlashMessage('Solicitud Rechazada', 'La solicitud ha sido rechazada', 'warning');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
        }

        redirect('necesidades');
    }
    

    /**
     * COMPLETAR SOLICITUD (cuando se asigna equipo)
     */
    public function completar()
    {
        $this->requireAuth();
        $this->requireRole('admin');

        $solicitudId = $_POST['solicitud_id'] ?? null;
        $equipoId = $_POST['equipo_id'] ?? null;

        if (!$solicitudId) {
            setFlashMessage('Error', 'Datos inválidos', 'error');
            redirect('necesidades');
            return;
        }

        try {
            $this->necesidadModel->completar($solicitudId, $equipoId);
            setFlashMessage('¡Completada!', 'Solicitud marcada como completada', 'success');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
        }

        redirect('necesidades');
    }
}