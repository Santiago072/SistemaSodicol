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

// Obtener datos de la tarea usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT * FROM tareas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_tarea);
mysqli_stmt_execute($stmt);
$res_tarea = mysqli_stmt_get_result($stmt);
$tarea = mysqli_fetch_assoc($res_tarea);
mysqli_stmt_close($stmt);

if (!$tarea) {
    header("Location: tareas_usuarios.php?error=not_found");
    exit();
}

// Obtener lista de usuarios activos
$sql_usuarios = "SELECT id, nombre FROM usuarios WHERE estado = 'activo' ORDER BY nombre ASC";
$res_usuarios = mysqli_query($conexion, $sql_usuarios);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
        $mensaje_error = "Token de seguridad inválido";
    } else {
        $usuario_id = intval($_POST['usuario_id']);
        $descripcion_tarea = sanitizar_entrada($_POST['descripcion_tarea']);
        $estado = sanitizar_entrada($_POST['estado']);

        // Validaciones
        if (!in_array($estado, ['pendiente', 'completo'])) {
            $mensaje_error = "Estado no válido";
        } else {
            // Actualizar tarea usando prepared statement
            $stmt = mysqli_prepare($conexion, "UPDATE tareas SET usuario_id=?, descripcion_tarea=?, estado=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "issi", $usuario_id, $descripcion_tarea, $estado, $id_tarea);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: tareas_usuarios.php?updated=1");
                exit();
            } else {
                $mensaje_error = "Error al actualizar la tarea";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$csrf_token = generar_token_csrf();
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
        
        <?php if (isset($mensaje_error)) { ?>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
        <?php } ?>
        
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo intval($tarea['id']); ?>">
                
                <div class="grupo-campo">
                    <label for="usuario_id">Usuario Asignado</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">Seleccione un Usuario</option>
                        <?php while($user_item = mysqli_fetch_array($res_usuarios)): ?>
                        <option value="<?php echo intval($user_item['id']); ?>" <?php if($user_item['id']==$tarea['usuario_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($user_item['nombre']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="grupo-campo">
                    <label for="descripcion_tarea">Descripción</label>
                    <textarea id="descripcion_tarea" name="descripcion_tarea" required maxlength="500"><?php echo htmlspecialchars($tarea['descripcion_tarea']); ?></textarea>
                </div>
                
                <div class="grupo-campo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione un Estado</option>
                        <option value="pendiente" <?php if($tarea['estado']=='pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="completo" <?php if($tarea['estado']=='completo') echo 'selected'; ?>>Completo</option>
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