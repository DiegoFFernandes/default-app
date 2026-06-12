<div class="tab-pane fade" id="painel-associadas" role="tabpanel">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mr-3">
                        <i class="fas fa-filter mr-1 text-muted"></i> Filtros
                    </h3>
                    <div class="filtros-associadas">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkNaoAssociadas"
                                name="checkNaoAssociadas">
                            <label class="form-check-label" for="checkNaoAssociadas">
                                <i class="fas fa-unlink mr-1 text-warning"></i> Não Associadas
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkAssociadas"
                                name="checkAssociadas">
                            <label class="form-check-label" for="checkAssociadas">
                                <i class="fas fa-link mr-1 text-success"></i> Associadas
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered compact table-font-small" id="tabela-preco">
                    </table>
                </div>
            </div>
        </div>
        @include('admin.comercial.tabela-preco.modals.modal-vincular-tabela-preco', [
            'idModal' => 'modal-vincular-tab-preco-pessoas2',
            'idPessoa' => 'cd_pessoa_multi2',
            'idTabelaPreco' => 'cd_tabela_preco2',
            'dsTabelaPreco' => 'ds_tabela_preco2',
            'idBtnSalvarVinculo' => 'btn-salvar-vinculo2',
        ])
        @include('admin.comercial.tabela-preco.modals.modal-item-tabela-preco', [
            'idModal' => 'modal-item-tab-preco',
            'idTabelaItem' => 'table-item-tab-preco',
        ])
    </div>
</div>
