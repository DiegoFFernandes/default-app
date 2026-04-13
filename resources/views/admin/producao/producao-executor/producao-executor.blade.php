@extends('layouts.master')

@section('title', 'Executor x Etapa')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @include('admin.producao.producao-executor.filtros.filtros')
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Produção por Executor x Etapa</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-2">
                            <div class="nav flex-column nav-tabs" id="tabs-principais" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link active" id="tab-servicos-ativos" data-toggle="pill"
                                    href="#painel-ativos">
                                    Serviços Aprovados
                                </a>
                                <a class="nav-link" id="tab-servicos-recusados" data-toggle="pill" href="#painel-recusados">
                                    Serviços Recusados
                                </a>
                                <a class="nav-link" id="tab-retrabalhos" data-toggle="pill" href="#painel-retrabalhos">
                                    Retrabalhos
                                </a>
                                <a class="nav-link" id="tab-canceladas" data-toggle="pill" href="#painel-canceladas">
                                    Canceladas
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-10">
                            <div class="tab-content" id="ver-tabs-tabContent-servicos">
                                <div class="tab-pane fade show active" id="painel-ativos">
                                    @include('admin.producao.producao-executor.components.servicos', [
                                        'painelPrincipal' => 'painel-ativos',
                                    ])
                                </div>
                                <div class="tab-pane fade" id="painel-recusados">
                                    @include('admin.producao.producao-executor.components.servicos', [
                                        'painelPrincipal' => 'painel-recusados',
                                    ])
                                </div>
                                <div class="tab-pane fade" id="painel-retrabalhos">
                                    @include('admin.producao.producao-executor.components.servicos', [
                                        'painelPrincipal' => 'painel-retrabalhos',
                                    ])
                                </div>
                                <div class="tab-pane fade" id="painel-canceladas">
                                    @include('admin.producao.producao-executor.components.servicos', [
                                        'painelPrincipal' => 'painel-canceladas',
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.producao.producao-executor.modals.modal-details-producao-executor')
    </section>
@stop
@section('css')
    <style>
        .btn-detalhes-executor {
            background-color: #0d6efd26 !important;
            color: #0d6efd !important;
            border: none !important;
            border-radius: 6px !important;
            font-size: 12px !important;
        }

        .btn-detalhes-executor:hover {
            background-color: #0d6efd40 !important;
        }

        .nav-tabs.flex-column {
            border-bottom: 0;
            border-right: none !important;
        }

        #nav-tabs-servicos .nav-link.active {
            background-color: #e9f2ff;
            color: #0d6efd;
            font-weight: 600;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            padding: 6px 12px;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #000;
        }

        .nav-tabs .nav-link.active {
            /* color: #0d6efd; */
            font-weight: 600;
            border-bottom: 2px solid #0d6efd;
            background: transparent;
        }

        #tabs-principais .nav-link.active {
            font-weight: 600;
            border-right: 2px solid #0d6efd;
            background: transparent
        }

        #nav-tabs-setores .nav-link {
            padding: 5px 8px;
            font-size: 12px;
        }

        #nav-tabs-setores .nav-item {
            margin-right: 2px;
        }

        @media (max-width: 767.98px) {
            #tabs-principais {
                padding-bottom: 10px;
            }

            #tabs-principais .nav-link.active {
                border-right: none;
                border-bottom: 2px solid #0d6efd;
            }
        }
    </style>
