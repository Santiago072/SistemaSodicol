<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: index.php");
    exit();
}

$cotizacion_id = $_SESSION['cotizacion_id'];

// --- PARTE 1: PROCESAR EL FORMULARIO DE EDICIÓN (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $iva = $_POST['IVA'];
    $precio = $_POST['precio'];

    // Lógica de actualización de foto
    // Por defecto, conservamos la ruta anterior (hidden input)
    $ruta_final = $_POST['foto_actual']; 

    // Si el usuario subió una NUEVA foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_nombre = basename($_FILES['foto']['name']);
        $dir = '../uploads/';
        
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $nombre_final = time() . "_" . $foto_nombre;
        $ruta_destino = $dir . $nombre_final;

        if (move_uploaded_file($foto_temp, $ruta_destino)) {
            $ruta_final = $nombre_final;
            
            // Opcional: Borrar la foto anterior para limpiar servidor
            if (!empty($_POST['foto_actual']) && file_exists($_POST['foto_actual'])) {
                unlink($_POST['foto_actual']);
            }
        }
    }

    // Actualizar base de datos
    $sql_update = "UPDATE cotizacion_items SET 
                   titulo='$titulo', 
                   foto='$ruta_final', 
                   descripcion='$descripcion', 
                   cantidad='$cantidad', 
                   iva='$iva', 
                   precio='$precio' 
                   WHERE id='$item_id' AND cotizacion_id='$cotizacion_id'";

    if (mysqli_query($conexion, $sql_update)) {
        header("Location: crear_cotizacion.php?mensaje=editado");
        exit();
    } else {
        $error = "Error al actualizar: " . mysqli_error($conexion);
    }
}

// --- PARTE 2: OBTENER DATOS PARA MOSTRAR EN EL FORMULARIO (GET) ---
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $sql_get = "SELECT * FROM cotizacion_items WHERE id = '$item_id' AND cotizacion_id = '$cotizacion_id'";
    $query = mysqli_query($conexion, $sql_get);
    $datos = mysqli_fetch_array($query);

    if (!$datos) {
        header("Location: crear_cotizacion.php"); // Si no existe el ID, volver
        exit();
    }
} else {
    header("Location: crear_cotizacion.php");
    exit();
}
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

        <div class="formulario-contenedor">
            <form method="POST" enctype="multipart/form-data" class="formulario">
                <input type="hidden" name="item_id" value="<?php echo $datos['id']; ?>">
                <input type="hidden" name="foto_actual" value="<?php echo $datos['foto']; ?>">

                <div class="grupo-campo">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="titulo" value="<?php echo $datos['titulo']; ?>" required>
                </div>

                <div class="grupo-campo">
                    <label>Foto Actual</label>
                    <?php if(!empty($datos['foto'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../uploads/<?php echo $datos['foto']; ?>" width="100" style="border:1px solid #ccc;">
                    </div>
                    <?php else: ?>
                    <p>No hay foto asignada</p>
                    <?php endif; ?>
                    <label>Cambiar Foto (Opcional)</label>
                    <input type="file" name="foto">
                </div>

                <div class="grupo-campo">
                    <label>Descripción *</label>
                    <textarea name="descripcion" required><?php echo $datos['descripcion']; ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" value="<?php echo $datos['cantidad']; ?>" required min="1">
                </div>

                <div class="grupo-campo">
                    <label>Valor con IVA *</label>
                    <select name="IVA" required>
                        <option value="si" <?php if($datos['iva']=='si' ) echo 'selected' ; ?>>Aplicar IVA</option>
                        <option value="no" <?php if($datos['iva']=='no' ) echo 'selected' ; ?>>No Aplicar IVA</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label>Precio Unitario *</label>
                    <input type="number" name="precio" value="<?php echo $datos['precio']; ?>" required min="0"
                        step="0.01">
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