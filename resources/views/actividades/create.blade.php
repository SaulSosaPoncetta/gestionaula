@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-clipboard2-plus me-2"></i>Nueva actividad</h4>
        <p class="text-muted"><strong>{{ $materia->nombre }}</strong></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('actividades.seleccionar') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('actividades.store') }}" id="formActividad">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-info-circle me-1"></i>Datos de la actividad
        </div>
        <div class="card-body">
            <div class="row g-3">

                {{-- Numero de unidad --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        N° de unidad <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="numerounidad"
                           class="form-control @error('numerounidad') is-invalid @enderror"
                           value="{{ old('numerounidad') }}" min="1" required
                           placeholder="Ej: 1">
                    @error('numerounidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Tema (desde contenidos) --}}
                <div class="col-md-7">
                    <label class="form-label fw-semibold">
                        Tema <span class="text-danger">*</span>
                    </label>
                    <select name="tema" id="selectTema" class="form-select" required>
                        <option value="">— Selecciona un tema —</option>
                        @foreach($contenidos as $cont)
                            <option value="{{ $cont->tema }}"
                                    data-subtemas="{{ $cont->subtemas->pluck('subtema')->toJson() }}"
                                {{ old('tema') == $cont->tema ? 'selected' : '' }}>
                                @if($cont->numerounidad) [Unidad {{ $cont->numerounidad }}] @endif
                                {{ $cont->tema }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de actividad --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Tipo de actividad <span class="text-danger">*</span>
                    </label>
                    <select name="tipoactividad_id" class="form-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach($tiposactividad as $tipo)
                            <option value="{{ $tipo->id }}"
                                {{ old('tipoactividad_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->denominacion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Numero de actividad --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° de actividad</label>
                    <input type="number" name="numeroactividad"
                           class="form-control"
                           value="{{ old('numeroactividad') }}" min="1"
                           placeholder="Opcional">
                </div>

                {{-- Subtema --}}
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Subtema</label>
                    <select name="subtema" id="selectSubtema" class="form-select" disabled>
                        <option value="">— Selecciona primero un tema —</option>
                    </select>
                </div>

                {{-- Observaciones --}}
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        Observaciones <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <input type="text" name="descripcion" class="form-control"
                           value="{{ old('descripcion') }}"
                           placeholder="Observaciones opcionales...">
                </div>

            </div>
        </div>
    </div>

    {{-- Consignas / Items --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-list-ol me-1"></i>Consignas / Items
        </div>
        <div class="card-body">

            {{-- Tabla de items --}}
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle mb-0" id="tablaItems">
                    <thead class="table-light">
                        <tr>
                            <th style="width:100px" class="text-center">N° Item</th>
                            <th>Consigna / Pregunta</th>
                            <th style="width:60px" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoItems">
                        <tr id="filaVacia">
                            <td colspan="3" class="text-center text-muted py-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Presiona "Agregar consigna" para agregar items.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" id="btnAgregarItem">
                    <i class="bi bi-plus-circle me-1"></i>Agregar consigna
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Guardar actividad
                </button>
                <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>

        </div>
    </div>

</form>
@endsection

@push('scripts')
<script>
let contadorItems = 0;

// Selector de tema -> habilita subtemas
document.getElementById('selectTema').addEventListener('change', function () {
    const opt      = this.options[this.selectedIndex];
    const subtemas = opt.dataset.subtemas ? JSON.parse(opt.dataset.subtemas) : [];
    const sel      = document.getElementById('selectSubtema');

    sel.innerHTML = '<option value="">— Sin subtema —</option>';
    subtemas.forEach(s => {
        sel.innerHTML += `<option value="${s}">${s}</option>`;
    });
    sel.disabled = subtemas.length === 0;
});

// Agregar item
document.getElementById('btnAgregarItem').addEventListener('click', function () {
    contadorItems++;
    const fila = document.getElementById('filaVacia');
    if (fila) fila.remove();

    const tbody = document.getElementById('cuerpoItems');
    const tr    = document.createElement('tr');
    tr.id = `itemFila${contadorItems}`;
    tr.innerHTML = `
        <td class="text-center">
            <input type="number" name="items[${contadorItems}][numero]"
                   class="form-control text-center fw-bold"
                   value="${contadorItems}" min="1" required
                   style="width:80px;margin:0 auto;">
        </td>
        <td>
            <textarea name="items[${contadorItems}][texto]"
                      class="form-control" rows="2"
                      placeholder="Escribi la consigna o pregunta..." required></textarea>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="quitarItem(${contadorItems})">
                <i class="bi bi-x"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);

    // Focus en el textarea del nuevo item
    tr.querySelector('textarea').focus();
});

function quitarItem(id) {
    const fila = document.getElementById(`itemFila${id}`);
    if (fila) fila.remove();

    // Si no quedan filas mostrar la fila vacía
    const tbody = document.getElementById('cuerpoItems');
    if (tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr id="filaVacia">
                <td colspan="3" class="text-center text-muted py-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Presiona "Agregar consigna" para agregar items.
                </td>
            </tr>`;
    }
}

document.getElementById('formActividad')?.addEventListener('submit', async function(e) {
    if (navigator.onLine) return;
    e.preventDefault();

    const form = e.target;
    const datos = {
        materia_id:  parseInt(form.querySelector('[name=materia_id]')?.value),
        curso_id:    parseInt(form.querySelector('[name=curso_id]')?.value),
        titulo:      form.querySelector('[name=titulo]')?.value,
        descripcion: form.querySelector('[name=descripcion]')?.value || null,
        fecha:       form.querySelector('[name=fecha]')?.value || new Date().toISOString().split('T')[0],
    };

    await OfflineManager.guardar('actividades', 'insert', datos);

    const btn = form.querySelector('[type=submit]');
    btn.disabled    = true;
    btn.textContent = '✅ Guardado localmente';
    btn.className   = 'btn btn-warning';

    const alerta = document.createElement('div');
    alerta.className = 'alert alert-warning mt-3';
    alerta.innerHTML = '⚠️ <strong>Sin conexión</strong> — Actividad guardada localmente. Se sincronizará cuando vuelva internet.';
    form.insertAdjacentElement('afterend', alerta);
});
</script>
@endpush