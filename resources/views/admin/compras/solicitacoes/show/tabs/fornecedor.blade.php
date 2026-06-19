<div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
    @if($solicitacao->ST_SOLICITACAO === 'RAS')
    {{-- ===== Seleção de fornecedor (rascunho) ===== --}}
    <div class="row mt-1">
        <div class="col-md-5">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Cotação Vencedora <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2" id="sel_id_cotacao" style="width:100%">
                    <option value="">Selecione</option>
                    @foreach($cotacoes ?? [] as $c)
                        <option value="{{ $c->ID_COTACAO }}" {{ $c->ST_SELECIONADA === 'S' ? 'selected' : '' }}>
                            {{ $c->NM_FORNECEDOR }} — R$ {{ number_format($c->VL_TOTAL, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Motivo da Escolha <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_motivo_escolha" maxlength="500"
                    value="{{ collect($cotacoes ?? [])->firstWhere('ST_SELECIONADA', 'S')->DS_MOTIVO_ESCOLHA ?? '' }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3">
            <button id="btn-selecionar-forn" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-check"></i> Confirmar Vencedor
            </button>            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3 text-center">
            <button id="btn-submeter" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane"></i> Enviar para Aprovação
            </button>
        </div>
    </div>
    @else
    {{-- ===== Somente leitura ===== --}}
    @if($cotacaoSelecionada)
    <div class="row mt-2">
        <div class="col-md-4">
            <div class="info-box info-box-custom">
                <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Fornecedor</span>
                    <span class="info-box-number" style="font-size:13px">{{ $cotacaoSelecionada->NM_FORNECEDOR }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box info-box-custom">
                <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Total</span>
                    <span class="info-box-number">R$ {{ number_format($cotacaoSelecionada->VL_TOTAL, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box info-box-custom">
                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Prazo de Entrega</span>
                    <span class="info-box-number">{{ $cotacaoSelecionada->NR_PRAZO_ENTREGA }} dias</span>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-1">
            <div class="bg-light border rounded p-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="text-muted mb-0"><small>Condição de Pagamento</small></label>
                            <p class="font-weight-bold mb-0 mt-1">{{ $cotacaoSelecionada->DS_CONDICAO_PAGAMENTO }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="text-muted mb-0"><small>Motivo da Escolha</small></label>
                            <p class="font-weight-bold mb-0 mt-1">{{ $cotacaoSelecionada->DS_MOTIVO_ESCOLHA ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center text-muted py-4">
        <i class="fas fa-info-circle fa-2x mb-2"></i>
        <p>Nenhum fornecedor selecionado ainda.</p>
    </div>
    @endif
    @endif
</div>
