@extends('layouts.master')
@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="content-fluid">
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
                            <input type="text" class="form-control" id="nm_supervisor" placeholder="Nome Supervisor">
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
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-secondary btn-sm float-right mr-2"
                            id="btn-limpar">Limpar</button>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pedidos Bloqueados</h3>
                <button class="btn btn-primary btn-xs float-right" id="btn-liberar">Liberar Abaixo
                    {{ intval($percentual[0]->perc_desconto_max) }}%</button>

            </div>
            <div class="card-body">
                <span class="badge bg-warning">Coordenador</span>
                <span class="badge bg-secondary mr-2">Supervisor</span>
                @hasrole('admin|gerente comercial')
                    <a class="btn btn-info btn-xs float-right mr-2" href="{{ route('tabela-preco.index') }}">Tabela Preço</a>
                @endhasrole

                <table class="table table-responsive compact table-font-small" id="table-ordem-block">
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Itens --}}
    <div class="modal modal-default fade" id="modal-table-pedido">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <blockquote class="quote-danger d-none" style="margin: 0">
                        <small class="form-text text-muted">Apenas o Coordenador. Edição
                            permitida.</small>
                    </blockquote>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
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
                            <div class="col-6 col-md-6">
                                <div class="form-group">
                                    <label for="vendedor">Vendedor</label>
                                    <input id="" class="form-control form-control-sm vendedor" type="text"
                                        readonly>

                                </div>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="form-group">
                                    <label for="condicao">Condição</label>
                                    <input id="" class="form-control form-control-sm condicao" type="text"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2" class="d-none" id="card-pedido">
                        <div class="card-header">
                            <h3 class="card-title">Itens</h3>
                            <div class="card-tools">
                                <button id="btn-observacao" class="btn btn-secondary btn-xs" data-toggle="tooltip"
                                    title="">Observação</button>
                            </div>
                        </div>
                        <div id="card-container"></div>
                    </div>
                    <table class="table compact row-border" id="table-item-pedido" style="font-size:12px">
                    </table>
                    <div class="modal-footer justify-content-center">
                        <div class="col-md-12">
                            <div class="form-group" style="text-align: left">
                                <label for="liberacao">Motivo Liberação:</label>
                                <textarea id="" class="form-control liberacao" rows="3" cols="50"></textarea>
                            </div>
                        </div>
                        <div class="d-flex">
                            <button type="button" class="btn btn-alert" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success btn-save-confirm">Liberar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-btn-topo-modal :modalId="'modal-table-pedido'" />



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

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 700px;
                margin: 1.75rem auto;
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
            processing: false,
            serverSide: false,
            pagingType: "simple",
            pageLength: 50,
            // scrollY: "400px",
            // scrollX: true,
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            },
            ajax: "{{ route('get-ordens-bloqueadas-comercial') }}",
            columns: [{
                    data: "actions",
                    name: "actions",
                    "width": "3%",
                    title: "Emp",
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
                    title: 'Pedido',
                    className: 'text-center',
                },
                {
                    data: 'PESSOA',
                    name: 'PESSOA',
                    title: 'Cliente'
                },
                {
                    data: 'QTDPNEUS',
                    name: 'QTDPNEUS',
                    title: 'Pneus'
                },
                {
                    data: 'VENDEDOR',
                    name: 'VENDEDOR',
                    title: 'Vendedor'
                },
                {
                    data: 'TABPRECO',
                    name: 'TABPRECO',
                    title: 'Tabela'
                },
                {
                    data: 'NM_SUPERVISOR',
                    name: 'NM_SUPERVISOR',
                    title: 'Supervisor'
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
            $('.condicao').val(row.data().DS_CONDPAGTO);

            $('#btn-observacao')
                .attr('data-original-title', '') // limpa qualquer title antigo
                .tooltip('dispose') // destrói tooltip existente
                .attr('title', row.data().DSOBSFATURAMENTO) // define novo texto
                .tooltip(); // recria tooltip

            $('#modal-table-pedido').modal('show');


            initTable('table-item-pedido', row.data());
        });

        $(document).on('click', '#table-item-pedido td:nth-child(3)', function() {
            var tr = $(this).closest('tr');
            var row = table_item_pedido.row(tr);
            var rowData = row.data();
            var valorCellVenda = tr.find('td').eq(2);
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
                            if (data === 0) {
                                msgToastr(
                                    'Não foi possivel efetuar o cálculo de comissão. Somente Borracheiro no pedido!',
                                    'warning');
                                return;
                            }

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
                            rowData.ST_CALCULO = 'A'; // Automático
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

        $(document).on('click', '#table-item-pedido td:nth-child(7)', function() {
            var tr = $(this).closest('tr');
            var row = table_item_pedido.row(tr);
            var rowData = row.data();
            var valorCellPcComissao = tr.find('td').eq(6);
            var valorVenda = parseFloat(rowData.VL_VENDA).toFixed(2);
            const valorPcComissao = parseFloat(rowData.PC_COMISSAO).toFixed(2);

            console.log(valorCellPcComissao);

            if (!valorCellPcComissao.find('input').length) {
                valorCellPcComissao.html(
                    `<input type="number" value="${valorPcComissao}" class="edit-input" style="width: 100%; box-sizing: border-box;"/>`
                );

                var input = valorCellPcComissao.find('input');
                input.focus();
                input.select();

                input.on('blur', function(e) {

                    const pc_comissao = parseFloat($(this).val()) || 0;
                    const vl_comissao = (valorVenda * pc_comissao) / 100;

                    rowData.PC_COMISSAO = parseFloat(pc_comissao).toFixed(2);
                    rowData.VL_COMISSAO = parseFloat(vl_comissao).toFixed(2);

                    if (valorPcComissao == pc_comissao) {
                        rowData.ST_CALCULO =
                        'A'; // compara com a pc original, se não mudou continua aumático
                    } else {
                        rowData.ST_CALCULO = 'M'; // Manual
                    }

                    row.data(rowData).draw();

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
                    const st_calculo = card.find('.st-calculo').val();

                    pneus.push({
                        ID: $(this).data('id'),
                        VL_VENDA: vl_venda,
                        VL_PRECO: vl_preco,
                        VL_COMISSAO: vl_comissao,
                        PC_DESCONTO: desconto,
                        PC_COMISSAO: pc_comissao,
                        ST_CALCULO: st_calculo
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

                    if (data === 0) {
                        msgToastr(
                            'Não foi possivel efetuar o cálculo de comissão. Somente Borracheiro no pedido!',
                            'warning');
                        return;
                    }

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

        $('#card-container').on('input', '.percentual-comissao', function() {
            const input = $(this);

            const venda = input.closest('.card-body').find('.input-venda').val() || 0;
            const valorPcComissao = input.closest('.card-body').find('.percentual-comissao').val() || 0;
            const vlComissao = input.closest('.card-body').find('.vl-comissao');


            // Calcula o valor da comissão com base na venda e no percentual
            vlComissao.val(parseFloat((venda * valorPcComissao) / 100).toFixed(2));

            // Aplica destaque
            [vlComissao].forEach(el => {
                el.addClass('input-destaque');
            });

            // Aplica foco visual (danger) ao input editado
            input.addClass('is-valid');


        });

        $('#nm_supervisor').on('keyup change', function() {
            let value = $(this).val();
            table.column(7).search(value).draw();
        });

        $('#nm_vendedor').on('keyup change', function() {
            let value = $(this).val();
            table.column(5).search(value).draw();
        });

        $('#nm_cliente').on('keyup change', function() {
            let value = $(this).val();
            table.column(3).search(value).draw();
        });

        $('#btn-limpar').on('click', function() {
            $('#nm_supervisor').val('');
            $('#nm_vendedor').val('');
            $('#nm_cliente').val('');
            table.search('').columns().search('').draw();
        });

        $('#btn-liberar').on('click', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('libera-abaixo-desconto') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        msgToastr(response.success, 'success');
                        $('#table-ordem-block').DataTable().ajax.reload();
                    }
                }
            });
        });

        function initTable(tableId, data) {

            var url = "{{ route('get-pneus-ordens-bloqueadas-comercial', ':pedido') }}";
            url = url.replace(':pedido', data.PEDIDO);

            $('#' + tableId).DataTable().destroy();

            table_item_pedido = $('#' + tableId).DataTable({
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 50,
                scrollY: "400px",
                scrollCollapse: true,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
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
                        data: 'CD_TABPRECO',
                        name: 'CD_TABPRECO',
                        title: 'Tabela'
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
                        title: '%Comis.'
                    },
                    {
                        data: 'ST_CALCULO',
                        name: 'ST_CALCULO',
                        title: 'Cálculo',
                        render: function(data, type, row) {
                            if (data === 'A') {
                                return 'Automático';
                            } else if (data === 'M') {
                                return 'Manual';
                            } else {
                                return data;
                            }
                        }
                    }, {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        visible: false,
                        title: 'Pedido'
                    }
                ],
                columnDefs: [{
                    targets: [2, 3, 8],
                    className: 'dt-right'
                }, {
                    targets: [3, 7],
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).css('background-color', '#E2E5E8');
                        $(td).css('color', '#000');
                        $(td).css('font-weight', 'bold');
                    }
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
                            <div class="card-body shadow-sm p-3 ${item.ST_CALCULO == 'M' ? 'bg-purple' : ''} ">
                                <span class="badge badge-secondary">${item.DS_TABPRECO}</span>
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
                                            style="width: 100px;"
                                            value="${parseFloat(item.PC_COMISSAO).toFixed(2)}"/>
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
