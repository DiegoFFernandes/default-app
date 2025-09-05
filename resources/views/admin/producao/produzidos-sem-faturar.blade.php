@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
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

            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Valor</span>
                            <span class="info-box-number" id="valorTotal"></span>
                        </div>
                    </div>
                </div>
            @endhasrole

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expedicionado</span>
                        <span class="info-box-number" id="expedicionado"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros:</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select name="cd_empresa" id="cd_empresa" class="form-control" style="width: 100%;">
                                        <option value="0" selected>Todas</option>
                                        @foreach ($empresa as $e)
                                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Dt Emissão</label>
                                    <input type="text" class="form-control" id="daterange" placeholder="Data Emissão">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido Palm</label>
                                    <input type="number" class="form-control" id="pedido_palm" placeholder="Pedido Palm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido</label>
                                    <input type="number" class="form-control" id="pedido" placeholder="Pedido">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grupo Item</label>
                                    <select name="grupo_item" id="grupo_item" class="form-control" style="width: 100%;">
                                        <option value="0">Todos</option>
                                        {{-- @foreach ($grupo as $g)
                                        <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}
                                        </option>
                                    @endforeach --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Região</label>
                                    <select name="cd_regiaocomercial[]" class="form-control" id="cd_regiaocomercial"
                                        style="width: 100%;" multiple>
                                        @foreach ($regiao as $r)
                                            <option value="{{ $r->CD_REGIAOCOMERCIAL }}">
                                                {{ $r->DS_REGIAOCOMERCIAL }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vendedor</label>
                                    <input type="text" class="form-control" id="nm_vendedor" placeholder="Nome Vendedor">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" id="nm_cliente" placeholder="Nome Cliente">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm float-right mr-2"
                                        id="search">Filtrar</button>
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="produzidosTable" class="table table-bordered table-font-small compact">

                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align: right;"></th>
                                    <th colspan="3"></th>
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
        .col-actions {
            width: 1% !important;
        }

        @media (max-width: 768px) {
            .table-left {
                margin-left: 0 !important;
            }

            .col-actions {
                width: 2% !important;
            }            
        }
    </style>
@stop

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge badge-danger">{{ NM_PESSOA }}</span>
            <table class="table stripe row-border no-padding table-left" id="pedido-{{ NR_COLETA }}-{{ EXPEDICIONADO }}-{{ NR_EMBARQUE }}" style="width:80%">
                <thead style="background-color: #434A51;">
                    <tr>
                        <th>Expedicinado</th>
                        <th>Nr Ordem</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script>
        $(document).ready(function() {
            var inicioData = 0;
            var fimData = 0;
            var dados;
            var table;

            $('#grupo_item').select2({
                placeholder: 'Selecione o grupo',
                theme: 'bootstrap4',
            });
            $('#cd_regiaocomercial').select2({
                theme: 'bootstrap4',
            });
            var template = Handlebars.compile($("#details-template").html());

            initTablePneus();

            $('#search').click(function() {
                $('#produzidosTable').DataTable().destroy();

                dados = {
                    cd_empresa: $('#cd_empresa').val(),
                    nm_cliente: $('#nm_cliente').val(),
                    nm_vendedor: $('#nm_vendedor').val(),
                    pedido_palm: $('#pedido_palm').val(),
                    pedido: $('#pedido').val(),
                    grupo_item: $('#grupo_item').val(),
                    cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                    dt_inicial: inicioData,
                    dt_final: fimData,
                    regiao: $('#cd_regiaocomercial').val()
                };

                initTablePneus(dados);
            });


            $('#produzidosTable').on('click', 'tbody tr', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                // console.log(tableId);
                var tableId = 'pedido-' + row.data().NR_COLETA;

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    // Open this row
                    row.child(template(row.data())).show();
                    initTable(tableId, row.data());
                    tr.addClass('shown');
                    $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    // tr.next().find('td').addClass('no-padding');
                }

            });

            function initTablePneus(dados) {
                table = $('#produzidosTable').DataTable({
                    pageLength: 50,
                    "language": {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    fixedHeader: true,
                    "scrollX": true,
                    ajax: {
                        url: "{{ route('get-pneus-produzidos-sem-faturar') }}",
                        method: "GET",
                        data: {
                            data: dados
                        }
                    },
                    "columns": [{
                            "data": "CD_EMPRESA",
                            title: "Emp",
                        },
                        {
                            "data": "NR_EMBARQUE",
                            title: "Embarque"
                        },
                        {
                            "data": "NR_COLETA",
                            title: "Coleta"
                        },
                        {
                            "data": "NM_PESSOA",
                            title: "Cliente"
                        },
                        @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                            {
                                "data": "VALOR",
                                title: "Valor"
                            },
                        @endhasrole {
                            "data": "PNEUS",
                            title: "Pneus"
                        },
                        {
                            "data": "NM_VENDEDOR",
                            title: "Vendedor"
                        },
                        {
                            "data": "EXPEDICIONADO",
                            title: "Expedicionado",
                        },
                        {
                            "data": "DTFIM",
                            render: function(data) {
                                return moment(data).format('DD/MM/YYYY HH:mm');
                            },
                            title: "Data",
                            "visible": true
                        }
                    ],
                    "columnDefs": [{
                        "targets": 0,
                        "className": "text-center",
                    }],
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
            }

            function initTable(tableId, data) {
                var tableItemOrdem = $('#' + tableId + '-' + data.EXPEDICIONADO + '-' + data.NR_EMBARQUE).DataTable({
                    "language": {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    sDom: 't',
                    paging: false,
                    searching: true,
                    ajax: {
                        "url": "{{ route('get-pneus-produzidos-sem-faturar-details') }}",
                        "method": "GET",
                        "data": {
                            'pedido': data.NR_COLETA,
                            'nr_embarque': data.NR_EMBARQUE,
                            'expedicionado': data.EXPEDICIONADO
                        }
                    },
                    columns: [{
                            data: "EXPEDICIONADO",
                            title: "Expedicionado"
                        },
                        {
                            data: "NRORDEMPRODUCAO",
                            title: "Nr Ordem"
                        },
                        {
                            data: "DS_ITEM",
                            title: "Descrição"
                        },
                        @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                            {
                                "data": "VALOR",
                                title: "Valor"
                            },
                        @endhasrole {
                            data: "DTFIM",
                            title: "Data",
                            render: function(data) {
                                return moment(data).format('DD/MM/YYYY HH:mm');
                            }
                        }

                    ]
                });
            }

        });
    </script>
@stop
