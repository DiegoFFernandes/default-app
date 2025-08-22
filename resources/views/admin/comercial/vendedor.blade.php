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
                <div class="card card-outline card-dark">
                    <div class="card-header">
                        <h3 class="card-title">Acompanhamento Mês Atual</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive h-100">
                            <table id="acompanhamentoMesAtual"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">                               
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
            if ($.fn.DataTable.isDataTable('#acompanhamentoMesAtual')) {
                $('#acompanhamentoMesAtual').DataTable().destroy();
            }

            $('#acompanhamentoMesAtual').DataTable({
                processing: false,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
                ajax: {
                    url: '{{ route('get-vendedor-acompanhamento') }}',
                    type: 'GET',
                },
                columns: [
                    { data: 'NM_PESSOA', name: 'vendedor', title: 'Vendedor' },                    
                    { data: 'QT_PNEU', name: 'pneus_coletados', title: 'Pneus Coletados' },
                    { data: 'QT_FATURADO', name: 'qtde_prod', title: 'Qtde Prod.' },
                    { data: 'VL_FATURADO', name: 'recusado', title: 'Recusado' }                   
                ]
            });
        });
    </script>
@stop
