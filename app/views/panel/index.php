<?php
/**
 * Vista: Dashboard / Panel de control
 * Variables: $usuario, $total_administradores, $total_usuarios_rol,
 *            $total_cotizaciones, $tareas_pendientes
 */
$pageTitle   = 'Panel de Control - Sodicol';
$basePath    = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(dirname(dirname(__DIR__))) . '/includes/menu.php';
?>

<div class="contenido-principal">
    <?php
    $pageHeading = '';
    include dirname(__DIR__) . '/layout/topbar.php';
    ?>

    <div class="cabecera-bienvenida panel-bienvenida" style="margin-bottom: 24px;">
        <h3 class="bienvenida-titulo">
            ¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!
        </h3>
        <?php if ($usuario['rol'] === 'admin'): ?>
        <span class="rol-badge rol-admin"><i class="bi bi-shield-check"></i> Administrador</span>
        <?php else: ?>
        <span class="rol-badge rol-usuario"><i class="bi bi-person"></i> Usuario</span>
        <?php endif; ?>
    </div>

    <div class="panel-dos-columnas">

        <!-- Tarjetas de resumen -->
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

        <!-- Tareas pendientes -->
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
                            <a href="/PROYECTO_SODICOL/panel.php?completar_id=<?= intval($tarea['id']) ?>"
                               class="boton-primario">
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

    </div><!-- /panel-dos-columnas -->
</div><!-- /contenido-principal -->

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
