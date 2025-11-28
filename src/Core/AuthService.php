<?php

namespace App\Core;

use App\Models\Usuario;

/**
 * Servicio de autenticación con validaciones de seguridad avanzadas
 */
class AuthService
{
    /**
     * Intenta autenticar un usuario
     *
     * @param string $email Email del usuario
     * @param string $password Contraseña sin encriptar
     * @return array ['success' => bool, 'user_id' => int|null, 'rol' => string|null, 'reason' => string|null]
     */
    public function attemptLogin(string $email, string $password): array
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findByEmail($email);

        // Verificar que el usuario existe
        if (!$usuario) {
            return [
                'success' => false,
                'user_id' => null,
                'rol' => null,
                'reason' => 'Usuario no encontrado'
            ];
        }

        // Verificar que el usuario está activo
        if ($usuario['estado'] !== 'activo') {
            return [
                'success' => false,
                'user_id' => $usuario['id'],
                'rol' => $usuario['rol'],
                'reason' => 'Cuenta inactiva o deshabilitada'
            ];
        }

        // Verificar la contraseña
        if (!password_verify($password, $usuario['password'])) {
            return [
                'success' => false,
                'user_id' => $usuario['id'],
                'rol' => $usuario['rol'],
                'reason' => 'Contraseña incorrecta'
            ];
        }

        // ✅ Autenticación exitosa - crear sesión
        $this->createSession($usuario);

        return [
            'success' => true,
            'user_id' => $usuario['id'],
            'rol' => $usuario['rol'],
            'reason' => null
        ];
    }

    /**
     * Crea la sesión del usuario con datos completos
     *
     * @param array $usuario Datos del usuario
     */
    private function createSession(array $usuario): void
    {
        // Almacenar datos del usuario en sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_apellido'] = $usuario['apellido'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_role'] = $usuario['rol'];
        $_SESSION['user_foto'] = $usuario['foto_perfil'] ?? null;

        // Datos de seguridad
        $_SESSION['user_ip'] = SecurityManager::getClientIp();
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        $_SESSION['fingerprint'] = SecurityManager::generateDeviceFingerprint();

        // Regenerar ID de sesión por seguridad (prevenir session fixation)
        session_regenerate_id(true);
    }

    /**
     * Cierra la sesión del usuario de forma segura
     */
    public function logout(): void
    {
        // Limpiar todas las variables de sesión
        $_SESSION = [];

        // Destruir la cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destruir la sesión
        session_destroy();
    }

    /**
     * Obtiene el ID del usuario actual
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el rol del usuario actual
     *
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Verifica si el usuario está autenticado
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     *
     * @param string $role Rol a verificar
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->getRole() === $role;
    }

    /**
     * Obtiene los datos completos del usuario actual
     *
     * @return array|null
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'] ?? '',
            'apellido' => $_SESSION['user_apellido'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'rol' => $_SESSION['user_role'] ?? '',
            'foto_perfil' => $_SESSION['user_foto'] ?? null,
        ];
    }

    /**
     * Verifica si la sesión es válida y segura
     *
     * @return bool
     */
    public function validateSession(): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        // Verificar timeout de inactividad
        if (SecurityManager::checkSessionTimeout(1800)) { // 30 minutos
            $this->logout();
            return false;
        }

        // Verificar consistencia de IP (opcional)
        if (!SecurityManager::checkIpConsistency()) {
            SecurityManager::logSecurityEvent('session_ip_mismatch', [
                'user_id' => $this->getUserId(),
                'session_ip' => $_SESSION['user_ip'] ?? 'unknown',
                'current_ip' => SecurityManager::getClientIp()
            ]);
            $this->logout();
            return false;
        }

        // Actualizar timestamp de última actividad
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Requiere autenticación para continuar
     * Útil como middleware
     *
     * @param bool $redirectOnFail Si debe redirigir al login en caso de fallo
     * @return bool
     */
    public function requireAuth(bool $redirectOnFail = true): bool
    {
        if (!$this->validateSession()) {
            if ($redirectOnFail) {
                setFlashMessage(
                    'Autenticación Requerida',
                    'Debe iniciar sesión para acceder a esta página.',
                    'warning'
                );
                redirect('login');
            }
            return false;
        }

        return true;
    }

    /**
     * Requiere un rol específico para continuar
     *
     * @param string $role Rol requerido
     * @param bool $redirectOnFail Si debe redirigir en caso de fallo
     * @return bool
     */
    public function requireRole(string $role, bool $redirectOnFail = true): bool
    {
        if (!$this->requireAuth($redirectOnFail)) {
            return false;
        }

        if (!$this->hasRole($role)) {
            if ($redirectOnFail) {
                http_response_code(403);
                setFlashMessage(
                    'Acceso Denegado',
                    'No tiene permisos para acceder a esta sección.',
                    'error'
                );
                redirect('dashboard');
            }
            return false;
        }

        return true;
    }

    /**
     * Obtiene el tiempo de sesión activa en segundos
     *
     * @return int Segundos desde el login
     */
    public function getSessionDuration(): int
    {
        if (!isset($_SESSION['login_time'])) {
            return 0;
        }

        return time() - $_SESSION['login_time'];
    }

    /**
     * Obtiene el tiempo de inactividad en segundos
     *
     * @return int Segundos desde la última actividad
     */
    public function getInactivityTime(): int
    {
        if (!isset($_SESSION['last_activity'])) {
            return 0;
        }

        return time() - $_SESSION['last_activity'];
    }
}
