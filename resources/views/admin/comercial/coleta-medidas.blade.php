@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row row-equal-height">
            <div class="col-12 col-md-6 mb-3">
                <div class="card card-outline card-danger h-100">
                    <div class="card-header">
                        <h3 class="card-title">Coletas por Medida Hoje</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="coletasMedidasHoje"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>

                                        <th>Descrição</th>
                                        <th>Qtde</th>
                                        <th>Valor Médio</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="card card-outline card-danger h-100">
                    <div class="card-header">
                        <h3 class="card-title">Coletas por Medida Ontem</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="coletasMedidasOntem"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>

                                        <th>Descrição</th>
                                        <th>Qtde</th>
                                        <th>Valor Médio</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
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
                        <div class="table-responsive">
                            <table id="coletasMedidasMes"
                                class="table compact table-font-small table-striped table-bordered nowrap"
                                style="width:100%; font-size: 12px;">
                                <thead class="bg-dark text-white">
                                    <tr>

                                        <th>Coletas</th>
                                        <th>Total Faturado</th>
                                        <th>Valor Médio</th>
                                    </tr>
                                </thead>
                            </table>
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

            coletaMedida('05.02.2025', '05.02.2025', 'coletasMedidasHoje');

            coletaMedida('04.02.2025', '04.02.2025', 'coletasMedidasOntem');

            coletaMedida('01.02.2025', '28.02.2025', 'coletasMedidasMes');

            function coletaMedida(dt_inicio, dt_fim, idTabela) {
                $('#' + idTabela).DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        method: 'GET',
                        url: '{{ route('get-coleta-medidas') }}',
                        data: {
                            dt_inicio: dt_inicio,
                            dt_fim: dt_fim
                        }
                    },
                    columns: [

                        {
                            data: 'DSMEDIDAPNEU',
                            name: 'DSMEDIDAPNEU'
                        },
                        {
                            data: 'QTD',
                            name: 'QTD'
                        },
                        {
                            data: 'PRECO_MEDIA',
                            name: 'PRECO_MEDIA'
                        }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                });
            }
        });
    </script>
@stop
