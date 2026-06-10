<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/ProductoController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new ProductoController($conexion);
$data       = $controller->listar();
extract($data);

$urlBase = 'lista_productos.php' . ($busqueda !== '' ? '?busqueda=' . urlencode($busqueda) : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    <div class="encabezado-pagina"><h1>Lista de Productos</h1></div>

    <?php if ($mensajeExito): ?>
    <div class="success-box" style="background:#d4edda;color:#155724;padding:15px;margin:15px 0;border-radius:8px;border:1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
    </div>
    <?php endif; ?>

    <?php if ($mensajeError): ?>
    <?php $clase = (strpos($mensajeError, 'asociado') !== false) ? 'warning-box' : 'error-box'; ?>
    <div class="<?= $clase ?>">
        <i class="<?= $clase === 'warning-box' ? 'bi bi-exclamation-triangle' : 'fas fa-exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($mensajeError) ?>
    </div>
    <?php endif; ?>

    <div class="barra-busqueda">
        <form action="lista_productos.php" method="GET" class="formulario-busqueda">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar producto...">
            <button type="submit" class="boton-primario">Buscar</button>
            <?php if ($busqueda): ?>
            <a href="lista_productos.php" class="boton-limpiar">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr><th>Nombre del Producto</th><th>Precio</th><th>Cantidad</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['titulo']) ?></td>
                    <td><?= number_format($p['precio'], 0, '', '.') ?></td>
                    <td><?= intval($p['cantidad']) ?></td>
                    <td class="acciones-tabla">
                        <a href="editar_producto.php?id=<?= intval($p['id']) ?>" class="boton-editar"><i class="fas fa-edit"></i></a>
                        <a href="eliminar_producto.php?id=<?= intval($p['id']) ?>" class="boton-eliminar"
                           onclick="return confirm('¿Está seguro de eliminar este producto?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($productos)): ?>
                <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--gold-light);">
                    <i class="bi bi-search"></i> No se encontraron productos.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include '../app/views/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> productos)</p>
    <?php endif; ?>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
