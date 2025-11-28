<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase para manejar la conexión a la base de datos
 */
class Database
{
    private static $instance = null;
    private $connection;

    /**
     * Constructor privado (patrón Singleton)
     */
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la instancia única de la base de datos
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Evita la clonación del objeto
     */
    private function __clone() {}

    /**
     * Evita la deserialización del objeto
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
