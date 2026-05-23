# Sistema Sodicol - Sistema de Gestión Empresarial

Sistema web de gestión para empresa de diseño y mobiliario, desarrollado en PHP con MySQL.

## 📋 Características

- **Autenticación de usuarios** con roles (Administrador/Usuario)
- **Gestión de usuarios** (CRUD completo)
- **Gestión de productos** con imágenes
- **Sistema de tareas** asignables a usuarios
- **Cotizaciones** con generación de PDF
- **Interfaz responsiva** con modo claro/oscuro

## 🔧 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP requeridas:
  - mysqli
  - gd (para procesamiento de imágenes)
  - mbstring

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Santiago072/SistemaSodicol.git
cd SistemaSodicol
```

### 2. Configurar la base de datos

```bash
# Crear la base de datos
mysql -u root -p < BD.txt
```

### 3. Configurar variables de entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example config/.env

# Editar config/.env con tus credenciales
```

Ejemplo de configuración:
```env
DB_HOST=localhost
DB_USER=tu_usuario
DB_PASS=tu_contraseña
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
UPLOAD_MAX_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp
```

### 4. Ejecutar migración de contraseñas

**IMPORTANTE**: Este paso es necesario para actualizar el sistema de autenticación.

```
1. Acceder a: http://localhost/PROYECTO_SODICOL/migracion_password.php
2. Seguir las instrucciones en pantalla
3. Todos los usuarios usarán su documento como contraseña temporal
```

### 5. Configurar permisos

```bash
# Dar permisos de escritura a la carpeta de uploads
chmod 755 uploads/
```

## 🚀 Uso

### Acceso al Sistema

```
URL: http://localhost/PROYECTO_SODICOL/
```

### Credenciales Iniciales

Después de ejecutar la migración, los usuarios podrán iniciar sesión con:
- **Correo**: El correo registrado en la base de datos
- **Contraseña**: Su número de documento (contraseña temporal)

**Se recomienda cambiar la contraseña después del primer inicio de sesión.**

## 👥 Roles de Usuario

### Administrador
- Gestión completa de usuarios
- Crear, editar y eliminar productos
- Asignar tareas a usuarios
- Crear y consultar cotizaciones
- Generar PDFs de cotizaciones

### Usuario
- Ver tareas asignadas
- Marcar tareas como completadas
- Crear cotizaciones
- Consultar sus propias cotizaciones

## 🔒 Seguridad

El sistema implementa las siguientes medidas de seguridad:

- ✅ **Contraseñas hasheadas** con bcrypt (password_hash)
- ✅ **Prepared statements** para prevenir SQL Injection
- ✅ **Tokens CSRF** en formularios críticos
- ✅ **Validación de archivos** en uploads (tipo MIME, tamaño, extensión)
- ✅ **Sanitización de entradas** con htmlspecialchars
- ✅ **Sesiones seguras** con timeout configurable
- ✅ **Variables de entorno** para credenciales sensibles
- ✅ **Control de acceso** basado en roles
- ✅ **Validación de permisos** en cada endpoint

## 📁 Estructura del Proyecto

```
PROYECTO_SODICOL/
├── config/
│   ├── conexion.php          # Conexión a base de datos
│   ├── seguridad.php          # Funciones de seguridad
│   └── .env                   # Variables de entorno (no incluir en git)
├── usuarios/
│   ├── lista_usuarios.php
│   ├── crear_usuario.php
│   ├── editar_usuario.php
│   └── eliminar_usuario.php
├── productos/
│   ├── lista_productos.php
│   ├── editar_producto.php
│   └── eliminar_producto.php
├── tareas/
│   ├── tareas_usuarios.php
│   ├── editar_tarea.php
│   └── eliminar_tarea.php
├── cotizaciones/
│   ├── crear_cotizacion.php
│   ├── consultar_cotizacion.php
│   ├── editar_cotizacion.php
│   └── generar_pdf.php
├── css/
│   └── estilos.css
├── img/                       # Imágenes del sistema
├── uploads/                   # Archivos subidos por usuarios
├── includes/
│   ├── menu.php
│   └── script.js
├── index.php                  # Página de login
├── panel.php                  # Dashboard principal
├── logout.php                 # Cerrar sesión
└── BD.txt                     # Script de base de datos
```

## 🛠️ Funciones de Seguridad Disponibles

El archivo `config/seguridad.php` proporciona las siguientes funciones:

```php
iniciar_sesion_segura()        // Inicia sesión con configuración segura
generar_token_csrf()            // Genera token CSRF
verificar_token_csrf($token)    // Verifica token CSRF
sanitizar_entrada($data)        // Sanitiza entrada de usuario
validar_email($email)           // Valida formato de email
validar_numero($numero)         // Valida número positivo
validar_imagen($archivo)        // Valida archivo de imagen
generar_nombre_archivo($ext)    // Genera nombre único para archivo
verificar_autenticacion()       // Verifica usuario logueado
verificar_admin()               // Verifica usuario administrador
regenerar_sesion()              // Regenera ID de sesión
```

## 📝 Desarrollo

### Agregar Nuevas Funcionalidades

Al agregar nuevas funcionalidades, seguir estas prácticas:

1. **Usar prepared statements** para todas las consultas SQL
2. **Incluir tokens CSRF** en todos los formularios
3. **Sanitizar todas las entradas** de usuario
4. **Validar permisos** antes de ejecutar acciones
5. **Validar archivos** antes de subirlos al servidor

### Ejemplo de Código Seguro

```php
<?php
require_once '../config/conexion.php';
require_once '../config/seguridad.php';

iniciar_sesion_segura();
verificar_autenticacion();

$conexion = conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar CSRF
    if (!verificar_token_csrf($_POST['csrf_token'])) {
        die('Token inválido');
    }
    
    // Sanitizar entrada
    $nombre = sanitizar_entrada($_POST['nombre']);
    
    // Usar prepared statement
    $stmt = mysqli_prepare($conexion, "INSERT INTO tabla (nombre) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $nombre);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$csrf_token = generar_token_csrf();
?>
```

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verificar credenciales en `config/.env`
- Asegurar que MySQL esté ejecutándose
- Verificar que la base de datos exista

### Error al subir imágenes
- Verificar permisos de la carpeta `uploads/`
- Verificar tamaño máximo en `php.ini` (upload_max_filesize)
- Verificar extensiones permitidas en `.env`

### Sesión expirada constantemente
- Ajustar `SESSION_LIFETIME` en `.env`
- Verificar configuración de sesiones en `php.ini`

## 📄 Licencia

Este proyecto es de uso interno para Sodicol.

## 👨‍💻 Contribuir

Para contribuir al proyecto:

1. Crear una rama para la nueva funcionalidad
2. Seguir las prácticas de seguridad establecidas
3. Probar exhaustivamente los cambios
4. Crear un pull request con descripción detallada

## 📞 Soporte

Para soporte técnico, contactar al equipo de desarrollo interno.

---

**Nota**: Este sistema maneja información sensible. Asegurar que todas las credenciales y datos de configuración se mantengan seguros y no se incluyan en el control de versiones.
