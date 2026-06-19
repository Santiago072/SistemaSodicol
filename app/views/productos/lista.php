<?php
/**
 * Vista: Lista de productos
 * Variables: $productos, $busqueda, $paginaActual, $totalPaginas, $total,
 *            $mensajeExito, $mensajeError, $urlBase
 */
$pageTitle = 'Lista de Productos';
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Lista de Productos';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Lista de Productos</h1></div>

    <?php if ($mensajeExito): ?>
    <div class="success-box">
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
        <form action="<?= $basePath ?>" method="GET" class="formulario-busqueda">
            <input type="hidden" name="module" value="productos"><input type="hidden" name="action" value="lista"><input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar producto...">
            <?php if ($busqueda): ?>
            <a href="<?= $basePath ?>?module=productos&action=lista" class="boton-limpiar">Limpiar</a>
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
                        <a href="<?= $basePath ?>?module=productos&action=editar&id=<?= intval($p['id']) ?>" class="boton-editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= $basePath ?>?module=productos&action=eliminar&id=<?= intval($p['id']) ?>"
                           class="boton-eliminar"
                           onclick="return confirm('¿Está seguro de eliminar este producto?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($productos)): ?>
                <tr><td colspan="4" class="text-center p-30 text-gold">
                    <i class="bi bi-search"></i> No se encontraron productos.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include dirname(__DIR__) . '/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> productos)</p>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
