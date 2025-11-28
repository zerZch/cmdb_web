<?php

namespace App\Controllers;

/**
 * Controlador base del que heredan todos los controladores
 */
class BaseController
{
    /**
     * Renderiza una vista con datos
     */
    protected function render($view, $data = [], $useLayout = true) {
        // Extraer datos como variables
        extract($data);

        // Buffer de salida
        ob_start();

        // Incluir la vista
        $viewPath = __DIR__ . '/../' . $view;
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new \Exception("Vista no encontrada: {$view}");
        }

        $content = ob_get_clean();

        // Si usa layout, envolver el contenido
        if ($useLayout) {
            require __DIR__ . '/../Views/layouts/main.php';
        } else {
            echo $content;
        }
    }

    /**
     * Requiere autenticación para acceder
     */
    protected function requireAuth() {
        if (!isAuthenticated()) {
            setFlashMessage('Acceso Denegado', 'Debe iniciar sesión para acceder.', 'warning');
            redirect('login');
        }
    }

    /**
     * Requiere un rol específico
     */
    protected function requireRole($role) {
        $this->requireAuth();

        if (!hasRole($role)) {
            http_response_code(403);
            echo "<h1>403 - Acceso Denegado</h1>";
            echo "<p>No tiene permisos para acceder a esta sección.</p>";
            exit;
        }
    }

    /**
     * Devuelve JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Valida datos del formulario
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = "El campo {$field} es obligatorio.";
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "El campo {$field} debe ser un email válido.";
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "El campo {$field} debe tener al menos {$min} caracteres.";
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "El campo {$field} no debe exceder {$max} caracteres.";
                    }
                }
            }
        }

        return $errors;
    }
}
