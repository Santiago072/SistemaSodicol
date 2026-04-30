<?php
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: ../index.php");
    exit();
}
$rol = $_SESSION['rol'];
$base_path = '/PROYECTO_SODICOL/'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>Menu</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <nav class="menu-principal">
        <div class="menu-lateral" id="menuLateral">
            <div class="logo-wrap">
                <div class="logo-ring-wrap">
                    <!-- NUEVO: Anillos giratorios -->
                    <div class="logo-ring"></div>
                    <div class="logo-ring"></div>
                    <div class="logo-ring"></div>
                    <div class="logo-halo"></div>
                    <div class="logo-halo"></div>
                    <div class="logo-halo"></div>
                    <div class="logo-dash"></div>
                    <div class="logo-circle">
                        <img src="<?php echo $base_path; ?>logo/logo.png" alt="Logo Sodicol">
                    </div>
                </div>
            </div>
            <ul class="lista-menu-lateral">
                <li><a href="<?php echo $base_path; ?>panel.php"><i class="bi bi-house-door-fill"></i></a></li>
                <?php if($rol == 'admin'): ?>
                <li class="menu-desplegable" data-panel="usuarios">
                    <a href="#"><i class="bi bi-person-fill"></i></a>
                </li>
                <?php endif; ?>
                <li class="menu-desplegable" data-panel="cotizaciones">
                    <a href="#"><i class="bi bi-currency-dollar"></i></a>
                </li>
                <li><a href="<?php echo $base_path; ?>logout.php"><i class="bi bi-box-arrow-right"></i></a></li>
            </ul>
        </div>
    </nav>

    <div class="panel-flotante" id="panel">
        <ul class="submenu" id="submenu-dinamico"></ul>
    </div>

    <script>
        const panel = document.getElementById('panel');
        const submenu = document.getElementById('submenu-dinamico');
        let timeout;
        
        const menus = {
            usuarios: `
                <h3>Usuarios</h3>
                <li><a href="<?php echo $base_path; ?>usuarios/lista_usuarios.php"><i class="fas fa-user-friends"></i> Lista de Usuarios</a></li>
                <li><a href="<?php echo $base_path; ?>usuarios/crear_usuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>
                <li><a href="<?php echo $base_path; ?>tareas/tareas_usuarios.php"><i class="fas fa-tasks"></i> Tareas Usuarios</a></li>
            `,
            cotizaciones: `
                <h3>Cotizaciones</h3>
                <li><a href="<?php echo $base_path; ?>cotizaciones/crear_cotizacion.php"><i class="fas fa-dollar-sign"></i> Crear Cotización</a></li>
                <li><a href="<?php echo $base_path; ?>cotizaciones/consultar_cotizacion.php"><i class="fas fa-list"></i> Consultar Cotización</a></li>
                <li><a href="<?php echo $base_path; ?>productos/lista_productos.php"><i class="fas fa-box"></i> Lista de Productos</a></li>
            `
        };

        document.querySelectorAll('.menu-desplegable').forEach(item => {
            const tipo = item.dataset.panel;
            item.addEventListener('mouseenter', () => {
                clearTimeout(timeout);
                submenu.innerHTML = menus[tipo];
                panel.classList.add('visible');
            });
            item.addEventListener('mouseleave', () => {
                timeout = setTimeout(() => {
                    panel.classList.remove('visible');
                }, 300);
            });
        });

        panel.addEventListener('mouseenter', () => clearTimeout(timeout));
        panel.addEventListener('mouseleave', () => panel.classList.remove('visible'));
    </script>
</body>
</html>