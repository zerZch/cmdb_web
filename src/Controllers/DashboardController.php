<?php

namespace App\Controllers;

use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\Categoria;

/**
 * Controlador del Dashboard
 */
class DashboardController extends BaseController
{
    /**
     * Muestra el dashboard principal
     */
    public function index() {
        $this->requireAuth();

        $equipoModel = new Equipo();
        $usuarioModel = new Usuario();
        $categoriaModel = new Categoria();

        // Obtener estadísticas de equipos
        $estadisticas = $equipoModel->getEstadisticas();

        // Obtener equipos por categoría
        $equiposPorCategoria = $equipoModel->getPorCategoria();

        // Obtener totales adicionales
        $totalUsuarios = $usuarioModel->count();
        $totalCategorias = $categoriaModel->count();

        $this->render('Views/dashboard/index.php', [
            'pageTitle' => 'Dashboard',
            'estadisticas' => $estadisticas,
            'equiposPorCategoria' => $equiposPorCategoria,
            'totalUsuarios' => $totalUsuarios,
            'totalCategorias' => $totalCategorias
        ]);
    }
}
