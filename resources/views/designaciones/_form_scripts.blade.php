<script>
(function () {
    const radios          = document.querySelectorAll('input[name="tipohorario"]');
    const bloqueUnificado = document.getElementById('bloqueUnificado');
    const bloqueDividido  = document.getElementById('bloqueDividido');
    const diasemana       = document.getElementById('diasemana_unificado');
    const horaentrada     = document.getElementById('horaentrada_unificado');
    const horasalida      = document.getElementById('horasalida_unificado');
    const tbody           = document.getElementById('filasHorarioDividido');
    const btnAgregar      = document.getElementById('btnAgregarFilaHorario');

    const DIAS = @json(\App\Models\Designacion::DIAS);

    function aplicarModo() {
        const modo = document.querySelector('input[name="tipohorario"]:checked')?.value || 'unificado';

        if (modo === 'dividido') {
            bloqueUnificado.classList.add('d-none');
            bloqueDividido.classList.remove('d-none');

            diasemana.required   = false;
            horaentrada.required = false;
            horasalida.required  = false;

            document.querySelectorAll('#filasHorarioDividido select, #filasHorarioDividido input[type="time"]')
                .forEach(el => el.required = true);
        } else {
            bloqueUnificado.classList.remove('d-none');
            bloqueDividido.classList.add('d-none');

            diasemana.required   = true;
            horaentrada.required = true;
            horasalida.required  = true;

            document.querySelectorAll('#filasHorarioDividido select, #filasHorarioDividido input[type="time"]')
                .forEach(el => el.required = false);
        }
    }

    radios.forEach(r => r.addEventListener('change', aplicarModo));
    aplicarModo();

    function reindexarFilas() {
        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            tr.querySelectorAll('select, input').forEach(el => {
                el.name = el.name.replace(/horarios\[\d+\]/, `horarios[${idx}]`);
            });
        });
    }

    function nuevaFila() {
        const idx = tbody.querySelectorAll('tr').length;
        let opcionesDias = '<option value="">Seleccioná...</option>';
        Object.entries(DIAS).forEach(([val, label]) => {
            opcionesDias += `<option value="${val}">${label}</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="horarios[${idx}][dia]" class="form-select form-select-sm">
                    ${opcionesDias}
                </select>
            </td>
            <td>
                <input type="text" name="horarios[${idx}][cantmodulos]" class="form-control form-control-sm" placeholder="Ej: 4">
            </td>
            <td>
                <input type="time" name="horarios[${idx}][horaentrada]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="time" name="horarios[${idx}][horasalida]" class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-fila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
        aplicarModo();
    }

    btnAgregar.addEventListener('click', nuevaFila);

    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-quitar-fila');
        if (!btn) return;

        if (tbody.querySelectorAll('tr').length > 1) {
            btn.closest('tr').remove();
            reindexarFilas();
        } else {
            // No dejar la tabla vacia, solo limpiar valores
            btn.closest('tr').querySelectorAll('select, input').forEach(el => el.value = '');
        }
    });
})();
</script>
