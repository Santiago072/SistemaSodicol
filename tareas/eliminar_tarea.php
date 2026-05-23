<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_admin();

$conexion = conexion();

// Validar ID de tarea
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: tareas_usuarios.php?error=invalid_id");
    exit();
}

$id_tarea = intval($_GET['id']);

// Eliminar tarea usando prepared statement
$stmt = mysqli_prepare($conexion, "DELETE FROM tareas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_tarea);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: tareas_usuarios.php?deleted=1");
} else {
    mysqli_stmt_close($stmt);
    header("Location: tareas_usuarios.php?error=delete_failed");
}
exit();
?>  