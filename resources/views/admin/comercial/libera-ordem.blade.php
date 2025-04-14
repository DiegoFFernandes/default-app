@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="content-fluid">
        <div class="card">
            <div class="card-body">
                <table class="table stripe compact table-font-small" style="width:100%" id="table-ordem-block">
                    <thead>
                        <tr>
                            <th>Emp</th>
                            <th></th>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Qtd</th>
                            <th>Vendedor</th>
                            <th>Tabela S/N</th>
                            <th>Validade</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Emp</th>
                            <th></th>
                            <th class="text-center">Pedido</th>
                            <th>Cliente</th>
                            <th>Qtd</th>
                            <th>Vendedor</th>
                            <th>Tabela S/N</th>
                            <th>Validade</th>
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
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
            ajax: "{{ route('get-ordens-bloqueadas-comercial') }}",
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
                    data: 'PEDIDO',
                    name: 'PEDIDO'
                },
                {
                    data: 'PESSOA',
                    name: 'PESSOA'
                }, {
                    data: 'QTDPNEUS',
                    name: 'QTDPNEUS'
                }, {
                    data: 'VENDEDOR',
                    name: 'VENDEDOR',
                    visible: true
                },
                {
                    data: 'TABPRECO',
                    name: 'TABPRECO',
                    visible: true
                },
                {
                    data: 'DT_VALIDADE',
                    name: 'DT_VALIDADE',
                    visible: true
                }
            ],
            order: [2, 'asc']
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

            var url = "{{ route('get-pneus-ordens-bloqueadas-comercial', ':pedido') }}";
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
                }
            });
        }

        $(document).on('click', '.btn-save-confirm', function(e) {


            //obtem os dados de toda a tabela, para fazer o update no banco 
            var dataTable = table_item_pedido.rows().data().toArray();

            $.ajax({
                url: "{{ route('save-libera-pedido') }}",
                method: 'post',
                data: {
                    _token: $("[name=csrf-token]").attr("content"),
                    pedido: $('.nr_pedido').val(),
                    liberacao: $('.liberacao').val(),
                    pneus: dataTable
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
    </script>

@stop
