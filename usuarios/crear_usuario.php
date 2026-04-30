<?php
session_start();
if(!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/conexion.php';
$conexion = conexion();
$base_path = '/PROYECTO_SODICOL/';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    if ($documento != "" && $nombre != "" && $correo != "" && $telefono != "" && $rol != "") {

        // VALIDAR SI YA EXISTE
        $sql_check = "SELECT id FROM usuarios WHERE documento = '$documento' LIMIT 1";
        $resultado = mysqli_query($conexion, $sql_check);

        if (mysqli_num_rows($resultado) > 0) {
            $mensaje_error = "El documento ya está registrado";
        } else {
            $sql_insert = "INSERT INTO usuarios (documento, nombre, correo, telefono, rol) 
                           VALUES ('$documento', '$nombre', '$correo', '$telefono', '$rol')";
            mysqli_query($conexion, $sql_insert);
            header("Location: lista_usuarios.php");
            exit();
        }
    } else {
        $mensaje_error = "Todos los campos son obligatorios";
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
    <title>Nuevo Usuario</title>
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
            <h1>Crear Nuevo Usuario</h1>
        </div>
        <?php if ($mensaje_error != '') { ?>
        <br>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo $mensaje_error; ?></span>
        </div>
        <br>
        <?php } ?>
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <div class="grupo-campo">
                    <label for="documento">Documento *</label>
                    <input type="text" id="documento" name="documento" required>
                </div>

                <div class="grupo-campo">
                    <label for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="grupo-campo">
                    <label for="correo">Correo Electrónico *</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="grupo-campo">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>

                <div class="grupo-campo">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" required>
                        <option value="">Seleccione un Rol</option>
                        <option value="admin">Administrador</option>
                        <option value="usuario">Usuario</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <button type="submit" class="boton-primario">Crear Usuario</button>
                </div>
            </form>

        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>