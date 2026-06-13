<?php
/**
 * Vista: Dashboard / Panel de control
 * Variables: $usuario, $total_administradores, $total_usuarios_rol,
 *            $total_cotizaciones, $tareas_pendientes
 */
$pageTitle   = 'Panel de Control - Sodicol';
$basePath    = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php
    $pageHeading = '';
    $esDashboard = true;
    include dirname(__DIR__) . '/layout/topbar.php';
    ?>

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
                            <button type="button" class="boton-primario btn-completar-tarea" data-id="<?= intval($tarea['id']) ?>">
                                <i class="bi bi-check2-all"></i> Completo
                            </button>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const botonesCompletar = document.querySelectorAll('.btn-completar-tarea');
    
    botonesCompletar.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const tareaDiv = this.closest('.svc-tarea');
            
            // Estado visual de carga
            const txtOriginal = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i>...';
            this.disabled = true;

            fetch(`/PROYECTO_SODICOL/?module=panel&action=ajax_completar_tarea&id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    // Animación de salida
                    tareaDiv.style.transition = 'all 0.4s ease';
                    tareaDiv.style.opacity = '0';
                    tareaDiv.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        tareaDiv.remove();
                        // Comprobar si quedan tareas
                        const grid = document.querySelector('.servicios-grid');
                        if(grid && grid.children.length === 0) {
                            grid.innerHTML = `
                                <div class="tareas-vacias">
                                    <i class="bi bi-info-circle"></i>
                                    <p>No tienes tareas pendientes actualmente.</p>
                                </div>
                            `;
                            grid.classList.remove('servicios-grid');
                        }
                    }, 400);
                } else {
                    alert(res.message || 'Error al completar la tarea');
                    this.innerHTML = txtOriginal;
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error de conexión');
                this.innerHTML = txtOriginal;
                this.disabled = false;
            });
        });
    });
});
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
