@extends('layouts.master')

@section('title', 'Liberação Financeiro')

@section('content')
    <div class="content-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-danger">
                    <div class="stat-title"><i class="fa fa-list-ul"></i> Total Bloqueado</div>
                    <div class="stat-value"><span id="qtd-bloqueado">0</span> <small
                            style="font-size:.7rem;font-weight:400;">Pedidos</small></div>
                    <div class="stat-rows">
                        <div class="stat-row">
                            <span class="stat-row-label">Valor</span>
                            <span class="stat-row-val" id="valor-bloqueado">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-danger">
                    <div class="stat-title"><i class="fa fa-list-ul"></i> Bloqueado por Empresa</div>
                    <div class="stat-rows" id="rows-empresas"></div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-warning">
                    <div class="stat-title"><i class="fas fa-sort-amount-up-alt"></i> Quantidade de Pneus</div>
                    <div class="stat-value"><span id="qtd-titulos">0</span></div>
                </div>
            </div>
        </div>
        {{-- Filtros --}}
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1 text-muted"></i> Filtros</h3>
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
                        <label for="filtro-empresa" class="form-label small"><i
                                class="fas fa-building mr-1 text-muted"></i>Empresa</label>
                        <input id="filtro-empresa" type="text" class="form-control" placeholder="Filtrar por Empresa">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label for="filtro-pedido" class="form-label small"><i
                                class="fas fa-file-invoice mr-1 text-muted"></i>Pedido</label>
                        <input id="filtro-pedido" type="text" class="form-control" placeholder="Filtrar por Pedido">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="filtro-nome" class="form-label small"><i
                                class="fas fa-user mr-1 text-muted"></i>Pessoa</label>
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label for="filtro-vendedor" class="form-label small"><i
                                class="fas fa-user-tie mr-1 text-muted"></i>Vendedor</label>
                        <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-responsive compact table-font-small" id="table-ordem-block">
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Emp</th>
                            <th></th>
                            <th>Cliente</th>
                            <th class="text-center">Pedido</th>
                            <th>Qtd Pneus</th>
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

                    <table class="table compact table-font-small table-bordered table-responsive" id="item-pedido">
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
        table.dataTable {
            table-layout: fixed;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {

            display: none;
        }

        .stat-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .09);
            border-left: 4px solid;
            border-radius: 4px;
            padding: 10px 12px;
            height: 100%;
            position: relative;
        }

        .stat-card .stat-title {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card .stat-title i {
            font-size: .7rem;
        }

        .stat-card .stat-value {
            font-size: 1rem;
            font-weight: 700;
            word-break: break-all;
            line-height: 1.3;
        }

        .stat-card .stat-rows {
            margin-top: 1px;
        }

        .stat-card .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: .71rem;
            padding: 2px 0;
            border-top: 1px solid rgba(0, 0, 0, .05);
        }

        .stat-card .stat-row-label {
            color: #6c757d;
            flex-shrink: 0;
        }

        .stat-card .stat-row-val {
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 58%;
        }

        .stat-danger {
            border-left-color: #dc3545;
        }

        .stat-danger .stat-title i,
        .stat-danger .stat-value {
            color: #dc3545;
        }

        .stat-warning {
            border-left-color: #e0a800;
        }

        .stat-warning .stat-title i,
        .stat-warning .stat-value {
            color: #c89100;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var tableId = 0;
            var table_item_pedido;
            var table = $('#table-ordem-block').DataTable({
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 50,
                // scrollY: "400px",
                // scrollX: true,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: "{{ route('get-ordens-bloqueadas-financeiro') }}",
                columns: [{
                        data: "actions",
                        name: "actions",
                        "width": "3%",
                        className: 'text-center',
                        title: "Emp",
                    },
                    {
                        data: 'EMP',
                        name: 'EMP',
                        "width": "1%",
                        visible: false,
                        title: 'Emp.'
                    }, {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        title: 'Cliente'
                    }, {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        "width": "6%",
                        title: 'Pedido',
                        className: 'text-center'
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS',
                        title: 'Qtd Pneus',
                        className: 'text-center'
                    }, {
                        data: 'VENDEDOR',
                        name: 'VENDEDOR',
                        title: 'Vendedor',
                        visible: true
                    },
                    {
                        data: 'VL_TOTAL',
                        name: 'VL_TOTAL',
                        title: 'Valor',
                        visible: true
                    }, {
                        data: 'DTBLOQUEIO',
                        name: 'DTBLOQUEIO',
                        title: 'Bloqueio',
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
                    $(api.column(6).footer()).html('R$ ' + total.toLocaleString('pt-BR'));

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
                            width: '1%',
                            title: 'Seq'
                        },
                        {
                            data: 'DS_ITEM',
                            name: 'DS_ITEM',
                            width: '20%',
                            title: 'Serviço'
                        },
                        {
                            data: 'VL_VENDA',
                            name: 'VL_VENDA',
                            title: 'Vl Venda',
                            width: '2%'
                        },
                        {
                            data: 'VL_PRECO',
                            name: 'VL_PRECO',
                            title: 'Vl Tabela',
                            width: '2%'
                        },
                        {
                            data: 'PC_DESCONTO',
                            name: 'PC_DESCONTO',
                            title: '%Desconto',
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
                    const emp = item.NM_EMPRESA;
                    contagemEmpresas[emp] = (contagemEmpresas[emp] || 0) + 1;
                    valor += Number(item.VL_TOTAL);
                    qtdPneus += Number(item.QTDPNEUS);
                    qtdBloqueio++;
                });
                const rowsEmpresas = Object.entries(contagemEmpresas)
                    .map(([emp, qtd]) =>
                        `<div class="stat-row"><span class="stat-row-label">${emp}</span><span class="stat-row-val">${qtd}</span></div>`
                    ).join('');

                $('#qtd-bloqueado').text(qtdBloqueio);
                $('#valor-bloqueado').text(valor.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));

                $('#qtd-titulos').text(qtdPneus);
                $('#rows-empresas').html(rowsEmpresas);
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
