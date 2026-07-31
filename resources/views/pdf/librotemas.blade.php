@extends('pdf._layout')
@section('titulo', 'Libro de Temas')
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo)

@section('content')
<div class="grid2">
    <div>
        <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Total registros</label><span>{{ $temas->count() }}</span></div>
    </div>
</div>
@if($temas->isEmpty())
<div class="alerta">No hay registros de libro de temas para el período seleccionado.</div>
@else
<table>
    <thead>
        <tr>
            <th width="12%">Fecha</th>
            <th width="18%">Tipo de clase</th>
            <th width="40%">Tema</th>
            <th width="30%">Observación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($temas as $t)
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->fecha)->format('d/m/Y') }}</td>
            <td>{{ $t->tipoclase?->nombre ?? '—' }}</td>
            <td>{{ $t->tema ?? '—' }}</td>
            <td style="font-size:9px;color:#555">{{ $t->observacion ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
