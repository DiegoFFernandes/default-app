@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-6 col-sm-6 col-md-2 mb-3"> 
            <div class="card card-outline card-success text-center">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Coleta hoje</h3>
                </div>
                <div class="card-body p-2">
                    <h1>4</h1>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-md-2 mb-3">
            <div class="card card-outline card-success text-center">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Coleta Ontem</h3>
                </div>
                <div class="card-body p-2">
                    <h1>4</h1>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-md-2 mb-3">
            <div class="card card-outline card-success text-center">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Projeção do Mês</h3>
                </div>
                <div class="card-body p-2">
                    <h1>4</h1>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-md-3 mb-3">
            <div class="card card-outline card-danger text-center">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">% Diferença ano Passado</h3>
                </div>
                <div class="card-body p-2">
                    <h1>4</h1>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-md-3 mb-3">
            <div class="card card-outline card-danger text-center">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">% Indice de Recusa</h3>
                </div>
                <div class="card-body p-2">
                    <h1>4</h1>
                </div>
            </div>
        </div>
    </div> 
    

    <div class="row mt-3">
        <div class="col-12 col-sm-6 col-md-3 mb-3"> 
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Valor médio hoje</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Grafico 1</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Valor médio ontem</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Grafico 2</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Valor médio semana</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Grafico 3</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title w-100 text-center">Valor médio mês</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Grafico 4</p>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-4 d-flex" style="min-height: 420px;">
        <div class="col-12 col-md-6 d-flex flex-column mb-3">
            <div class="card card-outline card-info flex-grow-1 d-flex flex-column">
                <div class="card-header">
                    <h3 class="card-title">Coleta por Vendedor Hoje</h3>
                </div>
                <div class="card-body p-2 flex-grow-1 overflow-auto">
                    <div class="table-responsive h-100">
                        <table id="coletasVendedorHoje" class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Vendedor</th>
                                    <th>Qtd</th>
                                    <th>Valor médio</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 d-flex flex-column mb-3">
            <div class="card card-outline card-info flex-grow-1 d-flex flex-column">
                <div class="card-header">
                    <h3 class="card-title">Coletas nos ultimos 6 meses</h3>
                </div>
                <div class="card-body flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="myBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#coletasVendedorHoje')) {
                $('#coletasVendedorHoje').DataTable().destroy();
            }

            $('#coletasVendedorHoje').DataTable({
                processing: true, 
                serverSide: false, 
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
