{{-- Modal de confirmacion global (reemplaza a confirm() del navegador) --}}
<div class="modal fade" id="modalConfirmAccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-4">
                <div id="modalConfirmIcono" class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:2.5rem"></i>
                </div>
                <h5 class="fw-bold mb-2">Confirmar accion</h5>
                <p class="text-muted mb-4" id="modalConfirmMensaje"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" id="btnConfirmAccion" class="btn px-4">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Reemplazo de confirm() nativo por un modal de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl   = document.getElementById('modalConfirmAccion');
        const mensajeEl = document.getElementById('modalConfirmMensaje');
        const btnOk     = document.getElementById('btnConfirmAccion');
        const iconoEl   = document.getElementById('modalConfirmIcono');

        if (!modalEl || typeof bootstrap === 'undefined') return;

        const modal = new bootstrap.Modal(modalEl);
        let elementoPendiente = null;

        document.querySelectorAll('[onclick*="confirm("]').forEach(function(el) {
            const original = el.getAttribute('onclick') || '';
            const match = original.match(/confirm\((['"])([\s\S]*?)\1\)/);
            if (!match) return;

            const mensaje = match[2];
            el.removeAttribute('onclick');

            el.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                elementoPendiente = el;
                mensajeEl.textContent = mensaje;

                // Estilo segun el tipo de accion (eliminar/suspender = peligro, resto = primario)
                const esPeligrosa = /eliminar|borrar|suspender|vencid/i.test(mensaje);
                btnOk.className   = 'btn px-4 ' + (esPeligrosa ? 'btn-danger' : 'btn-primary');
                iconoEl.innerHTML = esPeligrosa
                    ? '<i class="bi bi-trash3-fill text-danger" style="font-size:2.5rem"></i>'
                    : '<i class="bi bi-question-circle-fill text-primary" style="font-size:2.5rem"></i>';

                modal.show();
            });
        });

        btnOk.addEventListener('click', function() {
            modal.hide();
            if (!elementoPendiente) return;

            if (elementoPendiente.tagName === 'A') {
                window.location.href = elementoPendiente.href;
            } else {
                const form = elementoPendiente.closest('form');
                if (form) form.submit();
            }
            elementoPendiente = null;
        });
    });
</script>
