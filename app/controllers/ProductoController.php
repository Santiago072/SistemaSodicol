<?php
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * ProductoController — lógica de negocio del módulo de productos.
 */
class ProductoController {
    private ProductoModel $model;
    private int $porPagina = 10;

    public function __construct($conexion) {
        $this->model = new ProductoModel($conexion);
    }

    // ── LISTAR ──────────────────────────────────────────────
    public function listar(): array {
        verificar_autenticacion();

        $busqueda     = '';
        $paginaActual = max(1, (int)($_GET['pagina'] ?? 1));
        $offset       = ($paginaActual - 1) * $this->porPagina;

        if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
            $busqueda = sanitizar_entrada($_GET['busqueda']);
        }

        $total        = $this->model->contar($busqueda);
        $productos    = $this->model->listar($offset, $this->porPagina, $busqueda);
        $totalPaginas = (int)ceil($total / $this->porPagina);

        $mensajeExito = '';
        $mensajeError = '';
        if (isset($_GET['success'])) $mensajeExito = "Producto creado exitosamente";
        if (isset($_GET['updated'])) $mensajeExito = "Producto actualizado exitosamente";
        if (isset($_GET['deleted'])) $mensajeExito = "Producto eliminado exitosamente";
        if (isset($_GET['error'])) {
            $map = [
                'en_uso' => "No se puede eliminar: el producto está asociado a cotizaciones existentes.",
                'default'=> "Error al procesar la solicitud",
            ];
            $mensajeError = $map[$_GET['error']] ?? $map['default'];
        }

        return compact('productos', 'busqueda', 'paginaActual', 'totalPaginas', 'total',
                        'mensajeExito', 'mensajeError');
    }

    // ── EDITAR ───────────────────────────────────────────────
    public function editar(): array {
        verificar_autenticacion();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&error=default");
            exit();
        }

        $id      = intval($_GET['id']);
        $producto = $this->model->buscarPorId($id);
        if (!$producto) {
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&error=default");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $titulo      = sanitizar_entrada($_POST['titulo'] ?? '');
                $descripcion = sanitizar_entrada($_POST['descripcion'] ?? '');
                $cantidad    = intval($_POST['cantidad'] ?? 0);
                $iva         = sanitizar_entrada($_POST['iva'] ?? '');
                $precio      = floatval($_POST['precio'] ?? 0);

                if (!in_array($iva, ['si', 'no'])) {
                    $mensajeError = "Valor de IVA no válido";
                } elseif ($cantidad < 0 || $precio < 0) {
                    $mensajeError = "Cantidad y precio deben ser valores positivos";
                } elseif ($this->model->existePorTitulo($titulo, $id)) {
                    $mensajeError = "Ya existe otro producto con este nombre";
                } else {
                    $rutaFinal = $_POST['foto_actual'] ?? '';

                    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                        $validacion = validar_imagen($_FILES['foto']);
                        if ($validacion['valido']) {
                            $ext    = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                            $nombre = generar_nombre_archivo($ext);
                            $dir    = dirname(__DIR__, 2) . '/uploads/';
                            if (!is_dir($dir)) mkdir($dir, 0755, true);
                            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nombre)) {
                                if (!empty($_POST['foto_actual'])) {
                                    $anterior = $dir . basename($_POST['foto_actual']);
                                    if (file_exists($anterior)) unlink($anterior);
                                }
                                $rutaFinal = $nombre;
                            } else {
                                $mensajeError = "Error al subir el archivo";
                            }
                        } else {
                            $mensajeError = $validacion['mensaje'];
                        }
                    } else {
                        $rutaFinal = basename($_POST['foto_actual'] ?? '');
                    }

                    if (empty($mensajeError)) {
                        if ($this->model->actualizar($id, $titulo, $rutaFinal, $descripcion, $cantidad, $iva, $precio)) {
                            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&updated=1");
                            exit();
                        }
                        $mensajeError = "Error al actualizar el producto";
                    }
                }
            }
        }

        return compact('producto', 'mensajeError', 'csrf_token');
    }

    // ── ELIMINAR ─────────────────────────────────────────────
    public function eliminar(): void {
        verificar_autenticacion();

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&error=default");
            exit();
        }

        $id      = intval($_GET['id']);
        $producto = $this->model->buscarPorId($id);

        // Verificar dependencias con cotizaciones
        if ($this->model->tieneEnCotizaciones($id)) {
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&error=en_uso");
            exit();
        }

        if ($producto && $this->model->eliminar($id)) {
            // Eliminar imagen del servidor
            if (!empty($producto['foto'])) {
                $ruta = dirname(__DIR__, 2) . '/uploads/' . $producto['foto'];
                if (file_exists($ruta)) unlink($ruta);
            }
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&deleted=1");
        } else {
            header("Location: /PROYECTO_SODICOL/?module=productos&action=lista&error=default");
        }
        exit();
    }
}
