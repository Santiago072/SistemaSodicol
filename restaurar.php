<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'sodicol_db';
$db_user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'sodicol_user';
$db_pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'root';
$db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'sistema_sodicol';

try {
    $conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conexion->connect_error) {
        // Fallback for local XAMPP if docker env vars are missing
        $conexion = new mysqli('localhost', 'root', '', 'sistema_sodicol');
        if ($conexion->connect_error) {
            die("Connection failed: " . $conexion->connect_error);
        }
    }
    $conexion->set_charset("utf8mb4");
    
    $sql = "DELETE FROM productos";
    $conexion->query($sql);
    
    $sql = "ALTER TABLE productos AUTO_INCREMENT = 1";
    $conexion->query($sql);

    $sql_insert = "INSERT INTO `productos` (`id`, `titulo`, `foto`, `descripcion`, `cantidad`, `iva`, `precio`) VALUES
(1, 'ESTACION 2380', '1774732866_Item1.png', 'De 2380mm x 630mm; en su area de trabajo\\ry 960mm de alto. Mueble en aglomerado\\rPELIKANO RH fresno y blanco de 15mm.\\rCon nariz en el tablero de 30mm, CANTO\\rRIGIDO y PVC 19MM – 33MM termo\\rfundido...', 1, 'si', 4280000.00),
(2, 'ESCRITORIO EN L 1300', '1774732915_Item2.png', 'De 1300mm x 1500mm; 715 mm de alto.\\rMueble en aglomerado PELIKANO RH fresno\\rde 15mm. Consta de una (1) puertas con sus\\rrespectivas MANIJA. ALUMINIO 671 NIQUEL\\rCEP CC. 96 MM; una (1) Gaveta con su\\rrespectivo RIEL EXT TOTAL ZINC PESADO\\r450MMX350MM 45Kg; PATA 697 ACERO\\r201 CON NIVELADOR 40MM (HPA697-04)\\r(HRE126-25n nariz en el tablero de 30mm,\\rCANTO RIGIDO y PVC 19MM – 33MM termo\\rfundido...', 4, 'si', 3500000.00),
(3, 'ESCRITORIO EN L 1400', '1774732953_Item3.png', 'De 1400mm x 1500mm; 715 mm de alto.\\rMueble en aglomerado PELIKANO RH fresno\\rde 15mm. Consta de una (1) puertas con sus\\rrespectivas MANIJA. ALUMINIO 671 NIQUEL\\rCEP CC. 96 MM; una (1) Gaveta con su\\rrespectivo RIEL EXT TOTAL ZINC PESADO\\r45X350MMM 45Kg; PATA 697 ACERO 201\\rCON NIVELADOR 40MM (HPA697-04)\\r(HRE126-25n nariz en el tablero de 30mm,\\rCANTO RIGIDO y PVC 19MM – 33MM termo\\rfundido...', 2, 'si', 3550000.00),
(4, 'MODULO AEREO 800 ', '1774898774_Item4.png', 'De 800mm ancho x 500mm alto x por 420mm de fondo.    \\rMueble totalmente en aglomerado PELIKANO RH 15mm \\rCANTO RIGIDO Y PVC de 19MM termo fundido. Consta de \\runa (1) puertas con sus respectivas MANIJA. ALUMINIO 671 \\rNIQUEL CEP CC. 96 MM y brazos hidraulico en las puertas.', 6, 'si', 1420000.00),
(5, 'MODULO AEREO 1030', '1775429396_Item5.png', 'De 1030mm ancho x 730mm alto x por 360mm de fondo.\\rMueble totalmente en aglomerado PELIKANO RH de\\r15mmCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\\rConsta de cuatro (4) puertas con sus respectivas MANIJA.\\rALUMINIO 671 NIQUEL CEP CC. 96 MM ', 2, 'si', 1400000.00),
(6, 'ESCRITORIO SEN', '1775429485_Item6.png', 'De 950mm x 550mm. En su area de trabajo x por 720mm de\\ralto. Mueble totalmente en aglomerado PELIKANO RH\\rcolor FRESNO de 15mmCANTO RIGIDO ORANGE 19MM –\\r33MM termo fundido. ', 1, 'si', 750000.00),
(7, 'MODULO AEREO 500', '1775429582_Item7.png', 'De 550mm ancho x 500mm alto x por 360mm de fondo.\\rMueble totalmente en aglomerado PELIKANO RH de 15mm\\rCANTO RIGIDO y PVC 19MM – 33MM termo fundido.\\rConsta de una (1) puertas con sus respectivas MANIJA.\\rALUMINIO 671 NIQUEL CEP CC. 96 MM ', 1, 'si', 950000.00),
(8, 'MODULO INFERIOR 2070', '1775429700_Item8.png', 'De 720mm de alto x 2070 mm ancho x 520mm de fondo;\\rMueble totalmente en aglomerado PELIKANO RH de 15mm\\rCANTO RIGIDO y PVC 19MM termo fundido...\\r', 1, 'si', 2330000.00),
(9, 'MODULO INFERIOR 1570', '../uploads/Item9.png', 'De 710mm de alto x 1570 mm ancho x 570mm de fondo;\\rMueble totalmente en aglomerado PELIKANO de 15mm\\rCANTO RIGIDO y PVC 19MM termo fundido.', 2, 'si', 1750000.00),
(10, 'DIVISION CON PUERTA', '1775430033_Item10.png', 'De 2630mm de alto x 1490 mm ancho; Mueble totalmente en\\raglomerado PELIKANO RH FRESNO y BLANCO de 15mm\\rpuerta entamborada en RH, CANTO RIGIDO y PVC 19-\\r44MM termo fundido...', 1, 'si', 4220000.00),
(11, 'ESCRITORIO LINEAL 2400', '1775430117_Item11.png', 'De 2400mm x 650mm. En su area de trabajo x por 720mm\\rde alto. Mueble totalmente en aglomerado PELIKANO RH\\rcolor FRESNO de 15mm superficie reforzada con nariz por\\rsus 4 lados CANTO RIGIDO ORANGE 19MM – 33MM\\rtermo fundido. ', 1, 'si', 2200000.00),
(12, 'LOQUER 885 ', '1775430262_Item12.png', 'Modulo de tres puestos de 885mm de ancho x 500mm de\\ralto por 400mm de fondo; Mueble totalmente en aglomerado\\rPELIKANO RH FRESNO y BLANCO de 15mm consta de\\rtres puertas con seguridad y sus respectivas manijas,\\rCANTO RIGIDO y PVC 19 MM termo fundido...', 4, 'si', 1080000.00),
(13, 'LOQUER 595', '1775430367_Item13.png', 'Modulo de dos puestos de 595mm de ancho x 500mm de\\ralto por 400mm de fondo; Mueble totalmente en aglomerado\\rPELIKANO RH FRESNO y BLANCO de 15mm consta de\\rdos puertas con seguridad y sus respectivas manijas,\\rCANTO RIGIDO y PVC 19 MM termo fundido...\\r', 4, 'si', 820000.00),
(14, 'MODULO AEREO 750', '1775430467_Item14.png', 'De 750mm de ancho x 630 mm alto 330mm de fondo;\\rMueble totalmente en aglomerado PELIKANO RH de 15mm\\rconta de consta de dos puertas con sus respectivas manijas,\\rCANTO RIGIDO y PVC 19 MM termo fundido...\\r', 2, 'si', 1400000.00),
(15, 'MODULO MESON 1500', '1775430553_Item15.png', 'De 1500mm de ancho x 630 mm alto 330mm de fondo;\\rMueble en aglomerado PELIKANO RH FRESNO y BLANCO\\rde 15mm y superficie en PVC, consta de cuatro puertas con\\rsus respectivas manijas, CANTO RIGIDO y PVC 19 MM\\rtermo fundido...\\r', 1, 'si', 2650000.00),
(16, 'MODULO SUPERIOR 1480', '1775430760_Item16.png', 'De 570 mm de alto x 1480 mm ancho x 400mm de fondo;\\rMueble totalmente en aglomerado PELIKANO RH FRESNO\\rde 15mm CANTO RIGIDO ORANGE MACADAMIA19MM\\rtermo fundido...\\r', 1, 'si', 400000.00),
(17, 'MODULO ESTANTE 1270', '1775430858_Item17.png', 'De 730mm ancho x 1270mm alto x por 300mm de fondo.\\rMueble totalmente en aglomerado PELIKANO RH color\\rblanco de 15mm, PATA 697 ACERO 201 CON NIVELADOR\\r40MM (HPA697-04) CANTO RIGIDO ORANGE blanco\\r19MM – 33MM termo fundido.', 4, 'si', 1400000.00),
(18, 'MODULO GAVETERO 730', '1775430911_Item18.png', 'De 730mm de alto x 700mm de ancho y\\r550mm de fondo. Mueble totalmente en\\raglomerado PELIKANO FRESNO de 15mm\\rConsta de tres (3) Gavetas con sus\\rrespectivas MANIJA. ALUMINIO 671 NIQUEL\\rCEP CC. 96 MM; RIEL EXT TOTAL ZINC\\rPESADO 350MMM 45Kg; PATA 697 ACERO\\r201 CON NIVELADOR 40MM (HPA697-04)\\r(HRE126-25n nariz en el tablero de 30mm,\\rCANTO RIGIDO color PLOMO 19MM –\\r33MM termo fundido...', 1, 'si', 1720000.00)";

    if ($conexion->query($sql_insert)) {
        echo "<h1 style='color:green;'>¡Éxito! Productos Restaurados</h1>";
        echo "<p>Se han eliminado los productos antiguos y se han restaurado los 18 productos originales sin tildes ni caracteres especiales.</p>";
        echo "<a href='/?module=productos&action=lista' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Volver a la lista de productos</a>";
    } else {
        echo "Error al insertar: " . $conexion->error;
    }

} catch (Exception $e) {
    echo "Excepción: " . $e->getMessage();
}
?>
