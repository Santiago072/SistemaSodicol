<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();
$mensaje_error = '';

// Verificar sesión de cotización
if (!isset($_SESSION['cotizacion_id'])) {
    header("Location: crear_cotizacion.php?error=no_session");
    exit();
}

$cotizacion_id = intval($_SESSION['cotizacion_id']);

// PROCESAR FORMULARIO DE EDICIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
        $mensaje_error = "Token de seguridad inválido";
    } else {
        $item_id = intval($_POST['item_id']);
        $titulo = sanitizar_entrada($_POST['titulo']);
        $descripcion = sanitizar_entrada($_POST['descripcion']);
        $cantidad = intval($_POST['cantidad']);
        $iva = sanitizar_entrada($_POST['IVA']);
        $precio = floatval($_POST['precio']);

        // Validaciones
        if (!in_array($iva, ['si', 'no'])) {
            $mensaje_error = "Valor de IVA no válido";
        } elseif ($cantidad <= 0 || $precio < 0) {
            $mensaje_error = "Cantidad y precio deben ser valores válidos";
        } else {
            $ruta_final = $_POST['foto_actual'];

            // Procesar nueva foto si se subió
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $validacion = validar_imagen($_FILES['foto']);
                
                if ($validacion['valido']) {
                    $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    $nombre_archivo = generar_nombre_archivo($extension);
                    $dir = '../uploads/';
                    
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    $ruta_destino = $dir . $nombre_archivo;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                        // Eliminar foto anterior
                        if (!empty($_POST['foto_actual'])) {
                            $ruta_anterior = '../uploads/' . $_POST['foto_actual'];
                            if (file_exists($ruta_anterior)) {
                                unlink($ruta_anterior);
                            }
                        }
                        $ruta_final = $nombre_archivo;
                    } else {
                        $mensaje_error = "Error al subir el archivo";
                    }
                } else {
                    $mensaje_error = $validacion['mensaje'];
                }
            } else {
                // Mantener solo el nombre del archivo
                $ruta_final = basename($_POST['foto_actual']);
            }

            if (empty($mensaje_error)) {
                // Actualizar usando prepared statement
                $stmt = mysqli_prepare($conexion, "UPDATE cotizacion_items SET titulo=?, foto=?, descripcion=?, cantidad=?, iva=?, precio=? WHERE id=? AND cotizacion_id=?");
                mysqli_stmt_bind_param($stmt, "sssissii", $titulo, $ruta_final, $descripcion, $cantidad, $iva, $precio, $item_id, $cotizacion_id);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: crear_cotizacion.php?updated=1");
                    exit();
                } else {
                    $mensaje_error = "Error al actualizar el ítem";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// OBTENER DATOS PARA MOSTRAR EN EL FORMULARIO (GET)
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: crear_cotizacion.php?error=invalid_id");
    exit();
}

$item_id = intval($_GET['id']);
$stmt = mysqli_prepare($conexion, "SELECT * FROM cotizacion_items WHERE id = ? AND cotizacion_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $item_id, $cotizacion_id);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
$datos = mysqli_fetch_array($query);
mysqli_stmt_close($stmt);

if (!$datos) {
    header("Location: crear_cotizacion.php?error=not_found");
    exit();
}

$csrf_token = generar_token_csrf();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Ítem</title>
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
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
            <h1>Editar Ítem</h1>
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
                <input type="hidden" name="item_id" value="<?php echo intval($datos['id']); ?>">
                <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($datos['foto']); ?>">

                <div class="grupo-campo">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="titulo" value="<?php echo htmlspecialchars($datos['titulo']); ?>" required maxlength="100">
                </div>

                <div class="grupo-campo">
                    <label>Foto Actual</label>
                    <?php if(!empty($datos['foto'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../uploads/<?php echo htmlspecialchars($datos['foto']); ?>" width="100" style="border:1px solid #ccc; max-width: 200px;">
                    </div>
                    <?php else: ?>
                    <p>No hay foto asignada</p>
                    <?php endif; ?>
                    <label>Cambiar Foto (Opcional)</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small style="color: #666;">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
                </div>

                <div class="grupo-campo">
                    <label>Descripción *</label>
                    <textarea name="descripcion" required maxlength="1000"><?php echo htmlspecialchars($datos['descripcion']); ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" value="<?php echo intval($datos['cantidad']); ?>" required min="1">
                </div>

                <div class="grupo-campo">
                    <label>Valor con IVA *</label>
                    <select name="IVA" required>
                        <option value="si" <?php if($datos['iva']=='si') echo 'selected'; ?>>Aplicar IVA</option>
                        <option value="no" <?php if($datos['iva']=='no') echo 'selected'; ?>>No Aplicar IVA</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label>Precio Unitario *</label>
                    <input type="number" name="precio" value="<?php echo floatval($datos['precio']); ?>" required min="0" step="0.01">
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