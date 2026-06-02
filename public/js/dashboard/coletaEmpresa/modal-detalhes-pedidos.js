$(document).on("click", ".btn-show-modal", function (e) {
    e.preventDefault();

    $("#item-pedido").DataTable().destroy();
    $("#modal-detalhes-pedido").modal("show");
    const dt_sinc = formatDate($(this).data("dt_sincronizacao"));
    const dt_registro_palm = formatDate($(this).data("dt_registro_palm"));

    $("#badge-num-pedido").text("Pedido:" + $(this).data("pedido"));
    $("#badge-dt-sinc").text("Sinc: " + dt_sinc);
    $("#badge-dt-registro-palm").text("Reg. Palm: " + dt_registro_palm);

    let ds_motivo = $(this).data("ds_motivo").trim();

    if (ds_motivo === "LIBERADO") {
        $("#badge-ds-motivo")
            .text(ds_motivo)
            .removeClass("badge-warning")
            .addClass("badge-success");
        $(".form-group-bloqueio").addClass("d-none");
    } else {
        $("#badge-ds-motivo")
            .text(ds_motivo)
            .removeClass("badge-success")
            .addClass("badge-warning");
        $(".form-group-bloqueio").removeClass("d-none");
        $("#dsBloqueioDetails").val($(this).data("ds_bloqueio"));
    }

    $("#dsEmpresa").val($(this).data("empresa"));
    $("#nomePessoa").val($(this).data("nm_pessoa"));
    $("#nomeVendedor").val($(this).data("nm_vendedor"));
    $("#condicaoDetails").val($(this).data("cond_pagamento"));
    $("#formaDetails").val($(this).data("forma_pagamento"));
    $("#observacaoDetails").val($(this).data("observacao"));

    $("#pedidoPalm").val($(this).data("pedido_palm"));
    $("#pedidoColeta").val($(this).data("pedido"));
    $("#dtEmissao").val($(this).data("dt_emissao"));
    $("#dtEntrega").val($(this).data("dt_entrega"));

    initTableItemPedido("item-pedido", {
        ID: $(this).data("pedido"),
    });

    const ds_liberacao_anterior = $(this).data("ds_liberacao_anterior");

    $("#badge-ds-liberacao-anterior").click(function () {
        swal.fire({
            text: ds_liberacao_anterior
                ? ds_liberacao_anterior
                : "Nenhuma liberação anterior registrada.",
            icon: "info",
            buttons: {
                confirm: {
                    text: "Fechar",
                },
            },
        });
    });
});

function initTableItemPedido(tableId, data) {
    console.log(data);
    return $("#" + tableId).DataTable({
        language: {
            url: window.routes.languageDatatables,
        },
        searching: false,
        paging: false,
        sDom: "t",
        processing: false,
        serverSide: false,
        scrollX: true,
        autoWidth: false,
        ajax: {
            method: "GET",
            url: window.routes.getItemPedidoAcompanhar,
            data: {
                _token: $("[name=csrf-token]").attr("content"),
                id: data.ID,
            },
        },
        columns: [
            {
                data: "NRSEQUENCIA",
                name: "NRSEQUENCIA",
                title: "Sq",
                className: "col-actions",
            },
            {
                data: "NRORDEM",
                name: "NRORDEM",
                title: "Ordem",
                width: "60px",
            },
            {
                data: "DSSERVICO",
                name: "DSSERVICO",
                title: "Serviço",
            },
            {
                data: "VLUNITARIO",
                name: "VLUNITARIO",
                title: "Valor",
            },
            {
                data: "STORDEM",
                name: "STORDEM",
                title: "Status",
            },
            {
                data: "DTINICIO",
                name: "DTINICIO",
                title: "Impressão",
                render: function (data, type, row) {
                    if (!data) return "";
                    return moment(data).format("DD/MM/YYYY HH:mm");
                },
            },
        ],
        columnDefs: [
            {
                targets: [3],
                className: "dt-right",
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
        ],
    });
}

function initTableItemDetalhesPedido(tableId, data) {
    console.log("Data for AJAX:", data); // Log the data being sent to the server
    $("#" + tableId).DataTable({
        language: {
            url: window.routes.languageDatatables,
        },
        searching: false,
        paging: false,
        bInfo: false,
        processing: false,
        serverSide: false,
        autoWidth: false,
        sDom: "t",
        ajax: data.details_item_pedido_url,
        columns: [
            {
                data: "O_DS_ETAPA",
                name: "O_DS_ETAPA",
            },
            {
                data: "O_NM_USUARIO",
                name: "O_NM_USUARIO",
                // "width": "15%"
            },
            {
                data: "DT_ENTRADA",
                name: "DT_ENTRADA",
                render: function (data, type, row) {
                    return moment(data).format("DD/MM/YYYY HH:mm");
                },
            },
            {
                data: "DT_SAIDA",
                name: "DT_SAIDA",
                render: function (data, type, row) {
                    return moment(data).format("DD/MM/YYYY HH:mm");
                },
            },
            {
                data: "O_DS_COMPLEMENTOETAPA",
                name: "O_DS_COMPLEMENTOETAPA",
            },
            {
                data: "O_ST_RETRABALHO",
                name: "O_ST_RETRABALHO",
                // width: "2%",
                visible: false,
            },
        ],
        columnDefs: [
            {
                targets: [1],
                className: "text-truncate",
            },
        ],
        order: [[10, "asc"]],
    });
}
