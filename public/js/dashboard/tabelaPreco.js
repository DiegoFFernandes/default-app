const chkNaoAssociados = document.getElementById("checkNaoAssociadas");
const chkAssociados = document.getElementById("checkAssociadas");

$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    const valor = parseInt(data[3]) || 0; // Use data for the "Associados" column

    const naoAssociados = chkNaoAssociados && chkNaoAssociados.checked;
    const associados = chkAssociados && chkAssociados.checked;

    // Ambos marcados mostra tudo
    if (naoAssociados && associados) {
        return true;
    }
    // Só não associados
    if (naoAssociados) {
        return valor === 0;
    }
    // Só associados
    if (associados) {
        return valor > 0;
    }
    return true;
});

if (chkNaoAssociados) {
    chkNaoAssociados.addEventListener("change", function () {
        tabelaPreco.draw();
    });
}
if (chkAssociados) {
    chkAssociados.addEventListener("change", function () {
        tabelaPreco.draw();
    });
}

function initTabelaPreco(route) {
    if ($.fn.DataTable.isDataTable("#tabela-preco")) {
        $("#tabela-preco").DataTable().clear().destroy();
    }
    tabelaPreco = $("#tabela-preco").DataTable({
        processing: false,
        serverSide: false,
        pagingType: "simple",
        pageLength: 50,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.tabelaPreco,
            type: "GET",
        },
        columns: [
            {
                data: "clientes_associados",
                name: "clientes_associados",
                title: "Cód",
                className: "text-center",
            },
            {
                data: "DS_TABPRECO",
                name: "DS_TABPRECO",
                title: "Descrição",
            },
            {
                data: "QTD_ITENS",
                name: "QTD_ITENS",
                title: "Itens",
            },
            {
                data: "ASSOCIADOS",
                name: "ASSOCIADOS",
                title: "Clientes",
            },
            {
                data: "action",
                name: "action",
                title: "Ações",
                className: "text-center",
            },
        ],
    });

    return tabelaPreco;
}

function initTableClienteTabela(tableId, data, route) {
    itemTabelaCliente = $("#" + tableId).DataTable({
        searching: false,
        paging: false,
        sDom: "t",
        processing: false,
        serverSide: false,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.itemTabelaPrecoCliente,
            type: "GET",
            data: {
                cd_tabela: data.CD_TABPRECO,
            },
        },
        columns: [
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "VENDEDOR",
                name: "VENDEDOR",
                title: "Vendedor",
            },
            {
                data: "SUPERVISOR",
                name: "SUPERVISOR",
                title: "Supervisor",
            },
        ],
    });
    return itemTabelaCliente;
}

function initTableItemTabelaPreco(route, idTabela) {
    $("#modal-item-tab-preco").modal("show");
    if ($.fn.DataTable.isDataTable("#table-item-tab-preco")) {
        $("#table-item-tab-preco").DataTable().clear().destroy();
    }
    $("#table-item-tab-preco").DataTable({
        processing: false,
        serverSide: false,
        pagingType: "simple",
        pageLength: 50,
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: "excelHtml5",
                        title: $(".title-nm-tabela").html(),
                    },
                    {
                        extend: "print",
                        title: $(".title-nm-tabela").text(),
                        customize: function (win) {
                            $(win.document.body)
                                .find("h1")
                                .css("font-size", "12pt")
                                .css("color", "#333");
                        },
                    },
                ],
            },
        },
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.itemtabelaPreco,
            type: "GET",
            data: {
                cd_tabela: idTabela,
            },
        },
        columns: [
            {
                data: "CD_TABPRECO",
                name: "CD_TABPRECO",
                title: "Cód Tabela",
                visible: false,
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                width: "80%",
                title: "Descrição",
            },
            {
                data: "VL_PRECO",
                name: "VL_PRECO",
                title: "Valor",
            },
        ],
    });
}

function formatarNome(str) {
    return str.toLowerCase().replace(/\b\w/g, function (letra) {
        return letra.toUpperCase();
    });
}

function formularioDinamico() {
    const cardTabela = $("#item-tabela-preco").closest(".card");

    $("#desenho, #medida, #valor, #btn-associar").closest(".form-group").hide(); // esconde os select
    cardTabela.hide(); // esconde o card da tabela

    // exibe desenho quando pessoa for selecionado
    $("#pessoa").on("change", function () {
        if ($(this).val() && $(this).val().length > 0) {
            $("#desenho").closest(".form-group").show();
        } else {
            $("#desenho, #medida, #valor, #btn-associar")
                .closest(".form-group")
                .hide();
        }
    });

    // exibe medida quando desenho for selecionado
    $("#desenho").on("change", function () {
        if ($(this).val() && $(this).val().length > 0) {
            $("#medida").closest(".form-group").show();
        } else {
            $("#medida, #valor").closest(".form-group").hide();
        }
    });

    // exibe valor quando medida for selecionado
    $("#medida").on("change", function () {
        if ($(this).val() && $(this).val().length > 0) {
            $("#valor").closest(".form-group").show();
            $("#btn-associar").closest(".form-group").show();
        } else {
            $("#valor").closest(".form-group").hide();
            $("#btn-associar").closest(".form-group").hide();
        }
    });

    // exibe a tabela
    $("#btn-associar").on("click", function () {
        const nomeTabela = $("#pessoa option:selected").text();
        $(".card-title").html(
            "<span class='badge bg-gray-dark'>Prévia Tabela - " +
                formatarNome(nomeTabela) +
                "</span>"
        );
        cardTabela.show();
    });

    // limpa e esconde
    cardTabela.find("#btn-recomecar").on("click", function () {
        $("#item-tabela-preco").DataTable().clear().destroy();
        dados_atualizados = [];
        itens_preview = new Map();
        $("#pessoa, #desenho, #medida, #valor").val("").trigger("change"); // limpa os inputs
        cardTabela.hide();
    });
}

function carregaOpcoes(selectOrigem, selectDestino, url, paramName) {
    let selected = $(selectOrigem).val();

    $(selectDestino).empty().trigger("change");

    if (selected && selected.length > 0) {
        $.ajax({
            url: url,
            type: "GET",
            data: {
                _csrf: "{{ csrf_token() }}",
                [paramName]: selected,
                select: paramName,
            },
            success: function (data) {
                data.forEach(function (item) {
                    let newOption = new Option(
                        item.DESCRICAO,
                        item.ID,
                        false,
                        false
                    );
                    $(selectDestino).append(newOption);
                });
                $(selectDestino).trigger("change");
            },
        });
    }
}
