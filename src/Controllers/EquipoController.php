<?php
// src/Controllers/EquipoController.php

namespace App\Controllers;

use App\Models\Equipo;
use App\Models\Categoria;
use DateTime;

class EquipoController extends BaseController
{
    private $equipoModel;
    private $categoriaModel;
    
    public function __construct()
    {
        $this->equipoModel = new Equipo();
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Listar todos los equipos
     */
    public function index()
    {
        $this->requireAuth();
        
        $equipos = array_filter(
            $this->equipoModel->getAllWithCategoria(),
            function($equipo) {
                return $equipo['estado'] !== 'dado_de_baja';
            }
        );
        $estadisticas = $this->equipoModel->getEstadisticas();
        
        $this->render('Views/equipos/index.php', [
            'pageTitle' => 'Gestión de Equipos',
            'equipos' => $equipos,
            'estadisticas' => $estadisticas
        ]);
    }
    
    /**
     * Mostrar formulario de crear equipo
     */
    public function create()
    {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $categorias = $this->categoriaModel->getAllActivas();
        
        $this->render('Views/equipos/create.php', [
            'pageTitle' => 'Nuevo Equipo',
            'categorias' => $categorias
        ]);
    }
    
    /**
     * Guardar nuevo equipo
     */
    public function store()
    {
        $this->requireAuth();
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('equipos');
            return;
        }
        
        // Validar datos básicos
        $errors = $this->validate($_POST, [
            'categoria_id' => ['required'],
            'marca' => ['required', 'min:2', 'max:100'],
            'modelo' => ['required', 'min:2', 'max:100'],
            'numero_serie' => ['required', 'min:3', 'max:100'],
            'fecha_adquisicion' => ['required'],
            'costo_adquisicion' => ['required']
        ]);
        
        if (!empty($errors)) {
            setFlashMessage('Error de Validación', 'Por favor corrige los errores en el formulario', 'error');
            redirect('equipos&action=create');
            return;
        }
        
        // Verificar que número de serie no exista
        if ($this->equipoModel->serieExists($_POST['numero_serie'])) {
            setFlashMessage('Error', 'El número de serie ya existe en el sistema', 'error');
            redirect('equipos&action=create');
            return;
        }
        
        // Procesar foto si existe
        $fotoPath = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->uploadFoto($_FILES['foto']);
            if (!$fotoPath) {
                setFlashMessage('Error', 'Error al subir la foto del equipo', 'error');
                redirect('equipos&action=create');
                return;
            }
        }
        
        // Preparar datos
        $data = [
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'marca' => trim($_POST['marca']),
            'modelo' => trim($_POST['modelo']),
            'numero_serie' => trim($_POST['numero_serie']),
            'fecha_adquisicion' => $_POST['fecha_adquisicion'],
            'costo_adquisicion' => (float) $_POST['costo_adquisicion'],
            'vida_util_anos' => !empty($_POST['vida_util_anos']) ? (int) $_POST['vida_util_anos'] : 5,
            'valor_residual' => !empty($_POST['valor_residual']) ? (float) $_POST['valor_residual'] : 0,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'ubicacion' => trim($_POST['ubicacion'] ?? ''),
            'estado' => $_POST['estado'] ?? 'disponible',
            'foto' => $fotoPath
        ];
        
        // Crear equipo
        $equipoId = $this->equipoModel->create($data);
        
