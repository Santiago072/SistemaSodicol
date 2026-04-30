<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252');

$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

// ============================================
// MODO 1: VER/DESCARGAR COTIZACIÓN EXISTENTE (desde consultar_cotizacion.php)
// ============================================
if (isset($_GET['ver']) || isset($_GET['descargar'])) {
    $numero = $_GET['ver'] ?? $_GET['descargar'] ?? '';
    
    if (empty($numero)) {
        die('Número de cotización no especificado');
    }
    
    // Buscar cotización en BD
    $sql = "SELECT * FROM cotizaciones WHERE numero_cotizacion = '$numero' LIMIT 1";
    $result = mysqli_query($conexion, $sql);
    $cotizacion = mysqli_fetch_assoc($result);
    
    if (!$cotizacion) {
        die('Cotización no encontrada: ' . htmlspecialchars($numero));
    }
    
    // Usar datos de la cotización encontrada
    $cotizacion_id = $cotizacion['id'];
    $numero_cotizacion = $cotizacion['numero_cotizacion'];
    $profesion = $cotizacion['profesion'];
    $nombre_cliente = $cotizacion['nombre_cliente'];
    $especialidad = $cotizacion['especialidad'];
    $entidad = $cotizacion['entidad'];
    $ciudad = $cotizacion['ciudad'];
    $fecha = $cotizacion['fecha_creacion'];
    
    // Formatear fecha larga
    $fecha_obj = new DateTime($fecha);
    $fecha_larga = $fecha_obj->format('d') . " de " . $meses[$fecha_obj->format('n')-1] . " del " . $fecha_obj->format('Y');
    
    // Obtener items de esta cotización
    $sql_items = "SELECT * FROM cotizacion_items WHERE cotizacion_id = '$cotizacion_id' ORDER BY id ASC";
    $query_items = mysqli_query($conexion, $sql_items);
    
    // Verificar si hay items
    if (mysqli_num_rows($query_items) == 0) {
        die('La cotización no tiene ítems');
    }
    
    // Generar PDF (código común al final del archivo)
    generarPDF($query_items, $numero_cotizacion, $profesion, $nombre_cliente, $especialidad, $entidad, $ciudad, $fecha_larga, isset($_GET['descargar']));
    exit();
}

// ============================================
// MODO 2: GENERAR NUEVA COTIZACIÓN (desde crear_cotizacion.php)
// ============================================

