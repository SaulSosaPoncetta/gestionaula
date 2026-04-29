@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar horario</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('horarios.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" id="curso_id" class="form-select @error('curso_id') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('curso_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Materia <span class="text-muted fw-normal">(opcional)</span></label>
                    <select name="materia_id" id="materia_id" class="form-select">
                        <option value="">Sin materia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Día</label>
                    <select name="dia" class="form-select @error('dia') is-invalid @enderror" required>
                        <option value="">Seleccioná...</option>
                        @foreach($dias as $dia)
                            <option value="{{ $dia }}" {{ old('dia') == $dia ? 'selected' : '' }}>
                                {{ ucfirst($dia) }}
                            </option>
                        @endforeach
                    </select>
                    @error('dia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hora inicio</label>
                    <input type="time" name="horainicio"
                           class="form-control @error('horainicio') is-invalid @enderror"
                           value="{{ old('horainicio') }}" required>
                    @error('horainicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Hora fin</label>
                    <input type="time" name="horafin"
                           class="form-control @error('horafin') is-invalid @enderror"
                           value="{{ old('horafin') }}" required>
                    @error('horafin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Guardar horario
                </button>
                <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias]));

document.getElementById('curso_id').addEventListener('change', function () {
    const select = document.getElementById('materia_id');
    const materias = materiasPorCurso[this.value] || [];
    select.innerHTML = '<option value="">Sin materia</option>';
    materias.forEach(m => {
        select.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
    });
});
</script>
@endpush
@endsection