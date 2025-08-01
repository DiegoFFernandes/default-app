@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Filtro (mudar de acordo com a nescessidade da página) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Filtros</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
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
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-inadimplenciaVendedor" data-toggle="pill"
                                    href="#painel-inadimplenciaVendedor" role="tab"
                                    aria-controls="painel-inadimplenciaVendedor" aria-selected="true">
                                    Inadimplencia x Vendedor
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-maisInadimplentes" data-toggle="pill"
                                    href="#painel-maisInadimplentes" role="tab" aria-controls="painel-maisInadimplentes"
                                    aria-selected="false">
                                    5 Mais Inadimplentes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-totalPeriodo" data-toggle="pill" href="#painel-totalPeriodo"
                                    role="tab" aria-controls="painel-totalPeriodo" aria-selected="false">
                                    Total por Periodo
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-inadimplenciaVendedor" role="tabpanel"
                                aria-labelledby="tab-inadimplenciaVendedor">
                                <!-- grafico Inadimplencia x Vendedor -->
                            </div>
                            <div class="tab-pane fade" id="painel-maisInadimplentes" role="tabpanel"
                                aria-labelledby="tab-maisInadimplentes">
                                <!-- grafico maisInadimplentes -->
                            </div>
                            <div class="tab-pane fade" id="painel-totalPeriodo" role="tabpanel"
                                aria-labelledby="tab-totalPeriodo">
                                <!-- grafico totalPeriodo -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
