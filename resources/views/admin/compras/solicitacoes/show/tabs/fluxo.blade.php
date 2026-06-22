<div class="tab-pane fade" id="pane-fluxo" role="tabpanel">
    @php
        $fluxoEtapas = [
            'RAS' => ['secondary', 'fa-pencil-alt', 'Rascunho'],
            'ANA' => ['info',      'fa-search',     'Análise de Compra'],
            'APR' => ['warning',   'fa-thumbs-up',  'Em Aprovação'],
            'APC' => ['primary',   'fa-check-circle','Aprovada'],
            'REP' => ['danger',    'fa-times-circle','Reprovada'],
            'CAN' => ['dark',      'fa-ban',         'Cancelada'],
        ];

        $ordemFluxo = ['RAS', 'ANA', 'APR', 'APC'];
        $statusAtual = $solicitacao->ST_SOLICITACAO;
        $isReprovada = $statusAtual === 'REP';
        $isCancelada = $statusAtual === 'CAN';
        $posicaoAtual = array_search($statusAtual, $ordemFluxo);
    @endphp

    {{-- Stepper visual --}}
    <div class="row mt-3 mb-4 justify-content-center">
        @foreach($ordemFluxo as $i => $st)
        @php
            [$stCor, $stIcon, $stLabel] = $fluxoEtapas[$st];
            if ($isReprovada || $isCancelada) {
                $stepCor    = $i < $posicaoAtual ? 'secondary' : 'secondary';
                $stepActive = false;
                $stepDone   = $i < $posicaoAtual;
            } else {
                $stepDone   = $posicaoAtual !== false && $i < $posicaoAtual;
                $stepActive = $statusAtual === $st;
                $stepCor    = $stepActive ? $stCor : ($stepDone ? 'success' : 'secondary');
            }
        @endphp
        <div class="col-auto text-center px-2" style="min-width:90px">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                         bg-{{ $stepCor }} text-white"
                 style="width:48px;height:48px;font-size:1.1rem;
                        {{ $stepActive ? 'box-shadow:0 0 0 4px rgba(0,0,0,.15)' : '' }}">
                @if($stepDone)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas {{ $stIcon }}"></i>
                @endif
            </div>
            <div class="mt-1" style="font-size:0.72rem;font-weight:{{ $stepActive ? '700' : '400' }};
                         color:{{ $stepActive ? '#343a40' : '#6c757d' }}">
                {{ $stLabel }}
            </div>
        </div>
        @if(!$loop->last)
        <div class="col px-0 d-flex align-items-start pt-3">
            <div style="height:2px;width:100%;background:{{ ($posicaoAtual !== false && $i < $posicaoAtual && !$isReprovada && !$isCancelada) ? '#28a745' : '#dee2e6' }};margin-top:22px"></div>
        </div>
        @endif
        @endforeach

        @if($isReprovada || $isCancelada)
        <div class="col-auto text-center px-2" style="min-width:90px">
            @php [$fCor, $fIcon, $fLabel] = $fluxoEtapas[$statusAtual]; @endphp
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                         bg-{{ $fCor }} text-white"
                 style="width:48px;height:48px;font-size:1.1rem;box-shadow:0 0 0 4px rgba(0,0,0,.15)">
                <i class="fas {{ $fIcon }}"></i>
            </div>
            <div class="mt-1" style="font-size:0.72rem;font-weight:700;color:#343a40">
                {{ $fLabel }}
            </div>
        </div>
        @endif
    </div>

    {{-- Timeline de etapas de aprovação --}}
    @if(count($etapas) > 0 || in_array($statusAtual, ['ANA', 'APR', 'APC', 'REP', 'CAN']))
    @php $todasAprovadas = count($etapas) > 0 && collect($etapas)->every(fn($e) => $e->ST_ETAPA === 'APR'); @endphp
    <hr class="mb-3">
    <div class="timeline timeline-inverse">

        @if(in_array($statusAtual, ['ANA', 'APR', 'APC', 'REP', 'CAN']))
        <div>
            <i class="fas fa-search bg-info"></i>
            <div class="timeline-item">
                <h3 class="timeline-header">
                    Análise de Compra
                    @if($statusAtual === 'ANA')
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
                'PEN' => ['secondary', 'Pendente',  'clock'],
                'APR' => ['success',   'Aprovado',  'check'],
                'REP' => ['danger',    'Reprovado', 'times'],
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
        @else
        <div><i class="fas fa-clock bg-gray"></i></div>
        @endif
    </div>
    @else
    <div class="text-center text-muted py-3">
        <i class="fas fa-info-circle mr-1"></i> Nenhuma etapa de aprovação iniciada.
    </div>
    @endif
</div>
