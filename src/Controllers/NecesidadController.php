<?php

namespace App\Controllers;

class NecesidadController extends BaseController
{
    public function index()
    {
        $this->requireAuth();

        $this->render('Views/necesidades/index.php', [
            'pageTitle' => 'Solicitudes / Necesidades'
        ]);
    }

    // Aquí después puedes agregar:
    // public function crear() {}
    // public function guardar() {}
    // etc.
}
