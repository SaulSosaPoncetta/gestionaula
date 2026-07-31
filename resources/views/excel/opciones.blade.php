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
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success" style="font-size:4rem"></i>
                <h5 class="fw-bold mt-3 mb-1">{{ $titulo }}</h5>
                <p class="text-muted small mb-4">Compatible con Excel 97-365, LibreOffice y Google Sheets</p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                    <a href="{{ route('excel.descargar', ['archivo' => $archivo]) }}"
                       class="btn btn-success btn-lg px-5">
                        <i class="bi bi-download me-2"></i>Descargar a la PC
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-lg px-4"
                            data-bs-toggle="modal" data-bs-target="#modalGSheets">
                        <i class="bi bi-grid me-2"></i>Abrir en Google Sheets
                    </button>
                </div>

                <div class="mt-4 p-3 bg-light rounded small text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    El archivo estará disponible durante <strong>1 hora</strong> en el servidor.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Google Sheets --}}
<div class="modal fade" id="modalGSheets" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:#4285F4;color:white">
                <h5 class="modal-title"><i class="bi bi-grid me-2"></i>Abrir en Google Sheets</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold mb-3">Seguí estos pasos:</p>
                <ol style="line-height:2.2">
                    <li>Primero <strong>descargá el archivo</strong> a tu PC</li>
                    <li>Abrí <a href="https://sheets.new" target="_blank" class="btn btn-sm btn-outline-primary ms-1">sheets.new</a></li>
                    <li><kbd>Archivo</kbd> → <kbd>Importar</kbd> → <kbd>Subir</kbd> → seleccioná el archivo</li>
                    <li>Elegí <strong>"Reemplazar hoja"</strong> → <strong>"Importar datos"</strong></li>
                </ol>
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-lightbulb me-1"></i>
                    También podés arrastrar el archivo directo a Google Drive.
                </div>
                <label class="form-label small text-muted">O importar desde URL:</label>
                <div class="input-group input-group-sm">
                    <input type="text" id="linkArchivo" class="form-control font-monospace"
                           value="{{ $url }}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copiarLink()" type="button">
                        <i class="bi bi-clipboard" id="iconoCopiar"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="{{ route('excel.descargar', ['archivo' => $archivo]) }}" class="btn btn-success">
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
    navigator.clipboard.writeText(document.getElementById('linkArchivo').value).then(() => {
        const ic = document.getElementById('iconoCopiar');
        ic.className = 'bi bi-check2';
        setTimeout(() => ic.className = 'bi bi-clipboard', 2000);
    });
}
</script>
@endpush
