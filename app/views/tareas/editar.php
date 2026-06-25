<?php
/**
 * Vista: Editar tarea
 * Variables: $tarea, $usuarios, $mensajeError, $csrf_token
 */
$pageTitle = 'Editar Tarea';
$basePath  = defined('BASE_URL') ? BASE_URL : '/SistemaSodicol/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Editar Tarea';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Editar Tarea</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" action="<?= $basePath ?>?module=tareas&action=editar&id=<?= intval($tarea['id']) ?>" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= intval($tarea['id']) ?>">

            <div class="grupo-campo">
                <label for="usuario_id">Usuario Asignado</label>
                <select id="usuario_id" name="usuario_id" required>
                    <option value="">Seleccione un Usuario</option>
                    <?php foreach ($usuarios as $u): ?>
                    <option value="<?= intval($u['id']) ?>"
                            <?= $u['id'] == $tarea['usuario_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grupo-campo">
                <label for="descripcion_tarea">Descripción</label>
                <textarea id="descripcion_tarea" name="descripcion_tarea" required maxlength="150"><?= htmlspecialchars($tarea['descripcion_tarea']) ?></textarea>
            </div>
            <div class="grupo-campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione un Estado</option>
                    <option value="pendiente" <?= $tarea['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="completo"  <?= $tarea['estado'] === 'completo'  ? 'selected' : '' ?>>Completo</option>
                </select>
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Actualizar Tarea</button>
                <a href="<?= $basePath ?>?module=tareas&action=gestion" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
