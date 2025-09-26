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
                                    Inadimplência
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
                            <li class="nav-item">
                                <a class="nav-link" id="tab-canhoto" data-toggle="pill" href="#painel-canhoto"
                                    role="tab" aria-controls="painel-canhoto" aria-selected="false">
                                    Canhoto
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
                                @include('admin.cobranca.components.filtros-inadimplencia', [
                                    'pessoa' => 'pessoa',
                                    'filtro_gerente' => 'filtro-gerente',
                                    'filtro_supervisor' => 'filtro-supervisor',
                                    'filtro_vendedor' => 'filtro-vendedor',
                                    'filtro_cnpj' => 'filtro-cnpj',
                                    'btn_search' => 'btn-search',
                                    'btn_reset' => 'btn-reset',
                                    'daterange' => 'daterange',
                                    'placeholderDatarange' => 'Filtrar por Vencimento',
                                ])

                                @include('admin.cobranca.components.cards-inadimplencia')

                                @include('admin.cobranca.components.tab-relatorios', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses',
                                    'grafico_mensal' => 'graficoInadimplencia',
                                    'modal_table' => 'modal-table-cliente',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente',
                                    'accordion_id' => 'accordion-inadimplencia-gerente',
                                    'treeAccordion' => 'treeAccordion',
                                    'treeAccordionGerente' => 'treeAccordionGerente',
                                    'buscarCliente' => 'buscarCliente',
                                ])
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                @include('admin.cobranca.components.filtros-inadimplencia', [
                                    'pessoa' => 'pessoa_ch_cartao',
                                    'filtro_gerente' => 'filtro-gerente_ch_cartao',
                                    'filtro_supervisor' => 'filtro-supervisor_ch_cartao',
                                    'filtro_vendedor' => 'filtro-vendedor_ch_cartao',
                                    'filtro_cnpj' => 'filtro-cnpj_ch_cartao',
                                    'btn_search' => 'btn-search-ch-cartao',
                                    'btn_reset' => 'btn-reset-ch-cartao',
                                    'daterange' => 'daterange-ch-cartao',
                                    'placeholderDatarange' => 'Filtrar por Emissão',
                                ])
                                @include('admin.cobranca.components.tab-relatorios', [
                                    'tabela_mensal' => 'tabela-inadimplencia-meses-ch-cartao',
                                    'grafico_mensal' => 'grafico-inadimplencia-ch-cartao',
                                    'modal_table' => 'modal-table-cliente-ch-cartao',
                                    'card_inadimplencia' => 'card-inadimplencia-gerente-ch-cartao',
                                    'accordion_id' => 'accordion-inadimplencia-gerente-ch-cartao',
                                    'treeAccordion' => 'treeAccordion-ch-cartao',
                                    'treeAccordionGerente' => 'treeAccordionGerente-ch-cartao',
                                    'buscarCliente' => 'buscarCliente-ch-cartao',
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
                            <div class="tab-pane fade" id="painel-canhoto" role="tabpanel"
                                aria-labelledby="tab-painel-canhoto">
                                @include('admin.cobranca.components.tab-canhotos', [
                                    'tabela_canhoto_mensal' => 'tabela-canhoto-meses',
                                    'modal_canhoto_table' => 'modal-table-canhoto',
                                    'card_canhoto' => 'card-canhoto',
                                    'accordion_canhoto_id' => 'accordion-canhoto',
                                    'treeAccordionCanhoto' => 'treeAccordionCanhoto',
                                ])
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
         /* limita o tamanho da celula do nome*/
        #tabela-limite-credito td:nth-child(1) {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

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

            .btn-d-block {
                display: block;
                width: 100%;
            }

            .saldo {
                display: block;
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
    <script src="{{ asset('js/dashboard/inadimplencia.js?v=25') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js?v=2') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js?v=5') }}"></script>
    <script src="{{ asset('js/dashboard/canhoto.js?v=2') }}"></script>
    <script type="text/javascript">
        const tab = 1;
        var tableInadimplencia;
        var dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');
        //datas selecionadas no date range picker
        var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

        $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

        var routes = {
            'inadimplencia_gerente': "{{ route('get-list-cobranca') }}",
            'canhoto': "{{ route('get-list-canhoto') }}",
            'modal_canhotos': "{{ route('get-list-canhoto-details') }}",
        };

        var routesInadimplenciaMensal = {
            'tabela_mensal': "{{ route('get-inadimplencia') }}",
            'modal_clientes': "{{ route('get-inadimplencia-cliente') }}",
            'language_datatables': "{{ asset('vendor/datatables/pt-br.json') }}",
            'searchPessoa': "{{ route('usuario.search-pessoa') }}"
        };

        var routePrazoMedio = {
            'prazo_medio': "{{ route('get-prazo-medio') }}"
        };

        var routeLimiteCredito = {
            'limite_credito': "{{ route('get-limite-credito') }}",
            'language_datatables': "{{ asset('vendor/datatables/pt-br.json') }}"
        };

        //Carrega o select2 de pessoa
        initSelect2Pessoa('#pessoa', routesInadimplenciaMensal.searchPessoa);

        const data = {
            nm_pessoa: $("#pessoa option:selected").text(),
            nm_vendedor: $('#filtro-vendedor').val(),
            cnpj: $('#filtro-cnpj').val(),
            filtro_gerente: $('#filtro-gerente').val(),
            nm_supervisor: $('#filtro-supervisor').val(),
            session: true,
            dtFim: dtFim,
            dtInicio: dtInicio
        };

        carregaDadosTela1(data);

        buscarTermo('accordion-inadimplencia-gerente', '#buscarCliente');

        $('#tab-relatorio-cobranca').on('click', function() {
            const tab = 1;
            dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
            dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');

            const data = {
                nm_pessoa: $("#pessoa option:selected").text(),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                filtro_gerente: $('#filtro-gerente').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            carregaDadosTela1(data);
            vincularTabelaAoGrafico("tabela-inadimplencia-meses", "graficoInadimplencia");
        });

        let datasSelecionadasCartao = null;

        $('#tab-cartao-cheque').on('click', function() {
            initSelect2Pessoa('#pessoa_ch_cartao', routesInadimplenciaMensal.searchPessoa);

            const tab = 2;
            dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
            dtFim = moment().subtract(5, 'days').format('DD.MM.YYYY');
            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            if (!datasSelecionadasCartao) {
                datasSelecionadasCartao = initDateRangePicker('#daterange-ch-cartao', dtInicio, dtFim);
            }
            const data = {
                nm_pessoa: $("#pessoa_ch_cartao option:selected").text(),
                nm_vendedor: $('#filtro-vendedor_ch_cartao').val(),
                cnpj: $('#filtro-cnpj_ch_cartao').val(),
                filtro_gerente: $('#filtro-gerente_ch_cartao').val(),
                nm_supervisor: $('#filtro-supervisor_ch_cartao').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };
            carregaDadosTela2(data);

            buscarTermo('accordion-inadimplencia-gerente-ch-cartao', '#buscarCliente-ch-cartao');
            vincularTabelaAoGrafico("tabela-inadimplencia-meses-ch-cartao", "grafico-inadimplencia-ch-cartao");

        });

        $('#tab-limite-credito').one('click', function() {
            initTableLimiteCredito(routeLimiteCredito);
        });

        $('#tab-prazo-medio').one('click', function() {
            carregarDadosPrazoMedio(routePrazoMedio);
        });

        $('#tab-canhoto').on('click', function() {
            dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
            dtFim = moment().subtract(5, 'days').format('DD.MM.YYYY');

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            canhotoGerente(
                tab, {},
                routes,
                'treeAccordionCanhoto',
                'card_canhoto'
            );

            initTableCanhotoMeses(
                tab,
                'tabela-canhoto-meses',
                'modal-table-canhoto',
                'accordion-canhoto', {},
                routes,
            );

        });
        // faz a pesquisa pelos filtros
        $('#btn-search').on('click', function() {
            if (!datasSelecionadas.getInicio() == 0) {
                dtInicio = datasSelecionadas.getInicio();
                dtFim = datasSelecionadas.getFim();
            }
            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);
            hierarquia = null;
            const data = {
                nm_pessoa: $("#pessoa option:selected").text(),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                filtro_gerente: $('#filtro-gerente').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };
            carregaDadosTela1(data);
        });

        $('#btn-search-ch-cartao').on('click', function() {
            if (!datasSelecionadasCartao.getInicio() == 0) {
                dtInicio = datasSelecionadasCartao.getInicio();
                dtFim = datasSelecionadasCartao.getFim();
            }
            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);
            // hierarquia = null;
            const data = {
                nm_pessoa: $("#pessoa_ch_cartao option:selected").text(),
                nm_vendedor: $('#filtro-vendedor_ch_cartao').val(),
                cnpj: $('#filtro-cnpj_ch_cartao').val(),
                filtro_gerente: $('#filtro-gerente_ch_cartao').val(),
                nm_supervisor: $('#filtro-supervisor_ch_cartao').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };
            carregaDadosTela2(data);
        });

        //limpa as filtros e retorna tudo novamente
        $('#btn-reset').on('click', function() {
            hierarquia = null;
            dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
            dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

            $('#daterange').val('');
            $('#filtro-nome').val('');
            $('#filtro-vendedor').val('');
            $('#filtro-cnpj').val('');
            $('#filtro-supervisor').val('');
            $('#filtro-gerente').val(0).change();

            const data = {
                nm_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                session: false,
                dtFim: dtFim,
                dtInicio: dtInicio
            };

            carregaDadosTela1(data);
        });

        $('#btn-reset-ch-cartao').on('click', function() {
            hierarquia = null;
            dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
            dtFim = moment().subtract(5, 'days').format('DD.MM.YYYY');

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            datasSelecionadasCartao = initDateRangePicker('#daterange-ch-cartao', dtInicio, dtFim);

            $('#daterange-ch-cartao').val('');
            $('#filtro-nome_ch_cartao').val('');
            $('#filtro-vendedor_ch_cartao').val('');
            $('#filtro-cnpj_ch_cartao').val('');
            $('#filtro-supervisor_ch_cartao').val('');
            $('#filtro-gerente_ch_cartao').val(0).change();

            const data = {
                nm_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                session: false,
                dtFim: dtFim,
                dtInicio: dtInicio
            };

            carregaDadosTela2(data);
        });

        function carregaDadosTela1(data) {
            inadGerente = null;
            inadMeses = null;
            hierarquia = null;
            inadimplenciaGerente(
                tab,
                data,
                routes['inadimplencia_gerente'],
                'treeAccordionGerente',
                'card-inadimplencia-gerente'
            );

            initTableInadimplenciaMeses(
                tab,
                'tabela-inadimplencia-meses',
                'modal-table-cliente',
                'accordion-inadimplencia-gerente',
                data,
                routesInadimplenciaMensal
            );
        }

        function carregaDadosTela2(data) {
            inadGerente = null;
            inadMeses = null;
            hierarquia = null;

            inadimplenciaGerente(
                2,
                data,
                routes['inadimplencia_gerente'],
                'treeAccordionGerente-ch-cartao',
                'card-inadimplencia-gerente-ch-cartao'
            );

            initTableInadimplenciaMeses(
                2,
                'tabela-inadimplencia-meses-ch-cartao',
                'modal-table-cliente-ch-cartao',
                'accordion-inadimplencia-gerente-ch-cartao',
                data,
                routesInadimplenciaMensal
            );
        }
    </script>

    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js?v=10') }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>

@stop
