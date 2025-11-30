<?php

namespace App\Controllers;

use App\Models\Equipo;
use App\Models\Colaborador;
use App\Models\HistorialMovimiento;
use App\Models\Baja;
use App\Models\Donacion;

/**
 * Controlador de Reportes
 * Integrante 3 - Reportes de Inventario e Historial
 */
class ReporteController extends BaseController
{
    private $equipoModel;
    private $colaboradorModel;
    private $historialModel;
    private $bajaModel;
    private $donacionModel;

    public function __construct()
    {
        $this->equipoModel = new Equipo();
        $this->colaboradorModel = new Colaborador();
        $this->historialModel = new HistorialMovimiento();
        $this->bajaModel = new Baja();
        $this->donacionModel = new Donacion();
    }

    /**
     * Dashboard de reportes
     */
    public function index()
    {
        $this->requireAuth();

        $this->render('Views/reportes/index.php', [
            'pageTitle' => 'Reportes del Sistema'
        ]);
    }

    /**
     * Reporte de Inventario Completo
     * Requisito de Rúbrica: Implementar Reporte de Inventario
     */
    public function inventario()
    {
        $this->requireAuth();

        // Obtener datos de la vista SQL creada
        $inventario = $this->equipoModel->query(
            "SELECT * FROM v_inventario_completo ORDER BY id DESC"
        )->fetchAll();

        $estadisticas = $this->equipoModel->getEstadisticas();

        $this->render('Views/reportes/inventario.php', [
            'pageTitle' => 'Reporte de Inventario',
            'inventario' => $inventario,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Vista de Historial de un Equipo Específico
     * Requisito de Rúbrica: Implementar Vista de Historial de un Equipo (trazabilidad completa)
     */
    public function historialEquipo()
    {
        $this->requireAuth();

        $equipoId = $_GET['equipo_id'] ?? null;

        if (!$equipoId) {
            // Mostrar selector de equipos
            $equipos = $this->equipoModel->all();
            
            $this->render('Views/reportes/seleccionar_equipo.php', [
                'pageTitle' => 'Historial de Equipos',
                'equipos' => $equipos
            ]);
            return;
        }

        // Obtener equipo
        $equipo = $this->equipoModel->find($equipoId);
        if (!$equipo) {
            setFlashMessage('Error', 'Equipo no encontrado.', 'error');
            redirect('reportes', 'historialEquipo');
        }

        // Obtener historial completo con timeline visual
        $historial = $this->historialModel->getTimelineEquipo($equipoId);
        $ultimoMovimiento = $this->historialModel->getUltimoMovimiento($equipoId);

        $this->render('Views/reportes/historial_equipo.php', [
            'pageTitle' => 'Historial del Equipo',
            'equipo' => $equipo,
            'historial' => $historial,
            'ultimoMovimiento' => $ultimoMovimiento
        ]);
    }

    /**
     * Reporte de Equipos por Colaborador
     */
    public function equiposPorColaborador()
    {
        $this->requireAuth();

        $reporte = $this->colaboradorModel->query(
            "SELECT * FROM v_equipos_por_colaborador ORDER BY total_equipos_asignados DESC"
        )->fetchAll();

        $this->render('Views/reportes/equipos_colaborador.php', [
            'pageTitle' => 'Equipos por Colaborador',
            'reporte' => $reporte
        ]);
    }

    /**
     * Reporte de Movimientos Recientes
     */
    public function movimientosRecientes()
    {
        $this->requireAuth();

        $periodo = $_GET['periodo'] ?? 'semana';

        switch ($periodo) {
            case 'hoy':
                $movimientos = $this->historialModel->getMovimientosHoy();
                $titulo = 'Movimientos de Hoy';
                break;
            case 'semana':
                $movimientos = $this->historialModel->getMovimientosSemana();
                $titulo = 'Movimientos de esta Semana';
                break;
            case 'mes':
                $movimientos = $this->historialModel->getMovimientosMes();
                $titulo = 'Movimientos de este Mes';
                break;
            default:
                $movimientos = $this->historialModel->getRecientes(100);
                $titulo = 'Últimos 100 Movimientos';
        }

        $this->render('Views/reportes/movimientos_recientes.php', [
            'pageTitle' => $titulo,
            'movimientos' => $movimientos,
            'periodo' => $periodo
        ]);
    }

    /**
     * Reporte Consolidado (resumen general)
     */
    public function consolidado()
    {
        $this->requireAuth();

        $estadisticasEquipos = $this->equipoModel->getEstadisticas();
        $estadisticasColaboradores = $this->colaboradorModel->getEstadisticas();
        $estadisticasHistorial = $this->historialModel->getEstadisticas();
        $estadisticasBajas = $this->bajaModel->getEstadisticas();
        $estadisticasDonaciones = $this->donacionModel->getEstadisticas();

        $equiposMasActivos = $this->historialModel->getEquiposMasActivos(10);
        $colaboradoresConEquipos = $this->colaboradorModel->getTopConEquipos(10);

        $this->render('Views/reportes/consolidado.php', [
            'pageTitle' => 'Reporte Consolidado',
            'equipos' => $estadisticasEquipos,
            'colaboradores' => $estadisticasColaboradores,
            'historial' => $estadisticasHistorial,
            'bajas' => $estadisticasBajas,
            'donaciones' => $estadisticasDonaciones,
            'equiposMasActivos' => $equiposMasActivos,
            'colaboradoresConEquipos' => $colaboradoresConEquipos
        ]);
    }

    /**
     * Exportar Inventario a CSV
     */
    public function exportarInventarioCsv()
    {
        $this->requireAuth();

        $inventario = $this->equipoModel->query(
            "SELECT * FROM v_inventario_completo"
        )->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="inventario_completo_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'ID',
            'Nombre',
            'Número Serie',
            'Marca',
            'Modelo',
            'Estado',
            'Condición',
            'Categoría',
            'Ubicación',
            'Fecha Adquisición',
            'Valor',
            'Asignado A',
            'Departamento'
        ]);

        foreach ($inventario as $item) {
            fputcsv($output, [
                $item['id'],
                $item['nombre'],
                $item['numero_serie'],
                $item['marca'],
                $item['modelo'],
                $item['estado'],
                $item['condicion'],
                $item['categoria'],
                $item['ubicacion'],
                $item['fecha_adquisicion'],
                $item['valor_adquisicion'],
                $item['asignado_a'],
                $item['departamento_asignado']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Exportar Historial de Equipo a PDF (versión simple HTML imprimible)
     */
    public function exportarHistorialPdf()
    {
        $this->requireAuth();

        $equipoId = $_GET['equipo_id'] ?? null;

        if (!$equipoId) {
            setFlashMessage('Error', 'Debe especificar un equipo.', 'error');
            redirect('reportes', 'historialEquipo');
        }

        $equipo = $this->equipoModel->find($equipoId);
        $historial = $this->historialModel->getHistorialEquipo($equipoId);

        // Renderizar vista imprimible
        $this->render('Views/reportes/historial_pdf.php', [
            'pageTitle' => 'Historial del Equipo',
            'equipo' => $equipo,
            'historial' => $historial
        ], false);
    }

    /**
     * Buscar en todo el sistema
     */
    public function buscarGlobal()
    {
        $this->requireAuth();

        $termino = $_GET['q'] ?? '';

        if (empty($termino)) {
            redirect('reportes');
        }

        // Buscar en múltiples tablas
        $equipos = $this->equipoModel->query(
            "SELECT *, 'equipo' as tipo FROM equipos 
             WHERE nombre LIKE ? OR numero_serie LIKE ? OR marca LIKE ?",
            ["%{$termino}%", "%{$termino}%", "%{$termino}%"]
        )->fetchAll();

        $colaboradores = $this->colaboradorModel->buscar($termino);
        foreach ($colaboradores as &$col) {
            $col['tipo'] = 'colaborador';
        }

        $bajas = $this->bajaModel->query(
            "SELECT b.*, e.nombre as equipo_nombre, 'baja' as tipo 
             FROM bajas_equipos b 
             INNER JOIN equipos e ON b.equipo_id = e.id 
             WHERE e.nombre LIKE ? OR b.numero_acta LIKE ?",
            ["%{$termino}%", "%{$termino}%"]
        )->fetchAll();

        $donaciones = $this->donacionModel->buscar($termino);
        foreach ($donaciones as &$don) {
            $don['tipo'] = 'donacion';
        }

        $resultados = array_merge($equipos, $colaboradores, $bajas, $donaciones);

        $this->render('Views/reportes/busqueda_global.php', [
            'pageTitle' => 'Resultados de Búsqueda',
            'termino' => $termino,
            'resultados' => $resultados,
            'totalResultados' => count($resultados)
        ]);
    }

    /**
     * Dashboard de actividad (timeline general)
     */
    public function actividadReciente()
    {
        $this->requireAuth();

        $actividad = $this->historialModel->getRecientes(50);

        $this->render('Views/reportes/actividad_reciente.php', [
            'pageTitle' => 'Actividad Reciente del Sistema',
            'actividad' => $actividad
        ]);
    }
}
