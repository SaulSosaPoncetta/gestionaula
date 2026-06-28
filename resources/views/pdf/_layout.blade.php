<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #222; }
    .pdf-header { background: #1565c0; color: white; padding: 10px 16px; margin-bottom: 14px; border-radius: 0; }
    .pdf-header h1 { font-size: 14px; font-weight: bold; }
    .pdf-header .sub { font-size: 9px; opacity: 0.85; margin-top: 2px; }
    .pdf-footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding: 4px 16px; display: flex; justify-content: space-between; }
    .seccion { margin: 0 16px 12px 16px; }
    .seccion h2 { font-size: 11px; font-weight: bold; color: #1565c0; border-bottom: 1px solid #1565c0; padding-bottom: 3px; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; font-size: 9px; }
    th { background: #1565c0; color: white; padding: 5px 6px; text-align: left; }
    td { padding: 4px 6px; border-bottom: 1px solid #eee; vertical-align: top; }
    tr:nth-child(even) td { background: #f5f8ff; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .badge-green  { background: #c8e6c9; color: #1b5e20; }
    .badge-red    { background: #ffcdd2; color: #b71c1c; }
    .badge-yellow { background: #fff9c4; color: #795548; }
    .badge-blue   { background: #bbdefb; color: #0d47a1; }
    .badge-grey   { background: #eeeeee; color: #555; }
    .nota-alta    { color: #1b5e20; font-weight: bold; }
    .nota-media   { color: #e65100; font-weight: bold; }
    .nota-baja    { color: #b71c1c; font-weight: bold; }
    .text-center  { text-align: center; }
    .text-right   { text-align: right; }
    .pagebreak    { page-break-after: always; }
    .grid2 { display: flex; gap: 16px; margin: 0 16px 12px 16px; }
    .grid2 > div { flex: 1; }
    .dato { margin-bottom: 5px; }
    .dato label { font-size: 8px; color: #888; display: block; }
    .dato span { font-size: 10px; font-weight: bold; }
    .cuadro { border: 1px solid #ccc; border-radius: 4px; padding: 8px; }
</style>
</head>
<body>

<div class="pdf-header">
    <h1>@yield('titulo')</h1>
    <div class="sub">
        Docente: {{ auth()->user()->name }} &nbsp;|&nbsp;
        GestiónAula &nbsp;|&nbsp;
        Generado: {{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }}
    </div>
</div>

@yield('content')

<div class="pdf-footer">
    <span>GestiónAula © {{ date('Y') }}</span>
    <span>@yield('titulo')</span>
    <span class="page"></span>
</div>

</body>
</html>