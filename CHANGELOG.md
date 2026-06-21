# Registro de Cambios (Changelog)

Todos los cambios notables de este proyecto se documentarán en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto se adhiere al [Versionamiento Semántico](https://semver.org/lang/es/).

## [v1.2.0] - 2026-06-21
### Agregado
- **Seguridad (Rate Limiting)**: Implementación de sistema nativo anti-saturación (`verificar_rate_limit`) configurado a 15 peticiones por minuto. Aplicado a módulos críticos (Login, Buscador AJAX y Generación de PDF) para prevenir ataques de fuerza bruta y colapsos de CPU.
- **Manejo de Errores**: Nuevo manejador global de excepciones (`set_exception_handler` en `index.php`) para evitar caídas silenciosas (pantalla en blanco) y devolver respuestas JSON limpias en entornos AJAX sin tumbar el hilo de Apache.
- **Suite de Pruebas**: Configuración de entorno local con `PHPUnit` e implementación de pruebas unitarias al 100% de cobertura sobre las utilidades de seguridad (Tokens CSRF, Rate Limiting y Sanitización).
- **Optimización de BD**: Incorporación de índices de alta velocidad (`INDEX`) en columnas de búsqueda masiva (`titulo` de productos, `numero_cotizacion` y `nombre_cliente`) dentro de `docker/mysql/init.sql` y `BD.txt`, reduciendo el tiempo de escaneo a microsegundos.

## [v1.2.1] - 2026-06-21
### Modificado
- Migración de la biblioteca **DomPDF** hacia el ecosistema oficial de dependencias de PHP usando `Composer`. Se eliminó la carpeta pesada estática `dompdf/` del control de versiones.
- El archivo `docker/apache/Dockerfile` ahora instala Composer automáticamente y el contenedor descarga las dependencias nativas durante su construcción en producción.

### Corregido
- Eliminación de la estricta rotación del Token CSRF en peticiones estándar `POST` de búsqueda y filtrado (`rotar_token_csrf()`). Esto soluciona el error `"Token de seguridad inválido"` al abrir múltiples pestañas o al usar el botón "Atrás" del navegador, mejorando drásticamente la experiencia de usuario (Multi-Tab Browsing).

## [v1.1.2] - 2026-06-20
### Modificado
- Unificación del diseño visual de los buscadores en los módulos "Crear Cotización" y "Consultar Cotización" para que coincidan con el diseño del resto del sistema (`filter-panel`).
- Eliminación del cursor personalizado animado en la pantalla de inicio de sesión (`login`) por solicitud para mejorar la usabilidad tradicional.

## [v1.1.1] - 2026-06-20
### Corregido
- Solución al error 500 en generación de PDF por falta de la carpeta `dompdf/vendor`, ajustando el archivo `.gitignore`.
- Prevención total de envío de formulario de búsqueda de productos al pulsar Enter, reemplazando las etiquetas `<form>` por `<div>`.
- Optimización de consulta en finalización de cotización, reemplazando `LOCK TABLES` por `SELECT FOR UPDATE` para evitar problemas de permisos de base de datos.
- Mejora de robustez en conversión de imágenes locales a Base64 para el PDF, tolerando archivos inexistentes y previniendo caídas críticas de DomPDF.
- Corrección de envío de cookies en las solicitudes AJAX del buscador mediante la cabecera `credentials: 'same-origin'`.

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
- Limpieza completa de contraseñas de la base de datos en `init.sql` forzando un hash válido para el administrador.
- `config/seguridad.php`: se removió el uso de `htmlspecialchars` al momento de entrada de datos (sanitización) para no corromper la BD, delegando el escape de HTML puramente a la capa de vista.

### Corregido
- Sintaxis de fechas en la tabla `cotizaciones` de `BD.txt` e `init.sql`. Se eliminó la dependencia a fechas nulas `0000-00-00` o la antigua sintaxis `DEFAULT CURRENT_DATE` que ocasionaba error 500 en MySQL 8 en modo estricto. En su lugar se utiliza sintaxis compatible con MySQL 8.0 `DEFAULT (CURRENT_DATE)`.
