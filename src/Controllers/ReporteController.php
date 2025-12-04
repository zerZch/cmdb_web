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

    /**
     * Reporte de Inventario por Categoría
     */
public function inventarioPorCategoria()
{
    $this->requireAuth();

    // Verificar si se solicita exportar
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        return $this->exportarInventarioCategoriaCSV();
    }

    // Obtener datos del reporte
    $datosReporte = $this->obtenerDatosInventarioPorCategoria();

    $this->render('Views/reportes/inventario_categoria.php', [
        'pageTitle' => 'Reporte de Inventario por Categoría',
        'datosReporte' => $datosReporte
    ]);
}

/**
 * Obtiene los datos del inventario agrupados por categoría
 */

private function obtenerDatosInventarioPorCategoria()
{
    try {
        $query = "
            SELECT 
                c.id as categoria_id,
                c.nombre as categoria,
                c.descripcion as categoria_descripcion,
                COUNT(e.id) as total_equipos,
                SUM(CASE WHEN e.estado = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                SUM(CASE WHEN e.estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                SUM(CASE WHEN e.estado = 'mantenimiento' THEN 1 ELSE 0 END) as en_revision,
                SUM(CASE WHEN e.estado = 'dañado' THEN 1 ELSE 0 END) as descarte,
                SUM(CASE WHEN e.costo_adquisicion IS NOT NULL THEN e.costo_adquisicion ELSE 0 END) as valor_total
            FROM categorias c
            LEFT JOIN equipos e ON c.id = e.categoria_id
            WHERE c.estado = 'activa'
            GROUP BY c.id, c.nombre, c.descripcion
            HAVING total_equipos > 0
            ORDER BY total_equipos DESC, c.nombre ASC
        ";

        $stmt = $this->equipoModel->getConnection()->query($query);
        $datos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $totales = [
            'total_equipos' => 0,
            'disponibles' => 0,
            'asignados' => 0,
            'en_revision' => 0,
            'descarte' => 0,
            'valor_total' => 0
        ];

        foreach ($datos as $row) {
            $totales['total_equipos'] += $row['total_equipos'];
            $totales['disponibles'] += $row['disponibles'];
            $totales['asignados'] += $row['asignados'];
            $totales['en_revision'] += $row['en_revision'];
            $totales['descarte'] += $row['descarte'];
            $totales['valor_total'] += $row['valor_total'];
        }

        return [
            'categorias' => $datos,
            'totales' => $totales
        ];

    } catch (\PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return [
            'categorias' => [],
            'totales' => [
                'total_equipos' => 0,
                'disponibles' => 0,
                'asignados' => 0,
                'en_revision' => 0,
                'descarte' => 0,
                'valor_total' => 0
            ]
        ];
    }
}

/**
 * Exporta el inventario por categoría a CSV
 */
private function exportarInventarioCategoriaCSV()
{
    // Obtener datos
    $datosReporte = $this->obtenerDatosInventarioPorCategoria();
    
    // Configurar headers
    $fecha = date('Y-m-d_H-i-s');
    $filename = "inventario_por_categoria_{$fecha}.csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Crear output
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
    
    // Título
    fputcsv($output, ['REPORTE DE INVENTARIO POR CATEGORÍA']);
    fputcsv($output, ['Generado el: ' . date('d/m/Y H:i:s')]);
    fputcsv($output, []); // Línea vacía
    
    // Resumen
    fputcsv($output, ['RESUMEN GENERAL']);
    fputcsv($output, ['Total de Equipos:', $datosReporte['totales']['total_equipos']]);
    fputcsv($output, ['Equipos Disponibles:', $datosReporte['totales']['disponibles']]);
    fputcsv($output, ['Equipos Asignados:', $datosReporte['totales']['asignados']]);
    fputcsv($output, ['Equipos en Revisión:', $datosReporte['totales']['en_revision']]);
    fputcsv($output, ['Equipos en Descarte:', $datosReporte['totales']['descarte']]);
    fputcsv($output, ['Valor Total:', '$' . number_format($datosReporte['totales']['valor_total'], 2)]);
    fputcsv($output, []);
    
    // Encabezados
    fputcsv($output, [
        '#',
        'Categoría',
        'Descripción',
        'Total',
        'Disponibles',
        'Asignados',
        'En Revisión',
        'Descarte',
        'Valor Total'
    ]);
    
    // Datos
    $contador = 1;
    foreach ($datosReporte['categorias'] as $cat) {
        fputcsv($output, [
            $contador++,
            $cat['categoria'],
            $cat['categoria_descripcion'] ?? '',
            $cat['total_equipos'],
            $cat['disponibles'],
            $cat['asignados'],
            $cat['en_revision'],
            $cat['descarte'],
            '$' . number_format($cat['valor_total'], 2)
        ]);
    }
    
    // Totales
    fputcsv($output, []);
    fputcsv($output, [
        '',
        'TOTALES:',
        '',
        $datosReporte['totales']['total_equipos'],
        $datosReporte['totales']['disponibles'],
        $datosReporte['totales']['asignados'],
        $datosReporte['totales']['en_revision'],
        $datosReporte['totales']['descarte'],
        '$' . number_format($datosReporte['totales']['valor_total'], 2)
    ]);
    
    fclose($output);
    exit;
}

public function detalleCategoria()
{
    $this->requireAuth();

    $categoriaId = $_GET['categoria_id'] ?? null;

    if (!$categoriaId) {
        setFlashMessage('Error', 'Categoría no especificada.', 'error');
        redirect('reportes', 'inventarioPorCategoria');
        return;
    }

    try {
        $db = $this->equipoModel->getConnection();
        
        $queryCategoria = "SELECT * FROM categorias WHERE id = ? AND activo = 1 LIMIT 1";
        $stmtCategoria = $db->prepare($queryCategoria);
        $stmtCategoria->execute([$categoriaId]);
        $categoria = $stmtCategoria->fetch(\PDO::FETCH_ASSOC);

        if (!$categoria) {
            setFlashMessage('Error', 'Categoría no encontrada.', 'error');
            redirect('reportes', 'inventarioPorCategoria');
            return;
        }

        $queryEquipos = "
            SELECT 
                e.*,
                CONCAT(col.nombre, ' ', col.apellido) as colaborador_nombre,
                col.ubicacion as colaborador_ubicacion
            FROM equipos e
            LEFT JOIN asignaciones a ON e.id = a.equipo_id AND a.fecha_devolucion IS NULL
            LEFT JOIN colaboradores col ON a.colaborador_id = col.id
            WHERE e.categoria_id = ? AND e.activo = 1
            ORDER BY e.estado, e.nombre
        ";

        $stmtEquipos = $db->prepare($queryEquipos);
        $stmtEquipos->execute([$categoriaId]);
        $equipos = $stmtEquipos->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('Views/reportes/detalle_categoria.php', [
            'pageTitle' => 'Equipos de ' . $categoria['nombre'],
            'categoria' => $categoria,
            'equipos' => $equipos
        ]);

    } catch (\PDOException $e) {
        error_log("Error: " . $e->getMessage());
        setFlashMessage('Error', 'Error al obtener datos.', 'error');
        redirect('reportes', 'inventarioPorCategoria');
    }
}
}
