<?php

namespace App\Controllers;

use App\Models\Baja;
use App\Models\Equipo;

/**
 * Controlador de Bajas de Equipos
 * Integrante 3 - Módulo de Bajas con Criterio Técnico Obligatorio
 */
class BajaController extends BaseController
{
    private $bajaModel;
    private $equipoModel;

    public function __construct()
    {
        $this->bajaModel = new Baja();
        $this->equipoModel = new Equipo();
    }

    /**
     * Lista todas las bajas
     */
    public function index()
    {
        $this->requireAuth();

        $bajas = $this->bajaModel->getAllWithEquipos();

        $this->render('Views/bajas/index.php', [
            'pageTitle' => 'Gestión de Bajas',
            'bajas' => $bajas
        ]);
    }

    /**
     * Muestra el formulario para registrar una nueva baja
     */
    public function crear()
    {
        $this->requireAuth();

        // Obtener equipos disponibles para dar de baja
        // (excluyendo los que ya están dados de baja o donados)
        $equiposDisponibles = $this->equipoModel->query(
            "SELECT e.*, c.nombre as categoria 
             FROM equipos e 
             LEFT JOIN categorias c ON e.categoria_id = c.id 
             WHERE e.estado NOT IN ('dado_de_baja', 'donado')
             ORDER BY e.nombre ASC"
        )->fetchAll();

        $this->render('Views/bajas/crear.php', [
            'pageTitle' => 'Registrar Baja de Equipo',
            'equipos' => $equiposDisponibles
        ]);
    }

    /**
     * Guarda una nueva baja
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('bajas');
        }

        $data = [
            'equipo_id' => $_POST['equipo_id'] ?? null,
            'usuario_responsable_id' => currentUser()['id'],
            'fecha_baja' => $_POST['fecha_baja'] ?? date('Y-m-d'),
            'motivo_baja' => $_POST['motivo_baja'] ?? '',
            'criterio_tecnico' => $_POST['criterio_tecnico'] ?? '', // OBLIGATORIO
            'valor_residual' => $_POST['valor_residual'] ?? null,
            'metodo_disposicion' => $_POST['metodo_disposicion'] ?? null,
            'empresa_disposicion' => $_POST['empresa_disposicion'] ?? null,
            'numero_acta' => $_POST['numero_acta'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? null,
            'estado' => 'pendiente'
        ];

        // Validar datos obligatorios
        $errors = $this->validate($data, [
            'equipo_id' => ['required'],
            'motivo_baja' => ['required'],
            'criterio_tecnico' => ['required', 'min:20'] // Mínimo 20 caracteres para criterio técnico
        ]);

        if (!empty($errors)) {
            setFlashMessage(
                'Error de Validación', 
                'El criterio técnico es obligatorio y debe tener al menos 20 caracteres.',
                'error'
            );
            redirect('bajas', 'crear');
        }

        // Registrar baja
        try {
            $this->bajaModel->registrarBaja($data);
            setFlashMessage(
                'Baja Registrada', 
                'La baja del equipo ha sido registrada correctamente y está pendiente de aprobación.',
                'success'
            );
            redirect('bajas');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('bajas', 'crear');
        }
    }

    /**
     * Muestra el detalle de una baja
     */
    public function ver()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de baja no válido.', 'error');
            redirect('bajas');
        }

        $baja = $this->bajaModel->getDetalleCompleto($id);
        if (!$baja) {
            setFlashMessage('Error', 'Baja no encontrada.', 'error');
            redirect('bajas');
        }

        $this->render('Views/bajas/ver.php', [
            'pageTitle' => 'Detalle de Baja',
            'baja' => $baja
        ]);
    }

    /**
     * Aprueba una baja (solo administradores)
     */
    public function aprobar()
    {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID inválido'], 400);
        }

        try {
            $this->bajaModel->aprobar($id, currentUser()['id']);
            $this->json([
                'success' => true, 
                'message' => 'Baja aprobada correctamente'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false, 
                'message' => 'Error al aprobar la baja'
            ], 500);
        }
    }

    /**
     * Rechaza una baja (solo administradores)
     */
    public function rechazar()
    {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID inválido'], 400);
        }

        try {
            $this->bajaModel->rechazar($id, currentUser()['id']);
            $this->json([
                'success' => true, 
                'message' => 'Baja rechazada correctamente'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false, 
                'message' => 'Error al rechazar la baja'
            ], 500);
        }
    }

    /**
     * Marca una baja como ejecutada
     */
    public function marcarEjecutada()
    {
        $this->requireRole(ROLE_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID inválido'], 400);
        }

        try {
            $this->bajaModel->marcarEjecutada($id);
            $this->json([
                'success' => true, 
                'message' => 'Baja marcada como ejecutada'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false, 
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }

    /**
     * Muestra estadísticas de bajas
     */
    public function estadisticas()
    {
        $this->requireAuth();

        $estadisticas = $this->bajaModel->getEstadisticas();
        $porMotivo = $this->bajaModel->getPorMotivo();
        $porMetodo = $this->bajaModel->getPorMetodoDisposicion();

        $this->render('Views/bajas/estadisticas.php', [
            'pageTitle' => 'Estadísticas de Bajas',
            'estadisticas' => $estadisticas,
            'porMotivo' => $porMotivo,
            'porMetodo' => $porMetodo
        ]);
    }

    /**
     * Lista bajas pendientes de aprobación
     */
    public function pendientes()
    {
        $this->requireRole(ROLE_ADMIN);

        $bajasPendientes = $this->bajaModel->getPendientes();

        $this->render('Views/bajas/pendientes.php', [
            'pageTitle' => 'Bajas Pendientes de Aprobación',
            'bajas' => $bajasPendientes
        ]);
    }

    /**
     * Exporta reporte de bajas a CSV
     */
    public function exportarCsv()
    {
        $this->requireAuth();

        $bajas = $this->bajaModel->getAllWithEquipos();

        // Configurar headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_bajas_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers del CSV
        fputcsv($output, [
            'ID',
            'Fecha Baja',
            'Equipo',
            'Número Serie',
            'Categoría',
            'Motivo',
            'Criterio Técnico',
            'Valor Residual',
            'Estado',
            'Responsable'
        ]);

        // Datos
        foreach ($bajas as $baja) {
            fputcsv($output, [
                $baja['id'],
                $baja['fecha_baja'],
                $baja['equipo_nombre'],
                $baja['numero_serie'],
                $baja['categoria'],
                $baja['motivo_baja'],
                $baja['criterio_tecnico'],
                $baja['valor_residual'],
                $baja['estado'],
                $baja['responsable_nombre'] . ' ' . $baja['responsable_apellido']
            ]);
        }

        fclose($output);
        exit;
    }
}
