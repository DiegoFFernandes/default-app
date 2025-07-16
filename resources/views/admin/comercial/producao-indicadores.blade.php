@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Prdodução do dia</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pneus aguar. exame inicial</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-week"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Produção da semana</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Projeção do mês</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Diferença ano Passado</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Indice de recusa</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-danger card-outline card-outline-tabs mt-3">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab1-tab" data-toggle="pill" href="#tab1" role="tab"
                            aria-controls="tab1" aria-selected="true">Produção Ultimos 6 meses - Carga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2-tab" data-toggle="pill" href="#tab2" role="tab"
                            aria-controls="tab2" aria-selected="false">Quantidade por dia - Carga</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">

                    </div>
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <table id="qtdDia" class="table table-sm table-striped table-bordered"
                            style="width:100%; font-size: 12px;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Dia</th>
                                    <th>Penus</th>
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
