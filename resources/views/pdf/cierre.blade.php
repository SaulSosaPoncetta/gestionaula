@extends('pdf._layout')
@section('titulo', 'Cierre de Notas — ' . $tipocierre . ' · ' . $materia->nombre)

@section('content')
<div class="seccion">
    <div class="grid2">
        <div class="cuadro">
            <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
            <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
        </div>
        <div class="cuadro">
            <div class="dato"><label>Tipo de cierre</label><span>{{ $tipocierre }}</span></div>
            <div class="dato"><label>Total alumnos</label><span>{{ $cierres->count() }}</span></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="28%">Alumno</th>
                <th width="12%" class="text-center">Nota Final</th>
                <th width="24%">Valoración</th>
                <th width="10%" class="text-center">Prom. Calif.</th>
                <th width="10%" class="text-center">Prom. Activ.</th>
                <th width="8%" class="text-center">Asistencia</th>
                <th width="8%">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cierres as $r)
            @php
                $n   = $r->notanumerica;
                $cls = $n >= 7 ? 'nota-alta' : ($n >= 4 ? 'nota-media' : 'nota-baja');
            @endphp
            <tr>
                <td><strong>{{ $r->alumno?->apellido }}</strong>, {{ $r->alumno?->nombre }}</td>
                <td class="text-center {{ $cls }}" style="font-size:12px"><strong>{{ number_format($n, 2) }}</strong></td>
                <td>
                    @foreach(explode(' / ', $r->notavalorativa ?? '') as $v)
                        @if(trim($v))<span class="badge badge-{{ $n >= 7 ? 'green' : ($n >= 4 ? 'yellow' : 'red') }}">{{ trim($v) }}</span> @endif
                    @endforeach
                </td>
                <td class="text-center">{{ $r->promediocalificaciones ? number_format($r->promediocalificaciones, 2) : '—' }}</td>
                <td class="text-center">{{ $r->promedioactividades ? number_format($r->promedioactividades, 2) : '—' }}</td>
                <td class="text-center">{{ $r->porcentajeasistencia ? number_format($r->porcentajeasistencia, 1) . '%' : '—' }}</td>
                <td style="font-size:8px">{{ $r->fecharegistro?->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $prom = $cierres->avg('notanumerica');
        $apr  = $cierres->where('notanumerica', '>=', 7)->count();
        $reg  = $cierres->where('notanumerica', '>=', 4)->where('notanumerica', '<', 7)->count();
        $rep  = $cierres->where('notanumerica', '<', 4)->count();
    @endphp
    <div style="margin-top:10px;border:1px solid #ccc;border-radius:4px;padding:8px;display:flex;gap:20px;font-size:9px">
        <div><label style="color:#888;display:block">Promedio del grupo</label><strong class="{{ $prom >= 7 ? 'nota-alta' : ($prom >= 4 ? 'nota-media' : 'nota-baja') }}">{{ number_format($prom, 2) }}</strong></div>
        <div><label style="color:#888;display:block">Aprobados (≥7)</label><strong class="nota-alta">{{ $apr }}</strong></div>
        <div><label style="color:#888;display:block">Regulares (4-6.99)</label><strong class="nota-media">{{ $reg }}</strong></div>
        <div><label style="color:#888;display:block">Reprobados (&lt;4)</label><strong class="nota-baja">{{ $rep }}</strong></div>
    </div>
</div>
@endsection