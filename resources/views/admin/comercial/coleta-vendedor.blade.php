@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coletas</span>
                        <span id="coletas" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Médio</span>
                        <span id="media" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Faturados</span>
                        <span id="faturados" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recusados</span>
                        <span id="recusado" class="info-box-number">0</span>
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
                                        <table id="coletasMesAtual-{{ $emp->CD_EMPRESA }}"
                                            class="table compact table-font-small table-striped table-bordered"
                                            style="width:100%; font-size: 11px;">
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="painel-todas" role="tabpanel" aria-labelledby="tab-todas">
                                <div class="table-responsive">
                                    <table id="coletasMesAtualtodas"
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

@section('js')
    <script>
        $(document).ready(function() {
            $('#tab-empresa a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                var empresaId = $(e.target).data('empresa');
                if (empresaId === 'null') empresaId = null;
                var tableId = empresaId ? '#coletasMesAtual-' + empresaId :
                    '#coletasMesAtualtodas';

                if (!$.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable({
                        processing: false,
                        serverSide: false,
                        pageLength: 50,
                        language: {
                            url: "{{ asset('vendor/datatables/pt-br.json') }}",
                        },
                        ajax: {
                            url: "{{ route('get-coleta-vendedor-mes') }}",
                        },
                        columns: [{
                                title: 'Vendedor',
                                data: 'NM_PESSOA',
                                name: 'NM_PESSOA'
                            },
                            {
                                title: 'Coletas',
                                data: 'QT_COLETA',
                                name: 'QT_COLETA'
                            },
                            {
                                title: 'Qtd Faturado',
                                data: 'QT_FATURADO',
                                name: 'QT_FATURADO'
                            },
                            {
                                title: 'Qt Mes Anterior',
                                data: 'QT_FATURADOMESANTERIOR',
                                name: 'QT_FATURADOMESANTERIOR'
                            },
                            {
                                title: 'Vlr Médio',
                                data: 'VL_MEDIO',
                                name: 'VL_MEDIO'
                            },
                            {
                                title: 'Vlr Médio Mês Anterior',
                                data: 'VL_MEDIOMESANTERIOR',
                                name: 'VL_MEDIOMESANTERIOR'
                            },
                            {
                                title: 'Qtd Recusado',
                                data: 'QT_RECUSADO',
                                name: 'QT_RECUSADO'
                            },

                        ],
                        drawCallback: function(settings) {
                            var api = this.api();
                            var data = api.rows({
                                search: 'applied'
                            }).data();

                            // inicializa somas
                            let totalColetas = 0;
                            let totalValorMedio = 0;
                            let totalFaturado = 0;
                            let totalRecusado = 0;
                            let countValores = 0;

                            // percorre todas as linhas e soma os valores
                            data.each(function(d) {
                                totalColetas += parseFloat(d.QT_COLETA) || 0;
                                if (d.VL_MEDIO !== null && d.VL_MEDIO !== undefined) {
                                    totalValorMedio += parseFloat(d.VL_MEDIO);
                                    countValores++;
                                }
                                totalFaturado += parseFloat(d.QT_FATURADO) || 0;
                                totalRecusado += parseFloat(d.QT_RECUSADO) || 0;
                            });

                            // Calcula a média real
                            let mediaFinal = countValores ? (totalValorMedio / countValores) : 0;
                            let mediaFormatada = mediaFinal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                            // atualiza os cards
                            $('#coletas').text(totalColetas);
                            $('#media').text(mediaFormatada);
                            $('#faturados').text(totalFaturado);
                            $('#recusado').text(totalRecusado);
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
