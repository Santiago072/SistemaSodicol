<?php
/**
 * conexion_example.php — Guía de configuración de la conexión a la BD
 *
 * INSTRUCCIONES PARA NUEVOS DESARROLLADORES:
 * ─────────────────────────────────────────────────────────────────
 * 1. Copia el archivo config/.env.example y renómbralo config/.env
 *    (o docker/.env si usas Docker):
 *
 *       cp config/.env.example config/.env
 *
 * 2. Edita config/.env y coloca tus credenciales reales:
 *
 *       DB_HOST=localhost
 *       DB_USER=tu_usuario
 *       DB_PASS=tu_contraseña
 *       DB_NAME=sistema_sodicol
 *
 * 3. El archivo config/conexion.php carga automáticamente ese .env
 *    a través de config/EnvLoader.php. No necesitas modificar nada más.
 *
 * ─────────────────────────────────────────────────────────────────
 * PARA DOCKER (producción):
 * ─────────────────────────────────────────────────────────────────
 * 1. Copia docker/.env.example y renómbralo docker/.env:
 *
 *       cp docker/.env.example docker/.env
 *
 * 2. Edita docker/.env con tus credenciales seguras.
 * 3. Levanta los contenedores:
 *
 *       cd docker
 *       docker compose up -d
 *
 * ─────────────────────────────────────────────────────────────────
 * ARQUITECTURA DE CONEXIÓN (para referencia):
 * ─────────────────────────────────────────────────────────────────
 *
 *   index.php
 *     └── config/EnvLoader.php   ← Carga el .env una sola vez (SRP)
 *     └── config/conexion.php    ← Crea y devuelve la conexión mysqli
 *           └── función conexion(): mysqli
 *
 * Todos los Controllers reciben la conexión por inyección:
 *
 *   $ctrl = new UsuarioController(conexion());
 *
 * ─────────────────────────────────────────────────────────────────
 * ⚠️  NUNCA subas config/.env al repositorio.
 *     Ya está incluido en .gitignore.
 * ─────────────────────────────────────────────────────────────────
 */

// Este archivo es solo documentación. No contiene código ejecutable.
// El archivo real de conexión es: config/conexion.php
