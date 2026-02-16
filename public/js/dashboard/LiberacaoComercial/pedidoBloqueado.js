var tableId = 0;
var table_item_pedido;
let debounceTimer; // armazena o timer de debounce

var table = $('#table-ordem-block').DataTable({
    processing: false,
    serverSide: false,
    pagingType: "simple",
    pageLength: 50,
    // scrollY: "400px",
    // scrollX: true,
    language: {
        url: window.routes.languageDatatables,
    },
    ajax: window.routes.ordensBloqueadas,
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

                $.get(window.routes.calculaComissao, {
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
            const pedido = card.find('.input-id-pedido').val();

            pneus.push({
                ID: $(this).data('id'),
                PEDIDO: pedido,
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
        url: window.routes.savePedidosLiberadas,
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

$('#card-container').on('input', '.input-venda', function() {
    const input = $(this);

    clearTimeout(debounceTimer); // Limpa o timer anterior, se houver

    debounceTimer = setTimeout(() => {
        const venda = parseFloat(input.val()) || 0;
        const item_pedido = input.closest('.card-body').find('.input-id-item').val() || 0;
        const preco = input.closest('.card-body').find('.input-preco').val() || 0;

        $.get(window.routes.calculaComissao, {
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
        url: window.routes.liberaAbaixoDesconto,
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.message) {
                Swal.fire({
                    icon: 'success',                    
                    text: response.message,
                    showConfirmButton: true,                    
                });    
                
                $('#table-ordem-block').DataTable().ajax.reload();
            }
        }
    });
});

function initTable(tableId, data) {

    var url = window.routes.itensPneusOrdensBloqueadas;
    url = url.replace(':pedido', data.PEDIDO);

    $('#' + tableId).DataTable().clear().destroy();

    table_item_pedido = $('#' + tableId).DataTable({
        processing: false,
        serverSide: false,
        pagingType: "simple",
        pageLength: 50,
        scrollY: "400px",
        scrollCollapse: true,
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: url,
            beforeSend: function() {
                $('#card-container').html(`
                    <div class="card-body shadow-sm p-3 text-center">
                        <i class="fas fa-sync-alt fa-spin"></i>
                    </div>`);
            },
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
                        <input type="hidden" class="st-calculo" value="${item.ST_CALCULO}" />
                        <input type="hidden" class="input-id-pedido" value="${item.PEDIDO}" />
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
  
