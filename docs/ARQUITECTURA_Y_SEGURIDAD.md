# Arquitectura y Seguridad del Sistema Sodicol

Este documento describe detalladamente la arquitectura de software, los patrones de diseño implementados y las medidas de seguridad del Sistema Sodicol (Gestión Empresarial y Cotizaciones).

## 1. Arquitectura de Software

El sistema está construido bajo el patrón **Modelo-Vista-Controlador (MVC)**, garantizando una separación clara entre la lógica de negocio, la interacción con la base de datos y la interfaz de usuario.

### Estructura de Directorios (MVC y Capas)
- **`app/controllers/`**: Contiene los controladores que procesan las peticiones HTTP (GET/POST) (ej. `CotizacionController.php`, `UsuarioController.php`).
- **`app/models/`**: Contiene las clases que interactúan con la base de datos mediante sentencias preparadas (ej. `CotizacionModel.php`).
- **`app/services/`**: Contiene servicios especializados con responsabilidad única (SRP), como `FileUploadService.php` para la gestión de archivos.
- **`app/contracts/`**: Contiene interfaces que definen contratos formales para los repositorios (`RepositoryInterface.php`).
- **`app/views/`**: Contiene los archivos HTML/PHP de presentación organizados por módulos.
- **`config/`**: Archivos de configuración del sistema (`EnvLoader.php`, `conexion.php`, `seguridad.php`).

### Front Controller y Enrutamiento
Todas las solicitudes web pasan por un único punto de entrada: `index.php`. Este archivo actúa como **Front Controller** e implementa un mapa estricto de rutas.
- Las URLs mantienen el formato `?module=nombre_modulo&action=nombre_accion`.
- Las rutas no definidas explícitamente redirigen al login para evitar la enumeración de directorios.
- Utiliza la clase `EnvLoader` para cargar las variables del entorno `.env` en una etapa temprana del ciclo de vida de la aplicación.

## 2. Implementación de Principios SOLID

El sistema ha sido refactorizado para cumplir con estándares empresariales mediante los principios SOLID:

1. **[S] Single Responsibility Principle (SRP)**:
   - Se crearon servicios como `FileUploadService` cuya única responsabilidad es validar, subir y reemplazar archivos, liberando a los controladores de esa carga.
   - `EnvLoader.php` centraliza la carga de configuraciones, de manera que `conexion.php` solo se encarga de crear la conexión.

2. **[O] Open/Closed Principle (OCP)**:
   - El enrutador de `index.php` utiliza un mapa estático de módulos (`$rutasMap`). Para añadir un nuevo módulo al sistema, simplemente se añade una línea al array sin modificar la lógica interna de enrutamiento.

3. **[I] Interface Segregation Principle (ISP)**:
   - Los modelos que requieren un CRUD estándar (`UsuarioModel`, `ProductoModel`, `TareaModel`) implementan la interfaz `RepositoryInterface`.
   - Los modelos con lógicas complejas de múltiples tablas (como `CotizacionModel`, que maneja cabeceras e ítems) no se ven obligados a implementar métodos que no necesitan, previniendo los *Fat Interfaces*.

4. **[D] Dependency Inversion Principle (DIP)**:
   - Los controladores reciben sus dependencias (como la conexión `\mysqli`) a través del constructor, en lugar de instanciar la base de datos de manera rígida y global.

## 3. Seguridad Implementada

### Prevención de Inyección SQL (CWE-89)
- **Sentencias Preparadas**: Todas las consultas a la base de datos se ejecutan obligatoriamente mediante sentencias preparadas de `mysqli` (`mysqli_prepare`, `mysqli_stmt_bind_param`). Esto garantiza inmunidad total contra SQL Injection.
- **Tipado estricto**: Las variables pasadas a la base de datos son forzadas a su tipo correcto (`(int)`, `(float)`) antes del enlace de parámetros.

### Prevención de XSS (Cross-Site Scripting) (CWE-79)
- En todas las vistas, los datos provenientes de los usuarios o de la base de datos se renderizan utilizando la función especializada `escapar_salida()`, la cual implementa `htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- Esto neutraliza la inyección de etiquetas HTML maliciosas o scripts de Javascript directamente en la vista.

### Seguridad en Sesiones e Inicio de Sesión
- **Protección de Contraseñas**: Se emplea `password_hash()` con el algoritmo robusto BCRYPT. Su validación se hace con `password_verify()`.
- **Protección contra CSRF y Secuestro de Sesión**:
  - Las cookies de sesión se emiten con directivas `HttpOnly` y `SameSite=Strict`. Además, `Secure` puede activarse mediante `.env`.
  - El sistema regenera el ID de la sesión al hacer login para prevenir *Session Fixation*.
  - Se implementan tokens **CSRF rotativos** (`generar_token_csrf()` y `rotar_token_csrf()`), validándose con comparación de tiempo constante (`hash_equals()`) para prevenir ataques CSRF y *Timing Attacks*. El token se rota después del login y después de cada POST exitoso para prevenir *Replay Attacks*.

### Carga Segura de Archivos
- Las subidas de archivos en `FileUploadService` verifican no solo la extensión (`.jpg`, `.png`, etc.), sino el **tipo MIME real** empleando `finfo`.
- Los archivos se renombran usando una combinación de `time()` y 8 bytes aleatorios generados criptográficamente (`bin2hex(random_bytes(8))`), impidiendo la colisión de nombres y la enumeración de archivos en el servidor.

### Prevención de Enumeración
- Mensajes de error genéricos: El sistema retorna respuestas genéricas como "Correo o contraseña incorrectos" en el login para evitar revelar a los atacantes qué correos están registrados y cuáles no.

### Concurrencia y Bloqueos (Locks)
- Al finalizar una cotización y asignar su número único y consecutivo, `CotizacionModel` ejecuta la operación dentro de una transacción (`mysqli_begin_transaction`) e implementa el bloqueo exclusivo de tablas (`LOCK TABLES cotizaciones WRITE`), asegurando que en un entorno altamente concurrente no se generen números de cotización duplicados.
