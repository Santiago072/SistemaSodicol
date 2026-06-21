<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SeguridadTest extends TestCase
{
    protected function setUp(): void
    {
        // Asegurar que la sesión esté iniciada para las pruebas
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Cargar archivo de seguridad si no está cargado
        if (!function_exists('generar_token_csrf')) {
            require_once __DIR__ . '/../../config/seguridad.php';
        }
    }

    protected function tearDown(): void
    {
        // Limpiar la sesión después de cada prueba
        session_unset();
    }

    public function test_generar_token_csrf_crea_un_token_valido()
    {
        $token = generar_token_csrf();
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // bin2hex de 32 bytes = 64 chars
        $this->assertEquals($token, $_SESSION['csrf_token']);
    }

    public function test_verificar_token_csrf_falla_con_token_invalido()
    {
        generar_token_csrf();
        
        $this->assertFalse(verificar_token_csrf('token_falso'));
    }

    public function test_verificar_token_csrf_pasa_con_token_valido()
    {
        $token = generar_token_csrf();
        
        $this->assertTrue(verificar_token_csrf($token));
    }

    public function test_rotar_token_csrf_genera_uno_nuevo()
    {
        $tokenAntiguo = generar_token_csrf();
        $tokenNuevo = rotar_token_csrf();
        
        $this->assertNotEquals($tokenAntiguo, $tokenNuevo);
        $this->assertEquals($tokenNuevo, $_SESSION['csrf_token']);
    }

    public function test_sanitizar_entrada_elimina_espacios_y_barras()
    {
        $entrada = "  test \\ data  ";
        $esperado = "test  data"; // stripslashes quita la barra, trim quita los espacios extremos
        
        $this->assertEquals($esperado, sanitizar_entrada($entrada));
    }

    public function test_escapar_salida_convierte_caracteres_especiales()
    {
        $entrada = "<script>alert('xss');</script>";
        $esperado = "&lt;script&gt;alert(&#039;xss&#039;);&lt;/script&gt;";
        
        $this->assertEquals($esperado, escapar_salida($entrada));
    }

    public function test_verificar_rate_limit_registra_peticion()
    {
        $limite = 5;
        $ventana = 60;
        $accion = 'test_rate';
        $clave = "rate_limit_" . $accion;

        // Limpiar
        $_SESSION[$clave] = [];

        verificar_rate_limit($limite, $ventana, $accion);
        
        $this->assertCount(1, $_SESSION[$clave]);
    }
}
