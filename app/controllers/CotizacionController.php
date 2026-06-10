<?php
require_once dirname(__DIR__) . '/models/CotizacionModel.php';
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * CotizacionController — lógica de negocio del módulo de cotizaciones.
 */
class CotizacionController {
    private CotizacionModel $model;
    private ProductoModel $productoModel;
    private int $porPagina = 10;

    public function __construct($conexion) {
        $this->model         = new CotizacionModel($conexion);
        $this->productoModel = new ProductoModel($conexion);
    }

    // ── CREAR / GESTIONAR ÍTEMS ──────────────────────────────
    public function crear(): array {
        verificar_autenticacion();

        $usuario   = $_SESSION['usuario_nombre'];
        $csrf_token = generar_token_csrf();

        // Búsqueda de productos
        $busqueda = '';
        if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
            $busqueda = sanitizar_entrada($_GET['busqueda']);
        }
        $productos = $this->productoModel->listarTodos($busqueda);

        // Producto seleccionado
        $producto = null;
        if (isset($_GET['producto_id']) && validar_numero($_GET['producto_id'])) {
            $producto = $this->productoModel->buscarPorId(intval($_GET['producto_id']));
        }

        // Recuperar borrador de sesión
        $cotizacion_id = $this->recuperarOCrearBorrador($usuario);

