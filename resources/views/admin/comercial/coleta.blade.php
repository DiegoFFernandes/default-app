@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Hoje</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Ontem</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Projeção/Mês</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Dif. ano Passado</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Índice de Recusa</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-dark card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-vendedor" data-toggle="pill" href="#painel-vendedor"
                            role="tab" aria-controls="painel-vendedor" aria-selected="true">
                            Coleta por Vendedor Hoje
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-ultimos6" data-toggle="pill" href="#painel-ultimos6" role="tab"
                            aria-controls="painel-ultimos6" aria-selected="false">
                            Coletas Últimos 6 Meses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-coletaDesenho" data-toggle="pill" href="#painel-coletaDesenho"
                            role="tab" aria-controls="painel-coletaDesenho" aria-selected="false">
                            Coletas por Desenho
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-coletaMedida" data-toggle="pill" href="#painel-coletaMedida"
                            role="tab" aria-controls="painel-coletaMedida" aria-selected="false">
                            Coletas por Medida
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="tabContentColetas">
                    <div class="tab-pane fade show active" id="painel-vendedor" role="tabpanel"
                        aria-labelledby="tab-vendedor">
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table id="coletasVendedorHoje"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Qtd</th>
                                        <th>Valor médio</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="painel-ultimos6" role="tabpanel" aria-labelledby="tab-ultimos6">
                        <div>
                            <canvas id="coletaUltimos6Meses"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="painel-coletaDesenho" role="tabpanel"
                        aria-labelledby="tab-coletaDesenho">
                        <div>
                            <canvas id="coletaPorDesenho"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="painel-coletaMedida" role="tabpanel"
                        aria-labelledby="tab-coletaMedida">
                        <div>
                            <canvas id="coletaPorMedida"></canvas>
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
            if ($.fn.DataTable.isDataTable('#coletasVendedorHoje')) {
                $('#coletasVendedorHoje').DataTable().destroy();
            }

            $('#coletasVendedorHoje').DataTable({
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
