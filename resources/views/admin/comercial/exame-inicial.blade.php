@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Hoje</span>
                        <span class="info-box-number">1,310</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-week"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Semana</span>
                        <span class="info-box-number">1,310</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Turno A </span>
                        <span class="info-box-number">1,310</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Turno B</span>
                        <span class="info-box-number">1,310</span>
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
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header  p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-executorHora" data-toggle="pill"
                                    href="#painel-executorHora" role="tab" aria-controls="painel-executorHora"
                                    aria-selected="true">
                                    Executor/Horas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-turno" data-toggle="pill" href="#painel-turno" role="tab"
                                    aria-controls="painel-turno" aria-selected="false">
                                    Turno
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-totalHoras" data-toggle="pill" href="#painel-totalHoras"
                                    role="tab" aria-controls="painel-totalHoras" aria-selected="false">
                                    Toral Horas
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabColetasContent">
                            <div class="tab-pane fade show active" id="painel-executorHora" role="tabpanel"
                                aria-labelledby="tab-executorHora">
                                <div class="table-responsive">
                                    <table id="executorHora"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>Executor</th>
                                                <th>Horas</th>
                                                <th>Total do dia</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-turno" role="tabpanel" aria-labelledby="tab-turno">
                                <!-- grafico do painel de turno -->
                            </div>
                            <div class="tab-pane fade" id="painel-totalHoras" role="tabpanel"
                                aria-labelledby="tab-totalHoras">
                                <!-- grafico do painel de toaltal/horas -->
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
            $('#executorHora').DataTable({
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>
@stop
