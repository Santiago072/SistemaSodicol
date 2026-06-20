# Registro de Cambios (Changelog)

Todos los cambios notables de este proyecto se documentarán en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto se adhiere al [Versionamiento Semántico](https://semver.org/lang/es/).

## [v1.1.0] - 2026-06-20
### Agregado
- Scripts de mantenimiento (`fix_db.php`, `check_uploads.php`, `unzip_images.php`) para corregir codificación de base de datos e importar imágenes en producción de forma segura.

### Modificado
- **UI/UX**: Rediseño visual general utilizando tema claro (`light-theme`), efecto *glassmorphism* y micro-animaciones en tarjetas de productos y usuarios.
- Búsqueda en vivo y paginación ahora están integrados directamente con diseño en formato *Grid*.

### Corregido
- Corrección de `ajax_tables.js` para soportar reemplazo de contenedores `.grid-cards` en paginación y evitar conflictos con la inicialización del `loginForm`.
- Corrección de comportamiento "doble confirmación" al eliminar un ítem de cotización y soporte de respuesta JSON en el controlador.

## [v1.0.0] - 2026-06-20
### Agregado
- Implementación de entorno Docker y Docker Compose para producción (Apache + PHP 8.2 + MySQL 8.0).
- Archivo `.env` para manejo de variables de entorno de forma segura, junto con `EnvLoader.php`.
- Script de backups automatizados (`docker/backup.sh`) para realizar volcados seguros de la base de datos MySQL 8.
- Sistema de versionamiento con este archivo `CHANGELOG.md` y documentación oficial en `docs/`.
- Protección anti-replay CSRF post-login y ajuste de tiempo de vida de sesiones parametrizable.

### Modificado
- `index.php` ahora detecta automáticamente si se encuentra en entorno XAMPP o Docker, calculando la `BASE_URL`.
- La conexión a la base de datos en `config/conexion.php` ahora lee las credenciales dinámicas mediante el `.env`.
- Limpieza completa de contraseñas de la base de datos en `init.sql` forzando un hash válido para la clave `1234567890`.
- `config/seguridad.php`: se removió el uso de `htmlspecialchars` al momento de entrada de datos (sanitización) para no corromper la BD, delegando el escape de HTML puramente a la capa de vista.

### Corregido
- Sintaxis de fechas en la tabla `cotizaciones` de `BD.txt` e `init.sql`. Se eliminó la dependencia a fechas nulas `0000-00-00` o la antigua sintaxis `DEFAULT CURRENT_DATE` que ocasionaba error 500 en MySQL 8 en modo estricto. En su lugar se utiliza sintaxis compatible con MySQL 8.0 `DEFAULT (CURRENT_DATE)`.
