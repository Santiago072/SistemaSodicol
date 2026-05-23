<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_admin();

$conexion = conexion();
$base_path = '/PROYECTO_SODICOL/';
$mensaje_error = '';
$mensaje_exito = '';

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
        $password = $_POST['password'] ?? '';

        // Validaciones
        if ($documento != "" && $nombre != "" && $correo != "" && $telefono != "" && $rol != "") {
            
            if (!validar_email($correo)) {
                $mensaje_error = "El correo electrónico no es válido";
            } elseif (!in_array($rol, ['admin', 'usuario'])) {
                $mensaje_error = "Rol no válido";
            } else {
                // Verificar si ya existe el documento o correo usando prepared statement
                $stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE documento = ? OR correo = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt, "ss", $documento, $correo);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);

                if (mysqli_num_rows($resultado) > 0) {
                    $mensaje_error = "El documento o correo ya está registrado";
                } else {
                    // Generar hash de contraseña (usar documento como contraseña temporal si no se proporciona)
                    $password_final = !empty($password) ? $password : $documento;
                    $password_hash = password_hash($password_final, PASSWORD_DEFAULT);
                    
                    // Insertar usuario con prepared statement
                    $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (documento, nombre, correo, password, telefono, rol) VALUES (?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "ssssss", $documento, $nombre, $correo, $password_hash, $telefono, $rol);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        header("Location: lista_usuarios.php?success=1");
                        exit();
                    } else {
                        $mensaje_error = "Error al crear el usuario";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            $mensaje_error = "Todos los campos son obligatorios";
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
    <title>Nuevo Usuario</title>
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
            <h1>Crear Nuevo Usuario</h1>
        </div>
        <?php if ($mensaje_error != '') { ?>
        <br>
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo $mensaje_error; ?></span>
        </div>
        <br>
        <?php } ?>
        <div class="formulario-contenedor">
            <form method="POST" class="formulario">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
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
                    <small style="color: #666;">Si no se proporciona, se usará el documento como contraseña temporal</small>
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
                </div>
            </form>

        </div>
    </div>
    <script src="../includes/script.js"></script>
</body>

</html>