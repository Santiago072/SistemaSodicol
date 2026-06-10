# Sistema Sodicol — Sistema de Gestión Empresarial

Sistema web de gestión interno para **Sodicol Zomac S.A.S**, empresa de diseño y mobiliario. Desarrollado en PHP nativo con MySQL, sin frameworks externos.

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
http://localhost/PROYECTO_SODICOL/
```

**Credenciales iniciales** (usuario creado en BD.txt):
- Correo: `juanperez@gmail.com`
- Contraseña: `1119469827` (su documento — cambiar tras el primer acceso)

---

## Estructura del proyecto (Patrón MVC)

El proyecto utiliza un patrón MVC completo con un único punto de entrada (Front Controller).

```text
PROYECTO_SODICOL/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── PanelController.php
│   │   ├── UsuarioController.php
│   │   ├── ProductoController.php
│   │   ├── TareaController.php
│   │   └── CotizacionController.php
│   ├── models/
│   │   ├── Database.php
│   │   ├── UsuarioModel.php
│   │   ├── ProductoModel.php
│   │   ├── TareaModel.php
│   │   └── CotizacionModel.php
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
│   ├── conexion.php          # (Opcional si usas .env) Carga .env y establece conexión
│   ├── conexion_example.php  # Plantilla de conexión pública
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
├── .htaccess                 # Routing hacia index.php y bloqueos de seguridad
├── BD.txt                    # Script SQL
├── .env.example
└── .gitignore
```

### Arquitectura MVC

1. **Front Controller (`index.php`)**: Recibe todas las peticiones gracias a `.htaccess`. Lee los parámetros `?module=` y `?action=`.
2. **Controladores (`app/controllers/`)**: Contienen la lógica de negocio (validaciones, redirecciones, manejo de archivos).
3. **Modelos (`app/models/`)**: Encapsulan todas las consultas a la base de datos MySQL (con sentencias preparadas).
4. **Vistas (`app/views/`)**: Renderizan el HTML utilizando los datos proveídos por el controlador.

**Ejemplo de rutas**:
- Panel: `/PROYECTO_SODICOL/?module=panel`
- Lista de Usuarios: `/PROYECTO_SODICOL/?module=usuarios&action=lista`
- Crear Cotización: `/PROYECTO_SODICOL/?module=cotizaciones&action=crear`

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
