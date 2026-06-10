<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/CotizacionController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new CotizacionController($conexion);
$data       = $controller->consultar();
extract($data);

$urlBase = 'consultar_cotizacion.php?buscando=1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Consultar Cotización</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<canvas id="particle-canvas"></canvas>
<div class="noise-overlay"></div>
<?php include '../includes/menu.php'; ?>

<div class="contenido-principal">
    <div class="cabecera-superior">
        <button class="boton-menu-ocultar" id="btnMenu"><i class="fas fa-bars"></i> Ocultar Menú</button>
        <button class="btn-modo" id="btnModo" title="Cambiar tema">
            <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
            <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
        </button>
    </div>
    <div class="encabezado-pagina"><h1>Consultar Cotización</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="barra-busqueda">
        <form method="POST" action="consultar_cotizacion.php" class="formulario-busqueda">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="date" name="fecha" value="<?= htmlspecialchars($busquedaFecha) ?>" placeholder="Buscar por fecha...">
            <input type="text" name="nombre_cliente" value="<?= htmlspecialchars($busquedaCliente) ?>" placeholder="Buscar por cliente..." maxlength="255">
            <input type="text" name="numero_cotizacion" value="<?= htmlspecialchars($busquedaNumero) ?>" placeholder="Número cotización..." maxlength="50">
            <button type="submit" class="boton-primario"><i class="bi bi-search"></i> Buscar</button>
            <?php if (!empty($cotizaciones) || $busquedaFecha || $busquedaCliente || $busquedaNumero): ?>
            <a href="consultar_cotizacion.php?limpiar=1" class="boton-limpiar"><i class="bi bi-x-circle"></i> Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>N° Cotización</th><th>Fecha</th><th>Nombre del cliente</th>
                    <th>Entidad</th><th>Ciudad</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cotizaciones)): ?>
                    <?php foreach ($cotizaciones as $cot): ?>
                    <tr>
                        <td><?= htmlspecialchars($cot['numero_cotizacion'] ?: 'Sin número') ?></td>
                        <td><?= htmlspecialchars($cot['fecha_creacion']) ?></td>
                        <td><?= htmlspecialchars($cot['nombre_cliente']) ?></td>
                        <td><?= htmlspecialchars($cot['entidad']) ?></td>
                        <td><?= htmlspecialchars($cot['ciudad']) ?></td>
                        <td class="acciones-tabla">
                            <?php if (!empty($cot['numero_cotizacion'])): ?>
                            <button type="button" class="btn-ver-pdf"
                                onclick="verPDF('<?= htmlspecialchars($cot['numero_cotizacion']) ?>',
                                               '<?= htmlspecialchars($cot['nombre_cliente']) ?>')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <?php else: ?>
                            <span class="estado-no-generado">No generado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php elseif (isset($_GET['buscando'])): ?>
                <tr><td colspan="6" class="tabla-mensaje-vacio">
                    <i class="bi bi-search"></i> No se encontraron cotizaciones.
                </td></tr>
                <?php else: ?>
                <tr><td colspan="6" class="tabla-mensaje-vacio">
                    <i class="bi bi-arrow-up-circle"></i> Use los filtros de arriba para buscar cotizaciones.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include '../app/views/partials/paginacion.php'; ?>
</div>

<!-- Modal PDF viewer -->
<div id="modal-pdf-viewer" class="modal-pdf-viewer">
    <div class="modal-pdf-contenido">
        <div class="modal-pdf-header">
            <h3><i class="bi bi-file-earmark-pdf"></i> Cotización: <span id="pdf-titulo"></span></h3>
            <div class="modal-pdf-acciones">
                <a id="btn-descargar" href="#" class="btn-descargar-pdf" download>
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" class="btn-cerrar-pdf" onclick="cerrarPDF()">
                    <i class="bi bi-x-lg"></i> Cerrar
                </button>
            </div>
        </div>
        <div class="pdf-container">
            <iframe id="pdf-frame" src=""></iframe>
            <div id="pdf-error" class="pdf-error" style="display:none;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <h4>No se pudo cargar el PDF</h4>
                <p>El archivo no está disponible o ha sido movido.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function verPDF(numero, cliente) {
        const modal   = document.getElementById('modal-pdf-viewer');
        const frame   = document.getElementById('pdf-frame');
        const titulo  = document.getElementById('pdf-titulo');
        const btnDesc = document.getElementById('btn-descargar');
        const pdfErr  = document.getElementById('pdf-error');

        pdfErr.style.display  = 'none';
        frame.style.display   = 'block';

        titulo.textContent    = numero + ' - ' + cliente;
        frame.src             = 'generar_pdf.php?ver=' + encodeURIComponent(numero) + '&temp=1';
        btnDesc.href          = 'generar_pdf.php?descargar=' + encodeURIComponent(numero);
        btnDesc.setAttribute('download', 'cotizacion_' + numero + '.pdf');

        modal.style.display         = 'block';
        document.body.style.overflow = 'hidden';
    }

    function cerrarPDF() {
        document.getElementById('modal-pdf-viewer').style.display = 'none';
        document.getElementById('pdf-frame').src                  = '';
        document.body.style.overflow                               = 'auto';
    }

    window.onclick = e => { if (e.target === document.getElementById('modal-pdf-viewer')) cerrarPDF(); };
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarPDF(); });
</script>
<script src="../includes/script.js"></script>
</body>
</html>
