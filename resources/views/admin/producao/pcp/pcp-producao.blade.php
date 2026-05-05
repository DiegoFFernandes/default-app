@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">

        @include('admin.producao.pcp.cards.cards')
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">


                            <ul class="nav nav-tabs border-bottom-0" id="tab-pcp" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-lotesPCP" data-toggle="pill" href="#painel-lotesPCP"
                                        role="tab" aria-controls="painel-lotesPCP" aria-selected="false">
                                        Lotes PCP
                                    </a>
                                </li>
                                @foreach ($empresa as $emp)
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-painelPCP-{{ $emp->CD_EMPRESA }}" data-toggle="pill"
                                            href="#painel-pcp-{{ $emp->CD_EMPRESA }}" role="tab"
                                            aria-controls="painel-pcp-{{ $emp->CD_EMPRESA }}" aria-selected="false"
                                            data-empresa="{{ $emp->CD_EMPRESA }}">
                                            {{ $emp->NM_EMPRESA }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-tools" >
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="atualizarTela" checked="">
                                <label for="atualizarTela" class="custom-control-label">Atualizar 5 minutos</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            @foreach ($empresa as $emp)
                                <div class="tab-pane fade" id="painel-pcp-{{ $emp->CD_EMPRESA }}" role="tabpanel"
                                    aria-labelledby="tab-painelPCP-{{ $emp->CD_EMPRESA }}">
                                    <table id="pneus-lote-pcp-{{ $emp->CD_EMPRESA }}"
                                        class="table compact table-font-small table-striped table-bordered"
                                        style="font-size: 11px;">
                                    </table>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="painel-lotesPCP" role="tabpanel" aria-labelledby="tab-lotesPCP">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Lotes em Aberto</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="lote-pcp"
                                                        class="table compact table-font-small table-striped table-bordered"
                                                        style="width:100%; font-size: 11px;">
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Bandas a consumir</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="bandas-consumir"
                                                        class="table compact table-font-small table-striped table-bordered"
                                                        style="width:100%">
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.producao.pcp.modals.modal-pneus-lote')
    @include('admin.producao.pcp.modals.modal-bandas-sem-associacao')
@stop

@section('css')
    <style>
        .info-box-custom {
            border-radius: 12px;
            padding: 6px;
            transition: all 0.2s ease;
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
    </style>
@stop

@section('js')
    <script src="{{ asset('js/dashboard/painelPCP/lotepcp.js') }}?v={{ time() }}"></script>
    <script>
        window.routes = {
            token: "{{ csrf_token() }}",
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            getPneusAtrasoLotePcp: "{{ route('get-pneus-atraso-lote-pcp') }}",
            getLotePcp: "{{ route('get-lote-pcp') }}",
            detalhesPneusLotePcp: "{{ route('detalhes-pneus-lote-pcp') }}",
            consumoEstoqueLoteMateriaPrima: "{{ route('consumo-estoque-lote-materia-prima') }}",
            bandasSemAssociacao: "{{ route('bandas-sem-associacao') }}",
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
        var pcAtrasado = 0;

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

            $('#' + idTabela).DataTable({
                pageLength: 100,
                pagingType: "simple",
                destroy: true,
                processing: false,
                serverSide: false,
                scrollY: '400px',
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
                        //busca a quantidade de lotes
                        totalLotes = lotePcpTable.length;

                        lotePcpTable.forEach(function(lote) {
                            totalPneusLote += parseInt(lote.QTDE_TOT_LOTE);
                            totalEmProducao += parseInt(lote.QTDE_EM_PROD);

                        });

                        return json.datatables.data || [];
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        title: '#',
                        width: '5%',
                        orderable: false,
                    },
                    {
                        data: 'NR_LOTE',
                        name: 'NR_LOTE',
                        title: 'Lote',
                        className: 'text-center'
                    },
                    {
                        data: 'NR_COLETA',
                        name: 'NR_COLETA',
                        title: 'Pedido',
                        className: 'text-center'
                    },
                    {
                        data: 'NR_OP',
                        name: 'NR_OP',
                        title: 'Ordem',
                        className: 'text-center'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente',
                        width: '20%'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO',
                        title: 'Serviço',
                        className: 'no-wrap',
                        width: '20%'
                    }, {
                        data: 'DT_EXAME',
                        name: 'DT_EXAME',
                        title: 'Exame Inicial',
                        className: 'text-center no-wrap',
                        width: '15%'
                    }, {
                        data: 'DT_MANCHAO',
                        name: 'DT_MANCHAO',
                        title: 'Manchão',
                        className: 'text-center no-wrap',
                        width: '10%'
                    }, {
                        data: 'DT_COBER',
                        name: 'DT_COBER',
                        title: 'Cobertura',
                        className: 'text-center no-wrap',
                        width: '10%'
                    }, {
                        data: 'DT_VULC',
                        name: 'DT_VULC',
                        title: 'Vulcanização',
                        className: 'text-center no-wrap',
                        width: '10%'
                    }, {
                        data: 'DT_FINAL',
                        name: 'DT_FINAL',
                        title: 'Exame Final',
                        className: 'text-center no-wrap',
                        width: '10%',
                        visible: false,
                    }, {
                        data: 'DS_ETAPA',
                        name: 'DS_ETAPA',
                        title: 'Última.Etapa',
                        className: 'text-center no-wrap',
                        width: '10%',
                    },
                    {
                        data: 'DSOBSERVACAO',
                        name: 'DSOBSERVACAO',
                        title: 'Observação',
                        width: '20%',
                    }
                ],
                columnDefs: [{
                    targets: [6, 7, 8, 9, 10],
                    render: function(data, type, row) {
                        if (type === "display" || type === "filter") {
                            return data ? moment(data).format("DD/MM/YYYY HH:mm:ss") : '';
                        }
                        return data;
                    },
                }],
                createdRow: function(row, data, dataIndex) {

                    if (parseInt(data.CD_ETAPA) === 0) {
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


                    // console.log(lotePcpTable);


                    data.each(function(rowData) {
                        // formata a sting para nao dar erros
                        let cd_etapa = parseInt(rowData.CD_ETAPA);

                        if (cd_etapa === 1) {
                            totalIniciando++;
                        }

                        if (cd_etapa === 0) {
                            totalSemExame++;
                        }
                    });



                    //atualiza o card de quantidade de lotes
                    $('#lotes').text(totalLotes);

                    // atualiza os cards
                    $('#card-pneus-em-producao').html(totalPneusLote + ' / ' + totalEmProducao);
                    $('#card-pneus-atraso').html(totalAtraso + ' <small class="percentual text-muted">' +
                        pcAtrasado +
                        '%</small>');
                    $('#card-pneus-iniciando').text(totalIniciando);
                    $('#card-pneus-sem-exame').text(totalSemExame);
                    $('#card-pneus-finalizados').text(totalPneusLote - totalEmProducao);
                },
                order: [
                    [1, 'asc']
                ]
            });
        }

        $(document).on('click', '.btn-remover-pneus-lote', function() {
            Swal.fire({
                title: 'Confirmação',
                text: 'Tem certeza que deseja remover os pneus deste lote?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lógica para remover os pneus do lote
                    Swal.fire('Aviso!', 'Essa funcionalidade ainda não está implementada.', 'warning');
                }
            });
        });

        $(document).on('click', '.btn-transferir-pneus-lote', function() {
            Swal.fire({
                title: 'Confirmação',
                text: 'Tem certeza que deseja transferir os pneus deste lote?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, transferir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lógica para transferir os pneus do lote
                    Swal.fire('Aviso!', 'Essa funcionalidade ainda não está implementada.', 'warning');
                }
            });
        });
    </script>
@stop
