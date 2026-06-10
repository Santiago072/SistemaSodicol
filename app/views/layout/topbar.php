<?php
/**
 * Topbar reutilizable — botón de menú + título de página + botón modo.
 * Variables esperadas:
 *   $pageHeading  string  — título h1 de la sección
 */
$pageHeading = $pageHeading ?? '';
?>
<div class="cabecera-superior">
    <button class="boton-menu-ocultar" id="btnMenu">
        <i class="fas fa-bars"></i> Ocultar Menú
    </button>
    <?php if ($pageHeading): ?>
    <div class="cabecera-bienvenida" style="flex:1; padding-left: 16px;">
        <span style="font-size:15px; font-weight:600; color:var(--gold-light);">
            <?= htmlspecialchars($pageHeading) ?>
        </span>
    </div>
    <?php endif; ?>
    <button class="btn-modo" id="btnModo" title="Cambiar tema">
        <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
        <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
        <span class="modo-label"></span>
    </button>
</div>
