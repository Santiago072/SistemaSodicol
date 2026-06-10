<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/CotizacionController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new CotizacionController($conexion);
$data       = $controller->editarItem();
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Editar Ítem</title>
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
    <div class="encabezado-pagina"><h1>Editar Ítem de Cotización</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" enctype="multipart/form-data" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="item_id"    value="<?= intval($datos['id']) ?>">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($datos['foto']) ?>">

            <div class="grupo-campo">
                <label>Nombre del Producto *</label>
                <input type="text" name="titulo" value="<?= htmlspecialchars($datos['titulo']) ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label>Foto Actual</label>
                <?php if (!empty($datos['foto'])): ?>
                <div style="margin-bottom:10px;">
                    <img src="../uploads/<?= htmlspecialchars($datos['foto']) ?>" width="100" style="border:1px solid #ccc;max-width:200px;">
                </div>
                <?php else: ?><p>No hay foto asignada</p><?php endif; ?>
                <label>Cambiar Foto (Opcional)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color:#666;">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
            </div>
            <div class="grupo-campo">
                <label>Descripción *</label>
                <textarea name="descripcion" required maxlength="1000"><?= htmlspecialchars($datos['descripcion']) ?></textarea>
            </div>
            <div class="grupo-campo">
                <label>Cantidad *</label>
                <input type="number" name="cantidad" value="<?= intval($datos['cantidad']) ?>" required min="1">
            </div>
            <div class="grupo-campo">
                <label>Valor con IVA *</label>
                <select name="IVA" required>
                    <option value="si" <?= $datos['iva'] === 'si' ? 'selected' : '' ?>>Aplicar IVA</option>
                    <option value="no" <?= $datos['iva'] === 'no' ? 'selected' : '' ?>>No Aplicar IVA</option>
                </select>
            </div>
            <div class="grupo-campo">
                <label>Precio Unitario *</label>
                <input type="number" name="precio" value="<?= floatval($datos['precio']) ?>" required min="0" step="0.01">
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Actualizar Ítem</button>
                <a href="crear_cotizacion.php" class="boton-secundario">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
