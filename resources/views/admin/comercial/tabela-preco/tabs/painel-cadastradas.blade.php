<div class="tab-pane fade" id="painel-cadastradas" role="tabpanel">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card card-primary">
                <x-loading-card />
                <div class="card-body">
                    <div id="card-container"></div>
                    <table class="table table-bordered compact table-font-small"
                        id="tabela-preco-cadastradas">
                    </table>
                </div>
            </div>
        </div>
        @include('admin.comercial.tabela-preco.modals.modal-item-tabela-preco', [
            'idModal' => 'modal-item-tab-preco-cadastradas',
            'idTabelaItem' => 'table-item-tab-preco-cadastradas',
        ])
        @include('admin.comercial.tabela-preco.modals.modal-vincular-tabela-preco', [
            'idModal' => 'modal-vincular-tab-preco-pessoas',
            'idPessoa' => 'cd_pessoa_multi',
            'idTabelaPreco' => 'cd_tabela_preco',
            'dsTabelaPreco' => 'ds_tabela_preco',
            'idBtnSalvarVinculo' => 'btn-salvar-vinculo',
        ])
    </div>
</div>
