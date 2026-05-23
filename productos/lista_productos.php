<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();
$mensaje_exito = "";
$mensaje_error = "";

// Mensajes de feedback
if (isset($_GET['success'])) $mensaje_exito = "Producto creado exitosamente";
if (isset($_GET['updated'])) $mensaje_exito = "Producto actualizado exitosamente";
if (isset($_GET['deleted'])) $mensaje_exito = "Producto eliminado exitosamente";
if (isset($_GET['error'])) $mensaje_error = "Error al procesar la solicitud";

$busqueda = "";
if(isset($_GET['busqueda']) && $_GET['busqueda'] != "") {
    $busqueda = sanitizar_entrada($_GET['busqueda']);
    $busqueda_param = "%$busqueda%";
    $stmt = mysqli_prepare($conexion, "SELECT * FROM productos WHERE titulo LIKE ? ORDER BY titulo ASC");
    mysqli_stmt_bind_param($stmt, "s", $busqueda_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql_productos = "SELECT * FROM productos ORDER BY titulo ASC";
    $result = mysqli_query($conexion, $sql_productos);
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
            <form action="lista_productos.php" method="GET" class="formulario-busqueda">
                <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda) ?>" placeholder="Buscar producto...">
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
                            <?php echo htmlspecialchars($producto['titulo']); ?>
                        </td>
                        <td>
                            <?php echo number_format($producto['precio'], 0, '', '.'); ?>
                        </td>
                        <td>
                            <?php echo intval($producto['cantidad']); ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_producto.php?id=<?php echo intval($producto['id']); ?>" class="boton-editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="eliminar_producto.php?id=<?php echo intval($producto['id']); ?>" class="boton-eliminar"
                                onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                <i class="fas fa-trash"></i>
                            </a>
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