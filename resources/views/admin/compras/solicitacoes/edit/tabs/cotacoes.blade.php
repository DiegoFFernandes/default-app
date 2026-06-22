<style>
    .custom-file-sm .custom-file-input,
    .custom-file-sm .custom-file-label {
        height: calc(1.5em + 0.5rem + 2px);
        font-size: 0.875rem;
        line-height: 1.5;
        padding: 0.25rem 0.5rem;
    }
    .custom-file-sm .custom-file-label::after {
        height: calc(1.5em + 0.5rem);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        content: "Buscar Orçamento";
    }
</style>
<div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
    <div class="row mt-1 mb-1">
        <div class="col-md-4">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Fornecedor <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2-ajax" id="cd_fornecedor" style="width:100%"
                    data-url="{{ route('compras.search-fornecedor') }}"
                    data-placeholder="Buscar fornecedor (mín. 3 caracteres)"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Prazo (dias) <span class="text-danger">*</span></small></label>
                <input type="number" class="form-control form-control-sm" id="nr_prazo" min="1" placeholder="0">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Condição de Pagamento <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_condicao" maxlength="200" placeholder="Ex: 30 dias">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Forma Pgto. <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm" id="cd_formapagto">
                    <option value="">Selecione</option>
                    <option value="BL">Boleto</option>
                    <option value="DI">Dinheiro</option>
                    <option value="CH">Cheque</option>
                    <option value="PX">Pix</option>
                    <option value="CC">Cartão de Credito</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Valor Total (R$) <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm money-mask" id="vl_total_cot" placeholder="0,00">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Obs.</small></label>
                <input type="text" class="form-control form-control-sm" id="ds_obs_cot" maxlength="500">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Orçamento PDF <span class="text-danger">*</span></small></label>
                <div class="custom-file custom-file-sm">
                    <input type="file" class="custom-file-input" id="doc_orcamento" accept="application/pdf">
                    <label class="custom-file-label" for="doc_orcamento">Selecionar PDF...</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">
            <button id="btn-add-cot" class="btn btn-info btn-sm">
                <i class="fas fa-plus"></i> Adicionar Cotação
            </button>
        </div>
    </div>
    <table class="table table-striped table-bordered compact table-font-small" id="table-cotacoes" style="width:100%">
        <thead>
            <tr>
                <th>Fornecedor</th>
                <th>Prazo</th>
                <th>Condição</th>
                <th>Forma Pgto.</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
</div>
