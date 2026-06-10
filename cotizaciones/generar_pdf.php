<?php
/**
 * generar_pdf.php — Entry point para generación y descarga de PDFs.
 * La lógica de negocio (incluyendo transacción atómica para el número)
 * vive en CotizacionController / CotizacionModel.
 */
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/CotizacionController.php';

iniciar_sesion_segura();

$conexion   = conexion();
$controller = new CotizacionController($conexion);

try {
    $data = $controller->generarPdf();
} catch (Exception $e) {
    die('Error al generar la cotización: ' . htmlspecialchars($e->getMessage()));
}

$cotizacion       = $data['cotizacion'];
$items            = $data['items'];       // array de filas
$forzar_descarga  = $data['forzar'];

$numero_cotizacion = $cotizacion['numero_cotizacion'];
$profesion         = $cotizacion['profesion'];
$nombre_cliente    = $cotizacion['nombre_cliente'];
$especialidad      = $cotizacion['especialidad'];
$entidad           = $cotizacion['entidad'];
$ciudad            = $cotizacion['ciudad'];
$fecha_raw         = $cotizacion['fecha_creacion'];

// ─── Formatear fecha larga ──────────────────────────────────────────────────
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252');
$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
          "Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
$fecha_obj   = new DateTime($fecha_raw);
$fecha_larga = $fecha_obj->format('d') . " de " . $meses[(int)$fecha_obj->format('n') - 1]
             . " del " . $fecha_obj->format('Y');

// ─── Verificar que hay ítems ────────────────────────────────────────────────
if (empty($items)) {
    die('La cotización no tiene ítems.');
}

