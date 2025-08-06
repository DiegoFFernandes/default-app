@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Prododução do dia</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus aguardando exame inicial</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Diferença ano Passado</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Indice de recusa</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-week"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Produção da semana</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Projeção do mês</span>
                        <span class="info-box-number">1,410</span>
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
        <div class="card card-dark card-outline card-outline-tabs mt-3">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab1-tab" data-toggle="pill" href="#tab1" role="tab"
                            aria-controls="tab1" aria-selected="true">Produção dos Últimos 6 Meses - Carga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2-tab" data-toggle="pill" href="#tab2" role="tab"
                            aria-controls="tab2" aria-selected="false">Produção Diária - Carga</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">

                    </div>
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <table id="qtdDia" class="table compact table-font-small table-striped table-bordered nowrap"
                            style="width:100%; font-size: 12px;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Dia</th>
                                    <th>Pneus</th>
                                </tr>
                            </thead>
                        </table>
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

        @media (max-width: 576px) {
            .nav-tabs {
                overflow-x: auto;
                overflow-y: hidden;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
            }

            .nav-tabs .nav-item {
                flex-shrink: 0;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#qtdDia')) {
                $('#qtdDia').DataTable().destroy();
            }

            $('#qtdDia').DataTable({
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
