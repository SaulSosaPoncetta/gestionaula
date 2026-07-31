@extends('pdf._layout')
@section('titulo', 'Contenidos por Unidad')
@section('subtitulo', $materia->nombre)

@section('content')
<div class="dato"><label>Materia</label><span>{{ $materia->nombre }}</span></div>
@forelse($contenidos->sortKeys() as $unidad => $temas)
<div class="seccion">
    <div style="background:#1565c0;color:white;padding:5px 10px;font-weight:bold;font-size:11px;border-radius:3px;margin-bottom:6px">
        UNIDAD {{ $unidad }}
    </div>
    @foreach($temas as $i => $tema)
    <div style="margin-left:10px;margin-bottom:8px">
        <div style="font-weight:bold;font-size:11px;border-left:3px solid #42a5f5;padding-left:8px;margin-bottom:3px">
            {{ $i+1 }}. {{ $tema->tema }}
        </div>
        @if($tema->subtemas->isNotEmpty())
        <ul style="margin:0 0 4px 24px;padding:0;font-size:10px;color:#444">
            @foreach($tema->subtemas->sortBy('orden') as $sub)
            <li>{{ $sub->subtema }}</li>
            @endforeach
        </ul>
        @endif
        @if($tema->observacion)
        <div style="font-size:9px;color:#888;margin-left:12px;font-style:italic">{{ $tema->observacion }}</div>
        @endif
    </div>
    @endforeach
</div>
@empty
<div class="alerta">No hay contenidos registrados para esta materia.</div>
@endforelse
@endsection
