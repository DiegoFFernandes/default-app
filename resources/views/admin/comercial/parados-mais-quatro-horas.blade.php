@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Exame Inical</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-tools"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Raspagem</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-paint-brush"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Aplicação Cola</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-wrench"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Escariação</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-sync"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Limpeza Manchão</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Emborrachamento</span>
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
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-exameInical" data-toggle="pill"
                                    href="#painel-exameInical" role="tab" aria-controls="painel-exameInical"
                                    aria-selected="true">
                                    Exame inicial
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-raspagem" data-toggle="pill" href="#painel-raspagem"
                                    role="tab" aria-controls="painel-raspagem" aria-selected="false">
                                    Raspagem
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-aplicacaoCola" data-toggle="pill" href="#painel-aplicacaoCola"
                                    role="tab" aria-controls="painel-aplicacaoCola" aria-selected="false">
                                    Aplicação Cola
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-escariacao" data-toggle="pill" href="#painel-escariacao"
                                    role="tab" aria-controls="painel-escariacao" aria-selected="false">
                                    Escariação
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-limpezaManchao" data-toggle="pill"
                                    href="#painel-limpezaManchao" role="tab" aria-controls="painel-limpezaManchao"
                                    aria-selected="false">
                                    Limpeza Manchão
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-emborrachamento" data-toggle="pill"
                                    href="#painel-emborrachamento" role="tab" aria-controls="painel-emborrachamento"
                                    aria-selected="false">
                                    Emborrachamento
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-exameInical" role="tabpanel"
                                aria-labelledby="tab-exameInical">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="exameInicial"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                            style="width:100%; font-size: 12px;">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <td>Ordem Produção</td>
                                                    <td>Inicio</td>
                                                    <td>Etapa</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-raspagem" role="tabpanel"
                                aria-labelledby="tab-raspagem">
                                <!-- tabela raspagem -->
                            </div>
                            <div class="tab-pane fade" id="painel-aplicacaoCola" role="tabpanel"
                                aria-labelledby="tab-aplicacaoCola">
                                <!-- tabela aplicação cola -->
                            </div>
                            <div class="tab-pane fade" id="painel-escariacao" role="tabpanel"
                                aria-labelledby="tab-cobertura">
                                <!-- tabela escariação -->
                            </div>
                            <div class="tab-pane fade" id="painel-limpezaManchao" role="tabpanel"
                                aria-labelledby="tab-limpezaManchao">
                                <!-- tabela limpeza manchao -->
                            </div>
                            <div class="tab-pane fade" id="painel-emborrachamento" role="tabpanel"
                                aria-labelledby="tab-emborrachamento">
                                <!-- tabela emborrachamento -->
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
            $('#exameInicial').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                }
            });
        });
    </script>
@stop
