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
        <!-- Filtro (mudar de acordo com a nescessidade da página) -->
        <div class="row">
            <div class="col-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h5 class="card-title">Filtros</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-empresa">Empresa:</label>
                                    <select id="filtro-empresa" class="form-control mt-1">

                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="daterange">Data:</label>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="daterange"
                                            placeholder="Selecione a Data">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0 d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs m-0" id="coletaTabs" role="tablist" style="flex-grow:1">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-vendedor-hoje" data-toggle="tab"
                                    href="#tabContent-vendedor-hoje" role="tab" aria-controls="tabContent-vendedor-hoje"
                                    aria-selected="true">
                                    Coleta por vendedor hoje
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content" style="min-height: 120px;">
                        <div class="tab-pane fade show active" id="tabContent-vendedor-hoje" role="tabpanel"
                            aria-labelledby="tab-vendedor-hoje">
                            <table id="coletasVendedorHoje"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Celular</th>
                                        <th>Qtd</th>
                                        <th>Valor médio</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs m-0" id="outrasColetasTabs" role="tablist" style="flex-grow:1">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-desenho" data-toggle="tab" href="#tabContent-desenho"
                                    role="tab" aria-controls="tabContent-desenho" aria-selected="true">
                                    Coleta por Desenho
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-medida" data-toggle="tab" href="#tabContent-medida"
                                    role="tab" aria-controls="tabContent-medida" aria-selected="false">
                                    Coleta por Medida
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content" style="min-height: 235px;">
                        <div class="tab-pane fade show active" id="tabContent-desenho" role="tabpanel"
                            aria-labelledby="tab-desenho">
                        </div>
                        <div class="tab-pane fade" id="tabContent-medida" role="tabpanel" aria-labelledby="tab-medida">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .nav-tabs .nav-link {
            font-size: 15px;
            padding: 7px 15px;
        }

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
                responsive:true,
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
