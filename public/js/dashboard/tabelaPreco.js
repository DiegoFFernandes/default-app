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
        scrollY: "400px",
        scrollCollapse: true,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.tabelaPreco,
            type: "GET",
            beforeSend: function () {
                $("#loading").removeClass("invisible");
            },
            complete: function () {
                $("#loading").addClass("invisible");
            },
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
                visible: false,
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
                data: "action",
                name: "action",
                title: "Ações",
            },
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

function initTableItemTabelaPreco(
    route,
    idTabela,
    tela,
    idTabelaItem,
    idModal
) {
    $("#" + idModal).modal("show");
    if ($.fn.DataTable.isDataTable("#" + idTabelaItem)) {
        $("#" + idTabelaItem)
            .DataTable()
            .clear()
            .destroy();
    }
    $("#" + idTabelaItem).DataTable({
        paging: false,
        searching: true,
        scrollY: "300px",
        scrollCollapse: true,
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
                tela: tela,
            },
            beforeSend: function () {
                $("#loading").removeClass("invisible");
            },
            complete: function () {
                $("#loading").addClass("invisible");
            },
        },
        columns: [
            {
                data: "CD_TABELA",
                name: "CD_TABELA",
                title: "Cód Tabela",
                visible: false,
            },
            {
                data: "DESCRICAO",
                name: "DESCRICAO",
                width: "80%",
                title: "Descrição",
            },
            {
                data: "VALOR",
                name: "VALOR",
                title: "Valor",
            },
        ],
    });
}

function initTableTabelaPrecoCadastradasPreview(route) {
    $("#tabela-preco-cadastradas").DataTable({
        paging: false,
        searching: true,
        scrollY: "300px",
        scrollCollapse: true,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.tabelaPrecoCadastradasPreview,
            type: "GET",
            beforeSend: function () {
                $("#loading").removeClass("invisible");
            },
            complete: function () {
                $("#loading").addClass("invisible");
            },
        },
        columns: [
            {
                title: "Ações",
                data: "action",
                orderable: false,
                searchable: false,
            },
            {
                title: "ID",
                data: "CD_TABPRECO",
                visible: false,
            },
            {
                title: "Tabela",
                data: "DS_TABPRECO",
            },
            {
                title: "Supervisor",
                data: "SUPERVISOR",
            },
            {
                title: "Itens",
                data: "QTD_ITENS",
            },
        ],
    });
}

function formatarNome(str) {
    return str.toLowerCase().replace(/\b\w/g, function (letra) {
        return letra.toUpperCase();
    });
}

function formularioDinamico(route) {
    const cardTabela = $("#item-tabela-preco").closest(".card");

    $("#desenho, #medida, #valor, #btn-associar").closest(".form-group").hide(); // esconde os select
    cardTabela.hide(); // esconde o card da tabela

    // exibe desenho quando pessoa for selecionado
    $("#pessoa").on("change", function () {
        if ($(this).val() === null || $(this).val().length === 0) {
            // Impede a requisição caso esteja vazio
            return false;
        }
        $.ajax({
            type: "GET",
            url: route.verificaTabelaCadastrada,
            data: {
                idTabela: $(this).val(),
            },
            success: function (response) {
                if (response.data.length > 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Atenção",
                        text: "Já existe uma tabela de preço cadastrada para este cliente, deseja atualiza-la?",
                        showCancelButton: true,
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não",
                        customClass: {
                            confirmButton: "btn btn-danger mr-2",
                            cancelButton: "btn btn-secondary",
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#desenho, #medida, #valor, #btn-associar")
                                .closest(".form-group")
                                .show();
                            cardTabela.show();
                            initTableTabelaPrecoPrevia();

                            // Adiciona os dados existentes na tabela
                            response.data.forEach(function (item) {
                                itens_preview.set(item.ID, item); // Adiciona no Map
                            });

                            tabela_preview
                                .clear()
                                .rows.add(Array.from(itens_preview.values()))
                                .draw(); // Atualiza a tabela com os dados existentes
                        } else {
                            $("#pessoa").val("").trigger("change");
                            $("#desenho, #medida, #valor, #btn-associar")
                                .closest(".form-group")
                                .hide();
                        }
                    });
                } else {
                    $("#desenho").closest(".form-group").show();
                }
            },
        });
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

    // limpa e esconde
    $("#btn-recomecar").on("click", function () {
        recomecar();
    });
}

function initTableTabelaPrecoPrevia() {
    tabela_preview = $("#item-tabela-preco").DataTable({
        paging: false,
        searching: true,
        scrollY: "300px",
        scrollCollapse: true,
        language: {
            url: routes.language_datatables,
        },
        select: {
            style: "multi",
        },
        data: [],
        columns: [
            {
                data: null,
                width: "1%",
                render: DataTable.render.select(),
            },
            {
                title: "Cód",
                data: "ID",               
                visible: false,
            },
            {
                title: "Descrição",
                data: "DESCRICAO",
                width: "60%",
            },
            {
                title: "Valor",
                data: "VALOR",
                width: "20%",
                render: $.fn.dataTable.render.number(".", ",", 2),
            },
        ],
        orderBy: [[0, "asc"]],
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

function recomecar() {
    $("#item-tabela-preco").DataTable().clear().destroy();
    dados_atualizados = [];
    itens_preview = new Map();
    $("#pessoa, #desenho, #medida, #valor").val("").trigger("change"); // limpa os inputs
    $("#desenho").closest(".form-group").hide(); // esconde os selects
    $("#item-tabela-preco").closest(".card").hide();
}
