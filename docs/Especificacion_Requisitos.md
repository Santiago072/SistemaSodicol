# Especificación de Requisitos: Sistema de Cotizaciones SODICOL

> **Empresa:** Sodicol Zomac S.A.S · **Plataforma:** Web (PHP + MySQL, Patrón MVC)

---

## 1. Resumen de la Problemática
La empresa Sodicol Zomac S.A.S realiza actualmente sus cotizaciones de forma manual, lo que genera errores frecuentes en el ingreso de precios y en los cálculos matemáticos (sumas y multiplicaciones). Se requiere un sistema que automatice estas operaciones internas manteniendo la estructura informativa de la empresa (precios, descripciones e imágenes).

---

## 2. Requisitos Funcionales (RF)

Estos requisitos definen las funciones específicas que el sistema debe ejecutar según la estructura de módulos propuesta:

- **RF01 - Gestión de Usuarios (CRUD)**: El sistema debe permitir al administrador crear, buscar, listar y gestionar cuentas de usuarios/empleados.
- **RF02 - Creación de Cotizaciones**: El sistema debe contar con un módulo para completar formularios de cotización ya sea de forma automática (reutilizando productos) o de forma manual, permitiendo guardar ítems de productos individualmente en una lista temporal en la cual se podrá eliminar y editar los ítems.
- **RF03 – Creación de Productos**: El sistema debe verificar en el módulo de crear cotización si el producto existe antes de añadirlo: (a) por clave primaria, el formulario se completa automáticamente al pulsar "Usar Producto"; (b) por nombre, el formulario se completa manualmente. En ambos casos, al pulsar "Guardar ítem" se valida la existencia del producto. Si no existe, debe registrarlo en la lista de productos y añadirlo simultáneamente a la cotización actual.
- **RF04 - Gestión de Catálogo General**: El sistema debe contar con un módulo de lista productos (inventario) que almacene todos los productos registrados, en el cual se incluyen ítems que no existan en la lista productos, esto es durante el proceso de cotización. Este módulo debe permitir realizar búsquedas, así como editar o eliminar productos de la base de datos para que la información se mantenga actualizada.
- **RF05 - Automatización de Cálculos**: El software debe realizar internamente todas las operaciones de suma y multiplicación de precios para garantizar la eficiencia y precisión.
- **RF06 - Generación de Documentos PDF**: Una vez completados los datos del cliente y los ítems, el sistema debe generar por medio de un botón un archivo PDF que contenga toda la información de la cotización.
- **RF07 - Consulta y Búsqueda**: El sistema debe permitir la búsqueda de cotizaciones realizadas filtrándolas en una tabla específicamente por fecha, nombre del cliente, número de cotización, para que al pulsar "Ver" observar el contenido de la cotización respectiva y descargarla nuevamente si es necesario.
- **RF08 - Resumen General**: El menú principal debe ofrecer un acceso a un resumen general de la actividad del sistema y también debe mostrar las tareas pendientes asignadas al usuario.
- **RF09 – Asignación de Tareas**: El sistema debe permitir al administrador crear instrucciones de trabajo (tareas) vinculadas a un usuario específico, almacenando una descripción textual de la labor y un estado inicial "pendiente".
- **RF10 – Control de Flujo de Tareas**: El sistema debe listar en el panel principal las tareas asignadas y permitirle cambiar su estado de "pendiente" a "completo" mediante una acción directa, refrescando la vista de manera inmediata.
- **RF11 – Gestión Administrativa de Tareas (CRUD)**: El administrador debe tener la facultad de listar todas las tareas del sistema, editar sus descripciones o responsables, y eliminar registros de tareas que ya no sean necesarios.

---

## 3. Requisitos No Funcionales (RNF)

Estos requisitos definen los atributos de calidad y seguridad del sistema:

