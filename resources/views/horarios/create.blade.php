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

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-file-earmark-person me-1 text-primary"></i>
        Cargar desde una designación
        <span class="badge bg-info ms-2">Opcional</span>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Seleccioná una designación para completar automáticamente los datos del horario.
        </p>
        <div class="row g-3">
            <div class="col-md-8">
                <select id="selectDesignacion" class="form-select">
                    <option value="">— Seleccioná una designación para autocompletar —</option>
                    @foreach($designaciones as $d)
                        <option value="{{ $d->id }}"
                            data-nombreestablecimiento="{{ $d->nombreestablecimiento }}"
                            data-numeroescuela="{{ $d->numeroescuela }}"
                            data-nombremateria="{{ $d->nombremateria }}"
                            data-anodesignado="{{ $d->anodesignado }}"
                            data-divisiondesignada="{{ $d->divisiondesignada }}"
                            data-turno="{{ $d->turnodesempeno }}"
                            data-dia="{{ $d->diasemana }}"
                            data-horaentrada="{{ substr($d->horaentrada, 0, 5) }}"
                            data-horasalida="{{ substr($d->horasalida, 0, 5) }}">
                            {{ $d->nombreestablecimiento }} — {{ $d->nombremateria }}
                            ({{ \App\Models\Designacion::DIAS[$d->diasemana] ?? $d->diasemana }}
                            {{ substr($d->horaentrada, 0, 5) }}-{{ substr($d->horasalida, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" onclick="autocompletar()">
                    <i class="bi bi-magic me-1"></i>Autocompletar campos
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('horarios.store') }}">
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

                {{-- Establecimiento --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Establecimiento</label>
                    <select name="establecimiento_id" id="establecimiento_id" class="form-select">
                        <option value="">— Sin establecimiento —</option>
                        @foreach($establecimientos as $est)
                            <option value="{{ $est->id }}"
                                data-nombre="{{ strtolower($est->nombre) }}"
                                {{ old('establecimiento_id') == $est->id ? 'selected' : '' }}>
                                {{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <div id="hintEstablecimiento" class="form-text text-warning d-none">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        El establecimiento de la designación no coincide exactamente.
                        Seleccionalo manualmente.
                    </div>
                </div>

                {{-- Curso --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                    <select name="curso_id" id="curso_id"
                            class="form-select @error('curso_id') is-invalid @enderror" required>
                        <option value="">— Seleccioná un curso —</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}"
                                data-anio="{{ strtolower($curso->anio) }}"
                                data-division="{{ strtolower($curso->division) }}"
                                data-turno="{{ strtolower($curso->turno) }}"
                                {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                {{ $curso->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('curso_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Materia --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Materia</label>
                    <select name="materia_id" id="materia_id" class="form-select">
                        <option value="">— Sin materia —</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id }}"
                                data-nombre="{{ strtolower($m->nombre) }}"
                                {{ old('materia_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <div id="hintMateria" class="form-text text-warning d-none">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        La materia de la designación no coincide exactamente.
                        Seleccionala manualmente.
                    </div>
                </div>

                {{-- Día --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Día <span class="text-danger">*</span></label>
                    <select name="dia" id="dia"
                            class="form-select @error('dia') is-invalid @enderror" required>
                        <option value="">—</option>
                        @foreach($dias as $dia)
                            <option value="{{ $dia }}" {{ old('dia') == $dia ? 'selected' : '' }}>
                                {{ ucfirst($dia) }}
                            </option>
                        @endforeach
                    </select>
                    @error('dia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Hora inicio --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
                    <input type="time" name="horainicio" id="horainicio"
                           class="form-control @error('horainicio') is-invalid @enderror"
                           value="{{ old('horainicio') }}" required>
                    @error('horainicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Hora fin --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hora fin <span class="text-danger">*</span></label>
                    <input type="time" name="horafin" id="horafin"
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
@endsection

@push('scripts')
<script>
const materiasPorCurso = @json($cursos->mapWithKeys(fn($c) => [$c->id => $c->materias->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])]));
const todasLasMaterias = @json($materias->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]));

document.getElementById('curso_id').addEventListener('change', function () {
    const select  = document.getElementById('materia_id');
    const cursoId = this.value;
    const materias = (cursoId && materiasPorCurso[cursoId]?.length)
                   ? materiasPorCurso[cursoId]
                   : todasLasMaterias;

    select.innerHTML = '<option value="">— Sin materia —</option>';
    materias.forEach(m => {
        select.innerHTML += `<option value="${m.id}" data-nombre="${m.nombre.toLowerCase()}">${m.nombre}</option>`;
    });
});

function autocompletar() {
    const sel = document.getElementById('selectDesignacion');
    const opt = sel.options[sel.selectedIndex];

    if (!opt || !opt.value) {
        alert('Seleccioná una designación primero.');
        return;
    }

    const nombreEstab   = opt.dataset.nombreestablecimiento?.toLowerCase() ?? '';
    const nombreMateria = opt.dataset.nombremateria?.toLowerCase() ?? '';
    const anoDesignado  = opt.dataset.anodesignado?.toLowerCase() ?? '';
    const division      = opt.dataset.divisiondesignada?.toLowerCase() ?? '';
    const turno         = opt.dataset.turno?.toLowerCase() ?? '';
    const dia           = opt.dataset.dia ?? '';
    const horaEntrada   = opt.dataset.horaentrada ?? '';
    const horaSalida    = opt.dataset.horasalida ?? '';

    // Día
    document.getElementById('dia').value = dia;

    // Horas
    document.getElementById('horainicio').value = horaEntrada;
    document.getElementById('horafin').value     = horaSalida;

    // Establecimiento — buscar por nombre similar
    let estEncontrado = false;
    const selectEst = document.getElementById('establecimiento_id');
    for (let i = 0; i < selectEst.options.length; i++) {
        const opt = selectEst.options[i];
        const nombre = opt.dataset.nombre ?? '';
        if (nombre && (
            nombre.includes(nombreEstab) ||
            nombreEstab.includes(nombre) ||
            levenshtein(nombre, nombreEstab) < 5
        )) {
            selectEst.value = opt.value;
            estEncontrado = true;
            break;
        }
    }
    document.getElementById('hintEstablecimiento').classList.toggle('d-none', estEncontrado);

    // Curso — buscar por año, división y turno
    let cursoEncontrado = false;
    const selectCurso = document.getElementById('curso_id');
    for (let i = 0; i < selectCurso.options.length; i++) {
        const opt   = selectCurso.options[i];
        const anio  = opt.dataset.anio ?? '';
        const div   = opt.dataset.division ?? '';
        const trn   = opt.dataset.turno ?? '';
        if (
            anio.includes(anoDesignado) || anoDesignado.includes(anio)
        ) {
            if (div === division || !division) {
                if (trn.includes(turno) || turno.includes(trn) || !turno) {
                    selectCurso.value = opt.value;
                    cursoEncontrado = true;
                    // Actualizar materias del curso
                    selectCurso.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }
    }

    // Materia — buscar por nombre similar (después de actualizar el select)
    setTimeout(() => {
        let materiaEncontrada = false;
        const selectMat = document.getElementById('materia_id');
        for (let i = 0; i < selectMat.options.length; i++) {
            const opt    = selectMat.options[i];
            const nombre = opt.dataset.nombre ?? '';
            if (nombre && (
                nombre.includes(nombreMateria) ||
                nombreMateria.includes(nombre) ||
                levenshtein(nombre, nombreMateria) < 8
            )) {
                selectMat.value = opt.value;
                materiaEncontrada = true;
                break;
            }
        }
        document.getElementById('hintMateria').classList.toggle('d-none', materiaEncontrada);
    }, 300);

    // Feedback visual
    const btn = event.target;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Completado';
    btn.classList.replace('btn-outline-primary', 'btn-success');
    setTimeout(() => {
        btn.innerHTML = original;
        btn.classList.replace('btn-success', 'btn-outline-primary');
    }, 2000);
}

// Distancia Levenshtein para matching aproximado
function levenshtein(a, b) {
    const m = a.length, n = b.length;
    const dp = Array.from({length: m+1}, (_, i) => Array.from({length: n+1}, (_, j) => i === 0 ? j : j === 0 ? i : 0));
    for (let i = 1; i <= m; i++) {
        for (let j = 1; j <= n; j++) {
            dp[i][j] = a[i-1] === b[j-1]
                ? dp[i-1][j-1]
                : 1 + Math.min(dp[i-1][j], dp[i][j-1], dp[i-1][j-1]);
        }
    }
    return dp[m][n];
}
</script>
@endpush