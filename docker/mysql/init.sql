CREATE DATABASE IF NOT EXISTS sistema_sodicol;
USE sistema_sodicol;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    telefono VARCHAR(20),
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla principal de cotizaciones
CREATE TABLE IF NOT EXISTS cotizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_nombre VARCHAR(255) NOT NULL,
    fecha_creacion DATE DEFAULT (CURRENT_DATE),
    profesion VARCHAR(255),
    nombre_cliente VARCHAR(255),
    especialidad VARCHAR(255),
    entidad VARCHAR(255),
    ciudad VARCHAR(255),
    numero_cotizacion VARCHAR(50) DEFAULT NULL  
);

-- Tabla de ítems de cada cotización
CREATE TABLE IF NOT EXISTS cotizacion_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    descripcion TEXT NOT NULL,
    cantidad INT NOT NULL,
    iva ENUM('si', 'no') NOT NULL DEFAULT 'si',
    precio DECIMAL(20,2) NOT NULL,
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id)
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    descripcion TEXT NOT NULL,
    cantidad INT NOT NULL,
    iva ENUM('si', 'no') NOT NULL DEFAULT 'si',
    precio DECIMAL(20,2) NOT NULL
);

-- Tabla de Tareas(Cotizaciones) para Empleados
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    descripcion_tarea VARCHAR(255) NOT NULL,
    estado ENUM('pendiente', 'completo') NOT NULL DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Insertar Productos
INSERT INTO `productos` (`id`, `titulo`, `foto`, `descripcion`, `cantidad`, `iva`, `precio`) VALUES
(1, 'ESTACIÓN 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(2, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(3, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(4, 'MODULO AÉREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \r\nMueble totalmente en aglomerado PELIKANO RH 15mm \r\nCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \r\nuna (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \r\nNIQUEL CEP CC. 96 MM y brazos hidráulico en las puertas.', 6, 'si', 1420000.00),
(5, 'MÓDULO AÉREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de\r\n15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de cuatro (4) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 2, 'si', 1400000.00),
(6, 'ESCRITORIO SEN', '1775429485_Item6.png', 'De 950mm x 550mm. En su área de trabajo x por 720mm de\r\nalto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mmCANTO RIGIDO ORANGE 19MM –\r\n33MM termo fundido. ', 1, 'si', 750000.00),
(7, 'MODULO AÉREO 500', '1775429582_Item7.png', 'De 550mm ancho x 500mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de una (1) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 1, 'si', 950000.00),
(8, 'MODULO INFERIOR 2070', '1775429700_Item8.png', 'De 720mm de alto x 2070 mm ancho x 520mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido...\r\n', 1, 'si', 2330000.00),
(9, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(10, 'DIVISIÓN CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(11, 'ESCRITORIO LINEAL 2400', '1775430117_Item11.png', 'De 2400mm x 650mm. En su área de trabajo x por 720mm\r\nde alto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mm superficie reforzada con nariz por\r\nsus 4 lados CANTO RIGIDO ORANGE 19MM – 33MM\r\ntermo fundido. ', 1, 'si', 2200000.00),
(12, 'LOQUER 885 ', '1775430262_Item12.png', 'Módulo de tres puestos de 885mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ntres puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...', 4, 'si', 1080000.00),
(13, 'LOQUER 595', '1775430367_Item13.png', 'Módulo de dos puestos de 595mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ndos puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 4, 'si', 820000.00),
(14, 'MODULO AÉREO 750', '1775430467_Item14.png', 'De 750mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nconta de consta de dos puertas con sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 2, 'si', 1400000.00),
(15, 'MODULO MESON 1500', '1775430553_Item15.png', 'De 1500mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble en aglomerado PELIKANO RH FRESNO y BLANCO\r\nde 15mm y superficie en PVC, consta de cuatro puertas con\r\nsus respectivas manijas, CANTO RIGIDO y PVC 19 MM\r\ntermo fundido...\r\n', 1, 'si', 2650000.00),
(16, 'MÓDULO SUPERIOR 1480', '1775430760_Item16.png', 'De 570 mm de alto x 1480 mm ancho x 400mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH FRESNO\r\nde 15mm CANTO RIGIDO ORANGE MACADAMIA19MM\r\ntermo fundido...\r\n', 1, 'si', 400000.00),
(17, 'MODULO ESTANTE 1270', '1775430858_Item17.png', 'De 730mm ancho x 1270mm alto x por 300mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH color\r\nblanco de 15mm, PATA 697 ACERO 201 CON NIVELADOR\r\n40MM (HPA697-04) CANTO RIGIDO ORANGE blanco\r\n19MM – 33MM termo fundido.', 4, 'si', 1400000.00),
(18, 'MODULO GAVETERO 730', '1775430911_Item18.png', 'De 730mm de alto x 700mm de ancho y\r\n550mm de fondo. Mueble totalmente en\r\naglomerado PELIKANO FRESNO de 15mm\r\nConsta de tres (3) Gavetas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; RIEL EXT TOTAL ZINC\r\nPESADO 350MMM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO color PLOMO 19MM –\r\n33MM termo fundido...', 1, 'si', 1720000.00);

-- Insertar Usuarios
INSERT INTO `usuarios` (`id`, `documento`, `nombre`, `correo`, `password`, `telefono`, `rol`, `estado`) VALUES
(1, '1234567890', 'Admin', 'admin@sodicol.com', '$2y$10$B/KHnf1uTy0vzXlQOAyJmeX20jOdvTYSlk.uzp0tTM/SotZu8OSqW', '3000000000', 'admin', 'activo'),
(2, '1118367962', 'Santiago ', 'santiago@gmail.com', '$2y$10$B/KHnf1uTy0vzXlQOAyJmeX20jOdvTYSlk.uzp0tTM/SotZu8OSqW', '3217235089', 'usuario', 'activo'),
(4, '12345', 'Fabian Ramos', 'fabian@gmail.com', '$2y$10$B/KHnf1uTy0vzXlQOAyJmeX20jOdvTYSlk.uzp0tTM/SotZu8OSqW', '3190986753', 'usuario', 'activo'),
(6, '32', 'Santiago', 'santiagolizcanosuarez@gmail.com', '$2y$10$B/KHnf1uTy0vzXlQOAyJmeX20jOdvTYSlk.uzp0tTM/SotZu8OSqW', '3148440088', 'usuario', 'activo');