- **RNF01 - Control de Acceso por Roles**: El sistema debe restringir las funciones según el tipo de cuenta; los empleados solo podrán crear cotizaciones, mientras que los administradores tendrán control total de todo.
- **RNF02 - Integridad de Datos**: El sistema debe asegurar que la estructura de la cotización se mantenga fiel al formato manejado por la empresa.
- **RNF03 - Persistencia de Información**: Todos los registros de usuarios y cotizaciones deben almacenarse en una base de datos centralizada para su consulta posterior.
- **RNF04 - Eficiencia Operativa y UX**: El sistema debe procesar las operaciones matemáticas de forma más rápida y exacta que el método manual anterior. Además, la interfaz debe estar optimizada con AJAX para evitar recargas de página completas durante búsquedas, eliminación de registros y paginación, brindando una experiencia más fluida (Live Search).
- **RNF05 - Seguridad de Autenticación**: Las contraseñas deben almacenarse hasheadas con bcrypt y la sesión debe protegerse con cookies HttpOnly, regeneración de ID, timeout configurable y tokens CSRF en todos los formularios.
- **RNF06 - Protección contra Inyección SQL**: Todas las consultas a la base de datos deben utilizar sentencias preparadas (prepared statements) para prevenir inyecciones SQL.
- **RNF07 - Validación de Datos**: El sistema debe validar la entrada de datos tanto en el cliente (HTML5) como en el servidor (formato, longitud, tipo), incluyendo documentos, correos, teléfonos y archivos de imagen.
- **RNF08 - Gestión de Errores**: Los errores de PHP no deben mostrarse al usuario en producción; deben registrarse en un archivo de log protegido.

---

## 4. Módulos del Sistema (Estructura)

De acuerdo con el diagrama de modularización, el sistema se divide en:

1. **Módulo de Acceso**: Inicio de sesión diferenciado para Admin y Usuario, con autenticación bcrypt y protección CSRF.
2. **Módulo de Usuarios**: Gestión y búsqueda de personal con paginación y validaciones de servidor.
3. **Módulo de Cotización**: Registro de ítems, datos del cliente y exportación a PDF con numeración atómica.
4. **Módulo de Productos**: Gestión de productos como mostrar, editar y eliminar ítems que se han utilizado en las distintas cotizaciones realizadas, con verificación de dependencias.
5. **Módulo de Tareas Pendientes**: Gestión de tareas asignadas por el administrador a empleados específicos, con seguimiento del estado de cada tarea (pendiente / completo) y visualización inmediata en el panel principal.
6. **Resumen General y Mostrar Tareas Asignadas**: Vista consolidada de la información en el panel principal con contadores animados.

---

## 5. Historias de Usuario

### HU-01: Inicio de Sesión
**Como** usuario del sistema (Administrador o Empleado), **Quiero** ingresar mis credenciales (correo y contraseña) en un módulo de login, **Para** acceder a las funcionalidades correspondientes a mi rol.

**Criterios de Aceptación:**
- El sistema debe validar si las credenciales existen en la base de datos con contraseña hasheada (bcrypt).
- Si las credenciales son incorrectas, debe mostrar un mensaje de error.
- Si es Administrador, debe redirigir al menú completo (Gestión de Usuarios y Cotizaciones).
- Si es Empleado, debe redirigir únicamente al menú de Cotizaciones.
- El formulario debe incluir un token CSRF para protección.
- La sesión debe expirar por inactividad según configuración.

### HU-02: Gestión de Cuentas de Empleados (CRUD)
**Como** Administrador, **Quiero** crear, buscar, listar y eliminar cuentas de usuarios, **Para** mantener el control de quién tiene acceso al sistema.

**Criterios de Aceptación:**
- El sistema debe permitir registrar nuevos usuarios con documento, nombre, correo, contraseña (opcional, por defecto el documento), teléfono y rol.
- El sistema debe validar en el servidor: formato de documento (numérico, 5-20 dígitos), longitud de nombre (3-100 caracteres), formato de correo, formato de teléfono (numérico, 7-20 dígitos), longitud mínima de contraseña (6 caracteres).
- El sistema debe permitir visualizar una lista paginada de todos los usuarios registrados sin recargar la página al cambiar de página.
- El sistema debe permitir buscar usuarios específicos por nombre en tiempo real (Live Search), actualizando la tabla automáticamente al escribir.
- Las eliminaciones de usuarios se deben procesar en segundo plano desapareciendo la fila afectada suavemente.
- Esta funcionalidad debe estar bloqueada para el rol de "Empleado".
- No debe permitirse eliminar al último administrador ni auto-eliminarse.

