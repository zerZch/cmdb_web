<?php

namespace App\Core;

/**
 * Clase Validator - Validaciones profesionales con POO
 * Implementa todas las validaciones de seguridad necesarias
 */
class Validator
{
    private array $errors = [];
    private array $data = [];

    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Valida un conjunto de datos con reglas especificas
     *
     * @param array $rules Reglas de validación
     * @return bool True si pasa todas las validaciones
     */
    public function validate(array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? '';

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Aplica una regla de validación específica
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Ya hay un error para este campo, no seguir validando
        if (isset($this->errors[$field])) {
            return;
        }

        // Parsear regla con parámetros (ej: min:8)
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $param = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field] = "El campo {$field} es obligatorio.";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "El campo {$field} debe ser un email válido.";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$param) {
                    $this->errors[$field] = "El campo {$field} debe tener al menos {$param} caracteres.";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$param) {
                    $this->errors[$field] = "El campo {$field} no debe exceder {$param} caracteres.";
                }
                break;

            case 'username':
                // Solo alfanuméricos, guiones y puntos
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9._-]+$/', $value)) {
                    $this->errors[$field] = "El campo {$field} solo puede contener letras, números, puntos, guiones y guiones bajos.";
                }
                break;

            case 'no_spaces':
                if (!empty($value) && preg_match('/\s/', $value)) {
                    $this->errors[$field] = "El campo {$field} no debe contener espacios.";
                }
                break;

            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value)) {
                    $this->errors[$field] = "El campo {$field} solo puede contener letras.";
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->errors[$field] = "El campo {$field} solo puede contener letras y números.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field] = "El campo {$field} debe ser numérico.";
                }
                break;

            case 'integer':
                if (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->errors[$field] = "El campo {$field} debe ser un número entero.";
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->errors[$field] = "El campo {$field} debe ser una URL válida.";
                }
                break;

            case 'ip':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_IP)) {
                    $this->errors[$field] = "El campo {$field} debe ser una IP válida.";
                }
                break;

            case 'date':
                if (!empty($value) && !$this->isValidDate($value)) {
                    $this->errors[$field] = "El campo {$field} debe ser una fecha válida (YYYY-MM-DD).";
                }
                break;

            case 'strong_password':
                if (!empty($value) && !$this->isStrongPassword($value)) {
                    $this->errors[$field] = "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales.";
                }
                break;
        }
    }

    /**
     * Verifica si una fecha es válida
     */
    private function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Verifica si una contraseña es fuerte
     * Mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número y 1 caracter especial
     */
    private function isStrongPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        // Verificar mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Verificar minúscula
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Verificar número
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // Verificar caracter especial
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Sanitiza un string eliminando caracteres peligrosos
     */
    public static function sanitize(string $input): string
    {
        // Trim espacios
        $input = trim($input);

        // Eliminar tags HTML y PHP
        $input = strip_tags($input);

        // Convertir caracteres especiales a entidades HTML
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $input;
    }

    /**
     * Sanitiza un email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitiza una URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitiza un integer
     */
    public static function sanitizeInt($value): int
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Valida y sanitiza datos de entrada en un solo paso
     */
    public static function cleanInput($input, string $type = 'string')
    {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::cleanInput($item, $type);
            }, $input);
        }

        switch ($type) {
            case 'email':
                return self::sanitizeEmail($input);
            case 'url':
                return self::sanitizeUrl($input);
            case 'int':
            case 'integer':
                return self::sanitizeInt($input);
            case 'string':
            default:
                return self::sanitize($input);
        }
    }

    /**
     * Obtiene todos los errores
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Obtiene el primer error
     */
    public function getFirstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }

        return reset($this->errors);
    }

    /**
     * Verifica si hay errores
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Añade un error manualmente
     */
    public function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}
