<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_GET['id'];

/* Eliminar Usuario */
$sql = "DELETE FROM usuarios WHERE id = $id_usuario";
mysqli_query($conexion, $sql);

header("Location: lista_usuarios.php");
exit();
?>