<div class="modal fade" id="modal-editar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning py-2">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-edit mr-2"></i> Editar Comprovante
                </h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label class="small" for="edit-tp_despesa">Tipo de Despesa <span class="text-danger">*</span></label>
                    <select id="edit-tp_despesa" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="ALI">Alimentação</option>
                        <option value="COM">Combustível</option>
                        <option value="HOS">Hospedagem</option>
                        <option value="PED">Pedágio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="small" for="edit-vl_consumido">Valor <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="text" id="edit-vl_consumido" class="form-control money" placeholder="0,00">
                    </div>
                </div>
                <div class="form-group">
                    <label class="small" for="edit-dt_despesa">Data da Despesa <span class="text-danger">*</span></label>
                    <input type="date" id="edit-dt_despesa" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <label class="small" for="edit-ds_observacao">Observação</label>
                    <textarea id="edit-ds_observacao" class="form-control" rows="2"
                        placeholder="Observações adicionais..."></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="btn-salvar-edicao">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
