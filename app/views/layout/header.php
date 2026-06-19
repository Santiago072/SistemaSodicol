<?php
/**
 * Layout header — emite el <head> HTML completo.
 * Variables esperadas:
 *   $pageTitle  string  — título de la pestaña
 *   $basePath   string  — ruta raíz ('/PROYECTO_SODICOL/')
 *   $extraHead  string  — HTML adicional opcional (estilos inline, etc.)
 */
$pageTitle = $pageTitle ?? 'Sistema Sodicol';
$basePath  = defined('BASE_URL') ? BASE_URL : ($basePath ?? '/PROYECTO_SODICOL/');
$extraHead = $extraHead ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePath ?>css/estilos.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?= $extraHead ?>
</head>
<body>
<canvas id="particle-canvas"></canvas>
<div class="noise-overlay"></div>
