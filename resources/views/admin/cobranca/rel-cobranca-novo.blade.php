@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRelatorio" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-relatorio-cobranca" data-toggle="pill"
                                    href="#painel-relatorio-cobranca" role="tab"
                                    aria-controls="painel-relatorio-cobranca" aria-selected="true">
                                    Relatório
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-cartao-cheque" data-toggle="pill" href="#painel-cartao-cheque"
                                    role="tab" aria-controls="painel-cartao-cheque" aria-selected="false">
                                    Cheques e Cartão
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-limite-credito" data-toggle="pill" href="#painel-limite-credito"
                                    role="tab" aria-controls="painel-limite-credito" aria-selected="false">
                                    Limite Crédito
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-prazo-medio" data-toggle="pill" href="#painel-prazo-medio"
                                    role="tab" aria-controls="painel-prazo-medio" aria-selected="false">
                                    Prazo Médio
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="badge badge-danger badge-date-inadimplencia"></small>
                        </div>
                        <div class="tab-content" id="tabContentRelatorio">
                            <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                aria-labelledby="tab-relatorio-cobranca">
                                @include('admin.cobranca.components.filtros-inadimplencia')

                                @include('admin.cobranca.components.cards-inadimplencia')

                                @include('admin.cobranca.components.body-inadimplencia', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses',
                                    'grafico_mensal' => 'graficoInadimplencia',
                                    'modal_table' => 'modal-table-cliente',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente',
                                    'accordion_id' => 'accordion-inadimplencia-gerente',
                                ])
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                @include('admin.cobranca.components.body-inadimplencia', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses-ch-cartao',
                                    'grafico_mensal' => 'graficoInadimplencia1',
                                    'modal_table' => 'modal-table-cliente-ch-cartao',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente-ch-cartao',
                                    'accordion_id' => 'accordion-inadimplencia-gerente-ch-cartao',
                                ])
                            </div>
                            <div class="tab-pane fade" id="painel-limite-credito" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-prazo-medio" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('css')
    <style>
        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        .btn-hover {
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        #container-grafico {
            height: 300px;
        }

        @media (max-width: 768px) {
            .btn-list {
                font-size: 13px;
            }

            .table-font-xs {
                font-size: 13px;
            }

            .text-small {
                font-size: 13px;
            }
        }

        /* otimiza a busca do modal*/
        .hidden {
            display: none !important;
        }

        .input-busca {
            max-width: 300px;
            /* width: auto; */
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/dashboard/inadimplencia.js?v=3') }}"></script>
    <script type="text/javascript">
        const tabChCartao = 1;
        var tableInadimplencia;
        var dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');
        $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

        var routes = {
            'inadimplencia_gerente': "{{ route('get-list-cobranca') }}"
        };

        var routesInadimplenciaMensal = {
            'tabela_mensal': "{{ route('get-inadimplencia') }}",
            'modal_clientes': "{{ route('get-inadimplencia-cliente') }}",
            'language_datatables': "{{ asset('vendor/datatables/pt-br.json') }}"
        };

        inadimplenciaGerente({}, routes['inadimplencia_gerente']);
        initTableInadimplenciaMeses(
            'tabela-inadimplencia-meses',
            'modal-table-cliente',
            'accordion-inadimplencia-gerente', {},
            routesInadimplenciaMensal);

        buscarTermo('accordion-inadimplencia-gerente');

        $('#tab-relatorio-cobranca').on('click', function() {
            const tabChCartao = 1;
            inadimplenciaGerente({}, routes['inadimplencia_gerente']);
            initTableInadimplenciaMeses(
                'tabela-inadimplencia-meses',
                'modal-table-cliente',
                'accordion-inadimplencia-gerente', {},
                routesInadimplenciaMensal
            );
        });

        $('#tab-cartao-cheque').on('click', function() {
            const tabChCartao = 2;
            inadimplenciaGerente({}, routes['inadimplencia_gerente']);
            initTableInadimplenciaMeses(
                'tabela-inadimplencia-meses-ch-cartao',
                'modal-table-cliente-ch-cartao',
                'accordion-inadimplencia-gerente-ch-cartao', {},
                routesInadimplenciaMensal
            );
            buscarTermo('accordion-inadimplencia-gerente-ch-cartao');

        });
        // faz a pesquisa pelos filtros
        $('#btn-search').on('click', function() {
            const data = {
                nm_pessoa: $('#filtro-nome').val(),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                session: true
            };

            inadimplenciaGerente(data, routes['inadimplencia_gerente']);
            initTableInadimplenciaMeses('tabela-inadimplencia-meses', 'modal-table-cliente', data,
                routesInadimplenciaMensal);
        });

        //limpa as filtros e retorna tudo novamente
        $('#btn-reset').on('click', function() {
            $('#filtro-nome').val('');
            $('#filtro-vendedor').val('');
            $('#filtro-cnpj').val('');
            $('#filtro-supervisor').val('');
            const data = {
                nm_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                session: false
            };
            inadimplenciaGerente(data, routes['inadimplencia_gerente']);
            initTableInadimplenciaMeses('tabela-inadimplencia-meses', data, routesInadimplenciaMensal);
        });
    </script>

    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js?v=4') }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}"></script>

@stop
