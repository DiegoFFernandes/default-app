@extends('layouts.master')

@section('title', $title_page)

@section('content')
<section class="content">

    @php
        $statusMap = [
            'RAS' => ['secondary', 'Rascunho'],
            'APR' => ['warning',   'Em Aprovação'],
            'APC' => ['success',   'Aprovada para Compra'],
            'REP' => ['danger',    'Reprovada'],
        ];
        [$cor, $label] = $statusMap[$solicitacao->ST_SOLICITACAO] ?? ['secondary', $solicitacao->ST_SOLICITACAO];

        $cotacaoSelecionada = collect($cotacoes)->firstWhere('ST_SELECIONADA', 'S');
    @endphp

    <div class="card card-{{ $cor }} card-outline card-outline-tabs">

        {{-- Nav Tabs --}}
        <div class="card-header p-0 d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs border-bottom-0" id="tabs-solicitacao" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-cabecalho" data-toggle="pill"
                        href="#pane-cabecalho" role="tab">
                        <i class="fas fa-info-circle mr-1"></i> Cabeçalho
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-itens" data-toggle="pill"
                        href="#pane-itens" role="tab">
                        <i class="fas fa-list mr-1"></i> Itens
                        <span class="badge badge-secondary ml-1">{{ count($itens) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-cotacoes" data-toggle="pill"
                        href="#pane-cotacoes" role="tab">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Cotações
                        <span class="badge badge-secondary ml-1">{{ count($cotacoes) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-fornecedor" data-toggle="pill"
                        href="#pane-fornecedor" role="tab">
                        <i class="fas fa-handshake mr-1"></i> Fornecedor Compra
                        @if($cotacaoSelecionada)
                            <span class="badge badge-success ml-1"><i class="fas fa-check"></i></span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="card-tools mr-2">
                <span class="badge badge-{{ $cor }} mr-2">
                    #{{ $solicitacao->CD_SOLICITACAO }} — {{ $label }}
                </span>
                @if($solicitacao->ST_SOLICITACAO === 'RAS')
                    <button id="btn-submeter" class="btn btn-danger btn-xs mr-1">
                        <i class="fas fa-paper-plane"></i> Enviar para Aprovação
                    </button>
                    <a href="{{ route('compras.solicitacoes.edit', $solicitacao->CD_SOLICITACAO) }}"
                        class="btn btn-warning btn-xs mr-1">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endif
                <a href="{{ route('compras.solicitacoes.index') }}" class="btn btn-default btn-xs">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="card-body">
            <div class="tab-content" id="tabs-solicitacao-content">

                {{-- Tab: Cabeçalho --}}
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

                    {{-- Timeline de aprovação (dentro do cabeçalho) --}}
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

                {{-- Tab: Itens --}}
                <div class="tab-pane fade" id="pane-itens" role="tabpanel">
                    <table class="table table-striped table-bordered table-font-small mt-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-center">Un.</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itens as $item)
                            <tr>
                                <td>{{ $item->CD_ITEM }}</td>
                                <td>{{ $item->DS_ITEM }}</td>
                                <td class="text-center">{{ number_format($item->QT_ITEM, 3, ',', '.') }}</td>
                                <td class="text-center">{{ $item->DS_UNIDADE }}</td>
                                <td>{{ $item->DS_OBSERVACAO ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">Nenhum item cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tab: Cotações --}}
                <div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
                    <table class="table table-striped table-bordered table-font-small mt-2" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fornecedor</th>
                                <th class="text-center">Prazo</th>
                                <th>Condição Pgto.</th>
                                <th class="text-right">Valor Total</th>
                                <th>Observação</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cotacoes as $cot)
                            <tr class="{{ $cot->ST_SELECIONADA === 'S' ? 'table-success font-weight-bold' : '' }}">
                                <td>{{ $cot->NM_FORNECEDOR }}</td>
                                <td class="text-center">{{ $cot->NR_PRAZO_ENTREGA }} dias</td>
                                <td>{{ $cot->DS_CONDICAO_PAGAMENTO }}</td>
                                <td class="text-right">R$ {{ number_format($cot->VL_TOTAL, 2, ',', '.') }}</td>
                                <td>{{ $cot->DS_OBSERVACAO ?? '-' }}</td>
                                <td class="text-center">
                                    @if($cot->ST_SELECIONADA === 'S')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i> Selecionado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Nenhuma cotação cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tab: Fornecedor Selecionado --}}
                <div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
                    @if($cotacaoSelecionada)
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="info-box info-box-custom">
                                <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fornecedor</span>
                                    <span class="info-box-number" style="font-size:13px">
                                        {{ $cotacaoSelecionada->NM_FORNECEDOR }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box info-box-custom">
                                <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Valor Total</span>
                                    <span class="info-box-number">
                                        R$ {{ number_format($cotacaoSelecionada->VL_TOTAL, 2, ',', '.') }}
                                    </span>
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
                </div>

            </div>
        </div>
    </div>

</section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
@stop

@section('js')
@if($solicitacao->ST_SOLICITACAO === 'RAS')
<script>
$(document).ready(function () {
    const token = $('[name=csrf-token]').attr('content');

    $('#btn-submeter').click(function () {
        Swal.fire({
            title: 'Enviar para aprovação?',
            text: 'Após enviada, a solicitação não poderá ser editada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sim, enviar',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            Swal.fire({ title: 'Enviando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
            $.post('{{ route('compras.solicitacoes.submeter', $solicitacao->CD_SOLICITACAO) }}', {
                _token: token
            }, function (res) {
                if (res.errors) {
                    Swal.fire('Atenção', res.errors, 'warning');
                } else {
                    Swal.fire('Enviada!', res.success, 'success').then(() => {
                        window.location.reload();
                    });
                }
            });
        });
    });
});
</script>
@endif
@stop