        // Guardar ítem (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['action']) && $_POST['action'] === 'guardar_item') {
            $this->procesarGuardarItem($cotizacion_id);
        }

        $items       = $this->model->obtenerItems($cotizacion_id);
        $totalItems  = count($items);

        return compact('productos', 'producto', 'busqueda', 'cotizacion_id',
                        'items', 'totalItems', 'csrf_token');
    }

    private function recuperarOCrearBorrador(string $usuario): int {
        if (!isset($_SESSION['cotizacion_id'])) {
            $id = $this->model->buscarBorradorConItems($usuario)
               ?? $this->model->buscarCabeceraVacia($usuario)
               ?? $this->model->crearCabecera($usuario);
            $_SESSION['cotizacion_id'] = $id;
        }
        return intval($_SESSION['cotizacion_id']);
    }

    private function procesarGuardarItem(int $cotizacion_id): void {
        if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&error=csrf");
            exit();
        }

        $producto_id = isset($_POST['producto_id']) && validar_numero($_POST['producto_id'])
                       ? intval($_POST['producto_id']) : 0;
        $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
        $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
        $cantidad    = intval($_POST['cantidad'] ?? 0);
        $iva         = sanitizar_entrada($_POST['IVA'] ?? '');
        $precio      = floatval($_POST['precio'] ?? 0);

        if (!in_array($iva, ['si', 'no'])) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&error=iva");
            exit();
        }

        $foto = $this->procesarFoto($_POST['foto_actual'] ?? '');

        $this->model->insertarItem($cotizacion_id, $titulo, $foto, $descripcion, $cantidad, $iva, $precio);

        // Auto-registrar en catálogo si no existe
        if ($producto_id === 0 && !$this->productoModel->existePorTitulo($titulo)) {
            $this->productoModel->crear($titulo, $foto, $descripcion, $cantidad, $iva, $precio);
        }

        header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear");
        exit();
    }

    private function procesarFoto(string $fotoActual): string {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $validacion = validar_imagen($_FILES['foto']);
            if ($validacion['valido']) {
                $ext    = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $nombre = generar_nombre_archivo($ext);
                $dir    = dirname(__DIR__, 2) . '/uploads/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nombre)) {
                    return $nombre;
                }
            }
        }
        return basename($fotoActual);
    }

    // ── EDITAR ÍTEM ──────────────────────────────────────────
    public function editarItem(): array {
        verificar_autenticacion();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!isset($_SESSION['cotizacion_id'])) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&error=no_session");
            exit();
        }
        $cotizacion_id = intval($_SESSION['cotizacion_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $itemId      = intval($_POST['item_id'] ?? 0);
                $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
                $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
                $cantidad    = intval($_POST['cantidad'] ?? 0);
                $iva         = sanitizar_entrada($_POST['IVA'] ?? '');
                $precio      = floatval($_POST['precio'] ?? 0);

                if (!in_array($iva, ['si', 'no'])) {
                    $mensajeError = "Valor de IVA no válido";
                } elseif ($cantidad <= 0 || $precio < 0) {
                    $mensajeError = "Cantidad y precio deben ser valores válidos";
                } else {
                    $rutaFinal = $this->procesarFotoEdicion($_POST['foto_actual'] ?? '');
                    if ($this->model->actualizarItem($itemId, $cotizacion_id, $titulo, $rutaFinal, $descripcion, $cantidad, $iva, $precio)) {
                        header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&updated=1");
                        exit();
                    }
                    $mensajeError = "Error al actualizar el ítem";
                }
            }
        }

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&error=invalid_id");
            exit();
        }

        $itemId = intval($_GET['id']);
        $datos  = $this->model->buscarItemPorId($itemId, $cotizacion_id);
        if (!$datos) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear&error=not_found");
            exit();
        }

        return compact('datos', 'mensajeError', 'csrf_token');
    }

    private function procesarFotoEdicion(string $fotoActual): string {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $validacion = validar_imagen($_FILES['foto']);
            if ($validacion['valido']) {
                $ext    = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $nombre = generar_nombre_archivo($ext);
                $dir    = dirname(__DIR__, 2) . '/uploads/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nombre)) {
                    if (!empty($fotoActual)) {
                        $anterior = $dir . basename($fotoActual);
                        if (file_exists($anterior)) unlink($anterior);
                    }
                    return $nombre;
                }
            }
        }
        return basename($fotoActual);
    }

    // ── ELIMINAR ÍTEM ────────────────────────────────────────
    public function eliminarItem(): void {
        verificar_autenticacion();

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear");
            exit();
        }

        $this->model->eliminarItem(intval($_GET['id']));
        header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=crear");
        exit();
    }

    // ── CONSULTAR ────────────────────────────────────────────
    public function consultar(): array {
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
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $filtros = [];
                if (!empty($_POST['fecha']))              $filtros['fecha']             = sanitizar_entrada($_POST['fecha']);
                if (!empty($_POST['nombre_cliente']))     $filtros['nombre_cliente']    = sanitizar_entrada($_POST['nombre_cliente']);
                if (!empty($_POST['numero_cotizacion']))  $filtros['numero_cotizacion'] = sanitizar_entrada($_POST['numero_cotizacion']);

                $busquedaFecha   = $filtros['fecha'] ?? '';
                $busquedaCliente = $filtros['nombre_cliente'] ?? '';
                $busquedaNumero  = $filtros['numero_cotizacion'] ?? '';

                // Paginación en búsqueda: guardar filtros en sesión
                $_SESSION['cotizacion_filtros'] = $filtros;
                $_SESSION['cotizacion_pagina']  = 1;
                header("Location: /PROYECTO_SODICOL/?module=cotizaciones&action=consultar&buscando=1");
                exit();
            }
        }

        // GET con paginación o resultado de búsqueda
        if (isset($_GET['buscando']) && isset($_SESSION['cotizacion_filtros'])) {
            $filtros = $_SESSION['cotizacion_filtros'];
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

    // ── GENERAR PDF ──────────────────────────────────────────
    public function generarPdf(bool $descargar = true): array {
        verificar_autenticacion();

        // Modo ver/descargar cotización existente
        if (isset($_GET['ver']) || isset($_GET['descargar'])) {
            $numero     = sanitizar_entrada($_GET['ver'] ?? $_GET['descargar'] ?? '');
            $cotizacion = $this->model->buscarPorNumero($numero);
            if (!$cotizacion) {
                die('Cotización no encontrada: ' . htmlspecialchars($numero));
            }
            $items = $this->model->obtenerItems((int)$cotizacion['id']);
            return [
                'cotizacion' => $cotizacion,
                'items'      => $items,
                'forzar'     => isset($_GET['descargar']),
            ];
        }

        // Modo generar nueva
        if (!isset($_SESSION['cotizacion_id'])) {
            die('No hay cotización activa.');
        }

        $cotizacion_id = intval($_SESSION['cotizacion_id']);

        $profesion      = sanitizar_entrada($_POST['profesion'] ?? '');
        $nombreCliente  = sanitizar_entrada($_POST['nombre_cliente'] ?? '');
        $especialidad   = sanitizar_entrada($_POST['especialidad'] ?? '');
        $entidad        = sanitizar_entrada($_POST['entidad'] ?? '');
        $ciudad         = sanitizar_entrada($_POST['ciudad'] ?? '');
        $fecha          = sanitizar_entrada($_POST['fecha'] ?? '');

        // Finalizar con transacción atómica (número único garantizado)
        $numeroCotizacion = $this->model->finalizarCotizacion(
            $cotizacion_id, $fecha, $profesion, $nombreCliente,
            $especialidad, $entidad, $ciudad
        );

        $items = $this->model->obtenerItems($cotizacion_id);

        // Limpiar sesión de borrador
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
}
