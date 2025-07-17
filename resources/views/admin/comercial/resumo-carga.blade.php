@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <section class="content">
        {{-- ===== INFO-BOXES PRINCIPAIS ===== --}}
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
        {{-- ===== ÁREAS COM ABAS PRINCIPAIS ===== --}}
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
                                <a class="nav-link" id="producao-tab" data-toggle="pill" href="#producao"
                                    role="tab">Produção</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="etapa-tab" data-toggle="pill" href="#etapa" role="tab">Etapas
                                    de Produção</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="relatorio-tab" data-toggle="pill" href="#relatorio"
                                    role="tab">Relatório</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="main-tabs-content">
                            {{-- ORDENS ABERTAS --}}
                            <div class="tab-pane fade show active" id="ordens" role="tabpanel">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="ordensAbertas" class="table table-sm table-striped table-bordered"
                                            style="width:100%; font-size: 12px;">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th>Op</th>
                                                    <th>Data</th>
                                                    <th>Medida</th>
                                                    <th>Marca</th>
                                                    <th>Desenho</th>
                                                    <th>Vendedor</th>
                                                    <th>Cliente</th>
                                                    <th>Ult. Etapa</th>
                                                    <th>Data/Hora</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- PRODUÇÃO --}}
                            <div class="tab-pane fade" id="producao" role="tabpanel">

                            </div>
                            {{-- ETAPAS DE PRODUÇÃO --}}
                            <div class="tab-pane fade" id="etapa" role="tabpanel">

                            </div>
                            {{-- RELATÓRIO --}}
                            <div class="tab-pane fade" id="relatorio" role="tabpanel">
                                <div class="card card-outline card-dark mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Comparativo de Etapas</h3>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table id="comparativo" class="table table-sm table-striped table-bordered"
                                                style="width:100%; font-size: 12px;">
                                                <thead class="bg-dark text-white">
                                                    <tr>
                                                        <th>Etapa</th>
                                                        <th>Hoje</th>
                                                        <th>Ontem</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
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
            $('#ordensAbertas, #comparativo, #qtdCliente').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });

            $('#main-tabs a:first').tab('show');
        });
    </script>
@stop
