@extends('layouts.master')

@section('title', $title_page)

@section('content')
<section class="content">
    @php
        $idSolicitacao = $solicitacao->CD_SOLICITACAO;

        $statusMap = [
            'RAS' => ['secondary', 'Rascunho'],
            'ANA' => ['info',      'Em Análise de Compra'],
            'APR' => ['warning',   'Em Aprovação'],
            'APC' => ['success',   'Aprovada para Compra'],
            'REP' => ['danger',    'Reprovada'],
            'CAN' => ['dark',      'Cancelada'],
        ];

        [$cor, $label] = $statusMap[$solicitacao->ST_SOLICITACAO] ?? ['secondary', $solicitacao->ST_SOLICITACAO];

        $cotacaoSelecionada = collect($cotacoes ?? [])->firstWhere('ST_SELECIONADA', 'S');

        $isSolicitante = auth()->user()->can('solicitacao-compra-criar')
            && !auth()->user()->can('solicitacao-compra-gerenciar')
            && !auth()->user()->can('solicitacao-compra-aprovar');
    @endphp

    <div class="card card-{{ $cor }} card-outline card-outline-tabs">

        {{-- Nav Tabs --}}
        <div class="card-header p-0 d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs border-bottom-0" id="tabs-solicitacao" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-cabecalho" data-toggle="pill" href="#pane-cabecalho" role="tab">
                        <i class="fas fa-info-circle mr-1"></i> Cabeçalho
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-itens" data-toggle="pill" href="#pane-itens" role="tab">
                        <i class="fas fa-list mr-1"></i> Itens
                        <span class="badge badge-secondary ml-1">{{ count($itens) }}</span>
                    </a>
                </li>
                @if(!$isSolicitante)
                <li class="nav-item">
                    <a class="nav-link" id="tab-cotacoes" data-toggle="pill" href="#pane-cotacoes" role="tab">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Cotações
                        <span class="badge badge-secondary ml-1">{{ count($cotacoes) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-fornecedor" data-toggle="pill" href="#pane-fornecedor" role="tab">
                        <i class="fas fa-handshake mr-1"></i> Fornecedor Compra
                        @if($cotacaoSelecionada)
                            <span class="badge badge-success ml-1"><i class="fas fa-check"></i></span>
                        @endif
                    </a>
                </li>
                @endif
            </ul>

            <div class="card-tools mr-2">
                <span class="badge badge-{{ $cor }} mr-2">#{{ $idSolicitacao }} — {{ $label }}</span>
                @if(in_array($solicitacao->ST_SOLICITACAO, ['RAS', 'ANA']))
                    <a href="{{ route('compras.solicitacoes.edit', $idSolicitacao) }}"
                       class="btn btn-warning btn-xs mr-1">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endif
                @if(in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))
                    <button id="btn-cancelar-sol" class="btn btn-dark btn-xs mr-1">
                        <i class="fas fa-ban"></i> Cancelar
                    </button>
                @endif
                <a id="btn-exportar-excel"
                    href="{{ route('compras.solicitacoes.exportar-excel', $idSolicitacao) }}"
                    class="btn btn-success btn-xs mr-1">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <button id="btn-imprimir" class="btn btn-secondary btn-xs mr-1">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="{{ route('compras.solicitacoes.index') }}" class="btn btn-default btn-xs">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="card-body">
            <div class="tab-content" id="tabs-solicitacao-content">
                @include('admin.compras.solicitacoes.show.tabs.cabecalho')
                @include('admin.compras.solicitacoes.show.tabs.itens')
                @if(!$isSolicitante)
                @include('admin.compras.solicitacoes.show.tabs.cotacoes')
                @include('admin.compras.solicitacoes.show.tabs.fornecedor')
                @endif
            </div>
        </div>
    </div>

    @include('admin.compras.solicitacoes.show.prints.solicitacao-compra')

</section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
    <style>
        #print-area { display: none; }
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area {
                display: block !important;
                position: absolute;
                top: 0; left: 0; width: 100%;
            }
            @page { margin: 1.5cm 1.5cm 1.5cm 0.5cm; size: A4 portrait; }
        }
    </style>
@stop

@section('js')
<script>
$(document).ready(function () {

    const token    = $('[name=csrf-token]').attr('content');
    const qtdItens = {{ count($itens ?? []) }};

    $('a[data-toggle="pill"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    $('#btn-imprimir').click(function () {
        if (qtdItens === 0) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'A solicitação não possui itens para imprimir.', confirmButtonColor: '#dc3545' });
            return;
        }
        window.print();
    });

    $('#btn-exportar-excel').on('click', function (e) {
        if (qtdItens === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'A solicitação não possui itens para exportar.', confirmButtonColor: '#dc3545' });
        }
    });

    @if(in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))
    $('#btn-cancelar-sol').click(function () {
        Swal.fire({
            title: 'Cancelar solicitação?',
            text: 'O status será alterado para Cancelada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#343a40',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route('compras.solicitacoes.cancelar', $idSolicitacao) }}', { _token: token }, function (res) {
                if (res.errors) Swal.fire('Erro', res.errors, 'error');
                else Swal.fire('Cancelada!', res.success, 'success').then(() => window.location.reload());
            });
        });
    });
    @endif

});
</script>
@stop
