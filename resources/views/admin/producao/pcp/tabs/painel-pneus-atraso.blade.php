@foreach ($empresa as $emp)
    <div class="tab-pane fade" id="painel-pcp-{{ $emp->CD_EMPRESA }}" role="tabpanel"
        aria-labelledby="tab-painelPCP-{{ $emp->CD_EMPRESA }}">

        <div class="card-body p-0">
            <table id="pneus-lote-pcp-{{ $emp->CD_EMPRESA }}"
                class="table compact table-font-small table-striped table-bordered" style="font-size: 11px;">
            </table>
        </div>
        @if (auth()->user()->hasPermissionTo('editar-pneus-lote-pcp'))
            <div class="card-footer py-2">
                <div class="d-flex align-items-center" style="gap:6px;">
                    <button class="btn btn-danger btn-xs btn-remover-todos-pneus-lote-pcp"
                        data-cd_empresa="{{ $emp->CD_EMPRESA }}">
                        <i class="fa fa-trash"></i> Remover Todos
                    </button>
                    <button class="btn btn-primary btn-xs btn-transferir-todos-pneus-lote-pcp"
                        data-cd_empresa="{{ $emp->CD_EMPRESA }}">
                        <i class="fa fa-exchange-alt"></i> Transferir Todos
                    </button>
                    <span class="badge badge-warning pcp-count-badge"
                          id="pcp-count-badge-{{ $emp->CD_EMPRESA }}"
                          style="display:none; font-size:0.8rem;"></span>
                </div>
            </div>
        @endif
    </div>
@endforeach
