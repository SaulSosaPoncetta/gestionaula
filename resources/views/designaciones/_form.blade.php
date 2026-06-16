@php
    $d = $designacion; // alias corto, null en create

    // Filas iniciales para el modo "dividido": old() > registros existentes > una fila vacia
    $filasIniciales = collect(old('horarios', $horariosViejos ?? []));
    if ($filasIniciales->isEmpty() && $d && $d->tipohorario === 'dividido') {
        $filasIniciales = $d->horarios->map(fn($h) => [
            'dia'         => $h->dia,
            'cantmodulos' => $h->cantmodulos,
            'horaentrada' => substr($h->horaentrada, 0, 5),
            'horasalida'  => substr($h->horasalida, 0, 5),
        ]);
    }
    if ($filasIniciales->isEmpty()) {
        $filasIniciales = collect([['dia' => '', 'cantmodulos' => '', 'horaentrada' => '', 'horasalida' => '']]);
    }

    $tipohorarioActual = old('tipohorario', $d->tipohorario ?? 'unificado');
@endphp

{{-- Datos institucionales --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-building me-1"></i>Datos del establecimiento
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Distrito <span class="text-danger">*</span></label>
                <input type="text" name="distrito" class="form-control"
                       value="{{ old('distrito', $d->distrito ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tipo de establecimiento <span class="text-danger">*</span></label>
                <input type="text" name="tipoestablecimiento" class="form-control"
                       value="{{ old('tipoestablecimiento', $d->tipoestablecimiento ?? '') }}"
                       placeholder="Ej: E.E.S.T., E.E.S., E.P.B." required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">N° Escuela <span class="text-danger">*</span></label>
                <input type="text" name="numeroescuela" class="form-control"
                       value="{{ old('numeroescuela', $d->numeroescuela ?? '') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Secuencia</label>
                <input type="text" name="secuencia" class="form-control"
                       value="{{ old('secuencia', $d->secuencia ?? '') }}" placeholder="Opcional">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Nombre del establecimiento <span class="text-danger">*</span></label>
                <input type="text" name="nombreestablecimiento" class="form-control"
                       value="{{ old('nombreestablecimiento', $d->nombreestablecimiento ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Dependencia <span class="text-danger">*</span></label>
                <select name="dependencia_tipo" class="form-select" required>
                    @foreach(\App\Models\Designacion::DEPENDENCIA_TIPOS as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('dependencia_tipo', $d->dependencia_tipo ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Dependencia (detalle)</label>
                <input type="text" name="dependencia" class="form-control"
                       value="{{ old('dependencia', $d->dependencia ?? '') }}" placeholder="Opcional">
            </div>
        </div>
    </div>
</div>

{{-- Datos de la designación --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-file-earmark-person me-1"></i>Datos de la designación
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Régimen estatutario <span class="text-danger">*</span></label>
                <input type="text" name="regimenstatutario" class="form-control"
                       value="{{ old('regimenstatutario', $d->regimenstatutario ?? '') }}"
                       placeholder="Ej: Titular, Provisional, Suplente" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Carácter de revista <span class="text-danger">*</span></label>
                <input type="text" name="caracterderevista" class="form-control"
                       value="{{ old('caracterderevista', $d->caracterderevista ?? '') }}"
                       placeholder="Ej: Titular, Suplente" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">IGE</label>
                <input type="text" name="ige" class="form-control"
                       value="{{ old('ige', $d->ige ?? '') }}" placeholder="N° de IGE">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Tipo hora <span class="text-danger">*</span></label>
                <select name="tipohora" class="form-select" required>
                    @foreach(\App\Models\Designacion::TIPOS_HORA as $val => $label)
                        <option value="{{ $val }}" {{ old('tipohora', $d->tipohora ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Cant. Módulos/Hs</label>
                <input type="text" name="cantmodulos" class="form-control"
                       value="{{ old('cantmodulos', $d->cantmodulos ?? '') }}"
                       placeholder="Total del cargo">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Cupof</label>
                <input type="text" name="cupof" class="form-control"
                       value="{{ old('cupof', $d->cupof ?? '') }}" placeholder="Opcional">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Fecha desde</label>
                <input type="date" name="fechadesde" class="form-control"
                       value="{{ old('fechadesde', $d?->fechadesde?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Fecha hasta</label>
                <input type="date" name="fechahasta" class="form-control"
                       value="{{ old('fechahasta', $d?->fechahasta?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Fecha designación</label>
                <input type="date" name="fechadesignacion" class="form-control"
                       value="{{ old('fechadesignacion', $d?->fechadesignacion?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Fecha toma posesión</label>
                <input type="date" name="fechatomaposecion" class="form-control"
                       value="{{ old('fechatomaposecion', $d?->fechatomaposecion?->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>

{{-- Datos de la clase --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-calendar3 me-1"></i>Datos de la clase
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre de la materia <span class="text-danger">*</span></label>
                <input type="text" name="nombremateria" class="form-control"
                       value="{{ old('nombremateria', $d->nombremateria ?? '') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Año designado <span class="text-danger">*</span></label>
                <input type="text" name="anodesignado" class="form-control"
                       value="{{ old('anodesignado', $d->anodesignado ?? '') }}" placeholder="Ej: 4to" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">División <span class="text-danger">*</span></label>
                <input type="text" name="divisiondesignada" class="form-control"
                       value="{{ old('divisiondesignada', $d->divisiondesignada ?? '') }}" placeholder="Ej: 1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Turno <span class="text-danger">*</span></label>
                <select name="turnodesempeno" class="form-select" required>
                    <option value="">Seleccioná...</option>
                    @foreach(\App\Models\Designacion::TURNOS as $val => $label)
                        <option value="{{ $val }}" {{ old('turnodesempeno', $d->turnodesempeno ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>

        {{-- Selector de tipo de horario --}}
        <div class="mb-3">
            <label class="form-label fw-semibold d-block">Tipo de horario <span class="text-danger">*</span></label>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="tipohorario" id="tipo_unificado"
                       value="unificado" autocomplete="off"
                       {{ $tipohorarioActual == 'unificado' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="tipo_unificado">
                    <i class="bi bi-clock me-1"></i>Unificado
                </label>

                <input type="radio" class="btn-check" name="tipohorario" id="tipo_dividido"
                       value="dividido" autocomplete="off"
                       {{ $tipohorarioActual == 'dividido' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="tipo_dividido">
                    <i class="bi bi-calendar-week me-1"></i>Dividido por día
                </label>
            </div>
            <div class="form-text">
                <strong>Unificado:</strong> el horario y el día son los mismos durante toda la designación.
                <strong>Dividido por día:</strong> permite cargar un horario distinto para cada día de la semana.
            </div>
        </div>

        {{-- Modo unificado --}}
        <div id="bloqueUnificado" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Día de la semana <span class="text-danger">*</span></label>
                <select name="diasemana" class="form-select" id="diasemana_unificado">
                    <option value="">Seleccioná...</option>
                    @foreach(\App\Models\Designacion::DIAS as $val => $label)
                        <option value="{{ $val }}" {{ old('diasemana', $d->diasemana ?? '') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Hora entrada <span class="text-danger">*</span></label>
                <input type="time" name="horaentrada" class="form-control" id="horaentrada_unificado"
                       value="{{ old('horaentrada', $d?->horaentrada ? substr($d->horaentrada, 0, 5) : '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Hora salida <span class="text-danger">*</span></label>
                <input type="time" name="horasalida" class="form-control" id="horasalida_unificado"
                       value="{{ old('horasalida', $d?->horasalida ? substr($d->horasalida, 0, 5) : '') }}">
            </div>
        </div>

        {{-- Modo dividido --}}
        <div id="bloqueDividido" class="d-none">
            <div class="table-responsive">
                <table class="table table-sm align-middle" id="tablaHorariosDividido">
                    <thead class="table-light">
                        <tr>
                            <th style="width:26%">Día</th>
                            <th style="width:20%">Cant. Módulos/Hs</th>
                            <th style="width:20%">Hora entrada</th>
                            <th style="width:20%">Hora salida</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="filasHorarioDividido">
                        @foreach($filasIniciales as $i => $fila)
                        <tr>
                            <td>
                                <select name="horarios[{{ $i }}][dia]" class="form-select form-select-sm">
                                    <option value="">Seleccioná...</option>
                                    @foreach(\App\Models\Designacion::DIAS as $val => $label)
                                        <option value="{{ $val }}" {{ ($fila['dia'] ?? '') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="horarios[{{ $i }}][cantmodulos]" class="form-control form-control-sm"
                                       value="{{ $fila['cantmodulos'] ?? '' }}" placeholder="Ej: 4">
                            </td>
                            <td>
                                <input type="time" name="horarios[{{ $i }}][horaentrada]" class="form-control form-control-sm"
                                       value="{{ $fila['horaentrada'] ?? '' }}">
                            </td>
                            <td>
                                <input type="time" name="horarios[{{ $i }}][horasalida]" class="form-control form-control-sm"
                                       value="{{ $fila['horasalida'] ?? '' }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-fila">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarFilaHorario">
                <i class="bi bi-plus-circle me-1"></i>Agregar día
            </button>
        </div>
    </div>
</div>
