<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/UsuarioController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new UsuarioController($conexion);
$data       = $controller->editar();
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
    <div class="encabezado-pagina"><h1>Editar Usuario</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= intval($usuario['id']) ?>">
            <div class="grupo-campo">
                <label for="documento">Documento *</label>
                <input type="text" id="documento" name="documento" value="<?= htmlspecialchars($usuario['documento']) ?>" required maxlength="20">
            </div>
            <div class="grupo-campo">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="correo">Correo Electrónico *</label>
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required maxlength="100">
            </div>
            <div class="grupo-campo">
                <label for="nueva_password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" id="nueva_password" name="nueva_password" minlength="6" maxlength="50">
            </div>
            <div class="grupo-campo">
                <label for="telefono">Teléfono *</label>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" required maxlength="20">
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
                <a href="lista_usuarios.php" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
