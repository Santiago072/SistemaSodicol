<?php
/**
 * Partial de paginación reutilizable.
 * Variables esperadas: $paginaActual, $totalPaginas, $urlBase (sin ?pagina=)
 * Ejemplo de $urlBase: "lista_usuarios.php?busqueda=Juan"
 */
if (!isset($totalPaginas) || $totalPaginas <= 1) return;

$sep = (strpos($urlBase, '?') !== false) ? '&' : '?';
?>
<div class="paginacion">
    <?php if ($paginaActual > 1): ?>
        <a href="<?= htmlspecialchars($urlBase . $sep . 'pagina=' . ($paginaActual - 1)) ?>"
           class="pag-btn"><i class="bi bi-chevron-left"></i></a>
    <?php else: ?>
        <span class="pag-btn pag-disabled"><i class="bi bi-chevron-left"></i></span>
    <?php endif; ?>

    <?php
    $inicio = max(1, $paginaActual - 2);
    $fin    = min($totalPaginas, $paginaActual + 2);
    if ($inicio > 1): ?>
        <a href="<?= htmlspecialchars($urlBase . $sep . 'pagina=1') ?>" class="pag-btn">1</a>
        <?php if ($inicio > 2): ?><span class="pag-dots">…</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
        <?php if ($i === $paginaActual): ?>
            <span class="pag-btn pag-activo"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= htmlspecialchars($urlBase . $sep . 'pagina=' . $i) ?>"
               class="pag-btn"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($fin < $totalPaginas): ?>
        <?php if ($fin < $totalPaginas - 1): ?><span class="pag-dots">…</span><?php endif; ?>
        <a href="<?= htmlspecialchars($urlBase . $sep . 'pagina=' . $totalPaginas) ?>"
           class="pag-btn"><?= $totalPaginas ?></a>
    <?php endif; ?>

    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="<?= htmlspecialchars($urlBase . $sep . 'pagina=' . ($paginaActual + 1)) ?>"
           class="pag-btn"><i class="bi bi-chevron-right"></i></a>
    <?php else: ?>
        <span class="pag-btn pag-disabled"><i class="bi bi-chevron-right"></i></span>
    <?php endif; ?>
</div>
