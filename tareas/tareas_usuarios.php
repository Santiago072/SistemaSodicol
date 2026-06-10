<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/TareaController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new TareaController($conexion);
$data       = $controller->gestion();
extract($data);

$urlBase = 'tareas_usuarios.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Tareas de Usuarios</title>
</head>
<body>
<canvas id="particle-canvas"></canvas>
<div class="noise-overlay"></div>
<?php include '../includes/menu.php'; ?>

<div class="contenido-principal">
    <div class="cabecera-superior">
        <button class="boton-menu-ocultar" id="btnMenu"><i class="fas fa-bars"></i> Ocultar Menú</button>
        <button class="btn-modo" id="btnModo" title="Cambiar tema">
            <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
            <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
        </button>
    </div>
    <div class="encabezado-pagina"><h1>Gestión y Creación de Tareas de Usuarios</h1></div>
    <br>

    <div class="columna-derecha">
        <div class="contenedor-usuario">
            <div class="usuario-principal">
                <div class="usuario-principal-nombre">
                    <h3><i class="bi bi-plus-circle"></i> Nueva Tarea</h3>
                    <p class="login-sub">Asigna una instrucción de trabajo a un empleado.</p>
                </div>

                <?php if ($mensajeExito): ?>
                <div style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#86efac;padding:12px 16px;border-radius:12px;font-size:13px;">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
                </div>
                <?php endif; ?>
                <?php if ($mensajeError): ?>
                <div class="error-box"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
                <?php endif; ?>

                <form method="POST" class="formulario">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <div class="grupo-campo">
                        <label><i class="bi bi-card-text"></i> Descripción de la Tarea</label>
                        <textarea name="descripcion_tarea" rows="4" required maxlength="500" placeholder="Describe la instrucción de trabajo..."></textarea>
                    </div>
                    <div class="grupo-campo">
                        <label><i class="bi bi-person"></i> Asignar a Usuario</label>
                        <select name="usuario" required>
                            <option value="">Seleccione un Usuario</option>
                            <?php foreach ($usuarios as $u): ?>
                            <option value="<?= intval($u['id']) ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
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
                        <button type="submit" class="boton-primario" style="flex:1;">
                            <i class="bi bi-plus-lg"></i> Crear Tarea
                        </button>
                        <button type="reset" class="boton-secundario"><i class="bi bi-x-lg"></i> Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="tabla-contenedor" style="margin-top:30px;">
        <table class="tabla-datos">
            <thead>
                <tr><th>Usuario</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($tareas)): ?>
                    <?php foreach ($tareas as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion_tarea']) ?></td>
                        <td>
                            <?php if ($row['estado'] === 'completo'): ?>
                            <span class="rol-badge rol-admin"><i class="bi bi-check2-all"></i> Completo</span>
                            <?php else: ?>
                            <span class="rol-badge rol-usuario"><i class="bi bi-clock-history"></i> Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td class="acciones-tabla">
                            <a href="editar_tarea.php?id=<?= intval($row['id']) ?>" class="boton-editar"><i class="bi bi-pencil-square"></i></a>
                            <a href="eliminar_tarea.php?id=<?= intval($row['id']) ?>" class="boton-eliminar"
                               onclick="return confirm('¿Está seguro de eliminar esta tarea?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--gold-light);">
                    <i class="bi bi-info-circle"></i> No hay tareas registradas.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include '../app/views/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> tareas)</p>
    <?php endif; ?>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
