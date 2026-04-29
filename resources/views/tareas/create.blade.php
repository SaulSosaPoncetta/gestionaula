@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva tarea</h4>
    </div>
    <div class="col-auto">
        <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('tareas.store') }}">
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
                <div class="col-12">
                    <label class="form-label fw-semibold">Título</label>
                    <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo') }}" placeholder="Ej: Trabajo práctico N°1" required>
                    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea name="descripcion" class="form-control" rows="4"
                              placeholder="Detallá las consignas de la tarea...">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha de vencimiento</label>
                    <input type="date" name="fechavencimiento"
                           class="form-control @error('fechavencimiento') is-invalid @enderror"
                           value="{{ old('fechavencimiento') }}" required>
                    @error('fechavencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Crear tarea
                </button>
                <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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