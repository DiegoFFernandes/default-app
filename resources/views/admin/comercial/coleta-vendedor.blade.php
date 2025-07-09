@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Coletas Por Vendedor / Mês</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive h-100">
                            <table id="coletasPorVendedorMes"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Vendedores</th>
                                        <th>Coletas</th>
                                        <th>Valor Médio</th>
                                        <th>Faturados</th>
                                        <th>Recusados</th>
                                        <th>Faturado Mês Ant.</th>
                                        <th>Valor Médio Mês Ant.</th>
                                        <th>Posição</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#coletasPorVendedorMes')) {
                $('#coletasPorVendedorMes').DataTable().destroy();
            }

            $('#coletasPorVendedorMes').DataTable({
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
