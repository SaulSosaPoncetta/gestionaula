@extends('pdf._layout')
@section('titulo', 'Planilla de Asistencia en Blanco')
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo)

@section('content')
@php $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp
<div class="grid2">
    <div>
        <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Mes</label><span>{{ $meses[$mes] }} {{ $anio }}</span></div>
        <div class="dato"><label>Total días</label><span>{{ $diasMes }}</span></div>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th width="28%">Alumno</th>
            @for($d=1; $d<=$diasMes; $d++)
            <th class="text-center" style="font-size:8px;padding:3px 1px">{{ $d }}</th>
            @endfor
            <th width="6%" class="text-center">P</th>
            <th width="6%" class="text-center">A</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alumnos as $a)
        <tr style="height:22px">
            <td><strong>{{ $a->apellido }}</strong>, {{ $a->nombre }}</td>
            @for($d=1; $d<=$diasMes; $d++)
            <td style="border:1px solid #ddd;min-width:12px"></td>
            @endfor
            <td style="border:1px solid #ccc"></td>
            <td style="border:1px solid #ccc"></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top:8px;font-size:9px;color:#666">P = Presente &nbsp; A = Ausente &nbsp; T = Tarde &nbsp; J = Justificado</div>
<div style="margin-top:28px;display:flex;justify-content:space-between;font-size:9px">
    <div style="text-align:center;width:38%"><div style="border-top:1px solid #000;padding-top:3px">Firma del Docente</div></div>
    <div style="text-align:center;width:38%"><div style="border-top:1px solid #000;padding-top:3px">Sello</div></div>
</div>
@endsection