### HU-03: Creación de Nueva Cotización
**Como** Empleado o Administrador, **Quiero** registrar los ítems de una cotización (ya sea mediante búsqueda automática o ingreso manual) y los datos del cliente, **Para** generar un documento de cotización preciso y de manera eficiente.

**Criterios de Aceptación:**
- El sistema debe permitir buscar productos por nombre. Al escribir, se debe filtrar el select automáticamente mediante una petición AJAX (Live Search).
- Al seleccionar un producto y hacer clic en "Usar producto", los campos del formulario deben rellenarse automáticamente de forma asíncrona.
- El usuario debe poder escribir manualmente en los campos si el producto no existe.
- Al dar clic en "Guardar ítem", el producto se añade en una lista temporal de la cotización actual.
- Tras guardar un ítem exitosamente, los campos de entrada deben quedar vacíos para permitir la carga del siguiente producto.
- Cada ítem en la lista temporal debe tener opciones para ser editado o eliminado.
- El sistema debe confirmar que al pulsar "Cotización Lista" exista al menos un ítem.
- El sistema debe validar que el formulario de los datos del cliente esté completo antes de generar el PDF.

### HU-04: Automatización de Cálculos Matemáticos
**Como** Empleado o Administrador, **Quiero** que el sistema realice automáticamente las sumas y multiplicaciones de los precios e ítems, **Para** evitar errores en el cobro y agilizar el proceso.

**Criterios de Aceptación:**
- Al ingresar cantidad y precio unitario, el sistema debe realizar las operaciones internamente.
- El sistema debe calcular: subtotales por ítem, IVA (19%) cuando aplica, valor base total, valor IVA total y gran total.

### HU-05: Generación de Cotización en PDF
**Como** Empleado o Administrador, **Quiero** generar y descargar un archivo PDF con la información de la cotización, **Para** entregar al cliente un documento formal.

**Criterios de Aceptación:**
- El PDF se debe poder generar una vez se ingresen los datos del cliente.
- El documento debe incluir: datos del cliente, lista de productos, precios calculados, totales y fichas técnicas individuales.
- El formato debe respetar la estructura informativa de la empresa (logo, NIT, datos de contacto, firma).
- La numeración de cotizaciones debe ser atómica (con bloqueos de fila transaccionales `SELECT FOR UPDATE`) para evitar duplicados en concurrencia.

### HU-06: Búsqueda de Cotizaciones
**Como** Empleado o Administrador, **Quiero** buscar cotizaciones realizadas anteriormente filtrando por fecha, nombre del cliente o número de cotización, **Para** consultar precios o recuperar información.

**Criterios de Aceptación:**
- El sistema debe tener filtros por fecha, nombre del cliente y número de cotización.
- Si la cotización existe, debe mostrar los detalles en un visor PDF integrado.
- Si no existe, debe notificar que no se encontraron resultados.
- Debe permitir descargar el PDF nuevamente.
- Los resultados deben mostrarse paginados y la búsqueda debe ejecutarse en tiempo real sin recargar la página al cambiar los filtros.

### HU-07: Resumen General de Actividad
**Como** Administrador o Empleado, **Quiero** ver un resumen general de la actividad y las tareas pendientes en el panel principal, **Para** tener una visión rápida del movimiento del sistema.

**Criterios de Aceptación:**
- El menú principal debe mostrar contadores de administradores, usuarios y cotizaciones propias.
- Debe mostrar las tareas pendientes asignadas al usuario en sesión.

### HU-08: Creación de Productos
**Como** Empleado o Administrador, **Quiero** que el sistema valide la existencia de un producto al intentar guardarlo desde el formulario de cotización, **Para** evitar duplicados y permitir la reutilización ágil de ítems.

**Criterios de Aceptación:**
- Al hacer clic en "Guardar Ítem", el sistema debe verificar si el producto ya existe.
- Si el producto ya existe: se vincula desde el select "Usar producto" y se autocompleta la información.
- Si el producto no existe: el sistema lo registra automáticamente en la tabla productos y lo añade a la cotización.
- El ítem guardado debe aparecer inmediatamente en la tabla temporal.

