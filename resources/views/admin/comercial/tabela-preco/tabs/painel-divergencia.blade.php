<div class="tab-pane fade" id="painel-divergencia" role="tabpanel">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Cliente associados a 2 ou mais Tabelas, vincular somente 1.
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered compact table-responsive table-font-small"
                        id="tabela-divergencia" >
                    </table>
                </div>
            </div>
        </div>
        @include('admin.comercial.tabela-preco.modals.modal-item-tabela-preco', [
            'idModal' => 'modal-item-tab-preco-divergencia',
            'idTabelaItem' => 'table-item-tab-preco-divergencia',
        ])
    </div>
</div>
