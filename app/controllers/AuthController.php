<?php
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * AuthController — lógica de autenticación (login / logout).
 */
class AuthController {
    private UsuarioModel $model;

    public function __construct($conexion) {
        $this->model = new UsuarioModel($conexion);
    }

    /**
     * Procesa el login y devuelve datos para la vista.
     * Si el login es exitoso redirige a panel.php y termina.
     */
    public function login(): array {
        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        // Mensaje de sesión expirada
        if (isset($_GET['timeout'])) {
            $mensajeError = 'Su sesión ha expirado por inactividad. Por favor inicie sesión nuevamente.';
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return compact('mensajeError', 'csrf_token');
        }

        if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
            $mensajeError = 'Token de seguridad inválido. Por favor intente nuevamente.';
            return compact('mensajeError', 'csrf_token');
        }

        $correo    = sanitizar_entrada($_POST['correo'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        if ($correo === '' || $contrasena === '') {
            $mensajeError = 'Por favor complete todos los campos';
            return compact('mensajeError', 'csrf_token');
        }

        $usuario = $this->model->buscarPorCorreo($correo);

        if ($usuario && password_verify($contrasena, $usuario['password'])) {
            regenerar_sesion();
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['rol']            = $usuario['rol'];
            $_SESSION['LAST_ACTIVITY']  = time();

            sleep(1);
            header('Location: /PROYECTO_SODICOL/panel.php');
            exit();
        }

        $mensajeError = $usuario
            ? 'Correo o Contraseña Incorrectos'
            : 'Correo o Contraseña Incorrectos o Usuario Inactivo';

        return compact('mensajeError', 'csrf_token');
    }

    /**
     * Destruye la sesión y redirige al login.
     */
    public function logout(): void {
        iniciar_sesion_segura();
        session_unset();
        session_destroy();
        header('Location: /PROYECTO_SODICOL/index.php');
        exit();
    }
}
