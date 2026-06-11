<?php
require_once dirname(__DIR__) . '/models/TareaModel.php';
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * TareaController — lógica de negocio del módulo de tareas.
 */
class TareaController {
    private TareaModel $model;
    private UsuarioModel $usuarioModel;
    private int $porPagina = 10;

    public function __construct($conexion) {
        $this->model        = new TareaModel($conexion);
        $this->usuarioModel = new UsuarioModel($conexion);
    }

    // ── LISTAR + CREAR ───────────────────────────────────────
    public function gestion(): array {
        verificar_admin();

        $paginaActual = max(1, (int)($_GET['pagina'] ?? 1));
        $offset       = ($paginaActual - 1) * $this->porPagina;
        $total        = $this->model->contarTodas();
        $totalPaginas = (int)ceil($total / $this->porPagina);

        $usuarios  = $this->usuarioModel->listarActivos();
        $tareas    = $this->model->listarTodas($offset, $this->porPagina);
        $csrf_token = generar_token_csrf();

        $mensajeExito = '';
        $mensajeError = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=csrf");
                exit();
            }

            $usuarioId   = intval($_POST['usuario'] ?? 0);
            $descripcion = sanitizar_entrada($_POST['descripcion_tarea'] ?? '');
            $estado      = sanitizar_entrada($_POST['estado'] ?? '');

            if (!in_array($estado, ['pendiente', 'completo'])) {
                header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=estado");
                exit();
            }

            if ($this->model->crear($usuarioId, $descripcion, $estado)) {
                header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&success=1");
            } else {
                header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=insert");
            }
            exit();
        }

        if (isset($_GET['success'])) $mensajeExito = "Tarea creada correctamente.";
        if (isset($_GET['updated'])) $mensajeExito = "Tarea actualizada correctamente.";
        if (isset($_GET['deleted'])) $mensajeExito = "Tarea eliminada correctamente.";
        if (isset($_GET['error'])) {
            $map = [
                'csrf'   => 'Token de seguridad inválido',
                'estado' => 'Estado no válido',
                'insert' => 'Error al crear la tarea',
            ];
            $mensajeError = $map[$_GET['error']] ?? 'Error al procesar la solicitud';
        }

        return compact('usuarios', 'tareas', 'csrf_token', 'paginaActual',
                        'totalPaginas', 'total', 'mensajeExito', 'mensajeError');
    }

    // ── EDITAR ───────────────────────────────────────────────
    public function editar(): array {
        verificar_admin();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=invalid_id");
            exit();
        }

        $id    = intval($_GET['id']);
        $tarea = $this->model->buscarPorId($id);
        if (!$tarea) {
            header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=not_found");
            exit();
        }

        $usuarios = $this->usuarioModel->listarActivos();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $usuarioId   = intval($_POST['usuario_id'] ?? 0);
                $descripcion = sanitizar_entrada($_POST['descripcion_tarea'] ?? '');
                $estado      = sanitizar_entrada($_POST['estado'] ?? '');

                if (!in_array($estado, ['pendiente', 'completo'])) {
                    $mensajeError = "Estado no válido";
                } elseif ($this->model->actualizar($id, $usuarioId, $descripcion, $estado)) {
                    header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&updated=1");
                    exit();
                } else {
                    $mensajeError = "Error al actualizar la tarea";
                }
            }
        }

        return compact('tarea', 'usuarios', 'mensajeError', 'csrf_token');
    }

    // ── ELIMINAR ─────────────────────────────────────────────
    public function eliminar(): void {
        verificar_admin();

        $esAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_GET['ajax']);

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'ID inválido']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=invalid_id");
            exit();
        }

        $id = intval($_GET['id']);
        if ($this->model->eliminar($id)) {
            if ($esAjax) { echo json_encode(['status' => 'success']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&deleted=1");
            exit();
        }
        
        if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']); exit(); }
        header("Location: /PROYECTO_SODICOL/?module=tareas&action=gestion&error=delete_failed");
        exit();
    }
}
