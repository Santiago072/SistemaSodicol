<?php
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__) . '/services/FileUploadService.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * ProductoController — lógica de negocio del módulo de productos.
 *
 * Principios aplicados:
 *   - SRP: manejo de archivos delegado a FileUploadService.
 *   - Seguridad: CSRF rotation post-POST, type hints completos.
 */
class ProductoController
{
    private ProductoModel     $model;
    private FileUploadService $uploader;
    private int $porPagina = 10;

    public function __construct(\mysqli $conexion)
    {
        $this->model    = new ProductoModel($conexion);
        $this->uploader = new FileUploadService(dirname(__DIR__, 2) . '/uploads');
    }

    // ── LISTAR ───────────────────────────────────────────────────────────────
    public function listar(): array
    {
        verificar_autenticacion();

        $busqueda     = sanitizar_entrada($_GET['busqueda'] ?? '');
        $paginaActual = max(1, (int)($_GET['pagina'] ?? 1));
        $offset       = ($paginaActual - 1) * $this->porPagina;

        $total        = $this->model->contar($busqueda);
        $productos    = $this->model->listar($offset, $this->porPagina, $busqueda);
        $totalPaginas = (int)ceil($total / $this->porPagina);

        $mensajeExito = '';
        $mensajeError = '';
        if (isset($_GET['success'])) $mensajeExito = 'Producto creado exitosamente';
        if (isset($_GET['updated'])) $mensajeExito = 'Producto actualizado exitosamente';
        if (isset($_GET['deleted'])) $mensajeExito = 'Producto eliminado exitosamente';
        if (isset($_GET['error'])) {
            $mapa = [
                'en_uso' => 'No se puede eliminar: el producto está asociado a cotizaciones existentes.',
            ];
            $mensajeError = $mapa[$_GET['error']] ?? 'Error al procesar la solicitud';
        }

        return compact('productos', 'busqueda', 'paginaActual', 'totalPaginas', 'total',
                       'mensajeExito', 'mensajeError');
    }

    // ── EDITAR ────────────────────────────────────────────────────────────────
    public function editar(): array
    {
        verificar_autenticacion();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!validar_numero($_GET['id'] ?? '')) {
            header('Location: ' . BASE_URL . '?module=productos&action=lista');
            exit();
        }

        $id      = (int)$_GET['id'];
        $producto = $this->model->buscarPorId($id);
        if (!$producto) {
            header('Location: ' . BASE_URL . '?module=productos&action=lista');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return compact('producto', 'mensajeError', 'csrf_token');
        }

        if (!verificar_token_csrf($_POST['csrf_token'] ?? '')) {
            $mensajeError = 'Token de seguridad inválido';
            return compact('producto', 'mensajeError', 'csrf_token');
        }

        $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
        $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
        $cantidad    = (int)($_POST['cantidad'] ?? 0);
        $iva         = sanitizar_entrada($_POST['iva'] ?? '');
        $precio      = (float)($_POST['precio'] ?? 0);

        if (!in_array($iva, ['si', 'no'], true)) {
            $mensajeError = 'Valor de IVA no válido';
        } elseif ($cantidad < 0 || $precio < 0) {
            $mensajeError = 'Cantidad y precio deben ser valores positivos';
        } elseif ($this->model->existePorTitulo($titulo, $id)) {
            $mensajeError = 'Ya existe otro producto con este nombre';
        } else {
            // SRP: FileUploadService reemplaza el archivo
            $rutaFinal = $this->uploader->reemplazar(
                $_FILES['foto'] ?? [],
                $_POST['foto_actual'] ?? ''
            );

            if ($this->model->actualizar($id, $titulo, $rutaFinal, $descripcion, $cantidad, $iva, $precio)) {
                rotar_token_csrf();
                header('Location: ' . BASE_URL . '?module=productos&action=lista&updated=1');
                exit();
            }
            $mensajeError = 'Error al actualizar el producto';
        }

        // Retener datos del formulario en caso de error
        $producto = array_merge($producto, [
            'titulo' => $titulo, 'descripcion' => $descripcion,
            'cantidad' => $cantidad, 'iva' => $iva, 'precio' => $precio,
        ]);

        return compact('producto', 'mensajeError', 'csrf_token');
    }

    // ── ELIMINAR ──────────────────────────────────────────────────────────────
    public function eliminar(): void
    {
        verificar_autenticacion();

        $esAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        $responderError = function (string $msg, string $param) use ($esAjax): void {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $msg]);
                exit();
            }
            header('Location: ' . BASE_URL . '?module=productos&action=lista&error=' . $param);
            exit();
        };

        if (!validar_numero($_GET['id'] ?? '')) {
            $responderError('ID inválido', 'invalid_id');
        }

        $id      = (int)$_GET['id'];
        $producto = $this->model->buscarPorId($id);

        // Eliminar foto del disco si no está en uso en cotizaciones
        if ($producto && !empty($producto['foto']) && !$this->model->fotoEnUso($producto['foto'])) {
            $this->uploader->eliminarSiExiste($producto['foto']);
        }

        if ($this->model->eliminar($id)) {
            if ($esAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
                exit();
            }
            header('Location: ' . BASE_URL . '?module=productos&action=lista&deleted=1');
            exit();
        }

        $responderError('Error al eliminar', 'delete_failed');
    }
}
