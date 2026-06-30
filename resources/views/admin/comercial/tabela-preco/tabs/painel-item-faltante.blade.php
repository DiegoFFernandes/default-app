<div class="tab-pane fade" id="painel-item-faltante" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Itens faturados que estão ausentes na tabela de preço do(s) cliente(s).
                    </h3>
                    <div class="card-tools">
                        @role('admin')
                            <button type="button" class="btn btn-tool btn-parametros-cogs" data-toggle="modal"
                                data-target="#modal-parametros-cogs" title="Parâmetros Itens faltantes">
                                <i class="fas fa-cogs"></i>
                            </button>
                        @endrole
                        <button type="button" class="btn btn-xs btn-secondary btn-tool-export" data-export="copy"
                            title="Copiar">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                        <button type="button" class="btn btn-xs btn-info btn-tool-export" data-export="csv"
                            title="CSV">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                        <button type="button" class="btn btn-xs btn-success btn-tool-export" data-export="excel"
                            title="Excel">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-2 pb-2 border-bottom">
                        <button type="button" class="btn btn-xs btn-success" id="btn-adicionar-todos-item-faltante">
                            <i class="fas fa-plus mr-1"></i> Adicionar
                        </button>
                        <button type="button" class="btn btn-xs btn-danger ml-2" id="btn-ignoar-todos-item-faltante">
                            <i class="fas fa-minus mr-1"></i> Ignorar
                        </button>

                        <small class="ml-2" id="info-selecionados-item-faltante">
                            <span class="badge badge-secondary"></span>
                        </small>
                    </div>
                    <table class="table table-bordered compact table-responsive table-font-small"
                        id="tabela-item-faltante">
                    </table>
                </div>
                @include('admin.comercial.tabela-preco.modals.modal-alterar-valor-item-faltante')
                @include('admin.comercial.tabela-preco.modals.modal-parametros-cogs')
            </div>
        </div>
    </div>
</div>
