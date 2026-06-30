@extends('pdf._layout')
@section('titulo', 'Reporte General del Docente')
@section('subtitulo', $docente->name)

@section('content')
<div class="seccion">
    <div class="grid2">
        <div>
            <div class="dato"><label>Docente</label><span>{{ $docente->name }}</span></div>
            <div class="dato"><label>Email</label><span>{{ $docente->email }}</span></div>
        </div>
        <div>
            <div class="dato"><label>Fecha de emisión</label><span>{{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }}</span></div>
            <div class="dato"><label>Año lectivo</label><span>{{ \Carbon\Carbon::now()->year }}</span></div>
        </div>
    </div>
</div>

<div class="grid2">
    <div style="text-align:center;border:1px solid #1565c0;border-radius:6px;padding:12px">
        <div style="font-size:24px;font-weight:bold;color:#1565c0">{{ $totalAlumnos }}</div>
        <div style="font-size:10px;color:#555">Total Alumnos</div>
    </div>
    <div style="text-align:center;border:1px solid #2e7d32;border-radius:6px;padding:12px">
        <div style="font-size:24px;font-weight:bold;color:#2e7d32">{{ $totalAsistencias }}</div>
        <div style="font-size:10px;color:#555">Registros Asistencia</div>
    </div>
    <div style="text-align:center;border:1px solid #e65100;border-radius:6px;padding:12px">
        <div style="font-size:24px;font-weight:bold;color:#e65100">{{ $totalCalificaciones }}</div>
        <div style="font-size:10px;color:#555">Calificaciones</div>
    </div>
</div>

<div class="seccion">
    <h2>Materias ({{ $materias->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th width="40%">Nombre</th>
                <th width="12%">Año</th>
                <th width="16%">Tipo hora</th>
                <th width="12%" class="text-center">Hs/sem</th>
                <th width="12%" class="text-center">Hs/anual</th>
                <th width="8%" class="text-center">% límite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materias as $m)
            <tr>
                <td><strong>{{ $m->nombre }}</strong></td>
                <td>{{ $m->anio ?? '—' }}</td>
                <td>{{ $m->tipohora ?? '—' }}</td>
                <td class="text-center">{{ $m->cargahorariasemanal ?? '—' }}</td>
                <td class="text-center">{{ $m->cargahorariaanual ?? '—' }}</td>
                <td class="text-center">{{ $m->porcentajelimite ?? '—' }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="seccion">
    <h2>Cursos ({{ $cursos->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th width="32%">Curso</th>
                <th width="12%">Año</th>
                <th width="12%">División</th>
                <th width="16%">Turno</th>
                <th width="14%" class="text-center">Alumnos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cursos as $c)
            <tr>
                <td><strong>{{ $c->nombre_completo }}</strong></td>
                <td>{{ $c->anio }}</td>
                <td>{{ $c->division }}</td>
                <td>{{ $c->turno }}</td>
                <td class="text-center"><strong>{{ $c->alumnos->count() }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
