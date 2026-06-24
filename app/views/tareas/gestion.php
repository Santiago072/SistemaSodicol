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
                <div class="success-box">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
                </div>
                <?php endif; ?>
                <?php if ($mensajeError): ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= $basePath ?>?module=tareas&action=gestion" class="formulario">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="form-grid-2">
                        <div class="grupo-campo" style="grid-column: 1 / -1;">
                            <label class="form-label"><i class="bi bi-card-text"></i> Descripción de la Tarea</label>
                            <textarea name="descripcion_tarea" class="form-control" rows="2" required maxlength="500"
                                      placeholder="Describe la instrucción de trabajo..."></textarea>
                        </div>
                        <div class="grupo-campo">
                            <label class="form-label"><i class="bi bi-person"></i> Asignar a Usuario</label>
                            <select name="usuario" class="form-control" required>
                                <option value="">Seleccione un Usuario</option>
                                <?php foreach ($usuarios as $u): ?>
                                <option value="<?= intval($u['id']) ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grupo-campo">
                            <label class="form-label"><i class="bi bi-flag"></i> Estado Inicial</label>
                            <select name="estado" class="form-control" required>
                                <option value="">Seleccione un Estado</option>
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="completo">Completo</option>
                            </select>
                        </div>
                    </div>
                    <div class="grupo-campo-flex mt-10">
                        <button type="submit" class="boton-primario flex-1">
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
    <div class="grid-cards mt-30">
        <?php if (!empty($tareas)): ?>
            <?php foreach ($tareas as $row): ?>
            <div class="card-item ticket-card" style="border-left-color: <?= $row['estado'] === 'completo' ? '#2ed573' : 'var(--gold)' ?>;">
                <?php if ($row['estado'] === 'completo'): ?>
                <div class="ticket-status" style="background: rgba(46, 213, 115, 0.15); color: #7bed9f;"><i class="bi bi-check2-all"></i> Completo</div>
                <?php else: ?>
                <div class="ticket-status" style="background: rgba(212, 168, 83, 0.15); color: var(--gold-light);"><i class="bi bi-clock-history"></i> Pendiente</div>
                <?php endif; ?>
                
                <div class="card-title mt-10"><i class="bi bi-person"></i> <?= htmlspecialchars($row['nombre']) ?></div>
                <div class="card-subtitle mt-10" style="color:var(--white); font-size:14px; line-height:1.5;">
                    <?= htmlspecialchars($row['descripcion_tarea']) ?>
                </div>
                <div class="card-actions">
                    <a href="<?= $basePath ?>?module=tareas&action=editar&id=<?= intval($row['id']) ?>"
                       class="boton-editar"><i class="bi bi-pencil-square"></i> Editar</a>
                    <a href="<?= $basePath ?>?module=tareas&action=eliminar&id=<?= intval($row['id']) ?>"
                       class="boton-eliminar"
                       data-confirm="¿Está seguro de eliminar esta tarea?">
                        <i class="bi bi-trash"></i> Eliminar
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (empty($tareas)): ?>
        <div class="empty-state-card" style="grid-column: 1 / -1;">
            <i class="bi bi-inbox"></i>
            <p>No hay tareas registradas.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php include dirname(__DIR__) . '/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> tareas)</p>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
