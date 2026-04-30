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

/* Eliminar producto */
$sql = "DELETE FROM productos WHERE id = '$id_producto'";

if (mysqli_query($conexion, $sql)) {
    header("Location: lista_productos.php");
    exit();
}
