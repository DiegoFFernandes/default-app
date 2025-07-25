@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Exame inicial</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fas fa-tools"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Raspagem</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fas fa-wrench"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Escareação</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fas fa-wind"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Encimento</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fas fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Emborrachamento</span>
                        <span class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-clipboard-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Exame Final</span>
                        <span class="info-box-number">0</span>
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
                <div class="card">
                    <div class="card-header">
                        <h5>Procediemento e Meta</h5>
                    </div>
                    <div class="card-body">
                        grafico ou tabela com  procedimentos x metas
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
