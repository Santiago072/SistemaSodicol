<?php
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * UsuarioController — toda la lógica de negocio de usuarios.
 * Devuelve arrays de datos que las vistas consumen.
 */
class UsuarioController {
    private UsuarioModel $model;
    private int $porPagina = 10;

    public function __construct($conexion) {
        $this->model = new UsuarioModel($conexion);
    }

    // ── LISTAR ──────────────────────────────────────────────
    public function listar(): array {
        verificar_admin();

        $busqueda   = '';
        $paginaActual = max(1, (int)($_GET['pagina'] ?? 1));
        $offset     = ($paginaActual - 1) * $this->porPagina;

        if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
            $busqueda = sanitizar_entrada($_GET['busqueda']);
        }

        $total    = $this->model->contar($busqueda);
        $usuarios = $this->model->listar($offset, $this->porPagina, $busqueda);
        $totalPaginas = (int)ceil($total / $this->porPagina);

        $mensajeExito = '';
        $mensajeError = '';
        if (isset($_GET['success']))  $mensajeExito = "Usuario creado exitosamente";
        if (isset($_GET['updated']))  $mensajeExito = "Usuario actualizado exitosamente";
        if (isset($_GET['deleted']))  $mensajeExito = "Usuario eliminado exitosamente";
        if (isset($_GET['error'])) {
            $map = [
                'last_admin'   => "No se puede eliminar el último administrador",
                'self_delete'  => "No puede eliminarse a sí mismo",
                'delete_failed'=> "Error al eliminar el usuario",
                'invalid_id'   => "ID de usuario inválido",
            ];
            $mensajeError = $map[$_GET['error']] ?? "Error al procesar la solicitud";
        }

