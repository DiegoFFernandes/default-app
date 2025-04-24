@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pneus</span>
                        <span class="info-box-number" id="pneusTotal"></span>
                    </div>
                </div>
            </div>


            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor</span>
                        <span class="info-box-number" id="valorTotal"></span>
                    </div>
                </div>
            </div>


            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expedicionado</span>
                        <span class="info-box-number" id="expedicionado"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="produzidosTable" class="table table-bordered table-striped table-font-small compact">
                            <thead>
                                <tr>
                                    <th>Emp</th>
                                    <th>Nr Embarque</th>
                                    <th>Pedido</th>
                                    <th>Pessoa</th>
                                    <th>Valor</th>
                                    <th>Pneus</th>
                                    <th>Expedicionado</th>
                                    <th>Data Entrega</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align: right;"></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@stop

@section('css')
    <style>
        
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#produzidosTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                },
                "responsive": true,
                "autoWidth": false,
                "ajax": {
                    "url": "{{ route('get-pneus-produzidos-sem-faturar') }}",
                    "method": "GET",
                },
                "columns": [{
                        "data": "CD_EMPRESA"
                    },
                    {
                        "data": "NR_EMBARQUE"
                    },
                    {
                        "data": "NR_COLETA"
                    },
                    {
                        "data": "NM_PESSOA"
                    },
                    {
                        "data": "VALOR"
                    },
                    {
                        "data": "PNEUS"
                    },
                    {
                        "data": "EXPEDICIONADO"
                    }, {
                        "data": "DTENTREGA"
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    let QtdPneus = 0;
                    let valorTotal = 0;
                    let expedicionadoSim = 0;
                    let expedicionadoNao = 0;

                    data.forEach(function(item) {
                        QtdPneus += Number(item.PNEUS);
                        valorTotal += parseFloat(item.VALOR.replace(/\./g, '').replace(',',
                            '.'));
                        if (item.EXPEDICIONADO == 'SIM') {
                            expedicionadoSim += Number(item.PNEUS);
                        } else {
                            expedicionadoNao += Number(item.PNEUS);
                        }

                    });

                    $(this.api().column(5).footer()).html('Total: ' + QtdPneus);

                    $('#pneusTotal').html(QtdPneus);
                    $('#valorTotal').html('R$ ' + valorTotal.toFixed(2).replace('.', ',').replace(
                        /\B(?=(\d{3})+(?!\d))/g, '.'));
                    $('#expedicionado').html('Sim: ' + expedicionadoSim + ' | Não: ' +
                    expedicionadoNao);

                },

            });

            // Adjust font size for search and length elements
            $('.dataTables_length, .dataTables_filter').css('font-size', '9px');
        });
    </script>
@stop
