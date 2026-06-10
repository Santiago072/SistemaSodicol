# Especificación de Requisitos: Sistema de Cotizaciones SODICOL

## 1. Resumen de la Problemática
La empresa Sodicol Zomac S.A.S realiza actualmente sus cotizaciones de forma manual, lo que genera errores frecuentes en el ingreso de precios y en los cálculos matemáticos (sumas y multiplicaciones). Se requiere un sistema que automatice estas operaciones internas manteniendo la estructura informativa de la empresa (precios, descripciones e imágenes).

## 2. Requisitos Funcionales (RF)
Estos requisitos definen las funciones específicas que el sistema debe ejecutar según la estructura de módulos propuesta:

- **RF01 - Gestión de Usuarios (CRUD)**: El sistema debe permitir al administrador crear, buscar, listar y gestionar cuentas de usuarios/empleados.
- **RF02 - Creación de Cotizaciones**: El sistema debe contar con un módulo para completar formularios de cotización ya sea de forma automática (reutilizando productos) o de forma manual, permitiendo guardar ítems de productos individualmente en una lista temporal en la cual se podrá eliminar y editar los ítems.
- **RF03 – Creación de Productos**: El sistema debe verificar en el módulo de crear cotización si el producto existe antes de añadirlo: (a) por clave primaria, el formulario se completa automáticamente al pulsar “Usar Producto”; (b) por nombre, el formulario se completa manualmente. En ambos casos, al pulsar “Guardar ítem” se valida la existencia del producto. Si no existe, debe registrarlo en la lista de productos y añadirlo simultáneamente a la cotización actual.
- **RF04 - Gestión de Catálogo General**: El sistema debe contar con un módulo de lista productos(inventario) que almacene todos los productos registrados, en el cual se incluyen ítems que no existan en la lista productos, esto es durante el proceso de cotización. Este módulo debe permitir realizar búsquedas, así como editar o eliminar productos de la base de datos para que la información se mantenga actualizada.
- **RF05 - Automatización de Cálculos**: El software debe realizar internamente todas las operaciones de suma y multiplicación de precios para garantizar la eficiencia y precisión.
- **RF06 - Generación de Documentos PDF**: Una vez completados los datos del cliente y los ítems, el sistema debe generar por medio de un botón un archivo PDF que contenga toda la información de la cotización.
- **RF07 - Consulta y Búsqueda**: El sistema debe permitir la búsqueda de cotizaciones realizadas filtrándolas en una tabla específicamente por fecha, nombre del cliente, numero de cotización, para que al pulsar “Ver” observar el contenido de la cotización respectiva y descargarla nuevamente si es necesario.
- **RF08 - Resumen General**: El menú principal debe ofrecer un acceso a un resumen general de la actividad del sistema y también debe mostrar las tareas pendientes asignadas al usuario.
- **RF09 – Asignación de Tareas**: El sistema debe permitir al administrador crear instrucciones de trabajo (tareas) vinculadas a un usuario específico, almacenando una descripción textual de la labor y un estado inicial "pendiente".
- **RF10 – Control de Flujo de Tareas**: El sistema debe listar en el panel principal las tareas asignadas y permitirle cambiar su estado de "pendiente" a "completo" mediante una acción directa, refrescando la vista de manera inmediata.
- **RF11 – Gestión Administrativa de Tareas (CRUD)**: El administrador debe tener la facultad de listar todas las tareas del sistema, editar sus descripciones o responsables, y eliminar registros de tareas que ya no sean necesarios.

## 3. Requisitos No Funcionales (RNF)
Estos requisitos definen los atributos de calidad y seguridad del sistema:

- **RNF01 - Control de Acceso por Roles**: El sistema debe restringir las funciones según el tipo de cuenta; los empleados solo podrán crear cotizaciones, mientras que los administradores tendrán control total de todo.
- **RNF02 - Integridad de Datos**: El sistema debe asegurar que la estructura de la cotización se mantenga fiel al formato manejado por la empresa.
- **RNF03 - Persistencia de Información**: Todos los registros de usuarios y cotizaciones deben almacenarse en una base de datos centralizada para su consulta posterior.
- **RNF04 - Eficiencia Operativa**: El sistema debe procesar las operaciones matemáticas de forma más rápida y exacta que el método manual anterior.

## 4. Módulos del Sistema (Estructura)
De acuerdo con el diagrama de modularización, el sistema se divide en:

1. **Módulo de Acceso**: Inicio de sesión diferenciado para Admin y Usuario.
2. **Módulo de Usuarios**: Gestión y búsqueda de personal.
3. **Módulo de Cotización**: Registro de ítems, datos del cliente y exportación a PDF.
4. **Módulo de Productos**: Gestión de productos como mostrar, editar y eliminar ítems que se han utilizado en las distintas cotizaciones realizadas.
5. **Módulo de Tareas Pendientes**: Gestión de tareas asignadas por el administrador a empleados específicos, con seguimiento del estado de cada tarea (pendiente / completo) y visualización inmediata en el panel principal.
6. **Resumen General y Mostrar Tareas Asignadas**: Vista consolidada de la información en el panel principal.

## 5. Beneficios Esperados
- **Eliminación de Errores**: Erradicar los fallos en cálculos de precios y descripciones.
- **Centralización**: Pasar de un manejo manual disperso a un control digital organizado.
- **Profesionalización**: Entrega de cotizaciones estandarizadas en formato PDF de manera eficiente.
