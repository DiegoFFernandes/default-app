<div class="modal fade" id="modal-edit-item" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> Editar Item</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ei_id">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Produto <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm" id="ei_cd_item" style="width:100%"></select>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Quantidade <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="ei_qt_item" min="0.001" step="0.001">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Unidade <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="ei_ds_unidade" maxlength="10">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-1"><small>Observação</small></label>
                            <input type="text" class="form-control form-control-sm" id="ei_ds_observacao" maxlength="300">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning btn-sm" id="btn-salvar-edit-item">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
