<div class="modal fade" id="modal-edit-cot">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Cotação</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id_cotacao">
                <input type="hidden" id="edit_id_sol_cot">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Fornecedor</small></label>
                    <select class="form-control form-control-sm select2-ajax" id="edit_cd_fornecedor" style="width:100%"
                        data-url="{{ route('compras.search-fornecedor') }}"
                        data-placeholder="Buscar fornecedor"></select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Prazo (dias)</small></label>
                    <input type="number" class="form-control form-control-sm" id="edit_nr_prazo" min="1">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Condição de Pagamento</small></label>
                    <input type="text" class="form-control form-control-sm" id="edit_ds_condicao" maxlength="200">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Forma de Pagamento</small></label>
                    <select class="form-control form-control-sm" id="edit_cd_formapagto">
                        <option value="">Selecione</option>
                        <option value="BL">Boleto</option>
                        <option value="DI">Dinheiro</option>
                        <option value="CH">Cheque</option>
                        <option value="PX">Pix</option>
                        <option value="CC">Cartão de Crédito</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Valor Total (R$)</small></label>
                    <input type="text" class="form-control form-control-sm money-mask" id="edit_vl_total">
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Observação</small></label>
                    <input type="text" class="form-control form-control-sm" id="edit_ds_obs" maxlength="500">
                </div>
                <div class="form-group mb-0">
                    <label class="mb-1"><small>Orçamento PDF <span class="text-muted">(deixe em branco para manter o atual)</span></small></label>
                    <div id="edit_doc_atual" class="mb-1" style="display:none">
                        <a id="edit_doc_link" href="#" target="_blank" class="text-info small">
                            <i class="fas fa-file-pdf mr-1"></i>Ver orçamento atual
                        </a>
                    </div>
                    <div class="custom-file custom-file-sm">
                        <input type="file" class="custom-file-input" id="edit_doc_orcamento" accept="application/pdf">
                        <label class="custom-file-label" for="edit_doc_orcamento">Selecionar novo PDF...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-save-edit-cot" class="btn btn-warning btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
