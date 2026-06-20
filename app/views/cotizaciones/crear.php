<?php
/**
 * Vista: Crear cotización
 * Variables: $productos, $producto, $busqueda, $items, $totalItems, $csrf_token
 */
$pageTitle = 'Crear Cotización';
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Crear Cotización';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Crear Nueva Cotización</h1></div>

    <!-- Búsqueda de producto (AJAX) -->
    <div class="barra-busqueda">
        <form id="form-busqueda-ajax" class="formulario-busqueda" onsubmit="event.preventDefault(); return false;">
            <input type="text" id="input-busqueda" class="form-control" 
                   placeholder="Buscar producto..." autocomplete="off" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); return false; }">
            <button type="button" id="btn-limpiar-busqueda" class="boton-limpiar d-none">Limpiar</button>
        </form>
    </div>

    <!-- Seleccionar producto existente (AJAX) -->
    <div class="seleccion-producto">
        <form id="form-usar-producto" class="formulario" onsubmit="return false;">
            <select id="select-producto" class="producto-existente" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $prd): ?>
                <option value="<?= intval($prd['id']) ?>"
                    <?= (isset($_GET['producto_id']) && $_GET['producto_id'] == $prd['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prd['titulo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="btn-usar-producto" class="boton-primario">Usar producto</button>
            <button type="button" id="btn-limpiar-form" class="boton-limpiar">Limpiar Campos</button>
        </form>
    </div>
    <br>

    <!-- Formulario de ítem -->
    <div class="formulario-contenedor formulario-cotizacion">
        <form method="POST" enctype="multipart/form-data"
              action="<?= $basePath ?>?module=cotizaciones&action=crear" class="formulario">
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
                <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máx: 5MB</small>
                <div id="img-preview-container" class="grupo-campo mt-10">
                    <?php if (!empty($producto['foto'])): ?>
                    <label>Imagen actual</label><br>
                    <img src="<?= $basePath ?>uploads/<?= htmlspecialchars($producto['foto']) ?>"
                         width="150" class="img-preview">
                    <?php endif; ?>
                </div>
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
                        <a href="<?= $basePath ?>?module=cotizaciones&action=editar_item&id=<?= intval($item['id']) ?>"
                           class="boton-editar"><i class="fas fa-edit"></i></a>
                        <a href="<?= $basePath ?>?module=cotizaciones&action=eliminar_item&id=<?= intval($item['id']) ?>"
                           class="boton-eliminar"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="5" class="text-center p-30 text-gold">
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
        <form action="<?= $basePath ?>?module=cotizaciones&action=generar_pdf" method="POST" target="_blank">
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

    // ── Lógica AJAX para búsqueda y selección de productos ──
    const inputBusqueda = document.getElementById('input-busqueda');
    const btnLimpiarBusqueda = document.getElementById('btn-limpiar-busqueda');
    const selectProducto = document.getElementById('select-producto');
    const btnUsar = document.getElementById('btn-usar-producto');
    const btnLimpiarForm = document.getElementById('btn-limpiar-form');
    let timeoutBusqueda = null;

    function ejecutarBusqueda() {
        const query = inputBusqueda.value;
        fetch(`<?= $basePath ?>?module=cotizaciones&action=ajax_buscar_productos&busqueda=` + encodeURIComponent(query), {
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(res => {
            if(res.status === 'success') {
                selectProducto.innerHTML = '<option value="">Seleccione un producto</option>';
                res.data.forEach(prd => {
                    const opt = document.createElement('option');
                    opt.value = prd.id;
                    opt.textContent = prd.titulo;
                    selectProducto.appendChild(opt);
                });
                btnLimpiarBusqueda.style.display = query ? 'inline-block' : 'none';
            }
        });
    }


    // Búsqueda en vivo (debounce)
    inputBusqueda.addEventListener('input', () => {
        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(ejecutarBusqueda, 300);
    });

    btnLimpiarBusqueda.addEventListener('click', () => {
        inputBusqueda.value = '';
        ejecutarBusqueda();
    });

    // Autocompletar formulario al usar producto
    btnUsar.addEventListener('click', () => {
        const id = selectProducto.value;
        if(!id) {
            alert('Por favor seleccione un producto de la lista primero.');
            return;
        }
        
        // Mostrar estado de carga en el botón
        const txtOriginal = btnUsar.textContent;
        btnUsar.textContent = 'Cargando...';
        btnUsar.disabled = true;

        fetch(`<?= $basePath ?>?module=cotizaciones&action=ajax_get_producto&id=` + encodeURIComponent(id))
        .then(r => r.json())
        .then(res => {
            if(res.status === 'success') {
                const p = res.data;
                document.querySelector('input[name="producto_id"]').value = p.id;
                document.querySelector('input[name="titulo"]').value = p.titulo;
                document.querySelector('textarea[name="descripcion"]').value = p.descripcion;
                document.querySelector('input[name="cantidad"]').value = 1; // Valor por defecto sensato al agregar
                document.querySelector('select[name="IVA"]').value = p.iva;
                document.querySelector('input[name="precio"]').value = p.precio;
                
                const imgPreviewContainer = document.getElementById('img-preview-container');
                if(p.foto && imgPreviewContainer) {
                    imgPreviewContainer.innerHTML = '<label>Imagen actual</label><br><img src="<?= $basePath ?>uploads/' + p.foto + '" width="150" class="img-preview">';
                    document.querySelector('input[name="foto_actual"]').value = p.foto;
                } else if (imgPreviewContainer) {
                    imgPreviewContainer.innerHTML = '';
                    document.querySelector('input[name="foto_actual"]').value = '';
                }
            } else {
                alert(res.message || 'Error al cargar el producto');
            }
        })
        .finally(() => {
            btnUsar.textContent = txtOriginal;
            btnUsar.disabled = false;
        });
    });

    btnLimpiarForm.addEventListener('click', () => {
        document.querySelector('input[name="producto_id"]').value = '0';
        document.querySelector('input[name="titulo"]').value = '';
        document.querySelector('textarea[name="descripcion"]').value = '';
        document.querySelector('input[name="cantidad"]').value = '0';
        document.querySelector('select[name="IVA"]').value = '';
        document.querySelector('input[name="precio"]').value = '0';
        document.querySelector('input[name="foto_actual"]').value = '';
        const imgPreviewContainer = document.getElementById('img-preview-container');
        if(imgPreviewContainer) imgPreviewContainer.innerHTML = '';
        selectProducto.value = '';
    });

})();
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
