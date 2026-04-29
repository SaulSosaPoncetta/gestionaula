@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Nueva declaración jurada</h4>
        <p class="text-muted">Completá tu declaración jurada de horarios para el ciclo lectivo.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('declaracion.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<form method="POST" action="{{ route('declaracion.store') }}" id="formDeclaracion">
    @csrf

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Ciclo lectivo</label>
                    <input type="text" name="ciclo" class="form-control"
                           value="{{ $cicloactual }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Docente</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                </div>
            </div>
        </div>
    </div>

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
                            <th class="ps-3">Día</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Curso</th>
                            <th>Materia</th>
                            <th>Actividad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="filas">
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
                                <select name="items[0][curso_id]" class="form-select form-select-sm selectcurso">
                                    <option value="">—</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}">{{ $curso->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[0][materia_id]" class="form-select form-select-sm selectmateria">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td><input type="text" name="items[0][actividad]" class="form-control form-control-sm" placeholder="Ej: Clase, Reunión..."></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger btneliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Guardar borrador
        </button>
        <a href="{{ route('declaracion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias]));
const dias = @json($dias);
const cursos = @json($cursos);
let indice = 1;

function actualizarMaterias(selectCurso) {
    const selectMateria = selectCurso.closest('tr').querySelector('.selectmateria');
    const materias = materiasPorCurso[selectCurso.value] || [];
    selectMateria.innerHTML = '<option value="">—</option>';
    materias.forEach(m => {
        selectMateria.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
    });
}

document.getElementById('filas').addEventListener('change', function(e) {
    if (e.target.classList.contains('selectcurso')) {
        actualizarMaterias(e.target);
    }
});

document.getElementById('filas').addEventListener('click', function(e) {
    if (e.target.closest('.btneliminar')) {
        const filas = document.querySelectorAll('.fila-horario');
        if (filas.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});

document.getElementById('agregarfila').addEventListener('click', function() {
    const template = `
    <tr class="fila-horario">
        <td class="ps-3">
            <select name="items[${indice}][dia]" class="form-select form-select-sm" required>
                <option value="">—</option>
                ${dias.map(d => `<option value="${d}">${d.charAt(0).toUpperCase() + d.slice(1)}</option>`).join('')}
            </select>
        </td>
        <td><input type="time" name="items[${indice}][horainicio]" class="form-control form-control-sm" required></td>
        <td><input type="time" name="items[${indice}][horafin]" class="form-control form-control-sm" required></td>
        <td>
            <select name="items[${indice}][curso_id]" class="form-select form-select-sm selectcurso">
                <option value="">—</option>
                ${cursos.map(c => `<option value="${c.id}">${c.nombre} ${c.division ?? ''} ${c.turno ?? ''}</option>`).join('')}
            </select>
        </td>
        <td>
            <select name="items[${indice}][materia_id]" class="form-select form-select-sm selectmateria">
                <option value="">—</option>
            </select>
        </td>
        <td><input type="text" name="items[${indice}][actividad]" class="form-control form-control-sm" placeholder="Ej: Clase, Reunión..."></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btneliminar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;
    document.getElementById('filas').insertAdjacentHTML('beforeend', template);
    indice++;
});
</script>
@endpush
@endsection