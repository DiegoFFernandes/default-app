@extends('layouts.master')
@section('title', 'Relatorio de Cobrança')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRelatorio" role="tablist">
                            @role('admin|gerente comercial|supervisor|vendedor|gerente unidade|cobranca')
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab-relatorio-cobranca" data-toggle="tab"
                                        href="#painel-relatorio-cobranca" role="tab"
                                        aria-controls="painel-relatorio-cobranca" aria-selected="true">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Inadimplência
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-cartao-cheque" data-toggle="tab" href="#painel-cartao-cheque"
                                        role="tab" aria-controls="painel-cartao-cheque" aria-selected="false">
                                        <i class="fas fa-credit-card mr-1"></i> Cheques e Cartão
                                    </a>
                                </li>
                            @endrole
                            @role('admin|gerente comercial|supervisor|vendedor|gerente unidade')
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-limite-credito" data-toggle="tab" href="#painel-limite-credito"
                                        role="tab" aria-controls="painel-limite-credito" aria-selected="false">
                                        <i class="fas fa-tachometer-alt mr-1"></i> Limite Crédito
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-prazo-medio" data-toggle="tab" href="#painel-prazo-medio"
                                        role="tab" aria-controls="painel-prazo-medio" aria-selected="false">
                                        <i class="fas fa-clock mr-1"></i> Prazo Médio
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-canhoto" data-toggle="tab" href="#painel-canhoto" role="tab"
                                        aria-controls="painel-canhoto" aria-selected="false">
                                        <i class="fas fa-receipt mr-1"></i> Canhoto
                                    </a>
                                </li>
                            @endrole
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-pill badge-secondary">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <span class="badge-date-inadimplencia"></span>
                            </span>
                        </div>
                        <div class="tab-content" id="tabContentRelatorio">
                            @role('admin|gerente comercial|supervisor|vendedor|gerente unidade|cobranca')
                                <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                    aria-labelledby="tab-relatorio-cobranca">

                                    @include('admin.cobranca.components.filtros-inadimplencia', [
                                        'tela' => 1,
                                        'pessoa' => 'pessoa',
                                        'pessoa_multiple' => true,
                                        'filtro_gerente' => 'filtro-gerente',
                                        'filtro_supervisor' => 'filtro-supervisor',
                                        'filtro_vendedor' => 'filtro-vendedor',
                                        'filtro_cnpj' => 'filtro-cnpj',
                                        'btn_search' => 'btn-search',
                                        'btn_reset' => 'btn-reset',
                                        'daterange' => 'daterange',
                                        'placeholderDatarange' => 'Filtrar por Vencimento',
                                        'filtro_cartorio' => 'filtro-cartorio',
                                    ])

                                    @if (!auth()->user()->hasRole('vendedor'))
                                        @include('admin.cobranca.components.cards-inadimplencia')
                                    @endif

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
                                        'tela' => 2,
                                        'pessoa' => 'pessoa_ch_cartao',
                                        'pessoa_multiple' => true,
                                        'filtro_gerente' => 'filtro-gerente_ch_cartao',
                                        'filtro_supervisor' => 'filtro-supervisor_ch_cartao',
                                        'filtro_vendedor' => 'filtro-vendedor_ch_cartao',
                                        'filtro_cnpj' => 'filtro-cnpj_ch_cartao',
                                        'btn_search' => 'btn-search-ch-cartao',
                                        'btn_reset' => 'btn-reset-ch-cartao',
                                        'daterange' => 'daterange-ch-cartao',
                                        'placeholderDatarange' => 'Filtrar por Emissão',
                                        'filtro_cartorio' => 'filtro-cartorio-ch-cartao',
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
                            @endrole
                            @role('admin|gerente comercial|supervisor|vendedor|gerente unidade')
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
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.cobranca.components.modal-parametros-cogs')
    <!-- /.content -->
@stop

