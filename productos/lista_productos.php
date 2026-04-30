<?php
session_start();
include '../config/conexion.php';
$conexion = conexion();

// Validaciones de seguridad
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT * FROM productos ORDER BY id ASC";
$result = mysqli_query($conexion, $sql);

$busqueda = "";
if(isset($_GET['busqueda']) && $_GET['busqueda'] != "") {
    $busqueda = $_GET['busqueda'];
    $sql_productos = "SELECT * FROM productos WHERE titulo LIKE '%$busqueda%' ORDER BY titulo ASC";
} else {
    $sql_productos = "SELECT * FROM productos ORDER BY titulo ASC";
}

$result = mysqli_query($conexion, $sql_productos);
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
    <title>Lista de Productos</title>
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
            <h1>Lista de Productos</h1>
        </div>
        <div class="barra-busqueda">
            <form action="lista_productos.php" method="GET" class="formulario-busqueda">
                <input type="text" name="busqueda" value="<?php echo $busqueda ?>" placeholder="Buscar producto...">
                <button type="submit" class="boton-primario">Buscar</button>
                <?php if($busqueda != ''): ?>
                <a href="lista_productos.php" class="boton-limpiar">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="tabla-contenedor">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Nombre del Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($producto = mysqli_fetch_array($result)): ?>
                    <tr>
                        <td>
                            <?php echo $producto['titulo']; ?>
                        </td>
                        <td>
                            <?php echo number_format($producto['precio'], 0, '', '.'); ?>
                        </td>
                        <td>
                            <?php echo $producto['cantidad']; ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="boton-editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="boton-eliminar"
                                onclick="return confirm('¿Eliminar este ítem?')">
                                <i class="fas fa-trash"></i>
                            </a>
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