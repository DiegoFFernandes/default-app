<div class="tab-pane fade show active" id="pane-cabecalho" role="tabpanel">
    @if($solicitacao === null || $solicitacao->ST_SOLICITACAO === 'RAS')
    {{-- ===== Formulário editável (nova / rascunho) ===== --}}
    <div class="row mt-1">
        <div class="col-md-4">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2" id="cd_empresa" style="width:100%">
                    <option value="">Selecione</option>
                    @foreach($empresas as $e)
                        <option value="{{ $e->CD_EMPRESA }}"
                            {{ isset($solicitacao) && $solicitacao->CD_EMPRESA == $e->CD_EMPRESA ? 'selected' : '' }}>
                            {{ $e->NM_EMPRESA }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Data <span class="text-danger">*</span></small></label>
                <input type="date" class="form-control form-control-sm" id="dt_solicitacao"
                    value="{{ isset($solicitacao) ? $solicitacao->DT_SOLICITACAO : date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Justificativa <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_justificativa" maxlength="500"
                    value="{{ $solicitacao->DS_JUSTIFICATIVA ?? '' }}">
            </div>
        </div>

        <div id="div-saldo-ciclo" class="col-md-12" style="display:none">
            <div class="alert alert-light border mb-2 py-2 px-3">
                <small class="text-muted">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Ciclo: <span id="saldo-periodo" class="font-weight-bold text-dark"></span>
                </small>
                <div class="row mt-1">
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Orçamento</small>
                        <span class="font-weight-bold text-primary" id="saldo-orcado"></span>
                    </div>
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Utilizado</small>
                        <span class="font-weight-bold text-warning" id="saldo-utilizado"></span>
                    </div>
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Saldo</small>
                        <span class="font-weight-bold" id="saldo-valor"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="div-centrocusto" style="display:none">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Centro de Resultado</small></label>
                <select class="form-control form-control-sm select2" id="cd_centrocusto"
                    data-selected="{{ $solicitacao->CD_CENTROCUSTO ?? '' }}"
                    style="width:100%">
                    <option value="">Nenhum</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Observações</small></label>
                <textarea class="form-control form-control-sm" id="ds_observacao" rows="2" maxlength="500">{{ $solicitacao->DS_OBSERVACAO ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3">
            @if(!$idSolicitacao)
                <button id="btn-salvar" class="btn btn-danger btn-sm">
                    <i class="fas fa-save"></i> Salvar Rascunho
                </button>
            @else
                <button id="btn-atualizar" class="btn btn-warning btn-sm">
                    <i class="fas fa-save"></i> Atualizar Cabeçalho
                </button>
            @endif
        </div>
    </div>

    @else
    {{-- ===== Visualização somente leitura ===== --}}
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
    @if(count($etapas) > 0 || in_array($solicitacao->ST_SOLICITACAO, ['ANA', 'APR', 'APC', 'REP', 'CAN']))
    @php $todasAprovadas = count($etapas) > 0 && collect($etapas)->every(fn($e) => $e->ST_ETAPA === 'APR'); @endphp
    <hr class="mt-1 mb-2">
    <h6 class="text-muted mb-2"><i class="fas fa-stream mr-1"></i> Fluxo de Aprovação</h6>
    <div class="timeline timeline-inverse">

        {{-- Etapa: Análise de Compra --}}
        @if(in_array($solicitacao->ST_SOLICITACAO, ['ANA', 'APR', 'APC', 'REP', 'CAN']))
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
        @else
        <div><i class="fas fa-clock bg-gray"></i></div>
        @endif
    </div>
    @endif
    @endif
</div>
