<?php
/**
 * index.php — Front Controller / Router principal
 *
 * Único punto de entrada del sistema MVC. Lee el parámetro ?module= de la URL,
 * valida, instancia el controlador correspondiente y renderiza la vista adecuada.
 *
 * Principios aplicados:
 *   - OCP: el mapa de rutas ($rutasMap) permite añadir nuevos módulos sin modificar
 *     la lógica del router (solo se agrega una entrada al array).
 *   - SRP: la carga del .env está delegada a EnvLoader.
 *   - DRY: eliminada la lógica duplicada de carga del .env que existía aquí y en conexion.php.
 *
 * Rutas disponibles:
 *   (sin params)                               → Login
 *   ?action=logout                             → Cerrar sesión
 *   ?module=panel                              → Dashboard
 *   ?module=panel&action=ajax_completar_tarea  → AJAX panel
 *   ?module=usuarios&action=lista|crear|editar|eliminar
 *   ?module=productos&action=lista|editar|eliminar
 *   ?module=tareas&action=gestion|editar|eliminar
 *   ?module=cotizaciones&action=crear|consultar|editar_item|eliminar_item|generar_pdf|ajax_*
 */

// ── Producción: errores al log, nunca al usuario ──────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// ── Manejador Global de Excepciones ───────────────────────────────────────────
set_exception_handler(function (Throwable $e) {
    error_log((string)$e);
    http_response_code(500);
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        // Para depuración, enviamos el mensaje real del error
        echo json_encode([
            'status' => 'error', 
            'message' => 'Ocurrió un error interno: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine()
        ]);
    } else {
        echo "<h1>500 - Error Interno</h1><p>Ocurrió un error inesperado. El administrador ha sido notificado.</p><p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    exit();
});

// ── Carga del .env (SRP: delegado a EnvLoader) ────────────────────────────────
require_once __DIR__ . '/config/EnvLoader.php';
EnvLoader::load(__DIR__ . '/config/.env');

// ── URL base dinámica ─────────────────────────────────────────────────────────
// APP_BASE en .env = '/'  en Docker, autodetecta la ruta en XAMPP local.
if (!defined('BASE_URL')) {
    $appBase = $_ENV['APP_BASE'] ?? getenv('APP_BASE') ?: null;
    if ($appBase) {
        define('BASE_URL', rtrim($appBase, '/') . '/');
    } else {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        define('BASE_URL', rtrim($scriptDir, '/') . '/');
    }
}

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/seguridad.php';

iniciar_sesion_segura();

$module = sanitizar_entrada($_GET['module'] ?? '');
$action = sanitizar_entrada($_GET['action'] ?? '');

// ── Logout ────────────────────────────────────────────────────────────────────
if ($action === 'logout') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    (new AuthController(conexion()))->logout();
    exit();
}

// ── Login (sin módulo) ────────────────────────────────────────────────────────
if ($module === '') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $data = (new AuthController(conexion()))->login();
    extract($data);
    include __DIR__ . '/app/views/auth/login.php';
    exit();
}

// ── Mapa de módulos (OCP: agregar módulo = agregar una línea aquí) ────────────
$rutasMap = [
    'panel'        => __DIR__ . '/app/controllers/PanelController.php',
    'usuarios'     => __DIR__ . '/app/controllers/UsuarioController.php',
    'productos'    => __DIR__ . '/app/controllers/ProductoController.php',
    'tareas'       => __DIR__ . '/app/controllers/TareaController.php',
    'cotizaciones' => __DIR__ . '/app/controllers/CotizacionController.php',
];

if (!array_key_exists($module, $rutasMap)) {
    // Módulo desconocido → login
    header('Location: ' . BASE_URL);
    exit();
}

require_once $rutasMap[$module];
$db = conexion();

// ── Dispatch por módulo ───────────────────────────────────────────────────────

if ($module === 'panel') {
    $ctrl = new PanelController($db);
    if ($action === 'ajax_completar_tarea') {
        $ctrl->ajaxCompletarTarea();
        exit();
    }
    $data = $ctrl->index();
    extract($data);
    include __DIR__ . '/app/views/panel/index.php';
    exit();
}

if ($module === 'usuarios') {
    $ctrl = new UsuarioController($db);
    switch ($action) {
        case 'crear':
            $data = $ctrl->crear();
            extract($data);
            include __DIR__ . '/app/views/usuarios/crear.php';
            break;
        case 'editar':
            $data = $ctrl->editar();
            extract($data);
            include __DIR__ . '/app/views/usuarios/editar.php';
            break;
        case 'eliminar':
            $ctrl->eliminar();
            break;
        default:
            $data    = $ctrl->listar();
            $urlBase = BASE_URL . '?module=usuarios&action=lista'
                     . (!empty($data['busqueda']) ? '&busqueda=' . urlencode($data['busqueda']) : '');
            extract($data);
            include __DIR__ . '/app/views/usuarios/lista.php';
    }
    exit();
}

if ($module === 'productos') {
    $ctrl = new ProductoController($db);
    switch ($action) {
        case 'editar':
            $data = $ctrl->editar();
            extract($data);
            include __DIR__ . '/app/views/productos/editar.php';
            break;
        case 'eliminar':
            $ctrl->eliminar();
            break;
        default:
            $data    = $ctrl->listar();
            $urlBase = BASE_URL . '?module=productos&action=lista'
                     . (!empty($data['busqueda']) ? '&busqueda=' . urlencode($data['busqueda']) : '');
            extract($data);
            include __DIR__ . '/app/views/productos/lista.php';
    }
    exit();
}

if ($module === 'tareas') {
    $ctrl = new TareaController($db);
    switch ($action) {
        case 'editar':
            $data = $ctrl->editar();
            extract($data);
            include __DIR__ . '/app/views/tareas/editar.php';
            break;
        case 'eliminar':
            $ctrl->eliminar();
            break;
        default:
            $data    = $ctrl->gestion();
            $urlBase = BASE_URL . '?module=tareas&action=gestion';
            extract($data);
            include __DIR__ . '/app/views/tareas/gestion.php';
    }
    exit();
}

if ($module === 'cotizaciones') {
    $ctrl = new CotizacionController($db);
    switch ($action) {
        case 'consultar':
            $data    = $ctrl->consultar();
            $urlBase = BASE_URL . '?module=cotizaciones&action=consultar&buscando=1';
            extract($data);
            include __DIR__ . '/app/views/cotizaciones/consultar.php';
            break;
        case 'editar_item':
            $data = $ctrl->editarItem();
            extract($data);
            include __DIR__ . '/app/views/cotizaciones/editar_item.php';
            break;
        case 'eliminar_item':
            $ctrl->eliminarItem();
            break;
        case 'generar_pdf':
            include __DIR__ . '/app/views/cotizaciones/generar_pdf.php';
            break;
        case 'ajax_buscar_productos':
            $ctrl->ajaxBuscarProductos();
            break;
        case 'ajax_get_producto':
            $ctrl->ajaxGetProducto();
            break;
        default:
            $data = $ctrl->crear();
            extract($data);
            include __DIR__ . '/app/views/cotizaciones/crear.php';
    }
    exit();
}
