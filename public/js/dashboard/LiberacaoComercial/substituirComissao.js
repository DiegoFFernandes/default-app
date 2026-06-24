var tableSubstituirComissao;

$(document).on("click", "#tab-substituir-comissao", function () {
    initTableSubstituirComissao();
});

$(document).on("click", "#btn-substituir-comissao", function () {
    var pedidosSelecionados = getComissaoSelecionados();

    if (pedidosSelecionados.length === 0) {
        Swal.fire({
            title: "Nenhum pedido selecionado",
            text: "Por favor, selecione pelo menos um pedido para substituir a comissão automática.",
            icon: "warning",
        });
        return;
    }

    $.ajax({
        url: window.routes.saveSubstituiComissaoAutomatica,
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            pedidos: pedidosSelecionados,
        },
        beforeSend: function () {
            $(".loading-card").removeClass("invisible");
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    title: response.message,
                    html: `                    
                    <p><strong>Pedidos:</strong></br>
                    ${response.pedidos_atualizados.join("<br>")}
                    </p>
                    `,
                    icon: "success",
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-success btn-sm",
                    },
                });
                $(".loading-card").addClass("invisible");
                tableSubstituirComissao.ajax.reload();
            } else {
                Swal.fire({
                    title: "Erro",
                    text: response.message,
                    icon: "error",
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-danger btn-sm",
                    },
                });
            }
            $(".loading-card").addClass("invisible");
        },
    });
});

$(document).on("click", "#btn-manter-comissao", function () {
    var pedidosSelecionados = getComissaoSelecionados();

    if (pedidosSelecionados.length === 0) {
        Swal.fire({
            title: "Nenhum pedido selecionado",
            text: "Por favor, selecione pelo menos um pedido para manter a comissão automática.",
            icon: "warning",
        });
        return;
    }

    $.ajax({
        url: window.routes.saveManterComissaoAutomatica,
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            pedidos: pedidosSelecionados,
        },
        beforeSend: function () {
            $(".loading-card").removeClass("invisible");
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    title: response.message,
                    html: `                    
                    <p><strong>Pedidos:</strong></br>
                    ${response.pedidos_atualizados.join("<br>")}
                    </p>
                    `,
                    icon: "success",
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-success btn-sm",
                    },
                });
                $(".loading-card").addClass("invisible");
                tableSubstituirComissao.ajax.reload();
            } else {
                Swal.fire({
                    title: "Erro",
                    text: response.message,
                    icon: "error",
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-danger btn-sm",
                    },
                });
            }
            $(".loading-card").addClass("invisible");
        },
    });
});

function updateComissaoBadge() {
    if (!tableSubstituirComissao) return;
    var count = $(tableSubstituirComissao.table().container()).find('.dt-row-checkbox-comissao').filter(function() { return this.checked; }).length;
    var $badge = $('.comissao-count-badge');
    if (count > 0) {
        $badge.text(count + ' selecionado' + (count > 1 ? 's' : '')).show();
    } else {
        $badge.hide();
    }
}

function getComissaoSelecionados() {
    var lancamentos = [];
    $(tableSubstituirComissao.table().container()).find('.dt-row-checkbox-comissao').filter(function() {
        return this.checked;
    }).each(function() {
        lancamentos.push(String($(this).data('lancamento')));
    });
    return tableSubstituirComissao.rows().data().toArray().filter(function(row) {
        return lancamentos.includes(String(row.NR_LANCAMENTO));
    });
}

// Select all
$(document).on('click', '.dt-select-all-comissao', function(e) {
    e.stopPropagation();
    var checked = this.checked;
    if (!tableSubstituirComissao) return;
    tableSubstituirComissao.rows().nodes().to$().find('.dt-row-checkbox-comissao').prop('checked', checked);
    updateComissaoBadge();
});

// Checkbox individual
$(document).on('click', '.dt-row-checkbox-comissao', function(e) {
    e.stopPropagation();
    if (!tableSubstituirComissao) return;
    var total = tableSubstituirComissao.rows().count();
    var checkedCount = $(tableSubstituirComissao.table().container()).find('.dt-row-checkbox-comissao').filter(function() { return this.checked; }).length;
    $('.dt-select-all-comissao').prop('checked', total > 0 && checkedCount === total);
    updateComissaoBadge();
});

function initTableSubstituirComissao() {
    // if (!$.fn.DataTable.isDataTable("#table-substituir-comissao")) {
    $("#table-substituir-comissao").DataTable().clear().destroy();
    // }

    tableSubstituirComissao = $("#table-substituir-comissao").DataTable({
        processing: false,
        serverSide: false,
        pagingType: "simple",
        pageLength: 50,
        scrollY: "400px",
        // scrollX: true,
        scrollCollapse: true,
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.pedidosComissaoAutomatica,
        },
        columns: [
            {
                data: null,
                width: "30px",
                orderable: false,
                searchable: false,
                className: "text-center",
                title: '<input type="checkbox" class="dt-select-all-comissao" title="Selecionar todos" style="margin:0;">',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<input type="checkbox" class="dt-row-checkbox-comissao" data-lancamento="' + row.NR_LANCAMENTO + '" aria-label="Selecionar linha" style="margin:0;">';
                    }
                    return '';
                },
            },
            {
                data: "CD_EMPRESA",
                name: "CD_EMPRESA",
                title: "Emp.",
                width: "1%",
            },
            {
                data: "NR_PEDIDO",
                name: "NR_PEDIDO",
                title: "Pedido",
                width: "2%",
            },
            {
                data: "NR_LANCAMENTO",
                name: "NR_LANCAMENTO",
                title: "Lançamento",
                width: "2%",
                className: "text-center",
            },
            { data: "NM_PESSOA", name: "NM_PESSOA", title: "Cliente" },
            { data: "DS_ITEM", name: "DS_ITEM", title: "Item" },
            { data: "NM_VENDEDOR", name: "NM_VENDEDOR", title: "Vendedor" },
            {
                data: "QTD_PNEUS",
                name: "QTD_PNEUS",
                title: "Qtd",
                className: "text-center",
                width: "1%",
                createdCell: function (td) {
                    $(td).css({
                        "background-color": "#5cf5f5",
                        color: "#000000",
                        "font-weight": "bold",
                    });
                },
            },
            {
                data: "VL_AUTOMATICO",
                name: "VL_AUTOMATICO",
                title: "R$ Autom.",
                createdCell: function (td) {
                    $(td).css({
                        "background-color": "#FFFFCC",
                        color: "#000000",
                        "font-weight": "bold",
                    });
                },
            },
            {
                data: "VL_MANUAL",
                name: "VL_MANUAL",
                title: "R$ Manual",
                createdCell: function (td) {
                    $(td).css({
                        "background-color": "#CCFFCC",
                        color: "#000000",
                        "font-weight": "bold",
                    });
                },
            },
        ],
    });
}
