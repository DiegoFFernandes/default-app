@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Hoje</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Aguardar Exame</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Aguardar Raspagem</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recusados</span>
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
        <div class="row">
            <div class="col-12">
                <div class="card card-dark card-outline card-outline-tabs mt-3">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="ordens-tab" data-toggle="pill" href="#ordens"
                                    role="tab">Ordens Abertas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="produzir-tab" data-toggle="pill" href="#produzir"
                                    role="tab">Produzir/Cliente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="producao-tab" data-toggle="pill" href="#producao"
                                    role="tab">Produção</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="ultimos6-tab" data-toggle="pill" href="#ultimos6"
                                    role="tab">Últimos 6 meses</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="main-tabs-content">
                            <div class="tab-pane fade show active" id="ordens" role="tabpanel">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="ordensAbertas"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                            style="width:100%; font-size: 12px;">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <td></td>
                                                    <td>OP</td>
                                                    <td>Data</td>
                                                    <td>Medida</td>
                                                    <td>Marca</td>
                                                    <td>Desenho</td>
                                                    <td>Vendedor</td>
                                                    <td>Cliente</td>
                                                    <td>Ult. Etapa</td>
                                                    <td>Data/Hora</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="produzir" role="tabpanel">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="qtdCliente"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                            style="width:100%; font-size: 12px;">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <td>Pessoa</td>
                                                    <td>Emissão</td>
                                                    <td>Entrega</td>
                                                    <td>Qtde</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="producao" role="tabpanel">
                                <div class="card-body p-2">
                                    <!-- grafico sobre a produção -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="ultimos6" role="tabpanel">
                                <div class="card-body p-2">
                                    <!-- grafico ultimos 6 meses -->
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
            $('#ordensAbertas, #qtdCliente').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>
@stop
