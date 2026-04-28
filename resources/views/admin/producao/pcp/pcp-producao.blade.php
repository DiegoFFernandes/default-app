@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Lotes</span>
                        <span class="info-box-number" id="lotes">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pneus</span>
                        <span class="info-box-number" id="card-pneus-lote">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Atrasados</span>
                        <span class="info-box-number" id="card-pneus-atraso">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sem exame</span>
                        <span class="info-box-number" id="card-pneus-sem-exame">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-start"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Iniciando</span>
                        <span class="info-box-number" id="card-pneus-iniciando">0</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Finalizados</span>
                        <span class="info-box-number" id="card-pneus-finalizados">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab-pcp" role="tablist">
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
                                    <div class="col-md-7">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Lotes em Aberto</h3>
                                            </div>
                                            <div class="card-body">
                                                <table id="lote-pcp"
                                                    class="table compact table-font-small table-striped table-bordered table-responsive"
                                                    style="width:100%; font-size: 11px;">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Bandas a consumir</h3>
                                            </div>
                                            <div class="card-body">
                                                <table id="bandas-consumir"
                                                    class="table compact table-font-small table-striped table-bordered table-responsive"
                                                    style="width:100%; font-size: 11px;">
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
    </section>
    @include('admin.producao.pcp.modals.modal-pneus-lote')
@stop

@section('css')
    <style>
        .info-box-text {
            font-size: 14px;
        }

        .info-box-number {
            font-weight: bold;
            font-size: 18px;
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
            detalhesPneusLotePcp: "{{ route('detalhes-pneus-lote-pcp') }}"
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
                        title: 'OP',
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
                drawCallback: function(settings) {
                    var api = this.api();
                    var data = api.rows().data();

                    // variáveis de contagem

                    totalAtraso = data.length;
                    totalIniciando = 0;
                    totalFinalizados = 0;
                    totalSemExame = 0;

                    // console.log(lotePcpTable);


                    data.each(function(rowData) {
                        // formata a sting para nao dar erros
                        let etapa = rowData.DS_ETAPA ? rowData.DS_ETAPA.trim()
                            .toUpperCase() :
                            '';

                        if (etapa === 'EXAME INICIAL') {
                            totalIniciando++;
                        }

                        if (etapa === 'SEM EXAME') {
                            totalSemExame++;
                        }
                    });



                    //atualiza o card de quantidade de lotes
                    $('#lotes').text(totalLotes);

                    // atualiza os cards
                    $('#card-pneus-lote').text(totalPneusLote);
                    $('#card-pneus-atraso').text(totalAtraso);
                    $('#card-pneus-iniciando').text(totalIniciando);
                    $('#card-pneus-sem-exame').text(totalSemExame);
                    $('#card-pneus-finalizados').text(totalPneusLote - totalEmProducao);
                }
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
