<?php 
session_start();
include '../config/conexion.php';
$conexion = conexion();

if(!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header('Location: ../panel.php');
    exit();
}

$sql = "SELECT * FROM usuarios WHERE estado = 'activo'";
$query = mysqli_query($conexion, $sql);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_POST['usuario']; // Este es el ID que viene del select
    $descripcion_tarea = $_POST['descripcion_tarea'];
    $estado = $_POST['estado'];

    // Usamos el nombre correcto de la columna: usuario_id
    $sql = "INSERT INTO tareas (usuario_id, descripcion_tarea, estado) VALUES ('$usuario_id', '$descripcion_tarea', '$estado')";
    
    if(mysqli_query($conexion, $sql)) {
        header("Location: tareas_usuarios.php?mensaje=guardado");
        exit();
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Tareas de Usuarios</title>
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
            <h1>Gestión y Creación de Tareas de Usuarios</h1>
        </div>
        <br>
        <div class="columna-derecha">
            <div class="contenedor-usuario">
                <div class="usuario-principal">
                    <div class="usuario-principal-nombre">
                        <h3><i class="bi bi-plus-circle"></i> Nueva Tarea</h3>
                        <p class="login-sub">Asigna una instrucción de trabajo a un empleado.</p>
                    </div>

                    <?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'guardado'): ?>
                    <div style="background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); 
                                color: #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px;">
                        <i class="bi bi-check-circle"></i> Tarea creada correctamente.
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="formulario">
                        <div class="grupo-campo">
                            <label><i class="bi bi-card-text"></i> Descripción de la Tarea</label>
                            <textarea name="descripcion_tarea" rows="4" required 
                                      placeholder="Describe la instrucción de trabajo..."></textarea>
                        </div>
                        <div class="grupo-campo">
                            <label><i class="bi bi-person"></i> Asignar a Usuario</label>
                            <select name="usuario" required>
                                <option value="">Seleccione un Usuario</option>
                                <?php while($usuario = mysqli_fetch_array($query)): ?>
                                <option value="<?php echo $usuario['id']; ?>">
                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="grupo-campo">
                            <label><i class="bi bi-flag"></i> Estado Inicial</label>
                            <select name="estado" required>
                                <option value="">Seleccione un Estado</option>
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="completo">Completo</option>
                            </select>
                        </div>
                        <div class="grupo-campo">
                            <button type="submit" class="boton-primario" style="flex: 1;">
                                <i class="bi bi-plus-lg"></i> Crear Tarea
                            </button>
                            <button type="reset" class="boton-secundario">
                                <i class="bi bi-x-lg"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tabla-contenedor" style="margin-top: 30px;">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
            // 1. Consultamos solo las tareas pendientes (ya que es gestión de pendientes)
            $sql_listado = "SELECT t.*, u.nombre FROM tareas t 
                            JOIN usuarios u ON t.usuario_id = u.id 
                            ORDER BY t.id DESC";
            $res_todas = mysqli_query($conexion, $sql_listado);

            if(mysqli_num_rows($res_todas) > 0):
                while($row = mysqli_fetch_assoc($res_todas)): 
            ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['descripcion_tarea']); ?>
                        </td>
                        <td>
                            <?php if($row['estado'] == 'completo'): ?>
                            <span class="rol-badge rol-admin">
                                <i class="bi bi-check2-all"></i> Completo
                            </span>
                            <?php else: ?>
                            <span class="rol-badge rol-usuario">
                                <i class="bi bi-clock-history"></i> Pendiente
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_tarea.php?id=<?php echo $row['id']; ?>" class="boton-editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="eliminar_tarea.php?id=<?php echo $row['id']; ?>" class="boton-eliminar"
                                onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                endwhile; 
            else: 
            ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 30px; color: var(--gold-light);">
                            <i class="bi bi-info-circle"></i> No hay tareas pendientes registradas.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>