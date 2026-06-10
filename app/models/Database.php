<?php
/**
 * Clase Database — wrapper singleton sobre la conexión mysqli existente.
 * Mantiene compatibilidad total con el código que ya usa mysqli_*.
 */
class Database {
    private static ?Database $instance = null;
    private $conn;

    private function __construct() {
        require_once dirname(__DIR__, 2) . '/config/conexion.php';
        $this->conn = conexion();
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
