@extends('pdf._layout')
@section('titulo', 'Historial de Calificaciones')
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo)

@section('content')
<div class="grid2">
    <div><div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div></div>
    <div><div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div></div>
</div>

@foreach($alumnos as $a)
@php
    $cals = $a->calificaciones;
    $prom = $cals->whereNotNull('nota')->avg('nota');
    $cls  = $prom >= 7 ? 'nota-alta' : ($prom >= 4 ? 'nota-media' : 'nota-baja');
@endphp
<div class="seccion">
    <div style="background:#e8eaf6;padding:5px 10px;font-weight:bold;font-size:11px;border-left:4px solid #1565c0;margin-bottom:4px;display:flex;justify-content:space-between">
        <span>{{ $a->apellido }}, {{ $a->nombre }}</span>
        @if($prom) <span class="{{ $cls }}">Promedio: {{ number_format($prom,2) }}</span> @endif
    </div>
    @if($cals->isEmpty())
        <div style="padding:4px 10px;color:#999;font-size:10px">Sin calificaciones registradas.</div>
    @else
    <table>
        <thead>
            <tr>
                <th width="16%">Fecha</th>
                <th width="32%">Tipo de evaluación</th>
                <th width="10%" class="text-center">Nota</th>
                <th width="42%">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cals as $c)
            @php $nc = $c->nota >= 7 ? 'nota-alta' : ($c->nota >= 4 ? 'nota-media' : 'nota-baja'); @endphp
            <tr>
                <td>{{ $c->created_at ? $c->created_at->format('d/m/Y') : '—' }}</td>
                <td>{{ $c->tipoevaluacion?->nombre ?? '—' }}</td>
                <td class="text-center {{ $nc }}"><strong>{{ $c->nota ?? '—' }}</strong></td>
                <td style="font-size:9px;color:#555">{{ $c->observacion ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endforeach
@endsection
