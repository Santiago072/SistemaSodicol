<?php
/**
 * index.php — Front Controller / Router principal
 *
 * Único punto de entrada del sistema MVC. Lee los parámetros
 * ?module= y ?action= de la URL, valida, instancia el controlador
 * correspondiente y renderiza la vista adecuada.
 *
 * Rutas disponibles:
 *   (sin params)                      → Login
 *   ?module=panel                     → Dashboard
 *   ?module=usuarios&action=lista     → Lista de usuarios
 *   ?module=usuarios&action=crear     → Crear usuario
 *   ?module=usuarios&action=editar    → Editar usuario (requiere &id=)
 *   ?module=usuarios&action=eliminar  → Eliminar usuario (requiere &id=)
 *   ?module=productos&action=lista    → Lista de productos
 *   ?module=productos&action=editar   → Editar producto (requiere &id=)
 *   ?module=productos&action=eliminar → Eliminar producto (requiere &id=)
 *   ?module=tareas&action=gestion     → Gestión de tareas
 *   ?module=tareas&action=editar      → Editar tarea (requiere &id=)
 *   ?module=tareas&action=eliminar    → Eliminar tarea (requiere &id=)
 *   ?module=cotizaciones&action=crear         → Crear cotización
 *   ?module=cotizaciones&action=consultar     → Consultar cotizaciones
 *   ?module=cotizaciones&action=editar_item   → Editar ítem (requiere &id=)
 *   ?module=cotizaciones&action=eliminar_item → Eliminar ítem (requiere &id=)
 *   ?module=cotizaciones&action=generar_pdf   → Generar PDF
 *   ?action=logout                            → Cerrar sesión
 */

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/seguridad.php';

iniciar_sesion_segura();

$module = sanitizar_entrada($_GET['module'] ?? '');
$action = sanitizar_entrada($_GET['action'] ?? '');

// ── Logout (acción global sin módulo) ───────────────────────────────────────
if ($action === 'logout') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    (new AuthController(conexion()))->logout();
    exit();
}

// ── Sin módulo: mostrar login ────────────────────────────────────────────────
if ($module === '') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $data = (new AuthController(conexion()))->login();
    extract($data);
    include __DIR__ . '/app/views/auth/login.php';
    exit();
}

// ── Módulo: panel ────────────────────────────────────────────────────────────
if ($module === 'panel') {
    require_once __DIR__ . '/app/controllers/PanelController.php';
    $data = (new PanelController(conexion()))->index();
    extract($data);
    include __DIR__ . '/app/views/panel/index.php';
    exit();
}

// ── Módulo: usuarios ─────────────────────────────────────────────────────────
if ($module === 'usuarios') {
    require_once __DIR__ . '/app/controllers/UsuarioController.php';
    $ctrl = new UsuarioController(conexion());

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

        default: // lista
            $data    = $ctrl->listar();
            $urlBase = '/PROYECTO_SODICOL/?module=usuarios&action=lista'
                     . (!empty($data['busqueda']) ? '&busqueda=' . urlencode($data['busqueda']) : '');
            extract($data);
            include __DIR__ . '/app/views/usuarios/lista.php';
    }
    exit();
}

// ── Módulo: productos ────────────────────────────────────────────────────────
if ($module === 'productos') {
    require_once __DIR__ . '/app/controllers/ProductoController.php';
    $ctrl = new ProductoController(conexion());

    switch ($action) {
        case 'editar':
            $data = $ctrl->editar();
            extract($data);
            include __DIR__ . '/app/views/productos/editar.php';
            break;

        case 'eliminar':
            $ctrl->eliminar();
            break;

        default: // lista
            $data    = $ctrl->listar();
            $urlBase = '/PROYECTO_SODICOL/?module=productos&action=lista'
                     . (!empty($data['busqueda']) ? '&busqueda=' . urlencode($data['busqueda']) : '');
            extract($data);
            include __DIR__ . '/app/views/productos/lista.php';
    }
    exit();
}

// ── Módulo: tareas ───────────────────────────────────────────────────────────
if ($module === 'tareas') {
    require_once __DIR__ . '/app/controllers/TareaController.php';
    $ctrl = new TareaController(conexion());

    switch ($action) {
        case 'editar':
            $data = $ctrl->editar();
            extract($data);
            include __DIR__ . '/app/views/tareas/editar.php';
            break;

        case 'eliminar':
            $ctrl->eliminar();
            break;

        default: // gestion
            $data    = $ctrl->gestion();
            $urlBase = '/PROYECTO_SODICOL/?module=tareas&action=gestion';
            extract($data);
            include __DIR__ . '/app/views/tareas/gestion.php';
    }
    exit();
}

// ── Módulo: cotizaciones ─────────────────────────────────────────────────────
if ($module === 'cotizaciones') {
    require_once __DIR__ . '/app/controllers/CotizacionController.php';
    $ctrl = new CotizacionController(conexion());

    switch ($action) {
        case 'consultar':
            $data    = $ctrl->consultar();
            $urlBase = '/PROYECTO_SODICOL/?module=cotizaciones&action=consultar&buscando=1';
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
            // La generación de PDF requiere su propio flujo (salida binaria)
            include __DIR__ . '/app/views/cotizaciones/generar_pdf.php';
            break;

        default: // crear
            $data = $ctrl->crear();
            extract($data);
            include __DIR__ . '/app/views/cotizaciones/crear.php';
    }
    exit();
}

// ── Módulo desconocido → redirigir al login ──────────────────────────────────
header('Location: /PROYECTO_SODICOL/');
exit();
