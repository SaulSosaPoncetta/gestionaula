@extends('pdf._layout')
@section('titulo', 'Registro de Asistencia')
@section('subtitulo', $materia->nombre . ' — ' . $curso->nombre_completo)

@section('content')
<div class="grid2">
    <div>
        <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Total clases</label><span>{{ $fechas->count() }}</span></div>
        <div class="dato"><label>Total alumnos</label><span>{{ $alumnos->count() }}</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="22%">Alumno</th>
            <th width="6%" class="text-center">P</th>
            <th width="6%" class="text-center">A</th>
            <th width="6%" class="text-center">T</th>
            <th width="6%" class="text-center">J</th>
            <th width="7%" class="text-center">%</th>
            @foreach($fechas->take(22) as $f)
            <th class="text-center" style="font-size:8px;padding:3px 2px">{{ \Carbon\Carbon::parse($f)->format('d/m') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($alumnos as $a)
        @php
            $regs = $porAlumno->get($a->id, collect());
            $p = $regs->where('estado','presente')->count();
            $au= $regs->where('estado','ausente')->count();
            $t = $regs->where('estado','tarde')->count();
            $j = $regs->where('estado','justificado')->count();
            $total = $p + $au + $t + $j;
            $pct = $total > 0 ? round((($p+$t+$j)/$total)*100) : 0;
            $cPct = $pct >= 75 ? 'nota-alta' : ($pct >= 60 ? 'nota-media' : 'nota-baja');
        @endphp
        <tr>
            <td><strong>{{ $a->apellido }}</strong>, {{ $a->nombre }}</td>
            <td class="text-center nota-alta">{{ $p }}</td>
            <td class="text-center nota-baja">{{ $au }}</td>
            <td class="text-center nota-media">{{ $t }}</td>
            <td class="text-center">{{ $j }}</td>
            <td class="text-center {{ $cPct }}"><strong>{{ $pct }}%</strong></td>
            @foreach($fechas->take(22) as $f)
            @php
                $r   = $regs->first(fn($x) => $x->fecha->toDateString() === \Carbon\Carbon::parse($f)->toDateString());
                $est = $r?->estado;
                $lbl = match($est) {'presente'=>'P','ausente'=>'A','tarde'=>'T','justificado'=>'J',default=>'·'};
                $cls = match($est) {'presente'=>'nota-alta','ausente'=>'nota-baja','tarde'=>'nota-media',default=>''};
            @endphp
            <td class="text-center {{ $cls }}" style="font-size:9px;padding:3px 2px">{{ $lbl }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
<div style="font-size:9px;color:#666;margin-top:4px">
    <strong>P</strong> = Presente &nbsp; <strong>A</strong> = Ausente &nbsp;
    <strong>T</strong> = Tarde &nbsp; <strong>J</strong> = Justificado
    @if($fechas->count() > 22)
        &nbsp; · &nbsp; <em>Se muestran las primeras 22 fechas ({{ $fechas->count() }} en total).</em>
    @endif
</div>
@endsection
