-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-06-2026 a las 00:25:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_sodicol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `id` int(11) NOT NULL,
  `usuario_nombre` varchar(255) NOT NULL,
  `fecha_creacion` date DEFAULT curdate(),
  `profesion` varchar(255) DEFAULT NULL,
  `nombre_cliente` varchar(255) DEFAULT NULL,
  `especialidad` varchar(255) DEFAULT NULL,
  `entidad` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `numero_cotizacion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id`, `usuario_nombre`, `fecha_creacion`, `profesion`, `nombre_cliente`, `especialidad`, `entidad`, `ciudad`, `numero_cotizacion`) VALUES
(1, 'Juan', '2026-03-28', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026032801'),
(2, 'Juan', NULL, '', '', '', '', '', '2026032802'),
(3, 'Juan', '2026-03-29', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026032903'),
(4, 'Juan', '2026-03-29', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026032905'),
(5, 'Santiago ', '2026-03-29', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026032904'),
(6, 'Santiago ', '2026-05-23', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026052301'),
(7, 'Juan', '2026-03-30', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026033006'),
(8, 'Juan', '2026-03-30', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026033007'),
(9, 'Juan', '2026-04-05', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026040501'),
(10, 'Juan', '2026-04-05', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026040502'),
(11, 'Juan', '2026-04-09', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026040903'),
(12, 'Juan', '2026-04-09', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026040904'),
(13, 'Juan', '2026-04-24', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026042405'),
(14, 'Juan', '2026-06-10', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026061001'),
(15, 'Santiago ', '2026-05-23', 'Doctora', 'CINDY TATIANA VARGAS TORO.', 'Gerente', 'Hospital Departamental María Inmaculada.', 'Florencia', '2026052302'),
(16, 'Santiago ', '2026-05-23', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Juan', '2026-06-10', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_items`
--

CREATE TABLE `cotizacion_items` (
  `id` int(11) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `cantidad` int(11) NOT NULL,
  `iva` enum('si','no') NOT NULL DEFAULT 'si',
  `precio` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizacion_items`
--

INSERT INTO `cotizacion_items` (`id`, `cotizacion_id`, `titulo`, `foto`, `descripcion`, `cantidad`, `iva`, `precio`) VALUES
(1, 1, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(2, 1, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(3, 1, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(4, 3, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(5, 5, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(6, 5, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(7, 4, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(8, 7, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(9, 8, 'MODULO AEREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \r\nMueble totalmente en aglomerado PELIKANO RH 15mm \r\nCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \r\nuna (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \r\nNIQUEL CEP CC. 96 MM y brazos hidráulico en las puertas.', 6, 'si', 1420000.00),
(10, 8, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(11, 9, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(12, 9, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(13, 9, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(14, 9, 'MODULO AEREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \r\nMueble totalmente en aglomerado PELIKANO RH 15mm \r\nCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \r\nuna (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \r\nNIQUEL CEP CC. 96 MM y brazos hidráulico en las puertas.', 6, 'si', 1420000.00),
(15, 9, 'MÓDULO AEREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de\r\n15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de cuatro (4) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 2, 'si', 1400000.00),
(16, 9, 'ESCRITORIO SEN', '1775429485_Item6.png', 'De 950mm x 550mm. En su área de trabajo x por 720mm de\r\nalto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mmCANTO RIGIDO ORANGE 19MM –\r\n33MM termo fundido. ', 1, 'si', 750000.00),
(17, 9, 'MODULO AEREO 500', '1775429582_Item7.png', 'De 550mm ancho x 500mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de una (1) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 1, 'si', 950000.00),
(18, 9, 'MODULO INFERIOR 2070', '1775429700_Item8.png', 'De 720mm de alto x 2070 mm ancho x 520mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido...\r\n', 1, 'si', 2330000.00),
(19, 9, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(20, 9, 'DIVISIÓN CON PUERTA 2630', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(21, 9, 'ESCRITORIO LINEAL 2400', '1775430117_Item11.png', 'De 2400mm x 650mm. En su área de trabajo x por 720mm\r\nde alto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mm superficie reforzada con nariz por\r\nsus 4 lados CANTO RIGIDO ORANGE 19MM – 33MM\r\ntermo fundido. ', 1, 'si', 2200000.00),
(22, 9, 'LOQUER 885 ', '1775430262_Item12.png', 'Módulo de tres puestos de 885mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ntres puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...', 4, 'si', 1080000.00),
(23, 9, 'LOQUER 595', '1775430367_Item13.png', 'Módulo de dos puestos de 595mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ndos puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 4, 'si', 820000.00),
(24, 9, 'MODULO AREO 750', '1775430467_Item14.png', 'De 750mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nconta de consta de dos puertas con sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 2, 'si', 1400000.00),
(25, 9, 'MODULO MESON 1500', '1775430553_Item15.png', 'De 1500mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble en aglomerado PELIKANO RH FRESNO y BLANCO\r\nde 15mm y superficie en PVC, consta de cuatro puertas con\r\nsus respectivas manijas, CANTO RIGIDO y PVC 19 MM\r\ntermo fundido...\r\n', 1, 'si', 2650000.00),
(26, 9, 'MÓDULO SUPERIOR 1480', '1775430760_Item16.png', 'De 570 mm de alto x 1480 mm ancho x 400mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH FRESNO\r\nde 15mm CANTO RIGIDO ORANGE MACADAMIA19MM\r\ntermo fundido...\r\n', 1, 'si', 400000.00),
(27, 9, 'MODULO ESTANTE 1270', '1775430858_Item17.png', 'De 730mm ancho x 1270mm alto x por 300mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH color\r\nblanco de 15mm, PATA 697 ACERO 201 CON NIVELADOR\r\n40MM (HPA697-04) CANTO RIGIDO ORANGE blanco\r\n19MM – 33MM termo fundido.', 4, 'si', 1400000.00),
(28, 9, 'MODULO GAVETERO 730', '1775430911_Item18.png', 'De 730mm de alto x 700mm de ancho y\r\n550mm de fondo. Mueble totalmente en\r\naglomerado PELIKANO FRESNO de 15mm\r\nConsta de tres (3) Gavetas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; RIEL EXT TOTAL ZINC\r\nPESADO 350MMM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO color PLOMO 19MM –\r\n33MM termo fundido...', 1, 'si', 1720000.00),
(29, 10, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(30, 10, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(31, 10, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(32, 10, 'MODULO AEREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \r\nMueble totalmente en aglomerado PELIKANO RH 15mm \r\nCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \r\nuna (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \r\nNIQUEL CEP CC. 96 MM y brazos hidráulico en las puertas.', 6, 'si', 1420000.00),
(33, 10, 'MÓDULO AEREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de\r\n15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de cuatro (4) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 2, 'si', 1400000.00),
(34, 10, 'ESCRITORIO SEN', '1775429485_Item6.png', 'De 950mm x 550mm. En su área de trabajo x por 720mm de\r\nalto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mmCANTO RIGIDO ORANGE 19MM –\r\n33MM termo fundido. ', 1, 'si', 750000.00),
(35, 10, 'MODULO AEREO 500', '1775429582_Item7.png', 'De 550mm ancho x 500mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de una (1) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 1, 'si', 950000.00),
(36, 10, 'MODULO INFERIOR 2070', '1775429700_Item8.png', 'De 720mm de alto x 2070 mm ancho x 520mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido...\r\n', 1, 'si', 2330000.00),
(37, 10, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(38, 10, 'DIVISIÓN CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(39, 11, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(40, 11, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(41, 11, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'no', 3500000.00),
(42, 11, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(43, 12, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(44, 12, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(45, 12, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(47, 13, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(48, 13, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(50, 6, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(51, 6, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(52, 6, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(53, 15, 'MÓDULO AEREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de\r\n15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de cuatro (4) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM', 2, 'si', 1400000.00),
(54, 15, 'DIVISIÓN CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(55, 14, 'DIVISIÓN CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(56, 14, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(57, 14, 'ESCRITORIO LINEAL 2400', '1775430117_Item11.png', 'De 2400mm x 650mm. En su área de trabajo x por 720mm\r\nde alto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mm superficie reforzada con nariz por\r\nsus 4 lados CANTO RIGIDO ORANGE 19MM – 33MM\r\ntermo fundido.', 1, 'si', 2200000.00),
(58, 17, 'MODULO AREO 750', '1775430467_Item14.png', 'De 750mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nconta de consta de dos puertas con sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...', 1, 'si', 1400000.00),
(59, 17, 'MODULO ESTANTE 1270', '1775430858_Item17.png', 'De 730mm ancho x 1270mm alto x por 300mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH color\r\nblanco de 15mm, PATA 697 ACERO 201 CON NIVELADOR\r\n40MM (HPA697-04) CANTO RIGIDO ORANGE blanco\r\n19MM – 33MM termo fundido.', 1, 'si', 1400000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `cantidad` int(11) NOT NULL,
  `iva` enum('si','no') NOT NULL DEFAULT 'si',
  `precio` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `titulo`, `foto`, `descripcion`, `cantidad`, `iva`, `precio`) VALUES
(1, 'ESTACÍON 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su área de trabajo\r\ny 960mm de alto. Mueble en aglomerado\r\nPELIKANO RH fresno y blanco de 15mm.\r\nCon nariz en el tablero de 30mm, CANTO\r\nRIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 1, 'si', 4280000.00),
(2, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n450MMX350MM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 4, 'si', 3500000.00),
(3, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\r\nMueble en aglomerado PELIKANO RH fresno\r\nde 15mm. Consta de una (1) puertas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; una (1) Gaveta con su\r\nrespectivo RIEL EXT TOTAL ZINC PESADO\r\n45X350MMM 45Kg; PATA 697 ACERO 201\r\nCON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO y PVC 19MM – 33MM termo\r\nfundido...', 2, 'si', 3550000.00),
(4, 'MODULO AEREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \r\nMueble totalmente en aglomerado PELIKANO RH 15mm \r\nCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \r\nuna (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \r\nNIQUEL CEP CC. 96 MM y brazos hidráulico en las puertas.', 6, 'si', 1420000.00),
(5, 'MÓDULO AEREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de\r\n15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de cuatro (4) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 2, 'si', 1400000.00),
(6, 'ESCRITORIO SEN', '1775429485_Item6.png', 'De 950mm x 550mm. En su área de trabajo x por 720mm de\r\nalto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mmCANTO RIGIDO ORANGE 19MM –\r\n33MM termo fundido. ', 1, 'si', 750000.00),
(7, 'MODULO AEREO 500', '1775429582_Item7.png', 'De 550mm ancho x 500mm alto x por 360mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\r\nConsta de una (1) puertas con sus respectivas MANIJA.\r\nALUMINIO 671 NIQUEL CEP CC. 96 MM ', 1, 'si', 950000.00),
(8, 'MODULO INFERIOR 2070', '1775429700_Item8.png', 'De 720mm de alto x 2070 mm ancho x 520mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido...\r\n', 1, 'si', 2330000.00),
(9, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO de 15mm\r\nCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(10, 'DIVISIÓN CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\r\naglomerado PELIKANO RH FRESNO y BLANCO de 15mm\r\npuerta entamborada en RH, CANTO RIGIDO y PVC 19-\r\n44MM termo fundido...', 1, 'si', 4220000.00),
(11, 'ESCRITORIO LINEAL 2400', '1775430117_Item11.png', 'De 2400mm x 650mm. En su área de trabajo x por 720mm\r\nde alto. Mueble totalmente en aglomerado PELIKANO RH\r\ncolor FRESNO de 15mm superficie reforzada con nariz por\r\nsus 4 lados CANTO RIGIDO ORANGE 19MM – 33MM\r\ntermo fundido. ', 1, 'si', 2200000.00),
(12, 'LOQUER 885 ', '1775430262_Item12.png', 'Módulo de tres puestos de 885mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ntres puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...', 4, 'si', 1080000.00),
(13, 'LOQUER 595', '1775430367_Item13.png', 'Módulo de dos puestos de 595mm de ancho x 500mm de\r\nalto por 400mm de fondo; Mueble totalmente en aglomerado\r\nPELIKANO RH FRESNO y BLANCO de 15mm consta de\r\ndos puertas con seguridad y sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 4, 'si', 820000.00),
(14, 'MODULO AREO 750', '1775430467_Item14.png', 'De 750mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH de 15mm\r\nconta de consta de dos puertas con sus respectivas manijas,\r\nCANTO RIGIDO y PVC 19 MM termo fundido...\r\n', 2, 'si', 1400000.00),
(15, 'MODULO MESON 1500', '1775430553_Item15.png', 'De 1500mm de ancho x 630 mm alto 330mm de fondo;\r\nMueble en aglomerado PELIKANO RH FRESNO y BLANCO\r\nde 15mm y superficie en PVC, consta de cuatro puertas con\r\nsus respectivas manijas, CANTO RIGIDO y PVC 19 MM\r\ntermo fundido...\r\n', 1, 'si', 2650000.00),
(16, 'MÓDULO SUPERIOR 1480', '1775430760_Item16.png', 'De 570 mm de alto x 1480 mm ancho x 400mm de fondo;\r\nMueble totalmente en aglomerado PELIKANO RH FRESNO\r\nde 15mm CANTO RIGIDO ORANGE MACADAMIA19MM\r\ntermo fundido...\r\n', 1, 'si', 400000.00),
(17, 'MODULO ESTANTE 1270', '1775430858_Item17.png', 'De 730mm ancho x 1270mm alto x por 300mm de fondo.\r\nMueble totalmente en aglomerado PELIKANO RH color\r\nblanco de 15mm, PATA 697 ACERO 201 CON NIVELADOR\r\n40MM (HPA697-04) CANTO RIGIDO ORANGE blanco\r\n19MM – 33MM termo fundido.', 4, 'si', 1400000.00),
(18, 'MODULO GAVETERO 730', '1775430911_Item18.png', 'De 730mm de alto x 700mm de ancho y\r\n550mm de fondo. Mueble totalmente en\r\naglomerado PELIKANO FRESNO de 15mm\r\nConsta de tres (3) Gavetas con sus\r\nrespectivas MANIJA. ALUMINIO 671 NIQUEL\r\nCEP CC. 96 MM; RIEL EXT TOTAL ZINC\r\nPESADO 350MMM 45Kg; PATA 697 ACERO\r\n201 CON NIVELADOR 40MM (HPA697-04)\r\n(HRE126-25n nariz en el tablero de 30mm,\r\nCANTO RIGIDO color PLOMO 19MM –\r\n33MM termo fundido...', 1, 'si', 1720000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descripcion_tarea` varchar(255) NOT NULL,
  `estado` enum('pendiente','completo') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `usuario_id`, `descripcion_tarea`, `estado`) VALUES
(1, 2, 'Realizar cotización para el hospital Z con los productos Estación 2380, Escritorio en L 1300, y Escritorio en L 1400', 'completo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `documento`, `nombre`, `correo`, `password`, `telefono`, `rol`, `estado`) VALUES
(1, '1234567890', 'Juan', 'admin@sodicol.com', '$2y$10$zIR4ivZp2firpeewYOWfEuLdK9dVvF756.rT8DUwicy0TjM026rwe', '3000000000', 'admin', 'activo'),
(2, '1118367962', 'Santiago ', 'santiago@gmail.com', '$2y$10$vowrUYpi16iDqsHjYDbNm.2hw2GN1kj.b0BmLbuoNXbfRNzyblYpS', '3217235089', 'usuario', 'activo'),
(4, '12345', 'Fabian Ramos', 'fabian@gmail.com', '$2y$10$hAg5./LhLdYpVuDO9gZtvu5lNAZQQuuzsFdEye6PJpstUXkh2dlj.', '3190986753', 'usuario', 'activo'),
(6, '32', 'Santiago', 'santiagolizcanosuarez@gmail.com', '$2y$10$0QRBjE5ZPOINKbsOyvOPGu9axWlMl0VDcXs/8nYmimHOdwFA0Am4q', '3148440088', 'usuario', 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cotizacion_items`
--
ALTER TABLE `cotizacion_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotizacion_id` (`cotizacion_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `cotizacion_items`
--
ALTER TABLE `cotizacion_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cotizacion_items`
--
ALTER TABLE `cotizacion_items`
  ADD CONSTRAINT `cotizacion_items_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
