@extends('pdf._layout')
@section('titulo', 'Listado de Alumnos')
@section('subtitulo', $curso->nombre_completo . ' — ' . $curso->turno)

@section('content')
<div class="grid2">
    <div>
        <div class="dato"><label>Curso</label><span>{{ $curso->nombre_completo }}</span></div>
        <div class="dato"><label>Turno</label><span>{{ $curso->turno }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Total de alumnos</label><span>{{ $alumnos->count() }}</span></div>
        <div class="dato"><label>Año lectivo</label><span>{{ \Carbon\Carbon::now()->year }}</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="4%">N°</th>
            <th width="28%">Apellido y Nombre</th>
            <th width="13%">DNI</th>
            <th width="13%">Fecha Nac.</th>
            <th width="18%">Teléfono</th>
            <th width="24%">Email / Contacto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alumnos as $i => $a)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td><strong>{{ $a->apellido }}</strong>, {{ $a->nombre }}</td>
            <td>{{ $a->dni ?? '—' }}</td>
            <td>{{ $a->fechanacimiento ? \Carbon\Carbon::parse($a->fechanacimiento)->format('d/m/Y') : '—' }}</td>
            <td>{{ $a->telefono ?? '—' }}</td>
            <td style="font-size:9px">{{ $a->email ?? '' }} {{ $a->contactoemergencia ? '· '.$a->contactoemergencia : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
