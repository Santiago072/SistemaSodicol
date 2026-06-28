<?php
/**
 * generar_pdf.php — Vista de generación/descarga de PDFs.
 *
 * Este archivo es incluido por el Front Controller (index.php)
 * cuando ?module=cotizaciones&action=generar_pdf.
 * El controlador CotizacionController ya fue instanciado en index.php.
 */

try {
    $data = $ctrl->generarPdf();
} catch (Exception $e) {
    die('Error al generar la cotización: ' . htmlspecialchars($e->getMessage()));
}

// ── Extraer datos del controller ─────────────────────────────────────────────
$cotizacion      = $data['cotizacion'];
$items           = $data['items'];
$forzar_descarga = $data['forzar'];

$numero_cotizacion = $cotizacion['numero_cotizacion'];
$profesion         = $cotizacion['profesion'];
$nombre_cliente    = $cotizacion['nombre_cliente'];
$especialidad      = $cotizacion['especialidad'];
$entidad           = $cotizacion['entidad'];
$ciudad            = $cotizacion['ciudad'];
$fecha_raw         = $cotizacion['fecha_creacion'];

if (empty($items)) {
    die('La cotización no tiene ítems.');
}

// ── Fecha en español ─────────────────────────────────────────────────────────
date_default_timezone_set('America/Bogota');
$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
          'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$fecha_obj   = new DateTime($fecha_raw);
$fecha_larga = $fecha_obj->format('d') . ' de '
             . $meses[(int)$fecha_obj->format('n') - 1]
             . ' del ' . $fecha_obj->format('Y');

// ── DomPDF ───────────────────────────────────────────────────────────────────
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// ── Helper: imagen a base64 ──────────────────────────────────────────────────
function convertirImagen(string $ruta): string {
    if (!file_exists($ruta)) return '';
    $ext  = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    $mime = $ext === 'svg' ? 'svg+xml' : $ext;
    $contenido = @file_get_contents($ruta);
    if ($contenido === false) return '';
    return 'data:image/' . $mime . ';base64,' . base64_encode($contenido);
}

$imgDir         = dirname(__DIR__, 3) . '/img/';
$img_logo       = convertirImagen($imgDir . 'logo.png');
$img_firma      = convertirImagen($imgDir . 'firma.png');
$img_correo     = convertirImagen($imgDir . 'correo.png');
$img_ubicacion  = convertirImagen($imgDir . 'ubicacion.png');
$img_celular    = convertirImagen($imgDir . 'celular.png');
$img_logo_small = convertirImagen($imgDir . 'logo_small.png') ?: $img_logo;

// ── Totales ──────────────────────────────────────────────────────────────────
$valor_base_total = 0;
$valor_iva_total  = 0;
foreach ($items as $it) {
    $pu = (float)$it['precio'];
    $q  = (int)$it['cantidad'];
    $valor_base_total += $pu * $q;
    $valor_iva_total  += ($it['iva'] === 'si') ? $pu * 0.19 : 0;
}
$gran_total = $valor_base_total + $valor_iva_total;

// ── Helper interno: encabezado de página ─────────────────────────────────────
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
<?php }

// ── Plantilla HTML del PDF ───────────────────────────────────────────────────
ob_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización <?= htmlspecialchars($numero_cotizacion) ?></title>
    <style><?php include __DIR__ . '/estilo_pdf.css'; ?></style>
</head>
<body>

<?php imprimirHeader($img_logo); ?>

<div class="seccion-info">
    <div class="fila-fecha-cot">
        <div class="fecha-izq">Florencia, <?= $fecha_larga ?>.</div>
        <div class="cot-der">Cotización No: <?= htmlspecialchars($numero_cotizacion) ?></div>
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
    <p class="texto-intro">Para Sodicol Zomac S.A.S es muy grato presentar esta propuesta económica
       con el fin de atender sus requerimientos y necesidades, quedamos atentos a cualquier inquietud.</p>
</div>

