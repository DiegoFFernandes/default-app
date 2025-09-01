@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus Coletados</span>
                        <span class="info-box-number" id="coletados">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Produzida</span>
                        <span class="info-box-number" id="produzidos">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12">
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
                        <ul class="nav nav-tabs" id="tab-empresa" role="tablist">
                            @foreach ($empresa as $emp)
                                <li class="nav-item">
                                    <a class="nav-link @if ($loop->first) active @endif"
                                        id="tab-empresas-{{ $emp->CD_EMPRESA }}" data-toggle="pill"
                                        href="#painel-empresas-{{ $emp->CD_EMPRESA }}" role="tab"
                                        aria-controls="painel-empresas-{{ $emp->CD_EMPRESA }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                        data-empresa="{{ $emp->CD_EMPRESA }}">
                                        {{ $emp->NM_EMPRESA }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="nav-item">
                                <a class="nav-link" id="tab-todas" data-toggle="pill" href="#painel-todas" role="tab"
                                    aria-controls="painel-todas" aria-selected="false" data-empresa="null">
                                    Todas
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentEmpresas">
                            @foreach ($empresa as $emp)
                                <div class="tab-pane fade @if ($loop->first) show active @endif"
                                    id="painel-empresas-{{ $emp->CD_EMPRESA }}" role="tabpanel"
                                    aria-labelledby="tab-empresas-{{ $emp->CD_EMPRESA }}">
                                    <div class="table-responsive">
                                        <table id="acompanhamentoMesAtual-{{ $emp->CD_EMPRESA }}"
                                            class="table compact table-font-small table-striped table-bordered"
                                            style="width:100%; font-size: 11px;">
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="painel-todas" role="tabpanel" aria-labelledby="tab-todas">
                                <div class="table-responsive">
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
            $('#tab-empresa a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                var empresaId = $(e.target).data('empresa');
                if (empresaId === 'null') empresaId = null;
                var tableId = empresaId ? '#acompanhamentoMesAtual-' + empresaId : '#acompanhamentoMesAtualtodas';

                if (!$.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable({
                        processing: false,
                        serverSide: false,
                        pageLength: 50,
                        language: {
                            url: "{{ asset('vendor/datatables/pt-br.json') }}",
                        },
                        ajax: {
                            url: '{{ route('get-vendedor-acompanhamento') }}',
                            type: 'GET',
                            data: function(d) {
                                d.empresa = empresaId;
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
                                data: 'QT_PRODUZIDO',
                                name: 'qtde_prod',
                                title: 'Qtde Prod.'
                            },
                            {
                                data: 'QT_RECUSADO',
                                name: 'recusado',
                                title: 'Recusado'
                            },
                            {
                                data: 'QT_FATURADO',
                                name: 'qtde_fat',
                                title: 'Qtde Fat'
                            },
                            {
                                data: 'VL_FATURADO',
                                name: 'vl_Fat',
                                title: 'Vl Fat'
                            },
                            {
                                data: 'QT_PNEUANTERIOR',
                                name: 'qtde_pneusanterior',
                                title: 'Coleta Mês Ant.'
                            },
                            {
                                data: 'QT_FATURADOMESANTERIOR',
                                name: 'qtde_fatmesanterior',
                                title: 'Qtde Fat Mês Ant.'
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
                                somaProd += parseFloat(d.QT_PRODUZIDO) || 0;
                                somaRecusados += parseFloat(d.QT_RECUSADO) || 0;
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
            });
            $('#tab-empresa a.nav-link.active').trigger('shown.bs.tab');
        });
    </script>
@stop
