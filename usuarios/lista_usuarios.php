<?php
session_start();
// Verificar si el usuario tiene rol de administrador
if(!isset($_SESSION['usuario_nombre']) || $_SESSION['rol'] != 'admin') {
    header('Location: ../panel.php');
    exit();
}
$base_path = '/PROYECTO_SODICOL/';

include '../config/conexion.php';
$conexion = conexion();

$busqueda = "";
$condicion = "";
if(isset($_GET['busqueda']) && $_GET['busqueda'] != "") {
    $busqueda = $_GET['busqueda'];
    $condicion .= "WHERE (u.nombre LIKE '%$busqueda%')";
}

$sql = "SELECT * FROM usuarios u $condicion ORDER BY nombre";
$query = mysqli_query($conexion, $sql);

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
                            <?php echo $usuario['nombre'] ?>
                        </td>
                        <td>
                            <?php echo $usuario['rol'] ?>
                        </td>
                        <td>
                            <?php echo $usuario['estado'] ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_usuario.php?id=<?php echo $usuario['id'] ?>" class="boton-editar"> <i
                                    class="fas fa-edit"></i></a>
                            <a href="eliminar_usuario.php?id=<?php echo $usuario['id'] ?>" class="boton-eliminar"> <i
                                    class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>