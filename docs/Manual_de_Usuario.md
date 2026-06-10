# 📘 Manual de Usuario — Sistema SODICOL

> **Versión:** 1.0 · **Empresa:** Sodicol Zomac S.A.S · **Plataforma:** Web (PHP + MySQL)

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

---

## 1. Acceso al Sistema (Login)

1. Abre tu navegador e ingresa a la URL del sistema.
2. Verás una pantalla dividida en dos partes: el panel izquierdo informativo de SODICOL y el formulario de inicio de sesión a la derecha.
3. Ingresa tu **correo electrónico** y tu **contraseña** en los campos correspondientes.
4. Haz clic en el botón **Iniciar Sesión**.

> **Nota de seguridad:** Si ingresas una contraseña incorrecta o un correo no registrado, verás un mensaje de error en rojo. Tu sesión expirará automáticamente si permaneces inactivo. Consulta con tu administrador si olvidaste tu contraseña.

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
- Un botón **✔ Completo** para marcarla como finalizada al instante.

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

---

## 5. Módulo de Cotizaciones

### 5.1. Crear Cotización

Accede desde **menú 💲 > Crear Cotización**.

El proceso tiene dos fases: **añadir ítems** y luego **completar los datos del cliente para generar el PDF**.

#### Fase 1: Añadir productos a la cotización

La pantalla muestra tres secciones en la parte superior:

**A. Buscador de productos (filtrado automático)**
- Escribe en el campo de búsqueda para filtrar el listado de productos disponibles en el catálogo. El filtro se aplica automáticamente sin necesidad de presionar ningún botón.
- Cuando hay un filtro activo, aparece el botón **Limpiar** para resetear la búsqueda.

**B. Selector rápido "Usar producto"**
- Despliega todos los productos del catálogo en un menú seleccionable.
- Selecciona el producto deseado y haz clic en **Usar producto**. El formulario de abajo se autocompletará con los datos del producto (nombre, descripción, precio, IVA).
- Haz clic en **Limpiar** para restablecer el selector.

**C. Formulario del ítem**
- Puedes editar cualquier campo del producto autocompletado o llenar todo manualmente para un producto nuevo.
- Campos disponibles: Nombre del Producto, Foto (imagen), Descripción, Cantidad, Valor con IVA (Sí/No) y Precio Unitario.
- Haz clic en **Guardar Ítem** para agregar el producto a la lista de la cotización actual.

> **Registro automático:** Si el producto no existe en el catálogo, el sistema lo registrará automáticamente al guardar el ítem, para que puedas reutilizarlo en el futuro.

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

> **Numeración automática:** El sistema asigna automáticamente un número de cotización único y secuencial. No requiere ninguna acción por parte del usuario.

---

### 5.2. Consultar Cotizaciones

Accede desde **menú 💲 > Consultar Cotización**.

Muestra el historial completo de todas las cotizaciones registradas en el sistema (no solo las tuyas).

#### Filtros de búsqueda
Usa la barra superior para buscar por:
- 📅 **Fecha** (selector de calendario)
- 👤 **Nombre del cliente** (texto libre)
- 🔢 **Número de cotización** (texto libre)

Haz clic en **🔍 Buscar** para aplicar los filtros. Aparecerá el botón **🗑 Limpiar** si hay una búsqueda activa.

#### Tabla de resultados
Cada fila muestra: número de cotización, cliente, ciudad, fecha, y las acciones disponibles:

| Botón | Acción |
|---|---|
| 👁 Ver PDF | Abre el PDF de esa cotización en un visor dentro del sistema |
| ⬇ Descargar | Descarga directamente el archivo PDF al equipo |

---

## 6. Módulo de Productos (Catálogo)

Accede desde **menú 💲 > Lista de Productos**.

Muestra todos los productos que han sido registrados en el sistema (tanto los creados manualmente como los que se auto-registraron durante una cotización).

### Funciones disponibles

