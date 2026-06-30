@extends('pdf._layout')
@section('titulo', 'Cierre de Notas — ' . $tipocierre)
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo)

@section('content')
<div class="grid2">
    <div>
        <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Tipo</label><span>{{ $tipocierre }}</span></div>
        <div class="dato"><label>Total</label><span>{{ $cierres->count() }} alumnos</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="26%">Alumno</th>
            <th width="10%" class="text-center">Nota</th>
            <th width="26%">Valoración</th>
            <th width="10%" class="text-center">P. Cal.</th>
            <th width="10%" class="text-center">P. Act.</th>
            <th width="8%" class="text-center">Asist.</th>
            <th width="10%">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cierres as $r)
        @php $n=$r->notanumerica; $c=$n>=7?'nota-alta':($n>=4?'nota-media':'nota-baja'); @endphp
        <tr>
            <td><strong>{{ $r->alumno?->apellido }}</strong>, {{ $r->alumno?->nombre }}</td>
            <td class="text-center {{ $c }}" style="font-size:13px"><strong>{{ number_format($n,2) }}</strong></td>
            <td>
                @foreach(explode(' / ', $r->notavalorativa ?? '') as $v)
                    @if(trim($v)) <span class="badge badge-{{ $n>=7?'green':($n>=4?'yellow':'red') }}">{{ trim($v) }}</span> @endif
                @endforeach
            </td>
            <td class="text-center">{{ $r->promediocalificaciones ? number_format($r->promediocalificaciones,2) : '—' }}</td>
            <td class="text-center">{{ $r->promedioactividades ? number_format($r->promedioactividades,2) : '—' }}</td>
            <td class="text-center">{{ $r->porcentajeasistencia ? number_format($r->porcentajeasistencia,1).'%' : '—' }}</td>
            <td style="font-size:9px">{{ $r->fecharegistro?->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@php
    $prom = $cierres->avg('notanumerica');
    $apr  = $cierres->where('notanumerica','>=',7)->count();
    $reg  = $cierres->filter(fn($r)=>$r->notanumerica>=4&&$r->notanumerica<7)->count();
    $rep  = $cierres->where('notanumerica','<',4)->count();
@endphp
<div style="margin-top:10px;border:1px solid #ccc;border-radius:4px;padding:8px;display:flex;gap:24px;font-size:10px">
    <div><div style="color:#888;font-size:9px">Promedio</div><strong class="{{ $prom>=7?'nota-alta':($prom>=4?'nota-media':'nota-baja') }}">{{ number_format($prom,2) }}</strong></div>
    <div><div style="color:#888;font-size:9px">Aprobados ≥7</div><strong class="nota-alta">{{ $apr }}</strong></div>
    <div><div style="color:#888;font-size:9px">Regulares 4-6.99</div><strong class="nota-media">{{ $reg }}</strong></div>
    <div><div style="color:#888;font-size:9px">Reprobados &lt;4</div><strong class="nota-baja">{{ $rep }}</strong></div>
</div>
@endsection