@stop
@section('js')
    <script id="details-item-pedido" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ NR_ORDEM }} - {{DS_ITEM}}</span>
            <table class="table row-border" id="item-pedido-{{ NR_ORDEM }}" style="width:90%">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>Usúario</th>
                        <th>Entrada</th>
                        <th>Saida</th>
                        <th>Detalhes</th>
                        <th>Retrabalho</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script>
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getExecutorEtapas: "{{ route('get-producao-executor-etapas') }}",
            getDetailsExecutor: "{{ route('get-details-executor') }}",
            getResumoProducaoSetor: "{{ route('get-resumo-producao-setor') }}"
        };

        var details_item_pedido = Handlebars.compile($("#details-item-pedido").html());

        $('#filtro-executor').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        const datasSelecionadas = initDateRangePicker('#daterange', '02.03.2026', '02.03.2026');

        let empresaNome = $('#filtro-empresa option:selected').text();
        let idEmpresa = $('#filtro-empresa').val();

        $('.badge-periodo').text(
            `Periodo: ${datasSelecionadas.getInicio() + ' 00:00'} - ${datasSelecionadas.getFim() + ' 23:59'}`
        );
        $('.badge-empresa').text(`Empresa: ${empresaNome}`);

        const estado = getEstadoAtual();

        initTable(idEmpresa,
            datasSelecionadas.getInicio() + ' 00:00',
            datasSelecionadas.getFim() + ' 23:59',
            estado.tabela1,
            '1',
            estado.idPainel
        );

        initResumoExecutorSetor(
            'setorResumo-' + estado.idPainel,
            datasSelecionadas.getInicio() + ' 00:00',
            datasSelecionadas.getFim() + ' 23:59',
            estado.idSubTab2,
            'Setor'
        );

        //Aguarda Click para buscar os detalhes dos itens do pedido
        configurarDetalhesLinha('.btn-acompanhamento-executor', {
            idPrefixo: 'item-pedido-',
            idCampo: 'NR_ORDEM',
            templateFn: details_item_pedido,
            initFn: initTableItemPedido,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle'
        });

        // Clique nas tabs principais para mostrar a subtab correspondente e atualizar a tabela
        $('#tabs-principais .nav-link').on('shown.bs.tab', function(e) {
            const estado = getEstadoAtual();

            console.log(estado);

            $('#tab-exame-inicial-' + estado.idPainel).tab('show');

            initTable(idEmpresa,
                datasSelecionadas.getInicio() + ' 00:00',
                datasSelecionadas.getFim() + ' 23:59',
                estado.tabela1,
                '1',
                estado.idPainel
            );

            initResumoExecutorSetor(
                'setorResumo-' + estado.idPainel,
                datasSelecionadas.getInicio() + ' 00:00',
                datasSelecionadas.getFim() + ' 23:59',
                estado.idSubTab2,
                'Setor');

        });

        //Clique nas subtabs1 etapas para atualizar a tabela de acordo com a etapa selecionada
        $(document).on('shown.bs.tab', '.grupo-tabs .nav-link', function(e) {
            const estado = getEstadoAtual();

            const dt_inicio = datasSelecionadas.getInicio() + ' 00:00';
            const dt_fim = datasSelecionadas.getFim() + ' 23:59';
            const executor = $('#filtro-executor').val();

            const tabs = [{
                    nomeTabela: '1',
                    tableId: 'table-exame-inicial'
                },
                {
                    nomeTabela: '2',
                    tableId: 'table-raspa'
                },
                {
                    nomeTabela: '3',
                    tableId: 'table-prep-banda'
                },
                {
                    nomeTabela: '4',
                    tableId: 'table-escareacao'
                },
                {
                    nomeTabela: '5',
                    tableId: 'table-limpeza-manchao'
                },
                {
                    nomeTabela: '6',
                    tableId: 'table-cola'
                },
                {
                    nomeTabela: '9',
                    tableId: 'table-emborrachamento'
                },
                {
                    nomeTabela: '11',
                    tableId: 'table-vulcanizacao'
                },
                {
                    nomeTabela: '12',
                    tableId: 'table-exame-final'
                }
            ];

            const tabSelecionada = tabs.find(tab => tab.tableId + '-' + estado.idPainel === estado.tabela1);

            initTable(idEmpresa,
                dt_inicio,
                dt_fim,
                estado.tabela1,
                tabSelecionada.nomeTabela,
                estado.idPainel
            );
        });

        //Clique nas subtabs2 resumos para atualizar a tabela de acordo com a etapa selecionada
        $(document).on('shown.bs.tab', '.grupo-tabs-resumo  .nav-link', function(e) {
            const estado = getEstadoAtual();

            const dt_inicio = datasSelecionadas.getInicio() + ' 00:00';
            const dt_fim = datasSelecionadas.getFim() + ' 23:59';

            const executor = $('#filtro-executor').val();

            // console.log(estado.idSubTab2);

            if (estado.idSubTab2 === 'resumo-setor-painel-retrabalhos') {
                initResumoExecutorSetor(
                    estado.tabela2,
                    dt_inicio,
                    dt_fim,
                    estado.idSubTab2,
                    'Setor',
                    executor
                );
                return;
            } else {

                montaFooterTabelaResumo('executorResumo-' + estado.idPainel);

                initResumoExecutorSetor(
                    estado.tabela2,
                    dt_inicio,
                    dt_fim,
                    estado.idSubTab2,
                    'Executor',
                    executor
                );
            }
        });

        //Busca os dados ao clicar no botão "Buscar novos"
        $('#submit-seach').on('click', function() {

            const estado = getEstadoAtual();

            $('#tab-servicos-ativos').tab('show');
            $('#tab-exame-inicial-' + estado.idPainel).tab('show');
            $('#resumo-setor-tab-' + estado.idPainel).tab('show');

            const inicioData = datasSelecionadas.getInicio() + ' 00:00';
            const fimData = datasSelecionadas.getFim() + ' 23:59';

            const empresa = $('#filtro-empresa').val(); // puxa o código da empresa
            const empresaNome = $('#filtro-empresa option:selected').text(); //puxa o nome da empresa

            const executor = $('#filtro-executor').val(); // puxa o código do executor
            const executorNome = $('#filtro-executor option:selected')
                .text(); // puxa o nome do executor                 

            // automatiza o  badge-danger da empresa 
            $('.badge-empresa').text(`Empresa: ${empresaNome}`);
            // automatiza o  badge-danger do executor
            $('.badge-executor').text(`Executor: ${executorNome}`);
            // automatiza o  badge-danger do periodo e formata o valor 
            $('.badge-periodo').text(
                `Período: ${moment(datasSelecionadas.getInicio() + ' 00:00', "DD/MM/YYYY HH:mm").format("DD/MM/YYYY HH:mm")} - ${moment(datasSelecionadas.getFim() + ' 23:59', "DD/MM/YYYY HH:mm").format("DD/MM/YYYY HH:mm")}`
            );

            if (estado.idPainel === 'painel-canceladas') {
                initTableCanceladas(
                    empresa,
                    inicioData,
                    fimData,
                    'table-canceladas-painel-canceladas',
                    '1',
                    estado.idPainel);
                return;
            }

            initTable(idEmpresa, inicioData, fimData, 'table-exame-inicial-painel-ativos', '1',
                'painel-ativos');

            if (estado.idSubTab2 === 'resumo-setor-painel-ativos') {
                initResumoExecutorSetor(
                    'setorResumo-' + estado.idPainel,
                    inicioData,
                    fimData,
                    estado.idSubTab2,
                    'Setor',
                    executor
                );
                return;
            }


        });

        $(document).on('click', '.btn-detalhes-executor', function() {
            let cd_empresa = $(this).data('cd_empresa');
            let dt_inicio = $(this).data('dt_fim');
            let dt_fim = $(this).data('dt_fim');
            let idexecutor = $(this).data('idexecutor');
            let nomeTabela = $(this).data('tabela');
            let estado = getEstadoAtual();
            let origem = 'analitico';

            //mudo os dados para trazer os detalhes por setor
            if ($(this).data('idetapa')) {
                cd_empresa = $('#filtro-empresa').val();
                dt_inicio = datasSelecionadas.getInicio() + ' 00:00';
                dt_fim = datasSelecionadas.getFim() + ' 23:59';
                idexecutor = $('#filtro-executor').val();
                nomeTabela = $(this).data('idetapa');
                origem = 'sintetico';
            }

            $('#modal-details-executor').modal('show');
            initTableDetailsExecutor(cd_empresa, dt_inicio, dt_fim, idexecutor, nomeTabela, estado.idPainel,
                origem);

        });

        // Função para iniciar/reiniciar a DataTable
        function initTable(cdempresa, dtinicio, dtfim, idTabelaDataTable, nomeTabela, painel) {

            idEmpresa = $('#filtro-empresa').val();
            idExecutor = $('#filtro-executor').val();

            if ($.fn.DataTable.isDataTable('#' + idTabelaDataTable)) {
                $('#' + idTabelaDataTable).DataTable().destroy();
            }

            tabela = $('#' + idTabelaDataTable).DataTable({
                processing: false,
                serverSide: false,
                pageLength: 100,
                pagingType: 'simple',
                searching: false,
                scrollY: '400px',
                autoWidth: false,
                deferRender: true,
                scrollCollapse: true,
                layout: {
                    topStart: {
                        buttons: [{
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 6]
                                },
                                title: "Relatório " + retornaDsPainel(painel) + " - Executor x Etapa " +
                                    retornarNomeTabela(
                                        nomeTabela),
                                footer: true
                            },
                            {
                                extend: "print",
                                title: "Relatório " + retornaDsPainel(painel) + " - Executor x Etapa " +
                                    retornarNomeTabela(
                                        nomeTabela),
                                footer: true,
                                exportOptions: {
                                    columns: [1, 2, 3, 6]
                                },
                                customize: function(win) {
                                    $(win.document.body)
                                        .find("h1")
                                        .css("font-size", "12pt")
                                        .css("color", "#333");
                                },
                            },
                        ],
                    },
                },
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: {
                    url: window.routes.getExecutorEtapas,
                    type: 'GET',
                    data: {
                        cd_empresa: idEmpresa,
                        dt_inicio: dtinicio,
                        dt_fim: dtfim,
                        tabela: nomeTabela,
                        executor: idExecutor,
                        painel: painel
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        title: 'Ações',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        "width": '3%',
                    },

                    {
                        data: 'IDEMPRESA',
                        name: 'IDEMPRESA',
                        title: 'Emp',
                        className: 'text-center',
                        "width": '1%',
                    },
                    {
                        data: 'NM_EXECUTOR',
                        name: 'NM_EXECUTOR',
                        title: 'Executor',
                        className: 'text-nowrap',
                        "width": '10%',
                    },
                    {
                        data: 'QTD',
                        name: 'QTD',
                        title: 'Produzidos',
                        "width": '3%',
                    },
                    {
                        data: 'META',
                        name: 'META',
                        title: 'Meta',
                        "width": '3%',
                        visible: false
                    },
                    {
                        data: 'QTD_RETRABALHO',
                        name: 'QTD_RETRABALHO',
                        title: 'Retrabalho',
                        "width": '3%',
                        visible: false
                    },
                    {
                        data: 'DT_FIM',
                        name: 'DT_FIM',
                        title: 'Finalização',
                        className: 'text-center',
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY'),
                        "width": '2%',
                    },
                ],
                order: [
                    [6, 'asc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var totalProduzidos = api
                        .column(3, {
                            search: 'applied'
                        })
                        .data().reduce(function(a, b) {
                            return a + parseInt(b || 0);
                        }, 0);

                    var totalRetrabalho = api
                        .column(5, {
                            search: 'applied'
                        })
                        .data().reduce(function(a, b) {
                            return a + parseInt(b || 0);
                        }, 0);

                    $(api.column(3).footer()).html(totalProduzidos);
                    $(api.column(5).footer()).html(totalRetrabalho);

                    // $(api.column(6).footer()).html(totalProduzidos - totalRetrabalho);
                }
            });

            setTimeout(() => {
                tabela.columns.adjust().draw();
            }, 100)
        }

        function initTableCanceladas(cdempresa, dtinicio, dtfim, idTabelaDataTable, nomeTabela, painel) {

            idEmpresa = $('#filtro-empresa').val();

            if ($.fn.DataTable.isDataTable('#' + idTabelaDataTable)) {
                $('#' + idTabelaDataTable).DataTable().destroy();
            }

            tabela = $('#' + idTabelaDataTable).DataTable({
                processing: false,
                serverSide: false,
                pageLength: 100,
                pagingType: 'simple',
                // scrollY: '400px',                
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: {
                    url: window.routes.getExecutorEtapas,
                    type: 'GET',
                    data: {
                        cd_empresa: idEmpresa,
                        dt_inicio: dtinicio,
                        dt_fim: dtfim,
                        tabela: nomeTabela,
                        painel: painel
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        title: 'Ações',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        "width": '3%',
                    },

                    {
                        data: 'IDEMPRESA',
                        name: 'IDEMPRESA',
                        title: 'Emp',
                        className: 'text-center',
                        "width": '1%',
                    },
                    {
                        data: 'QTD',
                        name: 'QTD',
                        title: 'Canceladas',
                        "width": '3%',
                    },
                    {
                        data: 'DT_FIM',
                        name: 'DT_FIM',
                        title: 'Data de Cancelamento',
                        className: 'text-center',
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY'),
                        "width": '2%',
                    },
                ],
                order: [
                    [3, 'asc']
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var total = api.
                    column(2, {
                        search: 'applied'
                    }).data().reduce(function(a, b) {
                        return a + parseInt(b);
                    }, 0);
                    $(api.column(2).footer()).html(total);
                }
            });
        }

        function initTableDetailsExecutor(cd_empresa, dt_inicio, dt_fim, idexecutor = null, tabela, painel, origem) {

            if ($.fn.DataTable.isDataTable('#details-executor-table')) {
                $('#details-executor-table').DataTable().destroy();
            }

            const columns = [];

            columns.push({
                data: 'action',
                name: 'action',
                title: '#',
                className: 'text-center',
                orderable: false,
                searchable: false,
            }, {
                data: 'IDEMPRESA',
                name: 'IDEMPRESA',
                title: 'Emp',
                className: 'text-center',
                "width": '1%',
            }, {
                data: 'NM_EXECUTOR',
                name: 'NM_EXECUTOR',
                title: 'Executor',
                className: 'text-nowrap',
            }, {
                data: 'NR_ORDEM',
                name: 'NR_ORDEM',
                title: 'Ordem',
                className: 'text-center',
                "width": '1%',
            }, {
                data: 'DS_ITEM',
                name: 'DS_ITEM',
                title: 'Serviço',
                className: 'text-nowrap'
            }, {
                data: 'DT_FIM',
                name: 'DT_FIM',
                title: 'Finalização',
                className: 'text-nowrap',
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY HH:mm:ss'),
            }, {
                data: 'ST_RETRABALHO',
                name: 'ST_RETRABALHO',
                title: 'Retrabalho',
                className: 'text-center',
                "width": '1%',
            })


            return $('#details-executor-table').DataTable({
                processing: false,
                serverSide: false,
                pageLength: 100,
                pagingType: 'simple',
                scrollY: '400px',
                scrollCollapse: true,
                language: {
                    url: window.routes.languageDatatables
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: "excelHtml5",
                                exportOptions: {
                                    columns: [1, 2, 3, 5]
                                },
                                title: "Relatório Executor x Etapa",
                                footer: true
                            },
                            {
                                extend: "print",
                                title: "Relatório Executor x Etapa",
                                footer: true,
                                exportOptions: {
                                    columns: [1, 2, 3, 5]
                                },
                                customize: function(win) {
                                    $(win.document.body)
                                        .find("h1")
                                        .css("font-size", "12pt")
                                        .css("color", "#333");
                                },
                            },
                        ],
                    },
                },
                ajax: {
                    url: window.routes.getDetailsExecutor,
                    data: {
                        cd_empresa: cd_empresa,
                        dt_inicio: dt_inicio,
                        dt_fim: dt_fim,
                        idexecutor: idexecutor,
                        tabela: tabela,
                        painel: painel,
                        origem: origem
                    },
                    type: 'GET',
                },
                columns: columns,
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var total_linhas = api.data().count();
                    $(api.column(5).footer()).html(total_linhas);
                }
            });
        }

        function initTableDetailsCanceladas(cd_empresa, dt_fim, IdDataTables, tabelaDb, painel) {

            if ($.fn.DataTable.isDataTable('#' + IdDataTables)) {
                $('#' + IdDataTables).DataTable().destroy();
            }


            return $('#' + IdDataTables).DataTable({
                processing: false,
                serverSide: false,
                pageLength: 100,
                pagingType: 'simple',
                scrollY: '400px',
                scrollCollapse: true,
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: {
                    type: 'GET',
                    url: window.routes.getDetailsExecutor,
                    data: {
                        cd_empresa: cd_empresa,
                        dt_fim: dt_fim,
                        dt_inicio: dt_fim,
                        tabela: tabelaDb,
                        painel: painel
                    },
                },
                columns: [{
                    data: 'IDEMPRESA',
                    name: 'IDEMPRESA',
                    title: 'Emp',
                    className: 'text-center',
                    "width": '1%',
                }, {
                    data: 'NR_ORDEM',
                    name: 'NR_ORDEM',
                    title: 'Ordem',
                    className: 'text-center',
                    "width": '1%',
                }, {
                    data: 'DS_ITEM',
                    name: 'DS_ITEM',
                    title: 'Serviço',
                    className: 'text-nowrap'
                }, {
                    data: 'DT_FIM',
                    name: 'DT_FIM',
                    title: 'Cancelamento',
                    className: 'text-center',
                    "width": '2%',
                }, {
                    data: 'ST_RETRABALHO',
                    name: 'ST_RETRABALHO',
                    title: 'Motivo',
                    className: 'text-center',
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var total_linhas = api.data().count();
                    $(api.column(3).footer()).html(total_linhas);
                }
            });
        }

        function initTableItemPedido(tableId, data) {

            console.log(tableId);
            $('#' + tableId).DataTable({
                language: {
                    url: window.routes.languageDatatables,
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                autoWidth: false,
                sDom: 't',
                ajax: data.details_item_pedido_url,
                columns: [{
                        data: 'O_DS_ETAPA',
                        name: 'O_DS_ETAPA'
                    },
                    {
                        data: 'O_NM_USUARIO',
                        name: 'O_NM_USUARIO',
                        // "width": "15%"
                    },
                    {
                        data: 'DT_ENTRADA',
                        name: 'DT_ENTRADA',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'DT_SAIDA',
                        name: 'DT_SAIDA',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'O_DS_COMPLEMENTOETAPA',
                        name: 'O_DS_COMPLEMENTOETAPA',
                        visible: false
                    },
                    {
                        data: 'O_ST_RETRABALHO',
                        name: 'O_ST_RETRABALHO',
                        visible: false
                        // width: "2%",
                    },
                ],
                columnDefs: [{
                    targets: [1],
                    className: 'text-truncate'
                }],
                order: [
                    [10, 'asc']
                ]

            });
        }

        function getEstadoAtual() {
            const tabPrincipal = $('#tabs-principais .nav-link.active');

            const painel = tabPrincipal.attr('href');
            const idPainel = painel.replace('#', '');

            const resultado = [];

            $(painel).find('.grupo-tabs').each(function() {
                const ativas = $(this).find('.nav-link.active');

                if (ativas.length) {
                    const href = ativas.attr('href');
                    // console.log(href);
                    resultado.push({
                        idSubTab: href.replace('#', ''),
                        tabela: $(href).data('tabela') || null
                    });
                }
            });

            const idSubTab1 = resultado[0]?.idSubTab || null;
            const tabela1 = resultado[0]?.tabela || null;

            const idSubTab2 = resultado[1]?.idSubTab || null;
            const tabela2 = resultado[1]?.tabela || null;

            return {
                idPainel,
                idSubTab1,
                tabela1,
                idSubTab2,
                tabela2
            };
        }

        function initResumoExecutorSetor(idTabelaDatatable, dt_inicio, dt_fim, idSubPainel, tipoResumo = 'Setor',
            idExecutor = 0) {

            const estado = getEstadoAtual();
            idEmpresa = $('#filtro-empresa').val();
            idExecutor = $('#filtro-executor').val();

            // console.log(idTabelaDatatable);

            if ($.fn.DataTable.isDataTable('#' + idTabelaDatatable)) {
                $('#' + idTabelaDatatable).DataTable().destroy();
            }

            return $('#' + idTabelaDatatable).DataTable({
                processing: false,
                serverSide: false,
                paging: false,
                scrollY: '400px',
                searching: false,
                ordering: false,
                language: {
                    url: window.routes.languageDatatables
                },
                ajax: {
                    url: window.routes.getResumoProducaoSetor,
                    type: 'GET',
                    data: {
                        cd_empresa: idEmpresa,
                        dt_inicio: dt_inicio,
                        dt_fim: dt_fim,
                        executor: idExecutor,
                        painel: estado.idPainel,
                        subPainel: idSubPainel
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        title: '#',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        "width": '3%',
                    },
                    {
                        title: tipoResumo,
                        data: 'SETOR',
                        width: '70%'
                    },
                    {
                        title: 'Quantidade',
                        data: 'QTD',
                        width: '30%'
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var total = api
                        .column(1, {
                            search: 'applied'
                        })
                        .data().reduce(function(a, b) {
                            return a + parseInt(b || 0);
                        }, 0);
                    $(api.column(1).footer()).html(total);
                }
            });

        }

        function montaFooterTabelaResumo(idTabela) {
            $('#' + idTabela + ' tfoot').remove();

            $('#' + idTabela).append(
                `<tfoot>
                    <tr>
                        <th>Total:</th>
                        <th></th>
                    </tr>
                </tfoot>`
            );
        }

        function retornarNomeTabela(nomeTabela) {
            switch (nomeTabela) {
                case '1':
                    return 'Exame Inicial';
                case '2':
                    return 'Raspagem de Pneu';
                case '3':
                    return 'Preparação de Banda de Pneu';
                case '4':
                    return 'Escareação de Pneu';
                case '5':
                    return 'Limpeza de Manchão';
                case '6':
                    return 'Aplicação de Cola de Pneu';
                case '9':
                    return 'Emborrachamento';
                case '11':
                    return 'Vulcanização';
                case '12':
                    return 'Exame Final de Pneu';
                default:
                    return nomeTabela;
            }
        }

        function retornaDsPainel(painel) {
            switch (painel) {
                case 'painel-ativos':
                    return 'Serviços Aprovados';
                case 'painel-recusados':
                    return 'Serviços Recusados';
                case 'painel-retrabalhos':
                    return 'Retrabalhos';
                case 'painel-canceladas':
                    return 'Canceladas';
                default:
                    return painel;
            }
        }
    </script>
@stop
