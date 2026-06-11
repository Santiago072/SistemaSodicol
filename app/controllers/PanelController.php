<?php
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__) . '/models/TareaModel.php';
require_once dirname(__DIR__) . '/models/CotizacionModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * PanelController — lógica del dashboard principal.
 */
class PanelController {
    private UsuarioModel    $usuarioModel;
    private TareaModel      $tareaModel;
    private CotizacionModel $cotizacionModel;

    public function __construct($conexion) {
        $this->usuarioModel    = new UsuarioModel($conexion);
        $this->tareaModel      = new TareaModel($conexion);
        $this->cotizacionModel = new CotizacionModel($conexion);
    }

    public function index(): array {
        verificar_autenticacion();

        $usuario_id     = (int)$_SESSION['usuario_id'];
        $usuario_nombre = $_SESSION['usuario_nombre'];

        // Completar tarea (acción GET directa del panel)
        if (isset($_GET['completar_id']) && validar_numero($_GET['completar_id'])) {
            $this->tareaModel->completar(intval($_GET['completar_id']), $usuario_id);
            header('Location: /PROYECTO_SODICOL/?module=panel');
            exit();
        }

        $usuario               = $this->usuarioModel->buscarPorId($usuario_id);
        $total_administradores = $this->usuarioModel->contarAdmins();
        $total_usuarios_rol    = $this->usuarioModel->contar('') - $total_administradores;
        $total_cotizaciones    = $this->cotizacionModel->contarDelUsuario($usuario_nombre);
        $tareas_pendientes     = $this->tareaModel->listarPendientesDeUsuario($usuario_id);

        return compact(
            'usuario',
            'total_administradores',
            'total_usuarios_rol',
            'total_cotizaciones',
            'tareas_pendientes'
        );
    }

    public function ajaxCompletarTarea(): void {
        verificar_autenticacion();
        header('Content-Type: application/json');

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            exit();
        }

        $usuario_id = (int)$_SESSION['usuario_id'];
        $tarea_id   = (int)$_GET['id'];

        if ($this->tareaModel->completar($tarea_id, $usuario_id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al completar la tarea o no te pertenece']);
        }
        exit();
    }
}
