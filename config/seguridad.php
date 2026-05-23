<?php
/**
 * Funciones de seguridad para el sistema
 */

// Iniciar sesión segura
function iniciar_sesion_segura() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Cambiar a 1 si se usa HTTPS
        session_start();
        
        // Verificar timeout de sesión
        $timeout = $_ENV['SESSION_LIFETIME'] ?? 3600;
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
            session_unset();
            session_destroy();
            header("Location: ../index.php?timeout=1");
            exit();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

// Generar token CSRF
function generar_token_csrf() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verificar_token_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Sanitizar entrada
function sanitizar_entrada($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validar email
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validar número
function validar_numero($numero) {
    return is_numeric($numero) && $numero > 0;
}

// Validar archivo de imagen
function validar_imagen($archivo) {
    $extensiones_permitidas = explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,gif,webp');
    $max_size = $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880; // 5MB por defecto
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return ['valido' => false, 'mensaje' => 'Error al subir el archivo'];
    }
    
    if ($archivo['size'] > $max_size) {
        return ['valido' => false, 'mensaje' => 'El archivo es demasiado grande (máximo 5MB)'];
    }
    
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensiones_permitidas)) {
        return ['valido' => false, 'mensaje' => 'Tipo de archivo no permitido'];
    }
    
    // Verificar tipo MIME real
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);
    
    $mimes_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mime, $mimes_permitidos)) {
        return ['valido' => false, 'mensaje' => 'El archivo no es una imagen válida'];
    }
    
    return ['valido' => true, 'mensaje' => 'Archivo válido'];
}

// Generar nombre único para archivo
function generar_nombre_archivo($extension) {
    return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
}

// Verificar si el usuario está autenticado
function verificar_autenticacion() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nombre'])) {
        header("Location: ../index.php");
        exit();
    }
}

// Verificar si el usuario es administrador
function verificar_admin() {
    verificar_autenticacion();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header("Location: ../panel.php");
        exit();
    }
}

// Regenerar ID de sesión
function regenerar_sesion() {
    session_regenerate_id(true);
}
