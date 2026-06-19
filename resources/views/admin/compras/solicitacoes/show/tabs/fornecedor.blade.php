<div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
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
                        <label class="text-muted mb-0"><small>Condição de Pagamento</small></label>
                        <p class="font-weight-bold mb-0 mt-1">{{ $cotacaoSelecionada->DS_CONDICAO_PAGAMENTO }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted mb-0"><small>Motivo da Escolha</small></label>
                        <p class="font-weight-bold mb-0 mt-1">{{ $cotacaoSelecionada->DS_MOTIVO_ESCOLHA ?? '-' }}</p>
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
</div>
