@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">

        @include('admin.producao.pcp.cards.cards')
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.producao.pcp.tabs.nav-tabs')
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            @include('admin.producao.pcp.tabs.painel-pneus-atraso')

                            @include('admin.producao.pcp.tabs.painel-lotes')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.producao.pcp.modals.modal-pneus-lote')
    @include('admin.producao.pcp.modals.modal-bandas-sem-associacao')
    @include('admin.producao.pcp.modals.modal-adicionar-lote-pcp')
    @include('admin.producao.pcp.modals.modal-transferir-lote-pcp')
    @include('admin.producao.pcp.modals.modal-adicionar-pneus-lote-pcp')
@stop

@section('css')
    <style>
        .info-box-custom {
            border-radius: 12px;
            padding: 6px;
            transition: all 0.2s ease;
            min-height: 60px !important;
            display: flex;
            justify-content: center;
            /* centraliza horizontalmente */
            align-items: center;
            /* centraliza verticalmente */
        }

        /* Hover suave */
        .info-box-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Ícone mais proporcional */
        .info-box-custom .info-box-icon {
            font-size: 20px;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            margin: 0 10px 0 10px;
            display: flex !important;
            align-items: center !important;
            /* centraliza vertical */
            justify-content: center !important;
            /* centraliza horizontal */

        }

        /* Texto */
        .info-box-custom .info-box-text {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        /* Número principal */
        .info-box-custom .info-box-number {
            font-size: 20px;
            margin-top: -4px !important;
            font-weight: 600;
        }

        /* Percentual */
        .info-box-custom .percentual {
            font-size: 12px;
            color: #6c757d;
            margin-left: 4px;
        }

        @keyframes piscar {

            0%,
            100% {
                background-color: transparent;
            }

            50% {
                background-color: var(--blink-color);
            }
        }

        .badge-atrasado {
            animation: piscar 1.5s infinite;
            transition: background-color 0.3s ease;
        }

        /* Danger */
        .badge-atrasado-danger {
            --blink-color: #f8d7da;
        }

        /* Warning */
        .badge-atrasado-warning {
            --blink-color: #fff3cd;
        }

        .badge-atrasado-dias-purple {
            --blink-color: #d6b3ff;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/dashboard/painelPCP/lotepcp.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/painelPCP/adicionar-lotepcp.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/painelPCP/adicionar-pneus-lote-pcp.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/painelPCP/remover-lotepcp.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/painelPCP/transferir-pneu-lote-pcp.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/painelPCP/atualiza-tela-pcp.js') }}?v={{ time() }}"></script>

    <script>
        function collapseMenu() {
            $('[data-widget="pushmenu"]').PushMenu('collapse');
        }

        $(window).on('load resize', function() {
            setTimeout(() => {
                collapseMenu();
            }, 50);
        });

        window.routes = {
            token: "{{ csrf_token() }}",
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getPneusAtrasoLotePcp: "{{ route('get-pneus-atraso-lote-pcp') }}",
            getLotePcp: "{{ route('get-lote-pcp') }}",
            detalhesPneusLotePcp: "{{ route('detalhes-pneus-lote-pcp') }}",
            consumoEstoqueLoteMateriaPrima: "{{ route('consumo-estoque-lote-materia-prima') }}",
            bandasSemAssociacao: "{{ route('bandas-sem-associacao') }}",
            getControleLotePCP: "{{ route('get-controle-lote-pcp') }}",
            getExecutorEtapa: "{{ route('get-executor-etapa') }}",
            removerOrdemProducaoLotePcp: "{{ route('remover-ordem-producao-lote-pcp') }}",
            salvarLotePcp: "{{ route('salvar-lote-pcp') }}",
            getListLotePCPEmProducao: "{{ route('get-lote-pcp-em-producao') }}",
            transferirPneusLotePcp: "{{ route('atualiza-lote-pneus-lote-pcp') }}",
            getListPneusLoteSemPCP: "{{ route('get-list-pneus-lote-sem-pcp') }}",
            getListPedidosSemPCP: "{{ route('get-list-pedidos-sem-pcp') }}",
            getListOrdensProducaoSemPCP: "{{ route('get-list-ordens-producao-sem-pcp') }}",
            salvarPneusLotePcp: "{{ route('salvar-pneus-lote-pcp') }}",
        }

        const empresa = @json($empresa);
        var lotePcpTable;
        var totalPneusLote = 0;
        var totalEmProducao = 0;
        var totalAtraso = 0;
        var totalIniciando = 0;
        var totalFinalizados = 0;
        var totalSemExame = 0;
        var totalLotes = 0;
        var totalLotesAtraso = 0;
        var pcAtrasado = 0;
        var canLotePcp = @json($canEditPCP);

        initTable('pneus-lote-pcp-' + empresa[0].CD_EMPRESA, empresa[0].CD_EMPRESA);

        $('#tab-painelPCP-' + empresa[0].CD_EMPRESA).addClass('active');
        $('#painel-pcp-' + empresa[0].CD_EMPRESA).addClass('show active');

        $('#tab-pcp').on('shown.bs.tab', 'a.nav-link', function() {
            let empresa = $(this).data('empresa');
            if (empresa) {
                initTable('pneus-lote-pcp-' + empresa, empresa);
            }
        });

        
        function initTable(idTabela, cdEmpresa) {
            if ($.fn.DataTable.isDataTable('#' + idTabela)) {
                $('#' + idTabela).DataTable().destroy();
            }

            let columns = [{
                    data: null,
                    width: "1%",
                    className: 'pl-3 pr-3 text-center',
                    render: DataTable.render.select(),
                    orderable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    title: 'Ações',
                    width: "1%",
                    className: 'text-center no-wrap',
                    visible: false,
                    orderable: false,
                },
                {
                    data: 'NR_LOTE',
                    name: 'NR_LOTE',
                    title: 'Lote',
                    width: '5%',
                    className: 'text-center'
                },
                {
                    data: 'NR_COLETA',
                    name: 'NR_COLETA',
                    title: 'Pedido',
                    width: '5%',
                    className: 'text-center'
                },
                {
                    data: 'NR_OP',
                    name: 'NR_OP',
                    title: 'Ordem',
                    width: '5%',
                    className: 'text-center'
                },
                {
                    data: 'NM_PESSOA',
                    name: 'NM_PESSOA',
                    title: 'Cliente',
                },
                {
                    data: 'DSSERVICO',
                    name: 'DSSERVICO',
                    title: 'Serviço',
                    className: 'no-wrap',
                }, {
                    data: 'DT_EXAME',
                    name: 'DT_EXAME',
                    title: 'Exa.Inicial',
                    className: 'text-center no-wrap',
                }, {
                    data: 'DT_MANCHAO',
                    name: 'DT_MANCHAO',
                    title: 'Manchão',
                    className: 'text-center no-wrap',
                }, {
                    data: 'DT_COBER',
                    name: 'DT_COBER',
                    title: 'Cobertura',
                    className: 'text-center no-wrap',
                }, {
                    data: 'DT_VULC',
                    name: 'DT_VULC',
                    title: 'Vulcanização',
                    className: 'text-center no-wrap',
                }, {
                    data: 'DT_FINAL',
                    name: 'DT_FINAL',
                    title: 'Exame Final',
                    className: 'text-center no-wrap',

                    visible: false,
                }, {
                    data: 'DS_ETAPA',
                    name: 'DS_ETAPA',
                    title: 'Última.Etapa',
                    className: 'text-center no-wrap',

                },
                {
                    data: 'DSOBSERVACAO',
                    name: 'DSOBSERVACAO',
                    title: 'Observação',

                    visible: false,
                }
            ];


            $('#' + idTabela).DataTable({
                pageLength: 100,
                pagingType: "simple",
                destroy: true,
                processing: false,
                serverSide: false,
                scrollY: '400px',
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                language: {
                    url: window.routes.languageDatatables,
                },
                ajax: {
                    url: window.routes.getPneusAtrasoLotePcp,
                    type: 'POST',
                    data: {
                        _token: window.routes.token,
                        cd_empresa: cdEmpresa
                    },
                    dataSrc: function(json) {
                        lotePcpTable = json.lote;

                        totalPneusLote = 0;
                        totalEmProducao = 0;
                        totalLotesAtraso = 0;

                        //busca a quantidade de lotes
                        totalLotes = lotePcpTable.length;

                        lotePcpTable.forEach(function(lote) {
                            totalPneusLote += parseInt(lote.QTDE_TOT_LOTE);
                            totalEmProducao += parseInt(lote.QTDE_EM_PROD);

                            const [ano, mes, dia] = lote.DTPRODUCAO.split('-');
                            // -1 e o mes anterior, Date inicia em 0
                            const dtFim = new Date(ano, mes - 1, dia);
                            const hoje = new Date();
                            hoje.setHours(0, 0, 0, 0);

                            if (dtFim < hoje) {
                                totalLotesAtraso++;
                            }
                        });

                        return json.datatables.data || [];
                    }
                },
                columns: columns,
                columnDefs: [{
                    targets: [7, 8, 9, 10, 11],
                    render: function(data, type, row) {
                        if (type === "display" || type === "filter") {
                            return data ? moment(data).format("DD/MM/YYYY HH:mm:ss") : '';
                        }
                        return data;
                    },
                }],
                createdRow: function(row, data, dataIndex) {
                    const [ano, mes, dia] = data.DTFIM.split('-');

                    // -1 e o mes anterior, Date inicia em 0 
                    const dtFim = new Date(ano, mes - 1, dia);

                    const hoje = new Date();
                    hoje.setHours(0, 0, 0, 0);

                    // adiciona a classe de atraso dependendo da etapa e fica piscando  
                    if (dtFim < hoje) {
                        $(row).addClass('badge-atrasado badge-atrasado-dias-purple');
                    } else if (parseInt(data.CD_ETAPA) === 0) {
                        $(row).addClass('badge-atrasado badge-atrasado-danger');
                    } else if (parseInt(data.CD_ETAPA) === 1) {
                        $(row).addClass('badge-atrasado badge-atrasado-warning');
                    }
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    var data = api.rows().data();

                    // variáveis de contagem

                    totalAtraso = data.length;
                    totalIniciando = 0;
                    totalFinalizados = 0;
                    totalSemExame = 0;
                    pcAtrasado = totalEmProducao > 0 ? ((totalAtraso / totalEmProducao) * 100).toFixed(2) : 0;


                    data.each(function(rowData) {
                        // formata a string para nao dar erros
                        let cd_etapa = parseInt(rowData.CD_ETAPA);

                        if (cd_etapa === 1) {
                            totalIniciando++;
                        }

                        if (cd_etapa === 0) {
                            totalSemExame++;
                        }
                    });



                    //atualiza o card de quantidade de lotes
                    $('#lotes').html(totalLotes);

                    if (totalLotesAtraso > 0) {
                        $('#lotesAtraso').html(
                            '<small class="badge badge-atrasado badge-atrasado-dias-purple">' +
                            totalLotesAtraso + ' Atrasados</small>');
                    } else {
                        $('#lotesAtraso').html('<small class="text-muted">0 atraso</small>');
                    }

                    // atualiza os cards
                    $('#card-pneus-em-producao').html(totalPneusLote + ' / ' + totalEmProducao);
                    $('#card-pneus-atraso').html(totalAtraso + ' <small class="percentual text-muted">' +
                        pcAtrasado +
                        '%</small>');
                    $('#card-pneus-iniciando').text(totalIniciando);
                    $('#card-pneus-sem-exame').text(totalSemExame);

                    if (totalSemExame > 0) {
                        $('.bg-pneu-sem-exame').addClass('badge-atrasado badge-atrasado-danger').removeClass(
                            'bg-success');
                        $('.bg-pneu-sem-exame').find('i').css('color', 'red');
                    } else {
                        $('.bg-pneu-sem-exame').removeClass('badge-atrasado badge-atrasado-danger').addClass(
                            'bg-success');
                        $('.bg-pneu-sem-exame').find('i').css('color', 'white');
                    }
                    $('#card-pneus-finalizados').text(totalPneusLote - totalEmProducao);
                },
                order: [
                    [2, 'asc']
                ]
            });
        }
    </script>
@stop
