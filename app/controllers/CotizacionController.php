<?php
require_once dirname(__DIR__) . '/models/CotizacionModel.php';
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__) . '/services/FileUploadService.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * CotizacionController — lógica de negocio del módulo de cotizaciones.
 *
 * Principios aplicados:
 *   - SRP: el manejo de archivos fue delegado a FileUploadService.
 *   - DRY: eliminados procesarFoto() y procesarFotoEdicion() duplicados.
 *   - Seguridad: rota token CSRF después de cada POST exitoso.
 */
class CotizacionController
{
    private CotizacionModel   $model;
    private ProductoModel     $productoModel;
    private FileUploadService $uploader;
    private int $porPagina = 10;

    public function __construct(\mysqli $conexion)
    {
        $this->model         = new CotizacionModel($conexion);
        $this->productoModel = new ProductoModel($conexion);
        $this->uploader      = new FileUploadService(dirname(__DIR__, 2) . '/uploads');
    }

    // ── CREAR / GESTIONAR ÍTEMS ───────────────────────────────────────────────
    public function crear(): array
    {
        verificar_autenticacion();

        $usuario    = $_SESSION['usuario_nombre'];
        $csrf_token = generar_token_csrf();

        $busqueda = sanitizar_entrada($_GET['busqueda'] ?? '');
        $productos = $this->productoModel->listarTodos($busqueda);

        $producto = null;
        if (validar_numero($_GET['producto_id'] ?? '')) {
            $producto = $this->productoModel->buscarPorId((int)$_GET['producto_id']);
        }

        $cotizacion_id = $this->recuperarOCrearBorrador($usuario);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            ($_POST['action'] ?? '') === 'guardar_item') {
            $this->procesarGuardarItem($cotizacion_id);
        }

        $items      = $this->model->obtenerItems($cotizacion_id);
        $totalItems = count($items);

