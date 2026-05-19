import './bootstrap';
import 'bootstrap';

// Spinner global para formularios
document.addEventListener('DOMContentLoaded', function () {

    // Crear el spinner overlay
    const overlay = document.createElement('div');
    overlay.id = 'spinnerOverlay';
    overlay.innerHTML = `
        <div class="spinner-backdrop">
            <div class="spinner-box">
                <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-3 fw-semibold text-primary" id="spinnerMensaje">Procesando...</div>
            </div>
        </div>`;
    document.body.appendChild(overlay);

    // Interceptar todos los formularios
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.tagName !== 'FORM') return;

        // No mostrar en formularios de búsqueda o filtros (GET)
        if (form.method.toLowerCase() === 'get') return;

        // Determinar mensaje según el método
        const method = (form.querySelector('[name=_method]')?.value ?? form.method).toUpperCase();
        let mensaje = 'Procesando...';

        if (method === 'POST')   mensaje = 'Guardando datos...';
        if (method === 'PUT')    mensaje = 'Actualizando datos...';
        if (method === 'PATCH')  mensaje = 'Actualizando datos...';
        if (method === 'DELETE') mensaje = 'Eliminando registro...';

        // Buscar si el botón submit tiene un mensaje personalizado
        const btnSubmit = form.querySelector('[type=submit]');
        if (btnSubmit) {
            btnSubmit.disabled = true;
        }

        mostrarSpinner(mensaje);
    });

    // Interceptar confirmaciones de eliminación
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[type=submit]');
        if (!btn) return;

        const form = btn.closest('form');
        if (!form) return;

        const method = (form.querySelector('[name=_method]')?.value ?? '').toUpperCase();
        if (method === 'DELETE') {
            // El spinner se mostrará en el submit handler
            // Solo si el usuario confirma el confirm()
        }
    });
});

function mostrarSpinner(mensaje = 'Procesando...') {
    const overlay  = document.getElementById('spinnerOverlay');
    const mensajeEl = document.getElementById('spinnerMensaje');
    if (overlay)   overlay.classList.add('activo');
    if (mensajeEl) mensajeEl.textContent = mensaje;
}

function ocultarSpinner() {
    const overlay = document.getElementById('spinnerOverlay');
    if (overlay) overlay.classList.remove('activo');
}