@section('css')
    <style>
        /* limita o tamanho da celula do nome*/
        #tabela-limite-credito td:nth-child(1) {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-indicador {
            display: inline-block;
            min-width: 58px;
            text-align: center;
            padding: 4px 6px;
        }

        .badge-purple {
            color: #fff;
            background-color: #6f42c1;
        }

        .accordion-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        /* Accordion hierárquico */
        .gerente-card {
            overflow: hidden;
        }

        .gerente-card>.card-header button:focus {
            box-shadow: none;
        }

        .supervisor-container .btn {
            box-shadow: none !important;
        }

        .supervisor-container .btn:focus {
            outline: none;
        }

        .vendedor-container .btn {
            box-shadow: none !important;
        }

        .vendedor-container .btn:focus {
            outline: none;
        }


        @media (max-width: 768px) {
            #tabela-limite-credito td:nth-child(1) {
                max-width: 250px;
            }
        }

        #tabela-inadimplencia-meses div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        /* ── DataTable: Inadimplência Mensal ── */
        #tabela-inadimplencia-meses {
            font-size: 0.8rem;
        }

        #tabela-inadimplencia-meses thead th {
            background: #2d3748;
            color: #e2e8f0;
            border: none;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 10px;
            white-space: nowrap;
        }

        #tabela-inadimplencia-meses tbody td {
            padding: 6px 10px;
            vertical-align: middle;
            border-color: #edf2f7;
        }

        #tabela-inadimplencia-meses tbody tr:hover>td {
            background-color: #ebf4ff !important;
        }

        #tabela-inadimplencia-meses tfoot th {
            background: #f7fafc;
            font-weight: 700;
            font-size: 0.78rem;
            border-top: 2px solid #cbd5e0;
            padding: 6px 10px;
        }

        /* ── Botão detalhes ── */
        .btn-detalhes {
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 4px;
            transition: all .15s ease;
        }

        .btn-detalhes:hover {
            transform: scale(1.1);
        }

        .btn-hover {
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .container-grafico canvas {
            height: 260px !important;
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
                display: flex;
                flex-wrap: wrap;
                gap: 3px;
                margin-top: 4px;
            }

            .badge-detalhes {
                min-width: 70px;
                display: inline-block;
                margin-top: 5px;
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
    <script src="{{ asset('js/dashboard/chart-helpers.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/inadimplencia.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/canhoto.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}?v={{ time() }}"></script>

    <script type="text/javascript">
        const tab = 1;
        var tableInadimplencia;

        // Obtem o primeiro dia do mes em 240 dias
        var dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');

        //Obtem o ultimo dia do mes atual -1
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

        // Select2 multi-pessoa: inicializa lazy (somente ao expandir o card de filtros)
        // Motivo: collapsed-card oculta o elemento na carga; Select2 em elemento hidden
        //         não renderiza o placeholder. Inicializar após expansão resolve isso.
        $('#pessoa').closest('.card').one('expanded.lte.cardwidget', function() {
            $('#pessoa').select2({
                theme: 'bootstrap4',
                language: 'pt-BR',
                placeholder: 'Selecione uma ou mais pessoas',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: routesInadimplenciaMensal.searchPessoa,
                    type: 'POST',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function(items) {
                        return {
                            results: items.map(function(item) {
                                return {
                                    text: item.NM_PESSOA,
                                    id: item.ID
                                };
                            })
                        };
                    }
                }
            });
        });

        const data = {
            cd_pessoa: '',
            nm_vendedor: $('#filtro-vendedor').val(),
            cnpj: $('#filtro-cnpj').val(),
            filtro_gerente: $('#filtro-gerente').val(),
            nm_supervisor: $('#filtro-supervisor').val(),
            filtro_cartorio: $('#filtro-cartorio').val(),
            session: true,
            dtFim: dtFim,
            dtInicio: dtInicio
        };

        carregaDadosTela1(data);

        buscarTermo('accordion-inadimplencia-gerente', '#buscarCliente');

        $('#tab-relatorio-cobranca').on('click', function() {
            const tab = 1;
            // Obtem o primeiro dia do mes em 240 dias
            dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');
            dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');

            const data = {
                cd_pessoa: ($("#pessoa").val() || []).join(','),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                filtro_gerente: $('#filtro-gerente').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                filtro_cartorio: $('#filtro-cartorio').val(),
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
            if (!$('#pessoa_ch_cartao').data('select2')) {
                $('#pessoa_ch_cartao').select2({
                    theme: 'bootstrap4',
                    language: 'pt-BR',
                    placeholder: 'Selecione uma ou mais pessoas',
                    allowClear: true,
                    minimumInputLength: 2,
                    ajax: {
                        url: routesInadimplenciaMensal.searchPessoa,
                        type: 'POST',
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                q: params.term,
                                _token: '{{ csrf_token() }}'
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        text: item.NM_PESSOA,
                                        id: item.ID
                                    };
                                })
                            };
                        }
                    }
                }).trigger('change.select2');
            }

            const tab = 2;
            // Obtem o primeiro dia do mes em 240 dias
            dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');
            dtFim = moment().subtract(5, 'days').format('DD.MM.YYYY');
            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            if (!datasSelecionadasCartao) {
                datasSelecionadasCartao = initDateRangePicker('#daterange-ch-cartao', dtInicio, dtFim);
            }
            const data = {
                cd_pessoa: ($("#pessoa_ch_cartao").val() || []).join(','),
                nm_vendedor: $('#filtro-vendedor_ch_cartao').val(),
                cnpj: $('#filtro-cnpj_ch_cartao').val(),
                filtro_gerente: $('#filtro-gerente_ch_cartao').val(),
                nm_supervisor: $('#filtro-supervisor_ch_cartao').val(),
                filtro_cartorio: $('#filtro-cartorio-ch-cartao').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };
            carregaDadosTela2(data);

            buscarTermo('accordion-inadimplencia-gerente-ch-cartao', '#buscarCliente-ch-cartao');
            vincularTabelaAoGrafico("tabela-inadimplencia-meses-ch-cartao", "grafico-inadimplencia-ch-cartao");

        });

        $('#tab-limite-credito').on('click', function() {
            initTableLimiteCredito(routeLimiteCredito);
        });

        $('#tab-prazo-medio').on('click', function() {
            carregarDadosPrazoMedio(routePrazoMedio);
        });

        $('#tab-canhoto').on('click', function() {
            // Obtem o primeiro dia do mes em 240 dias
            dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');
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
                cd_pessoa: ($("#pessoa").val() || []).join(','),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                filtro_gerente: $('#filtro-gerente').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                filtro_cartorio: $('#filtro-cartorio').val(),
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
                cd_pessoa: ($("#pessoa_ch_cartao").val() || []).join(','),
                nm_vendedor: $('#filtro-vendedor_ch_cartao').val(),
                cnpj: $('#filtro-cnpj_ch_cartao').val(),
                filtro_gerente: $('#filtro-gerente_ch_cartao').val(),
                nm_supervisor: $('#filtro-supervisor_ch_cartao').val(),
                filtro_cartorio: $('#filtro-cartorio-ch-cartao').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };

            carregaDadosTela2(data);
        });

        //limpa as filtros e retorna tudo novamente
        $('#btn-reset').on('click', function() {
            hierarquia = null;
            // Obtem o primeiro dia do mes em 240 dias
            dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');
            dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

            $('#daterange').val('');
            $('#filtro-nome').val('');
            $('#filtro-vendedor').val('');
            $('#filtro-cnpj').val('');
            $('#filtro-supervisor').val('');
            $('#filtro-gerente').val(0).change();
            $('#pessoa').val(null).trigger('change');

            const data = {
                cd_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                filtro_cartorio: $('#filtro-cartorio').val(),
                session: false,
                dtFim: dtFim,
                dtInicio: dtInicio
            };

            carregaDadosTela1(data);
        });

        $('#btn-reset-ch-cartao').on('click', function() {
            hierarquia = null;
            // Obtem o primeiro dia do mes em 240 dias
            dtInicio = moment().subtract(240, 'days').startOf('month').format('DD.MM.YYYY');
            dtFim = moment().subtract(5, 'days').format('DD.MM.YYYY');

            $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

            datasSelecionadasCartao = initDateRangePicker('#daterange-ch-cartao', dtInicio, dtFim);

            $('#daterange-ch-cartao').val('');
            $('#filtro-nome_ch_cartao').val('');
            $('#filtro-vendedor_ch_cartao').val('');
            $('#filtro-cnpj_ch_cartao').val('');
            $('#filtro-supervisor_ch_cartao').val('');
            $('#filtro-gerente_ch_cartao').val(0).change();
            $('#pessoa_ch_cartao').val(null).trigger('change');

            const data = {
                cd_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                filtro_cartorio: $('#filtro-cartorio_ch_cartao').val(),
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
@stop
