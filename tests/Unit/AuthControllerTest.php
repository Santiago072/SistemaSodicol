<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * AuthControllerTest — Pruebas unitarias de AuthController.
 *
 * Cubre los flujos que NO requieren consulta real a la base de datos:
 *   - Petición GET (mostrar formulario de login con csrf_token)
 *   - Petición GET con parámetro ?timeout=1 (sesión expirada)
 *   - POST con token CSRF inválido (rechazado antes de consultar la BD)
 *   - POST con campos vacíos (rechazado por validación temprana)
 *
 * Estrategia de aislamiento:
 *   - Se usa createMock(mysqli::class) para instanciar el controlador
 *     sin una conexión real. Los flujos testeados retornan antes de que
 *     UsuarioModel ejecute cualquier consulta SQL.
 *   - La sesión PHP se abre UNA SOLA VEZ en setUp() y se limpia entre
 *     tests (evita el conflicto de módulo openssl en XAMPP que ocurre
 *     al usar @runInSeparateProcess).
 *
 * Los flujos que requieren BD (login exitoso, login fallido con correo real)
 * son candidatos para pruebas de integración con BD de test.
 */
class AuthControllerTest extends TestCase
{
    private AuthController $controller;

    protected function setUp(): void
    {
        // Arrancar la sesión una sola vez por proceso PHPUnit.
        // verificar_rate_limit() la necesita; no usamos @runInSeparateProcess
        // para evitar el conflicto de carga doble de openssl en XAMPP.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar datos de sesión antes de cada test (aislamiento)
        $_SESSION = [];

        // Mock de mysqli: no abre conexión real
        $mockMysqli = $this->createMock(mysqli::class);
        $this->controller = new AuthController($mockMysqli);

        // Estado limpio de superglobales
        $_GET  = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_GET     = [];
        $_POST    = [];
        $_SESSION = [];
    }

    // ── Petición GET ───────────────────────────────────────────────────────────

    /**
     * Una petición GET al login debe retornar un array con los datos
     * necesarios para renderizar el formulario: csrf_token y mensajeError vacío.
     */
    public function testLoginGetDevuelveArrayConCsrfTokenYSinError(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $resultado = $this->controller->login();

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('csrf_token', $resultado);
        $this->assertArrayHasKey('mensajeError', $resultado);
        $this->assertSame('', $resultado['mensajeError']);
        $this->assertNotEmpty($resultado['csrf_token']);
    }

    /**
     * GET con ?timeout=1 debe retornar el mensaje de sesión expirada.
     * Este parámetro lo agrega iniciar_sesion_segura() al hacer logout por inactividad.
     */
    public function testLoginGetConTimeoutDevuelveMensajeDeExpiracion(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['timeout'] = '1';

        $resultado = $this->controller->login();

        $this->assertArrayHasKey('mensajeError', $resultado);
        $this->assertStringContainsString('expirado', $resultado['mensajeError']);
    }

    // ── Petición POST — validaciones sin consulta a BD ─────────────────────────

    /**
     * POST con token CSRF inválido debe ser rechazado inmediatamente.
     * El mensaje debe ser genérico para no revelar detalles de implementación.
     */
    public function testLoginPostCsrfInvalidoDevuelveMensajeDeError(): void
    {
        $_SESSION['csrf_token']    = 'token_real_en_sesion';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'csrf_token' => 'token_falso_del_atacante',
            'correo'     => 'test@example.com',
            'contrasena' => 'password123',
        ];

        $resultado = $this->controller->login();

        $this->assertStringContainsString('Token de seguridad inválido', $resultado['mensajeError']);
    }

    /**
     * POST con correo y contraseña vacíos debe ser rechazado por validación
     * antes de consultar la base de datos.
     * El CSRF debe ser válido para llegar a esta validación.
     */
    public function testLoginPostCamposVaciosDevuelveMensajeDeValidacion(): void
    {
        // Generar un token CSRF válido en sesión
        $_SESSION['csrf_token']    = bin2hex(random_bytes(32));
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'csrf_token' => $_SESSION['csrf_token'],
            'correo'     => '',
            'contrasena' => '',
        ];

        $resultado = $this->controller->login();

        $this->assertStringContainsString('complete todos los campos', $resultado['mensajeError']);
    }
}