        if ($equipoId) {
            setFlashMessage('¡Éxito!', 'Equipo creado exitosamente', 'success');
            redirect('equipos&action=show&id=' . $equipoId);
        } else {
            setFlashMessage('Error', 'Error al crear el equipo', 'error');
            redirect('equipos&action=create');
        }
    }
    
    /**
     * Ver detalles de un equipo
     */
    public function show()
    {
        $this->requireAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de equipo no válido', 'error');
            redirect('equipos');
            return;
        }
        
        $equipo = $this->equipoModel->getByIdWithCategoria($id);
        
        if (!$equipo) {
            setFlashMessage('Error', 'Equipo no encontrado', 'error');
            redirect('equipos');
            return;
        }
        
        // Calcular depreciación
        $depreciacion = $this->equipoModel->calcularDepreciacion($equipo);
        
        $this->render('Views/equipos/show.php', [
            'pageTitle' => 'Detalles del Equipo',
            'equipo' => $equipo,
            'depreciacion' => $depreciacion
        ]);
    }
    
    /**
     * Mostrar formulario de editar equipo
     */
    public function edit()
    {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de equipo no válido', 'error');
            redirect('equipos');
            return;
        }
        
        $equipo = $this->equipoModel->getById($id);
        
        if (!$equipo) {
            setFlashMessage('Error', 'Equipo no encontrado', 'error');
            redirect('equipos');
            return;
        }
        
        $categorias = $this->categoriaModel->getAll();
        
        $this->render('Views/equipos/edit.php', [
            'pageTitle' => 'Editar Equipo',
            'equipo' => $equipo,
            'categorias' => $categorias
        ]);
    }
    
    /**
     * Actualizar equipo
     */
    public function update()
    {
        $this->requireAuth();
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('equipos');
            return;
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            setFlashMessage('Error', 'ID de equipo no válido', 'error');
            redirect('equipos');
            return;
        }
        
        $equipo = $this->equipoModel->getById($id);
        if (!$equipo) {
            setFlashMessage('Error', 'Equipo no encontrado', 'error');
            redirect('equipos');
            return;
        }
        
        // Validar datos
        $errors = $this->validate($_POST, [
            'categoria_id' => ['required'],
            'marca' => ['required', 'min:2', 'max:100'],
            'modelo' => ['required', 'min:2', 'max:100'],
            'numero_serie' => ['required', 'min:3', 'max:100'],
            'fecha_adquisicion' => ['required'],
            'costo_adquisicion' => ['required']
        ]);
        
        if (!empty($errors)) {
            setFlashMessage('Error de Validación', 'Por favor corrige los errores', 'error');
            redirect('equipos&action=edit&id=' . $id);
            return;
        }
        
        // Verificar número de serie
        if ($this->equipoModel->serieExists($_POST['numero_serie'], $id)) {
            setFlashMessage('Error', 'El número de serie ya existe', 'error');
            redirect('equipos&action=edit&id=' . $id);
            return;
        }
        
        // Procesar foto nueva si existe
        $fotoPath = $equipo['foto'];
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $newFoto = $this->uploadFoto($_FILES['foto']);
            if ($newFoto) {
                // Eliminar foto anterior
                if ($fotoPath && file_exists(__DIR__ . '/../../public' . $fotoPath)) {
                    unlink(__DIR__ . '/../../public' . $fotoPath);
                }
                $fotoPath = $newFoto;
            }
        }
        
        // Preparar datos
        $data = [
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'marca' => trim($_POST['marca']),
            'modelo' => trim($_POST['modelo']),
            'numero_serie' => trim($_POST['numero_serie']),
            'fecha_adquisicion' => $_POST['fecha_adquisicion'],
            'costo_adquisicion' => (float) $_POST['costo_adquisicion'],
            'vida_util_anos' => !empty($_POST['vida_util_anos']) ? (int) $_POST['vida_util_anos'] : 5,
            'valor_residual' => !empty($_POST['valor_residual']) ? (float) $_POST['valor_residual'] : 0,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'ubicacion' => trim($_POST['ubicacion'] ?? ''),
            'estado' => $_POST['estado'],
            'foto' => $fotoPath
        ];
        
        if ($this->equipoModel->update($id, $data)) {
            setFlashMessage('¡Éxito!', 'Equipo actualizado exitosamente', 'success');
            redirect('equipos&action=show&id=' . $id);
        } else {
            setFlashMessage('Error', 'Error al actualizar el equipo', 'error');
            redirect('equipos&action=edit&id=' . $id);
        }
    }
    
    /**
     * Eliminar equipo
     */
    public function delete()
{
    error_log("DELETE METHOD CALLED"); // Debug
    
    $this->requireAuth();
    $this->requireRole('admin');
    
    $id = $_POST['id'] ?? $_GET['id'] ?? null;
    
    error_log("ID recibido: " . $id); // Debug
    
    if (!$id) {
        error_log("ID es nulo"); // Debug
        setFlashMessage('Error', 'ID de equipo no válido', 'error');
        redirect('equipos');
        return;
    }
    
    $equipo = $this->equipoModel->getById($id);
    
    error_log("Equipo encontrado: " . print_r($equipo, true)); // Debug
    
    if (!$equipo) {
        setFlashMessage('Error', 'Equipo no encontrado', 'error');
        redirect('equipos');
        return;
    }
    
    if ($equipo['estado'] === 'asignado') {
        setFlashMessage('Error', 'No se puede eliminar un equipo asignado', 'error');
        redirect('equipos');
        return;
    }
    
    $result = $this->equipoModel->delete($id);
    
    error_log("Resultado delete: " . ($result ? 'true' : 'false')); // Debug
    
    if ($result) {
        setFlashMessage('¡Éxito!', 'Equipo eliminado exitosamente', 'success');
    } else {
        setFlashMessage('Error', 'Error al eliminar el equipo', 'error');
    }
    
    redirect('equipos');
}
    
    /**
     * Generar código QR - Optimizado para Windows
     */
    public function generateQR() {
    header('Content-Type: application/json');

    try {
        // 1. Validar ID
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id || $id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de equipo inválido'
            ]);
            return;
        }

        // 2. Obtener datos del equipo desde el MODELO
        $equipo = $this->equipoModel->getByIdWithCategoria($id);

        if (!$equipo) {
            echo json_encode([
                'success' => false,
                'message' => 'Equipo no encontrado'
            ]);
            return;
        }
        $qrUrl = "http://localhost/cmdb_web/public/index.php?route=equipos&action=show&id=" . $id;
        // 3. Crear directorio QR si no existe
        $uploadDir = __DIR__ . '/../../public/uploads/qr/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 4. Datos codificados del QR
        $qrData = json_encode([
            'id' => $equipo['id'],
            'marca' => $equipo['marca'],
            'modelo' => $equipo['modelo'],
            'numero_serie' => $equipo['numero_serie'],
            'categoria' => $equipo['categoria_nombre'], // ✔ AHORA SÍ
        ], JSON_UNESCAPED_UNICODE);


        // 5. Crear archivo QR
        $fileName = "qr_equipo_{$id}.png";
        $filePath = $uploadDir . $fileName;
        $relativePath = "/cmdb_web/public/uploads/qr/{$fileName}";

        // Generar QR según tu librería instalada
        if (class_exists('QRcode')) {
            \QRcode::png($qrData, $filePath, QR_ECLEVEL_L, 10, 2);
        } 
        else if (class_exists('Endroid\QrCode\QrCode')) {
            $qr = new \Endroid\QrCode\QrCode($qrData);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qr);
            $result->saveToFile($filePath);
        } 
        else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay librería QR instalada'
            ]);
            return;
        }

        // 6. Validar archivo QR generado
        if (!file_exists($filePath)) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar el código QR'
            ]);
            return;
        }

        // 7. Actualizar base de datos con la ruta del QR
        $this->equipoModel->updateQrCode($id, $relativePath);

        // 8. Respuesta final
        echo json_encode([
            'success' => true,
            'message' => 'Código QR generado exitosamente',
            'qr_url' => $relativePath,
            'equipo' => [
                'id' => $equipo['id'],
                'marca' => $equipo['marca'],
                'modelo' => $equipo['modelo'],
                'numero_serie' => $equipo['numero_serie']
            ]
        ]);

    } catch (\Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ]);
    }
}

