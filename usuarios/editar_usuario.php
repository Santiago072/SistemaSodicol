<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id = $id_usuario";
$result = mysqli_query($conexion, $sql);
$usuario = mysqli_fetch_assoc($result);

/* if (!$usuario) {
    header("Location: crear_usuario.php");
    exit();
} */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];

    $sql = "UPDATE usuarios SET 
            documento='$documento',
            nombre='$nombre',
            correo='$correo',
            telefono='$telefono',
            rol='$rol',
            estado='$estado'
            WHERE id='$id_usuario'";

    if (mysqli_query($conexion, $sql)) {
        header("Location: lista_usuarios.php");
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
    <title>Editar Usuario</title>
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
            <h1>Editar Usuario</h1>
        </div>
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                <div class="grupo-campo">
                    <label for="documento">Documento *</label>
                    <input type="text" id="documento" name="documento" value="<?php echo $usuario['documento']; ?>"
                        required>
                </div>

                <div class="grupo-campo">
                    <label for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                </div>

                <div class="grupo-campo">
                    <label for="correo">Correo Electrónico *</label>
                    <input type="email" id="correo" name="correo" value="<?php echo $usuario['correo']; ?>" required>
                </div>

                <div class="grupo-campo">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $usuario['telefono']; ?>"
                        required>
                </div>

                <div class="grupo-campo">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" required>
                        <option value="">Seleccione un Rol</option>
                        <option value="admin" <?php if($usuario['rol']=='admin' ) echo 'selected' ; ?>>Administrador
                        </option>
                        <option value="usuario" <?php if($usuario['rol']=='usuario' ) echo 'selected' ; ?>>Usuario
                        </option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="estado">Estado *</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione un Estado</option>
                        <option value="activo" <?php if($usuario['estado']=='activo' ) echo 'selected' ; ?>>Activo
                        </option>
                        <option value="inactivo" <?php if($usuario['estado']=='inactivo' ) echo 'selected' ; ?>>Inactivo
                        </option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <button type="submit" class="boton-primario">Actualizar Usuario</button>
                    <a class="boton-limpiar" href="lista_usuarios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>