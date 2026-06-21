<?php
/**
 * seguridad.php — Funciones de seguridad del sistema.
 *
 * Cambios aplicados:
 *   - FIX CRÍTICO: sanitizar_entrada() ya NO aplica htmlspecialchars.
 *     htmlspecialchars pertenece a la capa de SALIDA (vistas), no de entrada.
 *     Guardarlo en BD con htmlspecialchars corrompe los datos y rompe búsquedas.
 *   - NUEVO: escapar_salida() para usar en vistas al imprimir datos.
 *   - FIX: COOKIE_SECURE se castea a int para evitar que "0" sea evaluado como truthy.
 *   - NUEVO: rotar_token_csrf() para protección anti-replay post-POST.
 */

// ── Sesión segura ────────────────────────────────────────────────────────────

function iniciar_sesion_segura(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    // FIX: cast a int para que "0" (string) no sea truthy
    ini_set('session.cookie_secure', (int)($_ENV['COOKIE_SECURE'] ?? getenv('COOKIE_SECURE') ?: 0));
    ini_set('session.cookie_samesite', 'Strict');

    session_start();

    // Timeout por inactividad
    $timeout = (int)($_ENV['SESSION_LIFETIME'] ?? getenv('SESSION_LIFETIME') ?: 3600);
    $base    = defined('BASE_URL') ? BASE_URL : '/';

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
        session_unset();
        session_destroy();
        header("Location: {$base}?timeout=1");
        exit();
    }

    $_SESSION['LAST_ACTIVITY'] = time();
}

// ── CSRF ─────────────────────────────────────────────────────────────────────

/**
 * Genera (o recupera) el token CSRF de la sesión actual.
 */
function generar_token_csrf(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token recibido coincida con el de la sesión.
 * Usa comparación de tiempo constante para evitar timing attacks.
 */
function verificar_token_csrf(string $token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rota el token CSRF: destruye el actual y genera uno nuevo.
 * Llamar después de cada POST exitoso para prevenir replay attacks.
 */
function rotar_token_csrf(): string
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// ── Prevención de Saturación (Rate Limiting) ─────────────────────────────────

/**
 * Valida que el usuario/IP no exceda un límite de peticiones en una ventana de tiempo.
 * Si excede el límite, responde con HTTP 429 y termina la ejecución.
 * 
 * @param int $limite Máximo de peticiones permitidas (ej. 15).
 * @param int $ventanaSegundos Tiempo en el que se evalúa (ej. 60 segundos).
 * @param string $accion Clave para diferenciar el rate limit (ej. 'ajax_busqueda', 'login').
 */
function verificar_rate_limit(int $limite = 15, int $ventanaSegundos = 60, string $accion = 'global'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $clave = "rate_limit_" . $accion;
    $tiempoActual = time();

    if (!isset($_SESSION[$clave])) {
        $_SESSION[$clave] = [];
    }

    // Filtrar peticiones antiguas fuera de la ventana de tiempo
    $_SESSION[$clave] = array_filter($_SESSION[$clave], function ($timestamp) use ($tiempoActual, $ventanaSegundos) {
        return ($tiempoActual - $timestamp) < $ventanaSegundos;
    });

    // Si ya alcanzó el límite
    if (count($_SESSION[$clave]) >= $limite) {
        http_response_code(429); // Too Many Requests
        
        // Si es una petición AJAX (lo comprobamos de forma genérica)
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'message' => 'Demasiadas peticiones. Por favor, espere un momento antes de volver a intentar.'
            ]);
        } else {
            echo "<h1>429 - Demasiadas peticiones</h1><p>Por favor, espere un momento antes de volver a intentar.</p>";
        }
        exit;
    }

    // Registrar la petición actual
    $_SESSION[$clave][] = $tiempoActual;
}

// ── Sanitización y escape ─────────────────────────────────────────────────────

/**
 * Limpia una entrada de texto: elimina espacios y barras invertidas.
 * NO aplica htmlspecialchars — eso es responsabilidad de escapar_salida() en las vistas.
 * Los datos se guardan en BD tal como el usuario los escribió (limpios pero sin codificar).
 *
 * @param mixed $data
 * @return string
 */
function sanitizar_entrada($data): string
{
    return stripslashes(trim((string)$data));
}

/**
 * Escapa una cadena para imprimir de forma segura en HTML.
 * Usar en TODAS las vistas al imprimir datos de usuario o de la BD.
 *
 * @param mixed $data
 * @return string
 */
function escapar_salida($data): string
{
    return htmlspecialchars((string)$data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ── Validadores ───────────────────────────────────────────────────────────────

function validar_email(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validar_numero($numero): bool
{
    return is_numeric($numero) && $numero > 0;
}

/**
 * Valida un archivo de imagen subido.
 * Verifica error, tamaño, extensión y tipo MIME real del archivo.
 *
 * @param array $archivo Entrada de $_FILES.
 * @return array{valido: bool, mensaje: string}
 */
function validar_imagen(array $archivo): array
{
    $extensionesPermitidas = explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? getenv('ALLOWED_EXTENSIONS') ?: 'jpg,jpeg,png,gif,webp');
    $maxSize               = (int)($_ENV['UPLOAD_MAX_SIZE'] ?? getenv('UPLOAD_MAX_SIZE') ?: 5242880);

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return ['valido' => false, 'mensaje' => 'Error al subir el archivo'];
    }

    if ($archivo['size'] > $maxSize) {
        return ['valido' => false, 'mensaje' => 'El archivo es demasiado grande (máximo 5 MB)'];
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas, true)) {
        return ['valido' => false, 'mensaje' => 'Tipo de archivo no permitido'];
    }

    // Verificar tipo MIME real (no confiar en la extensión)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    $mimesPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime, $mimesPermitidos, true)) {
        return ['valido' => false, 'mensaje' => 'El archivo no es una imagen válida'];
    }

    return ['valido' => true, 'mensaje' => 'Archivo válido'];
}

/**
 * Genera un nombre único para un archivo subido.
 * Usa tiempo + bytes aleatorios para evitar colisiones y enumeración.
 */
function generar_nombre_archivo(string $extension): string
{
    return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
}

// ── Control de acceso ─────────────────────────────────────────────────────────

/**
 * Verifica que el usuario haya iniciado sesión.
 * Redirige al login si no está autenticado.
 */
function verificar_autenticacion(): void
{
    if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_nombre'])) {
        $base = defined('BASE_URL') ? BASE_URL : '/';
        header("Location: {$base}");
        exit();
    }
}

/**
 * Verifica que el usuario tenga rol 'admin'.
 * Redirige al panel si está autenticado pero no es admin.
 */
function verificar_admin(): void
{
    verificar_autenticacion();
    if (($_SESSION['rol'] ?? '') !== 'admin') {
        $base = defined('BASE_URL') ? BASE_URL : '/';
        header("Location: {$base}?module=panel");
        exit();
    }
}

/**
 * Regenera el ID de sesión para prevenir session fixation.
 */
function regenerar_sesion(): void
{
    session_regenerate_id(true);
}
