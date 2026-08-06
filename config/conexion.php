<?php
/**
 * Configuración de conexión a la base de datos.
 *
 * Principios aplicados:
 *   - SRP: este archivo SOLO gestiona la conexión a la BD.
 *     La carga del .env fue extraída a EnvLoader (config/EnvLoader.php).
 */

require_once __DIR__ . '/EnvLoader.php';

EnvLoader::load(__DIR__ . '/.env');

/**
 * Crea y devuelve una conexión mysqli a la base de datos.
 * En caso de error, registra en log y termina la ejecución de forma segura
 * (sin exponer detalles técnicos al usuario).
 *
 * @return \mysqli
 */
function conexion(): \mysqli
{
    $host = !empty($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (getenv('DB_HOST') ?: 'sodicol_db');
    $user = !empty($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (getenv('DB_USER') ?: 'sodicol_user');
    $pass = isset($_ENV['DB_PASS']) && $_ENV['DB_PASS'] !== '' ? $_ENV['DB_PASS'] : (getenv('DB_PASS') ?: 'root');
    $db   = !empty($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (getenv('DB_NAME') ?: 'sistema_sodicol');


    // Intentar conexión con usuario configurado
    $conn = @mysqli_connect($host, $user, $pass, $db);

    // Fallback 1: intentar con sodicol_user
    if (!$conn && $user !== 'sodicol_user') {
        $conn = @mysqli_connect($host, 'sodicol_user', 'root', $db);
    }

    // Fallback 2: intentar con root
    if (!$conn && $user !== 'root') {
        $conn = @mysqli_connect($host, 'root', 'root', $db);
    }

    if (!$conn) {
        error_log('Error de conexión a la BD: ' . mysqli_connect_error());
        http_response_code(503);
        die('El servicio no está disponible temporalmente. Por favor intente más tarde.');
    }


    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}