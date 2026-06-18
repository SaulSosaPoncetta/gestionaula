@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar declaración jurada</h4>
        <p class="text-muted">Solo se pueden editar declaraciones en estado <span class="badge bg-warning text-dark">borrador</span>.</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="{{ route('declaracion.show', $declaracion) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('declaracion.update', $declaracion) }}" id="formDeclaracion">
    @csrf @method('PUT')

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ciclo lectivo <span class="text-danger">*</span></label>
                    <input type="text" name="ciclo" class="form-control @error('ciclo') is-invalid @enderror"
                           value="{{ old('ciclo', $cicloactual) }}" required>
                    @error('ciclo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha de declaración <span class="text-danger">*</span></label>
                    <input type="date" name="fechadeclaracion"
                           class="form-control @error('fechadeclaracion') is-invalid @enderror"
                           value="{{ old('fechadeclaracion', $declaracion->fechadeclaracion?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                    @error('fechadeclaracion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Docente</label>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" disabled>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de ítems --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock me-1"></i>Carga horaria semanal</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="agregarfila">
                <i class="bi bi-plus-circle me-1"></i>Agregar fila
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" id="tablahorarios">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="min-width:110px">Día</th>
                            <th style="min-width:100px">Hora inicio</th>
                            <th style="min-width:100px">Hora fin</th>
                            <th style="min-width:180px">Establecimiento</th>
                            <th style="min-width:160px">Curso</th>
                            <th style="min-width:160px">Materia</th>
                            <th class="text-center" style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="filas">
                        @forelse($horarios as $i => $horario)
                        <tr class="fila-horario">
                            <td class="ps-3">
                                <select name="items[{{ $i }}][dia]" class="form-select form-select-sm" required>
                                    <option value="">—</option>
                                    @foreach($dias as $dia)
                                        <option value="{{ $dia }}" {{ $horario->dia === $dia ? 'selected' : '' }}>
                                            {{ ucfirst($dia) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="time" name="items[{{ $i }}][horainicio]"
                                       class="form-control form-control-sm"
                                       value="{{ substr($horario->horainicio, 0, 5) }}" required>
                            </td>
                            <td>
                                <input type="time" name="items[{{ $i }}][horafin]"
                                       class="form-control form-control-sm"
                                       value="{{ substr($horario->horafin, 0, 5) }}" required>
                            </td>
                            <td>
                                <select name="items[{{ $i }}][establecimiento_id]" class="form-select form-select-sm">
                                    <option value="">—</option>
                                    @foreach($establecimientos as $est)
                                        <option value="{{ $est->id }}"
                                            {{ $horario->establecimiento_id == $est->id ? 'selected' : '' }}>
                                            {{ $est->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[{{ $i }}][curso_id]" class="form-select form-select-sm selectcurso"
                                        data-selected="{{ $horario->curso_id }}">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td>
                                <select name="items[{{ $i }}][materia_id]" class="form-select form-select-sm selectmateria"
                                        data-selected="{{ $horario->materia_id }}">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger eliminarfila">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="fila-horario">
                            <td class="ps-3">
                                <select name="items[0][dia]" class="form-select form-select-sm" required>
                                    <option value="">—</option>
                                    @foreach($dias as $dia)
                                        <option value="{{ $dia }}">{{ ucfirst($dia) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="time" name="items[0][horainicio]" class="form-control form-control-sm" required></td>
                            <td><input type="time" name="items[0][horafin]" class="form-control form-control-sm" required></td>
                            <td>
                                <select name="items[0][establecimiento_id]" class="form-select form-select-sm">
                                    <option value="">—</option>
                                    @foreach($establecimientos as $est)
                                        <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[0][curso_id]" class="form-select form-select-sm selectcurso" data-selected="">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td>
                                <select name="items[0][materia_id]" class="form-select form-select-sm selectmateria" data-selected="">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger eliminarfila">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>Guardar cambios
        </button>
        <a href="{{ route('declaracion.show', $declaracion) }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
const dias             = @json($dias);
const establecimientos = @json($establecimientos->map(fn($e) => ['id' => $e->id, 'nombre' => $e->nombre]));
const cursosData       = @json(\App\Models\Curso::where('user_id', auth()->id())->orderBy('anio')->orderBy('division')->get()->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre_completo]));
const materiasData     = @json(\App\Models\Materia::where('user_id', auth()->id())->orderBy('nombre')->get()->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]));

let indice = {{ $horarios->count() ?: 1 }};

function llenarSelectCursos(select, selectedId = null) {
    select.innerHTML = '<option value="">—</option>';
    cursosData.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.nombre;
        if (selectedId && c.id == selectedId) opt.selected = true;
        select.appendChild(opt);
    });
}

function llenarSelectMaterias(select, selectedId = null) {
    select.innerHTML = '<option value="">—</option>';
    materiasData.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.nombre;
        if (selectedId && m.id == selectedId) opt.selected = true;
        select.appendChild(opt);
    });
}

function agregarFila() {
    const tbody = document.getElementById('filas');
    const tr    = document.createElement('tr');
    tr.className = 'fila-horario';

    const opcionesDias  = '<option value="">—</option>' +
        dias.map(d => `<option value="${d}">${d.charAt(0).toUpperCase() + d.slice(1)}</option>`).join('');
    const opcionesEstab = '<option value="">—</option>' +
        establecimientos.map(e => `<option value="${e.id}">${e.nombre}</option>`).join('');

    tr.innerHTML = `
        <td class="ps-3">
            <select name="items[${indice}][dia]" class="form-select form-select-sm" required>
                ${opcionesDias}
            </select>
        </td>
        <td><input type="time" name="items[${indice}][horainicio]" class="form-control form-control-sm" required></td>
        <td><input type="time" name="items[${indice}][horafin]"    class="form-control form-control-sm" required></td>
        <td>
            <select name="items[${indice}][establecimiento_id]" class="form-select form-select-sm">
                ${opcionesEstab}
            </select>
        </td>
        <td><select name="items[${indice}][curso_id]"   class="form-select form-select-sm selectcurso"   data-selected=""></select></td>
        <td><select name="items[${indice}][materia_id]" class="form-select form-select-sm selectmateria" data-selected=""></select></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger eliminarfila">
                <i class="bi bi-trash"></i>
            </button>
        </td>`;

    tbody.appendChild(tr);
    llenarSelectCursos(tr.querySelector('.selectcurso'));
    llenarSelectMaterias(tr.querySelector('.selectmateria'));
    indice++;
}

document.querySelectorAll('.fila-horario').forEach(fila => {
    const sc = fila.querySelector('.selectcurso');
    const sm = fila.querySelector('.selectmateria');
    if (sc) llenarSelectCursos(sc, sc.dataset.selected);
    if (sm) llenarSelectMaterias(sm, sm.dataset.selected);
});

document.getElementById('agregarfila').addEventListener('click', agregarFila);

document.getElementById('filas').addEventListener('click', function(e) {
    if (e.target.closest('.eliminarfila')) {
        if (document.querySelectorAll('.fila-horario').length > 1) {
            e.target.closest('tr').remove();
        }
    }
});
</script>
@endpush
