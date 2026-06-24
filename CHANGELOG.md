# Registro de Cambios (Changelog)

Todos los cambios notables de este proyecto se documentarán en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto se adhiere al [Versionamiento Semántico](https://semver.org/lang/es/).

## [v1.2.5] - 2026-06-24
### Agregado
- **Script de Restauración**: Se creó un script local (`restaurar.php`) para restaurar los 18 productos originales sin tildes ni caracteres especiales, previniendo distorsión en la codificación de la base de datos.
- **Respaldo Seguro**: Se añadió `restaurar.php` al `.gitignore` para mantenerlo como respaldo local exclusivo, asegurando que no se despliegue en el VPS de producción por motivos de seguridad.

### Modificado
- **Seguridad de Productos**: Se restringió el acceso al módulo "Gestión de Productos". Ahora, la opción de menú (`menu.php`) y las rutas de listar, editar y eliminar (`ProductoController.php`) solo están disponibles para cuentas con el rol de `admin`.
- **Prevención de Caché (Cache Busting)**: Se agregó un parámetro dinámico (`?v=time()`) a la importación de `ajax_tables.js` en `footer.php` para forzar a los navegadores a descargar la versión más reciente del script, solucionando el problema del "doble prompt de confirmación".

### Corregido
- **Fallo Crítico AJAX en Eliminación**: Se arregló un bug engañoso en `ajax_tables.js` donde la animación de eliminación fallaba silenciosamente (`TypeError` por no encontrar un elemento `<tr>` en un diseño de grilla o `.card-item`), lo cual desencadenaba un mensaje genérico falso de "Error de conexión al servidor".
- **Limpieza de Respuesta JSON**: Se incorporó el uso estricto de `@ob_clean()` antes de imprimir `json_encode` en los controladores `UsuarioController` y `ProductoController`, garantizando respuestas JSON impecables y evitando fallos de parseo en JavaScript causados por advertencias o espacios en blanco residuales.

## [v1.2.4] - 2026-06-23
### Agregado
- **Despliegue Automático**: Se incorporó el script `deploy.sh` en la raíz del proyecto para automatizar actualizaciones en producción (manejo de permisos, git reset y docker compose up), basado en las mejores prácticas de la DocumentacionVPS.

### Modificado
- **Docker Network**: Se renombró el servicio de base de datos de `db` a `sodicol_db` en `docker-compose.yml` para evitar colisiones de alias de red internos con otros proyectos en la misma VPS (ej. SistemaPQRS).
- **Seguridad (Historial Git)**: Se reescribió el historial completo del repositorio mediante `git filter-branch` para erradicar permanentemente volcados locales de base de datos (`database/`) que contenían información sensible.

### Corregido
- Bug en `ProductoController` que tras la edición exitosa de un producto, redirigía a una búsqueda vacía (`busqueda=1`) en vez de mostrar el banner de éxito (`updated=1`).

## [v1.2.3] - 2026-06-23
### Agregado
- **Límites de Campos**: Se añadieron restricciones estrictas de longitud en backend mediante `mb_substr()` a los controladores principales (`Usuario`, `Producto`, `Cotizacion`, `Tarea`) para prevenir el envío de cadenas maliciosamente largas.
- **Protección Rate Limiting**: Extensión de la validación de peticiones (`verificar_rate_limit`) a todas las acciones de escritura de datos (crear, editar, guardar ítems) para evitar la saturación del sistema.

### Modificado
- **Limpieza**: Se eliminaron los archivos remanentes de desarrollo de `PHPUnit` (`.phpunit.cache`, `phpunit.phar`, `phpunit.xml`, `tests/`) para mantener un directorio de producción limpio y enfocado.

## [v1.2.2] - 2026-06-21
### Corregido
- Alineación del contenedor de estado vacío ("No se encontraron usuarios") en la vista de lista de usuarios, ajustando la cuadrícula CSS.
- Validación de formulario de "Crear Usuario": La contraseña es ahora verdaderamente opcional tanto en Front-end como en Back-end. Si se omite, el sistema asume el documento de identidad como clave por defecto.
- Funcionalidad de Búsqueda en Vivo (Live Search) en tablas AJAX: Se restauró la capacidad de filtrar en vivo mientras se escribe, solucionando la recarga completa e involuntaria de la página mediante el despacho de un evento cancelable que el manejador asíncrono puede interceptar.

## [v1.2.1] - 2026-06-21
### Modificado
- Migración de la biblioteca **DomPDF** hacia el ecosistema oficial de dependencias de PHP usando `Composer`. Se eliminó la carpeta pesada estática `dompdf/` del control de versiones.
- El archivo `docker/apache/Dockerfile` ahora instala Composer automáticamente y el contenedor descarga las dependencias nativas durante su construcción en producción.

### Corregido
- Eliminación de la estricta rotación del Token CSRF en peticiones estándar `POST` de búsqueda y filtrado (`rotar_token_csrf()`). Esto soluciona el error `"Token de seguridad inválido"` al abrir múltiples pestañas o al usar el botón "Atrás" del navegador, mejorando drásticamente la experiencia de usuario (Multi-Tab Browsing).

## [v1.2.0] - 2026-06-21
### Agregado
- **Seguridad (Rate Limiting)**: Implementación de sistema nativo anti-saturación (`verificar_rate_limit`) configurado a 15 peticiones por minuto. Aplicado a módulos críticos (Login, Buscador AJAX y Generación de PDF) para prevenir ataques de fuerza bruta y colapsos de CPU.
- **Manejo de Errores**: Nuevo manejador global de excepciones (`set_exception_handler` en `index.php`) para evitar caídas silenciosas (pantalla en blanco) y devolver respuestas JSON limpias en entornos AJAX sin tumbar el hilo de Apache.
- **Suite de Pruebas**: Configuración de entorno local con `PHPUnit` e implementación de pruebas unitarias al 100% de cobertura sobre las utilidades de seguridad (Tokens CSRF, Rate Limiting y Sanitización).
- **Optimización de BD**: Incorporación de índices de alta velocidad (`INDEX`) en columnas de búsqueda masiva (`titulo` de productos, `numero_cotizacion` y `nombre_cliente`) dentro de `docker/mysql/init.sql` y `BD.txt`, reduciendo el tiempo de escaneo a microsegundos.

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
