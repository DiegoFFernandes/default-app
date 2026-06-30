$(document).on("click", "#tab-item-faltante", function () {
    initTableItemFaltanteTabelaPreco(window.routes);
});

function initTableItemFaltanteTabelaPreco(routes) {
    if ($.fn.DataTable.isDataTable("#tabela-item-faltante")) {
        $("#tabela-item-faltante").DataTable().clear().destroy();
    }

    $("#tabela-item-faltante").DataTable({
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        pageLength: 50,
        scrollY: "400px",
        scrollCollapse: true,
        order: [[3, "asc"]],
        layout: {
            topStart: ["pageLength", { buttons: ["copy", "csv", "excel"] }],
            topEnd: ["search"],
        },
        language: {
            url: routes.language_datatables,
        },
        ajax: {
            url: routes.itemFaltanteTabelaPreco,
            type: "GET",
            beforeSend: function () {
                Swal.fire({
                    title: "Carregando...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            complete: function () {
                Swal.close();
            },
        },
        drawCallback: function () {
            var api        = this.api();
            var rows       = api.rows({ page: "current" }).nodes();
            var lastPessoa = null;
            var grupoAtual = false;

            api.rows({ page: "current" }).data().each(function (data, i) {
                var row = $(rows[i]);
                row.removeClass("grupo-a grupo-b grupo-inicio");

                if (data.NM_PESSOA !== lastPessoa) {
                    row.addClass("grupo-inicio");
                    grupoAtual = !grupoAtual;
                }

                row.addClass(grupoAtual ? "grupo-a" : "grupo-b");
                lastPessoa = data.NM_PESSOA;
            });

            atualizarInfoSelecionados();
        },
        columns: [
            {
                data: null,
                name: "checkbox",
                title: '<input type="checkbox" id="check-all-item-faltante">',
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return '<input type="checkbox" class="check-item-faltante"' +
                        ' data-cd_tabpreco="' + row.CD_TABPRECO + '"' +
                        ' data-cd_item="' + row.CD_ITEM + '"' +
                        ' data-nm_item="' + $("<div>").text(row.DS_ITEM).html() + '"' +
                        ' data-vl_unitario="' + row.VL_UNITARIO + '">';
                },
            },
            {
                data: "action",
                name: "action",
                title: "#",
                orderable: false,
                searchable: false,
            },
            {
                data: "CD_PESSOA",
                name: "CD_PESSOA",
                title: "Cód. Cliente",
                className: "text-center",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "CD_TABPRECO",
                name: "CD_TABPRECO",
                title: "Tabela Vinculada",
                className: "text-center",
            },
            {
                data: "TABELA_FATURADA",
                name: "TABELA_FATURADA",
                title: "Tabela Faturada",
                className: "text-center",
            },
            {
                data: "CD_ITEM",
                name: "CD_ITEM",
                title: "Cód. Item",
                className: "text-center",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Item",
            },
            {
                data: "VL_UNITARIO",
                name: "VL_UNITARIO",
                title: "Vl. Unitário",
                className: "text-right",
                render: function (data) {
                    if (data == null) return "-";
                    return parseFloat(data).toLocaleString("pt-BR", {
                        style: "currency",
                        currency: "BRL",
                    });
                },
            },
        ],
    });
}

// Select all
$(document).on("change", "#check-all-item-faltante", function () {
    var checked = $(this).is(":checked");
    $("#tabela-item-faltante tbody .check-item-faltante").prop("checked", checked);
    atualizarInfoSelecionados();
});

// Individual checkbox
$(document).on("change", "#tabela-item-faltante tbody .check-item-faltante", function () {
    var total    = $("#tabela-item-faltante tbody .check-item-faltante").length;
    var marcados = $("#tabela-item-faltante tbody .check-item-faltante:checked").length;
    $("#check-all-item-faltante").prop("indeterminate", marcados > 0 && marcados < total);
    $("#check-all-item-faltante").prop("checked", marcados === total);
    atualizarInfoSelecionados();
});

function atualizarInfoSelecionados() {
    var marcados = $("#tabela-item-faltante tbody .check-item-faltante:checked").length;
    var info = marcados > 0
        ? '<span class="badge badge-warning">' + marcados + ' item(ns) selecionado(s)</span>'
        : '<span class="badge badge-secondary">Nenhum item selecionado</span>';
    $("#info-selecionados-item-faltante").html(info);
}

// Adicionar todos selecionados
$(document).on("click", "#btn-adicionar-todos-item-faltante", function () {
    var selecionados = [];

    $("#tabela-item-faltante tbody .check-item-faltante:checked").each(function () {
        selecionados.push({
            cd_tabpreco : $(this).data("cd_tabpreco"),
            cd_item     : $(this).data("cd_item"),
            nm_item     : $(this).data("nm_item"),
            vl_unitario : $(this).data("vl_unitario"),
        });
    });

    if (selecionados.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Selecione pelo menos um item antes de continuar.",
            customClass: { confirmButton: "btn btn-warning" },
        });
        return;
    }

    // Lógica de inserção em ITEMTABPRECO a ser implementada
    console.log("Adicionar selecionados:", selecionados);
});

// Botão Alterar Valor — abre modal com dados da linha
var _rowAlterarValor = null;

$(document).on("click", ".btn-alterar-valor-item-faltante", function () {
    _rowAlterarValor = $(this).closest("tr");

    $("#modal-alterar-nm-pessoa").val($(this).data("nm_pessoa"));
    $("#modal-alterar-ds-item").val($(this).data("ds_item"));
    $("#modal-alterar-vl-unitario").val(parseFloat($(this).data("vl_unitario")).toFixed(2));

    $("#modal-alterar-valor-item-faltante").modal("show");
});

// Confirmar alteração — atualiza valor na linha do DataTable
$(document).on("click", "#btn-confirmar-alterar-valor", function () {
    var novoValor = parseFloat($("#modal-alterar-vl-unitario").val());

    if (isNaN(novoValor) || novoValor < 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Informe um valor válido.",
            customClass: { confirmButton: "btn btn-warning" },
        });
        return;
    }

    var table = $("#tabela-item-faltante").DataTable();
    var rowData = table.row(_rowAlterarValor).data();
    rowData.VL_UNITARIO = novoValor;
    table.row(_rowAlterarValor).data(rowData).draw(false);

    $("#modal-alterar-valor-item-faltante").modal("hide");
    _rowAlterarValor = null;
});

$(document).on("click", "#painel-item-faltante .btn-tool-export", function () {
    var tipo = $(this).data("export");
    $("#tabela-item-faltante_wrapper .buttons-" + tipo).trigger("click");
});
