<?php
require_once 'config/conexion.php';
require_once 'config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario logueado usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$result_usuario = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result_usuario);
mysqli_stmt_close($stmt);

// Conteo de administradores
$sql_administradores = "SELECT COUNT(*) AS total_administradores FROM usuarios WHERE rol = 'admin'";
$result_administradores = mysqli_query($conexion, $sql_administradores);
$sql_total_administradores = mysqli_fetch_assoc($result_administradores)['total_administradores'];

// Conteo de usuarios
$sql_usuarios = "SELECT COUNT(*) AS total_usuarios FROM usuarios WHERE rol = 'usuario'";
$result_usuarios = mysqli_query($conexion, $sql_usuarios);
$sql_total_usuarios = mysqli_fetch_assoc($result_usuarios)['total_usuarios'];

// Conteo de cotizaciones del usuario logueado usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT COUNT(*) AS total_cotizaciones 
                     FROM cotizaciones 
                     WHERE numero_cotizacion IS NOT NULL 
                     AND numero_cotizacion != '' 
                     AND nombre_cliente IS NOT NULL 
                     AND nombre_cliente != ''
                     AND usuario_nombre = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario_nombre);
mysqli_stmt_execute($stmt);
$result_cotizaciones = mysqli_stmt_get_result($stmt);
$sql_total_cotizaciones = mysqli_fetch_assoc($result_cotizaciones)['total_cotizaciones'];
mysqli_stmt_close($stmt);

$base_path = '/PROYECTO_SODICOL/';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Anti-flash: aplica fondo crema ANTES de que cargue el CSS si el tema guardado es día -->
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Panel de Control - Sodicol</title>
</head>

<body>
    <canvas id="particle-canvas"></canvas>
    <div class="noise-overlay"></div>

    <?php include 'includes/menu.php'; ?>

    <div class="contenido-principal">
        <div class="cabecera-superior">

            <button class="boton-menu-ocultar" id="btnMenu">
                <i class="fas fa-bars"></i> Ocultar Menú
            </button>

            <div class="cabecera-bienvenida">
                <?php if($usuario['rol'] == 'admin'): ?>
                <h3>¡Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h3>
                <span class="rol-badge rol-admin">
                    <i class="bi bi-shield-check"></i> Administrador
                </span>
                <?php else: ?>
                <h3>¡Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h3>
                <span class="rol-badge rol-usuario">
                    <i class="bi bi-person"></i> Usuario
                </span>
                <?php endif; ?>
            </div>

            <!-- Botón modo día/noche -->
            <button class="btn-modo" id="btnModo" title="Cambiar tema">
                <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
                <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
                <span class="modo-label"></span>
            </button>
        </div>

        <div class="panel-dos-columnas">

            <!-- COLUMNA IZQUIERDA -->
            <div class="columna-izquierda">
                <div class="contenedor-tarjetas">
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?php echo $sql_total_administradores; ?>">0</div>
                        <div class="titulo-tarjeta">Administradores</div>
                        <div class="icono-tarjeta administradores">
                            <i class="bi bi-person-gear"></i>
                        </div>
                    </div>
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?php echo $sql_total_usuarios; ?>">0</div>
                        <div class="titulo-tarjeta">Usuarios</div>
                        <div class="icono-tarjeta usuarios">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?php echo $sql_total_cotizaciones; ?>">0</div>
                        <div class="titulo-tarjeta">Mis Cotizaciones</div>
                        <div class="icono-tarjeta cotizaciones">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA -->
            <div class="columna-derecha">
                <div class="contenedor-usuario">
                    <div class="usuario-principal">
                        <div class="usuario-principal-nombre">
                            <h3><i class="fa fa-tasks"></i> Mis Tareas Pendientes</h3>
                            <p class="login-sub">Gestión de cotizaciones asignadas por la administración.</p>
                        </div>

                        <?php
                        // Completar tarea con validación
                        if(isset($_GET['completar_id'])) {
                            if (validar_numero($_GET['completar_id'])) {
                                $id_tarea = intval($_GET['completar_id']);
                                $stmt = mysqli_prepare($conexion, "UPDATE tareas SET estado = 'completo' WHERE id = ? AND usuario_id = ?");
                                mysqli_stmt_bind_param($stmt, "ii", $id_tarea, $usuario_id);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);
                                echo "<script>window.location='panel.php';</script>";
                            }
                        }

                        // Obtener tareas pendientes usando prepared statement
                        $stmt = mysqli_prepare($conexion, "SELECT * FROM tareas WHERE usuario_id = ? AND estado = 'pendiente'");
                        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
                        mysqli_stmt_execute($stmt);
                        $resultado_tareas = mysqli_stmt_get_result($stmt);

                        if(mysqli_num_rows($resultado_tareas) > 0): ?>
                            <div class="servicios-grid">
                                <?php while($tarea = mysqli_fetch_assoc($resultado_tareas)): ?>
                                <div class="svc-tarea">
                                    <div class="svc-tarea-contenido">
                                        <div class="svc-icon"><i class="bi bi-clock-history"></i></div>
                                        <div class="svc-text">
                                            <strong>Instrucción:</strong>
                                            <span><?php echo htmlspecialchars($tarea['descripcion_tarea']); ?></span>
                                        </div>
                                    </div>
                                    <a href="panel.php?completar_id=<?php echo $tarea['id']; ?>" class="boton-primario">
                                        <i class="bi bi-check2-all"></i> Completo
                                    </a>
                                </div>
                                <?php endwhile; 
                                mysqli_stmt_close($stmt); ?>
                            </div>
                        <?php else: ?>
                            <div class="tareas-vacias">
                                <i class="bi bi-info-circle"></i>
                                <p>No tienes tareas pendientes actualmente.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="includes/script.js"></script>
</body>

</html>