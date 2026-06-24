<?php
/**
 * Menú lateral — partial puro (sin DOCTYPE ni <html>).
 * Requiere sesión activa con $_SESSION['rol'] y $_SESSION['usuario_nombre'].
 */
if (!isset($_SESSION['usuario_nombre'])) {
    $base = defined('BASE_URL') ? BASE_URL : '/';
    header('Location: ' . $base);
    exit();
}
$rol       = $_SESSION['rol'];
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
?>
<nav class="menu-principal">
    <div class="menu-lateral" id="menuLateral">
        <div class="logo-wrap">
            <div class="logo-ring-wrap">
                <div class="logo-ring"></div>
                <div class="logo-ring"></div>
                <div class="logo-ring"></div>
                <div class="logo-halo"></div>
                <div class="logo-halo"></div>
                <div class="logo-halo"></div>
                <div class="logo-dash"></div>
                <div class="logo-circle">
                    <img src="<?= $basePath ?>logo/logo.png" alt="Logo Sodicol">
                </div>
            </div>
        </div>

        <ul class="lista-menu-lateral">
            <li>
                <a href="<?= $basePath ?>?module=panel" title="Panel">
                    <i class="bi bi-house-door-fill"></i>
                </a>
            </li>
            <?php if ($rol === 'admin'): ?>
            <li class="menu-desplegable" data-panel="usuarios" title="Usuarios">
                <a href="#"><i class="bi bi-person-fill"></i></a>
            </li>
            <?php endif; ?>
            <li class="menu-desplegable" data-panel="cotizaciones" title="Cotizaciones">
                <a href="#"><i class="bi bi-currency-dollar"></i></a>
            </li>
            <li>
                <a href="<?= $basePath ?>?action=logout" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="panel-flotante" id="panel">
    <ul class="submenu" id="submenu-dinamico"></ul>
</div>

<script>
(function () {
    const panel   = document.getElementById('panel');
    const submenu = document.getElementById('submenu-dinamico');
    let   timeout;

    const menus = {
        usuarios: `
            <h3>Usuarios</h3>
            <li><a href="<?= $basePath ?>?module=usuarios&action=lista"><i class="fas fa-user-friends"></i> Lista de Usuarios</a></li>
            <li><a href="<?= $basePath ?>?module=usuarios&action=crear"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
            <li><a href="<?= $basePath ?>?module=tareas&action=gestion"><i class="fas fa-tasks"></i> Tareas Usuarios</a></li>
        `,
        cotizaciones: `
            <h3>Cotizaciones</h3>
            <li><a href="<?= $basePath ?>?module=cotizaciones&action=crear"><i class="fas fa-dollar-sign"></i> Crear Cotización</a></li>
            <li><a href="<?= $basePath ?>?module=cotizaciones&action=consultar"><i class="fas fa-list"></i> Consultar Cotización</a></li>
            <?php if ($rol === 'admin'): ?>
            <li><a href="<?= $basePath ?>?module=productos&action=lista"><i class="fas fa-box"></i> Lista de Productos</a></li>
            <?php endif; ?>
        `
    };

    document.querySelectorAll('.menu-desplegable').forEach(item => {
        const tipo = item.dataset.panel;
        item.addEventListener('mouseenter', () => {
            clearTimeout(timeout);
            submenu.innerHTML = menus[tipo] || '';
            panel.classList.add('visible');
        });
        item.addEventListener('mouseleave', () => {
            timeout = setTimeout(() => panel.classList.remove('visible'), 300);
        });
    });

    panel.addEventListener('mouseenter', () => clearTimeout(timeout));
    panel.addEventListener('mouseleave', () => panel.classList.remove('visible'));
})();
</script>
