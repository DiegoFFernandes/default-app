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
        </div>       
    </section>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            coletaMedida('05.02.2025', '05.02.2025', 'coletasMedidasHoje', 1);            

            function coletaMedida(dt_inicio, dt_fim, idTabela, cd_empresa) {
                $('#' + idTabela).DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    paging: false,
                    searching: false,
                    ajax: {
                        method: 'GET',
                        url: '{{ route('get-coleta-medidas') }}',
                        data: {
                            dt_inicio: dt_inicio,
                            dt_fim: dt_fim,
                            cd_empresa: cd_empresa
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
                            data: 'VALOR_MEDIO',
                            name: 'VALOR_MEDIO'
                        }
                    ],
                    
                });
            }
        });
    </script>
@stop
