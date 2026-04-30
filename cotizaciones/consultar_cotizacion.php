<?php
session_start();
if(!isset($_SESSION['usuario_nombre'])) {
    header('Location: index.php');
    exit();
}
include '../config/conexion.php';
$conexion = conexion();

$query = null;
$busqueda_fecha = '';
$busqueda_cliente = '';
$busqueda_numero = '';

/* Buscar cotización por múltiples criterios */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $condiciones = [];
    
    if (!empty($_POST['fecha'])) {
        $fecha = $_POST['fecha'];
        $busqueda_fecha = $fecha;
        $condiciones[] = "fecha_creacion = '$fecha'";
    }
    
    if (!empty($_POST['nombre_cliente'])) {
        $cliente = $_POST['nombre_cliente'];
        $busqueda_cliente = $cliente;
        $condiciones[] = "nombre_cliente LIKE '%$cliente%'";
    }
    
    if (!empty($_POST['numero_cotizacion'])) {
        $numero = $_POST['numero_cotizacion'];
        $busqueda_numero = $numero;
        $condiciones[] = "numero_cotizacion LIKE '%$numero%'";
    }
    
    if (count($condiciones) > 0) {
        $where = implode(' AND ', $condiciones);
        $sql = "SELECT * FROM cotizaciones WHERE $where ORDER BY id DESC";
        $query = mysqli_query($conexion, $sql);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
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
            <button class="boton-menu-ocultar" id="btnMenu">
                <i class="fas fa-bars"></i> Ocultar Menú
            </button>
            <!-- Botón modo día/noche -->
            <button class="btn-modo" id="btnModo" title="Cambiar tema">
                <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
                <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
                <span class="modo-label"></span>
            </button>
        </div>

        <div class="encabezado-pagina">
            <h1>Consultar Cotización</h1>
        </div>

        <!-- Formulario de búsqueda múltiple -->
        <div class="barra-busqueda">
            <form method="POST" action="consultar_cotizacion.php" class="formulario-busqueda">
                <input type="date" name="fecha" value="<?php echo $busqueda_fecha; ?>" placeholder="Buscar por fecha...">
                <input type="text" name="nombre_cliente" value="<?php echo htmlspecialchars($busqueda_cliente); ?>" placeholder="Buscar por cliente...">
                <input type="text" name="numero_cotizacion" value="<?php echo htmlspecialchars($busqueda_numero); ?>" placeholder="Número cotización...">
                <button type="submit" class="boton-primario">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <?php if ($query): ?>
                <a href="consultar_cotizacion.php" class="boton-limpiar">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>N° Cotización</th>
                        <th>Fecha</th>
                        <th>Nombre del cliente</th>
                        <th>Entidad</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query && mysqli_num_rows($query) > 0): ?>
                    <?php while ($cotizacion = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td><?php echo $cotizacion['numero_cotizacion'] ?: 'Sin número'; ?></td>
                        <td><?php echo $cotizacion['fecha_creacion']; ?></td>
                        <td><?php echo $cotizacion['nombre_cliente']; ?></td>
                        <td><?php echo $cotizacion['entidad']; ?></td>
                        <td><?php echo $cotizacion['ciudad']; ?></td>
                        <td class="acciones-tabla">
                            <?php if (!empty($cotizacion['numero_cotizacion'])): ?>
                            <button type="button" class="btn-ver-pdf"
                                onclick="verPDF('<?php echo $cotizacion['numero_cotizacion']; ?>', '<?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?>')">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <?php else: ?>
                            <span class="estado-no-generado">No generado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php elseif ($query && mysqli_num_rows($query) == 0): ?>
                    <tr>
                        <td colspan="6" class="tabla-mensaje-vacio">
                            <i class="bi bi-search"></i>
                            No se encontraron cotizaciones con los criterios de búsqueda.
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="tabla-mensaje-vacio">
                            <i class="bi bi-arrow-up-circle"></i>
                            Use los filtros de arriba para buscar cotizaciones.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para ver PDF -->
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
                <div id="pdf-error" class="pdf-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h4>No se pudo cargar el PDF</h4>
                    <p>El archivo de la cotización no está disponible o ha sido movido.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para ver PDF en modal
        function verPDF(numeroCotizacion, nombreCliente) {
            const modal = document.getElementById('modal-pdf-viewer');
            const frame = document.getElementById('pdf-frame');
            const titulo = document.getElementById('pdf-titulo');
            const btnDescargar = document.getElementById('btn-descargar');
            const pdfError = document.getElementById('pdf-error');

            // Ocultar error previo
            pdfError.style.display = 'none';
            frame.style.display = 'block';

            // Construir URL del PDF (usando el generador PHP con parámetro de solo visualización)
            // Esto regenera el PDF temporalmente para visualizarlo
            const pdfUrl = 'generar_pdf.php?ver=' + numeroCotizacion + '&temp=1';

            titulo.textContent = numeroCotizacion + ' - ' + nombreCliente;
            frame.src = pdfUrl;

            // Configurar botón de descarga (descarga el PDF real generado)
            btnDescargar.href = 'generar_pdf.php?descargar=' + numeroCotizacion;
            btnDescargar.setAttribute('download', 'cotizacion_' + numeroCotizacion + '.pdf');

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';

            // Manejar error de carga
            frame.onerror = function () {
                frame.style.display = 'none';
                pdfError.style.display = 'block';
            };

            // Timeout para detectar si no carga
            setTimeout(function () {
                try {
                    if (!frame.contentDocument || frame.contentDocument.body.innerHTML === '') {
                        // Si está vacío, probablemente no existe
                    }
                } catch (e) {
                    // Error de cross-origin o no cargó
                }
            }, 3000);
        }

        // Función para cerrar modal PDF
        function cerrarPDF() {
            const modal = document.getElementById('modal-pdf-viewer');
            const frame = document.getElementById('pdf-frame');
            const pdfError = document.getElementById('pdf-error');

            modal.style.display = 'none';
            frame.src = '';
            frame.style.display = 'block';
            pdfError.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function (event) {
            const modal = document.getElementById('modal-pdf-viewer');
            if (event.target == modal) {
                cerrarPDF();
            }
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarPDF();
            }
        });
    </script>

    <script src="../includes/script.js"></script>
</body>

</html>