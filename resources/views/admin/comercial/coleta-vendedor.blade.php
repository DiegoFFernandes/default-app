@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coletas</span>
                        <span id="coleta-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Médio</span>
                        <span id="media-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Faturados</span>
                        <span id="media-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recusados</span>
                        <span id="media-hoje" class="info-box-number">0</span>
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
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-vendedor">Vendedor:</label>
                                    <select id="filtro-vendedor" class="form-control mt-1">

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
            <div class="col-12 mb-3">
                <div class="card card-outline card-dark h-100 d-flex flex-column">
                    <div class="card-header">
                        <h3 class="card-title">Coletas Por Vendedor / Mês</h3>
                    </div>
                    <div class="card-body flex-grow-1 overflow-auto">
                        <span class="badge badge-danger badge-empresa">Empresa:</span>
                        <span class="badge badge-danger badge-periodo">Periodo:</span>
                        <table id="coletasPorVendedorMes"
                            class="table compact table-font-small table-striped table-bordered nowrap"
                            style="width: 100%; font-size: 12px;">
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

            let inicioData = moment().startOf('month').format('DD.MM.YYYY 00:00');
            let fimData = moment().format('DD.MM.YYYY 23:59');

            $('.badge-periodo').text(`Periodo: ${inicioData} - ${fimData}`);

            if ($.fn.DataTable.isDataTable('#coletasPorVendedorMes')) {
                $('#coletasPorVendedorMes').DataTable().destroy();
            }

            $('#coletasPorVendedorMes').DataTable({
                responsive: true,
                processing: false,
                serverSide: false,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: "{{ route('get-coleta-vendedor-mes') }}",
                },
                columns: [{
                        title: 'Vendedor',
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA'
                    },
                    {
                        title: 'Coletas',
                        data: 'QT_COLETA',
                        name: 'QT_COLETA'
                    },
                    {
                        title: 'Qtd Faturado',
                        data: 'QT_FATURADO',
                        name: 'QT_FATURADO'
                    },
                    {
                        title: 'Qt Mes Anterior',
                        data: 'QT_FATURADOMESANTERIOR',
                        name: 'QT_FATURADOMESANTERIOR'
                    },
                    {
                        title: 'Vlr Médio',
                        data: 'VL_MEDIO',
                        name: 'VL_MEDIO'
                    },
                    {
                        title: 'Vlr Médio Mês Anterior',
                        data: 'VL_MEDIOMESANTERIOR',
                        name: 'VL_MEDIOMESANTERIOR'
                    },
                    
                ],

              
            });
        });
    </script>
@stop
