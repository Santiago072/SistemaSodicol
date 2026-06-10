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

### 3. Variables de entorno

```bash
cp .env.example config/.env
```

Editar `config/.env`:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=tu_contraseña
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
UPLOAD_MAX_SIZE=5242880
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp
```

### 4. Permisos

```bash
chmod 755 uploads/
```

### 5. Acceder

```
http://localhost/PROYECTO_SODICOL/
```

**Credenciales iniciales** (usuario creado en BD.txt):
- Correo: `admin@sodicol.com`
- Contraseña: `1234567890` (su documento — cambiar tras el primer acceso)

---

## Estructura del proyecto

```
PROYECTO_SODICOL/
├── app/
│   ├── controllers/
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
│       └── partials/
│           └── paginacion.php
├── config/
│   ├── conexion.php          # Carga .env y establece conexión mysqli
│   ├── seguridad.php         # Funciones de seguridad centralizadas
│   └── .env                  # Variables de entorno (no incluir en git)
├── cotizaciones/
├── css/
├── dompdf/                   # Librería PDF (instalada manualmente)
├── img/                      # Imágenes del sistema (logo, firma, iconos)
├── includes/
│   ├── menu.php
│   └── script.js
├── logo/
├── productos/
├── tareas/
├── uploads/                  # Imágenes subidas por usuarios
├── usuarios/
├── index.php                 # Login
├── panel.php                 # Dashboard
├── logout.php
├── BD.txt                    # Script SQL
├── .env.example
└── .gitignore
```

### Arquitectura MVC ligera

Los archivos en `usuarios/`, `productos/`, `tareas/` y `cotizaciones/` actúan como **entry points** que:
1. Inician sesión y conexión
2. Instancian el **Controller** correspondiente
3. Reciben un array de datos
4. Renderizan la **vista** (HTML + PHP mínimo)

Los **Models** encapsulan todas las queries SQL. Los **Controllers** contienen la lógica de negocio (validaciones, redirecciones, manejo de archivos).

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
| Variables de entorno para credenciales | ✅ |
| Control de acceso por rol en cada endpoint | ✅ |
| Protección contra eliminar último admin / auto-eliminación | ✅ |
| Verificación de dependencias al eliminar productos | ✅ |
| Transacción atómica en asignación de número de cotización | ✅ |

---

## Funciones disponibles en `config/seguridad.php`

```php
iniciar_sesion_segura()         // Inicia sesión con flags seguros y timeout
verificar_autenticacion()       // Redirige si no hay sesión activa
verificar_admin()               // Redirige si el rol no es admin
regenerar_sesion()              // Regenera session_id (tras login)
generar_token_csrf()            // Genera/retorna token CSRF de sesión
verificar_token_csrf($token)    // Valida token CSRF
sanitizar_entrada($data)        // trim + stripslashes + htmlspecialchars
validar_email($email)           // filter_var FILTER_VALIDATE_EMAIL
validar_numero($numero)         // is_numeric && > 0
validar_imagen($archivo)        // Extensión + MIME real + tamaño
generar_nombre_archivo($ext)    // time() + random_bytes → nombre único
```

---

## Roles

### Administrador
- CRUD completo de usuarios, productos y tareas
- Crear, consultar y generar PDF de cotizaciones
- Ver panel con contadores globales

### Usuario
- Crear cotizaciones y generar PDFs
- Consultar sus propias cotizaciones
- Ver y completar tareas asignadas

---

## Paginación

Las listas de **usuarios**, **productos**, **tareas** y **cotizaciones** muestran **10 registros por página**. La URL de paginación sigue el patrón:

```
lista_usuarios.php?pagina=2
lista_usuarios.php?busqueda=Juan&pagina=3
```

---

## Solución de problemas

| Problema | Solución |
|---|---|
| Error de conexión a BD | Verificar credenciales en `config/.env` y que MySQL esté corriendo |
| Error al subir imágenes | Verificar permisos de `uploads/` y `upload_max_filesize` en `php.ini` |
| Sesión expira constantemente | Aumentar `SESSION_LIFETIME` en `config/.env` |
| PDF no genera | Verificar que `dompdf/vendor/` esté presente (no se sube al repo) |

---

## Licencia

Uso interno — Sodicol Zomac S.A.S.
