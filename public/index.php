<?php
/**
 * CMDB - Sistema de Gestión de Inventario v2
 * Punto de entrada principal de la aplicación
 */

// ================================================================
// 1. CONFIGURACIÓN INICIAL
// ================================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../src/Core/helpers.php';
// Cargar archivos de configuración y autoload
require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../config/app.php';


// Convertir errores PHP en excepciones
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// ================================================================
// 2. CONFIGURAR SESIONES SEGURAS (antes de session_start)
// ================================================================
use App\Core\SecurityManager;
SecurityManager::configureSecureSession();

// Iniciar sesión DESPUÉS de configurar los parámetros de seguridad
session_start();

// ================================================================
// 3. IMPORTAR CONTROLADORES
// ================================================================
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UsuarioController;
use App\Controllers\CategoriaController;
use App\Controllers\EquipoController;
use App\Controllers\ColaboradorController;
use App\Controllers\AsignacionController;
use App\Controllers\NecesidadController;
use App\Controllers\BajaController;
use App\Controllers\DonacionController;
use App\Controllers\ReporteController;

// ================================================================
// 4. DEFINIR RUTAS DE LA APLICACIÓN
// ================================================================
$routes = [
    'login'         => AuthController::class,
    'logout'        => AuthController::class,
    'dashboard'     => DashboardController::class,
    'usuarios'      => UsuarioController::class,
    'categorias'    => CategoriaController::class,
    'equipos'       => EquipoController::class,  // ← NUEVA RUTA
    'colaboradores' => ColaboradorController::class,
    'asignaciones'  => AsignacionController::class,
    'necesidades'   => NecesidadController::class,
    'bajas'         => BajaController::class,
    'donaciones'    => DonacionController::class,
    'reportes'      => ReporteController::class,
];

// ================================================================
// 5. PROCESAR SOLICITUDES
// ================================================================
try {
    $route = $_GET['route'] ?? 'dashboard';
    $action = $_GET['action'] ?? 'index';
    $actionAliases = [
        'reporte-depreciacion' => 'reporteDepreciacion',
        'generate-qr'          => 'generateQR',
    ];

    if (isset($actionAliases[$action])) {
        $action = $actionAliases[$action];
    }
    // Validar que la ruta existe
    if (!isset($routes[$route])) {
        throw new Exception("Ruta no encontrada: {$route}", 404);
    }

    // Crear instancia del controlador
    $controllerClass = $routes[$route];
    $controller = new $controllerClass();

    // Validar que la acción existe
    if (!method_exists($controller, $action)) {
        throw new Exception("Acción no encontrada: {$action}", 404);
    }

    // Ejecutar la acción
    $controller->$action();

} catch (Throwable $e) {
    // Manejo de errores
    if ($e->getCode() === 404) {
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    } else {
        handleException($e);
    }
    exit;
}