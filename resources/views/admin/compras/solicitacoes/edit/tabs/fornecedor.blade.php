<div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
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
            @php $motivoAtual = collect($cotacoes ?? [])->firstWhere('ST_SELECIONADA', 'S')->DS_MOTIVO_ESCOLHA ?? ''; @endphp
            <div class="form-group mb-2">
                <label class="mb-1"><small>Motivo da Escolha <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm" id="ds_motivo_escolha">
                    <option value="">Selecione</option>
                    @php
                        $motivos = [
                            'Menor Valor',
                            'Melhor Condição de Pagamento',
                            'Melhor Negociação',
                            'Melhor Prazo de Entrega',
                            'Qualidade Superior do Produto/Serviço',
                            'Disponibilidade Imediata em Estoque',
                            'Único Fornecedor Disponível',
                            'Melhor Suporte Técnico / Pós-venda',
                            'Prazo de Garantia',
                            'Relacionamento Comercial',
                        ];
                    @endphp
                    @foreach($motivos as $motivo)
                        <option value="{{ $motivo }}" {{ $motivoAtual === $motivo ? 'selected' : '' }}>
                            {{ $motivo }}
                        </option>
                    @endforeach
                    @if($motivoAtual && !in_array($motivoAtual, $motivos))
                        <option value="{{ $motivoAtual }}" selected>{{ $motivoAtual }}</option>
                    @endif
                </select>
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
    @if(isset($solicitacao) && $solicitacao->ST_SOLICITACAO === 'ANA')
    <div class="row">
        <div class="col-md-12 pt-3 text-center">
            <button id="btn-submeter" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane"></i> Enviar para Aprovação
            </button>
        </div>
    </div>
    @endif
</div>
