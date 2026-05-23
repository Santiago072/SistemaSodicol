<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();

// Validar ID de producto
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: lista_productos.php?error=1");
    exit();
}

$id_producto = intval($_GET['id']);

// Obtener información del producto para eliminar la foto
$stmt = mysqli_prepare($conexion, "SELECT foto FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_producto);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Eliminar producto usando prepared statement
$stmt = mysqli_prepare($conexion, "DELETE FROM productos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_producto);

if (mysqli_stmt_execute($stmt)) {
    // Eliminar foto del servidor si existe
    if ($producto && !empty($producto['foto'])) {
        $ruta_foto = '../uploads/' . $producto['foto'];
        if (file_exists($ruta_foto)) {
            unlink($ruta_foto);
        }
    }
    mysqli_stmt_close($stmt);
    header("Location: lista_productos.php?deleted=1");
} else {
    mysqli_stmt_close($stmt);
    header("Location: lista_productos.php?error=1");
}
exit();
?>