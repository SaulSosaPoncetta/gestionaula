@extends('pdf._layout')
@section('titulo', 'Planilla de Asistencia')
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo . ' — ' . \Carbon\Carbon::createFromDate($anio, $mes, 1)->translatedFormat('F Y'))

@section('content')
@php
    $mesesNombres = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
@endphp
<div class="grid2">
    <div>
        <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Mes</label><span>{{ $mesesNombres[$mes] }} {{ $anio }}</span></div>
        <div class="dato"><label>Total días</label><span>{{ $diasMes }}</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="26%">Alumno</th>
            @for($d = 1; $d <= $diasMes; $d++)
            <th class="text-center" style="font-size:8px;padding:3px 1px">{{ $d }}</th>
            @endfor
            <th width="8%" class="text-center">Total P</th>
            <th width="8%" class="text-center">Total A</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alumnos as $a)
        <tr style="height:20px">
            <td><strong>{{ $a->apellido }}</strong>, {{ $a->nombre }}</td>
            @for($d = 1; $d <= $diasMes; $d++)
            <td style="border:1px solid #ddd;min-width:14px"></td>
            @endfor
            <td style="border:1px solid #ccc"></td>
            <td style="border:1px solid #ccc"></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top:10px;font-size:9px;color:#666">
    <strong>Referencia:</strong> P = Presente &nbsp; A = Ausente &nbsp; T = Tarde &nbsp; J = Justificado
</div>
<div style="margin-top:30px;display:flex;justify-content:space-between;font-size:9px">
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Firma del Docente</div>
    </div>
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Sello</div>
    </div>
</div>
@endsection
