@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Finalizados</span>
                        <span class="info-box-number finalizados"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-2 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon"><i class="far fa-flag"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Aguardando</span>
                        <span class="info-box-number aguardando"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-2 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Em produção</span>
                        <span class="info-box-number producao"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-2 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-ban"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Bloqueados</span>
                        <span class="info-box-number bloqueados"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <!-- /.col -->
            <div class="col-md-2 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-window-close"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Canceladas</span>
                        <span class="info-box-number canceladas"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-danger card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="acompanhamento" data-toggle="pill"
                                    href="#acompanhamento-pedido" role="tab" aria-controls="acompanhamento-pedido"
                                    aria-selected="true">Acompanhamento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="bloqueio" data-toggle="pill" href="#bloqueio-pedido" role="tab"
                                    aria-controls="bloqueio-pedido" aria-selected="false">Pedidos Bloqueados</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="acompanhamento-pedido" role="tabpanel"
                                aria-labelledby="custom-tabs-four-home-tab">
                                <div>
                                    <button class="btn btn-tool pull-right" id="icon-filter"><i
                                            class="fas fa-filter"></i></button>
                                    <button class="btn btn-tool pull-right" id="icon-refresh"><i
                                            class="fas fa-sync-alt"></i></button>
                                </div>

                                <table class="table stripe compact nowrap" id="pedido-acompanhar"
                                    style="width:100%; font-size:12px">
                                    <thead>
                                        <tr>
                                            <th>Emp</th>
                                            <th>Emp</th>
                                            <th>Pedido</th>
                                            <th>Pedido Palm</th>
                                            <th>Cliente</th>
                                            <th>Pneus</th>
                                            <th>Dt Emissão</th>
                                            <th>Dt Entrega</th>
                                            <th>Status</th>
                                            <th>Tipo Pedido</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="bloqueio-pedido" role="tabpanel">
                                <table class="table stripe compact nowrap" id="bloqueio-pedidos"
                                    style="width:100%; font-size: 12px">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Emp</th>
                                            <th>Pedido</th>
                                            <th>Pedido Palm</th>
                                            <th>Data</th>
                                            <th>Bloqueio</th>
                                            <th>Ativo</th>
                                            <th>Scpc</th>
                                            <th>Status</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
    </section>
    <!-- /.content -->
    <div class="modal fade" id="modal-filter">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Filtros</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Empresa</label>
                                 <select name="cd_empresa" id="cd_empresa" class="form-control" style="width: 100%;">   
                                        <option value="0" selected>Todas</option>                                 
                                    @foreach ($empresa as $e)
                                        <option value="{{ $e->CD_EMPRESA }}">{{ $e->CD_EMPRESA }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Dt Emissão</label>
                                <input type="text" class="form-control" id="daterange" placeholder="Data Emissão">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Região</label>
                                <select name="cd_regiaocomercial[]" class="form-control" id="cd_regiaocomercial" style="width: 100%;"
                                    multiple>
                                    @foreach ($regiao as $r)
                                        <option value="{{ $r->CD_REGIAOCOMERCIAL }}">{{ $r->DS_REGIAOCOMERCIAL }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Cliente</label>
                                <input type="text" class="form-control" id="nm_cliente" placeholder="Nome Cliente">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pedido Palm</label>
                                <input type="number" class="form-control" id="pedido_palm" placeholder="Pedido Palm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pedido</label>
                                <input type="number" class="form-control" id="pedido" placeholder="Pedido">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Grupo Item</label>
                                <select name="grupo_item" id="grupo_item" class="form-control" style="width: 100%;">    
                                    <option value="0">Todos</option>                                 
                                    @foreach ($grupo as $g)
                                        <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="searchRegiao">Filtrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('css')
    <style>

    </style>
@endsection

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border" id="pedido-{{ ID }}" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sq</th>
                        <th>Nr Ordem</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script id="details-item-pedido" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ NRORDEM }} - {{DSSERVICO}}</span>
            <table class="table row-border" id="item-pedido-{{ ID }}" style="width:100%">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>Usúario</th>
                        <th>Entrada</th>
                        <th>Saida</th>
                        <th>Detalhes</th>
                        <th>Retrabalho</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script type="text/javascript">
        var template = Handlebars.compile($("#details-template").html());
        var details_item_pedido = Handlebars.compile($("#details-item-pedido").html());
        var regiao;
        var table;
        var inicioData = 0;
        var fimData = 0;
        var dados;

        $('#grupo_item').select2({
            placeholder: 'Selecione o grupo',
            theme: 'bootstrap4',
        });

        $('#cd_regiaocomercial').select2({
            theme: 'bootstrap4',
        });
        $('#bloqueio').click(function() {
            //Rever essa rotina atualiza caso o usuario voltar para aba bloqueio
            $('#bloqueio-pedidos').DataTable().destroy();
            $('#bloqueio-pedidos').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                pagingType: "simple",
                processing: false,
                serverSide: false,
                pageLength: 25,
                // responsive: true,
                scrollX: true,
                ajax: "{{ route('get-bloqueio-pedidos') }}",
                columns: [{
                        data: 'action',
                        name: 'action',
                        "width": "1%",
                    },
                    {
                        data: 'CLIENTE',
                        name: 'CLIENTE',
                        "width": "5%",
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        visible: true
                    },
                    {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        "width": "1%",
                        visible: false,
                    },
                    {
                        data: 'MOBILE',
                        name: 'MOBILE',
                        "width": "1%",
                    },
                    {
                        data: 'DATA',
                        name: 'DATA',
                    },
                    {
                        data: 'MOTIVO',
                        name: 'MOTIVO',
                    },
                    {
                        data: 'ST_ATIVA',
                        name: 'ST_ATIVA',
                    },
                    {
                        data: 'ST_SCPC',
                        name: 'ST_SCPC',
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                    }
                ],
                columnDefs: [{
                    targets: [5],
                    render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                }],
                createdRow: (row, data, dataIndex, cells) => {
                    $(cells[7]).css('background-color', data.status_cliente);
                    $(cells[8]).css('background-color', data.status_scpc);
                    $(cells[9]).css('background-color', data.status_pedido);
                }
            });

        });

        $('#acompanhamento').click(function() {
            $('#pedido-acompanhar').DataTable().ajax.reload();
        });

        $('#title-page').text('Acompanhameto Pedido');

        $('#pedido-acompanhar').DataTable().destroy();        

        table = initTableAcompanhar(dados);

        $('#pedido-acompanhar tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            // console.log(tableId);
            var tableId = 'pedido-' + row.data().ID;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');

                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');

            } else {
                // Open this row
                row.child(template(row.data())).show();
                initTable(tableId, row.data());
                // console.log(row.data());
                tr.addClass('shown');
                $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                tr.next().find('td').addClass('no-padding');
            }
        });

        $('#searchRegiao').click(function() {
            $('#pedido-acompanhar').DataTable().destroy();


            dados = {
                cd_empresa: $('#cd_empresa').val(),
                nm_cliente: $('#nm_cliente').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: inicioData,
                dt_final: fimData,
                regiao: $('#cd_regiaocomercial').val()
            };



            $('#modal-filter').modal('hide');
            initTableAcompanhar(dados);
        });

        function initTableAcompanhar(dados) {

            console.log(dados);
            table = $('#pedido-acompanhar').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                pagingType: "simple",
                processing: false,
                serverSide: false,
                pageLength: 25,
                retrieve: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('get-pedido-acompanhar') }}",
                    data: {
                        data: dados
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        "width": "1%"
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        visible: false
                    },
                    {
                        data: 'ID',
                        name: 'ID',
                        visible: true
                    },
                    {
                        data: 'IDPEDIDOMOVEL',
                        name: 'IDPEDIDOMOVEL',
                        "width": "10%"
                    },
                    {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        "width": "40%"
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS',
                        "width": "1%"
                    },
                    {
                        data: 'DTEMISSAO',
                        name: 'DTEMISSAO',
                    },
                    {
                        data: 'DTENTREGAPED',
                        name: 'DTENTREGAPED',
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                    }
                ],
                columnDefs: [{
                    targets: [6, 7],
                    className: 'dt-center',
                    render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                }],
                "order": [6, 'desc'],
                footerCallback: function(row, data, start, end, display) {

                    let finalizados = data.filter(item => item.STPEDIDO === "ATENDIDO        ").length;
                    let producao = data.filter(item => item.STPEDIDO === "EM PRODUCAO     ").length;
                    let bloqueados = data.filter(item => item.STPEDIDO === "BLOQUEADO       ").length;
                    let aguardando = data.filter(item => item.STPEDIDO === "AGUARDANDO      ").length;
                    let canceladas = data.filter(item => item.STPEDIDO === "CANCELADO       ").length;

                    $('.finalizados').text(finalizados);
                    $('.producao').text(producao);
                    $('.bloqueados').text(bloqueados);
                    $('.aguardando').text(aguardando);
                    $('.canceladas').text(canceladas);

                }

            });
            return table;
        }

        function initTable(tableId, data) {

            table_item_pedido = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ajax: {
                    method: "GET",
                    url: " {{ route('get-item-pedido-acompanhar') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        id: data.ID
                    }
                },
                columns: [{
                        "className": 'details-item-control',
                        "orderable": false,
                        "searchable": false,
                        "data": 'null',
                        "defaultContent": '<span class="right mr-2"><i class="fas fa-plus-square"></i></span>',
                        "width": "1%"
                    },
                    {
                        data: 'NRSEQUENCIA',
                        name: 'NRSEQUENCIA'
                    },
                    {
                        data: 'NRORDEM',
                        name: 'NRORDEM'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO'
                    },
                    {
                        data: 'VLUNITARIO',
                        name: 'VLUNITARIO'
                    }, {
                        data: 'STORDEM',
                        name: 'STORDEM',
                    },
                ]
            });
        }

        $('#pedido-acompanhar tbody').on('click', 'td.details-item-control', function() {
            var tr_item = $(this).closest('tr');
            var row_item = table_item_pedido.row(tr_item);
            var tableId = 'item-pedido-' + row_item.data().ID;
            if (row_item.child.isShown()) {
                // This row is already open - close it
                row_item.child.hide();
                tr_item.removeClass('shown');
                $(this).find('i').removeClass('fa-minus-square').addClass('fa-plus-square');
            } else {
                // Open this row
                row_item.child(details_item_pedido(row_item.data())).show();
                initTableItemPedido(tableId, row_item.data());
                tr_item.addClass('shown');
                $(this).find('i').removeClass('fa-plus-square').addClass('fa-minus-square');
                tr_item.next().find('td').addClass('no-padding');
            }
        });

        function initTableItemPedido(tableId, data) {
            $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ajax: data.details_item_pedido_url,
                columns: [{
                        data: 'O_DS_ETAPA',
                        name: 'O_DS_ETAPA'
                    },
                    {
                        data: 'O_NM_USUARIO',
                        name: 'O_NM_USUARIO'
                    },
                    {
                        data: 'entrada',
                        name: 'entrada'
                    },
                    {
                        data: 'saida',
                        name: 'saida'
                    },
                    {
                        data: 'O_DS_COMPLEMENTOETAPA',
                        name: 'O_DS_COMPLEMENTOETAPA'
                    },
                    {
                        data: 'O_ST_RETRABALHO',
                        name: 'O_ST_RETRABALHO'
                    },
                ],
                
            });
        }
        $('#icon-filter').click(function() {
            $('#modal-filter').modal('show');
        });
        // Ativar popover após cada renderização
        $('#bloqueio-pedidos').on('draw.dt', function() {
            $('[data-toggle="popover"]').popover({
                trigger: 'focus', // ou 'click' se quiser persistente
                html: true,
                placement: 'top'
            });
        });
    </script>
@stop
