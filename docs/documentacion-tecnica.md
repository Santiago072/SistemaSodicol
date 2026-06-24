# Documentación Técnica — Sistema Sodicol

**Versión:** 1.0  
**Tecnología:** PHP 7.4+ (MySQLi, MVC) · MySQL · Vanilla CSS · DomPDF

---

## Tabla de Contenidos

1. [Resumen del Sistema](#1-resumen-del-sistema)
2. [Arquitectura General](#2-arquitectura-general)
3. [Base de Datos](#3-base-de-datos)
4. [Módulos del Sistema](#4-módulos-del-sistema)
5. [Seguridad y Rendimiento](#5-seguridad-y-rendimiento)
6. [Generación de PDF](#6-generación-de-pdf)
7. [Pruebas Automatizadas](#7-pruebas-automatizadas)
8. [Instalación y Configuración](#8-instalación-y-configuración)

---

## 1. Resumen del Sistema

El **Sistema Sodicol** es una aplicación web de gestión interna diseñada para una empresa de mobiliario, que permite la administración ágil de productos, asignación de tareas a empleados y generación estandarizada de cotizaciones en PDF. 

Está diseñado para funcionar en entornos de desarrollo locales (XAMPP/WAMP) y en servidores de producción (Docker/VPS) mediante un sistema modular que facilita su mantenimiento a largo plazo.

---

## 2. Arquitectura General

El proyecto sigue una arquitectura **MVC (Modelo-Vista-Controlador)** con **Principios SOLID**, utilizando un Controlador Frontal. 

- Los **Controladores** coordinan el flujo HTTP y validan la entrada de datos.
- Los **Modelos** manejan exclusivamente los datos mediante **MySQLi** utilizando consultas preparadas. Se basan en un patrón Repository (`app/contracts/RepositoryInterface.php`).
- Los **Servicios** abstraen operaciones complejas o repetitivas, como la carga de archivos (`app/services/FileUploadService.php`).
- Las **Vistas** renderizan el HTML.

```text
Navegador ──► index.php?module=...&action=... 
                    │
                    ├── config/EnvLoader.php                  (Configuración)
                    ├── app/controllers/CotizacionController.php
                    ├── app/controllers/UsuarioController.php
                    │
                    ├── app/models/                           (Capa de Datos)
                    │        ├── UsuarioModel.php             (Implementa Repository)
                    │        ├── CotizacionModel.php          (Uso de Locks y Transacciones)
                    │        └── TareaModel.php
                    │
                    ├── app/services/                         (Capa de Servicios)
                    │        └── FileUploadService.php        (Subida segura de imágenes)
                    │
                    └── app/views/                            (UI / HTML puro)
```

**Patrón de URLs (Enrutamiento Frontal):**
```text
http://localhost/SistemaSodicol/                              → Login
http://localhost/SistemaSodicol/index.php?module=panel        → Dashboard
http://localhost/SistemaSodicol/index.php?module=cotizaciones&action=crear → Cotización
```

---

## 3. Base de Datos

**Nombre por defecto:** `sistema_sodicol`

### Tablas Principales

#### `usuarios`
Gestión de credenciales de acceso de los empleados y su información de contacto.
- Campos principales: `documento` (UK), `correo` (UK), `password` (bcrypt), `rol` (admin/usuario), `estado`.

#### `productos`
Inventario visual que facilita la creación de las cotizaciones sin tener que ingresar descripciones cada vez.
- Campos principales: `titulo`, `foto`, `descripcion`, `cantidad`, `precio`.

#### `cotizaciones` y `cotizacion_items`
Estructura de maestro-detalle para almacenar la cotización.
- `cotizaciones`: Almacena la cabecera (cliente, ciudad, fecha) y asigna un `numero_cotizacion` único y concurrente utilizando `LOCK TABLES`. Cuenta con índices (`INDEX`) para `numero_cotizacion`, `nombre_cliente` y `fecha_creacion` que optimizan la velocidad del buscador AJAX.
- `cotizacion_items`: Representa los renglones específicos de la cotización que posteriormente serán impresos en el PDF.

#### `tareas`
Sistema de ticketing simple interno para asignar instrucciones a los usuarios regulares.
- Campos principales: `usuario_id`, `descripcion_tarea`, `estado` (pendiente/completo).

---

## 4. Módulos del Sistema

### 4.1 Autenticación (`AuthController.php`)
Controla el inicio de sesión y la expiración de la sesión. Si se detecta un intento fallido, el sistema no revela si el correo existe o no, dificultando la enumeración de usuarios. Rota los tokens CSRF en cada sesión válida.

### 4.2 Panel (`PanelController.php`)
Genera el tablero de control de la aplicación calculando totales de cotizaciones, productos y usuarios, y proporcionando atajos a las tareas principales. También incluye endpoints AJAX para marcar las tareas como completadas.

### 4.3 Usuarios (`UsuarioController.php`)
CRUD completo y gestión de cuentas. La contraseña siempre es hasheada con BCRYPT. Un administrador no puede auto-eliminarse ni borrar al último administrador activo del sistema.

### 4.4 Productos (`ProductoController.php`)
Gestiona el catálogo base que se utiliza para generar cotizaciones. Verifica que la foto de un producto no se elimine del servidor si el producto todavía se encuentra asociado o referenciado dentro de un PDF/Cotización generada en el pasado. Su acceso está estrictamente limitado a usuarios con rol de `admin`.

### 4.5 Cotizaciones (`CotizacionController.php`)
Permite crear un borrador temporal asignado al usuario activo en la sesión. Incorpora un buscador AJAX de productos y permite subir imágenes personalizadas por ítem si el producto no se encuentra en el catálogo. Finaliza el proceso calculando el consecutivo seguro y llamando a la generación del PDF.

### 4.6 Tareas (`TareaController.php`)
Panel administrativo que permite crear, editar y eliminar instrucciones operativas dirigidas a otros usuarios de la plataforma.

---

## 5. Seguridad y Rendimiento

El sistema implementa sólidas garantías de seguridad y estabilidad en todo su flujo:
- Configuración centralizada de sesión desde `.env` (`COOKIE_SECURE`, `SESSION_LIFETIME`).
- Tokens CSRF implementados con `random_bytes()` y verificados a través de comparaciones `hash_equals()`.
- Rotación del token estructurada exclusivamente durante la autenticación y en acciones críticas post-login, permitiendo el uso multi-pestaña para búsquedas (Multi-Tab Browsing) sin perder sincronización.
- **Manejador Global de Excepciones**: Utiliza `set_exception_handler()` para interceptar todos los errores del backend sin crashear el proceso HTTP, devolviendo un JSON seguro y limpio al cliente.
- **Limpieza de Buffer (Output Buffering)**: Implementación de `@ob_clean()` antes de imprimir `json_encode()` en controladores AJAX para evitar que advertencias de PHP silenciosas o espacios en blanco corrompan las respuestas asíncronas.
- **Cache Busting**: Uso de versionamiento dinámico (`?v=time()`) en la inclusión de scripts del cliente (como `ajax_tables.js`) para asegurar que el navegador obtenga instantáneamente la última lógica asíncrona, evadiendo fallos por caché obsoleta.
- **Rate Limiting Nativo**: Restringe el límite de tráfico de los usuarios para módulos críticos (Ej: 15 peticiones por minuto en Autenticación, Búsquedas y PDFs) mediante `verificar_rate_limit()`, mitigando vulnerabilidades como DDoS a nivel de aplicación o scraping excesivo.

---

## 6. Generación de PDF

El sistema utiliza la biblioteca **DomPDF** instalada dinámicamente mediante el gestor de paquetes **Composer** (`dompdf/dompdf`).
- En entornos de producción (Docker), Composer se ejecuta automáticamente durante el ciclo de construcción (`docker compose up --build`).
- Permite renderizar cualquier estructura HTML compleja y convertirla en un archivo PDF descargable o previsualizable en el navegador.
- Carga las hojas de estilo de manera absoluta para asegurar la correcta presentación y posición de tablas, imágenes (logos y firmas) y tipografías.

---

## 7. Pruebas Automatizadas

El sistema cuenta con un marco de pruebas gestionado vía **PHPUnit**.  
El entorno y los tests se definen en `phpunit.xml` y garantizan la solidez del núcleo de seguridad y rendimiento de forma nativa.

- Ejecución de pruebas:
```bash
# Vía PHAR o Composer (./vendor/bin/phpunit)
php phpunit.phar tests/Unit/SeguridadTest.php
```

---

## 8. Instalación y Configuración

### 8.1 Variables de Entorno (Recomendado)
El sistema confía en el archivo `.env` en la raíz del proyecto para ocultar las variables críticas del repositorio público.
```env
DB_HOST=sodicol_db
DB_USER=sodicol
DB_PASS=tu_contraseña_segura
DB_NAME=sistema_sodicol
SESSION_LIFETIME=3600
COOKIE_SECURE=1
```

### 8.2 Despliegue en Producción (Docker Compose)
Se proporciona un script automatizado `deploy.sh` que se encarga de descargar la última versión y levantar los contenedores:
```bash
./deploy.sh
```
Automáticamente mapea el código PHP hacia el contenedor y sirve la aplicación en el puerto asignado (por defecto, `8891`).

### 8.3 Instalación Local (XAMPP / Laragon)
1. Clona el proyecto en tu carpeta raíz (`htdocs` o `www`).
2. Copia `.env.example` a `.env` en la carpeta `config/` y ajusta `DB_HOST=localhost`.
3. Instala dependencias: `composer install`.
4. Importa la base de datos `database/sistema_sodicol_con_datos_utf8.sql` en phpMyAdmin.
5. Abre `http://localhost/SistemaSodicol/`.

### 8.4 Credenciales Iniciales
```text
Correo: admin@sodicol.com
Contraseña: [REDACTED]
```
(El documento sirve como contraseña inicial hasta ser modificada por el administrador).
