<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();

// Verificar sesión de cotización
if (!isset($_SESSION['cotizacion_id'])) {
    header("Location: crear_cotizacion.php?error=no_session");
    exit();
}

// Validar ID del item
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: crear_cotizacion.php?error=invalid_id");
    exit();
}

$item_id = intval($_GET['id']);
$cotizacion_id = intval($_SESSION['cotizacion_id']);

// Obtener información del item usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT foto FROM cotizacion_items WHERE id = ? AND cotizacion_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $item_id, $cotizacion_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$fila = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if ($fila) {
    // Eliminar archivo de foto si existe
    if (!empty($fila['foto'])) {
        $ruta_foto = '../uploads/' . $fila['foto'];
        if (file_exists($ruta_foto)) {
            unlink($ruta_foto);
        }
    }

    // Eliminar registro usando prepared statement
    $stmt = mysqli_prepare($conexion, "DELETE FROM cotizacion_items WHERE id = ? AND cotizacion_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $item_id, $cotizacion_id);
    
    if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: crear_cotizacion.php?deleted=1");
    } else {
        mysqli_stmt_close($stmt);
        header("Location: crear_cotizacion.php?error=delete_failed");
    }
} else {
    header("Location: crear_cotizacion.php?error=not_found");
}
exit();
?>