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

                                @include('admin.cobranca.components.tab-relatorios', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses',
                                    'grafico_mensal' => 'graficoInadimplencia',
                                    'modal_table' => 'modal-table-cliente',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente',
                                    'accordion_id' => 'accordion-inadimplencia-gerente',
                                    'treeAccordion' => 'treeAccordion',
                                ])
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                @include('admin.cobranca.components.tab-relatorios', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses-ch-cartao',
                                    'grafico_mensal' => 'grafico-inadimplencia-ch-cartao',
                                    'modal_table' => 'modal-table-cliente-ch-cartao',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente-ch-cartao',
                                    'accordion_id' => 'accordion-inadimplencia-gerente-ch-cartao',
                                    'treeAccordion' => 'treeAccordion-ch-cartao',
                                ])
                            </div>
                            <div class="tab-pane fade" id="painel-limite-credito" role="tabpanel"
                                aria-labelledby="tab-limite-credito">
                                @include('admin.cobranca.components.tab-painel-limite-credito')
                            </div>
                            <div class="tab-pane fade" id="painel-prazo-medio" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                @include('admin.cobranca.components.tab-prazo-medio')
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
        #tabela-inadimplencia-meses div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
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
    <script src="{{ asset('js/dashboard/inadimplencia.js?v=5') }}"></script>
    <script type="text/javascript">
        const tab = 1;
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

        inadimplenciaGerente(
            tab, {},
            routes['inadimplencia_gerente'],
            'treeAccordion',
            'card-inadimplencia-gerente'
        );

        initTableInadimplenciaMeses(
            tab,
            'tabela-inadimplencia-meses',
            'modal-table-cliente',
            'accordion-inadimplencia-gerente', {},
            routesInadimplenciaMensal
        );

        buscarTermo('accordion-inadimplencia-gerente');

        $('#tab-relatorio-cobranca').on('click', function() {
            const tab = 1;
            inadimplenciaGerente(
                tab, {},
                routes['inadimplencia_gerente'],
                'treeAccordion',
                'card-inadimplencia-gerente'
            );

            initTableInadimplenciaMeses(
                tab,
                'tabela-inadimplencia-meses',
                'modal-table-cliente',
                'accordion-inadimplencia-gerente', {},
                routesInadimplenciaMensal
            );
            vincularTabelaAoGrafico("tabela-inadimplencia-meses", "graficoInadimplencia");
        });

        $('#tab-cartao-cheque').on('click', function() {
            const tab = 2;
            inadimplenciaGerente(
                tab, {},
                routes['inadimplencia_gerente'],
                'treeAccordion-ch-cartao',
                'card-inadimplencia-gerente-ch-cartao'
            );

            initTableInadimplenciaMeses(
                tab,
                'tabela-inadimplencia-meses-ch-cartao',
                'modal-table-cliente-ch-cartao',
                'accordion-inadimplencia-gerente-ch-cartao', {},
                routesInadimplenciaMensal
            );
            buscarTermo('accordion-inadimplencia-gerente-ch-cartao');
            vincularTabelaAoGrafico("tabela-inadimplencia-meses-ch-cartao", "grafico-inadimplencia-ch-cartao");

        });

        $('#tab-limite-credito').one('click', function() {
             $('#tabela-limite-credito').DataTable({
                processing: true,
                serverSide: false,
                searching: true,
                responsive: true,
                paging: true,
                pagingType: "simple",
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}"
                },
                ajax: {
                    url: "{{ route('get-limite-credito') }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'NM_PESSOA',
                        title: 'Cliente',
                        className: 'text-left',
                        responsivePriority: 1,
                    },
                    {
                        data: 'VL_NOTA',
                        title: 'Vl Notas',
                        className: 'text-right',
                        responsivePriority: 10000,
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    },
                    {
                        data: 'VL_USADO',
                        title: 'Vl Usado',
                        className: 'text-right',
                        responsivePriority: 10000,
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    }, {
                        data: 'VL_CREDITO',
                        title: 'Vl Credito',
                        className: 'text-right',
                        responsivePriority: 10000,
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    },
                    {
                        data: 'DISPONIVEL',
                        title: 'Vl Disponível',
                        className: 'text-right',
                        responsivePriority: 10000,
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    },
                ],
            });

        });

        var route = {
            'prazo-medio': "{{ route('get-prazo-medio') }}"
        };

        $('#tab-prazo-medio').one('click', function() {
            carregarDadosPrazoMedio(route ['prazo-medio']);

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
            inadimplenciaGerente(
                tab,
                data,
                routes['inadimplencia_gerente'],
                'treeAccordion',
                'card-inadimplencia-gerente');

            initTableInadimplenciaMeses(
                tab,
                'tabela-inadimplencia-meses',
                'modal-table-cliente',
                'treeAccordion',
                data,
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

            inadimplenciaGerente(
                tab,
                data,
                routes['inadimplencia_gerente'],
                'treeAccordion',
                'card-inadimplencia-gerente');

            initTableInadimplenciaMeses(
                tab,
                'tabela-inadimplencia-meses',
                'modal-table-cliente',
                'treeAccordion',
                data,
                routesInadimplenciaMensal);
        });
    </script>

    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js?v=9') }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}"></script>

@stop
