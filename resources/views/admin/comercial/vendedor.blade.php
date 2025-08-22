@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus Coletados</span>
                        <span class="info-box-number" id="coletados">0</span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Produzida</span>
                        <span class="info-box-number" id="produzidos">0</span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Recusada</span>
                        <span class="info-box-number" id="recusados">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tab-pcp" role="tablist">
                            @foreach ($empresa as $emp)
                                <li class="nav-item">
                                    <a class="nav-link @if ($loop->first) active @endif"
                                        id="tab-vendedores-{{ $emp->CD_EMPRESA }}" data-toggle="pill"
                                        href="#painel-vendedores-{{ $emp->CD_EMPRESA }}" role="tab"
                                        aria-controls="painel-vendedores-{{ $emp->CD_EMPRESA }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                        data-empresa="{{ $emp->CD_EMPRESA }}">
                                        {{ $emp->NM_EMPRESA }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="nav-item">
                                <a class="nav-link" id="tab-todas" data-toggle="pill" href="#painel-todas" role="tab"
                                    aria-controls="painel-todas" aria-selected="false">
                                    Todas
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            @foreach ($empresa as $emp)
                                <div class="tab-pane fade @if ($loop->first) show active @endif"
                                    id="painel-vendedores-{{ $emp->CD_EMPRESA }}" role="tabpanel"
                                    aria-labelledby="tab-vendedores-{{ $emp->CD_EMPRESA }}">
                                    <div class="table-responsive">
                                        <table id="acompanhamentoMesAtual-{{ $emp->CD_EMPRESA }}"
                                            class="table compact table-font-small table-striped table-bordered"
                                            style="width:100%; font-size: 11px;">
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="painel-todas" role="tabpanel" aria-labelledby="tab-todas">
                                <table id="acompanhamentoMesAtualtodas"
                                    class="table compact table-font-small table-striped table-bordered"
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
            $('#tab-pcp a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                var empresaId = $(e.target).data('empresa');
                var tableId = '#acompanhamentoMesAtual-' + empresaId;

                if (empresaId) {
                    if (!$.fn.DataTable.isDataTable(tableId)) {
                        $(tableId).DataTable({
                            processing: false,
                            serverSide: false,
                            pageLength: 50,
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                            },
                            ajax: {
                                url: '{{ route('get-vendedor-acompanhamento') }}',
                                type: 'GET',
                                data: {
                                    empresa: empresaId
                                }
                            },
                            columns: [{
                                    data: 'NM_PESSOA',
                                    name: 'vendedor',
                                    title: 'Vendedor'
                                },
                                {
                                    data: 'QT_PNEU',
                                    name: 'pneus_coletados',
                                    title: 'Pneus Coletados'
                                },
                                {
                                    data: 'QT_FATURADO',
                                    name: 'qtde_prod',
                                    title: 'Qtde Prod.'
                                },
                                {
                                    data: 'VL_FATURADO',
                                    name: 'recusado',
                                    title: 'Recusado'
                                }
                            ],
                            drawCallback: function(settings) {
                                var api = this.api();
                                var data = api.rows().data();

                                // inicializa somas
                                let somaPneus = 0;
                                let somaProd = 0;
                                let somaRecusados = 0;

                                // percorre todas as linhas e soma os valores
                                data.each(function(d) {
                                    somaPneus += parseFloat(d.QT_PNEU) || 0;
                                    somaProd += parseFloat(d.QT_FATURADO) || 0;
                                    somaRecusados += parseFloat(d.VL_FATURADO) || 0;
                                });

                                // atualiza os cards
                                $('#coletados').text(somaPneus);
                                $('#produzidos').text(somaProd);
                                $('#recusados').text(somaRecusados);

                            }
                        });
                    } else {
                        $(tableId).DataTable().ajax.reload();
                    }
                } else {
                    $('#acompanhamentoMesAtualtodas').DataTable().ajax.reload();
                }
            });
            $('#tab-pcp a.nav-link.active').trigger('shown.bs.tab');

            $('#acompanhamentoMesAtualtodas').DataTable({
                processing: false,
                serverSide: false,
                pageLength: 50,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
                ajax: {
                    url: '{{ route('get-vendedor-acompanhamento') }}',
                    type: 'GET',
                },
                columns: [{
                        data: 'NM_PESSOA',
                        name: 'vendedor',
                        title: 'Vendedor'
                    },
                    {
                        data: 'QT_PNEU',
                        name: 'pneus_coletados',
                        title: 'Pneus Coletados'
                    },
                    {
                        data: 'QT_FATURADO',
                        name: 'qtde_prod',
                        title: 'Qtde Prod.'
                    },
                    {
                        data: 'VL_FATURADO',
                        name: 'recusado',
                        title: 'Recusado'
                    }
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var data = api.rows().data();

                    // inicializa somas
                    let somaPneus = 0;
                    let somaProd = 0;
                    let somaRecusados = 0;

                    // percorre todas as linhas e soma os valores
                    data.each(function(d) {
                        somaPneus += parseFloat(d.QT_PNEU) || 0;
                        somaProd += parseFloat(d.QT_FATURADO) || 0;
                        somaRecusados += parseFloat(d.VL_FATURADO) || 0;
                    });

                    // atualiza os cards
                    $('#coletados').text(somaPneus);
                    $('#produzidos').text(somaProd);
                    $('#recusados').text(somaRecusados);
                }
            });
        });
    </script>
@stop
