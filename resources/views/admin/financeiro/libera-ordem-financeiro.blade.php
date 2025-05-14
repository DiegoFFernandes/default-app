@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="content-fluid">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fa fa-list-ul"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Bloqueado</span>
                        <span class="info-box-number" id="soma-geral"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fa fa-list-ul"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bloqueado Empresa</span>
                        <!-- Linha compacta com detalhamento -->
                        <span class="info-box-number" id="soma-empresas">
                            
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-sort-amount-up-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text titulos">Quantidade de Pneus</span>
                        <span class="info-box-number" id="qtd-titulos">

                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-1">
                        <input id="filtro-empresa" type="text" class="form-control" placeholder="Filtrar por Empresa">
                    </div>
                    <div class="col-md-2 mb-1">
                        <input id="filtro-pedido" type="text" class="form-control" placeholder="Filtrar por Pedido">
                    </div>
                    <div class="col-md-4 mb-1">
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4 mb-1">
                        <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table stripe compact table-font-small" style="width:100%" id="table-ordem-block">
                    <thead>
                        <tr>
                            <th>Emp</th>
                            <th></th>
                            <th>Cliente</th>
                            <th>Pedido</th>
                            <th>Qtd</th>
                            <th>Vendedor</th>
                            <th>Valor</th>
                            <th>Data Bloqueio</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Emp</th>
                            <th></th>
                            <th>Cliente</th>
                            <th class="text-center">Pedido</th>
                            <th>Qtd</th>
                            <th>Vendedor</th>
                            <th>Valor</th>
                            <th>Data Bloqueio</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Itens --}}
    <div class="modal modal-default fade" id="modal-table-pedido">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="nr_pedido">Pedido</label>
                                    <input class="form-control nr_pedido" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="pessoa">Pessoa</label>
                                    <input id="" class="form-control pessoa" type="text" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table compact row-border" id="item-pedido" style="font-size:12px">
                        <thead>
                            <tr>
                                <th style="">Sq</th>
                                <th>Item</th>
                                <th style="">Venda</th>
                                <th style="">Tabela</th>
                                <th style="">%Desc</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="form-group" style="text-align: left">
                                <label for="bloqueio">Motivo Bloqueio:</label>
                                <textarea id="" class="form-control bloqueio" rows="4" cols="50"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="text-align: left">
                                <label for="liberacao">Motivo Liberação:</label>
                                <textarea id="" class="form-control liberacao" rows="4" cols="50"></textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-alert pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success btn-save-confirm" id="">Aprovar</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    <style>
        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {

            display: none;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var tableId = 0;
            var table_item_pedido;
            var table = $('#table-ordem-block').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": true,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                scrollX: true,
                scrollY: '60vh',
                ajax: "{{ route('get-ordens-bloqueadas-financeiro') }}",
                columns: [{
                        data: "actions",
                        name: "actions",
                    },
                    {
                        data: 'EMP',
                        name: 'EMP',
                        // "width": "1%",
                        visible: false,
                    }, {
                        data: 'PESSOA',
                        name: 'PESSOA'
                    }, {
                        data: 'PEDIDO',
                        name: 'PEDIDO'
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS'
                    }, {
                        data: 'VENDEDOR',
                        name: 'VENDEDOR',
                        visible: true
                    },
                    {
                        data: 'VL_TOTAL',
                        name: 'VL_TOTAL',
                        visible: true
                    }, {
                        data: 'DTBLOQUEIO',
                        name: 'DTBLOQUEIO',
                        visible: true
                    }
                ],
                columnDefs: [{
                    targets: 6,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();


                    // Pegando a coluna desejada (ex: coluna 3 = índice 2)
                    var total = api
                        .column(6, {
                            page: 'all'
                        }) // ou 'page: all' para total geral
                        .data()
                        .reduce(function(a, b) {
                            return Number(a) + Number(b.toString().replace(/[^\d.-]/g, ''));
                        }, 0);

                    // Atualiza o footer da coluna
                    $(api.column(6).footer()).html('Total: ' + total.toLocaleString('pt-BR'));

                    updateNumberCards(data, total);
                },

                order: [2, 'asc']
            });
            $('#filtro-empresa').on('keyup', function() {
                table.column(0).search(this.value).draw();
            });
            $('#filtro-pedido').on('keyup', function() {
                table.column(3).search(this.value).draw();
            });
            $('#filtro-nome').on('keyup', function() {
                table.column(2).search(this.value).draw();
            });
            $('#filtro-vendedor').on('keyup', function() {
                table.column(5).search(this.value).draw();
            });
            $('#table-ordem-block tbody').on('click', '.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                tableId = 'pedido-' + row.data().PEDIDO;

                // console.log(row.data());

                $('.nr_pedido').val(row.data().PEDIDO);
                $('.pessoa').val(row.data().PESSOA);

                $('#modal-table-pedido').modal('show');


                initTable('item-pedido', row.data());
            });

            function initTable(tableId, data) {

                var url = "{{ route('get-pneus-ordens-bloqueadas-financeiro', ':pedido') }}";
                url = url.replace(':pedido', data.PEDIDO);

                $('#' + tableId).DataTable().destroy();

                table_item_pedido = $('#' + tableId).DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                    },
                    "searching": false,
                    "paging": false,
                    "bInfo": false,
                    processing: false,
                    serverSide: false,
                    ordering: false,
                    scrollX: true,
                    ajax: url,
                    columns: [{
                            data: 'SEQ',
                            name: 'SEQ',
                            width: '1%'
                        },
                        {
                            data: 'DS_ITEM',
                            name: 'DS_ITEM',
                            width: '20%'
                        },
                        {
                            data: 'VL_VENDA',
                            name: 'VL_VENDA',
                            width: '2%'
                        },
                        {
                            data: 'VL_PRECO',
                            name: 'VL_PRECO',
                            width: '2%'
                        },
                        {
                            data: 'PC_DESCONTO',
                            name: 'PC_DESCONTO',
                            width: '1%'
                        }
                    ],

                    "footerCallback": function(tfoot, data, start, end, display) {
                        $(tfoot).find('td').removeClass('no-padding');

                        let dsbloqueio = '';
                        data.forEach(function(i) {
                            dsbloqueio = i.DSBLOQUEIO;
                        });

                        $('.bloqueio').html(dsbloqueio).css('font-size', '12px');
                    }

                });
            }

            function updateNumberCards(data) {
                let qtdPneus = 0;
                let qtdBloqueio = 0;
                let valor = 0;
                const contagemEmpresas = {};

                data.forEach(function(item) {
                    const emp = item.EMP;
                    contagemEmpresas[emp] = (contagemEmpresas[emp] || 0) + 1;
                    valor += Number(item.VL_TOTAL);
                    qtdPneus += Number(item.QTDPNEUS);
                    qtdBloqueio++;
                });
                const resultado = Object.entries(contagemEmpresas)
                    .map(([emp, qtd]) => `${emp} (${qtd})`)
                    .join(' &bull; ');

                $('#soma-geral').html('Coletas: ' + qtdBloqueio + ' &bull; ' +
                    valor.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).replace('R$', 'Valor:').trim());

                $('#qtd-titulos').text(qtdPneus);
                $('#soma-empresas').html(resultado);
            }

            table.on('draw', function() {
                updateNumberCards(table.rows({
                    filter: 'applied'
                }).data().toArray());
            });

            $(document).on('click', '.btn-save-confirm', function(e) {
                //obtem os dados de toda a tabela, para fazer o update no banco 
                var dataTable = table_item_pedido.rows().data().toArray();

                $.ajax({
                    url: "{{ route('save-libera-pedido-financeiro') }}",
                    method: 'post',
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        pedido: $('.nr_pedido').val(),
                        liberacao: $('.liberacao').val()
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('invisible');
                    },
                    success: function(response) {
                        $("#loading").addClass('invisible');

                        if (response.success) {
                            msgToastr(response.success, 'success');
                            $('#table-ordem-block').DataTable().ajax.reload();
                            // $('#modal-pedido').modal('hide');
                            $('#modal-table-pedido').modal('hide');
                            $('#modal-pedido').modal('hide');
                        } else if (response.warning) {
                            msgToastr(response.warning, 'warning');
                            $('#table-ordem-block').DataTable().ajax.reload();
                            // $('#modal-pedido').modal('hide');
                            $('#modal-table-pedido').modal('hide');
                            $('#modal-pedido').modal('hide');
                        } else {
                            msgToastr(response.error, 'danger');
                        }
                    }
                });
            });


        });
    </script>
@stop
