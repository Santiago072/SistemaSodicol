# Sistema Sodicol — Sistema de Gestión Empresarial

Sistema web de gestión interno para **Sodicol Zomac S.A.S**, empresa de diseño y mobiliario. Desarrollado en PHP nativo con MySQL, sin frameworks externos.

---

## 📚 Documentación y Manuales
- [Especificación de Requisitos](docs/Especificacion_Requisitos.md)
- [Documentación Técnica](docs/documentacion-tecnica.md)
- [Manual de Usuario](docs/Manual_de_Usuario.md)
- [Arquitectura y Seguridad](docs/ARQUITECTURA_Y_SEGURIDAD.md)
- [Gestión de Datos y Versionamiento](docs/BACKUPS_Y_VERSIONAMIENTO.md)
- [Registro de Cambios (Changelog)](CHANGELOG.md)

---

## Funcionalidades

| Módulo | Descripción |
|---|---|
| **Autenticación** | Login con contraseñas bcrypt, roles (admin / usuario), timeout de sesión |
| **Usuarios** | CRUD completo con paginación y búsqueda |
| **Productos** | Catálogo con imágenes, paginación, búsqueda. Verificación de dependencias antes de eliminar |
| **Tareas** | Asignación de instrucciones de trabajo a usuarios, seguimiento de estado, paginación |
| **Cotizaciones** | Creación con ítems, auto-registro de productos, generación de PDF (DomPDF), consulta paginada |
| **Panel** | Dashboard con contadores y tareas pendientes del usuario |

---

## Requisitos

- Entorno de producción: **Docker** y **Docker Compose**
- Entorno de desarrollo: XAMPP (PHP 8.2 o superior), Composer
- Servidor web: Caddy (incluido en contenedor) o Apache (local)

---

## Instalación en Producción (Docker VPS)

### 1. Clonar

```bash
git clone https://github.com/Santiago072/SistemaSodicol.git
cd SistemaSodicol
```

### 2. Variables de entorno

Copia `.env.example` a `.env`:

```bash
cp .env.example .env
```

Y edita `.env` con tus contraseñas seguras:

```env
DB_HOST=sodicol_db
DB_USER=sodicol
DB_PASS=tu_contraseña_segura
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
COOKIE_SECURE=1
UPLOAD_MAX_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp
```

### 3. Despliegue Automático

El proyecto incluye un script de despliegue que maneja permisos, descargas de GitHub y contenedores de forma automatizada:

```bash
./deploy.sh
```

El servidor web interno (Caddy) y PHP estarán corriendo en el puerto `8891`. Solo necesitas configurar un proxy inverso (Nginx) para apuntar tu dominio a `127.0.0.1:8891`.

### 4. Acceder

```
http://tudominio.com/
```

**Credenciales iniciales** (usuario administrador por defecto):
- Correo: `admin@sodicol.com`
- Contraseña: Se te asignará temporalmente o deberás restaurar tu copia local de la BD (ver abajo).

---

## Restaurar Base de Datos Local

Si tienes tu base de datos de desarrollo (ej. `sistema_sodicol_con_datos_utf8.sql`), puedes inyectarla en producción:

```bash
source .env
docker exec -i sodicol_db mariadb -u $DB_USER -p$DB_PASS $DB_NAME < database/sistema_sodicol_con_datos_utf8.sql
```

---

## Estructura del proyecto (Patrón MVC)

El proyecto utiliza un patrón MVC completo con un único punto de entrada (Front Controller).

```text
SistemaSodicol/
├── app/
│   ├── contracts/            # Interfaces (Principios SOLID)
│   │   └── RepositoryInterface.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── PanelController.php
│   │   ├── UsuarioController.php
│   │   ├── ProductoController.php
│   │   ├── TareaController.php
│   │   └── CotizacionController.php
│   ├── models/               # Patrón Repository
│   │   ├── UsuarioModel.php
│   │   ├── ProductoModel.php
│   │   ├── TareaModel.php
│   │   └── CotizacionModel.php
│   ├── services/             # Lógica de negocio reutilizable
│   │   └── FileUploadService.php
│   └── views/
│       ├── auth/
│       ├── cotizaciones/
│       ├── layout/
│       ├── panel/
│       ├── partials/
│       ├── productos/
│       ├── tareas/
│       └── usuarios/
├── config/
│   ├── conexion.php          # Crea y devuelve la conexión mysqli
│   ├── conexion_example.php  # Plantilla de conexión pública
│   ├── EnvLoader.php         # Carga de variables de entorno (.env)
│   ├── seguridad.php         # Funciones de seguridad centralizadas
│   └── .env                  # Variables de entorno (en .gitignore)
├── public/                   # Recursos públicos
│   └── js/
│       └── script.js
├── css/
├── img/                      # Imágenes del sistema (logo, firma, iconos)
├── logo/
├── uploads/                  # Imágenes subidas por usuarios (en .gitignore)
├── index.php                 # Front Controller / Router (Punto de entrada único)
├── logs/                     # Logs de errores PHP (en .gitignore)
├── BD.txt                    # Script SQL
├── deploy.sh                 # Script automático de despliegue en servidor Linux
├── .env.example
└── .gitignore
```