/**
 * Mostrar reporte de depreciación
 */
public function reporteDepreciacion()
{
    $this->requireAuth();
    
    $filtros = [
        'categoria_id' => $_GET['categoria_id'] ?? null,
        'estado' => $_GET['estado'] ?? null,
        'ano' => $_GET['ano'] ?? null
    ];
    
    $equipos = $this->equipoModel->getReporteDepreciacion($filtros);
    $categorias = $this->categoriaModel->getAll();

    // ============================
    // 🔥 CÁLCULO DE DEPRECIACIÓN
    // ============================
    foreach ($equipos as &$eq) {

        // -----------------------------
        // Validaciones de seguridad
        // -----------------------------
        $costo = isset($eq['costo_adquisicion']) ? floatval($eq['costo_adquisicion']) : 0;
        $vida  = isset($eq['vida_util_anos']) ? intval($eq['vida_util_anos']) : 0;
        $fechaAdq = $eq['fecha_adquisicion'] ?? null;

        // Validar fecha
        if ($fechaAdq && $fechaAdq !== "0000-00-00") {
            try {
                $fechaObj = new \DateTime($fechaAdq);
            } catch (\Exception $e) {
                // Si falla la fecha -> asignar 0 años de uso
                $fechaObj = null;
            }
        } else {
            $fechaObj = null;
        }

        $hoy = new \DateTime();

        // =====================================
        // 🔹 Calcular meses de uso
        // =====================================
        if ($fechaObj) {
            $diff = $fechaObj->diff($hoy);
            $meses = ($diff->y * 12) + $diff->m;
        } else {
            $meses = 0;
        }

        // =====================================
        // 🔹 Depreciación mensual
        // =====================================
        $depMensual = ($vida > 0) ? $costo / ($vida * 12) : 0;

        // =====================================
        // 🔹 Depreciación acumulada
        // =====================================
        $depAcum = $depMensual * $meses;
        if ($depAcum > $costo) $depAcum = $costo;

        // =====================================
        // 🔹 Valor en libros
        // =====================================
        $valorLibro = $costo - $depAcum;
        if ($valorLibro < 0) $valorLibro = 0;

        // =====================================
        // 🔹 Porcentaje depreciado
        // =====================================
        $porcentaje = ($costo > 0) ? ($depAcum / $costo) * 100 : 0;

        // Guardar valores listos para la vista
        $eq['depreciacion_mensual']   = round($depMensual, 2);
        $eq['depreciacion_acumulada'] = round($depAcum, 2);
        $eq['valor_libro']            = round($valorLibro, 2);
        $eq['porcentaje_depreciado']  = round($porcentaje, 2);
    }
    unset($eq);

    // Render vista
    $this->render('Views/equipos/reporte-depreciacion.php', [
        'pageTitle' => 'Reporte de Depreciación',
        'equipos' => $equipos,
        'categorias' => $categorias,
        'filtros' => $filtros
    ]);
}

    
    // ============================================================
    // FUNCIONES DE IMPORTACIÓN MASIVA - OPTIMIZADAS
    // ============================================================
    
    /**
     * Mostrar formulario de importación masiva
     */
    public function importar()
    {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $categorias = $this->categoriaModel->getAllActivas();
        
        $this->render('Views/equipos/importar.php', [
            'pageTitle' => 'Importar Equipos Masivamente',
            'categorias' => $categorias
        ]);
    }
    
    /**
     * Descargar plantilla Excel MEJORADA
     */