// Verificar sesión solo para modo normal
if(!isset($_SESSION['usuario_nombre'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['cotizacion_id'])) {
    die('No hay cotización activa.');
}
$cotizacion_id = $_SESSION['cotizacion_id'];

// 1. Obtenemos el prefijo del MES actual (AñoMes -> ej: 202512)
$prefijo_mes = date('Ym'); 

// 2. Contamos cuántas cotizaciones existen ya en ESTE MES
$sql_conteo = "SELECT COUNT(*) as total FROM cotizaciones 
               WHERE numero_cotizacion LIKE '$prefijo_mes%'";
$res_conteo = mysqli_query($conexion, $sql_conteo);
$fila_conteo = mysqli_fetch_assoc($res_conteo);

// 3. El consecutivo será el total encontrado + 1
$consecutivo_mensual = $fila_conteo['total'] + 1;

// 4. Generamos el número final:
$numero_cotizacion = date('Ymd') . str_pad($consecutivo_mensual, 2, '0', STR_PAD_LEFT);

// Recibimos datos del formulario
$profesion      = $_POST['profesion'] ?? '';
$nombre_cliente = $_POST['nombre_cliente'] ?? '';
$especialidad   = $_POST['especialidad'] ?? '';
$entidad        = $_POST['entidad'] ?? '';
$ciudad         = $_POST['ciudad'] ?? '';
$fecha          = $_POST['fecha'] ?? '';

// Actualizar BD
$sql_update = "UPDATE cotizaciones SET 
                fecha_creacion='$fecha',
                profesion='$profesion',
                nombre_cliente='$nombre_cliente',
                especialidad='$especialidad',
                entidad='$entidad',
                ciudad='$ciudad',
                numero_cotizacion='$numero_cotizacion'
               WHERE id='$cotizacion_id'";
mysqli_query($conexion, $sql_update);

// Obtener items
$sql_items = "SELECT * FROM cotizacion_items WHERE cotizacion_id = '$cotizacion_id' ORDER BY id ASC";
$query_items = mysqli_query($conexion, $sql_items);

$fecha_larga = date('d') . " de " . $meses[date('n')-1] . " del " . date('Y');

// Generar PDF
generarPDF($query_items, $numero_cotizacion, $profesion, $nombre_cliente, $especialidad, $entidad, $ciudad, $fecha_larga, true);

// Limpiar sesión
unset($_SESSION['cotizacion_id']);

// ============================================
// FUNCIÓN PARA GENERAR EL PDF
// ============================================
function generarPDF($query_items, $numero_cotizacion, $profesion, $nombre_cliente, $especialidad, $entidad, $ciudad, $fecha_larga, $forzar_descarga) {
    
    global $conexion;
    
    // RUTA DEL LOGO
    $ruta_base_img = dirname(__DIR__) . '/img/';
    
    function convertirImagen($ruta) {
        if (file_exists($ruta)) {
            $type = pathinfo($ruta, PATHINFO_EXTENSION);
            $data = file_get_contents($ruta);
            /* Que es base64 y porque lo usamos? sirve para que el navegador no tenga que cargar la imagen
             * de forma asincrona, lo que haría el navegador cuando la imagen está en la carpeta img/
             * y se cargue de forma asincrona. 
             * 
             * Si no lo usamos, el navegador cargaría la imagen de forma asincrona y el PDF generado
             * no se mostraría correctamente.
             */
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return '';
    }
    
    $img_logo       = convertirImagen($ruta_base_img . 'logo.png');
    $img_firma      = convertirImagen($ruta_base_img . 'firma.png');
    $img_correo     = convertirImagen($ruta_base_img . 'correo.png');
    $img_ubicacion  = convertirImagen($ruta_base_img . 'ubicacion.png');
    $img_celular    = convertirImagen($ruta_base_img . 'celular.png');
    $img_logo_small = convertirImagen($ruta_base_img . 'logo_small.png');
    if(empty($img_logo_small)) $img_logo_small = $img_logo;
    
    $valor_base_total = 0;
    $valor_iva_total  = 0;
    $gran_total       = 0;
    
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización <?= $numero_cotizacion ?></title>
    <style>
        <?php include 'estilo_pdf.css'; ?>
    </style>
</head>
<body>

    <?php 
    function imprimirHeader($img_logo) { ?>
    <table class="tabla-encabezado">
        <tr>
            <td class="celda-logo">
                <img src="<?= $img_logo ?>" class="img-logo" alt="Logo">
            </td>
            <td class="info-empresa">
                <h2>SOLUCIONES LOGISTICAS DE DISEÑO Y DISTRIBUCIONES COLOMBIA</h2>
                <h3>NIT : 901545636-1</h3>
                <div class="linea-encabezado"></div>
            </td>
        </tr>
    </table>
    <?php } ?>

    <?php imprimirHeader($img_logo); ?>

    <div class="seccion-info">
        <div class="fila-fecha-cot">
            <div class="fecha-izq">Florencia, <?= $fecha_larga ?>.</div>
            <div class="cot-der">Cotización No: <?= $numero_cotizacion ?></div>
            <div style="clear: both;"></div>
        </div>
        <div class="datos-cliente">
            <?= $profesion ?>:<br>
            <?= mb_strtoupper($nombre_cliente) ?><br>
            <?= $especialidad ?><br>
            <?= $entidad ?>.<br>
            <?= $ciudad ?>.
        </div>
        <p class="texto-intro">Cordial saludo</p>
        <p class="texto-intro">Para Sodicol Zomac S.A.S es muy grato presentar esta propuesta económica con el fin de atender sus requerimientos y necesidades, quedamos atentos a cualquier inquietud.</p>
    </div>

    <table class="tabla-principal">
        <thead>
            <tr>
                <th class="col-item">ITEM</th>
                <th class="col-desc">DESCRIPCIÓN</th>
                <th class="col-cant">CANT</th>
                <th class="col-vu">V/U</th>
                <th class="col-iva">IVA</th>
                <th class="col-vt">V/T</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $contador = 1; 
            while ($item = mysqli_fetch_array($query_items)): 
                $precio_unit = $item['precio'];
                $cantidad    = $item['cantidad'];
                $iva_unitario = ($item['iva'] === 'si') ? ($precio_unit * 0.19) : 0;
                $valor_total = $precio_unit * $cantidad;

                $valor_base_total += $valor_total;
                $valor_iva_total  += $iva_unitario;
                $gran_total = $valor_base_total + $valor_iva_total; 
            ?>
            <tr>
                <td class="col-item"><?= $contador++ ?></td>
                <td class="col-desc"><?= $item['titulo'] ?></td>
                <td class="col-cant"><?= $cantidad ?></td>
                <td class="col-vu">$ <?= number_format($precio_unit, 0, '', '.') ?></td>
                <td class="col-iva">$ <?= number_format($iva_unitario, 0, '', '.') ?></td>
                <td class="col-vt">$ <?= number_format($valor_total, 0, '', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="celda-vacia"></td>
                <td class="etiqueta-total">VALOR BASE</td>
                <td class="valor-total">$ <?= number_format($valor_base_total, 0, '', '.') ?></td>
            </tr>
            <tr>
                <td colspan="4" class="celda-vacia"></td>
                <td class="etiqueta-total">VALOR IVA</td>
                <td class="valor-total">$ <?= number_format($valor_iva_total, 0, '', '.') ?></td>
            </tr>
            <tr>
                <td colspan="4" class="celda-vacia"></td>
                <td class="etiqueta-total">TOTAL</td>
                <td class="valor-total">$ <?= number_format($gran_total, 0, '', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="info-pago">
        <p style="font-size: 15px;">Para todos los efectos informo a ustedes que toda la correspondencia relacionada con esta cotización la recibiremos en:</p>
        <p style="font-size: 15px;">
            <strong>Teléfono:</strong> 310 251 6060<br>
            <strong>Correo Electrónico:</strong>
            <span style="color: #0066cc; border-bottom: 1px solid #0066cc; padding-bottom: 1px;">sodicolsas@gmail.com</span>
        </p>
    </div>

    <div class="seccion-firma">
        <p style="font-size: 15px;">Atentamente,</p>
        <br>
        <div style="font-weight: bold; line-height: 1.3; margin-top: 10px;">
            <?php if($img_firma): ?>
            <img src="<?= $img_firma ?>" style="height: 28px; vertical-align: bottom; margin-bottom: 5px;"><br>
            <?php else: ?>
            <br><br>__________________________<br>
            <?php endif; ?>
            Nombre: INGRI ESTEFANIA FLORES<br>
            C.C. 1.006.509.877<br>
            Representante Legal
        </div>
    </div>

    <footer>
        <div class="footer-contenedor">
            <div class="ola-fondo-marron"></div>
            <div class="ola-mascara-blanca"></div>

            <table class="tabla-datos-footer">
                <tr>
                    <td width="50%" class="celda-footer padding-bottom-extra">
                        <img src="<?= $img_logo_small ?>" class="icono-logo-pie">
                        <span class="texto-footer">Sodicol Zomac S.A.S</span>
                    </td>
                    <td width="50%" class="celda-footer padding-bottom-extra" style="padding-left: 30px;">
                        <img src="<?= $img_correo ?>" class="icono-pie">
                        <span class="texto-footer">sodicolsas@gmail.com</span>
                    </td>
                </tr>
                <tr>
                    <td width="50%" class="celda-footer">
                        <img src="<?= $img_ubicacion ?>" class="icono-pie">
                        <span class="texto-footer">Cl 18 7 43 -B/ 7 de agosto</span>
                    </td>
                    <td width="50%" class="celda-footer" style="padding-left: 30px;">
                        <img src="<?= $img_celular ?>" class="icono-pie">
                        <span class="texto-footer">3000000000</span>
                    </td>
                </tr>
            </table>
        </div>
    </footer>

    <?php
    // IMPORTANTE: Reiniciamos el puntero de la consulta para volver a recorrer los items
    mysqli_data_seek($query_items, 0);

    $es_primer_item = true; 

    while ($item_det = mysqli_fetch_array($query_items)): 
        $precio_u_det = (float)$item_det['precio'];
        $cant_det     = (int)$item_det['cantidad'];
        $iva_u_det    = ($item_det['iva'] === 'si') ? ($precio_u_det * 0.19) : 0;
        $precio_iva_det = $precio_u_det + $iva_u_det;
        $total_det    = $precio_iva_det * $cant_det;

        $img_prod_base64 = null;
        $ruta_foto_bd = $item_det['foto']; 

        if (!empty($ruta_foto_bd)) {
            $ruta_abs_foto = dirname(__DIR__) . '/uploads/' . $ruta_foto_bd;
            $img_prod_base64 = convertirImagen($ruta_abs_foto);
        }
    ?>
    <div class="salto-pagina"></div>

    <?php imprimirHeader($img_logo); ?>

    <div class="titulo-item-detalle">
        <?php if ($es_primer_item): ?>
        <h3 style="margin-bottom: 1rem;">FICHAS TECNICAS</h3>
        <h4><?php echo $item_det['titulo'] ?></h4>
        <?php $es_primer_item = false; ?>
        <?php else: ?>
        <h3><?= htmlspecialchars($item_det['titulo']) ?></h3>
        <?php endif; ?>
    </div>

    <?php if($img_prod_base64): ?>
    <div class="contenedor-imagen-producto">
        <img src="<?= $img_prod_base64 ?>" class="img-producto">
    </div>
    <?php endif; ?>

    <table class="tabla-detalle">
        <thead>
            <tr>
                <th class="th-desc">DESCRIPCION</th>
                <th class="th-cant">CANT.</th>
                <th class="th-vu">V/U IVA INCLUIDO</th>
                <th class="th-total">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="td-desc">
                    <strong><?= $item_det['titulo'] ?></strong>
                    <br><br>
                    <div class="descripcion-texto">
                        <?= $item_det['descripcion'] ?>
                    </div>
                </td>
                <td class="td-cant"><?= $cant_det ?></td>
                <td class="td-valores">$ <?= number_format($precio_iva_det, 0, '', '.') ?></td>
                <td class="td-valores">$ <?= number_format($total_det, 0, '', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <?php endwhile; ?>

</body>
</html>
    <?php
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Si es descarga forzada, descargar. Si no, mostrar en navegador
    $dompdf->stream("cotizacion_$numero_cotizacion.pdf", array("Attachment" => $forzar_descarga));
}
?>