- **Búsqueda con filtrado automático:** Escribe en el campo de búsqueda y la tabla se filtrará automáticamente. El botón **Limpiar** aparece cuando hay un filtro activo.
- **Paginación:** La tabla muestra 10 productos por página. Usa los botones de navegación al final de la tabla para cambiar de página.
- ✏️ **Editar producto:** Modifica nombre, foto, descripción, cantidad, IVA y precio.
- 🗑️ **Eliminar producto:** El sistema pedirá confirmación antes de borrar.

> ⚠️ **Importante:** Si un producto ha sido utilizado en alguna cotización existente, el sistema **no permitirá eliminarlo** para proteger la integridad de los documentos. Verás un mensaje de advertencia indicando que el producto está en uso.

---

## 7. Módulo de Tareas

### Para empleados (rol Usuario)

Las tareas asignadas se visualizan directamente en el **Panel Principal**, en la columna derecha "Mis Tareas Pendientes".

- Cada tarea muestra la instrucción de trabajo.
- Haz clic en **✔ Completo** para marcar la tarea como finalizada. El panel se actualizará inmediatamente.

### Para administradores

Accede desde **menú 👤 > Tareas Usuarios**.

La pantalla está dividida en dos secciones:

**A. Crear nueva tarea** (panel derecho)
1. Escribe la **Descripción de la Tarea** (instrucción de trabajo, máximo 500 caracteres).
2. Selecciona el **Usuario** al que se le asignará.
3. Selecciona el **Estado inicial** (por defecto: Pendiente).
4. Haz clic en **➕ Crear Tarea**.

**B. Tabla de todas las tareas** (parte inferior)

Muestra todas las tareas del sistema con el usuario asignado, la descripción, y el estado actual:
- 🟡 **Pendiente**
- ✅ **Completo**

Acciones disponibles por fila:
- ✏️ **Editar** tarea (modifica descripción, usuario asignado o estado)
- 🗑️ **Eliminar** tarea (pide confirmación)

---

## 8. Módulo de Usuarios (Solo Administradores)

Accede desde **menú 👤 > Lista de Usuarios** o **menú 👤 > Nuevo Usuario**.

### Lista de Usuarios

- Muestra todos los empleados registrados con nombre, rol y estado.
- **Búsqueda automática:** filtra la tabla escribiendo en el buscador.
- **Paginación** para navegar entre registros.
- Acciones por fila: ✏️ Editar y 🗑️ Eliminar.

### Crear Nuevo Usuario

Accede desde **menú 👤 > Nuevo Usuario**. Completa el formulario:

| Campo | Descripción |
|---|---|
| Nombre completo | Nombre del empleado |
| Correo electrónico | Se usará para iniciar sesión |
| Contraseña | Se almacena de forma encriptada (bcrypt) |
| Rol | **admin** o **usuario** (ver sección 10) |
| Estado | Activo / Inactivo |

> **Contraseña inicial:** Por convención interna se suele usar el número de documento del empleado. Se recomienda que el usuario la cambie en su primera sesión, contactando al administrador para que realice la edición.

### Editar Usuario

Desde la lista, haz clic en el ícono ✏️ del usuario a modificar. Puedes actualizar todos sus datos, incluida la contraseña.

### Eliminar Usuario

Haz clic en 🗑️ y confirma la acción. El usuario será eliminado del sistema de forma permanente.

---

## 9. Cierre de Sesión

Cuando termines de trabajar, haz clic en el **ícono de salida 🚪** al final del menú lateral.

> El sistema también cerrará tu sesión automáticamente si permaneces inactivo durante el tiempo configurado por el administrador del servidor (por defecto: 1 hora).

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
| Ver lista de productos | ✅ | ✅ |
| Editar / Eliminar productos | ✅ | ✅ |
| Crear y gestionar usuarios | ✅ | ❌ |
| Crear / Editar / Eliminar tareas | ✅ | ❌ |
| Ver tareas de todos los usuarios | ✅ | ❌ |

---

*© 2026 Sodicol Zomac S.A.S — Documento de uso interno.*
