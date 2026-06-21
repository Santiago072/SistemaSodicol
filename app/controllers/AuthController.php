<?php
require_once dirname(__DIR__) . '/models/UsuarioModel.php';
require_once dirname(__DIR__, 2) . '/config/seguridad.php';

/**
 * AuthController — lógica de autenticación (login / logout).
 *
 * Principios aplicados:
 *   - SRP: solo maneja autenticación.
 *   - Seguridad: rota el token CSRF post-login para prevenir replay attacks.
 */
class AuthController
{
    private UsuarioModel $model;

    public function __construct(\mysqli $conexion)
    {
        $this->model = new UsuarioModel($conexion);
    }

    /**
     * Procesa el login y devuelve datos para la vista.
     * Si el login es exitoso redirige al panel y termina.
     */
    public function login(): array
    {
        $mensajeError = '';
        $csrf_token   = generar_token_csrf();

        if (isset($_GET['timeout'])) {
            $mensajeError = 'Su sesión ha expirado por inactividad. Por favor inicie sesión nuevamente.';
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return compact('mensajeError', 'csrf_token');
        }

        // Prevención de ataques de fuerza bruta (15 peticiones por minuto)
        verificar_rate_limit(15, 60, 'login');

        $tokenPost = $_POST['csrf_token'] ?? '';
        if (!verificar_token_csrf($tokenPost)) {
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

            // Rotar token CSRF post-login (anti-replay)
            rotar_token_csrf();

            header('Location: ' . BASE_URL . '?module=panel');
            exit();
        }

        // Mensaje genérico para no revelar si el correo existe o no
        $mensajeError = 'Correo o contraseña incorrectos';

        return compact('mensajeError', 'csrf_token');
    }

    /**
     * Destruye la sesión y redirige al login.
     */
    public function logout(): void
    {
        iniciar_sesion_segura();
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL);
        exit();
    }
}
