<?php
declare(strict_types=1);

/**
 * bootstrap.php — Punto de arranque de la suite de pruebas PHPUnit.
 *
 * Responsabilidades:
 *   1. Definir las constantes globales que el sistema espera.
 *   2. Cargar el autoloader de Composer (PHPUnit y dependencias).
 *   3. Cargar las funciones globales de seguridad que se testean directamente.
 *
 * NO conecta a ninguna base de datos. Los tests que necesitan
 * acceso a datos usan mocks de mysqli.
 */

// ── Constantes requeridas por los controladores ────────────────────────────────
define('BASE_URL', 'http://localhost/');

// ── Autoloader de Composer ─────────────────────────────────────────────────────
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

// ── Funciones globales del sistema ─────────────────────────────────────────────
// seguridad.php define funciones procedurales (no clases), por eso se carga aquí
// y no a través de autoload.
require_once dirname(__DIR__) . '/config/seguridad.php';

// ── Contratos e interfaces ──────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/contracts/RepositoryInterface.php';

// ── Modelos ────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/models/UsuarioModel.php';

// ── Controladores ──────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/controllers/AuthController.php';
