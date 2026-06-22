<div class="tab-pane fade show active" id="pane-cabecalho" role="tabpanel">
    <div class="bg-light border rounded p-2 mt-1">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Empresa</small></label>
                    <p class="font-weight-bold mb-0 mt-1">{{ $solicitacao->NM_EMPRESA }}</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Data</small></label>
                    <p class="font-weight-bold mb-0 mt-1">
                        {{ \Carbon\Carbon::parse($solicitacao->DT_SOLICITACAO)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Urgência</small></label>
                    @php $urgMap = ['I' => ['danger','Imediato'], 'U' => ['warning','Urgente'], 'N' => ['secondary','Necessário']]; @endphp
                    <p class="mb-0 mt-1">
                        <span class="badge badge-{{ ($urgMap[$solicitacao->ST_URGENCIA ?? 'N'])[0] }}">
                            {{ ($urgMap[$solicitacao->ST_URGENCIA ?? 'N'])[1] }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Status</small></label>
                    <p class="mb-0 mt-1">
                        <span class="badge badge-{{ $cor }}">{{ $label }}</span>
                    </p>
                </div>
            </div>
            @if(!empty($solicitacao->TP_SOLICITACAO))
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Tipo</small></label>
                    <p class="mb-0 mt-1">
                        <span class="badge badge-{{ $solicitacao->TP_SOLICITACAO === 'C' ? 'danger' : 'info' }}">
                            {{ $solicitacao->TP_SOLICITACAO === 'C' ? 'Corretiva' : 'Preventiva' }}
                        </span>
                    </p>
                </div>
            </div>
            @endif
            @if(!empty($solicitacao->NR_PLACA))
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Placa</small></label>
                    <p class="font-weight-bold mb-0 mt-1">{{ $solicitacao->NR_PLACA }}</p>
                </div>
            </div>
            @endif
            @if(!empty($solicitacao->NR_KM))
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>KM Atual</small></label>
                    <p class="font-weight-bold mb-0 mt-1">{{ number_format($solicitacao->NR_KM, 0, ',', '.') }}</p>
                </div>
            </div>
            @endif
            @if($solicitacao->VL_TOTAL)
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Valor Total</small></label>
                    <p class="font-weight-bold text-success mb-0 mt-1">
                        R$ {{ number_format($solicitacao->VL_TOTAL, 2, ',', '.') }}
                    </p>
                </div>
            </div>
            @endif
            @if(!empty($solicitacao->DS_CENTROCUSTO))
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Centro de Resultado</small></label>
                    <p class="font-weight-bold mb-0 mt-1">{{ $solicitacao->DS_CENTROCUSTO }}</p>
                </div>
            </div>
            @endif
            <div class="col-md-8">
                <div class="form-group mb-0">
                    <label class="text-muted mb-0"><small>Justificativa</small></label>
                    <p class="mb-0 mt-1">{{ $solicitacao->DS_JUSTIFICATIVA }}</p>
                </div>
            </div>
            @if($solicitacao->DS_OBSERVACAO)
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="text-muted mb-0"><small>Observação</small></label>
                    <p class="mb-0 mt-1">{{ $solicitacao->DS_OBSERVACAO }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Saldo do ciclo orçamentário --}}
    @if(!empty($saldoCiclo))
    <div class="alert alert-light border mt-2 mb-2 py-2 px-3">
        <small class="text-muted">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ $saldoCiclo->ds_centrocusto }} — Ciclo:
            <strong class="text-dark">{{ $saldoCiclo->dt_inicio }} a {{ $saldoCiclo->dt_fim }}</strong>
        </small>
        <div class="row mt-1">
            <div class="col-4 text-center">
                <small class="d-block text-muted">Orçamento</small>
                <span class="font-weight-bold text-primary">R$ {{ $saldoCiclo->vl_orcado_fmt }}</span>
            </div>
            <div class="col-4 text-center">
                <small class="d-block text-muted">Utilizado</small>
                <span class="font-weight-bold text-warning">R$ {{ $saldoCiclo->vl_utilizado_fmt }}</span>
            </div>
            <div class="col-4 text-center">
                <small class="d-block text-muted">Saldo</small>
                <span class="font-weight-bold {{ $saldoCiclo->vl_saldo >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $saldoCiclo->vl_saldo < 0 ? '− ' : '' }}R$ {{ $saldoCiclo->vl_saldo_fmt }}
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline de aprovação --}}
    @if(count($etapas) > 0 || in_array($solicitacao->ST_SOLICITACAO, ['ANA', 'APR', 'APC', 'REP', 'CAN', 'FIN']))
    @php $todasAprovadas = count($etapas) > 0 && collect($etapas)->every(fn($e) => $e->ST_ETAPA === 'APR'); @endphp
    <hr class="mt-1 mb-2">
    <h6 class="text-muted mb-2"><i class="fas fa-stream mr-1"></i> Fluxo de Aprovação</h6>
    <div class="timeline timeline-inverse">

        @if(in_array($solicitacao->ST_SOLICITACAO, ['ANA', 'APR', 'APC', 'REP', 'CAN', 'FIN']))
        <div>
            <i class="fas fa-search bg-info"></i>
            <div class="timeline-item">
                <h3 class="timeline-header">
                    Análise de Compra
                    @if($solicitacao->ST_SOLICITACAO === 'ANA')
                        <span class="badge badge-info ml-1">Aguardando Análise</span>
                    @else
                        <span class="badge badge-success ml-1"><i class="fas fa-check mr-1"></i>Concluída</span>
                    @endif
                </h3>
            </div>
        </div>
        @endif

        @foreach($etapas as $etapa)
        @php
            $etapaMap = [
                'PEN' => ['secondary', 'Pendente', 'clock'],
                'APR' => ['success',   'Aprovado', 'check'],
                'REP' => ['danger',    'Reprovado','times'],
            ];
            [$ec, $el, $ei] = $etapaMap[$etapa->ST_ETAPA] ?? ['secondary', $etapa->ST_ETAPA, 'circle'];
        @endphp
        <div>
            <i class="fas fa-{{ $ei }} bg-{{ $ec }}"></i>
            <div class="timeline-item">
                <span class="time text-muted">
                    <i class="fas fa-clock mr-1"></i>{{ $etapa->DT_ACAO ?? 'Aguardando' }}
                </span>
                <h3 class="timeline-header">
                    Etapa {{ $etapa->NR_ORDEM }} — {{ $etapa->DS_CARGO }} - {{ explode(' ', $etapa->NM_APROVADOR ?? '')[0] }}
                    <span class="badge badge-{{ $ec }} ml-1">{{ $el }}</span>
                </h3>
                @if($etapa->DS_OBSERVACAO)
                <div class="timeline-body text-muted">{{ $etapa->DS_OBSERVACAO }}</div>
                @endif
            </div>
        </div>
        @endforeach

        @if($todasAprovadas)
        <div>
            <i class="fas fa-shopping-cart bg-success"></i>
            <div class="timeline-item">
                <h3 class="timeline-header">
                    <span class="text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Compra Autorizada
                    </span>
                </h3>
            </div>
        </div>
        @if($solicitacao->ST_SOLICITACAO === 'FIN')
        <div>
            <i class="fas fa-flag-checkered bg-success"></i>
            <div class="timeline-item">
                <h3 class="timeline-header">
                    <span class="text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Compra Finalizada
                    </span>
                </h3>
            </div>
        </div>
        @else
        <div><i class="fas fa-clock bg-gray"></i></div>
        @endif
        @else
        <div><i class="fas fa-clock bg-gray"></i></div>
        @endif
    </div>
    @endif
</div>
