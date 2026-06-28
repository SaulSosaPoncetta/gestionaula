@extends('pdf._layout')
@section('titulo', 'Listado de Alumnos — ' . $curso->nombre_completo)

@section('content')
<div class="seccion">
    <div class="grid2">
        <div class="cuadro">
            <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
            <div class="dato"><label>Año / División</label><span>{{ $curso->anio }} — División {{ $curso->division }}</span></div>
            <div class="dato"><label>Turno</label><span>{{ $curso->turno }}</span></div>
        </div>
        <div class="cuadro">
            <div class="dato"><label>Total de alumnos</label><span>{{ $alumnos->count() }}</span></div>
            <div class="dato"><label>Fecha de emisión</label><span>{{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y') }}</span></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">N°</th>
                <th width="30%">Apellido y Nombre</th>
                <th width="14%">DNI</th>
                <th width="16%">Fecha Nac.</th>
                <th width="20%">Teléfono / Email</th>
                <th width="16%">Contacto emergencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $i => $a)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $a->apellido }}, {{ $a->nombre }}</strong></td>
                <td>{{ $a->dni ?? '—' }}</td>
                <td>{{ $a->fechanacimiento ? \Carbon\Carbon::parse($a->fechanacimiento)->format('d/m/Y') : '—' }}</td>
                <td>{{ $a->telefono ?? '' }}{{ $a->email ? ' · ' . $a->email : '' }}</td>
                <td>{{ $a->contactoemergencia ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection