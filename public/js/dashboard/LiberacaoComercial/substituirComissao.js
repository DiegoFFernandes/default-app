var tableSubstituirComissao;

$(document).on("click", "#tab-substituir-comissao", function () {
    initTableSubstituirComissao();
});

$(document).on("click", "#btn-substituir-comissao", function () {
    var pedidosSelecionados = tableSubstituirComissao
        .rows({ selected: true })
        .data()
        .toArray();

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
            $('.loading-card').removeClass('invisible');
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
                    customClass:{
                        confirmButton: 'btn btn-success btn-sm'
                    }
                });
                $('.loading-card').addClass('invisible');
                tableSubstituirComissao.ajax.reload();
            } else {
                Swal.fire({
                    title: "Erro",
                    text: response.message,
                    icon: "error",
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass:{
                        confirmButton: 'btn btn-danger btn-sm'
                    }
                });
            }
            $('.loading-card').addClass('invisible');
        },
    });
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
        select: {
            style: "multi",
        },
        ajax: {
            url: window.routes.pedidosComissaoAutomatica,
        },
        columns: [
            {
                data: null,
                width: "1%",
                render: DataTable.render.select(),
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
            { data: "NM_PESSOA", name: "NM_PESSOA", title: "Cliente" },
            { data: "DS_ITEM", name: "DS_ITEM", title: "Item" },
            { data: "NM_VENDEDOR", name: "NM_VENDEDOR", title: "Vendedor" },
            {
                data: "QTD_PNEUS",
                name: "QTD_PNEUS",
                title: "Qtd",
                className: "text-center",
                width: "1%",
            },
            {
                data: "VL_AUTOMATICO",
                name: "VL_AUTOMATICO",
                title: "R$ Autom.",
            },
            { data: "VL_MANUAL", name: "VL_MANUAL", title: "R$ Manual" },
        ],
        columnDefs: [
            {
                targets: [6],
                createdCell: function (td) {
                    $(td).css({
                        "background-color": "#5cf5f5",
                        color: "#000000",
                        "font-weight": "bold",
                    });
                },
            },
            {
                targets: [7],
                createdCell: function (td) {
                    $(td).css({
                        "background-color": "#FFFFCC",
                        color: "#000000",
                        "font-weight": "bold",
                    });
                },
            },
            {
                targets: [7],
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
