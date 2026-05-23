<?php
/**
 * Script de migración para agregar campo password y migrar contraseñas
 * EJECUTAR UNA SOLA VEZ después de actualizar la base de datos
 */

require_once 'config/conexion.php';

$conexion = conexion();

echo "<h2>Migración de Sistema de Contraseñas</h2>";

// Paso 1: Verificar si la columna password ya existe
$check_column = "SHOW COLUMNS FROM usuarios LIKE 'password'";
$result = mysqli_query($conexion, $check_column);

if (mysqli_num_rows($result) == 0) {
    echo "<p>Agregando columna 'password' a la tabla usuarios...</p>";
    
    // Agregar columna password
    $sql_add_column = "ALTER TABLE usuarios ADD COLUMN password VARCHAR(255) NULL AFTER correo";
    if (mysqli_query($conexion, $sql_add_column)) {
        echo "<p style='color: green;'>✓ Columna 'password' agregada exitosamente</p>";
    } else {
        echo "<p style='color: red;'>✗ Error al agregar columna: " . mysqli_error($conexion) . "</p>";
        exit();
    }
} else {
    echo "<p style='color: blue;'>ℹ La columna 'password' ya existe</p>";
}

// Paso 2: Migrar contraseñas existentes (documento -> password hasheado)
echo "<p>Migrando contraseñas existentes...</p>";

$sql_usuarios = "SELECT id, documento FROM usuarios WHERE password IS NULL OR password = ''";
$result_usuarios = mysqli_query($conexion, $sql_usuarios);

$contador = 0;
while ($usuario = mysqli_fetch_assoc($result_usuarios)) {
    $id = $usuario['id'];
    $documento = $usuario['documento'];
    
    // Hashear el documento como contraseña temporal
    $password_hash = password_hash($documento, PASSWORD_DEFAULT);
    
    // Actualizar usando prepared statement
    $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $password_hash, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $contador++;
        echo "<p style='color: green;'>✓ Usuario ID $id migrado (contraseña temporal: su documento)</p>";
    } else {
        echo "<p style='color: red;'>✗ Error al migrar usuario ID $id</p>";
    }
    
    mysqli_stmt_close($stmt);
}

echo "<h3>Resumen de Migración</h3>";
echo "<p><strong>Total de usuarios migrados:</strong> $contador</p>";
echo "<p style='color: orange;'><strong>IMPORTANTE:</strong> Todos los usuarios ahora deben usar su número de documento como contraseña temporal.</p>";
echo "<p style='color: orange;'>Se recomienda que cambien su contraseña después del primer inicio de sesión.</p>";

echo "<hr>";
echo "<p><a href='index.php'>Ir al Login</a></p>";

mysqli_close($conexion);
?>
