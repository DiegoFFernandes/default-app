<div class="tab-pane fade" id="painel-lotesPCP" role="tabpanel" aria-labelledby="tab-lotesPCP">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lotes em Aberto</h3>
                    @if (auth()->user()->hasPermissionTo('editar-pneus-lote-pcp'))
                        <div class="card-tools m-0">
                            <button class="btn btn-primary btn-xs" id="btn-novo-lote-pcp">
                                <i class="fa fa-plus"></i> Novo Lote
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lote-pcp" class="table compact table-font-small table-striped table-bordered"
                            style="width:100%; font-size: 11px;">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bandas a consumir</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bandas-consumir" class="table compact table-font-small table-striped table-bordered"
                            style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
