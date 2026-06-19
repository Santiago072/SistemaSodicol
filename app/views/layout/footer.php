<?php
/**
 * Layout footer — cierra el body/html e incluye el script global.
 * Variables esperadas (opcionales):
 *   $basePath  string — ruta raíz para cargar script.js
 */
$basePath = $basePath ?? '/SistemaSodicol/';
?>
<script src="<?= $basePath ?>public/js/script.js"></script>
<script src="<?= $basePath ?>public/js/ajax_tables.js"></script>
</body>
</html>
