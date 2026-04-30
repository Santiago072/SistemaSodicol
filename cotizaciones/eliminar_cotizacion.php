<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Verificar sesión
if (!isset($_SESSION['usuario_nombre']) || !isset($_SESSION['cotizacion_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $cotizacion_id = $_SESSION['cotizacion_id'];

    // 1. Primero obtenemos la ruta de la foto para borrar el archivo físico
    $query_foto = "SELECT foto FROM cotizacion_items WHERE id = '$item_id' AND cotizacion_id = '$cotizacion_id'";
    $resultado = mysqli_query($conexion, $query_foto);
    
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $ruta_foto = $fila['foto'];
        // Si existe una foto y el archivo existe en el servidor, lo borramos
        if (!empty($ruta_foto) && file_exists($ruta_foto)) {
            unlink($ruta_foto); 
        }
    }

    // 2. Eliminamos el registro de la base de datos
    $sql = "DELETE FROM cotizacion_items WHERE id = '$item_id' AND cotizacion_id = '$cotizacion_id'";
    
    if(mysqli_query($conexion, $sql)) {
        // Redirigir con éxito
        header("Location: crear_cotizacion.php?mensaje=eliminado");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
} else {
    header("Location: crear_cotizacion.php");
}
exit();
?>