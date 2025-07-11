@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Acompanhamento Mês Atual</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive h-100">
                            <table id="acompanhamentoMesAtual"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Pneus Coletados</th>
                                        <th>Qtde Prod.</th>
                                        <th>Recusado</th>
                                        <th>Qtde Fat.</th>
                                        <th>VI Fat</th>
                                        <th>Coleta Mês Ant.</th>
                                        <th>Qtde Fat Mês Ant.</th>
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
            if ($.fn.DataTable.isDataTable('#acompanhamentoMesAtual')) {
                $('#acompanhamentoMesAtual').DataTable().destroy();
            }

            $('#acompanhamentoMesAtual').DataTable({
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
