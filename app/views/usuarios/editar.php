<?php
/**
 * Vista: Editar usuario
 * Variables: $usuario, $mensajeError, $csrf_token
 */
$pageTitle = 'Editar Usuario';
$basePath  = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Editar Usuario';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Editar Usuario</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" action="/PROYECTO_SODICOL/?module=usuarios&action=editar&id=<?= intval($usuario['id']) ?>" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= intval($usuario['id']) ?>">

            <div class="grupo-campo">
                <label for="documento">Documento *</label>
                <input type="text" id="documento" name="documento"
                       value="<?= htmlspecialchars($usuario['documento']) ?>" required maxlength="20">
            </div>
            <div class="grupo-campo">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre"
                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo"
                       value="<?= htmlspecialchars($usuario['correo']) ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="nueva_password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" id="nueva_password" name="nueva_password" minlength="6" maxlength="50">
            </div>
            <div class="grupo-campo">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono"
                       value="<?= htmlspecialchars($usuario['telefono']) ?>" required maxlength="20">
            </div>
            <div class="grupo-campo">
                <label for="rol">Rol *</label>
                <select id="rol" name="rol" required>
                    <option value="">Seleccione un Rol</option>
                    <option value="admin"   <?= $usuario['rol'] === 'admin'   ? 'selected' : '' ?>>Administrador</option>
                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                </select>
            </div>
            <div class="grupo-campo">
                <label for="estado">Estado *</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione un Estado</option>
                    <option value="activo"   <?= $usuario['estado'] === 'activo'   ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Actualizar Usuario</button>
                <a href="/PROYECTO_SODICOL/?module=usuarios&action=lista" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
