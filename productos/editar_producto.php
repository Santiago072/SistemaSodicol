<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();
$mensaje_error = '';

// Validar ID de producto
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: lista_productos.php?error=1");
    exit();
}

$id_producto = intval($_GET['id']);

// Obtener producto usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT * FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_producto);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$producto) {
    header("Location: lista_productos.php?error=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
        $mensaje_error = "Token de seguridad inválido";
    } else {
        $titulo = sanitizar_entrada($_POST['titulo']);
        $descripcion = sanitizar_entrada($_POST['descripcion']);
        $cantidad = intval($_POST['cantidad']);
        $iva = sanitizar_entrada($_POST['iva']);
        $precio = floatval($_POST['precio']);

        // Validaciones
        if (!in_array($iva, ['si', 'no'])) {
            $mensaje_error = "Valor de IVA no válido";
        } elseif ($cantidad < 0 || $precio < 0) {
            $mensaje_error = "Cantidad y precio deben ser valores positivos";
        } else {
            $ruta_final = $_POST['foto_actual'];

            // Validar y procesar archivo de imagen
            if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $validacion = validar_imagen($_FILES['foto']);
                
                if ($validacion['valido']) {
                    $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    $nombre_archivo = generar_nombre_archivo($extension);
                    $dir = '../uploads/';
                    
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    $ruta_destino = $dir . $nombre_archivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                        // Eliminar foto anterior si existe
                        if (!empty($_POST['foto_actual']) && file_exists($_POST['foto_actual'])) {
                            unlink($_POST['foto_actual']);
                        }
                        $ruta_final = $nombre_archivo;
                    } else {
                        $mensaje_error = "Error al subir el archivo";
                    }
                } else {
                    $mensaje_error = $validacion['mensaje'];
                }
            } else {
                // Si no se sube nueva foto, mantener solo el nombre del archivo
                $ruta_final = basename($_POST['foto_actual']);
            }

            if (empty($mensaje_error)) {
                // Actualizar producto usando prepared statement
                $stmt = mysqli_prepare($conexion, "UPDATE productos SET titulo=?, foto=?, descripcion=?, cantidad=?, iva=?, precio=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssisdi", $titulo, $ruta_final, $descripcion, $cantidad, $iva, $precio, $id_producto);
                
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: lista_productos.php?updated=1");
                    exit();
                } else {
                    $mensaje_error = "Error al actualizar el producto";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

$csrf_token = generar_token_csrf();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
    <title>Editar Producto</title>
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
            <h1>Editar Producto</h1>
        </div>
        
        <?php if ($mensaje_error != '') { ?>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
        <?php } ?>
        
        <div class="formulario-contenedor">
            <form method="POST" enctype="multipart/form-data" class="formulario">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo intval($producto['id']); ?>">
                <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($producto['foto']); ?>">

                <div class="grupo-campo">
                    <label for="titulo">Nombre del Producto *</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($producto['titulo']); ?>" required maxlength="255">
                </div>

                <div class="grupo-campo">
                    <label>Foto Actual del Producto</label>
                    <?php if(!empty($producto['foto'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../uploads/<?php echo htmlspecialchars($producto['foto']); ?>" width="100" style="border:1px solid #ccc; max-width: 200px;">
                    </div>
                    <?php else: ?>
                    <p>No hay foto asignada</p>
                    <?php endif; ?>
                    <label>Cambiar Foto (Opcional)</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: #666;">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
                </div>

                <div class="grupo-campo">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required maxlength="1000"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label for="cantidad">Cantidad *</label>
                    <input type="number" id="cantidad" name="cantidad" required min="0" value="<?php echo intval($producto['cantidad']); ?>">
                </div>

                <div class="grupo-campo">
                    <label for="iva">Valor con IVA *</label>
                    <select id="iva" name="iva" required>
                        <option value="">Seleccione una Opción</option>
                        <option value="si" <?php if($producto['iva']==='si') echo 'selected'; ?>>Aplicar IVA</option>
                        <option value="no" <?php if($producto['iva']==='no') echo 'selected'; ?>>No Aplicar IVA</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="precio">Precio Unitario *</label>
                    <input type="number" id="precio" name="precio" required min="0" step="0.01" value="<?php echo floatval($producto['precio']); ?>">
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