<!-- Tabla resumen de ítems -->
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
            $pu    = (float)$item['precio'];
            $qty   = (int)$item['cantidad'];
            $iva_u = ($item['iva'] === 'si') ? $pu * 0.19 : 0;
            $vt    = $pu * $qty;
        ?>
        <tr>
            <td class="col-item"><?= $contador++ ?></td>
            <td class="col-desc"><?= htmlspecialchars($item['titulo']) ?></td>
            <td class="col-cant"><?= $qty ?></td>
            <td class="col-vu">$&nbsp;<?= number_format($pu, 0, '', '.') ?></td>
            <td class="col-iva">$&nbsp;<?= number_format($iva_u, 0, '', '.') ?></td>
            <td class="col-vt">$&nbsp;<?= number_format($vt, 0, '', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="celda-vacia"></td>
            <td class="etiqueta-total">VALOR BASE</td>
            <td class="valor-total">$&nbsp;<?= number_format($valor_base_total, 0, '', '.') ?></td>
        </tr>
        <tr>
            <td colspan="4" class="celda-vacia"></td>
            <td class="etiqueta-total">VALOR IVA</td>
            <td class="valor-total">$&nbsp;<?= number_format($valor_iva_total, 0, '', '.') ?></td>
        </tr>
        <tr>
            <td colspan="4" class="celda-vacia"></td>
            <td class="etiqueta-total">TOTAL</td>
            <td class="valor-total">$&nbsp;<?= number_format($gran_total, 0, '', '.') ?></td>
        </tr>
    </tfoot>
</table>

<div class="info-pago">
    <p style="font-size:15px;">Para todos los efectos informo a ustedes que toda la correspondencia
       relacionada con esta cotización la recibiremos en:</p>
    <p style="font-size:15px;">
        <strong>Teléfono:</strong> 310 251 6060<br>
        <strong>Correo Electrónico:</strong>
        <span style="color:#0066cc;border-bottom:1px solid #0066cc;">sodicolsas@gmail.com</span>
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
// ── Fichas técnicas (una por ítem) ───────────────────────────────────────────
$esPrimero = true;
foreach ($items as $det):
    $pu_d    = (float)$det['precio'];
    $qty_d   = (int)$det['cantidad'];
    $iva_d   = ($det['iva'] === 'si') ? $pu_d * 0.19 : 0;
    $pu_iva  = $pu_d + $iva_d;
    $total_d = $pu_iva * $qty_d;
    $imgProd = !empty($det['foto'])
               ? convertirImagen(dirname(__DIR__, 3) . '/uploads/' . $det['foto'])
               : '';
?>
<div class="salto-pagina"></div>
<?php imprimirHeader($img_logo); ?>

<div class="titulo-item-detalle">
    <?php if ($esPrimero): ?>
    <h3 style="margin-bottom:1rem;">FICHAS TECNICAS</h3>
    <h4><?= htmlspecialchars($det['titulo']) ?></h4>
    <?php $esPrimero = false; ?>
    <?php else: ?>
    <h3><?= htmlspecialchars($det['titulo']) ?></h3>
    <?php endif; ?>
</div>

<?php if ($imgProd): ?>
<div class="contenedor-imagen-producto">
    <img src="<?= $imgProd ?>" class="img-producto">
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
                <strong><?= htmlspecialchars($det['titulo']) ?></strong><br><br>
                <div class="descripcion-texto"><?= htmlspecialchars($det['descripcion']) ?></div>
            </td>
            <td class="td-cant"><?= $qty_d ?></td>
            <td class="td-valores">$&nbsp;<?= number_format($pu_iva, 0, '', '.') ?></td>
            <td class="td-valores">$&nbsp;<?= number_format($total_d, 0, '', '.') ?></td>
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
$options->set('defaultFont', 'DejaVu Sans');
$dompdf  = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("cotizacion_{$numero_cotizacion}.pdf", ['Attachment' => $forzar_descarga]);
