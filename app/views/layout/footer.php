<?php
/**
 * Layout footer — cierra el body/html e incluye el script global.
 * Variables esperadas (opcionales):
 *   $basePath  string — ruta raíz para cargar script.js
 */
$basePath = $basePath ?? '/PROYECTO_SODICOL/';
?>
<script src="<?= $basePath ?>includes/script.js"></script>
</body>
</html>
