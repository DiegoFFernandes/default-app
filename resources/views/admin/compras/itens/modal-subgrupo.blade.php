{{-- Modal de cadastro/edição de subgrupo (COMPRA_SUBGRUPO). HTML apenas. --}}
<div class="modal fade" id="modal-subgrupo" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-subgrupo-title">Novo Subgrupo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sg_cd">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Descrição <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="sg_ds_subgrupo" maxlength="100"
                        placeholder="Ex: MATERIAL ELÉTRICO">
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-subgrupo" class="btn btn-primary btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
