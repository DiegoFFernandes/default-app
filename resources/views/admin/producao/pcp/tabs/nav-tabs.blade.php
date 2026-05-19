<div class="card-header p-0 d-flex justify-content-between align-items-center">
    <div class="flex-grow-1">
        <ul class="nav nav-tabs border-bottom-0" id="tab-pcp" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="tab-lotesPCP" data-toggle="pill" href="#painel-lotesPCP" role="tab"
                    aria-controls="painel-lotesPCP" aria-selected="false">
                    Lotes PCP
                </a>
            </li>
            @foreach ($empresa as $emp)
                <li class="nav-item">
                    <a class="nav-link" id="tab-painelPCP-{{ $emp->CD_EMPRESA }}" data-toggle="pill"
                        href="#painel-pcp-{{ $emp->CD_EMPRESA }}" role="tab"
                        aria-controls="painel-pcp-{{ $emp->CD_EMPRESA }}" aria-selected="false"
                        data-empresa="{{ $emp->CD_EMPRESA }}">
                        {{ $emp->NM_EMPRESA }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-tools d-flex align-items-center">
        <span id="minutosParaAtualizacao" class="badge badge-primary"><i class="fa fa-clock" aria-hidden="true"></i>
            05:00</span>
        <div class="custom-control custom-checkbox ml-2">
            <input class="custom-control-input" type="checkbox" id="atualizarTela">
            <label for="atualizarTela" class="custom-control-label">Atualizar</label>
        </div>
    </div>
</div>
