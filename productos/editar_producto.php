<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../index.php");
    exit();
}

$id_producto = $_GET['id'];

$sql = "SELECT * FROM productos WHERE id = $id_producto";
$result = mysqli_query($conexion, $sql);
$producto = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $iva = $_POST['iva'];
    $precio = $_POST['precio'];

    $ruta_final = $_POST['foto_actual'];

    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_temp = $_FILES['foto']['tmp_name'];
        $foto_nombre = basename($_FILES['foto']['name']);
        $dir = '../uploads/';
        
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ruta_destino = $dir . $foto_nombre; 
        if (move_uploaded_file($foto_temp, $ruta_destino)) {
            $ruta_final = $ruta_destino;
            
            // Opcional: Borrar la foto anterior para limpiar servidor
            if (!empty($_POST['foto_actual']) && file_exists($_POST['foto_actual'])) {
                unlink($_POST['foto_actual']);
            }
        }
    }   

    $sql = "UPDATE productos SET 
            titulo='$titulo',
            foto='$ruta_final',
            descripcion='$descripcion',
            cantidad='$cantidad',
            iva='$iva',
            precio='$precio'
            WHERE id='$id_producto'";

    if (mysqli_query($conexion, $sql)) {
        header("Location: lista_productos.php");
        exit();
    } else {
        $error = "Error al actualizar: " . mysqli_error($conexion);
    }
}

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
        <div class="formulario-contenedor">
            <form method="POST" enctype="multipart/form-data" class="formulario">
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                <input type="hidden" name="foto_actual" value="<?php echo $producto['foto']; ?>">

                <div class="grupo-campo">
                    <label for="titulo">Nombre del Producto *</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo $producto['titulo']; ?>" required>
                </div>

                <div class="grupo-campo">
                    <label>Foto Actula del Producto</label>
                    <?php if(!empty($producto['foto'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../uploads/<?php echo $producto['foto']; ?>" width="100"
                            style="border:1px solid #ccc;">
                    </div>
                    <?php else: ?>
                    <p>No hay foto asignada</p>
                    <?php endif; ?>
                    <label>Cambiar Foto (Opcional)</label>
                    <input type="file" id="foto" name="foto">
                </div>

                <div class="grupo-campo">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion"
                        required><?php echo $producto['descripcion']; ?></textarea>
                </div>

                <div class="grupo-campo">
                    <label for="cantidad">Cantidad *</label>
                    <input type="number" id="cantidad" name="cantidad" required min="1"
                        value="<?php echo $producto['cantidad']; ?>">
                </div>

                <div class="grupo-campo">
                    <label for="iva">Valor con IVA *</label>
                    <select id="iva" name="iva" required>
                        <option value="">Seleccione una Opción</option>
                        <option value="si" <?php if($producto['iva']==='si' ) echo 'selected' ; ?>>Aplicar IVA</option>
                        <option value="no" <?php if($producto['iva']==='no' ) echo 'selected' ; ?>>No Aplicar IVA
                        </option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="precio">Precio Unitario *</label>
                    <input type="number" id="precio" name="precio" required min="0" step="0.01"
                        value="<?php echo $producto['precio']; ?>">
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