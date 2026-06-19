<?php
/**
 * Vista: Lista de usuarios
 * Variables: $usuarios, $busqueda, $paginaActual, $totalPaginas, $total,
 *            $mensajeExito, $mensajeError, $urlBase
 */
$pageTitle = 'Lista Usuarios';
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Gestión de Usuarios';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Gestión Usuarios</h1></div>

    <?php if ($mensajeExito): ?>
    <div class="success-box">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
    </div>
    <?php endif; ?>
    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="barra-busqueda">
        <form action="<?= $basePath ?>" method="GET" class="formulario-busqueda">
            <input type="hidden" name="module" value="usuarios">
            <input type="hidden" name="action" value="lista">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar usuario...">
            <?php if ($busqueda): ?>
            <a href="<?= $basePath ?>?module=usuarios&action=lista" class="boton-limpiar">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr><th>Nombre</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['rol']) ?></td>
                    <td><?= htmlspecialchars($u['estado']) ?></td>
                    <td class="acciones-tabla">
                        <a href="<?= $basePath ?>?module=usuarios&action=editar&id=<?= intval($u['id']) ?>" class="boton-editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= $basePath ?>?module=usuarios&action=eliminar&id=<?= intval($u['id']) ?>"
                           class="boton-eliminar"
                           onclick="return confirm('¿Está seguro de eliminar este usuario?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="4" class="text-center p-30 text-gold">
                    <i class="bi bi-search"></i> No se encontraron usuarios.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include dirname(__DIR__) . '/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> usuarios)</p>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
