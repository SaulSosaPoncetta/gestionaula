@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-calendar-x me-2"></i>Registrar cese</h4>
        <p class="text-muted">Al registrar el cese se eliminará el horario correspondiente.</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('ceses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('ceses.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">

                {{-- Materia --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Materia <span class="text-danger">*</span>
                    </label>
                    <select name="materia_id" id="materia_id" class="form-select" required>
                        <option value="">— Seleccioná una materia —</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}"
                                {{ old('materia_id') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Establecimiento --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Establecimiento <span class="text-danger">*</span>
                    </label>
                    <select name="establecimiento_id" class="form-select" required>
                        <option value="">— Seleccioná un establecimiento —</option>
                        @foreach($establecimientos as $est)
                            <option value="{{ $est->id }}"
                                {{ old('establecimiento_id') == $est->id ? 'selected' : '' }}>
                                {{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Horario a cesar --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Horario a cesar
                        <span class="text-muted fw-normal">(se eliminará al registrar el cese)</span>
                    </label>
                    <select name="horario_id" id="horario_id" class="form-select">
                        <option value="">— Sin horario asociado —</option>
                        @foreach($horarios as $horario)
                            <option value="{{ $horario->id }}"
                                data-materia="{{ $horario->materia_id }}"
                                {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                                {{ ucfirst($horario->dia) }}
                                {{ \Carbon\Carbon::parse($horario->horainicio)->format('H:i') }}
                                —
                                {{ \Carbon\Carbon::parse($horario->horafin)->format('H:i') }}
                                |
                                {{ $horario->materia?->nombre ?? '—' }}
                                {{ $horario->curso?->nombre_completo ? '(' . $horario->curso->nombre_completo . ')' : '' }}
                                {{ $horario->establecimiento?->nombre ? '— ' . $horario->establecimiento->nombre : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Al guardar el cese este horario será eliminado del sistema.
                    </div>
                </div>

                {{-- Número de secuencia --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Número de secuencia</label>
                    <input type="text" name="numerosecuencia" class="form-control"
                           value="{{ old('numerosecuencia') }}"
                           placeholder="Ej: 001/2026">
                </div>

                {{-- Fecha toma de posesión --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Fecha de toma de posesión <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fechatomapossesion" class="form-control"
                           value="{{ old('fechatomapossesion') }}" required>
                </div>

                {{-- Fecha de cese --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Fecha de cese <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fechacese" class="form-control"
                           value="{{ old('fechacese') }}" required>
                </div>

            </div>

            {{-- Preview del horario seleccionado --}}
            <div id="previewHorario" class="alert alert-warning mt-4" style="display:none">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Se eliminará el horario: <strong id="previewTexto"></strong>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-calendar-x me-1"></i>Registrar cese
                </button>
                <a href="{{ route('ceses.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('horario_id').addEventListener('change', function () {
    const preview = document.getElementById('previewHorario');
    const texto   = document.getElementById('previewTexto');
    const opt     = this.options[this.selectedIndex];

    if (this.value) {
        texto.textContent  = opt.text;
        preview.style.display = '';
    } else {
        preview.style.display = 'none';
    }
});

// Filtrar horarios por materia seleccionada
document.getElementById('materia_id').addEventListener('change', function () {
    const materiaId = this.value;
    const horarioSel = document.getElementById('horario_id');
    const opciones   = horarioSel.querySelectorAll('option[data-materia]');

    opciones.forEach(opt => {
        if (!materiaId || opt.dataset.materia === materiaId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });

    horarioSel.value = '';
    document.getElementById('previewHorario').style.display = 'none';
});
</script>
@endpush