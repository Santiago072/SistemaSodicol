<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/ProductoController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new ProductoController($conexion);
$data       = $controller->editar();
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Editar Producto</title>
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
    <div class="encabezado-pagina"><h1>Editar Producto</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" enctype="multipart/form-data" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= intval($producto['id']) ?>">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($producto['foto']) ?>">

            <div class="grupo-campo">
                <label for="titulo">Nombre del Producto *</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($producto['titulo']) ?>" required maxlength="255">
            </div>
            <div class="grupo-campo">
                <label>Foto Actual del Producto</label>
                <?php if (!empty($producto['foto'])): ?>
                <div style="margin-bottom:10px;">
                    <img src="../uploads/<?= htmlspecialchars($producto['foto']) ?>" width="100" style="border:1px solid #ccc;max-width:200px;">
                </div>
                <?php else: ?><p>No hay foto asignada</p><?php endif; ?>
                <label>Cambiar Foto (Opcional)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color:#666;">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
            </div>
            <div class="grupo-campo">
                <label for="descripcion">Descripción *</label>
                <textarea id="descripcion" name="descripcion" required maxlength="1000"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>
            <div class="grupo-campo">
                <label for="cantidad">Cantidad *</label>
                <input type="number" id="cantidad" name="cantidad" required min="0" value="<?= intval($producto['cantidad']) ?>">
            </div>
            <div class="grupo-campo">
                <label for="iva">Valor con IVA *</label>
                <select id="iva" name="iva" required>
                    <option value="">Seleccione una Opción</option>
                    <option value="si" <?= $producto['iva'] === 'si' ? 'selected' : '' ?>>Aplicar IVA</option>
                    <option value="no" <?= $producto['iva'] === 'no' ? 'selected' : '' ?>>No Aplicar IVA</option>
                </select>
            </div>
            <div class="grupo-campo">
                <label for="precio">Precio Unitario *</label>
                <input type="number" id="precio" name="precio" required min="0" step="0.01" value="<?= floatval($producto['precio']) ?>">
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Guardar Producto</button>
                <a href="lista_productos.php" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
