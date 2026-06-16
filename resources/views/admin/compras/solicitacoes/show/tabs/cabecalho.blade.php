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
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label class="text-muted mb-0"><small>Status</small></label>
                    <p class="mb-0 mt-1">
                        <span class="badge badge-{{ $cor }}">{{ $label }}</span>
                    </p>
                </div>
            </div>
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

    {{-- Timeline de aprovação --}}
    @if(count($etapas) > 0)
    @php
        $todasAprovadas = collect($etapas)->every(fn($e) => $e->ST_ETAPA === 'APR');
    @endphp
    <hr class="mt-1 mb-2">
    <h6 class="text-muted mb-2"><i class="fas fa-stream mr-1"></i> Fluxo de Aprovação</h6>
    <div class="timeline timeline-inverse">
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
                    <i class="fas fa-clock mr-1"></i>
                    {{ $etapa->DT_ACAO ?? 'Aguardando' }}
                </span>
                <h3 class="timeline-header">
                    Etapa {{ $etapa->NR_ORDEM }} — {{ $etapa->DS_CARGO }}
                    <span class="badge badge-{{ $ec }} ml-1">{{ $el }}</span>
                </h3>
                @if($etapa->DS_OBSERVACAO)
                <div class="timeline-body text-muted">
                    {{ $etapa->DS_OBSERVACAO }}
                </div>
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
    @endif
</div>
