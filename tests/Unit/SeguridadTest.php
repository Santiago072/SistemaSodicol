<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * SeguridadTest — Pruebas unitarias de config/seguridad.php
 *
 * Cubre las funciones procedurales de seguridad sin necesidad de base de datos.
 * Todas las funciones testeadas aquí son funciones puras o trabajan
 * únicamente con variables de superglobales ($_SESSION).
 *
 * Grupos de pruebas:
 *   - sanitizar_entrada(): limpieza de texto de entrada
 *   - escapar_salida(): codificación HTML para vistas
 *   - validar_email(): validación de formato de correo electrónico
 *   - validar_numero(): validación de números positivos
 *   - Funciones CSRF: generación, verificación y rotación de tokens
 */
class SeguridadTest extends TestCase
{
    protected function setUp(): void
    {
        // Asegurar que $_SESSION existe como array para los tests CSRF.
        // En CLI (PHPUnit), no se necesita session_start() para usar $_SESSION directamente.
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }
        // Limpiar el token CSRF antes de cada test para garantizar aislamiento.
        unset($_SESSION['csrf_token']);
    }

    // ── sanitizar_entrada() ────────────────────────────────────────────────────

    /** Elimina espacios en ambos extremos de la cadena */
    public function testSanitizarEntradaEliminaEspaciosExtremos(): void
    {
        $this->assertSame('hola mundo', sanitizar_entrada('  hola mundo  '));
    }

    /** Elimina barras invertidas (protección contra magic_quotes_gpc legado) */
    public function testSanitizarEntradaEliminaBarrasInvertidas(): void
    {
        $this->assertSame("O'Brien", sanitizar_entrada("O\\'Brien"));
    }

    /**
     * NO debe aplicar htmlspecialchars: los datos se guardan en BD sin codificar.
     * El escape HTML es responsabilidad exclusiva de escapar_salida() en las vistas.
     */
    public function testSanitizarEntradaNoAplicaHtmlspecialchars(): void
    {
        $this->assertSame('<script>alert(1)</script>', sanitizar_entrada('<script>alert(1)</script>'));
    }

    /** Cadena vacía debe devolver cadena vacía sin errores */
    public function testSanitizarEntradaCadenaVacia(): void
    {
        $this->assertSame('', sanitizar_entrada(''));
    }

    /** Cadena que solo tiene espacios debe devolver cadena vacía */
    public function testSanitizarEntradaSoloEspacios(): void
    {
        $this->assertSame('', sanitizar_entrada('   '));
    }

    // ── escapar_salida() ───────────────────────────────────────────────────────

    /** Codifica etiquetas HTML para prevenir XSS en vistas */
    public function testEscaparSalidaCodificaEtiquetasHtml(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            escapar_salida('<script>alert(1)</script>')
        );
    }

    /** Codifica comillas dobles para atributos HTML seguros */
    public function testEscaparSalidaCodificaComillasDobles(): void
    {
        $this->assertSame('&quot;hola&quot;', escapar_salida('"hola"'));
    }

    /** Codifica el ampersand para prevenir entidades HTML malformadas */
    public function testEscaparSalidaCodificaAmpersand(): void
    {
        $this->assertSame('Tom &amp; Jerry', escapar_salida('Tom & Jerry'));
    }

    /** Cadena sin caracteres especiales no debe modificarse */
    public function testEscaparSalidaCadenaNormal(): void
    {
        $this->assertSame('texto normal 123', escapar_salida('texto normal 123'));
    }

    // ── validar_email() ────────────────────────────────────────────────────────

    /** Formato de correo estándar debe ser válido */
    public function testValidarEmailValido(): void
    {
        $this->assertTrue(validar_email('usuario@dominio.com'));
    }

    /** Correo con subdominio también debe ser válido */
    public function testValidarEmailConSubdominio(): void
    {
        $this->assertTrue(validar_email('user@mail.empresa.co'));
    }

    /** Sin arroba el formato es inválido */
    public function testValidarEmailSinArroba(): void
    {
        $this->assertFalse(validar_email('usuariodominio.com'));
    }

    /** Cadena vacía es inválida */
    public function testValidarEmailVacio(): void
    {
        $this->assertFalse(validar_email(''));
    }

    // ── validar_numero() ───────────────────────────────────────────────────────

    /** Un entero positivo debe ser válido */
    public function testValidarNumeroEnteroPositivo(): void
    {
        $this->assertTrue(validar_numero(10));
    }

    /** Un decimal positivo también debe ser válido */
    public function testValidarNumeroDecimalPositivo(): void
    {
        $this->assertTrue(validar_numero(3.14));
    }

    /** El cero no es un número positivo válido */
    public function testValidarNumeroCeroEsInvalido(): void
    {
        $this->assertFalse(validar_numero(0));
    }

    /** Un número negativo es inválido */
    public function testValidarNumeroNegativoEsInvalido(): void
    {
        $this->assertFalse(validar_numero(-5));
    }

    // ── Funciones CSRF ─────────────────────────────────────────────────────────

    /** El token generado debe ser una cadena hexadecimal de 64 caracteres (32 bytes) */
    public function testGenerarTokenCsrfRetornaCadenaHexadecimal(): void
    {
        $token = generar_token_csrf();
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    /** Llamar dos veces debe retornar el mismo token (idempotente) */
    public function testGenerarTokenCsrfEsIdempotente(): void
    {
        $token1 = generar_token_csrf();
        $token2 = generar_token_csrf();
        $this->assertSame($token1, $token2);
    }

    /** El token correcto debe verificar exitosamente */
    public function testVerificarTokenCsrfTokenCorrecto(): void
    {
        $token = generar_token_csrf();
        $this->assertTrue(verificar_token_csrf($token));
    }

    /** Un token fabricado por un atacante debe fallar la verificación */
    public function testVerificarTokenCsrfTokenFalsoFalla(): void
    {
        generar_token_csrf(); // Genera y almacena en $_SESSION
        $this->assertFalse(verificar_token_csrf('token_fabricado_por_atacante'));
    }

    /** Sin token en sesión la verificación debe fallar */
    public function testVerificarTokenCsrfSinSesionFalla(): void
    {
        unset($_SESSION['csrf_token']); // Asegurar que no hay token en sesión
        $this->assertFalse(verificar_token_csrf('cualquier_token'));
    }

    /** Rotar el token debe producir un token diferente al original */
    public function testRotarTokenCsrfCambiaElToken(): void
    {
        $tokenOriginal = generar_token_csrf();
        $tokenNuevo    = rotar_token_csrf();

        $this->assertNotSame($tokenOriginal, $tokenNuevo);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $tokenNuevo);
    }
}
