<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: index.php");
    exit();
}

$busqueda = '';

if (isset($_GET['busqueda']) && $_GET['busqueda'] !== '') {
    $busqueda = $_GET['busqueda'];
    $sql_prd = "SELECT * FROM productos WHERE titulo LIKE '%$busqueda%' ORDER BY titulo ASC";
} else {
    $sql_prd = "SELECT * FROM productos ORDER BY titulo ASC";
}

$query_prd = mysqli_query($conexion, $sql_prd);

$producto = null;

if (isset($_GET['producto_id']) && $_GET['producto_id'] != '') { // Si existe el producto_id en la URL se carga el producto
    $id_producto = $_GET['producto_id'];

    $sql_prod = "SELECT * FROM productos WHERE id = '$id_producto' LIMIT 1";
    $result_prod = mysqli_query($conexion, $sql_prod);

    if ($result_prod && mysqli_num_rows($result_prod) > 0) {
        $producto = mysqli_fetch_assoc($result_prod);
    }
}

// Crear cotización si no existe y LÓGICA DE RECUPERACIÓN DE SESIÓN 
if (!isset($_SESSION['cotizacion_id'])) { // El metodo isset sirve para verificar que cotización_id esté definida
    $usuario = $_SESSION['usuario_nombre'];

    // PASO 1: Buscar la última cotización QUE TENGA ÍTEMS Y QUE NO HAYA SIDO GENERADA AÚN, La clave es: AND c.numero_cotizacion IS NULL
    $sql_recuperar = "SELECT c.id
                      FROM cotizaciones c 
                      INNER JOIN cotizacion_items i ON c.id = i.cotizacion_id 
                      WHERE c.usuario_nombre = '$usuario' 
                      AND (c.numero_cotizacion IS NULL OR c.numero_cotizacion = '')
                      ORDER BY c.id DESC 
                      LIMIT 1";
    
    $res_recuperar = mysqli_query($conexion, $sql_recuperar);

    if ($fila = mysqli_fetch_assoc($res_recuperar)) {
        // ENCONTRADO: Recuperamos el borrador pendiente
        $_SESSION['cotizacion_id'] = $fila['id'];
    } else {
        // PASO 2: Si no hay borradores con items, buscamos si hay una cabecera vacía sin generar
        $sql_vacia = "SELECT id FROM cotizaciones 
                      WHERE usuario_nombre = '$usuario' 
                      AND (numero_cotizacion IS NULL OR numero_cotizacion = '')
                      ORDER BY id DESC LIMIT 1";
        $res_vacia = mysqli_query($conexion, $sql_vacia);

        if ($fila_vacia = mysqli_fetch_assoc($res_vacia)) {
            $_SESSION['cotizacion_id'] = $fila_vacia['id'];
        } else {
            // PASO 3: Si todo lo anterior está "cerrado" (ya tiene número de cotización), CREAMOS UNA NUEVA COTIZACIÓN (Esto hará que el ID aumente y la tabla empiece vacía)
            // IMPORTANTE!!!
            $sql_insert = "INSERT INTO cotizaciones (usuario_nombre) VALUES ('$usuario')"; // Se Ingresa el Nombre del que va a hacer la cotizacion en la tabla
            mysqli_query($conexion, $sql_insert);
            $_SESSION['cotizacion_id'] = mysqli_insert_id($conexion);
        }
    }
}
$cotizacion_id = $_SESSION['cotizacion_id'];

