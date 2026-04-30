<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id_tarea = $_GET['id'];

/* Eliminar Usuario */
$sql = "DELETE FROM tareas WHERE id = $id_tarea";
mysqli_query($conexion, $sql);

header("Location: tareas_usuarios.php");
exit();
?>  