@extends('layouts.master')
@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="content-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pedidos Bloqueados</h3>
                <span class="float-right badge bg-warning">Coordenador</span>
            </div>
            <div class="card-body">
                <table class="table stripe compact table-font-small" style="width:100%" id="table-ordem-block">
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
                                    <input class="form-control form-control-sm nr_pedido" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-8 col-md-8">
                                <div class="form-group">
                                    <label for="pessoa">Pessoa</label>
                                    <input id="" class="form-control form-control-sm pessoa" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="vendedor">Vendedor</label>
                                    <input id="" class="form-control form-control-sm vendedor" type="text"
                                        readonly>
                                    <blockquote class="quote-danger d-none">
                                        <small class="form-text text-muted">Apenas o Coordenador libera. Edição
                                            permitida.</small>
                                    </blockquote>

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
        table.dataTable {
            table-layout: fixed;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        @media (max-width: 768px) {
            #table-item-pedido_wrapper {
                display: none !important;
            }

            [id^="card-pedido"] {
                display: block !important;
            }

            .form-control {
                font-size: 13px;
            }
        }

        @media (min-width: 769px) {
            [id^="card-pedido"] {
                display: none;
            }
        }

        .input-destaque {
            background-color: #218838 !important;
            /* vermelho claro */
            border: 1px solid #1e7e34 !important;
            /* borda vermelha */
            transition: background-color 0.5s ease;
            color: #fff !important;
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
                    "width": "2%",
                    title: "",
                },
                {
                    data: 'EMP',
                    name: 'EMP',
                    "width": "1%",
                    visible: false,
                    title: 'Emp'
                }, {
                    data: 'PEDIDO',
                    "width": "6%",
                    name: 'PEDIDO',
                    title: 'Pedido'
                },
                {
                    data: 'PESSOA',
                    name: 'PESSOA',

                    title: 'Cliente'
                }, {
                    data: 'QTDPNEUS',
                    name: 'QTDPNEUS',
                    title: 'Pneus'
                }, {
                    data: 'VENDEDOR',
                    name: 'VENDEDOR',
                    title: 'Vendedor',
                    visible: true
                },
                {
                    data: 'TABPRECO',
                    name: 'TABPRECO',
                    title: 'Tabela',
                    visible: true
                },
                {
                    data: 'DT_VALIDADE',
                    name: 'DT_VALIDADE',
                    title: 'Validade',
                    visible: false
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
            var valorVenda = parseFloat(rowData.VL_VENDA).toFixed(2);

            if (!valorCellVenda.find('input').length) {
                valorCellVenda.html(
                    `<input type="number" value="${valorVenda}" class="edit-input" style="width: 100%; box-sizing: border-box;"/>`
                );

                var input = valorCellVenda.find('input');
                input.focus();
                input.select();

                input.on('blur', function(e) {
                    clearTimeout(debounceTimer); // Limpa o timer anterior, se houver

                    // Vai esperar 500ms antes de fazer a requisição
                    debounceTimer = setTimeout(() => {
                        const venda = parseFloat($(this).val()) || 0;
                        const preco = rowData.VL_PRECO || 0;

                        $.get('{{ route('get-calcula-comissao') }}', {
                            item_pedido: rowData.ID,
                            venda: venda
                        }, function(data) {

                            console.log(data);

                            let desconto = 0;
                            let commissao = 0;

                            if (preco > 0) {
                                desconto = 100 - (venda * 100) / preco;
                            }

                            rowData.VL_VENDA = parseFloat(venda).toFixed(2);
                            rowData.PC_DESCONTO = parseFloat(desconto).toFixed(2);
                            rowData.VL_COMISSAO = parseFloat(data[0].VL_COMISSAO).toFixed(
                                2);
                            rowData.PC_COMISSAO = parseFloat(data[0].PC_COMISSAO).toFixed(
                                2);
                            row.data(rowData).draw();
                        });
                    }, 500); // 500ms de debounce

                });

                input.on('keydown', function(e) {
                    if (e.which === 13) { // Enter
                        e.preventDefault(); // evita quebra de linha
                        $(this).blur(); // força o blur, que chama a função de atualização
                    }
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
                    const vl_preco = card.find('.input-preco').val();
                    const desconto = parseFloat((100 - (vl_venda * 100) / vl_preco)).toFixed(2);
                    const pc_comissao = card.find('.percentual-comissao').val();
                    const vl_comissao = card.find('.vl-comissao').val();

                    pneus.push({
                        ID: $(this).data('id'),
                        VL_VENDA: vl_venda,
                        VL_PRECO: vl_preco,
                        VL_COMISSAO: vl_comissao,
                        PC_DESCONTO: desconto,
                        PC_COMISSAO: pc_comissao
                    });
                })
            } else {
                //obtem os dados de toda a tabela, para fazer o update no banco            
                pneus = table_item_pedido.rows().data().toArray();
            }
            $.ajax({
                url: "{{ route('save-libera-pedido') }}",
                method: 'post',
                data: {
                    _token: $("[name=csrf-token]").attr("content"),
                    pedido: $('.nr_pedido').val(),
                    liberacao: $('.liberacao').val(),
                    pneus: pneus
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

        let debounceTimer; // armazena o timer de debounce
        $('#card-container').on('input', '.input-venda', function() {
            const input = $(this);

            clearTimeout(debounceTimer); // Limpa o timer anterior, se houver

            debounceTimer = setTimeout(() => {
                const venda = parseFloat(input.val()) || 0;
                const item_pedido = input.closest('.card-body').find('.input-id-item').val() || 0;
                const preco = input.closest('.card-body').find('.input-preco').val() || 0;

                $.get('{{ route('get-calcula-comissao') }}', {
                    item_pedido: item_pedido,
                    venda: venda
                }, function(data) {

                    let desconto = 0;
                    let commissao = 0;

                    if (preco > 0) {
                        desconto = 100 - (venda * 100) / preco;
                    }

                    const percentual = input.closest('.card-body').find('.percentual');
                    const vlComissao = input.closest('.card-body').find('.vl-comissao');
                    const percentualComissao = input.closest('.card-body').find(
                        '.percentual-comissao');

                    percentual.val(desconto.toFixed(2));
                    vlComissao.val(parseFloat(data[0].VL_COMISSAO).toFixed(2));
                    percentualComissao.val(parseFloat(data[0].PC_COMISSAO).toFixed(2));

                    // Aplica destaque
                    [percentual, vlComissao, percentualComissao].forEach(el => {
                        el.addClass('input-destaque');                        
                    });

                    // 👉 Aplica foco visual (danger) ao input editado
                    input.addClass('is-valid');


                });
            }, 1000); // 1000ms de debounce

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
                        // width: '2%',
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

            if (data[0].ST_COMERCIAL == 'G') {
                $('.quote-danger').removeClass('d-none');
            } else {
                $('.quote-danger').addClass('d-none');
            }
            data.forEach(item => {
                const card = $(`                       
                            <div class="card-body shadow-sm p-3">
                                <input type="hidden" class="input-id-item" value="${item.ID}" />
                                <input type="hidden" class="input-empresa" value="${item.EMPRESA}" />
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
                                            <label>% Desc</label> 
                                            <input type="text" 
                                                class="form-control form-control-sm percentual"
                                                value="${parseFloat(item.PC_DESCONTO).toFixed(2)}" readonly />                                    
                                        </div>
                                    </div>  
                                
                                    <div class="col-8 col-md-8">
                                        <div class="form-group mb-0">    
                                            <label>Venda</label>                                
                                            <input type="number"
                                                class="form-control form-control-sm input-venda"
                                                style="width: 100px;"
                                                value="${parseFloat(item.VL_VENDA).toFixed(2)}"                                               
                                                data-id="${item.ID}" 
                                                data-pccomissao="${item.PC_COMISSAO}"
                                                />
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <div class="form-group mb-0">
                                            <label>Tabela</label>
                                            <input type="number"
                                                class="form-control form-control-sm input-preco"                                                
                                                value="${parseFloat(item.VL_PRECO).toFixed(2)}" readonly/>
                                        </div>  
                                    </div>  
                                    <div class="col-8 col-md-8">
                                    <div class="form-group mb-0">
                                        <label>Comissão</label>
                                        <input type="number"
                                            class="form-control form-control-sm vl-comissao"
                                            style="width: 100px;"
                                            value="${parseFloat(item.VL_COMISSAO).toFixed(2)}" readonly/>
                                    </div>
                                    </div>
                                    <div class="col-4 col-md-4">
                                        <div class="form-group mb-0">
                                            <label>% Comissao</label>
                                            <input type="number"
                                            class="form-control form-control-sm percentual-comissao"                                            
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
