<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();
$usuario = $_SESSION['usuario_nombre'];
$usuario_id = $_SESSION['usuario_id'];

// Búsqueda de productos con prepared statement
$busqueda = '';
if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
    $busqueda = sanitizar_entrada($_GET['busqueda']);
    $busqueda_param = "%$busqueda%";
    $stmt_prd = mysqli_prepare($conexion, "SELECT * FROM productos WHERE titulo LIKE ? ORDER BY titulo ASC");
    mysqli_stmt_bind_param($stmt_prd, "s", $busqueda_param);
    mysqli_stmt_execute($stmt_prd);
    $query_prd = mysqli_stmt_get_result($stmt_prd);
} else {
    $query_prd = mysqli_query($conexion, "SELECT * FROM productos ORDER BY titulo ASC");
}

// Cargar producto seleccionado con prepared statement
$producto = null;
if (isset($_GET['producto_id']) && validar_numero($_GET['producto_id'])) {
    $id_producto = intval($_GET['producto_id']);
    $stmt_prod = mysqli_prepare($conexion, "SELECT * FROM productos WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_prod, "i", $id_producto);
    mysqli_stmt_execute($stmt_prod);
    $result_prod = mysqli_stmt_get_result($stmt_prod);
    if ($result_prod && mysqli_num_rows($result_prod) > 0) {
        $producto = mysqli_fetch_assoc($result_prod);
    }
    mysqli_stmt_close($stmt_prod);
}

// Lógica de recuperación de sesión de cotización
if (!isset($_SESSION['cotizacion_id'])) {
    // Buscar borrador con ítems sin generar
    $stmt_rec = mysqli_prepare($conexion, "SELECT c.id FROM cotizaciones c 
                      INNER JOIN cotizacion_items i ON c.id = i.cotizacion_id 
                      WHERE c.usuario_nombre = ? 
                      AND (c.numero_cotizacion IS NULL OR c.numero_cotizacion = '')
                      ORDER BY c.id DESC LIMIT 1");
    mysqli_stmt_bind_param($stmt_rec, "s", $usuario);
    mysqli_stmt_execute($stmt_rec);
    $res_rec = mysqli_stmt_get_result($stmt_rec);
    mysqli_stmt_close($stmt_rec);

    if ($fila = mysqli_fetch_assoc($res_rec)) {
        $_SESSION['cotizacion_id'] = $fila['id'];
    } else {
        // Buscar cabecera vacía sin generar
        $stmt_vacia = mysqli_prepare($conexion, "SELECT id FROM cotizaciones 
                      WHERE usuario_nombre = ? 
                      AND (numero_cotizacion IS NULL OR numero_cotizacion = '')
                      ORDER BY id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt_vacia, "s", $usuario);
        mysqli_stmt_execute($stmt_vacia);
        $res_vacia = mysqli_stmt_get_result($stmt_vacia);
        mysqli_stmt_close($stmt_vacia);

        if ($fila_vacia = mysqli_fetch_assoc($res_vacia)) {
            $_SESSION['cotizacion_id'] = $fila_vacia['id'];
        } else {
            // Crear nueva cotización
            $stmt_ins = mysqli_prepare($conexion, "INSERT INTO cotizaciones (usuario_nombre) VALUES (?)");
            mysqli_stmt_bind_param($stmt_ins, "s", $usuario);
            mysqli_stmt_execute($stmt_ins);
            $_SESSION['cotizacion_id'] = mysqli_stmt_insert_id($stmt_ins);
            mysqli_stmt_close($stmt_ins);
        }
    }
}
$cotizacion_id = intval($_SESSION['cotizacion_id']);

// Guardar ítem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'guardar_item') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
        header("Location: crear_cotizacion.php?error=csrf");
        exit();
    }

    $producto_id = isset($_POST['producto_id']) && validar_numero($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $titulo      = sanitizar_entrada($_POST['titulo']);
    $descripcion = sanitizar_entrada($_POST['descripcion']);
    $cantidad    = intval($_POST['cantidad']);
    $iva         = sanitizar_entrada($_POST['IVA']);
    $precio      = floatval($_POST['precio']);

    if (!in_array($iva, ['si', 'no'])) {
        header("Location: crear_cotizacion.php?error=iva");
        exit();
    }

    $foto = $_POST['foto_actual'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $validacion = validar_imagen($_FILES['foto']);
        if ($validacion['valido']) {
            $extension   = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $nombre_final = generar_nombre_archivo($extension);
            $dir = '../uploads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nombre_final)) {
                $foto = $nombre_final;
            }
        }
    } else {
        $foto = basename($foto);
    }

    // Insertar ítem con prepared statement
    $stmt = mysqli_prepare($conexion, "INSERT INTO cotizacion_items (cotizacion_id, titulo, foto, descripcion, cantidad, iva, precio) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssisd", $cotizacion_id, $titulo, $foto, $descripcion, $cantidad, $iva, $precio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Registrar en catálogo de productos si no existe
    if ($producto_id == 0) {
        $stmt_check = mysqli_prepare($conexion, "SELECT id FROM productos WHERE titulo = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_check, "s", $titulo);
        mysqli_stmt_execute($stmt_check);
        $res_check = mysqli_stmt_get_result($stmt_check);
        mysqli_stmt_close($stmt_check);

        if (mysqli_num_rows($res_check) == 0) {
            $stmt_ins_prod = mysqli_prepare($conexion, "INSERT INTO productos (titulo, foto, descripcion, cantidad, iva, precio) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_ins_prod, "sssisd", $titulo, $foto, $descripcion, $cantidad, $iva, $precio);
            mysqli_stmt_execute($stmt_ins_prod);
            mysqli_stmt_close($stmt_ins_prod);
        }
    }

    header("Location: crear_cotizacion.php");
    exit();
}

// Obtener ítems de la cotización actual
$stmt_items = mysqli_prepare($conexion, "SELECT * FROM cotizacion_items WHERE cotizacion_id = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt_items, "i", $cotizacion_id);
mysqli_stmt_execute($stmt_items);
$query_items = mysqli_stmt_get_result($stmt_items);

$csrf_token = generar_token_csrf();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
    <title>Crear Cotización</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>
    <canvas id="particle-canvas"></canvas>
    <div class="noise-overlay"></div>
    <?php include '../includes/menu.php'; ?>

    <div class="contenido-principal">
        <div class="cabecera-superior">
            <button class="boton-menu-ocultar" id="btnMenu">
                <i class="fas fa-bars"></i> Ocultar Menú
            </button>
            <!-- Botón modo día/noche -->
            <button class="btn-modo" id="btnModo" title="Cambiar tema">
                <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
                <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
                <span class="modo-label"></span>
            </button>
        </div>

        <div class="encabezado-pagina">
            <h1>Crear Nueva Cotización</h1>
        </div>

        <div class="barra-busqueda">
            <form action="crear_cotizacion.php" method="GET" class="formulario-busqueda">
                <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda) ?>" placeholder="Buscar producto...">
                <button type="submit" class="boton-primario">Buscar</button>
                <?php if($busqueda != ''): ?>
                <a href="crear_cotizacion.php" class="boton-limpiar">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="seleccion-producto">
            <form method="GET" action="crear_cotizacion.php" class="formulario">
                <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda) ?>">
                <select name="producto_id" class="producto-existente" required>
                    <option value="">Seleccione un producto</option>
                    <?php while ($prd = mysqli_fetch_assoc($query_prd)): ?>
                    <option value="<?php echo intval($prd['id']); ?>" <?php if (isset($_GET['producto_id']) && $_GET['producto_id']==$prd['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($prd['titulo']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="boton-primario">Usar producto</button>
                <a href="crear_cotizacion.php" class="boton-limpiar">Limpiar</a>
            </form>
        </div>
        <br>
        <div class="formulario-contenedor formulario-cotizacion">
            <form method="POST" enctype="multipart/form-data" class="formulario">
                <input type="hidden" name="action" value="guardar_item">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="producto_id" value="<?php echo intval($producto['id'] ?? 0) ?>">

                <div class="grupo-campo">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($producto['titulo'] ?? '') ?>" required maxlength="100">
                </div>

                <div class="grupo-campo">
                    <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($producto['foto'] ?? '') ?>">
                    <label>Foto del Producto</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: #666;">Formatos: JPG, PNG, GIF, WEBP. Máx: 5MB</small>
                    <?php if(!empty($producto['foto'])): ?>
                    <div class="grupo-campo">
                        <label>Imagen actual</label><br>
                        <img src="../uploads/<?php echo htmlspecialchars($producto['foto']); ?>" width="150" style="max-width:200px;">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="grupo-campo">
                    <label>Descripción *</label>
                    <textarea name="descripcion" required maxlength="1000"><?php echo htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" value="<?php echo intval($producto['cantidad'] ?? 0) ?>" required min="1">
                </div>

                <div class="grupo-campo">
                    <label>Valor con IVA *</label>
                    <select name="IVA" required>
                        <option value="">Seleccione una Opción</option>
                        <option value="si" <?php if (($producto['iva'] ?? '') == 'si') echo 'selected'; ?>>Aplicar IVA</option>
                        <option value="no" <?php if (($producto['iva'] ?? '') == 'no') echo 'selected'; ?>>No Aplicar IVA</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label>Precio Unitario *</label>
                    <input type="number" name="precio" value="<?php echo floatval($producto['precio'] ?? 0) ?>" required min="0" step="0.01">
                </div>

                <div class="grupo-campo">
                    <button type="submit" class="boton-primario">Guardar Ítem</button>
                    <button type="button" id="abrir-modal-pdf" class="boton-generar-pdf">Cotización Lista</button>
                </div>
            </form>
        </div>
        <br>
        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Nombre del Producto</th>
                        <th>Cantidad</th>
                        <th>IVA</th>
                        <th>Precio Unitario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_array($query_items)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['titulo']); ?></td>
                        <td><?php echo intval($item['cantidad']); ?></td>
                        <td><?php echo $item['iva'] === 'si' ? 'Sí' : 'No'; ?></td>
                        <td><?php echo number_format($item['precio'], 0, '', '.'); ?></td>
                        <td class="acciones-tabla">
                            <a href="editar_cotizacion.php?id=<?php echo intval($item['id']); ?>" class="boton-editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_cotizacion.php?id=<?php echo intval($item['id']); ?>" class="boton-eliminar"
                                onclick="return confirm('¿Eliminar este ítem?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para datos del cliente -->
    <div id="modal-pdf" class="modal">
        <div class="modal-contenido">
            <span class="cerrar">&times;</span>
            <h2>Datos del Cliente</h2>
            <form action="generar_pdf.php" method="POST" target="_blank">

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
        // Control del modal
        const modal = document.getElementById('modal-pdf');
        const abrir = document.getElementById('abrir-modal-pdf');
        const cerrar = document.querySelectorAll('.cerrar, .cerrar-btn');
        
        abrir.addEventListener('click', () => {
            <?php $total = mysqli_num_rows($query_items); ?>
            if (<?php echo $total; ?> === 0) {
            alert('Debe agregar al menos un ítem antes de generar el PDF');
            return;
        }
        modal.style.display = 'block';
    });

    cerrar.forEach(btn => btn.addEventListener('click', () => modal.style.display = 'none'));
    window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // RECARGA AUTOMÁTICA AL GENERAR PDF
        const formPDF = document.querySelector('#modal-pdf form');
        formPDF.addEventListener('submit', function () {
            modal.style.display = 'none'; // Ocultar modal
            setTimeout(() => {
                window.location.reload(); // Recargar para limpiar datos
            }, 3000);
        });
    </script>
    <script src="../includes/script.js"></script>
</body>

</html>