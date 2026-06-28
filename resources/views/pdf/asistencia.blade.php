@extends('pdf._layout')
@section('titulo', 'Asistencia — ' . $materia->nombre . ' · ' . $curso->nombre_completo)

@section('content')
<div class="seccion">
    <div class="grid2">
        <div class="cuadro">
            <div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
            <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
        </div>
        <div class="cuadro">
            <div class="dato"><label>Total clases</label><span>{{ $fechas->count() }}</span></div>
            <div class="dato"><label>Total alumnos</label><span>{{ $alumnos->count() }}</span></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="24%">Alumno</th>
                <th width="8%" class="text-center">P</th>
                <th width="8%" class="text-center">A</th>
                <th width="8%" class="text-center">T</th>
                <th width="8%" class="text-center">J</th>
                <th width="8%" class="text-center">%</th>
                @foreach($fechas->take(20) as $f)
                <th class="text-center" style="font-size:7px;width:3%">
                    {{ \Carbon\Carbon::parse($f)->format('d/m') }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $a)
            @php
                $regs     = $porAlumno->get($a->id, collect());
                $p = $regs->where('estado','presente')->count();
                $au= $regs->where('estado','ausente')->count();
                $t = $regs->where('estado','tarde')->count();
                $j = $regs->where('estado','justificado')->count();
                $total = $p + $au + $t + $j;
                $pct = $total > 0 ? round((($p + $t + $j) / $total) * 100) : 0;
                $clsPct = $pct >= 75 ? 'nota-alta' : ($pct >= 60 ? 'nota-media' : 'nota-baja');
            @endphp
            <tr>
                <td><strong>{{ $a->apellido }}</strong>, {{ $a->nombre }}</td>
                <td class="text-center nota-alta">{{ $p }}</td>
                <td class="text-center nota-baja">{{ $au }}</td>
                <td class="text-center nota-media">{{ $t }}</td>
                <td class="text-center">{{ $j }}</td>
                <td class="text-center {{ $clsPct }}">{{ $pct }}%</td>
                @foreach($fechas->take(20) as $f)
                @php
                    $r = $regs->first(fn($x) => $x->fecha->toDateString() === \Carbon\Carbon::parse($f)->toDateString());
                    $est = $r?->estado;
                    $lbl = match($est) { 'presente'=>'P','ausente'=>'A','tarde'=>'T','justificado'=>'J', default=>'—' };
                    $cls = match($est) { 'presente'=>'nota-alta','ausente'=>'nota-baja','tarde'=>'nota-media', default=>'' };
                @endphp
                <td class="text-center {{ $cls }}" style="font-size:8px">{{ $lbl }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:6px;font-size:8px;color:#666">
        P = Presente &nbsp; A = Ausente &nbsp; T = Tarde &nbsp; J = Justificado
        @if($fechas->count() > 20) &nbsp; · &nbsp; Se muestran las primeras 20 fechas. @endif
    </div>
</div>
@endsection