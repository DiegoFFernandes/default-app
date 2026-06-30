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
                        ' data-cd_pessoa="' + row.CD_PESSOA + '"' +
                        ' data-nm_item="' + $("<div>").text(row.DS_ITEM).html() + '"' +
                        ' data-vl_unitario="' + row.VL_UNITARIO + '">';
                },
            },
            {
                data: "action",
                name: "action",
                title: "#",
                className: "text-center",
                orderable: false,
                searchable: false,
            },            
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "NM_VENDEDOR",
                name: "NM_VENDEDOR",
                title: "Vendedor",
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
        : '';
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

    $.ajax({
        type: "POST",
        url: window.routes.adicionarItensItemFaltante,
        data: {
            _token: window.routes.csrfToken,
            itens: selecionados,
        },
        dataType: "json",
        beforeSend: function () {
            Swal.fire({
                title: "Adicionando itens na tabela...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
        },
        success: function (response) {
            Swal.fire({
                icon: response.success ? "success" : "warning",
                title: "Atenção",
                text: response.message,
                customClass: {
                    confirmButton: response.success ? "btn btn-success" : "btn btn-warning",
                },
            }).then(function () {
                if (response.success) {
                    initTableItemFaltanteTabelaPreco(window.routes);
                }
            });
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Ocorreu um erro ao salvar os itens. Tente novamente.",
                customClass: { confirmButton: "btn btn-danger" },
            });
        },
    });
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

// Botão Não Incluir — ignora o item na tabela
$(document).on("click", ".btn-ignorar-item-tabpreco", function () {
    var cd_tabpreco = $(this).data("cd_tabpreco");
    var cd_item     = $(this).data("cd_item");
    var cd_pessoa   = $(this).data("cd_pessoa");
    var ds_item     = $(this).data("ds_item");
    var $btn        = $(this);

    Swal.fire({
        icon: "warning",
        title: "Não Incluir",
        html: 'O item <strong>' + ds_item + '</strong> não aparecerá mais nesta listagem.<br>Deseja continuar?',
        confirmButtonText: "Sim, não incluir",
        cancelButtonText: "Cancelar",
        showCancelButton: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger mr-2",
            cancelButton: "btn btn-secondary",
        },
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            type: "POST",
            url: window.routes.ignorarItemTabPreco,
            data: {
                _token: window.routes.csrfToken,
                cd_tabpreco: cd_tabpreco,
                cd_item: cd_item,
                cd_pessoa: cd_pessoa,
            },
            dataType: "json",
            beforeSend: function () {
                Swal.fire({
                    title: "Salvando...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });
            },
            success: function (response) {
                Swal.fire({
                    icon: response.success ? "success" : "warning",
                    title: "Atenção",
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    if (response.success) {
                        initTableItemFaltanteTabelaPreco(window.routes);
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "Ocorreu um erro ao processar a solicitação. Tente novamente.",
                    customClass: { confirmButton: "btn btn-danger" },
                });
            },
        });
    });
});

// Ignorar todos selecionados
$(document).on("click", "#btn-ignoar-todos-item-faltante", function () {
    var selecionados = [];

    $("#tabela-item-faltante tbody .check-item-faltante:checked").each(function () {
        selecionados.push({
            cd_tabpreco : $(this).data("cd_tabpreco"),
            cd_item     : $(this).data("cd_item"),
            cd_pessoa   : $(this).data("cd_pessoa"),
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

    Swal.fire({
        icon: "warning",
        title: "Não Incluir",
        html: 'Os <strong>' + selecionados.length + '</strong> item(ns) selecionado(s) não aparecerão mais nesta listagem.<br>Deseja continuar?',
        confirmButtonText: "Sim, não incluir",
        cancelButtonText: "Cancelar",
        showCancelButton: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger mr-2",
            cancelButton: "btn btn-secondary",
        },
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            type: "POST",
            url: window.routes.ignorarItensItemFaltante,
            data: {
                _token: window.routes.csrfToken,
                itens: selecionados,
            },
            dataType: "json",
            beforeSend: function () {
                Swal.fire({
                    title: "Salvando...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });
            },
            success: function (response) {
                Swal.fire({
                    icon: response.success ? "success" : "warning",
                    title: "Atenção",
                    text: response.message,
                    customClass: {
                        confirmButton: response.success ? "btn btn-success" : "btn btn-warning",
                    },
                }).then(function () {
                    if (response.success) {
                        initTableItemFaltanteTabelaPreco(window.routes);
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "Ocorreu um erro ao processar a solicitação. Tente novamente.",
                    customClass: { confirmButton: "btn btn-danger" },
                });
            },
        });
    });
});

$(document).on("click", "#painel-item-faltante .btn-tool-export", function () {
    var tipo = $(this).data("export");
    $("#tabela-item-faltante_wrapper .buttons-" + tipo).trigger("click");
});

// Modal parâmetros — carrega checkboxes ao abrir
$(document).on("show.bs.modal", "#modal-parametros-cogs", function () {
    $("#param-cogs-if-loading").show();
    $("#param-cogs-if-content").hide();

    $.get(window.routes.getParametrosItemFaltante, function (response) {
        var $checks = $("#serie-checks").empty();

        $.each(response.series, function (i, item) {
            var checked = response.cd_serie.indexOf(item.CD_SERIE) !== -1 ? "checked" : "";
            $checks.append(
                '<div class="col-6 mb-2">' +
                    '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox" class="custom-control-input serie-check"' +
                            ' id="serie-' + item.CD_SERIE + '"' +
                            ' value="' + item.CD_SERIE + '" ' + checked + '>' +
                        '<label class="custom-control-label" for="serie-' + item.CD_SERIE + '">' +
                            '<code class="text-secondary" style="font-size:0.78rem; background:#f4f4f4; padding:1px 4px; border-radius:3px;">' +
                                item.CD_SERIE +
                            '</code>' +
                        '</label>' +
                    '</div>' +
                '</div>'
            );
        });

        $("#param-cogs-if-loading").hide();
        $("#param-cogs-if-content").show();
    }).fail(function () {
        $("#param-cogs-if-loading").html(
            '<span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>Erro ao carregar parâmetros.</span>'
        );
    });
});

// Salvar parâmetros
$(document).on("click", "#btn-salvar-parametros-item-faltante", function () {
    var selecionados = $(".serie-check:checked").map(function () {
        return $(this).val();
    }).get();

    if (selecionados.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Selecione pelo menos uma série.",
            customClass: { confirmButton: "btn btn-warning" },
        });
        return;
    }

    var $btn = $(this).prop("disabled", true)
                      .html('<i class="fas fa-sync-alt fa-spin mr-1"></i> Salvando...');

    $.ajax({
        type: "POST",
        url: window.routes.saveParametrosItemFaltante,
        data: { _token: window.routes.csrfToken, cd_serie: selecionados },
        dataType: "json",
        success: function (response) {
            Swal.fire({
                icon: response.success ? "success" : "warning",
                title: response.success ? "Salvo!" : "Atenção",
                text: response.message,
                showConfirmButton: false,
                timer: 2000,
            }).then(function () {
                if (response.success) {
                    $("#modal-parametros-cogs").modal("hide");
                    initTableItemFaltanteTabelaPreco(window.routes);
                }
            });
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao salvar. Tente novamente.",
                customClass: { confirmButton: "btn btn-danger" },
            });
        },
        complete: function () {
            $btn.prop("disabled", false).html('<i class="fas fa-save mr-1"></i> Salvar');
        },
    });
});