// Guardar ítem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'guardar_item') {
    $producto_id = $_POST['producto_id']; 

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $iva = $_POST['IVA'];
    $precio = $_POST['precio'];

    $foto = $_POST['foto_actual'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_nombre = basename($_FILES['foto']['name']);

        $dir = '../uploads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); 
        }

        $nombre_final = time() . "_" . $foto_nombre;
        $ruta_destino = $dir . $nombre_final;

        if (move_uploaded_file($foto_temp, $ruta_destino)) {
            $foto = $nombre_final;
        }
    }

    $sql = "INSERT INTO cotizacion_items 
            (cotizacion_id, titulo ,foto ,descripcion, cantidad, iva, precio) 
            VALUES 
            ('$cotizacion_id', '$titulo'  ,'$foto','$descripcion', '$cantidad', '$iva', '$precio')";
    mysqli_query($conexion, $sql);

    // Validar si existe el producto en la base de datos
    if ($producto_id == '') {
    $sql_productos = "SELECT id FROM productos WHERE titulo = '$titulo' LIMIT 1"; // esto es para evitar duplicados
    $result_productos = mysqli_query($conexion, $sql_productos);

    if (mysqli_num_rows($result_productos) == 0) { // esto significa que el producto no existe
        $sql_productos = "INSERT INTO productos
            (titulo, foto, descripcion, cantidad, iva, precio)
            VALUES
            ('$titulo','$foto','$descripcion','$cantidad','$iva','$precio')";
        mysqli_query($conexion, $sql_productos);
        }
    }
    
    header("Location: crear_cotizacion.php"); 
    exit();
}

// Obtener ítems de la cotización actual
$sql_items = "SELECT * FROM cotizacion_items WHERE cotizacion_id = '$cotizacion_id' ORDER BY id ASC";
$query_items = mysqli_query($conexion, $sql_items);
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
                <input type="text" name="busqueda" value="<?php echo $busqueda ?>" placeholder="Buscar producto...">
                <button type="submit" class="boton-primario">Buscar</button>
                <?php if($busqueda != ''): ?>
                <a href="crear_cotizacion.php" class="boton-limpiar">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="seleccion-producto">
            <form method="GET" action="crear_cotizacion.php" class="formulario">
                <input type="hidden" name="busqueda" value="<?php echo $busqueda ?>">
                <!-- Esto es para que el buscador funcione -->
                <select name="producto_id" class="producto-existente" required>
                    <option value="">Seleccione un producto</option>
                    <?php while ($prd = mysqli_fetch_assoc($query_prd)): ?>
                    <!-- Esto es para mostrar los productos que hay en la base de datos -->
                    <option value="<?php echo $prd['id']; ?>" <?php if (isset($_GET['producto_id']) &&
                        $_GET['producto_id']==$prd['id']) echo 'selected' ; ?>> <!-- -->
                        <?php echo $prd['titulo']; ?>
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
                <input type="hidden" name="producto_id" value="<?php echo $producto['id'] ?? '' ?>">

                <div class="grupo-campo">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="titulo" value="<?php echo $producto['titulo'] ?? '' ?>" required>
                </div>

                <div class="grupo-campo">
                    <input type="hidden" name="foto_actual" value="<?php echo $producto['foto'] ?? '' ?>">
                    <label>Foto del Producto</label>
                    <input type="file" name="foto">
                    <?php if(!empty($producto['foto'])): ?>
                    <div class="grupo-campo">
                        <label>Imagen actual</label><br>
                        <img src="../uploads/<?php echo $producto['foto']; ?>" width="150">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="grupo-campo">
                    <label>Descripción *</label>
                    <textarea name="descripcion" required><?php echo $producto['descripcion'] ?? '' ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" value="<?php echo $producto['cantidad'] ?? 0 ?>" required>
                </div>

                <div class="grupo-campo">
                    <label>Valor con IVA *</label>
                    <select name="IVA" required>
                        <option value="">Seleccione una Opción</option>
                        <option value="si" <?php if (($producto['iva'] ?? '' )=='si' ) echo 'selected' ; ?>>Aplicar IVA
                        </option>
                        <option value="no" <?php if (($producto['iva'] ?? '' )=='no' ) echo 'selected' ; ?>>No Aplicar
                            IVA</option>
                    </select>

                </div>

                <div class="grupo-campo">
                    <label>Precio Unitario *</label>
                    <input type="number" name="precio" value="<?php echo $producto['precio'] ?? '' ?>" required>
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
                        <td>
                            <?php echo $item['titulo']; ?>
                        </td>
                        <td>
                            <?php echo $item['cantidad']; ?>
                        </td>
                        <td>
                            <?php echo $item['iva'] === 'si' ? 'Sí' : 'No'; ?>
                        </td>
                        <td>
                            <?php echo number_format($item['precio'], 0, '', '.'); ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_cotizacion.php?id=<?php echo $item['id']; ?>" class="boton-editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_cotizacion.php?id=<?php echo $item['id']; ?>" class="boton-eliminar"
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