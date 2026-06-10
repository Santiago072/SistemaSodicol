<?php
/**
 * Vista: Crear usuario
 * Variables: $mensajeError, $csrf_token
 */
$pageTitle = 'Nuevo Usuario';
$basePath  = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(dirname(dirname(__DIR__))) . '/includes/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Crear Nuevo Usuario';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina"><h1>Crear Nuevo Usuario</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" action="/PROYECTO_SODICOL/usuarios/crear_usuario.php" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="grupo-campo">
                <label for="documento">Documento *</label>
                <input type="text" id="documento" name="documento" required maxlength="20">
            </div>
            <div class="grupo-campo">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="password">Contraseña (opcional)</label>
                <input type="password" id="password" name="password" minlength="6" maxlength="50">
                <small style="color:#888;">Si no se proporciona, se usará el documento como contraseña temporal</small>
            </div>
            <div class="grupo-campo">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono" required maxlength="20">
            </div>
            <div class="grupo-campo">
                <label for="rol">Rol *</label>
                <select id="rol" name="rol" required>
                    <option value="">Seleccione un Rol</option>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Usuario</option>
                </select>
            </div>
            <div class="grupo-campo">
                <button type="submit" class="boton-primario">Crear Usuario</button>
                <a href="/PROYECTO_SODICOL/usuarios/lista_usuarios.php" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
