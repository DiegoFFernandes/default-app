@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Producao X Executor</h3>
                </div>
                <div class="card-body">
                    <table id="executorEtapas" class="table compact table-bordered table-striped">

                    </table>
                </div>
            </div>
        </div>
    </section>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            

            initTable(1, '05.02.2025 00:00', '05.02.2025 23:59');


            function initTable(cdempresa, dtinicio, dtfim) {
                $('#executorEtapas').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('get-producao-executor-etapas') }}',
                        data: {
                            cd_empresa: cdempresa,
                            dt_inicio: dtinicio,
                            dt_fim: dtfim
                        },
                        type: 'GET'
                    },
                    columns: [{
                            data: 'DT_FIM',
                            name: 'DT_FIM',
                            title: 'Finalização'
                        },
                        {
                            data: 'IDEMPRESA',
                            name: 'IDEMPRESA',
                            title: 'Empresa'
                        },
                        {
                            data: 'NM_EXECUTOR',
                            name: 'NM_EXECUTOR',
                            title: 'Executor'
                        },
                        {
                            data: 'EXAME_INI',
                            name: 'EXAME_INI',
                            title: 'Exame Inicial'
                        },
                        {
                            data: 'RASPA',
                            name: 'RASPA',
                            title: 'Raspagem'
                        },
                        {
                            data: 'PREPBANDA',
                            name: 'PREPBANDA',
                            title: 'Prep. Banda'
                        },
                        {
                            data: 'ESCAREACAO',
                            name: 'ESCAREACAO',
                            title: 'Escareação'
                        },
                        {
                            data: 'LIMPEZAMANCHAO',
                            name: 'LIMPEZAMANCHAO',
                            title: 'Limpeza Manchão'
                        },
                        {
                            data: 'APLICOLA',
                            name: 'APLICOLA',
                            title: 'Cola'
                        },
                        {
                            data: 'EMBORRACHAMENTO',
                            name: 'EMBORRACHAMENTO',
                            title: 'Emborrachamento'
                        },
                        {
                            data: 'VULCANIZACAO',
                            name: 'VULCANIZACAO',
                            title: 'Vulcanização'
                        },
                        {
                            data: 'EXAMEFINAL',
                            name: 'EXAMEFINAL',
                            title: 'Exame Final'
                        }
                    ]
                });
            }
        });
    </script>
@stop
