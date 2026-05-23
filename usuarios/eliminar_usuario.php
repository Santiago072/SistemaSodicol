<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_admin();

$conexion = conexion();

// Validar ID de usuario
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: lista_usuarios.php?error=invalid_id");
    exit();
}

$id_usuario = intval($_GET['id']);

// Verificar que no sea el último administrador
$stmt = mysqli_prepare($conexion, "SELECT rol FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($usuario && $usuario['rol'] === 'admin') {
    // Contar administradores
    $sql_count = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'";
    $result_count = mysqli_query($conexion, $sql_count);
    $count = mysqli_fetch_assoc($result_count)['total'];
    
    if ($count <= 1) {
        header("Location: lista_usuarios.php?error=last_admin");
        exit();
    }
}

// Verificar que no se elimine a sí mismo
if ($id_usuario == $_SESSION['usuario_id']) {
    header("Location: lista_usuarios.php?error=self_delete");
    exit();
}

// Eliminar usuario usando prepared statement
$stmt = mysqli_prepare($conexion, "DELETE FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: lista_usuarios.php?deleted=1");
} else {
    mysqli_stmt_close($stmt);
    header("Location: lista_usuarios.php?error=delete_failed");
}
exit();
?>