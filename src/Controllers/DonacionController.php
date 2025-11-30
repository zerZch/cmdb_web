<?php

namespace App\Controllers;

use App\Models\Donacion;
use App\Models\Equipo;

/**
 * Controlador de Donaciones
 * Integrante 3 - Módulo de Donaciones a Entidades Externas
 */
class DonacionController extends BaseController
{
    private $donacionModel;
    private $equipoModel;

    public function __construct()
    {
        $this->donacionModel = new Donacion();
        $this->equipoModel = new Equipo();
    }

    /**
     * Lista todas las donaciones
     */
    public function index()
    {
        $this->requireAuth();

        $donaciones = $this->donacionModel->getAllWithEquipos();

        $this->render('Views/donaciones/index.php', [
            'pageTitle' => 'Gestión de Donaciones',
            'donaciones' => $donaciones
        ]);
    }

    /**
     * Muestra el formulario para registrar una nueva donación
     */
    public function crear()
    {
        $this->requireAuth();

        // Obtener equipos disponibles para donar
        $equiposDisponibles = $this->equipoModel->query(
            "SELECT e.*, c.nombre as categoria 
             FROM equipos e 
             LEFT JOIN categorias c ON e.categoria_id = c.id 
             WHERE e.estado IN ('disponible', 'dañado', 'en_revision')
             ORDER BY e.nombre ASC"
        )->fetchAll();

        $this->render('Views/donaciones/crear.php', [
            'pageTitle' => 'Registrar Donación',
            'equipos' => $equiposDisponibles
        ]);
    }

    /**
     * Guarda una nueva donación
     */
    public function guardar()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('donaciones');
        }

        $data = [
            'equipo_id' => $_POST['equipo_id'] ?? null,
            'usuario_responsable_id' => currentUser()['id'],
            'fecha_donacion' => $_POST['fecha_donacion'] ?? date('Y-m-d'),
            'entidad_beneficiada' => $_POST['entidad_beneficiada'] ?? '', // OBLIGATORIO
            'tipo_entidad' => $_POST['tipo_entidad'] ?? null,
            'ruc_entidad' => $_POST['ruc_entidad'] ?? null,
            'contacto_nombre' => $_POST['contacto_nombre'] ?? null,
            'contacto_telefono' => $_POST['contacto_telefono'] ?? null,
            'contacto_email' => $_POST['contacto_email'] ?? null,
            'direccion_entidad' => $_POST['direccion_entidad'] ?? null,
            'valor_donacion' => $_POST['valor_donacion'] ?? null,
            'motivo_donacion' => $_POST['motivo_donacion'] ?? null,
            'condicion_equipo' => $_POST['condicion_equipo'] ?? 'bueno',
            'numero_acta' => $_POST['numero_acta'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? null
        ];

        // Validar datos obligatorios
        $errors = $this->validate($data, [
            'equipo_id' => ['required'],
            'entidad_beneficiada' => ['required', 'min:3'],
            'condicion_equipo' => ['required']
        ]);

        if (!empty($errors)) {
            setFlashMessage(
                'Error de Validación', 
                'La entidad beneficiada es obligatoria.',
                'error'
            );
            redirect('donaciones', 'crear');
        }

        // Manejar certificado de donación si se sube
        if (isset($_FILES['certificado_donacion']) && $_FILES['certificado_donacion']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/donaciones/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = pathinfo($_FILES['certificado_donacion']['name'], PATHINFO_EXTENSION);
            $fileName = 'certificado_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['certificado_donacion']['tmp_name'], $uploadPath)) {
                $data['certificado_donacion'] = 'uploads/donaciones/' . $fileName;
            }
        }

        // Registrar donación
        try {
            $this->donacionModel->registrarDonacion($data);
            setFlashMessage(
                'Donación Registrada', 
                'La donación del equipo ha sido registrada correctamente.',
                'success'
            );
            redirect('donaciones');
        } catch (\Exception $e) {
            setFlashMessage('Error', $e->getMessage(), 'error');
            redirect('donaciones', 'crear');
        }
    }

    /**
     * Muestra el detalle de una donación
     */
    public function ver()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de donación no válido.', 'error');
            redirect('donaciones');
        }

        $donacion = $this->donacionModel->getDetalleCompleto($id);
        if (!$donacion) {
            setFlashMessage('Error', 'Donación no encontrada.', 'error');
            redirect('donaciones');
        }

        $this->render('Views/donaciones/ver.php', [
            'pageTitle' => 'Detalle de Donación',
            'donacion' => $donacion
        ]);
    }

    /**
     * Muestra estadísticas de donaciones
     */
    public function estadisticas()
    {
        $this->requireAuth();

        $estadisticas = $this->donacionModel->getEstadisticas();
        $porTipo = $this->donacionModel->getPorTipoEntidad();
        $topEntidades = $this->donacionModel->getTopEntidades(10);

        $this->render('Views/donaciones/estadisticas.php', [
            'pageTitle' => 'Estadísticas de Donaciones',
            'estadisticas' => $estadisticas,
            'porTipo' => $porTipo,
            'topEntidades' => $topEntidades
        ]);
    }

    /**
     * Genera certificado de donación en PDF
     */
    public function generarCertificado()
    {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de donación no válido.', 'error');
            redirect('donaciones');
        }

        $donacion = $this->donacionModel->getDetalleCompleto($id);
        if (!$donacion) {
            setFlashMessage('Error', 'Donación no encontrada.', 'error');
            redirect('donaciones');
        }

        // Renderizar certificado (HTML simple que puede imprimirse)
        $this->render('Views/donaciones/certificado.php', [
            'pageTitle' => 'Certificado de Donación',
            'donacion' => $donacion
        ], false); // Sin layout
    }

    /**
     * Exporta reporte de donaciones a CSV
     */
    public function exportarCsv()
    {
        $this->requireAuth();

        $donaciones = $this->donacionModel->getAllWithEquipos();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_donaciones_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'ID',
            'Fecha Donación',
            'Equipo',
            'Número Serie',
            'Categoría',
            'Entidad Beneficiada',
            'Tipo Entidad',
            'Valor Donación',
            'Condición',
            'Responsable'
        ]);

        foreach ($donaciones as $donacion) {
            fputcsv($output, [
                $donacion['id'],
                $donacion['fecha_donacion'],
                $donacion['equipo_nombre'],
                $donacion['numero_serie'],
                $donacion['categoria'],
                $donacion['entidad_beneficiada'],
                $donacion['tipo_entidad'],
                $donacion['valor_donacion'],
                $donacion['condicion_equipo'],
                $donacion['responsable_nombre'] . ' ' . $donacion['responsable_apellido']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Busca donaciones por entidad
     */
    public function buscarPorEntidad()
    {
        $this->requireAuth();

        $entidad = $_GET['entidad'] ?? '';

        if (empty($entidad)) {
            $this->json(['success' => false, 'message' => 'Entidad no especificada'], 400);
        }

        try {
            $resultados = $this->donacionModel->getByEntidad($entidad);
            $this->json(['success' => true, 'data' => $resultados]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error en la búsqueda'], 500);
        }
    }
}