/**
 * Descargar plantilla Excel OPTIMIZADA - Sin instrucciones en encabezados
 */
    public function descargarPlantilla()
    {
        $this->requireAuth();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantilla_equipos_' . date('Y-m-d') . '.csv');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 (mejor compatibilidad con Excel)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // ✅ ENCABEZADOS LIMPIOS (sin instrucciones)
        fputcsv($output, [
            'categoria_id',
            'marca',
            'modelo',
            'numero_serie',
            'fecha_adquisicion',
            'costo_adquisicion',
            'vida_util_anos',
            'valor_residual',
            'ubicacion',
            'descripcion',
            'estado'
        ]);
        
        // Obtener categorías reales para ejemplos
        $categorias = $this->categoriaModel->getAllActivas();
        $primeraCategoria = !empty($categorias) ? $categorias[0]['id'] : 1;
        
        // ✅ EJEMPLOS CON DATOS REALES
        fputcsv($output, [
            $primeraCategoria,
            'Dell',
            'Latitude 7420',
            'DELL-' . date('Y') . '-001',
            date('Y-m-d'),
            '1200.00',
            '5',
            '100.00',
            'Oficina 301',
            'Laptop empresarial i7 16GB RAM',
            'disponible'
        ]);
        
        fputcsv($output, [
            $primeraCategoria,
            'HP',
            'EliteBook 840',
            'HP-' . date('Y') . '-002',
            date('Y-m-d'),
            '1100.00',
            '5',
            '90.00',
            'Oficina 302',
            'Laptop empresarial i5 8GB RAM',
            'disponible'
        ]);
        
        // ✅ LÍNEA VACÍA
        fputcsv($output, []);
        
        // ✅ SECCIÓN DE INSTRUCCIONES (fuera de la tabla de datos)
        fputcsv($output, ['=== INSTRUCCIONES ===']);
        fputcsv($output, ['']);
        fputcsv($output, ['FORMATOS REQUERIDOS:']);
        fputcsv($output, ['- categoria_id: Número entero (ver lista abajo)']);
        fputcsv($output, ['- marca: Texto (Ejemplo: Dell, HP, Lenovo)']);
        fputcsv($output, ['- modelo: Texto (Ejemplo: Latitude 7420)']);
        fputcsv($output, ['- numero_serie: Texto único (Ejemplo: DELL-2025-001)']);
        fputcsv($output, ['- fecha_adquisicion: Formato YYYY-MM-DD (Ejemplo: 2025-12-03)']);
        fputcsv($output, ['- costo_adquisicion: Número decimal (Ejemplo: 1200.00)']);
        fputcsv($output, ['- vida_util_anos: Número entero (Ejemplo: 5)']);
        fputcsv($output, ['- valor_residual: Número decimal (Ejemplo: 100.00)']);
        fputcsv($output, ['- ubicacion: Texto (Ejemplo: Oficina 301)']);
        fputcsv($output, ['- descripcion: Texto (Ejemplo: Laptop empresarial)']);
        fputcsv($output, ['- estado: disponible, asignado, mantenimiento o dado_de_baja']);
        fputcsv($output, []);
        
        // ✅ LISTA DE CATEGORÍAS DISPONIBLES
        fputcsv($output, ['=== CATEGORÍAS DISPONIBLES ===']);
        fputcsv($output, ['ID', 'Nombre']);
        
        foreach ($categorias as $cat) {
            fputcsv($output, [$cat['id'], $cat['nombre']]);
        }
        
        fputcsv($output, []);
        fputcsv($output, ['NOTA: Elimina las filas de ejemplo antes de subir tu archivo']);
        fputcsv($output, ['NOTA: Las instrucciones y categorías son solo de referencia']);
        
        fclose($output);
        exit;
    }
        
    /**
 * PROCESAR IMPORTACIÓN - VERSIÓN OPTIMIZADA
 */
    public function procesarImportacion()
{
    $this->requireAuth();
    $this->requireRole('admin');
    
    error_log("=== DEBUG IMPORTACIÓN ===");
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("No es POST");
        setFlashMessage('Error', 'Método no válido', 'error');
        redirect('equipos&action=importar');
        return;
    }
    
    error_log("Es POST");
    
    if (!isset($_FILES['archivo'])) {
        error_log("No hay archivo");
        setFlashMessage('Error', 'No se recibió el archivo', 'error');
        redirect('equipos&action=importar');
        return;
    }
    
    error_log("Archivo recibido: " . $_FILES['archivo']['name']);
    error_log("Error code: " . $_FILES['archivo']['error']);
    
    if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        error_log("Error al subir: " . $_FILES['archivo']['error']);
        setFlashMessage('Error', 'Error al subir archivo: ' . $_FILES['archivo']['error'], 'error');
        redirect('equipos&action=importar');
        return;
    }
    
    error_log("Archivo OK, procesando...");
    
    // TEST: Solo contar filas sin procesar
    $archivo = $_FILES['archivo']['tmp_name'];
    $handle = fopen($archivo, 'r');
    
    if (!$handle) {
        error_log("No se pudo abrir el archivo");
        setFlashMessage('Error', 'No se pudo leer el archivo', 'error');
        redirect('equipos&action=importar');
        return;
    }
    
    $totalFilas = 0;
    while (($data = fgetcsv($handle)) !== false) {
        $totalFilas++;
    }
    fclose($handle);
    
    error_log("Total filas: " . $totalFilas);
    
    setFlashMessage('Test', "Archivo leído correctamente. Total filas: $totalFilas", 'success');
    redirect('equipos');
}
    
    // ============================================================
    // 🔧 FUNCIONES AUXILIARES PRIVADAS
    // ============================================================
    
    /**
     * Subir foto de equipo
     */
    private function uploadFoto($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/equipos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'equipo_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return '/uploads/equipos/' . $filename;
        }
        
        return false;
    }
}