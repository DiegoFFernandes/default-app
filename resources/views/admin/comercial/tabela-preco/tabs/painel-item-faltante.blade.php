<div class="tab-pane fade" id="painel-item-faltante" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Itens faturados que estão ausentes na tabela de preço do(s) cliente(s).
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-xs btn-secondary btn-tool-export" data-export="copy" title="Copiar">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                        <button type="button" class="btn btn-xs btn-info btn-tool-export" data-export="csv" title="CSV">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                        <button type="button" class="btn btn-xs btn-success btn-tool-export" data-export="excel" title="Excel">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered compact table-responsive table-font-small"
                        id="tabela-item-faltante">
                    </table>
                </div>
                @include('admin.comercial.tabela-preco.modals.modal-alterar-valor-item-faltante')
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <small class="text-muted" id="info-selecionados-item-faltante">Nenhum item selecionado.</small>
                    <button type="button" class="btn btn-sm btn-success" id="btn-adicionar-todos-item-faltante">
                        <i class="fas fa-plus-circle mr-1"></i> Adicionar Todos Selecionados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
