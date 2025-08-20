@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Lotes</span>
                        <span class="info-box-number" id="lotes">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pneus</span>
                        <span class="info-box-number" id="card-pneus-lote">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Atrasados</span>
                        <span class="info-box-number" id="card-pneus-atraso">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-start"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Iniciando</span>
                        <span class="info-box-number" id="card-pneus-iniciando">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
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
                                    <div class="table-responsive">
                                        <table id="pneus-lote-pcp-{{ $emp->CD_EMPRESA }}"
                                            class="table compact table-font-small table-striped table-bordered"
                                            style="width:100%; font-size: 11px;">
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="painel-lotesPCP" role="tabpanel" aria-labelledby="tab-lotesPCP">
                                <table id="lote-pcp" class="table compact table-font-small table-striped table-bordered"
                                    style="width:100%; font-size: 11px;">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    <script>
        $(document).ready(function() {

            const empresa = @json($empresa);

            initTable('pneus-lote-pcp-' + empresa[0].CD_EMPRESA, empresa[0].CD_EMPRESA);

            $('#tab-painelPCP-' + empresa[0].CD_EMPRESA).addClass('active');
            $('#painel-pcp-' + empresa[0].CD_EMPRESA).addClass('show active');

            $('#tab-pcp').on('click', 'a.nav-link', function() {
                let empresa = $(this).data('empresa');
                if (empresa) {
                    initTable('pneus-lote-pcp-' + empresa, empresa);
                }
            });
            $('#tab-pcp').on('click', '#tab-lotesPCP', function() {
                $('#lote-pcp').DataTable().destroy();
                $('#lote-pcp').DataTable({
                    processing: false,
                    serverSide: false,
                    pageLength: 100,
                    language: {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    ajax: {
                        url: '{{ route('get-lote-pcp') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        }
                    },
                    columns: [{
                            data: 'CD_EMPRESA',
                            name: 'CD_EMPRESA',
                            'title': 'Emp'
                        },
                        {
                            data: 'NR_LOTE',
                            name: 'NR_LOTE',
                            'title': 'Nr Lote'
                        },

                        {
                            data: 'DSCONTROLELOTEPCP',
                            name: 'DSCONTROLELOTEPCP',
                            'title': 'Ds Lote'
                        },
                        {
                            data: 'DTPRODUCAO',
                            name: 'DTPRODUCAO',
                            'title': 'Produção'
                        }, {
                            data: 'QTDE_TOT_LOTE',
                            name: 'QTDE_TOT_LOTE',
                            'title': 'Qtde Lote'
                        }, {
                            data: 'QTDE_EM_PROD',
                            name: 'QTDE_EM_PROD',
                            'title': 'Em producao'
                        }, {
                            data: 'QTDE_SEMEXAME',
                            name: 'QTDE_SEMEXAME',
                            'title': 'Sem Exame'
                        }
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();
                        var data = api.rows().data();

                        //busca a quantidade de lotes
                        let totalLotes = data.length;

                        //atualiza o card de quantidade de lotes
                        $('#lotes').text(totalLotes);
                    }
                });
            });



            function initTable(idTabela, cdEmpresa) {
                $('#' + idTabela).DataTable().destroy();
                $('#' + idTabela).DataTable({
                    processing: true,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'Todos']
                    ],
                    pageLength: 100,
                    processing: false,
                    serverSide: false,
                    language: {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    ajax: {
                        url: '{{ route('get-pneus-lote-pcp') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cd_empresa: cdEmpresa
                        }
                    },
                    columns: [{
                            data: 'NR_LOTE',
                            name: 'NR_LOTE',
                            'title': 'Lote'
                        },
                        {
                            data: 'NR_COLETA',
                            name: 'NR_COLETA',
                            'title': 'Pedido'
                        },
                        {
                            data: 'NR_OP',
                            name: 'NR_OP',
                            'title': 'OP'
                        },
                        {
                            data: 'NM_PESSOA',
                            name: 'NM_PESSOA',
                            'title': 'Cliente'
                        },
                        {
                            data: 'DSSERVICO',
                            name: 'DSSERVICO',
                            'title': 'Serviço'
                        }, {
                            data: 'DT_EXAME',
                            name: 'DT_EXAME',
                            'title': 'Exame Inicial'
                        }, {
                            data: 'DT_MANCHAO',
                            name: 'DT_MANCHAO',
                            'title': 'Manchão'
                        }, {
                            data: 'DT_COBER',
                            name: 'DT_COBER',
                            'title': 'Cobertura'
                        }, {
                            data: 'DT_VULC',
                            name: 'DT_VULC',
                            'title': 'Vulcanização'
                        }, {
                            data: 'DT_FINAL',
                            name: 'DT_FINAL',
                            'title': 'Exame Final'
                        }, {
                            data: 'DS_ETAPA',
                            name: 'DS_ETAPA',
                            'title': 'Etapa'
                        },
                        {
                            data: 'DSOBSERVACAO',
                            name: 'DSOBSERVACAO',
                            'title': 'Observação'
                        }
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();
                        var data = api.rows().data();

                        // variáveis de contagem
                        let totalPneusLote = data.length;
                        let totalAtraso = 0;
                        let totalIniciando = 0;
                        let totalFinalizados = 0;

                        data.each(function(rowData) {
                            // formata a sting para nao dar erros
                            let etapa = rowData.DS_ETAPA ? rowData.DS_ETAPA.trim()
                                .toUpperCase() :
                                '';

                            if (etapa === 'SEM EXAME') {
                                totalAtraso++;
                            } else if (etapa === 'EXAME') {
                                totalIniciando++;
                            } else if (etapa === 'EXAME FINAL') {
                                totalFinalizados++;
                            }
                        });

                        // atualiza os cards
                        $('#card-pneus-lote').text(totalPneusLote);
                        $('#card-pneus-atraso').text(totalAtraso);
                        $('#card-pneus-iniciando').text(totalIniciando);
                        $('#card-pneus-finalizados').text(totalFinalizados);
                    }
                });
            }
        });
    </script>
@stop
