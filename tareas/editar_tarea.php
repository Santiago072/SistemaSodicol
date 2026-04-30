<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id_tarea = mysqli_real_escape_string($conexion, $_GET['id']);

// 1. Obtener los datos de la tarea actual
$sql_tarea = "SELECT * FROM tareas WHERE id = $id_tarea";
$res_tarea = mysqli_query($conexion, $sql_tarea);
$tarea = mysqli_fetch_assoc($res_tarea);

// 2. Obtener la lista de usuarios para el SELECT (ESTO FALTABA)
$sql_usuarios = "SELECT id, nombre FROM usuarios WHERE estado = 'activo'";
$res_usuarios = mysqli_query($conexion, $sql_usuarios);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $descripcion_tarea = mysqli_real_escape_string($conexion, $_POST['descripcion_tarea']);
    $estado = $_POST['estado'];

    $sql_update = "UPDATE tareas SET 
            usuario_id='$usuario_id',
            descripcion_tarea='$descripcion_tarea',
            estado='$estado'
            WHERE id='$id_tarea'";

    if (mysqli_query($conexion, $sql_update)) {
        header("Location: tareas_usuarios.php?mensaje=actualizado");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="../css/estilos.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <h1>Editar Tarea</h1>
        </div>
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">
                <div class="grupo-campo">
                    <label for="usuario_id">Usuario Asignado</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">Seleccione un Usuario</option>
                        <?php while($user_item = mysqli_fetch_array($res_usuarios)): ?>
                        <option value="<?php echo $user_item['id']; ?>" <?php if($user_item['id']==$tarea['usuario_id'])
                            echo 'selected' ; ?>>
                            <?php echo $user_item['nombre']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="grupo-campo">
                    <label for="descripcion_tarea">Descripción</label>
                    <textarea id="descripcion_tarea" name="descripcion_tarea"
                        required><?php echo $tarea['descripcion_tarea']; ?></textarea>
                </div>
                <div class="grupo-campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione un Estado</option>
                        <option value="pendiente" <?php if($tarea['estado']=='pendiente' ) echo 'selected' ; ?>
                            >Pendiente</option>
                        <option value="completo" <?php if($tarea['estado']=='completo' ) echo 'selected' ; ?>>Completo
                        </option>
                    </select>
                </div>
                <div class="grupo-campo">
                    <button type="submit" class="boton-primario">Actualizar Tarea</button>
                    <a class="boton-limpiar" href="tareas_usuarios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>