### Arquitectura MVC y SOLID

1. **Front Controller (`index.php`)**: Recibe todas las peticiones gracias a `.htaccess`. Lee los parámetros `?module=` y `?action=`.
2. **Controladores (`app/controllers/`)**: Contienen la lógica de negocio, delegando tareas específicas a los servicios.
3. **Servicios (`app/services/`)**: Clases especializadas con una única responsabilidad (SRP), como manejo de archivos.
4. **Modelos (`app/models/`)**: Encapsulan todas las consultas a la base de datos MySQL, implementando contratos estrictos (`app/contracts/`).
5. **Vistas (`app/views/`)**: Renderizan el HTML utilizando los datos proveídos por el controlador.

**Ejemplo de rutas**:
- Panel: `/SistemaSodicol/?module=panel`
- Lista de Usuarios: `/SistemaSodicol/?module=usuarios&action=lista`
- Crear Cotización: `/SistemaSodicol/?module=cotizaciones&action=crear`

### ¿Por qué esta estructura es importante en el VPS?
1. **Seguridad:** Todas las peticiones web externas apuntan a `index.php`. El código de negocio (`app/`) y las credenciales (`.env`) están aisladas y protegidas mediante reglas en el servidor web interno (Caddy) y por bloqueo de `.htaccess`.
2. **Persistencia (Volúmenes Docker):** Las carpetas `uploads/` y `logs/` se montan como volúmenes persistentes en `docker-compose.yml`. Al actualizar el sistema con `./deploy.sh`, **no se pierden las imágenes subidas por los usuarios ni los registros de errores**.
3. **Escalabilidad:** Separar el código frontal (`public/`) permite que el servidor web sirva los archivos CSS y JS de forma inmediata, sin cargar ni invocar el intérprete de PHP, mejorando radicalmente la velocidad.

---

## Seguridad implementada

| Medida | Estado |
|---|---|
| Contraseñas hasheadas con bcrypt | ✅ |
| Prepared statements (SQL injection) | ✅ Todos los módulos |
| Tokens CSRF en formularios | ✅ Todos los formularios |
| Validación de tipo MIME en uploads | ✅ |
| Nombres de archivo aleatorios | ✅ |
| Sanitización de entradas / `htmlspecialchars` en salidas | ✅ |
| Sesión con HttpOnly, timeout configurable, regeneración de ID | ✅ |
| Variables de entorno / Archivos ignorados | ✅ |
| Front controller y bloqueo .htaccess | ✅ |
| Verificación de dependencias al eliminar productos | ✅ |
| Transacción atómica en asignación de número de cotización | ✅ |
| Validación de longitud y formato de campos en servidor | ✅ |
| Límite estricto de longitud de campos (mb_substr) | ✅ Todos los controladores |
| Prevención de saturación (Rate Limiting) | ✅ En módulos críticos y acciones de escritura |
| Supresión de display_errors y log centralizado | ✅ |
| Cookie Secure configurable vía .env (COOKIE_SECURE) | ✅ |

---

## Solución de problemas

| Problema | Solución |
|---|---|
| Error de conexión a BD | Verifica `.env` y asegúrate de usar `DB_HOST=sodicol_db` |
| Error 502 Bad Gateway | El contenedor de la app no está corriendo, revisa `docker compose logs app` |
| Error al subir imágenes | Verifica permisos de `uploads/` (`chown -R www-data:www-data uploads`) |
| Sesión expira constantemente | Aumentar `SESSION_LIFETIME` en `.env` |
| PDF no genera | Ejecuta `docker exec -it sodicol_app composer install` para instalar DomPDF |

---

## Licencia

Uso interno — Sodicol Zomac S.A.S.
