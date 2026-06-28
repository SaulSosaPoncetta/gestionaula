@extends('pdf._layout')
@section('titulo', 'Calificaciones — ' . $materia->nombre . ' · ' . $curso->nombre_completo)

@section('content')
<div class="seccion">
    <div class="grid2">
        <div class="cuadro">
            <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
            <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
        </div>
        <div class="cuadro">
            <div class="dato"><label>Total alumnos</label><span>{{ $alumnos->count() }}</span></div>
            <div class="dato"><label>Fecha</label><span>{{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y') }}</span></div>
        </div>
    </div>

    @foreach($alumnos as $a)
    @php
        $cals = $a->calificaciones;
        $prom = $cals->whereNotNull('nota')->avg('nota');
        $clsProm = $prom >= 7 ? 'nota-alta' : ($prom >= 4 ? 'nota-media' : 'nota-baja');
    @endphp
    <div style="margin-bottom:10px">
        <div style="background:#e3f2fd;padding:4px 8px;font-weight:bold;font-size:10px;border-left:3px solid #1565c0;margin-bottom:4px">
            {{ $a->apellido }}, {{ $a->nombre }}
            @if($prom)
                &nbsp;·&nbsp; <span class="{{ $clsProm }}">Promedio: {{ number_format($prom, 2) }}</span>
            @endif
        </div>
        @if($cals->isEmpty())
            <div style="padding:4px 8px;color:#999;font-size:9px">Sin calificaciones registradas.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th width="16%">Fecha</th>
                    <th width="30%">Tipo evaluación</th>
                    <th width="12%" class="text-center">Nota</th>
                    <th width="42%">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cals as $c)
                @php $cls = $c->nota >= 7 ? 'nota-alta' : ($c->nota >= 4 ? 'nota-media' : 'nota-baja'); @endphp
                <tr>
                    <td>{{ $c->fecha ? \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $c->tipoevaluacion?->nombre ?? '—' }}</td>
                    <td class="text-center {{ $cls }}"><strong>{{ $c->nota ?? '—' }}</strong></td>
                    <td style="font-size:8px;color:#666">{{ $c->observacion ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endforeach
</div>
@endsection