### HU-09: Gestión de Productos
**Como** Administrador, **Quiero** disponer de un módulo de lista de productos para buscar, editar y eliminar productos, **Para** mantener actualizado el catálogo sin que usuarios sin autorización puedan alterar datos.

**Criterios de Aceptación:**
- El sistema debe listar automáticamente productos registrados desde cotizaciones.
- Debe permitir buscar productos por nombre con filtrado.
- El administrador debe poder modificar la información de productos o eliminarlos permanentemente (sin afectar las cotizaciones en las que ya se haya usado).
- Los cambios se reflejan inmediatamente en el select del módulo de cotizaciones.
- La lista debe ser paginada y la búsqueda de productos en la tabla se realiza en tiempo real (Live Search) sin recargar la página. La eliminación también es asíncrona.

### HU-10: Asignación de Labores Administrativas
**Como** Administrador, **Quiero** asignar instrucciones de cotización a empleados específicos, **Para** organizar la carga de trabajo del equipo.

**Criterios de Aceptación:**
- El administrador debe seleccionar un usuario activo de un listado desplegable.
- Debe existir un campo de texto obligatorio para la descripción de la tarea.
- Al guardar, la tarea debe quedar registrada con estado "pendiente" y mostrarse en la lista de gestión.

### HU-11: Visualización y Finalización de Pendientes
**Como** Empleado o Administrador, **Quiero** ver mis tareas pendientes en mi panel principal con un botón para marcarlas como completadas, **Para** saber exactamente qué debo procesar.

**Criterios de Aceptación:**
- Solo se deben visualizar las tareas que coincidan con el ID del usuario en sesión.
- Al hacer clic en "Completo", el sistema debe realizar un UPDATE del estado en la base de datos de manera asíncrona (AJAX).
- Una tarea marcada como "completo" debe desaparecer automáticamente de la vista de pendientes con una animación, sin recargar el panel.

### HU-12: Mantenimiento y Corrección de Tareas
**Como** Administrador, **Quiero** poder editar o eliminar las tareas asignadas, **Para** corregir errores o reasignar labores.

**Criterios de Aceptación:**
- El sistema debe cargar los datos actuales de la tarea en el formulario de edición.
- La eliminación de una tarea debe remover el registro permanentemente usando peticiones en segundo plano sin recargar toda la página.
- Tras cualquier modificación el sistema debe redirigir al listado general.

---

## 6. Medidas de Seguridad Implementadas

| Medida | Estado |
|---|---|
| Contraseñas hasheadas con bcrypt (`password_hash` / `password_verify`) | ✅ |
| Sentencias preparadas en todas las consultas SQL (prevención de SQL injection) | ✅ |
| Tokens CSRF en todos los formularios POST | ✅ |
| Sanitización de entradas con `htmlspecialchars` y funciones dedicadas | ✅ |
| Validación de tipo MIME real en uploads de imágenes | ✅ |
| Nombres de archivo aleatorios para uploads | ✅ |
| Sesión con HttpOnly cookies, timeout configurable, regeneración de ID | ✅ |
| Variables de entorno (archivo `.env` fuera del repositorio) | ✅ |
| Front controller único y bloqueo `.htaccess` de archivos sensibles | ✅ |
| Validación de longitud y formato de campos en servidor | ✅ |
| Supresión de `display_errors` en producción con log centralizado | ✅ |
| Cookie Secure configurable vía variable de entorno | ✅ |
| Transacción atómica con `SELECT FOR UPDATE` para numeración de cotizaciones | ✅ |
| Cabeceras de seguridad HTTP (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection) | ✅ |

---

## 7. Beneficios Esperados

- **Eliminación de Errores**: Erradicar los fallos en cálculos de precios y descripciones con operaciones automáticas.
- **Centralización**: Pasar de un manejo manual disperso a un control digital organizado con base de datos centralizada.
- **Profesionalización**: Entrega de cotizaciones estandarizadas en formato PDF con identidad corporativa.
- **Seguridad**: Protección de datos con autenticación robusta, prevención de inyección SQL y tokens CSRF.
- **Organización del Trabajo**: Gestión de tareas integrada para distribuir y dar seguimiento a las labores del equipo.
