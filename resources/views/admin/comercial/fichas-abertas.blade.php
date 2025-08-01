@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-hourglass-start"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Abertos de 0 a 3 dias</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Abertos de 4 a 7 dias</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Abertos de 8 a 10 dias</span>
                        <span class="info-box-number">1,410</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-bell"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Abertos a mais de 10 dias</span>
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
            <div class="col-12 mb-">
                <div class="card card-outline card-dark">
                    <div class="card-header">
                        <h3 class="card-title">Ordens Abertas</h3>
                    </div>
                    <div class="card-body">
                        <table id="ordensAbertas" class="table compact table-font-small table-striped table-bordered"
                            style="width:100%; font-size: 12px;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Nr. Ordem</th>
                                    <th>Data</th>
                                    <th>Medida</th>
                                    <th>Desenho</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#ordensAbertas')) {
                $('#ordensAbertas').DataTable().destroy();
            }

            $('#ordensAbertas').DataTable({
                responsive: true,
                scrollX: true,
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
