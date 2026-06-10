<?php
/**
 * Vista: Crear cotización
 * Variables: $productos, $producto, $busqueda, $items, $totalItems, $csrf_token
 */
$pageTitle = 'Crear Cotización';
$basePath  = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Crear Cotización';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Crear Nueva Cotización</h1></div>

    <!-- Búsqueda de producto -->
    <div class="barra-busqueda">
        <form action="/PROYECTO_SODICOL/cotizaciones/crear_cotizacion.php" method="GET" class="formulario-busqueda">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>"
                   placeholder="Buscar producto...">
            <button type="submit" class="boton-primario">Buscar</button>
            <?php if ($busqueda): ?>
            <a href="/PROYECTO_SODICOL/cotizaciones/crear_cotizacion.php" class="boton-limpiar">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Seleccionar producto existente -->
    <div class="seleccion-producto">
        <form method="GET" action="/PROYECTO_SODICOL/cotizaciones/crear_cotizacion.php" class="formulario">
            <input type="hidden" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>">
            <select name="producto_id" class="producto-existente" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $prd): ?>
                <option value="<?= intval($prd['id']) ?>"
                    <?= (isset($_GET['producto_id']) && $_GET['producto_id'] == $prd['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prd['titulo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="boton-primario">Usar producto</button>
            <a href="/PROYECTO_SODICOL/cotizaciones/crear_cotizacion.php" class="boton-limpiar">Limpiar</a>
        </form>
    </div>
    <br>

    <!-- Formulario de ítem -->
    <div class="formulario-contenedor formulario-cotizacion">
        <form method="POST" enctype="multipart/form-data"
              action="/PROYECTO_SODICOL/cotizaciones/crear_cotizacion.php" class="formulario">
            <input type="hidden" name="action"      value="guardar_item">
            <input type="hidden" name="csrf_token"  value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="producto_id" value="<?= intval($producto['id'] ?? 0) ?>">

            <div class="grupo-campo">
                <label>Nombre del Producto *</label>
                <input type="text" name="titulo"
                       value="<?= htmlspecialchars($producto['titulo'] ?? '') ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($producto['foto'] ?? '') ?>">
                <label>Foto del Producto</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color:#888;">Formatos: JPG, PNG, GIF, WEBP. Máx: 5MB</small>
                <?php if (!empty($producto['foto'])): ?>
                <div class="grupo-campo">
                    <label>Imagen actual</label><br>
                    <img src="/PROYECTO_SODICOL/uploads/<?= htmlspecialchars($producto['foto']) ?>"
                         width="150" style="max-width:200px;">
                </div>
                <?php endif; ?>
            </div>
            <div class="grupo-campo">
                <label>Descripción *</label>
                <textarea name="descripcion" required maxlength="1000"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="grupo-campo">
                <label>Cantidad *</label>
                <input type="number" name="cantidad"
                       value="<?= intval($producto['cantidad'] ?? 0) ?>" required min="1">
            </div>
            <div class="grupo-campo">
                <label>Valor con IVA *</label>
                <select name="IVA" required>
                    <option value="">Seleccione una Opción</option>
                    <option value="si" <?= (($producto['iva'] ?? '') === 'si') ? 'selected' : '' ?>>Aplicar IVA</option>
                    <option value="no" <?= (($producto['iva'] ?? '') === 'no') ? 'selected' : '' ?>>No Aplicar IVA</option>
                </select>
            </div>
            <div class="grupo-campo">
                <label>Precio Unitario *</label>
                <input type="number" name="precio"
                       value="<?= floatval($producto['precio'] ?? 0) ?>" required min="0" step="0.01">
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Guardar Ítem</button>
                <button type="button" id="abrir-modal-pdf" class="boton-generar-pdf">Cotización Lista</button>
            </div>
        </form>
    </div>
    <br>

    <!-- Lista de ítems -->
    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Producto</th><th>Cantidad</th><th>IVA</th>
                    <th>Precio Unitario</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['titulo']) ?></td>
                    <td><?= intval($item['cantidad']) ?></td>
                    <td><?= $item['iva'] === 'si' ? 'Sí' : 'No' ?></td>
                    <td><?= number_format($item['precio'], 0, '', '.') ?></td>
                    <td class="acciones-tabla">
                        <a href="/PROYECTO_SODICOL/cotizaciones/editar_cotizacion.php?id=<?= intval($item['id']) ?>"
                           class="boton-editar"><i class="fas fa-edit"></i></a>
                        <a href="/PROYECTO_SODICOL/cotizaciones/eliminar_cotizacion.php?id=<?= intval($item['id']) ?>"
                           class="boton-eliminar"
                           onclick="return confirm('¿Eliminar este ítem?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--gold-light);">
                    <i class="bi bi-info-circle"></i> No hay ítems en esta cotización.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal datos del cliente -->
<div id="modal-pdf" class="modal">
    <div class="modal-contenido">
        <span class="cerrar">&times;</span>
        <h2>Datos del Cliente</h2>
        <form action="/PROYECTO_SODICOL/cotizaciones/generar_pdf.php" method="POST" target="_blank">
            <div class="grupo-fila">
                <div class="grupo-campo">
                    <label>Profesión *</label>
                    <input type="text" name="profesion" required>
                </div>
                <div class="grupo-campo">
                    <label>Nombre del Cliente *</label>
                    <input type="text" name="nombre_cliente" required>
                </div>
            </div>
            <div class="grupo-fila">
                <div class="grupo-campo">
                    <label>Especialidad *</label>
                    <input type="text" name="especialidad" required>
                </div>
                <div class="grupo-campo">
                    <label>Entidad *</label>
                    <input type="text" name="entidad" required>
                </div>
            </div>
            <div class="grupo-fila">
                <div class="grupo-campo">
                    <label>Ciudad *</label>
                    <input type="text" name="ciudad" required>
                </div>
                <div class="grupo-campo">
                    <label>Fecha de Cotización *</label>
                    <input type="date" name="fecha" required>
                </div>
            </div>
            <div class="grupo-campo grupo-acciones">
                <button type="submit" class="boton-generar-pdf">Generar PDF</button>
                <button type="button" class="cerrar-btn boton-secundario">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal      = document.getElementById('modal-pdf');
    const abrir      = document.getElementById('abrir-modal-pdf');
    const cierres    = document.querySelectorAll('.cerrar, .cerrar-btn');
    const totalItems = <?= intval($totalItems) ?>;

    abrir.addEventListener('click', () => {
        if (totalItems === 0) {
            alert('Debe agregar al menos un ítem antes de generar el PDF');
            return;
        }
        modal.style.display = 'block';
    });

    cierres.forEach(b => b.addEventListener('click', () => modal.style.display = 'none'));
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

    document.querySelector('#modal-pdf form').addEventListener('submit', () => {
        modal.style.display = 'none';
        setTimeout(() => window.location.reload(), 3000);
    });
})();
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
