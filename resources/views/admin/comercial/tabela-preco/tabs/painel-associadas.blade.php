<div class="tab-pane fade" id="painel-associadas" role="tabpanel">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center mb-3 border-bottom pb-2 g-2">
                        <div class="col-12 col-sm-auto">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="checkNaoAssociadas"
                                    name="checkNaoAssociadas">
                                <label class="form-check-label" for="checkNaoAssociadas">
                                    Tabelas não Associadas
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-sm-auto">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="checkAssociadas"
                                    name="checkAssociadas">
                                <label class="form-check-label" for="checkAssociadas">
                                    Tabelas Associadas
                                </label>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered compact table-responsive table-font-small" id="tabela-preco">
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
