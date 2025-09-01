@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><a href="#" id="i-finalizados"><i
                                class="fas fa-check"></i></a></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Finalizados</span>
                        <span class="info-box-number finalizados"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon"><a href="#" id="i-aguardando"><i class="far fa-flag"></i></a></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Aguardando</span>
                        <span class="info-box-number aguardando"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><a href="#" id="i-producao"><i
                                class="far fa-copy"></i></a></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Em produção</span>
                        <span class="info-box-number producao"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><a href="#" id="i-bloqueados"><i
                                class="fas fa-ban"></i></a></span>

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
            <div class="col-12 col-sm-4 col-lg-2">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><a href="#" id="i-cancelados"><i
                                class="fas fa-window-close"></i></a></span>

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
                                <div class="card collapsed-card mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title mt-2">Filtros:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Empresa</label>
                                                    <select name="cd_empresa" id="cd_empresa" class="form-control"
                                                        style="width: 100%;">
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
                                                    <input type="text" class="form-control" id="daterange"
                                                        placeholder="Data Emissão">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pedido Palm</label>
                                                    <input type="number" class="form-control" id="pedido_palm"
                                                        placeholder="Pedido Palm">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pedido</label>
                                                    <input type="number" class="form-control" id="pedido"
                                                        placeholder="Pedido">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Grupo Item</label>
                                                    <select name="grupo_item" id="grupo_item" class="form-control"
                                                        style="width: 100%;" multiple>
                                                        <option value="0">Todos</option>
                                                        @foreach ($grupo as $g)
                                                            <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Região</label>
                                                    <select name="cd_regiaocomercial[]" class="form-control"
                                                        id="cd_regiaocomercial" style="width: 100%;" multiple>
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
                                                    <input type="text" class="form-control" id="nm_vendedor"
                                                        placeholder="Nome Vendedor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Cliente</label>
                                                    <input type="text" class="form-control" id="nm_cliente"
                                                        placeholder="Nome Cliente">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary btn-sm float-right mr-2"
                                                        id="searchRegiao">Filtrar</button>
                                                </div>
                                                <!-- /.row -->
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                                <hr>
                                <table class="table stripe compact nowrap" id="pedido-acompanhar"
                                    style="width:100%; font-size:12px">

                                </table>
                            </div>
                            <div class="tab-pane fade" id="bloqueio-pedido" role="tabpanel">
                                <div class="card collapsed-card mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title mt-2">Filtros:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Supervisor</label>
                                                    <input type="text" class="form-control" id="nm_supervisor_bloq"
                                                        placeholder="Nome Supervisor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Vendedor</label>
                                                    <input type="text" class="form-control" id="nm_vendedor_bloq"
                                                        placeholder="Nome Vendedor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Cliente</label>
                                                    <input type="text" class="form-control" id="nm_cliente_bloq"
                                                        placeholder="Nome Cliente">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm float-right mr-2"
                                                        id="searchRegiao">Filtrar</button>
                                                </div>
                                                <!-- /.row -->
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                                <hr>
                                <table class="table stripe compact nowrap" id="bloqueio-pedidos"
                                    style="width:100%; font-size: 12px">
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 10%;
        }

        table.dataTable {
            table-layout: fixed;
        }

        @media (max-width: 768px) {
            .table-left {                
                margin-left: 0 !important;
            }
            .btn-detalhes {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #f0f0f0;
                cursor: pointer;
            }
        }
    </style>
