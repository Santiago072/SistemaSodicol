# 📘 Manual de Usuario — Sistema SODICOL

> **Empresa:** Sodicol Zomac S.A.S · **Plataforma:** Web (PHP + MySQL)

Este documento describe paso a paso cómo utilizar el Sistema de Gestión de Cotizaciones de SODICOL. Está dirigido a los empleados y administradores de la empresa.

---

## Índice

1. [Acceso al Sistema (Login)](#1-acceso-al-sistema-login)
2. [Cambio de Tema Visual](#2-cambio-de-tema-visual)
3. [Panel Principal (Dashboard)](#3-panel-principal-dashboard)
4. [Menú de Navegación](#4-menú-de-navegación)
5. [Módulo de Cotizaciones](#5-módulo-de-cotizaciones)
   - [5.1. Crear Cotización](#51-crear-cotización)
   - [5.2. Consultar Cotizaciones](#52-consultar-cotizaciones)
6. [Módulo de Productos (Catálogo)](#6-módulo-de-productos-catálogo)
7. [Módulo de Tareas](#7-módulo-de-tareas)
8. [Módulo de Usuarios (Solo Administradores)](#8-módulo-de-usuarios-solo-administradores)
9. [Cierre de Sesión](#9-cierre-de-sesión)
10. [Roles y Permisos](#10-roles-y-permisos)
11. [Seguridad del Sistema](#11-seguridad-del-sistema)

---

## 1. Acceso al Sistema (Login)

1. Abre tu navegador e ingresa a la URL del sistema.
2. Verás una pantalla dividida en dos partes: el panel izquierdo informativo de SODICOL y el formulario de inicio de sesión a la derecha.
3. Ingresa tu **correo electrónico** y tu **contraseña** en los campos correspondientes.
4. Haz clic en el botón **Iniciar Sesión**.

> **Nota de seguridad:** Si ingresas una contraseña incorrecta o un correo no registrado, verás un mensaje de error en rojo. Tu sesión expirará automáticamente si permaneces inactivo (por defecto: 1 hora). Consulta con tu administrador si olvidaste tu contraseña.

> **Contraseña inicial:** La primera vez que accedas, tu contraseña será la que el administrador haya configurado (por defecto, tu número de documento). Se recomienda solicitar al administrador que la cambie después del primer acceso.

---

## 2. Cambio de Tema Visual

El sistema dispone de dos modos de visualización:

- 🌙 **Modo Noche** (oscuro, activado por defecto): fondo oscuro con acentos en tonos dorados.
- ☀️ **Modo Día** (claro): fondo en tonos cálidos y beige.

Para cambiar el tema, haz clic en el **botón circular** ubicado en la **esquina superior derecha** de cualquier pantalla del sistema. El sistema recordará tu preferencia entre sesiones.

---

## 3. Panel Principal (Dashboard)

Al iniciar sesión accedes al panel principal. Está dividido en dos columnas:

### Columna izquierda — Tarjetas de resumen
Muestra contadores animados con la actividad del sistema:

| Tarjeta | Qué muestra |
|---|---|
| **Administradores** | Número total de cuentas de tipo Administrador |
| **Usuarios** | Número total de cuentas de tipo Usuario/Empleado |
| **Mis Cotizaciones** | Total de cotizaciones registradas por ti |

### Columna derecha — Mis Tareas Pendientes
Muestra las tareas de trabajo que el administrador te ha asignado. Si tienes tareas, cada una aparecerá con:
- La descripción de la instrucción.
- Un botón **✔ Completo** para marcarla como finalizada al instante. La tarea desaparecerá de tu lista de pendientes inmediatamente, con una suave animación y sin recargar la página.

Si no tienes tareas asignadas, verás el mensaje *"No tienes tareas pendientes actualmente."*

---

## 4. Menú de Navegación

El menú se encuentra en la **barra lateral izquierda** de la pantalla. Está compuesto por íconos verticales:

| Ícono | Función |
|---|---|
| 🏠 Casa | Ir al Panel Principal |
| 👤 Persona *(solo Admin)* | Submenú de Usuarios y Tareas |
| 💲 Dólar | Submenú de Cotizaciones y Productos |
| 🚪 Salida | Cerrar sesión |

Al pasar el cursor por los íconos de submenú aparece un **panel flotante** con las opciones disponibles. El panel desaparece automáticamente al mover el cursor fuera de él.

### Submenú de Usuarios (solo Admin)
- **Lista de Usuarios**: Ver y gestionar todos los usuarios.
- **Nuevo Usuario**: Crear una nueva cuenta.
- **Tareas Usuarios**: Asignar y gestionar tareas.

### Submenú de Cotizaciones
- **Crear Cotización**: Iniciar una nueva cotización.
- **Consultar Cotización**: Buscar cotizaciones existentes.
- **Lista de Productos**: Ver y gestionar el catálogo *(solo Admin)*.

---

## 5. Módulo de Cotizaciones

### 5.1. Crear Cotización

Accede desde **menú 💲 > Crear Cotización**.

El proceso tiene dos fases: **añadir ítems** y luego **completar los datos del cliente para generar el PDF**.

#### Fase 1: Añadir productos a la cotización

La pantalla muestra tres secciones en la parte superior:

**A. Buscador de productos (filtrado)**
- Escribe en el campo de búsqueda y el sistema filtrará automáticamente los productos disponibles en el catálogo a medida que tecleas (*live search*).
- Cuando hay un filtro activo, aparece el botón **Limpiar** para vaciar el campo de texto y ver todos los productos nuevamente.

**B. Selector rápido "Usar producto"**
- Despliega todos los productos del catálogo en un menú seleccionable.
- Selecciona el producto deseado y haz clic en **Usar producto**. El formulario de abajo se autocompletará con los datos del producto (nombre, descripción, precio, IVA, foto).
- Haz clic en **Limpiar** para restablecer el selector.

**C. Formulario del ítem**
- Puedes editar cualquier campo del producto autocompletado o llenar todo manualmente para un producto nuevo.
- Campos disponibles:
  - **Nombre del Producto** *(obligatorio, máx. 100 caracteres)*
  - **Foto del Producto** *(opcional, formatos: JPG, PNG, GIF, WEBP, máx. 5MB)*
  - **Descripción** *(obligatorio, máx. 1000 caracteres)*
  - **Cantidad** *(obligatorio, mínimo 1)*
  - **Valor con IVA** *(obligatorio, Sí/No)*
  - **Precio Unitario** *(obligatorio, mínimo 0)*
- Haz clic en **Guardar Ítem** para agregar el producto a la lista de la cotización actual.

> **Registro automático:** Si el producto no existe en el catálogo, el sistema lo registrará automáticamente al guardar el ítem, para que puedas reutilizarlo en el futuro.

> **Validación de imágenes:** El sistema verifica que la imagen sea realmente una imagen válida (tipo MIME), que no exceda 5MB y que tenga extensión permitida. Los archivos se guardan con nombres aleatorios por seguridad.

#### Tabla de ítems
Debajo del formulario se muestra la lista de ítems agregados a la cotización actual. Desde aquí puedes:
- ✏️ **Editar** un ítem (ícono de lápiz)
- 🗑️ **Eliminar** un ítem (ícono de basurero, pide confirmación)

#### Fase 2: Generar el PDF

Cuando hayas añadido todos los ítems:

1. Haz clic en el botón dorado **Cotización Lista**.
   > Si no has añadido al menos un ítem, el sistema te avisará con una alerta.
2. Se abrirá un **modal (ventana flotante)** con el formulario **Datos del Cliente**:
   - Profesión
   - Nombre del Cliente
   - Especialidad
   - Entidad
   - Ciudad
   - Fecha de Cotización
3. Completa todos los campos y haz clic en **Generar PDF**.
4. El PDF se abrirá en una nueva pestaña del navegador, listo para imprimir o descargar.

> **Numeración automática:** El sistema asigna automáticamente un número de cotización único y secuencial. No requiere ninguna acción por parte del usuario. La numeración utiliza transacciones seguras para garantizar que no se dupliquen números incluso con múltiples usuarios simultáneos.

#### Contenido del PDF generado

El documento PDF incluye:
- **Página 1**: Encabezado corporativo (logo, NIT), datos del cliente, tabla resumen con todos los ítems (cantidad, valor unitario, IVA, valor total), totales calculados (valor base, IVA total, gran total), datos de contacto y firma de representante legal.
- **Páginas siguientes**: Fichas técnicas individuales por cada ítem, con imagen del producto, descripción detallada y desglose de precios con IVA incluido.

---

### 5.2. Consultar Cotizaciones

Accede desde **menú 💲 > Consultar Cotización**.

Muestra el historial de cotizaciones registradas en el sistema.

#### Filtros de búsqueda
Usa la barra superior para buscar por:
- 📅 **Fecha** (selector de calendario)
- 👤 **Nombre del cliente** (texto libre)
- 🔢 **Número de cotización** (texto libre)

Puedes combinar múltiples filtros. La tabla de resultados **se actualizará en tiempo real** mientras cambias la fecha o escribes en los campos, sin necesidad de presionar ningún botón. Aparecerá el botón **🗑 Limpiar** si hay una búsqueda activa.

#### Tabla de resultados
Cada fila muestra: número de cotización, fecha, nombre del cliente, entidad, ciudad, y las acciones disponibles:

| Botón | Acción |
|---|---|
| 👁 **Ver** | Abre el PDF de esa cotización en un visor integrado dentro del sistema |
| ⬇ **Descargar** | Descarga directamente el archivo PDF al equipo |

> El visor PDF se abre dentro del sistema sin necesidad de salir. Puedes cerrarlo con el botón ✕ o presionando la tecla **Escape**.

Los resultados se muestran **paginados** (10 por página) con botones de navegación. Cambiar de página se hace al instante mediante tecnología asíncrona (no recarga todo el sitio).

---

## 6. Módulo de Productos (Solo Administradores)

Accede desde **menú 💲 > Lista de Productos**.

Muestra todos los productos que han sido registrados en el sistema (tanto los creados manualmente como los que se auto-registraron durante una cotización).

### Funciones disponibles

- **Búsqueda:** Escribe en el campo de búsqueda y la tabla se filtrará instantáneamente por nombre de producto (*live search*). El botón **Limpiar** aparece cuando hay un filtro activo.
- **Paginación:** La tabla muestra 10 productos por página. Usa los botones de navegación al final de la tabla para cambiar de página de forma rápida y sin recargas.
- ✏️ **Editar producto:** Modifica nombre, foto, descripción, cantidad, IVA y precio. Se valida que no exista otro producto con el mismo nombre.
- 🗑️ **Eliminar producto:** El sistema pedirá confirmación antes de borrar y lo eliminará de la vista en tiempo real si tiene éxito.

> ⚠️ **Importante:** Puedes eliminar cualquier producto de tu catálogo. Las cotizaciones anteriores que ya hayan incluido este producto no se verán afectadas, mantendrán la copia exacta de los datos (e imagen) que tenían al momento de ser creadas.

---

## 7. Módulo de Tareas

### Para empleados (rol Usuario)

Las tareas asignadas se visualizan directamente en el **Panel Principal**, en la columna derecha "Mis Tareas Pendientes".

- Cada tarea muestra la instrucción de trabajo.
- Haz clic en **✔ Completo** para marcar la tarea como finalizada. La tarea desaparecerá inmediatamente de tu panel y el estado se actualizará en la base de datos.

> Solo puedes ver y completar tus propias tareas. No tienes acceso a tareas de otros usuarios.

### Para administradores

Accede desde **menú 👤 > Tareas Usuarios**.

La pantalla está dividida en dos secciones:

**A. Crear nueva tarea** (panel superior)
1. Escribe la **Descripción de la Tarea** (instrucción de trabajo, máximo 500 caracteres).
2. Selecciona el **Usuario** al que se le asignará del listado de usuarios activos.
3. Selecciona el **Estado inicial** (por defecto: Pendiente).
4. Haz clic en **➕ Crear Tarea**.
5. Puedes usar el botón **✕ Limpiar** para reiniciar el formulario.

**B. Tabla de todas las tareas** (parte inferior)

Muestra todas las tareas del sistema con paginación. Cada fila incluye:
- Nombre del usuario asignado
- Descripción de la tarea
- Estado: 🟡 **Pendiente** o ✅ **Completo**

Acciones disponibles por fila:
- ✏️ **Editar** tarea (modifica descripción, usuario asignado o estado)
- 🗑️ **Eliminar** tarea (pide confirmación)

> La eliminación es permanente y no se puede deshacer.

---

## 8. Módulo de Usuarios (Solo Administradores)

Accede desde **menú 👤 > Lista de Usuarios** o **menú 👤 > Nuevo Usuario**.

### Lista de Usuarios

- Muestra todos los empleados registrados con nombre, documento, correo, teléfono, rol y estado.
- **Búsqueda:** Filtra la tabla escribiendo el nombre del usuario (búsqueda automática sin botón).
- **Paginación** asíncrona de 10 registros por página.
- Acciones por fila: ✏️ Editar y 🗑️ Eliminar (en segundo plano).

### Crear Nuevo Usuario

Accede desde **menú 👤 > Nuevo Usuario**. Completa el formulario:

| Campo | Descripción | Validación del servidor |
|---|---|---|
| **Documento** | Número de identificación del empleado | Numérico, 5-20 dígitos, único |
| **Nombre completo** | Nombre del empleado | 3-100 caracteres |
| **Correo electrónico** | Se usará para iniciar sesión | Formato de email válido, máx. 100 caracteres, único |
| **Contraseña** | Se almacena encriptada (bcrypt) | Mínimo 6 caracteres (opcional: si no se proporciona, se usa el documento) |
| **Teléfono** | Número de contacto | Numérico, 7-20 dígitos |
| **Rol** | Tipo de cuenta | `admin` o `usuario` |

> **Contraseña por defecto:** Si no se especifica una contraseña, el sistema usa el número de documento como contraseña temporal hasheada. Se recomienda que el administrador la actualice lo antes posible.

> **Duplicados:** El sistema no permite registrar usuarios con un documento o correo que ya exista en la base de datos.

### Editar Usuario

Desde la lista, haz clic en el ícono ✏️ del usuario a modificar. Puedes actualizar todos sus datos, incluida la contraseña (el campo de nueva contraseña es opcional) y el estado (activo/inactivo).

Las mismas validaciones del formulario de creación aplican al formulario de edición.

### Eliminar Usuario

Haz clic en 🗑️ y confirma la acción. El usuario será eliminado del sistema de forma permanente.

> ⚠️ **Restricciones de seguridad:**
> - No se puede eliminar al **último administrador** del sistema.
> - No se puede eliminar tu **propia cuenta** mientras estás en sesión.

---

## 9. Cierre de Sesión

Cuando termines de trabajar, haz clic en el **ícono de salida 🚪** al final del menú lateral.

> El sistema también cerrará tu sesión automáticamente si permaneces inactivo durante el tiempo configurado por el administrador del servidor (por defecto: 1 hora). Verás un mensaje informándote que la sesión expiró cuando intentes acceder nuevamente.

---

## 10. Roles y Permisos

El sistema tiene dos tipos de cuenta con accesos diferenciados:

| Función | Administrador | Usuario |
|---|---|---|
| Iniciar sesión | ✅ | ✅ |
| Ver Panel Principal | ✅ | ✅ |
| Ver y marcar propias tareas | ✅ | ✅ |
| Crear cotizaciones | ✅ | ✅ |
| Consultar cotizaciones | ✅ | ✅ |
| Ver lista de productos | ✅ | ❌ |
| Editar / Eliminar productos | ✅ | ❌ |
| Crear y gestionar usuarios | ✅ | ❌ |
| Crear / Editar / Eliminar tareas | ✅ | ❌ |
| Ver tareas de todos los usuarios | ✅ | ❌ |

---

## 11. Seguridad del Sistema

El sistema implementa múltiples capas de seguridad para proteger los datos:

- **Contraseñas seguras:** Las contraseñas nunca se almacenan en texto plano; se hashean con el algoritmo bcrypt.
- **Protección contra inyección SQL:** Todas las consultas a la base de datos usan sentencias preparadas.
- **Tokens CSRF:** Cada formulario incluye un token de seguridad para prevenir ataques de falsificación de peticiones.
- **Validación de archivos:** Las imágenes subidas se verifican por extensión, tamaño y tipo MIME real. Se guardan con nombres aleatorios.
- **Sesión segura:** Las cookies de sesión tienen la bandera HttpOnly, se regenera el ID de sesión al iniciar sesión, y la sesión expira automáticamente por inactividad.
- **Validación de datos:** Los datos se validan tanto en el navegador como en el servidor, incluyendo formato de documento, longitud de campos, formato de correo y teléfono.
- **Manejo de errores:** Los errores técnicos se registran en un archivo de log protegido y no se muestran al usuario.

---

*© 2026 Sodicol Zomac S.A.S — Documento de uso interno.*
