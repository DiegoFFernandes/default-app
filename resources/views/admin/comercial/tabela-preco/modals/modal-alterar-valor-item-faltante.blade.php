<div class="modal fade" id="modal-alterar-valor-item-faltante" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Valor do Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label class="small font-weight-bold">Cliente</label>
                            <input type="text" class="form-control form-control-sm" id="modal-alterar-nm-pessoa" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label class="small font-weight-bold">Item</label>
                            <input type="text" class="form-control form-control-sm" id="modal-alterar-ds-item" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-sm mb-0">
                            <label class="small font-weight-bold">Valor Unitário (R$)</label>
                            <input type="number" class="form-control form-control-sm" id="modal-alterar-vl-unitario"
                                step="0.01" min="0" placeholder="0,00">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning btn-sm" id="btn-confirmar-alterar-valor">
                    <i class="fas fa-check mr-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
