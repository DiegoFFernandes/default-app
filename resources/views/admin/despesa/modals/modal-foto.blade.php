<div class="modal fade" id="modal-fotos" tabindex="-1" role="dialog"
    aria-labelledby="modal-fotos-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="modal-fotos-label">
                    <i class="fas fa-images mr-2"></i> Fotos do Comprovante
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <div id="carousel-fotos" class="carousel slide" data-ride="carousel" data-interval="false">
                    <ol class="carousel-indicators" id="carousel-indicators"></ol>
                    <div class="carousel-inner" id="carousel-fotos-inner"></div>
                    <a class="carousel-control-prev" href="#carousel-fotos" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </a>
                    <a class="carousel-control-next" href="#carousel-fotos" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </a>
                </div>
                <p class="text-center text-muted mt-3" id="sem-fotos" style="display:none;">
                    <i class="fas fa-image fa-2x mb-2 d-block"></i>
                    Nenhuma foto registrada para este comprovante.
                </p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" id="btn-download-foto" class="btn btn-sm btn-outline-secondary" style="display:none;">
                    <i class="fas fa-download mr-1"></i> Baixar Foto
                </button>
            </div>
        </div>
    </div>
</div>
