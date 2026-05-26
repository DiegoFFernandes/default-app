<div class="modal fade" id="modal-pneus-lote-pcp" tabindex="-1" role="dialog" aria-labelledby="modal-pneus-lote-pcp-label"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <input type="hidden" name="cd_empresa" id="cd_empresa_pneus_lote_pcp">
        <input type="hidden" name="lote" id="lote_pneus_lote_pcp">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title modal-title-lote">Pneu Lote PCP</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm compact table-font-small table-bordered table-striped"
                    id="table-pneus-lote-pcp">
                </table>
            </div>
            @if (auth()->user()->hasPermissionTo('editar-pneus-lote-pcp'))
                <div class="modal-footer">
                    <button class="btn btn-danger btn-xs mb-1 btn-remover-todos-pneus-lote-pcp-detalhes">
                        <i class="fa fa-trash"></i>
                        Remover Todos
                    </button>
                    <button class="btn btn-primary btn-xs mb-1 btn-transferir-todos-pneus-lote-pcp-detalhes">
                        <i class="fa fa-exchange-alt"></i>
                        Transferir Todos
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
