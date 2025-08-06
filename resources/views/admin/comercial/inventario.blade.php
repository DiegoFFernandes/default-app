@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
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
            <div class="col-12 col-lg-8 mb-3">
                <div class="card card-dark card-outline card-outline-tabs equal-height">
                    <div class="card-header  p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-localDeposito" data-toggle="pill"
                                    href="#painel-localDeposito" role="tab" aria-controls="painel-localDeposito"
                                    aria-selected="true">
                                    Local Deposito
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-localProcesso" data-toggle="pill" href="#painel-localProcesso"
                                    role="tab" aria-controls="painel-localProcesso" aria-selected="false">
                                    Local Processo
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabColetasContent">
                            <div class="tab-pane fade show active" id="painel-localDeposito" role="tabpanel"
                                aria-labelledby="tab-localDeposito">
                                <div class="table-responsive">
                                    <table id="localDeposito"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th>Número</th>
                                                <th>Data</th>
                                                <th>Cod</th>
                                                <th>Descrição</th>
                                                <th>Secao</th>
                                                <th>Qt Sistema</th>
                                                <th>Qt Balan</th>
                                                <th>Qt Dif</th>
                                                <th>% Dif </th>
                                                <th>20%</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-localProcesso" role="tabpanel"
                                aria-labelledby="tab-localProcesso">
                                <div class="table-responsive">
                                    <table id="localDeposito"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 10px;">
                                        <thead>
                                            <tr>
                                                <th>Número</th>
                                                <th>Data</th>
                                                <th>Cod</th>
                                                <th>Descrição</th>
                                                <th>Secao</th>
                                                <th>Qt Sistema</th>
                                                <th>Qt Balan</th>
                                                <th>Qt Dif</th>
                                                <th>% Dif </th>
                                                <th>20%</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mb-3">
                <div class="card card-outline card-dark equal-height">
                    <div class="card-header">
                        <h5 class="card-title">Ajustes da Semana</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ajustesSemana"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 10px;">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Item</th>
                                        <th>Local</th>
                                        <th>Qtde</th>
                                        <th>Tipo</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')

    <style>
        @media (min-width: 992px) {
            .equal-height {
                display: flex;
                flex-direction: column;
                height: 100%;
            }
        }
    </style>

@stop

@section('js')

    <script>
        $(document).ready(function() {
            $('#localDeposito, #localProcesso, #ajustesSemana').DataTable({
                searching: false,
                paging: false,
                info: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>

@stop
