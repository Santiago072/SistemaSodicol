/**
 * ajax_tables.js
 * Intercepta búsquedas, paginación y eliminaciones en las tablas
 * para procesarlas por AJAX (usando pjax/fetch) y evitar recargas completas.
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Manejar Eliminaciones Asíncronas
    function bindEliminarAjax() {
        const botonesEliminar = document.querySelectorAll('.boton-eliminar');
        
        botonesEliminar.forEach(btn => {
            // Prevenir múltiples bind si se re-renderiza
            if (btn.dataset.ajaxBound) return;
            btn.dataset.ajaxBound = "true";

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const msg = this.getAttribute('data-confirm') || '¿Está seguro?';
                if (!confirm(msg)) {
                    return;
                }

                const url = this.getAttribute('href') + '&ajax=1';
                const fila = this.closest('tr') || this.closest('.card-item');
                
                // Mostrar estado de carga (opcional)
                this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                this.style.pointerEvents = 'none';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text())
                .then(text => {
                    let res;
                    try {
                        res = JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON:', text);
                        alert('Respuesta inválida del servidor: ' + text.substring(0, 100));
                        this.innerHTML = '<i class="fas fa-trash"></i>';
                        this.style.pointerEvents = 'auto';
                        return;
                    }

                    if (res.status === 'success') {
                        if (fila) {
                            fila.style.transition = 'all 0.4s ease';
                            fila.style.opacity = '0';
                            setTimeout(() => fila.remove(), 400);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(res.message || 'Error al eliminar el registro');
                        this.innerHTML = '<i class="fas fa-trash"></i>';
                        this.style.pointerEvents = 'auto';
                    }
                })
                .catch(err => {
                    console.error('JS Error in fetch chain:', err);
                    alert('Error en el navegador (JS): ' + err.message);
                    this.innerHTML = '<i class="fas fa-trash"></i>';
                    this.style.pointerEvents = 'auto';
                });
            });
        });
    }

    // 2. Manejar Búsquedas Asíncronas (PJAX style)
    function bindBusquedaAjax() {
        const formulario = document.querySelector('.formulario-busqueda');
        if (!formulario) return;

        if (formulario.id === 'form-busqueda-ajax') return; // Excluir el del módulo de cotización que tiene su propia lógica JSON

        if (formulario.dataset.ajaxBound) return;
        formulario.dataset.ajaxBound = "true";

        formulario.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const method = (this.getAttribute('method') || 'GET').toUpperCase();
            
            let url = this.getAttribute('action') || window.location.href;
            let fetchOptions = {};

            if (method === 'GET') {
                const params = new URLSearchParams(formData).toString();
                url = url + (url.includes('?') ? '&' : '?') + params;
            } else {
                fetchOptions = {
                    method: 'POST',
                    body: formData
                };
            }

            cargarTablaAsincrona(url, fetchOptions);
        });

        // Live search (búsqueda en vivo asíncrona)
        const inputs = formulario.querySelectorAll('input[type="text"], input[type="search"]');
        let timeoutBusqueda = null;
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timeoutBusqueda);
                timeoutBusqueda = setTimeout(() => {
                    // Generar evento cancelable para que el preventDefault() del submit ajax lo pueda interceptar
                    formulario.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }, 500);
            });
        });
    }

    // 3. Manejar Paginación Asíncrona (PJAX style)
    function bindPaginacionAjax() {
        const enlacesPaginacion = document.querySelectorAll('.paginacion a, .formulario-busqueda .boton-limpiar');
        
        enlacesPaginacion.forEach(enlace => {
            if (enlace.dataset.ajaxBound || !enlace.getAttribute('href') || enlace.getAttribute('href') === '#') return;
            enlace.dataset.ajaxBound = "true";

            enlace.addEventListener('click', function(e) {
                e.preventDefault();
                cargarTablaAsincrona(this.getAttribute('href'));
            });
        });
    }

    // Función core: Carga la URL por fetch, extrae el HTML de la tabla y lo reemplaza
    function cargarTablaAsincrona(url, options = {}) {
        const contenedorTabla = document.querySelector('.tabla-contenedor') || document.querySelector('.grid-cards');
        const paginacionDiv = document.querySelector('.paginacion');
        const pagInfoDiv = document.querySelector('.pag-info');

        if (!contenedorTabla) {
            // Si no hay contenedor asíncrono soportado, ir directamente a la URL
            window.location.href = url;
            return;
        }

        // Estado visual de carga
        contenedorTabla.style.opacity = '0.5';

        fetch(url, options)
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Reemplazar tabla/grid
            const nuevaTabla = doc.querySelector('.tabla-contenedor') || doc.querySelector('.grid-cards');
            if (nuevaTabla) {
                contenedorTabla.innerHTML = nuevaTabla.innerHTML;
            }

            // Reemplazar paginación
            const nuevaPaginacion = doc.querySelector('.paginacion');
            if (paginacionDiv) {
                if (nuevaPaginacion) paginacionDiv.innerHTML = nuevaPaginacion.innerHTML;
                else paginacionDiv.innerHTML = ''; // Si ya no hay paginación
            } else if (nuevaPaginacion) {
                // Si no existía paginación pero ahora sí, insertarla después de la tabla
                contenedorTabla.insertAdjacentHTML('afterend', nuevaPaginacion.outerHTML);
            }

            // Reemplazar texto de info paginación
            const nuevaPagInfo = doc.querySelector('.pag-info');
            if (pagInfoDiv) {
                if (nuevaPagInfo) pagInfoDiv.outerHTML = nuevaPagInfo.outerHTML;
                else pagInfoDiv.remove();
            } else if (nuevaPagInfo) {
                const nav = document.querySelector('.paginacion');
                if (nav) nav.insertAdjacentHTML('afterend', nuevaPagInfo.outerHTML);
            }

            // Actualizar URL en el navegador
            window.history.pushState({}, '', url);

            contenedorTabla.style.opacity = '1';
            bindEliminarAjax();
            
            // Update 'Limpiar' button dynamically without touching the input to prevent focus loss
            const nuevoFormBusqueda = doc.querySelector('.formulario-busqueda');
            const formActual = document.querySelector('.formulario-busqueda');
            if (nuevoFormBusqueda && formActual) {
                const viejoLimpiar = formActual.querySelector('.boton-limpiar');
                const nuevoLimpiar = nuevoFormBusqueda.querySelector('.boton-limpiar');
                
                if (nuevoLimpiar && !viejoLimpiar) {
                    formActual.appendChild(nuevoLimpiar);
                } else if (!nuevoLimpiar && viejoLimpiar) {
                    viejoLimpiar.remove();
                } else if (nuevoLimpiar && viejoLimpiar) {
                    viejoLimpiar.outerHTML = nuevoLimpiar.outerHTML;
                }
            }

            // Bind events for pagination and the newly added Limpiar button
            bindPaginacionAjax();
        })
        .catch(err => {
            console.error('Error cargando la tabla AJAX:', err);
            contenedorTabla.style.opacity = '1';
        });
    }

    // Inicializar todo
    bindEliminarAjax();
    bindBusquedaAjax();
    bindPaginacionAjax();

    // Manejar botón "Atrás/Adelante" del navegador
    window.addEventListener('popstate', () => {
        cargarTablaAsincrona(window.location.href);
    });

});
