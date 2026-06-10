<?php
/**
 * Plantilla de conexión a la base de datos (Ejemplo Público)
 * 
 * INSTRUCCIONES:
 * 1. El archivo config/.env contiene las variables reales de entorno.
 * 2. Si no usas .env, copia este archivo como "conexion.php" y coloca
 *    tus credenciales en el fallback.
 * 3. NO SUBAS TUS CREDENCIALES AL REPOSITORIO PÚBLICO.
 */

// Cargar variables de entorno (Opcional, busca .env en este mismo directorio)
function cargar_env() {
    $env_file = __DIR__ . '/.env';
    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

cargar_env();

function conexion(){
    // Aquí puedes poner el fallback para desarrollo local, 
    // pero idealmente deberías usar el archivo .env
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? ''; // <-- Pon tu contraseña de DB local aquí si no usas .env
    $db   = $_ENV['DB_NAME'] ?? 'sistema_sodicol';

    $conexion = mysqli_connect($host, $user, $pass, $db);
    
    if (!$conexion) {
        error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
        die("Error de conexión. Por favor contacte al administrador.");
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
    return $conexion;
}
?>
