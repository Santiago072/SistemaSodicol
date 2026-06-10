# Manual de Usuario: Sistema SODICOL

Bienvenido al manual de uso del Sistema de Gestión de Cotizaciones y Tareas de SODICOL. Este documento te guiará paso a paso para utilizar correctamente todas las funciones de la plataforma.

---

## 1. Acceso al Sistema (Login)
1. **Ingreso**: Abre tu navegador y navega a la URL del sistema. Verás la pantalla de inicio de sesión.
2. **Credenciales**: Ingresa tu correo electrónico registrado y tu contraseña. 
   > **Nota:** Si es tu primera vez accediendo, tu contraseña por defecto será tu número de documento. Se recomienda cambiarla posteriormente.
3. **Modos de Visualización**: Puedes cambiar entre "Modo Día" (claro) y "Modo Noche" (oscuro) haciendo clic en el botón circular ubicado en la esquina superior derecha de la pantalla.

---

## 2. Panel Principal (Dashboard)
Una vez dentro, llegarás al panel principal. Desde aquí puedes:
- **Ver un Resumen Estadístico**: Observa tarjetas con el total de usuarios, productos, cotizaciones generadas y tus tareas pendientes.
- **Navegar por el Menú Lateral**: A la izquierda encontrarás los accesos a los distintos módulos (Usuarios, Cotizaciones, etc.). Para ocultar o mostrar el texto del menú y ganar espacio en pantalla, usa el botón con ícono de hamburguesa en la parte inferior.
- **Ver tus Tareas Asignadas**: A la derecha (o debajo en pantallas pequeñas), verás un listado de las tareas que te han sido encomendadas por la administración.

---

## 3. Módulo de Cotizaciones
El corazón del sistema. Aquí puedes registrar y generar los PDFs para los clientes.

### 3.1. Crear Cotización
1. En el menú, dirígete a **Cotizaciones > Crear Cotización**.
2. **Datos del Cliente**: Rellena la información principal (Nombre, NIT/CC, Ciudad, etc.).
3. **Añadir Productos (Ítems)**:
   - Ingresa el nombre del producto. Si ya existe en la base de datos, el sistema te sugerirá usarlo y autocompletará el precio.
   - Si es un producto nuevo, simplemente rellena la descripción, cantidad y precio. Al guardar el ítem, el sistema lo agregará automáticamente a tu catálogo para futuras cotizaciones.
4. **Finalizar**: Tras agregar todos los ítems, haz clic en el botón de **Generar Cotización y PDF**. Se abrirá una ventana con el documento listo para imprimir o enviar al cliente.

### 3.2. Consultar Cotizaciones
1. Ve a **Cotizaciones > Consultar Cotización**.
2. Encontrarás el historial completo de todas las cotizaciones creadas.
3. **Búsqueda Automática**: Puedes filtrar por fecha, nombre de cliente o número de cotización usando la barra superior. Los resultados se actualizarán automáticamente apenas dejes de escribir.
4. Haz clic en el botón con ícono de documento en la columna de acciones para visualizar y/o volver a descargar el PDF de una cotización anterior.

---

## 4. Módulo de Productos (Inventario/Catálogo)
Todos los ítems que se utilizan en las cotizaciones se almacenan aquí.

1. Ve a **Cotizaciones > Lista de Productos**.
2. Visualizarás la tabla con todos los productos registrados, paginada para facilitar la navegación.
3. Puedes utilizar el buscador para encontrar un producto específico.
4. **Editar/Eliminar**: Puedes editar el precio y la descripción de los productos. 
   > **Aviso de Seguridad:** El sistema no te permitirá eliminar un producto si ya ha sido utilizado en alguna cotización previa, esto con el fin de proteger la integridad de los documentos antiguos.

---

## 5. Módulo de Tareas
Este módulo mejora la comunicación y seguimiento de actividades dentro de la empresa.

### Para Usuarios (Empleados)
- Al entrar al **Panel Principal**, revisa tu lista de "Mis Tareas".
- Cuando finalices una actividad, haz clic en el botón **Completar** al lado de la tarea. Su estado pasará a estar terminado e informará a la administración.

### Para Administradores
1. Ve a **Usuarios > Tareas Usuarios**.
2. **Asignar Nueva Tarea**: Selecciona un empleado de la lista desplegable, escribe la instrucción y haz clic en "Crear Tarea".
3. **Gestión Total**: Verás una lista con todas las tareas de la empresa. Puedes editarlas, reasignarlas a otra persona o eliminarlas usando los botones de acción en la tabla.

---

## 6. Módulo de Usuarios (Exclusivo Administradores)
Si tienes cuenta de Administrador, puedes gestionar el personal del sistema.

1. Ve a **Usuarios > Lista de Usuarios** para ver y buscar a todos los empleados.
2. Ve a **Usuarios > Nuevo Usuario** para dar de alta a un trabajador. Debes asignarle un rol:
   - **Administrador**: Control total sobre el sistema, creación de usuarios y gestión de tareas de terceros.
   - **Usuario**: Solo podrá crear/consultar cotizaciones y marcar como completadas sus propias tareas.
3. En la lista, puedes editar los datos de cualquier empleado (incluyendo cambios de contraseña) o eliminar su cuenta si ya no labora en la empresa.

---

## 7. Cierre de Sesión
Por políticas de seguridad, cuando termines de utilizar el sistema, ubica la opción **Cerrar sesión** (ícono de salida) al final del menú lateral.
Adicionalmente, el sistema cerrará tu sesión automáticamente por seguridad si permaneces inactivo durante mucho tiempo.
