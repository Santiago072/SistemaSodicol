-- ─────────────────────────────────────────────────────────────
--  init.sql — Script de inicialización de MySQL
--  Sistema: SODICOL | Generado automáticamente desde BD.txt
--  Este archivo se ejecuta SOLO si el volumen mysql_data está vacío
-- ─────────────────────────────────────────────────────────────

SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Base de datos ─────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS sistema_sodicol
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_sodicol;

-- ── Tabla usuarios ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    documento  VARCHAR(20)  UNIQUE NOT NULL,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(100) UNIQUE NOT NULL,
    password   VARCHAR(255) NULL,
    telefono   VARCHAR(20),
    rol        ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado     ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabla cotizaciones ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cotizaciones (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    usuario_nombre     VARCHAR(255) NOT NULL,
    fecha_creacion     DATE DEFAULT (CURRENT_DATE),
    profesion          VARCHAR(255),
    nombre_cliente     VARCHAR(255),
    especialidad       VARCHAR(255),
    entidad            VARCHAR(255),
    ciudad             VARCHAR(255),
    numero_cotizacion  VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabla cotizacion_items ────────────────────────────────────
CREATE TABLE IF NOT EXISTS cotizacion_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id INT NOT NULL,
    titulo        VARCHAR(100) NOT NULL,
    foto          VARCHAR(255) DEFAULT NULL,
    descripcion   TEXT NOT NULL,
    cantidad      INT NOT NULL,
    iva           ENUM('si', 'no') NOT NULL DEFAULT 'si',
    precio        DECIMAL(20,2) NOT NULL,
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabla productos ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS productos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titulo      VARCHAR(255) NOT NULL,
    foto        VARCHAR(255) DEFAULT NULL,
    descripcion TEXT NOT NULL,
    cantidad    INT NOT NULL,
    iva         ENUM('si', 'no') NOT NULL DEFAULT 'si',
    precio      DECIMAL(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabla tareas ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tareas (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT NOT NULL,
    descripcion_tarea VARCHAR(255) NOT NULL,
    estado           ENUM('pendiente', 'completo') NOT NULL DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Usuario administrador inicial ─────────────────────────────
-- Contraseña: 1234567890 (número de documento, hasheada con bcrypt)
-- ⚠  CAMBIAR tras el primer acceso al sistema
INSERT INTO usuarios (documento, nombre, correo, password, telefono, rol, estado)
VALUES ('1234567890', 'Juan', 'admin@sodicol.com',
        '$2y$10$EOofArNIR1pB.DfysZAhzeB13ImopdK5BVeexCagCl/2jxBo4OMGK',
        '3000000000', 'admin', 'activo')
ON DUPLICATE KEY UPDATE id = id; -- idempotente: no falla si ya existe
