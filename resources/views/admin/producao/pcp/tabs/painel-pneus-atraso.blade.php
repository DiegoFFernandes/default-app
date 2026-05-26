@foreach ($empresa as $emp)
    <div class="tab-pane fade" id="painel-pcp-{{ $emp->CD_EMPRESA }}" role="tabpanel"
        aria-labelledby="tab-painelPCP-{{ $emp->CD_EMPRESA }}">

        <div class="card-body p-0">
            <table id="pneus-lote-pcp-{{ $emp->CD_EMPRESA }}"
                class="table compact table-font-small table-striped table-bordered" style="font-size: 11px;">
            </table>
        </div>
        @if (auth()->user()->hasPermissionTo('editar-pneus-lote-pcp'))
            <div class="card-footer">
                <div class="pt-1">
                    <button class="btn btn-danger btn-xs mb-1 btn-remover-todos-pneus-lote-pcp"
                        data-cd_empresa="{{ $emp->CD_EMPRESA }}">
                        <i class="fa fa-trash"></i>
                        Remover Todos
                    </button>
                    <button class="btn btn-primary btn-xs mb-1 btn-transferir-todos-pneus-lote-pcp"
                        data-cd_empresa="{{ $emp->CD_EMPRESA }}">
                        <i class="fa fa-exchange-alt"></i>
                        Transferir Todos
                    </button>
                </div>
            </div>
        @endif
    </div>
@endforeach
