@extends('pdf._layout')
@section('titulo', 'Declaración Jurada de Horarios')
@section('subtitulo', 'Ciclo ' . $declaracion->ciclo . ' — ' . $declaracion->fechadeclaracion?->format('d/m/Y'))

@section('content')
<div class="grid2">
    <div>
        <div class="dato"><label>Docente</label><span>{{ $docente->name }}</span></div>
        <div class="dato"><label>Ciclo</label><span>{{ $declaracion->ciclo }}</span></div>
    </div>
    <div>
        <div class="dato"><label>Fecha declaración</label><span>{{ $declaracion->fechadeclaracion?->format('d/m/Y') ?? '—' }}</span></div>
        <div class="dato"><label>Estado</label><span>{{ ucfirst($declaracion->estado) }}</span></div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="10%">Día</th>
            <th width="10%">Entrada</th>
            <th width="10%">Salida</th>
            <th width="28%">Establecimiento</th>
            <th width="22%">Curso</th>
            <th width="20%">Materia</th>
        </tr>
    </thead>
    <tbody>
        @forelse($declaracion->items->sortBy('dia') as $item)
        <tr>
            <td>{{ ucfirst($item->dia) }}</td>
            <td>{{ $item->horainicio ? substr($item->horainicio,0,5) : '—' }}</td>
            <td>{{ $item->horafin ? substr($item->horafin,0,5) : '—' }}</td>
            <td>{{ $item->establecimiento?->nombre ?? '—' }}</td>
            <td>{{ $item->curso?->nombre_completo ?? '—' }}</td>
            <td>{{ $item->materia?->nombre ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#999">Sin ítems registrados.</td></tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top:40px;display:flex;justify-content:space-between;font-size:9px">
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Firma del Docente</div>
        <div>{{ $docente->name }}</div>
    </div>
    <div style="text-align:center;width:40%">
        <div style="border-top:1px solid #000;margin-bottom:5px;padding-top:3px">Sello y firma del Directivo</div>
    </div>
</div>
@endsection
