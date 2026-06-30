<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('titulo') — GestiónAula</title>
<style>
/* ── Pantalla ──────────────────────────────────────────────────── */
body { font-family: Arial, sans-serif; font-size: 12px; color: #222; margin: 0; background: #f0f2f5; }
.barra-impresion {
    position: fixed; top: 0; left: 0; right: 0; z-index: 999;
    background: #1565c0; color: white; padding: 8px 20px;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
.barra-impresion h1 { font-size: 15px; margin: 0; }
.barra-impresion .acciones { display: flex; gap: 10px; }
.btn-imprimir {
    background: white; color: #1565c0; border: none; padding: 6px 18px;
    border-radius: 5px; font-size: 13px; font-weight: bold; cursor: pointer;
}
.btn-volver {
    background: rgba(255,255,255,.2); color: white; border: 1px solid rgba(255,255,255,.5);
    padding: 6px 14px; border-radius: 5px; font-size: 12px; cursor: pointer; text-decoration: none;
}
.pagina {
    max-width: 210mm; margin: 60px auto 30px; background: white;
    padding: 20mm 18mm; box-shadow: 0 2px 16px rgba(0,0,0,.15);
    border-radius: 4px;
}
/* ── Estilos comunes del documento ─────────────────────────────── */
.doc-header { border-bottom: 3px solid #1565c0; margin-bottom: 14px; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: flex-end; }
.doc-header .titulo { font-size: 16px; font-weight: bold; color: #1565c0; }
.doc-header .meta { font-size: 10px; color: #666; text-align: right; line-height: 1.6; }
.doc-footer { border-top: 1px solid #ccc; margin-top: 16px; padding-top: 6px; font-size: 9px; color: #999; display: flex; justify-content: space-between; }
.seccion { margin-bottom: 14px; }
.seccion h2 { font-size: 12px; font-weight: bold; color: #1565c0; border-bottom: 1px solid #1565c0; padding-bottom: 3px; margin-bottom: 8px; }
.grid2 { display: flex; gap: 14px; margin-bottom: 12px; }
.grid2 > div { flex: 1; border: 1px solid #ddd; border-radius: 4px; padding: 8px; }
.dato { margin-bottom: 5px; }
.dato label { font-size: 9px; color: #888; display: block; }
.dato span { font-size: 11px; font-weight: bold; }
table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 8px; }
th { background: #1565c0; color: white; padding: 5px 7px; text-align: left; font-size: 10px; }
td { padding: 4px 7px; border-bottom: 1px solid #eee; vertical-align: top; }
tr:nth-child(even) td { background: #f5f8ff; }
.text-center { text-align: center !important; }
.text-right  { text-align: right !important; }
.nota-alta   { color: #1b5e20; font-weight: bold; }
.nota-media  { color: #e65100; font-weight: bold; }
.nota-baja   { color: #b71c1c; font-weight: bold; }
.badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 9px; font-weight: bold; }
.badge-green  { background: #c8e6c9; color: #1b5e20; }
.badge-red    { background: #ffcdd2; color: #b71c1c; }
.badge-yellow { background: #fff9c4; color: #795548; }
.badge-blue   { background: #bbdefb; color: #0d47a1; }
.alerta { background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 8px 12px; font-size: 10px; margin-bottom: 10px; }

/* ── Impresión ─────────────────────────────────────────────────── */
@media print {
    body { background: white; }
    .barra-impresion { display: none !important; }
    .pagina { margin: 0; padding: 10mm; box-shadow: none; max-width: 100%; border-radius: 0; }
    thead { display: table-header-group; }
    tr { page-break-inside: avoid; }
    .pagebreak { page-break-before: always; }
    a { color: inherit; text-decoration: none; }
}
</style>
</head>
<body>

<div class="barra-impresion">
    <h1>🖨 @yield('titulo')</h1>
    <div class="acciones">
        <a href="{{ url()->previous() }}" class="btn-volver">← Volver</a>
        <button class="btn-imprimir" onclick="window.print()">⬇ Imprimir / Guardar PDF</button>
    </div>
</div>

<div class="pagina">

    <div class="doc-header">
        <div>
            <div class="titulo">@yield('titulo')</div>
            <div style="font-size:11px;color:#555;margin-top:3px">@yield('subtitulo')</div>
        </div>
        <div class="meta">
            <div>GestiónAula</div>
            <div>{{ auth()->user()->name }}</div>
            <div>{{ \Carbon\Carbon::now('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @yield('content')

    <div class="doc-footer">
        <span>GestiónAula © {{ date('Y') }}</span>
        <span>@yield('titulo')</span>
        <span>Página <span class="page-num"></span></span>
    </div>

</div>

<script>
// Numerar páginas (aproximado para pantalla)
document.querySelectorAll('.page-num').forEach((el, i) => el.textContent = i + 1);
</script>
</body>
</html>
