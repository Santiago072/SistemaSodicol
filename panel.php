<?php
require_once 'config/conexion.php';
require_once 'config/seguridad.php';
require_once 'app/models/TareaModel.php';
require_once 'app/models/UsuarioModel.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion       = conexion();
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_id     = (int)$_SESSION['usuario_id'];

$tareaModel   = new TareaModel($conexion);
$usuarioModel = new UsuarioModel($conexion);

// Completar tarea (delegado al model, valida que pertenezca al usuario)
if (isset($_GET['completar_id']) && validar_numero($_GET['completar_id'])) {
    $tareaModel->completar(intval($_GET['completar_id']), $usuario_id);
    header('Location: panel.php');
    exit();
}

// Datos del usuario logueado
$usuario = $usuarioModel->buscarPorId($usuario_id);

// Contadores para tarjetas
$total_administradores = $usuarioModel->contarAdmins();
$total_usuarios_rol    = $usuarioModel->contar('') - $total_administradores;

// Cotizaciones del usuario actual
$stmt_cot = mysqli_prepare($conexion,
    "SELECT COUNT(*) AS total FROM cotizaciones
     WHERE numero_cotizacion IS NOT NULL AND numero_cotizacion != ''
     AND nombre_cliente IS NOT NULL AND nombre_cliente != ''
     AND usuario_nombre = ?");
mysqli_stmt_bind_param($stmt_cot, 's', $usuario_nombre);
mysqli_stmt_execute($stmt_cot);
$total_cotizaciones = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cot))['total'];
mysqli_stmt_close($stmt_cot);

// Tareas pendientes del usuario
$tareas_pendientes = $tareaModel->listarPendientesDeUsuario($usuario_id);

$base_path = '/PROYECTO_SODICOL/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
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
                <h3>¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</h3>
                <?php if ($usuario['rol'] === 'admin'): ?>
                <span class="rol-badge rol-admin"><i class="bi bi-shield-check"></i> Administrador</span>
                <?php else: ?>
                <span class="rol-badge rol-usuario"><i class="bi bi-person"></i> Usuario</span>
                <?php endif; ?>
            </div>

            <button class="btn-modo" id="btnModo" title="Cambiar tema">
                <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
                <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
                <span class="modo-label"></span>
            </button>
        </div>

        <div class="panel-dos-columnas">

            <!-- TARJETAS RESUMEN -->
            <div class="columna-izquierda">
                <div class="contenedor-tarjetas">
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?= $total_administradores ?>">0</div>
                        <div class="titulo-tarjeta">Administradores</div>
                        <div class="icono-tarjeta administradores"><i class="bi bi-person-gear"></i></div>
                    </div>
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?= $total_usuarios_rol ?>">0</div>
                        <div class="titulo-tarjeta">Usuarios</div>
                        <div class="icono-tarjeta usuarios"><i class="bi bi-people"></i></div>
                    </div>
                    <div class="tarjeta-dashboard">
                        <div class="numero-principal" data-target="<?= $total_cotizaciones ?>">0</div>
                        <div class="titulo-tarjeta">Mis Cotizaciones</div>
                        <div class="icono-tarjeta cotizaciones"><i class="bi bi-currency-dollar"></i></div>
                    </div>
                </div>
            </div>

            <!-- TAREAS PENDIENTES -->
            <div class="columna-derecha">
                <div class="contenedor-usuario">
                    <div class="usuario-principal">
                        <div class="usuario-principal-nombre">
                            <h3><i class="bi bi-list-task"></i> Mis Tareas Pendientes</h3>
                            <p class="login-sub">Instrucciones de trabajo asignadas por la administración.</p>
                        </div>

                        <?php if (!empty($tareas_pendientes)): ?>
                        <div class="servicios-grid">
                            <?php foreach ($tareas_pendientes as $tarea): ?>
                            <div class="svc-tarea">
                                <div class="svc-tarea-contenido">
                                    <div class="svc-icon"><i class="bi bi-clock-history"></i></div>
                                    <div class="svc-text">
                                        <strong>Instrucción:</strong>
                                        <span><?= htmlspecialchars($tarea['descripcion_tarea']) ?></span>
                                    </div>
                                </div>
                                <a href="panel.php?completar_id=<?= intval($tarea['id']) ?>" class="boton-primario">
                                    <i class="bi bi-check2-all"></i> Completo
                                </a>
                            </div>
                            <?php endforeach; ?>
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
