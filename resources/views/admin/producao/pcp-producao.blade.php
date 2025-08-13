@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title_page }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="pneus-lote-pcp" class="table table-bordered table-striped table-font-small compact">
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lote PCP</h3>
                    </div>
                    <div class="card-body">
                        <table id="lote-pcp" class="table table-bordered table-striped table-font-small compact">
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
            $('#pneus-lote-pcp').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get-pneus-lote-pcp') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'NR_LOTE',
                        name: 'NR_LOTE',
                        'title': 'Lote'
                    },
                    {
                        data: 'NR_COLETA',
                        name: 'NR_COLETA',
                        'title': 'Coleta'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        'title': 'Cliente'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO',
                        'title': 'Serviço'
                    }, {
                        data: 'DT_EXAME',
                        name: 'DT_EXAME',
                        'title': 'Exame Inicial'
                    }, {
                        data: 'DT_MANCHAO',
                        name: 'DT_MANCHAO',
                        'title': 'Manchão'
                    }, {
                        data: 'DT_COBER',
                        name: 'DT_COBER',
                        'title': 'Cobertura'
                    }, {
                        data: 'DT_VULC',
                        name: 'DT_VULC',
                        'title': 'Vulcanização'
                    }, {
                        data: 'DT_FINAL',
                        name: 'DT_FINAL',
                        'title': 'Exame Final'
                    }, {
                        data: 'DS_ETAPA',
                        name: 'DS_ETAPA',
                        'title': 'Etapa'
                    },
                    {
                        data: 'DSOBSERVACAO',
                        name: 'DSOBSERVACAO',
                        'title': 'Observação'
                    }
                ]
            });

            $('#lote-pcp').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get-lote-pcp') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        'title': 'Emp'
                    },
                    {
                        data: 'NR_LOTE',
                        name: 'NR_LOTE',
                        'title': 'Nr Lote'
                    },

                    {
                        data: 'DSCONTROLELOTEPCP',
                        name: 'DSCONTROLELOTEPCP',
                        'title': 'Ds Lote'
                    },
                    {
                        data: 'DTPRODUCAO',
                        name: 'DTPRODUCAO',
                        'title': 'Produção'
                    }, {
                        data: 'QTDE_TOT_LOTE',
                        name: 'QTDE_TOT_LOTE',
                        'title': 'Qtde Lote'
                    }, {
                        data: 'QTDE_EM_PROD',
                        name: 'QTDE_EM_PROD',
                        'title': 'Em producao'
                    }, {
                        data: 'QTDE_SEMEXAME',
                        name: 'QTDE_SEMEXAME',
                        'title': 'Sem Exame'
                    }
                ]
            });
        });
    </script>
@stop
