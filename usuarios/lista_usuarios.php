<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_admin();

$base_path = '/PROYECTO_SODICOL/';
$conexion = conexion();

$busqueda = "";
$mensaje_exito = "";
$mensaje_error = "";

// Mensajes de feedback
if (isset($_GET['success'])) $mensaje_exito = "Usuario creado exitosamente";
if (isset($_GET['updated'])) $mensaje_exito = "Usuario actualizado exitosamente";
if (isset($_GET['deleted'])) $mensaje_exito = "Usuario eliminado exitosamente";
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'last_admin': $mensaje_error = "No se puede eliminar el último administrador"; break;
        case 'self_delete': $mensaje_error = "No puede eliminarse a sí mismo"; break;
        case 'delete_failed': $mensaje_error = "Error al eliminar el usuario"; break;
        case 'invalid_id': $mensaje_error = "ID de usuario inválido"; break;
    }
}

// Búsqueda con prepared statement
if(isset($_GET['busqueda']) && $_GET['busqueda'] != "") {
    $busqueda = sanitizar_entrada($_GET['busqueda']);
    $busqueda_param = "%$busqueda%";
    $stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE nombre LIKE ? ORDER BY nombre");
    mysqli_stmt_bind_param($stmt, "s", $busqueda_param);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT * FROM usuarios ORDER BY nombre";
    $query = mysqli_query($conexion, $sql);
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
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Lista Usuarios</title>
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
            <h1>Gestión Usuarios</h1>
        </div>
        
        <?php if ($mensaje_exito != '') { ?>
        <div class="success-box" style="background: #d4edda; color: #155724; padding: 15px; margin: 15px 0; border-radius: 8px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($mensaje_exito); ?></span>
        </div>
        <?php } ?>
        
        <?php if ($mensaje_error != '') { ?>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
        <?php } ?>
        
        <div class="barra-busqueda">
            <form action="lista_usuarios.php" method="GET" class="formulario-busqueda">
                <input type="text" name="busqueda" value="<?php echo $busqueda; ?>" placeholder="Buscar usuario...">
                <button type="submit" class="boton-primario">Buscar</button>
                <?php if($busqueda != ''): ?>
                <a href="lista_usuarios.php" class="boton-limpiar">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($usuario = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($usuario['nombre']) ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($usuario['rol']) ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($usuario['estado']) ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_usuario.php?id=<?php echo intval($usuario['id']) ?>" class="boton-editar"> <i
                                    class="fas fa-edit"></i></a>
                            <a href="eliminar_usuario.php?id=<?php echo intval($usuario['id']) ?>" class="boton-eliminar" 
                               onclick="return confirm('¿Está seguro de eliminar este usuario?');"> <i
                                    class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; 
                    if (isset($stmt)) mysqli_stmt_close($stmt); ?>
                </tbody>
            </table>

        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>