<?php
/**
 * Vista: Gestión y creación de tareas
 * Variables: $usuarios, $tareas, $csrf_token, $paginaActual,
 *            $totalPaginas, $total, $mensajeExito, $mensajeError
 */
$pageTitle = 'Tareas de Usuarios';
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Gestión de Tareas';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Gestión y Creación de Tareas de Usuarios</h1></div>
    <br>

    <!-- Formulario de creación -->
    <div class="columna-derecha">
        <div class="contenedor-usuario">
            <div class="usuario-principal">
                <div class="usuario-principal-nombre">
                    <h3><i class="bi bi-plus-circle"></i> Nueva Tarea</h3>
                    <p class="login-sub">Asigna una instrucción de trabajo a un empleado.</p>
                </div>

                <?php if ($mensajeExito): ?>
                <div style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);
                            color:#86efac;padding:12px 16px;border-radius:12px;font-size:13px;">
                    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
                </div>
                <?php endif; ?>
                <?php if ($mensajeError): ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= $basePath ?>?module=tareas&action=gestion" class="formulario">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="grupo-campo">
                        <label><i class="bi bi-card-text"></i> Descripción de la Tarea</label>
                        <textarea name="descripcion_tarea" rows="4" required maxlength="500"
                                  placeholder="Describe la instrucción de trabajo..."></textarea>
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
                        <button type="reset" class="boton-secundario">
                            <i class="bi bi-x-lg"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de tareas -->
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
                            <a href="<?= $basePath ?>?module=tareas&action=editar&id=<?= intval($row['id']) ?>"
                               class="boton-editar"><i class="bi bi-pencil-square"></i></a>
                            <a href="<?= $basePath ?>?module=tareas&action=eliminar&id=<?= intval($row['id']) ?>"
                               class="boton-eliminar"
                               onclick="return confirm('¿Está seguro de eliminar esta tarea?')">
                                <i class="bi bi-trash"></i>
                            </a>
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

    <?php include dirname(__DIR__) . '/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> tareas)</p>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
