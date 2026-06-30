@extends('pdf._layout')
@section('titulo', 'Boletín de Calificaciones')
@section('subtitulo', $alumno->apellido . ', ' . $alumno->nombre)

@section('content')
<div style="border:2px solid #1565c0;border-radius:6px;padding:12px;margin-bottom:14px;text-align:center">
    <div style="font-size:14px;font-weight:bold;color:#1565c0;margin-bottom:8px">BOLETÍN DE CALIFICACIONES</div>
    <div class="grid2" style="text-align:left">
        <div>
            <div class="dato"><label>Apellido y Nombre</label><span>{{ $alumno->apellido }}, {{ $alumno->nombre }}</span></div>
            <div class="dato"><label>DNI</label><span>{{ $alumno->dni ?? '—' }}</span></div>
        </div>
        <div>
            <div class="dato"><label>Curso</label><span>{{ $alumno->curso?->nombre_completo ?? '—' }}</span></div>
            <div class="dato"><label>Año lectivo</label><span>{{ \Carbon\Carbon::now()->year }}</span></div>
        </div>
    </div>
</div>

@forelse($cierres as $tipo => $registros)
<div class="seccion">
    <h2>{{ $tipo }}</h2>
    <table>
        <thead>
            <tr>
                <th width="36%">Materia</th>
                <th width="12%" class="text-center">Nota</th>
                <th width="28%">Valoración</th>
                <th width="12%" class="text-center">Prom. Cal.</th>
                <th width="12%" class="text-center">Asistencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $r)
            @php $n=$r->notanumerica; $c=$n>=7?'nota-alta':($n>=4?'nota-media':'nota-baja'); @endphp
            <tr>
                <td><strong>{{ $r->materia?->nombre ?? '—' }}</strong></td>
                <td class="text-center {{ $c }}" style="font-size:13px"><strong>{{ number_format($n,2) }}</strong></td>
                <td>
                    @foreach(explode(' / ', $r->notavalorativa ?? '') as $v)
                        @if(trim($v)) <span class="badge badge-{{ $n>=7?'green':($n>=4?'yellow':'red') }}">{{ trim($v) }}</span> @endif
                    @endforeach
                </td>
                <td class="text-center">{{ $r->promediocalificaciones ? number_format($r->promediocalificaciones,2) : '—' }}</td>
                <td class="text-center">{{ $r->porcentajeasistencia ? number_format($r->porcentajeasistencia,1).'%' : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@empty
<div class="alerta">No hay cierres de notas registrados para este alumno.</div>
@endforelse

<div style="margin-top:36px;display:flex;justify-content:space-between;font-size:9px;color:#555">
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Firma del Docente</div>
    </div>
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Firma del Padre / Tutor</div>
    </div>
</div>
@endsection
