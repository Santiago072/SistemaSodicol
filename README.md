# Sistema Sodicol — Sistema de Gestión Empresarial

Sistema web de gestión interno para **Sodicol Zomac S.A.S**, empresa de diseño y mobiliario. Desarrollado en PHP nativo con MySQL, sin frameworks externos.

---

## 📚 Documentación y Manuales
- [Especificación de Requisitos](docs/Especificacion_Requisitos.md)
- [Documentación Técnica](docs/documentacion-tecnica.md)
- [Manual de Usuario](docs/Manual_de_Usuario.md)
- [Arquitectura y Seguridad](docs/ARQUITECTURA_Y_SEGURIDAD.md)

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

- PHP 7.4 o superior (`mysqli`, `gd`, `mbstring`, `fileinfo`)
- MySQL 5.7 o superior
- Servidor web Apache/Nginx (XAMPP en desarrollo)
- mod_rewrite habilitado (para el routing MVC)

---

## Instalación

### 1. Clonar

```bash
git clone https://github.com/Santiago072/SistemaSodicol.git
```

### 2. Base de datos

```bash
mysql -u root -p < BD.txt
```

### 3. Variables de entorno / Conexión

Si usas entorno de producción, copia `.env.example` a `config/.env`:

```bash
cp .env.example config/.env
```

Y edita `config/.env`:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=tu_contraseña
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
COOKIE_SECURE=0
UPLOAD_MAX_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp
```

Si estás en desarrollo y prefieres configurar directamente PHP, copia `config/conexion_example.php` a `config/conexion.php` y edita tus credenciales dentro de `conexion.php`.

### 4. Permisos

```bash
chmod 755 uploads/
```

### 5. Acceder

```
http://localhost/SistemaSodicol/
```

**Credenciales iniciales** (usuario creado en BD.txt):
- Correo: `admin@sodicol.com`
- Contraseña: `1234567890` (su documento — cambiar tras el primer acceso)

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
├── .htaccess                 # Routing hacia index.php y bloqueos de seguridad
├── BD.txt                    # Script SQL
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
| Supresión de display_errors y log centralizado | ✅ |
| Cookie Secure configurable vía .env (COOKIE_SECURE) | ✅ |

---

## Solución de problemas

| Problema | Solución |
|---|---|
| Error de conexión a BD | Verifica `config/.env` o `config/conexion.php` y que MySQL esté corriendo |
| Errores "File not found" al navegar | Asegúrate de que Apache tiene `mod_rewrite` activo para `.htaccess` |
| Error al subir imágenes | Verifica permisos de `uploads/` y `upload_max_filesize` en `php.ini` |
| Sesión expira constantemente | Aumentar `SESSION_LIFETIME` en `config/.env` |
| PDF no genera | Verifica que `dompdf/vendor/` esté presente (instálalo manual si falta) |

---

## Licencia

Uso interno — Sodicol Zomac S.A.S.
