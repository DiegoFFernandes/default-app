@extends('layouts.master')
@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="content-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pedidos Bloqueados</h3>
            </div>
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
                            {{-- <th>Comissão</th>
                            <th>% Comissão</th> --}}
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
                            {{-- <th>Comissão</th>
                            <th>% Comissão</th> --}}
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
                            <div class="col-4 col-md-4">
                                <div class="form-group">
                                    <label for="nr_pedido">Pedido</label>
                                    <input class="form-control nr_pedido" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-8 col-md-8">
                                <div class="form-group">
                                    <label for="pessoa">Pessoa</label>
                                    <input id="" class="form-control pessoa" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="vendedor">Vendedor</label>
                                    <input id="" class="form-control vendedor" type="text" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2" class="d-none" id="card-pedido">
                        <div class="card-header">
                            <h3 class="card-title">Itens</h3>
                        </div>
                        <div id="card-container"></div>
                    </div>
                    <table class="table compact row-border" id="table-item-pedido" style="font-size:12px">
                    </table>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="form-group" style="text-align: left">
                                <label for="liberacao">Motivo Liberação:</label>
                                <textarea id="" class="form-control liberacao" rows="3" cols="50"></textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-alert pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success btn-save-confirm" id="">Liberar</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        @media (max-width: 768px) {
            #table-item-pedido_wrapper {
                display: none !important;
            }

            [id^="card-pedido"] {
                display: block !important;
            }

            .form-control {
                font-size: 12px;
            }
        }

        @media (min-width: 769px) {
            [id^="card-pedido"] {
                display: none;
            }
        }
    </style>
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
            $('.vendedor').val(row.data().VENDEDOR);

            $('#modal-table-pedido').modal('show');


            initTable('table-item-pedido', row.data());
        });

        $(document).on('click', '#table-item-pedido td:nth-child(2)', function() {
            var tr = $(this).closest('tr');
            var row = table_item_pedido.row(tr);
            var rowData = row.data();

            var valorCellVenda = tr.find('td').eq(1);
            var valorVenda = parseFloat(rowData.VL_VENDA).toFixed(2); // ← usa o dado puro
            var valorTabela = parseFloat(rowData.VL_PRECO).toFixed(2); // ← usa o dado puro

            if (!valorCellVenda.find('input').length) {
                valorCellVenda.html(
                    `<input type="number" value="${valorVenda}" class="edit-input" style="width: 100%; box-sizing: border-box;"/>`
                );

                var input = valorCellVenda.find('input');
                input.focus();
                input.select();

                input.on('blur', function() {
                    var newValue = parseFloat($(this).val()).toFixed(2);
                    var newPercent = parseFloat((100 - (newValue * 100) / valorTabela)).toFixed(2);

                    rowData.VL_VENDA = parseFloat(newValue);
                    rowData.PC_DESCONTO = parseFloat(newPercent);

                    row.data(rowData).draw(); // ← desenha com formatação automática do render
                });
            }
        });

        $(document).on('click', '.btn-save-confirm', function(e) {
            //obtem os dados de toda a tabela, para fazer o update no banco
            var pneus = [];

            if (isMobile()) {
                $('.input-venda').each(function() {
                    const card = $(this).closest('.card');
                    const vl_venda = parseFloat($(this).val());
                    const vl_preco = parseFloat($(this).data('preco'));
                    const desconto = parseFloat((100 - (vl_venda * 100) / vl_preco)).toFixed(2);

                    pneus.push({
                        ID: $(this).data('id'),
                        VL_VENDA: vl_venda,
                        VL_PRECO: vl_preco,
                        PC_DESCONTO: desconto
                    });
                })
            } else {
                //obtem os dados de toda a tabela, para fazer o update no banco            
                pneus = table_item_pedido.rows().data().toArray();
            }

            console.log(pneus);

            return false;
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

        $('#card-container').on('input', '.input-venda', function() {
            const input = $(this);

            console.log(input);

            const venda = parseFloat(input.val()) || 0;
            const preco = parseFloat(input.data('preco')) || 0;

            let desconto = 0;
            if (preco > 0) {
                desconto = 100 - (venda * 100) / preco;
            }

            

            input.closest('.card-body').find('.percentual').val(desconto.toFixed(2));
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
                // scrollX: true,
                ajax: {
                    url: url,
                    dataSrc: function(json) {

                        const dados = json.data || json;
                        // Renderiza os cards com os mesmos dados
                        renderizarCards(dados, 'card-container');
                        return dados;
                    }
                },
                columns: [{
                        data: 'SEQ',
                        name: 'SEQ',
                        width: '1%',
                        title: 'Seq',
                        visible: false
                    },
                    {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        width: '20%',
                        title: 'Item'
                    },
                    {
                        data: 'VL_VENDA',
                        name: 'VL_VENDA',
                        width: '2%',
                        render: $.fn.dataTable.render.number('.', ',', 2),
                        title: 'Venda'
                    },
                    {
                        data: 'VL_PRECO',
                        name: 'VL_PRECO',

                        render: $.fn.dataTable.render.number('.', ',', 2),
                        title: 'Tabela'
                    },
                    {
                        data: 'PC_DESCONTO',
                        name: 'PC_DESCONTO',
                        render: function(data, type, row) {
                            return data + '%';
                        },

                        title: '%Desc'
                    },
                    {
                        data: 'VL_COMISSAO',
                        name: 'VL_COMISSAO',
                        visible: true,
                        render: $.fn.dataTable.render.number('.', ',', 2),
                        title: 'Comissão'
                    },
                    {
                        data: 'PC_COMISSAO',
                        name: 'PC_COMISSAO',
                        visible: true,
                        render: function(data, type, row) {
                            return data + '%';
                        },
                        title: '% Comissão'
                    }
                ],
                columnDefs: [{
                    targets: [2, 3],
                    className: 'dt-right'
                }],

                "drawCallback": function(settings) {
                    // Só executa em telas menores
                    if ($(window).width() <= 768) {
                        const api = this.api();
                        const headers = api.columns().header().toArray(); // Pega os cabeçalhos

                        api.rows({
                            page: 'current'
                        }).every(function(index) {
                            const rowNode = this.node();
                            const cells = $('td', rowNode);

                            cells.each(function(i) {
                                const label = $(headers[i])
                                    .text(); // Pega o texto do cabeçalho correspondente
                                $(this).attr('data-label', label);
                            });
                        });
                    }
                }
            });
        }

        function renderizarCards(data, containerId) {
            const container = $('#' + containerId);
            container.empty(); // Limpa antes
            data.forEach(item => {
                const card = $(`                       
                            <div class="card-body shadow-sm p-3">
                                <div class="row">
                                    <div class="col-8 col-md-8">
                                        <div class="form-group mb-0">                                  
                                            <label>Item:</label> 
                                            <input type="text" 
                                                class="form-control form-control-sm"
                                                value="${item.DS_ITEM}" readonly />                                    
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <div class="form-group mb-0">                                  
                                            <label>Desconto:</label> 
                                            <input type="text" 
                                                class="form-control form-control-sm percentual"
                                                value="${parseFloat(item.PC_DESCONTO).toFixed(2)}" readonly />                                    
                                        </div>
                                    </div>  
                                
                                    <div class="col-8 col-md-8">
                                        <div class="form-group mb-0">    
                                            <label>Venda:</label>                                
                                            <input type="number"
                                                class="form-control form-control-sm input-venda"
                                                style="width: 100px;"
                                                value="${parseFloat(item.VL_VENDA).toFixed(2)}"
                                                data-preco="${item.VL_PRECO}"
                                                data-id="${item.ID}" />
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <div class="form-group mb-0">
                                            <label>Tabela: </label>
                                            <input type="number"
                                                class="form-control form-control-sm"
                                                
                                                value="${parseFloat(item.VL_PRECO).toFixed(2)}" readonly/>
                                        </div>  
                                    </div>  
                                    <div class="col-8 col-md-8">
                                    <div class="form-group mb-0">
                                        <label>Comissão:</label>
                                        <input type="number"
                                            class="form-control form-control-sm"
                                            style="width: 100px;"
                                            value="${parseFloat(item.VL_COMISSAO).toFixed(2)}" readonly/>
                                    </div>
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <div class="form-group mb-0">
                                            <label>% Comissao</label>
                                            <input type="number"
                                            class="form-control form-control-sm"
                                            
                                            value="${parseFloat(item.PC_COMISSAO).toFixed(2)}" readonly/>
                                        </div>
                                    </div>
                                </div> 
                             </div>                      
                        `);
                container.append(card);
            });
        }

        function isMobile() {
            return $(window).width() <= 768;
        }
    </script>

@stop
