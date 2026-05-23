<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_admin();

$conexion = conexion();
$mensaje_error = '';

// Validar ID de usuario
if (!isset($_GET['id']) || !validar_numero($_GET['id'])) {
    header("Location: lista_usuarios.php");
    exit();
}

$id_usuario = intval($_GET['id']);

// Obtener usuario usando prepared statement
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$usuario) {
    header("Location: lista_usuarios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_token_csrf($_POST['csrf_token'])) {
        $mensaje_error = "Token de seguridad inválido";
    } else {
        $documento = sanitizar_entrada($_POST['documento']);
        $nombre = sanitizar_entrada($_POST['nombre']);
        $correo = sanitizar_entrada($_POST['correo']);
        $telefono = sanitizar_entrada($_POST['telefono']);
        $rol = sanitizar_entrada($_POST['rol']);
        $estado = sanitizar_entrada($_POST['estado']);
        $nueva_password = $_POST['nueva_password'] ?? '';

        // Validaciones
        if (!validar_email($correo)) {
            $mensaje_error = "El correo electrónico no es válido";
        } elseif (!in_array($rol, ['admin', 'usuario'])) {
            $mensaje_error = "Rol no válido";
        } elseif (!in_array($estado, ['activo', 'inactivo'])) {
            $mensaje_error = "Estado no válido";
        } else {
            // Si se proporciona nueva contraseña, actualizarla
            if (!empty($nueva_password)) {
                $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET documento=?, nombre=?, correo=?, password=?, telefono=?, rol=?, estado=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sssssssi", $documento, $nombre, $correo, $password_hash, $telefono, $rol, $estado, $id_usuario);
            } else {
                $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET documento=?, nombre=?, correo=?, telefono=?, rol=?, estado=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssssssi", $documento, $nombre, $correo, $telefono, $rol, $estado, $id_usuario);
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: lista_usuarios.php?updated=1");
                exit();
            } else {
                $mensaje_error = "Error al actualizar: " . mysqli_error($conexion);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$csrf_token = generar_token_csrf();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (localStorage.getItem('sodicol_tema') === 'dia') {
            document.documentElement.style.background = '#f0e6d3';
        }
    </script>
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>
    <canvas id="particle-canvas"></canvas>
    <div class="noise-overlay"></div>
    <?php include '../includes/menu.php'; ?>
    <div class="contenido-principal">
        <div class="cabecera-superior">
            <button class="boton-menu-ocultar" id="btnMenu">
                <i class="fas fa-bars"></i> Ocultar Menú
            </button>
            <!-- Botón modo día/noche -->
            <button class="btn-modo" id="btnModo" title="Cambiar tema">
                <span class="modo-icon-dia"><i class="bi bi-sun-fill"></i></span>
                <span class="modo-icon-noche"><i class="bi bi-moon-stars-fill"></i></span>
                <span class="modo-label"></span>
            </button>
        </div>
        <div class="encabezado-pagina">
            <h1>Editar Usuario</h1>
        </div>
        <?php if ($mensaje_error != '') { ?>
        <br>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
        <br>
        <?php } ?>
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                
                <div class="grupo-campo">
                    <label for="documento">Documento *</label>
                    <input type="text" id="documento" name="documento" value="<?php echo htmlspecialchars($usuario['documento']); ?>" required maxlength="20">
                </div>

                <div class="grupo-campo">
                    <label for="nombre">Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required maxlength="100">
                </div>

                <div class="grupo-campo">
                    <label for="correo">Correo Electrónico *</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required maxlength="100">
                </div>

                <div class="grupo-campo">
                    <label for="nueva_password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="nueva_password" name="nueva_password" minlength="6" maxlength="50">
                </div>

                <div class="grupo-campo">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required maxlength="20">
                </div>

                <div class="grupo-campo">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" required>
                        <option value="">Seleccione un Rol</option>
                        <option value="admin" <?php if($usuario['rol']=='admin' ) echo 'selected' ; ?>>Administrador
                        </option>
                        <option value="usuario" <?php if($usuario['rol']=='usuario' ) echo 'selected' ; ?>>Usuario
                        </option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="estado">Estado *</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione un Estado</option>
                        <option value="activo" <?php if($usuario['estado']=='activo' ) echo 'selected' ; ?>>Activo
                        </option>
                        <option value="inactivo" <?php if($usuario['estado']=='inactivo' ) echo 'selected' ; ?>>Inactivo
                        </option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <button type="submit" class="boton-primario">Actualizar Usuario</button>
                    <a class="boton-limpiar" href="lista_usuarios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>