        return compact('productos', 'producto', 'busqueda', 'cotizacion_id',
                       'items', 'totalItems', 'csrf_token');
    }

    private function recuperarOCrearBorrador(string $usuario): int
    {
        if (!isset($_SESSION['cotizacion_id'])) {
            $id = $this->model->buscarBorradorConItems($usuario)
               ?? $this->model->buscarCabeceraVacia($usuario)
               ?? $this->model->crearCabecera($usuario);
            $_SESSION['cotizacion_id'] = $id;
        }
        return (int)$_SESSION['cotizacion_id'];
    }

    private function procesarGuardarItem(int $cotizacion_id): void
    {
        if (!verificar_token_csrf($_POST['csrf_token'] ?? '')) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&error=csrf');
            exit();
        }

        $producto_id = validar_numero($_POST['producto_id'] ?? '') ? (int)$_POST['producto_id'] : 0;
        $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
        $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
        $cantidad    = (int)($_POST['cantidad'] ?? 0);
        $iva         = sanitizar_entrada($_POST['IVA'] ?? '');
        $precio      = (float)($_POST['precio'] ?? 0);

        if (!in_array($iva, ['si', 'no'], true)) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&error=iva');
            exit();
        }

        // SRP: FileUploadService maneja el archivo
        $foto = $this->uploader->subir($_FILES['foto'] ?? [], $_POST['foto_actual'] ?? '');

        $this->model->insertarItem($cotizacion_id, $titulo, $foto, $descripcion, $cantidad, $iva, $precio);

        if ($producto_id === 0 && !$this->productoModel->existePorTitulo($titulo)) {
            $this->productoModel->crear($titulo, $foto, $descripcion, $cantidad, $iva, $precio);
        }

        rotar_token_csrf();
        header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear');
        exit();
    }

    // ── EDITAR ÍTEM ───────────────────────────────────────────────────────────
    public function editarItem(): array
    {
        verificar_autenticacion();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!isset($_SESSION['cotizacion_id'])) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&error=no_session');
            exit();
        }
        $cotizacion_id = (int)$_SESSION['cotizacion_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verificar_token_csrf($_POST['csrf_token'] ?? '')) {
                $mensajeError = 'Token de seguridad inválido';
            } else {
                $itemId      = (int)($_POST['item_id'] ?? 0);
                $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
                $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
                $cantidad    = (int)($_POST['cantidad'] ?? 0);
                $iva         = sanitizar_entrada($_POST['IVA'] ?? '');
                $precio      = (float)($_POST['precio'] ?? 0);

                if (!in_array($iva, ['si', 'no'], true)) {
                    $mensajeError = 'Valor de IVA no válido';
                } elseif ($cantidad <= 0 || $precio < 0) {
                    $mensajeError = 'Cantidad y precio deben ser valores válidos';
                } else {
                    // SRP: FileUploadService maneja el reemplazo
                    $rutaFinal = $this->uploader->reemplazar(
                        $_FILES['foto'] ?? [],
                        $_POST['foto_actual'] ?? ''
                    );

                    if ($this->model->actualizarItem($itemId, $cotizacion_id, $titulo,
                        $rutaFinal, $descripcion, $cantidad, $iva, $precio)) {
                        rotar_token_csrf();
                        header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&updated=1');
                        exit();
                    }
                    $mensajeError = 'Error al actualizar el ítem';
                }
            }
        }

        if (!validar_numero($_GET['id'] ?? '')) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&error=invalid_id');
            exit();
        }

        $itemId = (int)$_GET['id'];
        $datos  = $this->model->buscarItemPorId($itemId, $cotizacion_id);
        if (!$datos) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear&error=not_found');
            exit();
        }

        return compact('datos', 'mensajeError', 'csrf_token');
    }

    // ── ELIMINAR ÍTEM ─────────────────────────────────────────────────────────
    public function eliminarItem(): void
    {
        verificar_autenticacion();

        $esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!validar_numero($_GET['id'] ?? '')) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
                exit();
            }
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear');
            exit();
        }

        $this->model->eliminarItem((int)$_GET['id']);
        
        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit();
        }
        header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear');
        exit();
    }

    // ── CONSULTAR ─────────────────────────────────────────────────────────────
    public function consultar(): array
    {
        verificar_autenticacion();

        $csrf_token   = generar_token_csrf();
        $mensajeError = '';
        $cotizaciones = [];
        $totalPaginas = 0;
        $paginaActual = max(1, (int)($_GET['pagina'] ?? 1));
        $offset       = ($paginaActual - 1) * $this->porPagina;

        $busquedaFecha   = '';
        $busquedaCliente = '';
        $busquedaNumero  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verificar_token_csrf($_POST['csrf_token'] ?? '')) {
                $mensajeError = 'Token de seguridad inválido';
            } else {
                $filtros = [];
                if (!empty($_POST['fecha']))             $filtros['fecha']             = sanitizar_entrada($_POST['fecha']);
                if (!empty($_POST['nombre_cliente']))    $filtros['nombre_cliente']    = sanitizar_entrada($_POST['nombre_cliente']);
                if (!empty($_POST['numero_cotizacion'])) $filtros['numero_cotizacion'] = sanitizar_entrada($_POST['numero_cotizacion']);

                $_SESSION['cotizacion_filtros'] = $filtros;
                $_SESSION['cotizacion_pagina']  = 1;

                rotar_token_csrf();
                header('Location: ' . BASE_URL . '?module=cotizaciones&action=consultar&buscando=1');
                exit();
            }
        }

        if (isset($_GET['buscando'], $_SESSION['cotizacion_filtros'])) {
            $filtros         = $_SESSION['cotizacion_filtros'];
            $busquedaFecha   = $filtros['fecha'] ?? '';
            $busquedaCliente = $filtros['nombre_cliente'] ?? '';
            $busquedaNumero  = $filtros['numero_cotizacion'] ?? '';

            $total        = $this->model->contarConFiltros($filtros);
            $totalPaginas = (int)ceil($total / $this->porPagina);
            $cotizaciones = $this->model->buscarConFiltros($filtros, $offset, $this->porPagina);
        }

        if (isset($_GET['limpiar'])) {
            unset($_SESSION['cotizacion_filtros']);
        }

        return compact('cotizaciones', 'csrf_token', 'mensajeError',
                       'busquedaFecha', 'busquedaCliente', 'busquedaNumero',
                       'paginaActual', 'totalPaginas');
    }

    // ── GENERAR PDF ───────────────────────────────────────────────────────────
    public function generarPdf(): array
    {
        verificar_autenticacion();

        if (isset($_GET['ver']) || isset($_GET['descargar'])) {
            $numero     = sanitizar_entrada($_GET['ver'] ?? $_GET['descargar'] ?? '');
            $cotizacion = $this->model->buscarPorNumero($numero);
            if (!$cotizacion) {
                http_response_code(404);
                error_log("Cotización no encontrada: $numero");
                die('Cotización no encontrada.');
            }
            return [
                'cotizacion' => $cotizacion,
                'items'      => $this->model->obtenerItems((int)$cotizacion['id']),
                'forzar'     => isset($_GET['descargar']),
            ];
        }

        if (!isset($_SESSION['cotizacion_id'])) {
            header('Location: ' . BASE_URL . '?module=cotizaciones&action=crear');
            exit();
        }

        $cotizacion_id = (int)$_SESSION['cotizacion_id'];

        $profesion     = sanitizar_entrada($_POST['profesion'] ?? '');
        $nombreCliente = sanitizar_entrada($_POST['nombre_cliente'] ?? '');
        $especialidad  = sanitizar_entrada($_POST['especialidad'] ?? '');
        $entidad       = sanitizar_entrada($_POST['entidad'] ?? '');
        $ciudad        = sanitizar_entrada($_POST['ciudad'] ?? '');
        $fecha         = sanitizar_entrada($_POST['fecha'] ?? '');

        $numeroCotizacion = $this->model->finalizarCotizacion(
            $cotizacion_id, $fecha, $profesion, $nombreCliente, $especialidad, $entidad, $ciudad
        );

        $items = $this->model->obtenerItems($cotizacion_id);
        unset($_SESSION['cotizacion_id']);

        return [
            'cotizacion' => [
                'numero_cotizacion' => $numeroCotizacion,
                'profesion'         => $profesion,
                'nombre_cliente'    => $nombreCliente,
                'especialidad'      => $especialidad,
                'entidad'           => $entidad,
                'ciudad'            => $ciudad,
                'fecha_creacion'    => $fecha,
            ],
            'items'  => $items,
            'forzar' => true,
        ];
    }

    // ── AJAX ──────────────────────────────────────────────────────────────────
    public function ajaxBuscarProductos(): void
    {
        verificar_autenticacion();
        header('Content-Type: application/json');

        $busqueda  = sanitizar_entrada($_GET['busqueda'] ?? '');
        $productos = $this->productoModel->listarTodos($busqueda);
        echo json_encode(['status' => 'success', 'data' => $productos]);
        exit();
    }

    public function ajaxGetProducto(): void
    {
        verificar_autenticacion();
        header('Content-Type: application/json');

        if (!validar_numero($_GET['id'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            exit();
        }

        $producto = $this->productoModel->buscarPorId((int)$_GET['id']);
        echo json_encode($producto
            ? ['status' => 'success', 'data' => $producto]
            : ['status' => 'error', 'message' => 'Producto no encontrado']
        );
        exit();
    }
}