        return compact('usuarios', 'busqueda', 'paginaActual', 'totalPaginas', 'total',
                        'mensajeExito', 'mensajeError');
    }

    // ── CREAR ────────────────────────────────────────────────
    public function crear(): array {
        verificar_admin();

        $mensajeError = '';
        $mensajeExito = '';
        $csrf_token   = generar_token_csrf();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $doc      = sanitizar_entrada($_POST['documento'] ?? '');
                $nombre   = sanitizar_entrada($_POST['nombre'] ?? '');
                $correo   = sanitizar_entrada($_POST['correo'] ?? '');
                $telefono = sanitizar_entrada($_POST['telefono'] ?? '');
                $rol      = sanitizar_entrada($_POST['rol'] ?? '');
                $password = $_POST['password'] ?? '';

                if (!$doc || !$nombre || !$correo || !$telefono || !$rol) {
                    $mensajeError = "Todos los campos son obligatorios";
                } elseif (!preg_match('/^\d{5,20}$/', $doc)) {
                    $mensajeError = "El documento debe ser numérico (5-20 dígitos)";
                } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
                    $mensajeError = "El nombre debe tener entre 3 y 100 caracteres";
                } elseif (!validar_email($correo)) {
                    $mensajeError = "El correo electrónico no es válido";
                } elseif (mb_strlen($correo) > 100) {
                    $mensajeError = "El correo no puede superar 100 caracteres";
                } elseif (!preg_match('/^\d{7,20}$/', $telefono)) {
                    $mensajeError = "El teléfono debe ser numérico (7-20 dígitos)";
                } elseif (!in_array($rol, ['admin', 'usuario'])) {
                    $mensajeError = "Rol no válido";
                } elseif (!empty($password) && mb_strlen($password) < 6) {
                    $mensajeError = "La contraseña debe tener al menos 6 caracteres";
                } elseif ($this->model->existeDocumentoOCorreo($doc, $correo)) {
                    $mensajeError = "El documento o correo ya está registrado";
                } else {
                    $hash = password_hash(!empty($password) ? $password : $doc, PASSWORD_DEFAULT);
                    if ($this->model->crear($doc, $nombre, $correo, $hash, $telefono, $rol)) {
                        header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&success=1");
                        exit();
                    }
                    $mensajeError = "Error al crear el usuario";
                }
            }
        }

        return compact('mensajeError', 'mensajeExito', 'csrf_token');
    }

    // ── EDITAR ───────────────────────────────────────────────
    public function editar(): array {
        verificar_admin();

        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista");
            exit();
        }

        $id      = intval($_GET['id']);
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
                $mensajeError = "Token de seguridad inválido";
            } else {
                $doc      = sanitizar_entrada($_POST['documento'] ?? '');
                $nombre   = sanitizar_entrada($_POST['nombre'] ?? '');
                $correo   = sanitizar_entrada($_POST['correo'] ?? '');
                $telefono = sanitizar_entrada($_POST['telefono'] ?? '');
                $rol      = sanitizar_entrada($_POST['rol'] ?? '');
                $estado   = sanitizar_entrada($_POST['estado'] ?? '');
                $nuevaPass= $_POST['nueva_password'] ?? '';

                if (!preg_match('/^\d{5,20}$/', $doc)) {
                    $mensajeError = "El documento debe ser numérico (5-20 dígitos)";
                } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
                    $mensajeError = "El nombre debe tener entre 3 y 100 caracteres";
                } elseif (!validar_email($correo)) {
                    $mensajeError = "El correo electrónico no es válido";
                } elseif (mb_strlen($correo) > 100) {
                    $mensajeError = "El correo no puede superar 100 caracteres";
                } elseif (!preg_match('/^\d{7,20}$/', $telefono)) {
                    $mensajeError = "El teléfono debe ser numérico (7-20 dígitos)";
                } elseif (!in_array($rol, ['admin', 'usuario'])) {
                    $mensajeError = "Rol no válido";
                } elseif (!in_array($estado, ['activo', 'inactivo'])) {
                    $mensajeError = "Estado no válido";
                } elseif (!empty($nuevaPass) && mb_strlen($nuevaPass) < 6) {
                    $mensajeError = "La contraseña debe tener al menos 6 caracteres";
                } elseif ($this->model->existeDocumentoOCorreo($doc, $correo, $id)) {
                    $mensajeError = "El documento o correo ya está registrado en otro usuario";
                } else {
                    $hash = !empty($nuevaPass) ? password_hash($nuevaPass, PASSWORD_DEFAULT) : null;
                    if ($this->model->actualizar($id, $doc, $nombre, $correo, $telefono, $rol, $estado, $hash)) {
                        header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&updated=1");
                        exit();
                    }
                    $mensajeError = "Error al actualizar";
                }

                // En caso de error, retener los datos enviados en el formulario
                $usuario['documento'] = $doc;
                $usuario['nombre']    = $nombre;
                $usuario['correo']    = $correo;
                $usuario['telefono']  = $telefono;
                $usuario['rol']       = $rol;
                $usuario['estado']    = $estado;
            }
        }

        return compact('usuario', 'mensajeError', 'csrf_token');
    }

    // ── ELIMINAR ─────────────────────────────────────────────
    public function eliminar(): void {
        verificar_admin();

        $esAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_GET['ajax']);

        if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
            if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'ID inválido']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&error=invalid_id");
            exit();
        }

        $id = intval($_GET['id']);

        if ($id === intval($_SESSION['usuario_id'])) {
            if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'No puedes eliminar tu propia cuenta']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&error=self_delete");
            exit();
        }

        if ($this->model->contarAdmins() <= 1 && $this->model->buscarPorId($id)['rol'] === 'admin') {
            if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar al último administrador']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&error=last_admin");
            exit();
        }

        if ($this->model->eliminar($id)) {
            if ($esAjax) { echo json_encode(['status' => 'success']); exit(); }
            header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&deleted=1");
            exit();
        }

        if ($esAjax) { echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']); exit(); }
        header("Location: /PROYECTO_SODICOL/?module=usuarios&action=lista&error=delete_failed");
        exit();
    }
}
