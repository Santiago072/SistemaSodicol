<?php
/**
 * Topbar reutilizable — botón de menú + mensaje de bienvenida + etiqueta de rol + botón modo.
 * Variables esperadas:
 *   $pageHeading  string  — título h1 de la sección (opcional)
 *   $usuario      array   — datos del usuario (nombre, rol)
 *   $esDashboard  bool    — indica si es el dashboard principal
 */
$pageHeading = $pageHeading ?? '';
$usuario = $usuario ?? null;
$esDashboard = $esDashboard ?? false;
?>
<div class="cabecera-superior">
    <button class="boton-menu-ocultar" id="btnMenu">
        <i class="fas fa-bars"></i> Ocultar Menú
    </button>
    <div class="cabecera-bienvenida" style="flex:1; padding-left: 16px;">
        <?php if ($esDashboard && $usuario): ?>
        <h3 style="margin:0; font-family:var(--font-display); font-size:20px; font-weight:700; background:linear-gradient(135deg, var(--white), var(--gold-light)); background-clip:text; -webkit-text-fill-color:transparent;">
            ¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!
        </h3>
        <?php endif; ?>
        <?php if ($pageHeading): ?>
        <span style="font-size:15px; font-weight:600; color:var(--gold-light);">
            <?= htmlspecialchars($pageHeading) ?>
        </span>
        <?php endif; ?>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
        <?php if ($esDashboard && $usuario): ?>
            <?php if ($usuario['rol'] === 'admin'): ?>
            <span class="rol-admin" style="margin:0; font-size:10px; padding:6px 14px;">
                <i class="bi bi-shield-check"></i> Administrador
            </span>
            <?php else: ?>
            <span class="rol-usuario" style="margin:0; font-size:10px; padding:6px 14px;">
                <i class="bi bi-person"></i> Usuario
            </span>
            <?php endif; ?>
        <?php endif; ?>
        <button class="btn-modo" id="btnModo" title="Cambiar tema">
            <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
            <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
            <span class="modo-label"></span>
        </button>
    </div>
</div>
