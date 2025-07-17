@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-6 col-sm-6 col-md-3 mb-3">
                <div class="card card-outline card-success text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100 text-center">Aberto de 0 a 3 dias</h3>
                    </div>
                    <div class="card-body p-2">
                        <h1>4</h1>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-3 mb-3">
                <div class="card card-outline card-success text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100 text-center">Aberto de 4 a 7 dias</h3>
                    </div>
                    <div class="card-body p-2">
                        <h1>4</h1>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-3 mb-3">
                <div class="card card-outline card-success text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100 text-center">Aberto de 8 a 10 dias</h3>
                    </div>
                    <div class="card-body p-2">
                        <h1>4</h1>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-3 mb-3">
                <div class="card card-outline card-success text-center">
                    <div class="card-header">
                        <h3 class="card-title w-100 text-center">Aberto a mais de 10 dias</h3>
                    </div>
                    <div class="card-body p-2">
                        <h1>4</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Coletas por Medida Mês</h3>
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
                processing: true,
                serverSide: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
            });
        });
    </script>
@stop
