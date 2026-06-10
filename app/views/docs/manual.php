<?php
/**
 * Vista: Manual de Usuario / Documentación
 */
$pageTitle = 'Manual de Usuario';
$basePath  = '/PROYECTO_SODICOL/';
include dirname(__DIR__) . '/layout/header.php';
include dirname(__DIR__) . '/layout/menu.php';
?>

<div class="contenido-principal">
    <?php $pageHeading = 'Manual y Documentación';
    include dirname(__DIR__) . '/layout/topbar.php'; ?>

    <div class="encabezado-pagina">
        <h1>Especificación de Requisitos: Sistema de Cotizaciones SODICOL</h1>
    </div>

    <div class="formulario-contenedor" style="max-width: 900px; margin: 0 auto; padding: 30px; line-height: 1.6;">
        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--gold); border-bottom: 2px solid var(--gold-light); padding-bottom: 10px; margin-bottom: 15px;">
                1. Resumen de la Problemática
            </h2>
            <p>
                La empresa Sodicol Zomac S.A.S realiza actualmente sus cotizaciones de forma manual, lo que genera errores frecuentes en el ingreso de precios y en los cálculos matemáticos (sumas y multiplicaciones). Se requiere un sistema que automatice estas operaciones internas manteniendo la estructura informativa de la empresa (precios, descripciones e imágenes).
            </p>
        </section>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--gold); border-bottom: 2px solid var(--gold-light); padding-bottom: 10px; margin-bottom: 15px;">
                2. Requisitos Funcionales (RF)
            </h2>
            <p>Estos requisitos definen las funciones específicas que el sistema debe ejecutar según la estructura de módulos propuesta:</p>
            <ul style="list-style-type: none; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF01 - Gestión de Usuarios (CRUD):</strong> El sistema debe permitir al administrador crear, buscar, listar y gestionar cuentas de usuarios/empleados.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF02 - Creación de Cotizaciones:</strong> El sistema debe contar con un módulo para completar formularios de cotización ya sea de forma automática (reutilizando productos) o de forma manual, permitiendo guardar ítems de productos individualmente en una lista temporal en la cual se podrá eliminar y editar los ítems.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF03 – Creación de Productos:</strong> El sistema debe verificar en el módulo de crear cotización si el producto existe antes de añadirlo: (a) por clave primaria, el formulario se completa automáticamente al pulsar “Usar Producto”; (b) por nombre, el formulario se completa manualmente. En ambos casos, al pulsar “Guardar ítem” se valida la existencia del producto. Si no existe, debe registrarlo en la lista de productos y añadirlo simultáneamente a la cotización actual.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF04 - Gestión de Catálogo General:</strong> El sistema debe contar con un módulo de lista productos (inventario) que almacene todos los productos registrados, en el cual se incluyen ítems que no existan en la lista productos, esto es durante el proceso de cotización. Este módulo debe permitir realizar búsquedas, así como editar o eliminar productos de la base de datos para que la información se mantenga actualizada.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF05 - Automatización de Cálculos:</strong> El software debe realizar internamente todas las operaciones de suma y multiplicación de precios para garantizar la eficiencia y precisión.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF06 - Generación de Documentos PDF:</strong> Una vez completados los datos del cliente y los ítems, el sistema debe generar por medio de un botón un archivo PDF que contenga toda la información de la cotización.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF07 - Consulta y Búsqueda:</strong> El sistema debe permitir la búsqueda de cotizaciones realizadas filtrándolas en una tabla específicamente por fecha, nombre del cliente, numero de cotización, para que al pulsar “Ver” observar el contenido de la cotización respectiva y descargarla nuevamente si es necesario.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF08 - Resumen General:</strong> El menú principal debe ofrecer un acceso a un resumen general de la actividad del sistema y también debe mostrar las tareas pendientes asignadas al usuario.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF09 – Asignación de Tareas:</strong> El sistema debe permitir al administrador crear instrucciones de trabajo (tareas) vinculadas a un usuario específico, almacenando una descripción textual de la labor y un estado inicial "pendiente".</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF10 – Control de Flujo de Tareas:</strong> El sistema debe listar en el panel principal las tareas asignadas y permitirle cambiar su estado de "pendiente" a "completo" mediante una acción directa, refrescando la vista de manera inmediata.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-check-circle" style="color: var(--gold);"></i> RF11 – Gestión Administrativa de Tareas (CRUD):</strong> El administrador debe tener la facultad de listar todas las tareas del sistema, editar sus descripciones o responsables, y eliminar registros de tareas que ya no sean necesarios.</li>
            </ul>
        </section>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--gold); border-bottom: 2px solid var(--gold-light); padding-bottom: 10px; margin-bottom: 15px;">
                3. Requisitos No Funcionales (RNF)
            </h2>
            <p>Estos requisitos definen los atributos de calidad y seguridad del sistema:</p>
            <ul style="list-style-type: none; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-shield-check" style="color: #3498db;"></i> RNF01 - Control de Acceso por Roles:</strong> El sistema debe restringir las funciones según el tipo de cuenta; los empleados solo podrán crear cotizaciones, mientras que los administradores tendrán control total de todo.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-shield-check" style="color: #3498db;"></i> RNF02 - Integridad de Datos:</strong> El sistema debe asegurar que la estructura de la cotización se mantenga fiel al formato manejado por la empresa.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-shield-check" style="color: #3498db;"></i> RNF03 - Persistencia de Información:</strong> Todos los registros de usuarios y cotizaciones deben almacenarse en una base de datos centralizada para su consulta posterior.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-shield-check" style="color: #3498db;"></i> RNF04 - Eficiencia Operativa:</strong> El sistema debe procesar las operaciones matemáticas de forma más rápida y exacta que el método manual anterior.</li>
            </ul>
        </section>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--gold); border-bottom: 2px solid var(--gold-light); padding-bottom: 10px; margin-bottom: 15px;">
                4. Módulos del Sistema (Estructura)
            </h2>
            <p>De acuerdo con el diagrama de modularización, el sistema se divide en:</p>
            <ol style="padding-left: 20px;">
                <li style="margin-bottom: 5px;"><strong>Módulo de Acceso:</strong> Inicio de sesión diferenciado para Admin y Usuario.</li>
                <li style="margin-bottom: 5px;"><strong>Módulo de Usuarios:</strong> Gestión y búsqueda de personal.</li>
                <li style="margin-bottom: 5px;"><strong>Módulo de Cotización:</strong> Registro de ítems, datos del cliente y exportación a PDF.</li>
                <li style="margin-bottom: 5px;"><strong>Módulo de Productos:</strong> Gestión de productos como mostrar, editar y eliminar ítems que se han utilizado en las distintas cotizaciones realizadas.</li>
                <li style="margin-bottom: 5px;"><strong>Módulo de Tareas Pendientes:</strong> Gestión de tareas asignadas por el administrador a empleados específicos, con seguimiento del estado de cada tarea (pendiente / completo) y visualización inmediata en el panel principal.</li>
                <li style="margin-bottom: 5px;"><strong>Resumen General y Mostrar Tareas Asignadas:</strong> Vista consolidada de la información en el panel principal.</li>
            </ol>
        </section>

        <section>
            <h2 style="color: var(--gold); border-bottom: 2px solid var(--gold-light); padding-bottom: 10px; margin-bottom: 15px;">
                5. Beneficios Esperados
            </h2>
            <ul style="list-style-type: none; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-graph-up-arrow" style="color: #2ecc71;"></i> Eliminación de Errores:</strong> Erradicar los fallos en cálculos de precios y descripciones.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-graph-up-arrow" style="color: #2ecc71;"></i> Centralización:</strong> Pasar de un manejo manual disperso a un control digital organizado.</li>
                <li style="margin-bottom: 10px;"><strong><i class="bi bi-graph-up-arrow" style="color: #2ecc71;"></i> Profesionalización:</strong> Entrega de cotizaciones estandarizadas en formato PDF de manera eficiente.</li>
            </ul>
        </section>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>