// ─── Librerías PDF ──────────────────────────────────────────────────────────
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// ─── Helper: imagen → base64 ────────────────────────────────────────────────
function convertirImagen(string $ruta): string {
    if (file_exists($ruta)) {
        $type = pathinfo($ruta, PATHINFO_EXTENSION);
        $data = file_get_contents($ruta);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    return '';
}

$ruta_base_img = dirname(__DIR__) . '/img/';
$img_logo      = convertirImagen($ruta_base_img . 'logo.png');
$img_firma     = convertirImagen($ruta_base_img . 'firma.png');
$img_correo    = convertirImagen($ruta_base_img . 'correo.png');
$img_ubicacion = convertirImagen($ruta_base_img . 'ubicacion.png');
$img_celular   = convertirImagen($ruta_base_img . 'celular.png');
$img_logo_small = convertirImagen($ruta_base_img . 'logo_small.png');
if (empty($img_logo_small)) $img_logo_small = $img_logo;

// ─── Calcular totales ───────────────────────────────────────────────────────
$valor_base_total = 0;
$valor_iva_total  = 0;

foreach ($items as $it) {
    $pu = (float)$it['precio'];
    $q  = (int)$it['cantidad'];
    $iva_u = ($it['iva'] === 'si') ? $pu * 0.19 : 0;
    $valor_base_total += $pu * $q;
    $valor_iva_total  += $iva_u;
}
$gran_total = $valor_base_total + $valor_iva_total;

// ─── Construir HTML del PDF ─────────────────────────────────────────────────
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización <?= $numero_cotizacion ?></title>
    <style><?php include 'estilo_pdf.css'; ?></style>
</head>
<body>

<?php
// Helper: encabezado de empresa
function imprimirHeader(string $img_logo): void { ?>
<table class="tabla-encabezado">
    <tr>
        <td class="celda-logo"><img src="<?= $img_logo ?>" class="img-logo" alt="Logo"></td>
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
        <div style="clear:both;"></div>
    </div>
    <div class="datos-cliente">
        <?= htmlspecialchars($profesion) ?>:<br>
        <?= mb_strtoupper(htmlspecialchars($nombre_cliente)) ?><br>
        <?= htmlspecialchars($especialidad) ?><br>
        <?= htmlspecialchars($entidad) ?>.<br>
        <?= htmlspecialchars($ciudad) ?>.
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
    <?php $contador = 1; foreach ($items as $item):
        $pu      = (float)$item['precio'];
        $qty     = (int)$item['cantidad'];
        $iva_u   = ($item['iva'] === 'si') ? $pu * 0.19 : 0;
        $vt      = $pu * $qty;
    ?>
    <tr>
        <td class="col-item"><?= $contador++ ?></td>
        <td class="col-desc"><?= htmlspecialchars($item['titulo']) ?></td>
        <td class="col-cant"><?= $qty ?></td>
        <td class="col-vu">$ <?= number_format($pu, 0, '', '.') ?></td>
        <td class="col-iva">$ <?= number_format($iva_u, 0, '', '.') ?></td>
        <td class="col-vt">$ <?= number_format($vt, 0, '', '.') ?></td>
    </tr>
    <?php endforeach; ?>
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
    <p style="font-size:15px;">Para todos los efectos informo a ustedes que toda la correspondencia relacionada con esta cotización la recibiremos en:</p>
    <p style="font-size:15px;">
        <strong>Teléfono:</strong> 310 251 6060<br>
        <strong>Correo Electrónico:</strong>
        <span style="color:#0066cc;border-bottom:1px solid #0066cc;padding-bottom:1px;">sodicolsas@gmail.com</span>
    </p>
</div>

<div class="seccion-firma">
    <p style="font-size:15px;">Atentamente,</p><br>
    <div style="font-weight:bold;line-height:1.3;margin-top:10px;">
        <?php if ($img_firma): ?>
        <img src="<?= $img_firma ?>" style="height:28px;vertical-align:bottom;margin-bottom:5px;"><br>
        <?php else: ?><br><br>__________________________<br><?php endif; ?>
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
                <td width="50%" class="celda-footer padding-bottom-extra" style="padding-left:30px;">
                    <img src="<?= $img_correo ?>" class="icono-pie">
                    <span class="texto-footer">sodicolsas@gmail.com</span>
                </td>
            </tr>
            <tr>
                <td width="50%" class="celda-footer">
                    <img src="<?= $img_ubicacion ?>" class="icono-pie">
                    <span class="texto-footer">Cl 18 7 43 -B/ 7 de agosto</span>
                </td>
                <td width="50%" class="celda-footer" style="padding-left:30px;">
                    <img src="<?= $img_celular ?>" class="icono-pie">
                    <span class="texto-footer">3000000000</span>
                </td>
            </tr>
        </table>
    </div>
</footer>

<?php
// ─── Fichas técnicas (una por ítem, página nueva) ────────────────────────────
$es_primer_item = true;
foreach ($items as $item_det):
    $pu_det    = (float)$item_det['precio'];
    $qty_det   = (int)$item_det['cantidad'];
    $iva_det   = ($item_det['iva'] === 'si') ? $pu_det * 0.19 : 0;
    $pu_iva    = $pu_det + $iva_det;
    $total_det = $pu_iva * $qty_det;

    $img_prod = '';
    if (!empty($item_det['foto'])) {
        $img_prod = convertirImagen(dirname(__DIR__) . '/uploads/' . $item_det['foto']);
    }
?>
<div class="salto-pagina"></div>
<?php imprimirHeader($img_logo); ?>

<div class="titulo-item-detalle">
    <?php if ($es_primer_item): ?>
    <h3 style="margin-bottom:1rem;">FICHAS TECNICAS</h3>
    <h4><?= htmlspecialchars($item_det['titulo']) ?></h4>
    <?php $es_primer_item = false; ?>
    <?php else: ?>
    <h3><?= htmlspecialchars($item_det['titulo']) ?></h3>
    <?php endif; ?>
</div>

<?php if ($img_prod): ?>
<div class="contenedor-imagen-producto">
    <img src="<?= $img_prod ?>" class="img-producto">
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
                <strong><?= htmlspecialchars($item_det['titulo']) ?></strong>
                <br><br>
                <div class="descripcion-texto"><?= htmlspecialchars($item_det['descripcion']) ?></div>
            </td>
            <td class="td-cant"><?= $qty_det ?></td>
            <td class="td-valores">$ <?= number_format($pu_iva, 0, '', '.') ?></td>
            <td class="td-valores">$ <?= number_format($total_det, 0, '', '.') ?></td>
        </tr>
    </tbody>
</table>
<?php endforeach; ?>

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
$dompdf->stream("cotizacion_{$numero_cotizacion}.pdf", ['Attachment' => $forzar_descarga]);
