<div class="modal fade" id="modal-parametros-cogs" tabindex="-1" role="dialog"
     aria-labelledby="modal-parametros-cogs-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-parametros-cogs-label">
                    <i class="fas fa-cogs mr-2 text-secondary"></i> Parâmetros — Itens Faltantes
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="param-cogs-if-loading" class="text-center py-4">
                    <i class="fas fa-sync-alt fa-spin mr-1 text-muted"></i>
                    <span class="text-muted small">Carregando...</span>
                </div>

                <div id="param-cogs-if-content" style="display:none;">
                    <p class="text-muted small mb-3 border-left border-info pl-2">
                        Séries de nota consideradas na listagem de itens faltantes.
                    </p>
                    <div class="row" id="serie-checks"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-salvar-parametros-item-faltante">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
            </div>

        </div>
    </div>
</div>
