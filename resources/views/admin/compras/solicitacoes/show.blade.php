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
                'CAN' => ['dark',      'Cancelada'],
            ];
            [$cor, $label] = $statusMap[$solicitacao->ST_SOLICITACAO] ?? ['secondary', $solicitacao->ST_SOLICITACAO];

            $cotacaoSelecionada = collect($cotacoes)->firstWhere('ST_SELECIONADA', 'S');
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
                    <li class="nav-item">
                        <a class="nav-link" id="tab-cotacoes" data-toggle="pill" href="#pane-cotacoes" role="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Cotações
                            <span class="badge badge-secondary ml-1">{{ count($cotacoes) }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-fornecedor" data-toggle="pill" href="#pane-fornecedor" role="tab">
                            <i class="fas fa-handshake mr-1"></i> Fornecedor Compra
                            @if ($cotacaoSelecionada)
                                <span class="badge badge-success ml-1"><i class="fas fa-check"></i></span>
                            @endif
                        </a>
                    </li>
                </ul>

                <div class="card-tools mr-2">
                    <span class="badge badge-{{ $cor }} mr-2">
                        #{{ $solicitacao->CD_SOLICITACAO }} — {{ $label }}
                    </span>
                    @if ($solicitacao->ST_SOLICITACAO === 'RAS')
                        <button id="btn-submeter" class="btn btn-primary btn-xs mr-1">
                            <i class="fas fa-paper-plane"></i> Enviar para Aprovação
                        </button>
                        <a href="{{ route('compras.solicitacoes.edit', $solicitacao->CD_SOLICITACAO) }}"
                            class="btn btn-warning btn-xs mr-1">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button id="btn-cancelar" class="btn btn-danger btn-xs mr-1">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    @endif
                    @if (in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))
                        <button id="btn-cancelar-sol" class="btn btn-dark btn-xs mr-1">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                    @endif
                    <a id="btn-exportar-excel"
                        href="{{ route('compras.solicitacoes.exportar-excel', $solicitacao->CD_SOLICITACAO) }}"
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

                    {{-- Tab: Cabeçalho --}}
                    @include('admin.compras.solicitacoes.show.tabs.cabecalho')

                    {{-- Tab: Itens --}}
                    @include('admin.compras.solicitacoes.show.tabs.itens')

                    {{-- Tab: Cotações --}}
                    @include('admin.compras.solicitacoes.show.tabs.cotacoes')

                    {{-- Tab: Fornecedor Selecionado --}}
                    @include('admin.compras.solicitacoes.show.tabs.fornecedor')

                </div>
            </div>
        </div>

        {{-- Área de Impressão --}}
        @include('admin.compras.solicitacoes.show.prints.solicitacao-compra')

    </section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
    <style>
        #print-area {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            @page {
                margin: 1.5cm 1.5cm 1.5cm 0.5cm;
                size: A4 portrait;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            const qtdItens = {{ count($itens) }};

            $('#btn-imprimir').click(function() {
                if (qtdItens === 0) {
                    Swal.fire('Atenção', 'A solicitação não possui itens para imprimir.', 'warning');
                    return;
                }
                window.print();
            });

            $('#btn-exportar-excel').on('click', function(e) {
                if (qtdItens === 0) {
                    e.preventDefault();
                    Swal.fire('Atenção', 'A solicitação não possui itens para exportar.', 'warning');
                }
            });
        });
    </script>
    @if ($solicitacao->ST_SOLICITACAO === 'RAS')
        <script>
            $(document).ready(function() {
                const token = $('[name=csrf-token]').attr('content');

                $('#btn-submeter').click(function() {
                    Swal.fire({
                        title: 'Enviar para aprovação?',
                        text: 'Após enviada, a solicitação não poderá ser editada, e os responsaveis serão notificados!',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Sim, enviar',
                        cancelButtonText: 'Cancelar',
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        Swal.fire({
                            title: 'Enviando...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                        $.post('{{ route('compras.solicitacoes.submeter', $solicitacao->CD_SOLICITACAO) }}', {
                            _token: token
                        }, function(res) {
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

                $('#btn-cancelar').click(function() {
                    Swal.fire({
                        title: 'Excluir rascunho?',
                        text: 'A solicitação será excluída permanentemente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Sim, cancelar',
                        cancelButtonText: 'Não',
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        $.ajax({
                            url: '/compras/solicitacoes/{{ $solicitacao->CD_SOLICITACAO }}',
                            method: 'DELETE',
                            data: {
                                _token: token
                            },
                            success: function(res) {
                                if (res.errors) {
                                    Swal.fire('Erro', res.errors, 'error');
                                } else {
                                    Swal.fire('Cancelado!', res.success, 'success').then(
                                    () => {
                                            window.location.href =
                                                '{{ route('compras.solicitacoes.index') }}';
                                        });
                                }
                            }
                        });
                    });
                });

                @if (in_array($solicitacao->ST_SOLICITACAO, ['APR', 'APC']))
                $('#btn-cancelar-sol').click(function () {
                    Swal.fire({
                        title: 'Cancelar solicitação?',
                        text: 'O status será alterado para Cancelada e o valor será liberado no saldo do ciclo.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#343a40',
                        confirmButtonText: 'Sim, cancelar',
                        cancelButtonText: 'Não',
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        $.post('{{ route('compras.solicitacoes.cancelar', $solicitacao->CD_SOLICITACAO) }}', {
                            _token: token,
                        }, function (res) {
                            if (res.errors) {
                                Swal.fire('Erro', res.errors, 'error');
                            } else {
                                Swal.fire('Cancelada!', res.success, 'success').then(() => {
                                    window.location.reload();
                                });
                            }
                        });
                    });
                });
                @endif

            });
        </script>
    @endif
@stop
