<?php

/**
 * Funciones auxiliares globales
 */

/**
 * Maneja excepciones y muestra página de error
 */
function handleException($e) {
    http_response_code(500);

    // En producción, solo mostrar mensaje genérico
    if ($_ENV['APP_ENV'] ?? 'development' === 'production') {
        echo "<h1>Error 500</h1>";
        echo "<p>Ha ocurrido un error. Por favor, intente más tarde.</p>";
    } else {
        // En desarrollo, mostrar detalles
        echo "<h1>Error en la aplicación</h1>";
        echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}

/**
 * Redirecciona a una ruta específica
 */
function redirect($route, $action = 'index', $params = []) {
    $url = BASE_URL . "index.php?route={$route}&action={$action}";

    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }

    header("Location: {$url}");
    exit;
}

/**
 * Verifica si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Obtiene el usuario actual
 */
function currentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nombre' => $_SESSION['user_nombre'] ?? '',
        'apellido' => $_SESSION['user_apellido'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'rol' => $_SESSION['user_role'] ?? '',
    ];
}

/**
 * Escapa HTML para prevenir XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Establece un mensaje flash en la sesión
 */
function setFlashMessage($title, $text, $icon = 'info') {
    $_SESSION['flash_message'] = [
        'title' => $title,
        'text' => $text,
        'icon' => $icon
    ];
}

/**
 * Obtiene y limpia el mensaje flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Genera un token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
