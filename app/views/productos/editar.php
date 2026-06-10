<?php
/**
 * Vista: Editar producto
 * Variables: $producto, $mensajeError, $csrf_token
 */
$pageTitle = 'Editar Producto';
$basePath  = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Editar Producto';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Editar Producto</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" enctype="multipart/form-data"
              action="/PROYECTO_SODICOL/productos/editar_producto.php?id=<?= intval($producto['id']) ?>"
              class="formulario">
            <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id"          value="<?= intval($producto['id']) ?>">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($producto['foto']) ?>">

            <div class="grupo-campo">
                <label for="titulo">Nombre del Producto *</label>
                <input type="text" id="titulo" name="titulo"
                       value="<?= htmlspecialchars($producto['titulo']) ?>" required maxlength="255">
            </div>
            <div class="grupo-campo">
                <label>Foto Actual del Producto</label>
                <?php if (!empty($producto['foto'])): ?>
                <div style="margin-bottom:10px;">
                    <img src="/PROYECTO_SODICOL/uploads/<?= htmlspecialchars($producto['foto']) ?>"
                         width="100" style="border:1px solid #ccc;max-width:200px;">
                </div>
                <?php else: ?><p>No hay foto asignada</p><?php endif; ?>
                <label>Cambiar Foto (Opcional)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color:#888;">Formatos: JPG, PNG, GIF, WEBP. Máx: 5MB</small>
            </div>
            <div class="grupo-campo">
                <label for="descripcion">Descripción *</label>
                <textarea id="descripcion" name="descripcion" required maxlength="1000"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>
            <div class="grupo-campo">
                <label for="cantidad">Cantidad *</label>
                <input type="number" id="cantidad" name="cantidad" required min="0"
                       value="<?= intval($producto['cantidad']) ?>">
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
                <input type="number" id="precio" name="precio" required min="0" step="0.01"
                       value="<?= floatval($producto['precio']) ?>">
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Guardar Producto</button>
                <a href="/PROYECTO_SODICOL/productos/lista_productos.php" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
