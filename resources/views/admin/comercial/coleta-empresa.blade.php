@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
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
                                        id="searchRegiao">Filtrar</button>
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-danger card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="acompanhamento-1" data-toggle="pill"
                                            href="#acompanhamento-1" role="tab" aria-controls="acompanhamento-pedido"
                                            aria-selected="true">Cambé</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="acompanhamento-1" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-home-tab">

                                        <table class="table stripe compact nowrap" id="coleta-empresa-1"
                                            style="width:100%; font-size:12px">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-danger card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="acompanhamento-3" data-toggle="pill"
                                            href="#tab-empresa-3" role="tab" aria-controls="acompanhamento-pedido"
                                            aria-selected="true">Osvaldo Cruz</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="tab-empresa-3" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-home-tab">
                                        <table class="table stripe compact nowrap" id="coleta-empresa-3"
                                            style="width:100%; font-size:12px">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-danger card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="acompanhamento-5" data-toggle="pill"
                                            href="#tab-empresa-5" role="tab" aria-controls="acompanhamento-pedido"
                                            aria-selected="true">Ponta Grossa</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="tab-empresa-5" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-home-tab">
                                        <table class="table stripe compact nowrap" id="coleta-empresa-5"
                                            style="width:100%; font-size:12px">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-danger card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="acompanhamento-6" data-toggle="pill"
                                            href="#tab-empresa-6" role="tab" aria-controls="acompanhamento-pedido"
                                            aria-selected="true">Catanduva</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="tab-empresa-6" role="tabpanel"
                                        aria-labelledby="custom-tabs-four-home-tab">
                                        <table class="table stripe compact nowrap" id="coleta-empresa-6"
                                            style="width:100%; font-size:12px">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </section>
@stop

@section('css')
    <style>
        table.dataTable thead tr {
            background-color: #444B53;
            color: #ffffff;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
    </style>
@endsection

@section('js')
    <script id="details-pedido-vendedor" type="text/x-handlebars-template">
        @verbatim            
            <table class="table-pedido stripe row-border" id="pedido-{{ IDVENDEDOR }}" style="width:100%">
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
        @endverbatim
    </script>
    <script id="details-pedido-cliente" type="text/x-handlebars-template">
        @verbatim            
            <table class="table stripe row-border no-padding" id="item-pedido-{{ ID }}" style="width:100%">
                <thead>
                    <tr>                       
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
            <table class="table str row-border" id="item-pedido-{{ ID }}" style="width:100%">
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
        var details_pedido_vendedor = Handlebars.compile($("#details-pedido-vendedor").html());
        var details_pedido_cliente = Handlebars.compile($("#details-pedido-cliente").html());
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

        $('#pedido-acompanhar').DataTable().destroy();

        var tableEmpresa1 = initTableColetaGeral(1, 'coleta-empresa-1');
        var tableEmpresa3 = initTableColetaGeral(3, 'coleta-empresa-3');
        var tableEmpresa5 = initTableColetaGeral(5, 'coleta-empresa-5');
        var tableEmpresa6 = initTableColetaGeral(6, 'coleta-empresa-6');


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
                regiao: $('#cd_regiaocomercial').val()
            };

            initTableVendedor(dados);
        });

        //Aguarda Click para buscar os detalhes dos pedidos dos vendedores
        configurarDetalhesLinha('.details-control-pedido', {
            idPrefixo: 'pedido-',
            idCampo: 'IDVENDEDOR',
            templateFn: details_pedido_vendedor,
            initFn: initTableVendedor,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle'
        });

        configurarDetalhesLinha('.details-control', {
            idPrefixo: 'item-pedido-',
            idCampo: 'ID',
            templateFn: details_pedido_cliente,
            initFn: initTablePedidoCliente,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle'
        });

        function configurarDetalhesLinha(selector, options) {
            $(document).on('click', selector, function() {
                const tr = $(this).closest('tr');
                const table = tr.closest('table');
                const tableId = table.attr('id');
                const row = $('#' + tableId).DataTable().row(tr);

                const data = row.data();
                const tableChildId = options.idPrefixo + (options.idCampo ? data[options.idCampo] : data.ID);

                if (row.child.isShown()) { // Se a linha já está expandida
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass(options.iconeMenos).addClass(options.iconeMais);
                } else { // Se a linha não está expandida
                    row.child(options.templateFn(data)).show();
                    options.initFn(tableChildId, data);
                    tr.addClass('shown');
                    $(this).find('i').removeClass(options.iconeMais).addClass(options.iconeMenos);
                    tr.next().find('td').addClass('no-padding');
                }
            });
        }

        function initTableColetaGeral(empresaId, tableId) {
            dados = {
                cd_empresa: empresaId,
                nm_cliente: $('#nm_cliente').val(),
                nm_vendedor: $('#nm_vendedor').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: inicioData,
                dt_final: fimData,
            };
            const table = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                pagingType: "simple",
                processing: false,
                serverSide: false,
                pageLength: 25,
                retrieve: true,
                scrollY: '400px',
                ajax: {
                    url: "{{ route('get-coleta-empresa-geral') }}",
                    data: {
                        data: dados
                    }
                },
                columns: [{
                        title: "",
                        data: 'actions',
                        name: 'actions',
                        "width": "1%"
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        title: "Empresa",
                        "width": "1%",
                        visible: false
                    },
                    {
                        data: 'NM_VENDEDOR',
                        title: "Vendedor",
                        width: "20%",
                        name: 'ID',
                        visible: true
                    },
                    {
                        data: 'BLOQUEADAS',
                        title: "Bloq.",
                        name: 'BLOQUEADAS',
                        "width": "1%"
                    },
                    {
                        data: 'QTDPEDIDOS',
                        title: "Pedidos",
                        name: 'QTDPEDIDOS',

                    },
                    {
                        data: 'QTDPNEUS',
                        title: "Pneus",
                        name: 'QTDPNEUS',
                        "width": "1%"
                    },
                    {
                        data: 'VALOR_MEDIO',
                        title: "Vlr Médio",
                        name: 'VALOR_MEDIO',
                        "width": "1%"
                    }
                ],
                columnDefs: [{
                        targets: [6],
                        className: 'dt-right',
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    },
                    {
                        targets: 2, // índice da coluna que você quer truncar
                        className: 'text-truncate'
                    }

                ],

                footerCallback: function(row, data, start, end, display) {


                }
            });
            return table;
        }

        function initTableVendedor(tableId, dados) {

            dados = {
                cd_empresa: dados.CD_EMPRESA,
                idvendedor: dados.IDVENDEDOR,
                pedido: "",
                pedido_palm: "",
                nm_cliente: "",
                nm_vendedor: "",
                empresa: 0,
                grupo_item: 0,
                dt_inicial: moment().format('DD.MM.YYYY'),
                dt_final: moment().format('DD.MM.YYYY')

            }

            table = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                paging: false,
                sDom: 't',
                processing: false,
                serverSide: false,
                retrieve: true,
                searching: false,
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
                        visible: false,
                        "width": "10%"
                    },
                    {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        "width": "35%"
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


                }
            });
            return table;
        }

        function initTablePedidoCliente(tableId, data) {
            return $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                sDom: 't',
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
    </script>
@stop
