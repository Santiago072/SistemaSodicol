<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/TareaController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new TareaController($conexion);
$data       = $controller->editar();
extract($data);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <title>Editar Tarea</title>
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
    <div class="encabezado-pagina"><h1>Editar Tarea</h1></div>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="formulario-contenedor">
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="id" value="<?= intval($tarea['id']) ?>">
            <div class="grupo-campo">
                <label for="usuario_id">Usuario Asignado</label>
                <select id="usuario_id" name="usuario_id" required>
                    <option value="">Seleccione un Usuario</option>
                    <?php foreach ($usuarios as $u): ?>
                    <option value="<?= intval($u['id']) ?>" <?= $u['id'] == $tarea['usuario_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grupo-campo">
                <label for="descripcion_tarea">Descripción</label>
                <textarea id="descripcion_tarea" name="descripcion_tarea" required maxlength="500"><?= htmlspecialchars($tarea['descripcion_tarea']) ?></textarea>
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
                <a href="tareas_usuarios.php" class="boton-limpiar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
