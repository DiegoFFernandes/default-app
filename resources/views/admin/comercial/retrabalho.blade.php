@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Retabalhos</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Custo médio</span>
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
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-9 mb-3">
                <div class="card card-dark card-outline card-outline-tabs equal-height">
                    <div class="card-header  p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-ordensRetrabalho" data-toggle="pill"
                                    href="#painel-ordensRetrabalho" role="tab" aria-controls="painel-ordensRetrabalho"
                                    aria-selected="true">
                                    Ordens Retrabalho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-retrabaUltimos6Meses" data-toggle="pill"
                                    href="#painel-retrabaUltimos6Meses" role="tab"
                                    aria-controls="painel-retrabaUltimos6Meses" aria-selected="false">
                                    Retrabalho Ultimos 6 meses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-retrabalhoOperador" data-toggle="pill"
                                    href="#painel-retrabalhoOperador" role="tab"
                                    aria-controls="painel-retrabalhoOperador" aria-selected="false">
                                    Retrabalho por Operador
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-retrabalhoEtapa" data-toggle="pill"
                                    href="#painel-retrabalhoEtapa" role="tab" aria-controls="painel-retrabalhoEtapa"
                                    aria-selected="false">
                                    Retrabalho por Etapa
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabColetasContent">
                            <div class="tab-pane fade show active" id="painel-ordensRetrabalho" role="tabpanel"
                                aria-labelledby="tab-ordensRetrabalho">
                                <div class="table-responsive">
                                    <table id="ordensRetrabalho"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>OP</th>
                                                <th>Coleta</th>
                                                <th>Motivo</th>
                                                <th>Cliente</th>
                                                <th>Vendedor</th>
                                                <th>Custo</th>
                                                <th>Dt.Retrabalho</th>
                                                <th>Dt.produção</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-retrabaUltimos6Meses" role="tabpanel"
                                aria-labelledby="tab-retrabaUltimos6Meses">
                                <!-- grafico do retrabalho ultimos 6 meses -->
                            </div>
                            <div class="tab-pane fade" id="painel-retrabalhoEtapa" role="tabpanel"
                                aria-labelledby="tab-retrabalhoEtapa">
                                <!-- grafico do retrabalaho etapa -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 mb-3">
                <div class="card card-outline card-dark equal-height">
                    <div class="card-header">
                        <h5 class="card-title">Retrabalho por motivo</h5>
                    </div>
                    <div class="card-body">
                        <table id="ordensRetrabalhoMotivo"
                            class="table compact table-font-small table-striped table-bordered nowrap"
                            style="width:100%; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Motivo</th>
                                    <th>Qtde</th>
                                    <th>Valor</th>
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
            $('#ordensRetrabalho, #ordensRetrabalhoMotivo').DataTable({
                resposive: true,
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