@endsection

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border table-left" id="pedido-{{ ID }}" style="width:80%; ">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sq</th>
                        <th>Nr Ordem</th>
                        <th>Serviço</th>
                        <th>Nr Fogo</th>
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
        var tableBloqueio;
        var inicioData = 0;
        var fimData = 0;
        var dados;

        $('#grupo_item').select2({
            theme: 'bootstrap4',
            width: '100%',
        });
        $('#cd_regiaocomercial').select2({
            theme: 'bootstrap4',
        });
        $('#bloqueio').click(function() {
            //Rever essa rotina atualiza caso o usuario voltar para aba bloqueio
            $('#bloqueio-pedidos').DataTable().destroy();
            tableBloqueio = $('#bloqueio-pedidos').DataTable({
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                fixedHeader: true,
                pagingType: "simple",
                processing: false,
                serverSide: false,
                pageLength: 100,
                // responsive: true,
                scrollX: true,
                ajax: "{{ route('get-bloqueio-pedidos') }}",


                columns: [{
                        data: 'action',
                        name: 'action',
                        "width": "1%",
                        title: 'Emp'
                    },
                    {
                        data: 'CLIENTE',
                        name: 'CLIENTE',
                        "width": "10%",
                        title: 'Cliente'
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        title: 'Emp',
                        visible: false,
                    },
                    {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        "width": "1%",
                        title: 'Pedido',
                        visible: false,
                    },
                    {
                        data: 'MOBILE',
                        name: 'MOBILE',
                        title: 'Palm',
                        "width": "1%",
                    },
                    {
                        data: 'DATA',
                        name: 'DATA',
                        title: 'Data'
                    },
                    {
                        data: 'MOTIVO',
                        name: 'MOTIVO',
                        title: 'Bloqueio'
                    },
                    {
                        data: 'ST_ATIVA',
                        name: 'ST_ATIVA',
                        title: 'Ativo'
                    },
                    {
                        data: 'ST_SCPC',
                        name: 'ST_SCPC',
                        title: 'Scpc'
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                        title: 'Status'
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                        title: 'Tipo Pedido'
                    }, {
                        data: 'VENDEDOR',
                        name: 'VENDEDOR',
                        title: 'Vendedor',
                        visible: false
                    },

                    {
                        data: 'NM_SUPERVISOR',
                        name: 'NM_SUPERVISOR',
                        title: 'Supervisor',
                        visible: false
                    }

                ],
                columnDefs: [{
                        targets: [5],
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    }, {
                        targets: [1],
                        className: 'text-truncate'
                    }

                ],
                createdRow: (row, data, dataIndex, cells) => {
                    $(cells[7]).css('background-color', data.status_cliente);
                    $(cells[8]).css('background-color', data.status_scpc);
                    $(cells[9]).css('background-color', data.status_pedido);
                }
            });

        });

        $('#nm_supervisor_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(12).search(value).draw();
        });

        $('#nm_vendedor_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(11).search(value).draw();
        });

        $('#nm_cliente_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(1).search(value).draw();
        });

        $('#btn-limpar').on('click', function() {
            $('#nm_supervisor_bloq').val('');
            $('#nm_vendedor_bloq').val('');
            $('#nm_cliente_bloq').val('');
            tableBloqueio.search('').columns().search('').draw();
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
                nm_vendedor: $('#nm_vendedor').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: inicioData,
                dt_final: fimData,
                regiao: $('#cd_regiaocomercial').val(),
                idvendedor: ''
            };

            initTableAcompanhar(dados);
        });

        function initTableAcompanhar(dados) {

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
                        "width": "1%",
                        title: 'Emp'
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        visible: false,
                        title: 'Emp'
                    },
                    {
                        data: 'ID',
                        name: 'ID',
                        visible: true,
                        title: 'Pedido'
                    },
                    {
                        data: 'IDPEDIDOMOVEL',
                        name: 'IDPEDIDOMOVEL',
                        "width": "10%",
                        title: 'Palm'
                    },
                    {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        "width": "40%",
                        title: 'Cliente'
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS',
                        "width": "1%",
                        title: 'Pneus'
                    },
                    {
                        data: 'DTEMISSAO',
                        name: 'DTEMISSAO',
                        title: 'Dt Emissão'
                    },
                    {
                        data: 'DTENTREGAPED',
                        name: 'DTENTREGAPED',
                        title: 'Dt Entrega'
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                        title: 'Status'
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                        title: 'Tipo Pedido'
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
                        "defaultContent": '<span class="right mr-2"><i class="btn-detalhes fas fa-plus-circle"></i></span>',
                        "width": "1%"
                    },
                    {
                        data: 'NRSEQUENCIA',
                        name: 'NRSEQUENCIA',
                        "width": "2%",
                    },
                    {
                        data: 'NRORDEM',
                        name: 'NRORDEM',
                        "width": "3%",
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO',
                        "width": "20%",
                    },
                    {
                        data: 'NRFOGO',
                        name: 'NRFOGO',
                        "width": "5%",
                    },
                    {
                        data: 'VLUNITARIO',
                        name: 'VLUNITARIO',
                        width: "5%",
                    }, {
                        data: 'STORDEM',
                        name: 'STORDEM',
                        "width": "2%",
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
                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Open this row
                row_item.child(details_item_pedido(row_item.data())).show();
                initTableItemPedido(tableId, row_item.data());
                tr_item.addClass('shown');
                $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
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
                autoWidth: false,
                sDom: 't',
                ajax: data.details_item_pedido_url,
                columns: [{
                        data: 'O_DS_ETAPA',
                        name: 'O_DS_ETAPA'
                    },
                    {
                        data: 'O_NM_USUARIO',
                        name: 'O_NM_USUARIO',
                        // "width": "15%"
                    },
                    {
                        data: 'DT_ENTRADA',
                        name: 'DT_ENTRADA',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'DT_SAIDA',
                        name: 'DT_SAIDA',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'O_DS_COMPLEMENTOETAPA',
                        name: 'O_DS_COMPLEMENTOETAPA'
                    },
                    {
                        data: 'O_ST_RETRABALHO',
                        name: 'O_ST_RETRABALHO',
                        // width: "2%",
                    },
                ],
                columnDefs: [{
                    targets: [1],
                    className: 'text-truncate'
                }],
                order: [
                    [2, 'asc']
                ]

            });
        }

        // Ativar popover após cada renderização
        $('#bloqueio-pedidos').on('draw.dt', function() {
            $('[data-toggle="popover"]').popover({
                trigger: 'focus', // ou 'click' se quiser persistente
                html: true,
                placement: 'top'
            });
        });

        table.on('draw.dt', function() {

            let dadosFiltrados = table.rows({
                filter: 'applied'
            }).data().toArray();

            let finalizados = dadosFiltrados.filter(item => item.STPEDIDO.trim() === "ATENDIDO").length;
            let producao = dadosFiltrados.filter(item => item.STPEDIDO.trim() === "EM PRODUCAO").length;
            let bloqueados = dadosFiltrados.filter(item => item.STPEDIDO.trim() === "BLOQUEADO").length;
            let aguardando = dadosFiltrados.filter(item => item.STPEDIDO.trim() === "AGUARDANDO").length;
            let canceladas = dadosFiltrados.filter(item => item.STPEDIDO.trim() === "CANCELADO").length;

            $('.finalizados').text(finalizados);
            $('.producao').text(producao);
            $('.bloqueados').text(bloqueados);
            $('.aguardando').text(aguardando);
            $('.canceladas').text(canceladas);

        });

        $('#i-finalizados').click(function() {
            table.search('ATENDIDO').draw();
        });
        $('#i-producao').click(function() {
            table.search('EM PRODUCAO').draw();
        });
        $('#i-aguardando').click(function() {
            table.search('AGUARDANDO').draw();
        });
        $('#i-cancelados').click(function() {
            table.search('CANCELADO').draw();
        });
        $('#i-bloqueados').click(function() {
            table.search('BLOQUEADO').draw();
        });
    </script>
@stop
