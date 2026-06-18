<div class="modal fade" id="modal-camera" tabindex="-1" role="dialog"
    aria-labelledby="modal-camera-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark py-2">
                <h6 class="modal-title text-white" id="modal-camera-label">
                    <i class="fas fa-camera mr-2"></i> Capturar Foto
                </h6>
                <button type="button" class="close text-white" id="btn-fechar-camera">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <div class="text-center position-relative bg-dark" style="border-radius:4px;overflow:hidden;">
                    <video id="camera-video" autoplay playsinline muted
                        style="width:100%;max-height:55vh;object-fit:cover;display:block;"></video>
                    <canvas id="scanner-canvas"
                        style="width:100%;max-height:55vh;object-fit:contain;display:none;background:#000;"></canvas>
                </div>

                <canvas id="camera-canvas" style="display:none;"></canvas>

                <div id="camera-erro" class="alert alert-warning mt-2 mb-0" style="display:none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="camera-erro-msg">Câmera não disponível.</span>
                </div>

                <div class="mt-2" id="fotos-capturadas-container" style="display:none;">
                    <label class="small mb-1">Fotos capturadas nesta sessão</label>
                    <div class="d-flex flex-wrap" id="fotos-capturadas"></div>
                </div>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-secondary btn-sm mr-1" id="btn-alternar-camera" title="Alternar câmera frontal/traseira">
                        <i class="fas fa-sync-alt mr-1"></i> Alternar
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" id="btn-scanner" title="Ativar scanner de documento (detecta bordas e corrige perspectiva)">
                        <i class="fas fa-magic mr-1"></i> Scanner
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="btn-capturar">
                        <i class="fas fa-camera mr-1"></i> Capturar
                    </button>
                    <button type="button" class="btn btn-success ml-1" id="btn-usar-fotos" style="display:none;">
                        <i class="fas fa-check mr-1"></i> Usar (<span id="qtd-fotos-capturadas">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
