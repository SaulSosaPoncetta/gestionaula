@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h4 class="fw-bold"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Archivo listo</h4>
        <p class="text-muted">{{ $titulo }}</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('excel.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-success" style="font-size:4rem"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $titulo }}</h5>
                <p class="text-muted small mb-4">
                    Archivo .xls compatible con Excel 97-365, LibreOffice y Google Sheets
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">

                    {{-- Descargar --}}
                    <a href="{{ route('excel.descargar', ['archivo' => $archivo]) }}"
                       class="btn btn-success btn-lg px-5">
                        <i class="bi bi-download me-2"></i>Descargar a la PC
                    </a>

                    {{-- Google Sheets --}}
                    <button type="button" class="btn btn-outline-primary btn-lg px-4"
                            data-bs-toggle="modal" data-bs-target="#modalGoogleSheets">
                        <i class="bi bi-grid me-2"></i>Abrir en Google Sheets
                    </button>

                </div>

                <div class="mt-4 p-3 bg-light rounded text-start small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    El archivo estará disponible por <strong>1 hora</strong>.
                    Después se elimina automáticamente del servidor.
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Google Sheets --}}
<div class="modal fade" id="modalGoogleSheets" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:#4285F4;color:white">
                <h5 class="modal-title">
                    <i class="bi bi-grid me-2"></i>Abrir en Google Sheets
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold mb-3">Seguí estos pasos:</p>
                <ol class="mb-3" style="line-height:2">
                    <li>
                        Primero <strong>descargá el archivo</strong> a tu PC haciendo click en
                        <em>"Descargar a la PC"</em>
                    </li>
                    <li>
                        Abrí <strong>Google Sheets</strong> en tu navegador:
                        <a href="https://sheets.new" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Abrir sheets.new
                        </a>
                    </li>
                    <li>
                        En Google Sheets: <br>
                        <kbd>Archivo</kbd> → <kbd>Importar</kbd> → <kbd>Subir</kbd>
                        → seleccioná el archivo descargado
                    </li>
                    <li>
                        Elegí <strong>"Reemplazar hoja de cálculo"</strong> y hacé click en
                        <strong>"Importar datos"</strong>
                    </li>
                </ol>

                <div class="alert alert-info py-2 small">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Tip:</strong> También podés arrastrar el archivo directamente
                    sobre la pantalla de Google Drive para subirlo automáticamente.
                </div>

                <div class="border rounded p-2 bg-light">
                    <div class="small text-muted mb-1">O copiá el enlace directo del archivo:</div>
                    <div class="input-group input-group-sm">
                        <input type="text" id="linkArchivo" class="form-control font-monospace"
                               value="{{ $url }}" readonly>
                        <button class="btn btn-outline-secondary" onclick="copiarLink()" type="button">
                            <i class="bi bi-clipboard" id="iconoCopiar"></i>
                        </button>
                    </div>
                    <div class="small text-muted mt-1">
                        Podés usar este enlace en la opción <em>"Importar desde URL"</em> de Google Sheets.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="{{ route('excel.descargar', ['archivo' => $archivo]) }}"
                   class="btn btn-success">
                    <i class="bi bi-download me-1"></i>Descargar primero
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copiarLink() {
    const input = document.getElementById('linkArchivo');
    const icono = document.getElementById('iconoCopiar');
    navigator.clipboard.writeText(input.value).then(() => {
        icono.className = 'bi bi-check2';
        setTimeout(() => { icono.className = 'bi bi-clipboard'; }, 2000);
    });
}
</script>
@endpush
