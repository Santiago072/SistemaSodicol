<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';
require_once '../app/controllers/UsuarioController.php';

iniciar_sesion_segura();
$conexion   = conexion();
$controller = new UsuarioController($conexion);
$data       = $controller->listar();
extract($data);

$base_path  = '/PROYECTO_SODICOL/';
$urlBase    = 'lista_usuarios.php' . ($busqueda !== '' ? '?busqueda=' . urlencode($busqueda) : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>if (localStorage.getItem('sodicol_tema') === 'dia') document.documentElement.style.background = '#f0e6d3';</script>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Lista Usuarios</title>
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

    <div class="encabezado-pagina"><h1>Gestión Usuarios</h1></div>

    <?php if ($mensajeExito): ?>
    <div class="success-box" style="background:#d4edda;color:#155724;padding:15px;margin:15px 0;border-radius:8px;border:1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensajeExito) ?>
    </div>
    <?php endif; ?>

    <?php if ($mensajeError): ?>
    <div class="error-box"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <div class="barra-busqueda">
        <form action="lista_usuarios.php" method="GET" class="formulario-busqueda">
            <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar usuario...">
            <button type="submit" class="boton-primario">Buscar</button>
            <?php if ($busqueda): ?>
            <a href="lista_usuarios.php" class="boton-limpiar">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre</th><th>Rol</th><th>Estado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['rol']) ?></td>
                    <td><?= htmlspecialchars($u['estado']) ?></td>
                    <td class="acciones-tabla">
                        <a href="editar_usuario.php?id=<?= intval($u['id']) ?>" class="boton-editar"><i class="fas fa-edit"></i></a>
                        <a href="eliminar_usuario.php?id=<?= intval($u['id']) ?>" class="boton-eliminar"
                           onclick="return confirm('¿Está seguro de eliminar este usuario?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--gold-light);">
                    <i class="bi bi-search"></i> No se encontraron usuarios.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include '../app/views/partials/paginacion.php'; ?>
    <?php if ($total > 0): ?>
    <p class="pag-info">Mostrando página <?= $paginaActual ?> de <?= $totalPaginas ?> (<?= $total ?> usuarios)</p>
    <?php endif; ?>
</div>
<script src="../includes/script.js"></script>
</body>
</html>
