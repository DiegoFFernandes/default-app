var table_carcaca_itens;
var itensCarcacaTable = [];
var selectedIds = new Set();

function updateCarcacasBadge() {
    var count = selectedIds.size;
    var $badge = $(".carcacas-count-badge");
    if (count > 0) {
        $badge.text(count + " selecionada" + (count > 1 ? "s" : "")).show();
    } else {
        $badge.hide();
    }
}

initSelect2Pessoa("#pessoa", window.routes.searchPessoa, "#modal-criar-pedido");

$("#btn-add-carcaca").on("click", function () {
    $("#modal-add-carcaca").modal("show");
    $("#modal-add-carcaca .modal-title").text("Adicionar Carcaça");
    $("#cd_medida").val(null).trigger("change");
    $("#cd_modelo").val(null).trigger("change");
    $("#nr_fogo").val("");
    $("#nr_serie").val("");
    $("#cd_tipo").val("1").change();
    $("#cd_local").val("1").change();
    $("#btn-save-carcaca").removeClass("d-none");
    $("#btn-edit-carcaca").addClass("d-none");
});

$("#cd_medida").select2({
    placeholder: "Selecione a Medida",
    theme: "bootstrap4",
    width: "100%",
    allowClear: true,
    dropdownParent: $("#modal-add-carcaca"),
    minimumInputLength: 2,
    ajax: {
        url: window.routes.searchMedidas,
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.DS_MEDIDA,
                        id: item.ID,
                    };
                }),
            };
        },
        cache: false,
    },
});

$("#cd_modelo").select2({
    placeholder: "Selecione o Modelo",
    theme: "bootstrap4",
    width: "100%",
    allowClear: true,
    dropdownParent: $("#modal-add-carcaca"),
    minimumInputLength: 2,
    ajax: {
        url: window.routes.searchModelos,
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.DSMODELO,
                        id: item.IDMODELO,
                    };
                }),
            };
        },
        cache: false,
    },
});

initTableCarcaca();

function initTableCarcaca() {
    if (table_carcaca_itens) {
        table_carcaca_itens.destroy();
    }
    selectedIds.clear();
    updateCarcacasBadge();

    let columns = [
        {
            data: null,
            width: "1%",
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                if (type === 'display') {
                    var checked = selectedIds.has(row.ID) ? ' checked' : '';
                    return '<input type="checkbox" class="dt-row-checkbox" data-id="' + row.ID + '" aria-label="Selecionar linha"' + checked + '>';
                }
                return '';
            },
        },
    ];

    if (window.canEdit) {
        columns.push({
            data: "action",
            name: "action",
            title: "Ações",
            orderable: false,
            searchable: false,
            // width: '10%',
            className: "text-center text-nowrap",
        });
    }

    columns = columns.concat([
        {
            data: "ID",
            name: "ID",
            title: "Cód.",
            visible: true,
            className: "text-center",
        },
        {
            data: "DSMEDIDAPNEU",
            name: "DSMEDIDAPNEU",
            title: "Medida",
        },
        {
            data: "DSMODELO",
            name: "DSMODELO",
            title: "Modelo",
        },
        {
            data: "NR_FOGO",
            name: "NR_FOGO",
            visible: false,
            title: "Fogo",
            className: "text-center",
        },
        {
            data: "NR_SERIE",
            name: "NR_SERIE",
            title: "Serie",
            className: "text-center",
        },
        {
            data: "NR_DOT",
            name: "NR_DOT",
            title: "Dot",
            className: "text-center",
        },
        {
            data: "VL_CARCACA",
            name: "VL_CARCACA",
            title: "Valor",
            className: "text-center",
            render: $.fn.dataTable.render.number(".", ",", 2),
        },
        {
            data: "DS_TIPO",
            name: "DS_TIPO",
            title: "Tipo",
        },
        {
            data: "LOCAL_ESTOQUE",
            name: "LOCAL_ESTOQUE",
            title: "Local",
        },
    ]);

    table_carcaca_itens = $("#estoque-carcacas").DataTable({
        processing: false,
        serverSide: false,
        responsive: false,
        paging: false,
        scrollX: true,
        scrollY: '400px',
        scrollCollapse: true,
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getCarcacaCasa,
            beforeSend: function () {
                window._swalCarcacaTimer = setTimeout(function () {
                    Swal.fire({
                        title: 'Carregando carcaças...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                }, 400);
            },
            complete: function () {
                clearTimeout(window._swalCarcacaTimer);
                Swal.close();
            },
            dataSrc: function (response) {
                $("#total-carcacas").text(response.extra.total_carcacas);

                $("#accordionResumoLocal")
                    .html(
                        initAccordion(
                            response.accordion_data_local_marca,
                            "accordionResumoLocal",
                        ),
                    )
                    .removeClass("d-none");

                itensCarcacaTable = response.datatable.data;
                return response.datatable.data;
            },
        },
        columns: columns,
        order: [[0, "desc"]],
    });
}

// initAccordion / renderNivel0..3 movidos para accordionResumo.js (compartilhado
// com a tela de Pneus Novos). Carregar accordionResumo.js antes deste arquivo.

function deleteOrDown(status, id, clearOnSuccess = false) {
    const config = verificaStatus(status);

    Swal.fire({
        // title: 'Tem certeza?',
        text: config.confirmText,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: config.confirmButtonTitle,
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.routes.deleteCarcaca,
                type: "POST",
                data: {
                    _token: window.routes.token,
                    id: id,
                    status: status,
                },
                success: function (response) {
                    if (response.success) {
                        $("#modal-add-carcaca").modal("hide");
                        $("#estoque-carcacas").DataTable().ajax.reload();
                        $("#estoque-carcacas-baixas").DataTable().ajax.reload();
                        if (clearOnSuccess) {
                            selectedIds.clear();
                            updateCarcacasBadge();
                        }
                        Swal.fire({
                            icon: "success",
                            title: config.swalSuccessText,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    } else {
                        // Erro
                        Swal.fire({
                            icon: "error",
                            title: config.swalErrorTitle,
                            html: response.errors,
                            customClass: {
                                htmlContainer: "text-left",
                            },
                        });
                        return;
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: config.swalErrorTitle,
                        text: xhr.responseText,
                    });
                },
            });
        } else {
            return;
        }
    });
}

function verificaStatus(status) {
    let messages = {
        B: {
            confirmButtonTitle: "Sim, baixar!",
            confirmText: "Tem certeza que deseja baixar esta carcaça?",
            swalSuccessText: "Carcaça baixada com sucesso!",
            swalErrorTitle: "Erro ao baixar carcaça.",
        },
        D: {
            confirmButtonTitle: "Sim, deletar!",
            confirmText: "Você não poderá reverter isso!",
            swalSuccessText: "Carcaça deletada com sucesso!",
            swalErrorTitle: "Erro ao deletar carcaça.",
        },
        A: {
            confirmButtonTitle: "Sim, cancelar!",
            confirmText: "Tem certeza que deseja cancelar a baixa?",
            swalSuccessText: "Baixa cancelada com sucesso!",
            swalErrorTitle: "Erro ao cancelar baixa.",
        },
    };
    return messages[status] || messages["default"];
}

$(document).on("click", "#btn-save-carcaca", function () {
    let medida = $("#cd_medida").val();
    let modelo = $("#cd_modelo").val();
    let fogo = $("#nr_fogo").val();
    let serie = $("#nr_serie").val();
    let tipo = $("#cd_tipo").val();
    let local = $("#cd_local").val();
    let dot = $("#nr_dot").val();
    let valor = $("#vl_carcaca").val().replace(",", ".");

    $.ajax({
        url: window.routes.storeCarcaca,
        type: "POST",
        data: {
            _token: window.routes.token,
            medida: medida,
            modelo: modelo,
            fogo: fogo,
            serie: serie,
            dot: dot,
            valor: valor,
            tipo: tipo,
            local: local,
        },
        success: function (response) {
            if (response.success) {
                $("#modal-add-carcaca").modal("hide");
                $("#estoque-carcacas").DataTable().ajax.reload();
                Swal.fire({
                    icon: "success",
                    title: "Carcaça salva com sucesso!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else {
                // Erro
                Swal.fire({
                    icon: "error",
                    title: "Erro ao salvar carcaça.",
                    html: response.errors,
                    customClass: {
                        htmlContainer: "text-left",
                    },
                });
                return;
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: "error",
                title: "Erro ao salvar carcaça.",
                text: xhr.responseText,
            });
        },
    });
});

$(document).on("click", ".btn-baixar", function () {
    let id = [$(this).data("id")];
    deleteOrDown("B", id);
});

$(document).on("click", ".btn-deletar", function () {
    let id = [$(this).data("id")];
    deleteOrDown("D", id);
});

$(document).on("click", ".btn-editar", function () {
    const $dataRow = $("#estoque-carcacas")
        .DataTable()
        .row($(this).parents("tr"))
        .data();

    $(".modal-title").text("Editar Carcaça");
    $("#id_carcaca").val($dataRow["ID"]);
    $("#cd_medida")
        .append(
            new Option(
                $dataRow["DSMEDIDAPNEU"],
                $dataRow["IDMEDIDAPNEU"],
                true,
                true,
            ),
        )
        .trigger("change");
    $("#cd_modelo")
        .append(
            new Option(
                $dataRow["DSMODELO"],
                $dataRow["IDMODELOPNEU"],
                true,
                true,
            ),
        )
        .trigger("change");
    $("#nr_fogo").val($dataRow["NR_FOGO"]);
    $("#nr_serie").val($dataRow["NR_SERIE"]);
    $("#cd_tipo").val($dataRow["CD_TIPO"]);
    $("#cd_local").val($dataRow["CD_LOCAL"]);
    $("#nr_dot").val($dataRow["NR_DOT"]);
    $("#vl_carcaca").val($dataRow["VL_CARCACA"]);

    $("#btn-save-carcaca").addClass("d-none");
    $("#btn-edit-carcaca").removeClass("d-none");

    $("#modal-add-carcaca").modal("show");
});

$(document).on("click", "#btn-edit-carcaca", function () {
    let medida = $("#cd_medida").val();
    let modelo = $("#cd_modelo").val();
    let fogo = $("#nr_fogo").val();
    let serie = $("#nr_serie").val();
    let dot = $("#nr_dot").val();
    let valor = $("#vl_carcaca").val().replace(",", ".");
    let tipo = $("#cd_tipo").val();
    let local = $("#cd_local").val();
    let id_carcaca = $("#id_carcaca").val();

    $.ajax({
        url: window.routes.editCarcaca,
        type: "GET",
        data: {
            _token: window.routes.token,
            id: id_carcaca,
            medida: medida,
            modelo: modelo,
            fogo: fogo,
            serie: serie,
            dot: dot,
            valor: valor,
            tipo: tipo,
            local: local,
        },
        success: function (response) {
            if (response.success) {
                $("#modal-add-carcaca").modal("hide");
                $("#estoque-carcacas").DataTable().ajax.reload();
                Swal.fire({
                    icon: "success",
                    title: "Carcaça atualizada com sucesso!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else {
                // Erro
                Swal.fire({
                    icon: "error",
                    title: "Erro ao atualizar carcaça.",
                    html: response.errors,
                    customClass: {
                        htmlContainer: "text-left",
                    },
                });
                return;
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: "error",
                title: "Erro ao atualizar carcaça.",
                text: xhr.responseText,
            });
        },
    });
});

$(document).on("click", ".dt-row-checkbox", function (e) {
    e.stopPropagation();
    var id = parseInt($(this).attr("data-id"));
    if ($(this).is(":checked")) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
    }
    updateCarcacasBadge();
});

$(document).on("click", "#btn-baixar-todos", function () {
    var id = Array.from(selectedIds);

    if (id.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para baixar.",
        });
        return;
    }

    deleteOrDown("B", id, true);
});

$(document).on("click", "#btn-transferir-todos", function () {
    var id = Array.from(selectedIds);

    if (id.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para transferencia.",
        });
        return;
    }

    $("#modal-transferir-carcaca").data("ids", id);
    $("#modal-transferir-carcaca").modal("show");
});

$(document).on("click", "#btn-transferir-carcaca", function () {
    let ids = $("#modal-transferir-carcaca").data("ids");
    let local = $("#cd_local_transferir").val();

    $.ajax({
        url: window.routes.transferCarcaca,
        type: "POST",
        data: {
            _token: window.routes.token,
            ids: ids,
            local: local,
        },
        success: function (response) {
            if (response.success) {
                $("#modal-transferir-carcaca").modal("hide");
                $("#estoque-carcacas").DataTable().ajax.reload();
                selectedIds.clear();
                updateCarcacasBadge();
                Swal.fire({
                    icon: "success",
                    title: "Carcaças transferidas com sucesso!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else {
                // Erro
                Swal.fire({
                    icon: "error",
                    title: "Erro ao transferir carcaças.",
                    html: response.errors,
                    customClass: {
                        htmlContainer: "text-left",
                    },
                });
                return;
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: "error",
                title: "Erro ao transferir carcaças.",
                text: xhr.responseText,
            });
        },
    });
});

var _medidasServicoReplicado = new Set();
var _medidasValorReplicado   = new Set();

$(document).on("hidden.bs.modal", "#modal-criar-pedido", function () {
    _medidasServicoReplicado.clear();
    _medidasValorReplicado.clear();
});

$(document).on("select2:select", "#itens-pedido select", function () {
    var medidaAtual = $(this).closest(".item-pedido").data("medida-pneu");
    if (_medidasServicoReplicado.has(medidaAtual)) return;
    _medidasServicoReplicado.add(medidaAtual);

    var $select      = $(this);
    var selectedData = $select.select2("data")[0];
    var $itemAtual   = $select.closest(".item-pedido");

    Swal.fire({
        icon: "question",
        title: "Replicar serviço?",
        text: "Deseja aplicar \"" + selectedData.text + "\" para todos os itens com a mesma medida?",
        showCancelButton: true,
        confirmButtonText: "Sim, replicar",
        cancelButtonText: "Não",
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $("#itens-pedido .item-pedido").not($itemAtual).each(function () {
            if ($(this).data("medida-pneu") !== medidaAtual) return;

            var $destSelect = $(this).find("select");
            if ($destSelect.find("option[value='" + selectedData.id + "']").length === 0) {
                $destSelect.append(new Option(selectedData.text, selectedData.id, true, true));
            }
            $destSelect.val(selectedData.id).trigger("change");
        });
    });
});

$(document).on("change", "#itens-pedido .input-venda", function () {
    var $itemAtual  = $(this).closest(".item-pedido");
    var medidaAtual = $itemAtual.data("medida-pneu");
    if (_medidasValorReplicado.has(medidaAtual)) return;
    _medidasValorReplicado.add(medidaAtual);

    var valor = $(this).val();

    Swal.fire({
        icon: "question",
        title: "Replicar valor?",
        text: "Deseja aplicar o valor \"" + valor + "\" para todos os itens com a mesma medida?",
        showCancelButton: true,
        confirmButtonText: "Sim, replicar",
        cancelButtonText: "Não",
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $("#itens-pedido .item-pedido").not($itemAtual).each(function () {
            if ($(this).data("medida-pneu") !== medidaAtual) return;
            $(this).find(".input-venda").val(valor);
        });
    });
});

$(document).on("click", "#btn-criar-pedido", function () {
    let selectedRows = itensCarcacaTable.filter(function (row) {
        return selectedIds.has(parseInt(row.ID));
    });

    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para criar o pedido.",
        });
        return;
    }

    $("#itens-pedido").html("");
    $("#btn-confirmar-pedido").prop("disabled", false);

    inicializaSelect2Lista({
        route: window.routes.condicaoPagamento,
        selectId: "#cd_cond_pagto",
        placeholder: "Selecione a Condição de Pagamento",
        modalParent: "#modal-criar-pedido",
        textField: "DS_CONDPAGTO",
        valueField: "CD_CONDPAGTO",
    });

    inicializaSelect2Lista({
        route: window.routes.formaPagamento,
        selectId: "#cd_form_pagto",
        placeholder: "Selecione a Forma de Pagamento",
        modalParent: "#modal-criar-pedido",
        textField: "DS_FORMAPAGTO",
        valueField: "CD_FORMAPAGTO",
    });

    selectedRows.forEach(function (rowData) {
        let itemHtml = `
            <div class="row mb-2 item-pedido" data-item-id="${rowData.ID}" data-medida-pneu="${rowData.IDMEDIDAPNEU}">
                <div class="col-4 col-md-3">
                    <label class="form-label small">Medida</label>
                    <input type="text" class="form-control form-control-sm" value="${rowData.DSMEDIDAPNEU}" readonly />
                </div>
                <div class="col-4 col-md-3">
                    <label class="form-label small">Marca/Modelo</label>
                    <input type="text" class="form-control form-control-sm" value="${rowData.DSMODELO}" readonly />
                </div>
                <div class="col-1 col-md-auto d-flex align-items-end">
                    <button type="button" class="btn btn-success btn-sm btn-atualizar-servicos mb-1"
                        data-item-id="${rowData.ID}" data-medida-pneu="${rowData.IDMEDIDAPNEU}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="col-11 col-md-4">
                    <label class="form-label small">Serviço</label>
                    <select class="form-control form-control-sm servico-item-${rowData.ID}" style="width: 100%"></select>
                </div>
                <div class="col-12 col-md-1">
                    <label class="form-label small">Valor</label>
                    <input type="text" class="form-control form-control-sm input-venda" />
                </div>
            </div>`;

        $("#itens-pedido").append(itemHtml);

        inicializaSelect2Lista({
            route: window.routes.servicoPneu + "?idMedidaPneu=" + rowData.IDMEDIDAPNEU,
            selectId: `.servico-item-${rowData.ID}`,
            placeholder: "Selecione o Serviço",
            modalParent: ".item-pedido",
            textField: "DSSERVICO",
            valueField: "ID",
        });
    });

    $(".input-venda").inputmask({
        mask: ["999,99", "9.999,99"],
        radixPoint: ",",
    });

    $("#modal-criar-pedido").modal("show");
});

$(document).on("click", "#btn-confirmar-pedido", function () {
    let $btn = $(this);

    if ($btn.prop("disabled")) {
        return; // impede segundo clique
    }

    $btn.prop("disabled", true); // bloqueia botão

    let cd_empresa = $("#cd_empresa").val();
    let pessoa = $("#pessoa").val();
    let cond_pagto = $("#cd_cond_pagto").val();
    let form_pagto = $("#cd_form_pagto").val();

    let itens = [];

    $(".item-pedido").each(function () {
        let itemId = $(this).data("item-id");
        let servico = $(this).find("select").val();
        let valor = $(this).find("input.input-venda").val()
            .replace(/\./g, "")
            .replace(",", ".");

        itens.push({
            itemId: itemId,
            servico: servico,
            valor: valor,
        });
    });

    $.ajax({
        url: window.routes.storePedidoPneu,
        type: "POST",
        data: {
            _token: window.routes.token,
            cd_empresa: cd_empresa,
            pessoa: pessoa,
            cond_pagto: cond_pagto,
            form_pagto: form_pagto,
            itens: itens,
        },
        beforeSend: function () {
            $(".loading-card").removeClass("invisible");
        },
        success: function (response) {
            if (response.success) {
                $(".loading-card").addClass("invisible");
                $("#modal-criar-pedido").modal("hide");
                $("#estoque-carcacas").DataTable().ajax.reload();
                selectedIds.clear();
                updateCarcacasBadge();
                Swal.fire({
                    icon: "success",
                    title:
                        "Pedido <strong>" +
                        response.id +
                        "</strong> criado com sucesso!",
                    showConfirmButton: true,
                    confirmButtonText: "Ok",
                });
            } else {
                // Erro
                $(".loading-card").addClass("invisible");
                Swal.fire({
                    icon: "error",
                    title: "Erro ao criar pedido.",
                    html: response.errors,
                    customClass: {
                        htmlContainer: "text-left",
                    },
                });
                $("#btn-confirmar-pedido").prop("disabled", false);
                return;
            }
        },
        error: function (xhr) {
            $(".loading-card").addClass("invisible");
            Swal.fire({
                icon: "error",
                title: "Erro ao criar pedido.",
                text: xhr.responseText,
            });
        },
    });
});

$(document).on("click", ".btn-atualizar-servicos", function () {
    let btn = $(this);
    let itemId = $(this).data("item-id");
    let medidaPneu = $(this).data("medida-pneu");
    let selectServico = $(`.servico-item-${itemId}`);

    if (!medidaPneu) {
        Swal.fire({
            icon: "error",
            title: "Erro ao atualizar serviços.",
            text: "Medida do pneu não encontrada.",
        });
        return;
    }

    let btnOriginalHtml = btn.html();

    // coloca loading
    btn.prop("disabled", true);
    btn.html('<i class="fas fa-spinner fa-spin"></i>');

    // reinicializa o select2 com os novos serviços
    inicializaSelect2Lista({
        route: window.routes.servicoPneu + "?idMedidaPneu=" + medidaPneu,
        selectId: `.servico-item-${itemId}`,
        placeholder: "Selecione o Serviço",
        modalParent: ".item-pedido",
        textField: "DSSERVICO",
        valueField: "ID",
        callback: function () {
            // volta botão ao normal
            btn.prop("disabled", false);
            btn.html(btnOriginalHtml);
        },
    });
});

$(document).on("click", "#tab-carcaca-saida", function () {
    $("#estoque-carcacas-baixas").DataTable().destroy();

    var table_carcaca_itens_baixas = $("#estoque-carcacas-baixas").DataTable({
        processing: false,
        serverSide: false,
        language: {
            url: window.routes.languageDatatables,
        },
        pagingType: "simple",
        ajax: {
            url: window.routes.getCarcacaCasaBaixas,
            dataSrc: function (response) {
                return response.datatable.data;
            },
        },
        columns: [
            {
                data: "ID",
                name: "ID",
                title: "id",
                visible: false,
                className: "text-center",
            },
            {
                data: "DSMEDIDAPNEU",
                name: "DSMEDIDAPNEU",
                title: "Medida",
                width: "20%",
            },
            {
                data: "DSMODELO",
                name: "DSMODELO",
                title: "Modelo",
            },
            {
                data: "NR_FOGO",
                name: "NR_FOGO",
                title: "Fogo",
                className: "text-center",
            },
            {
                data: "NR_SERIE",
                name: "NR_SERIE",
                title: "Serie",
                className: "text-center",
            },
            {
                data: "NR_DOT",
                name: "NR_DOT",
                title: "Dot",
                className: "text-center",
            },
            {
                data: "DS_TIPO",
                name: "DS_TIPO",
                title: "Tipo",
            },
            {
                data: "PEDIDO",
                name: "PEDIDO",
                title: "Pedido",
                className: "text-center",
            },
            {
                data: "LOCAL_ESTOQUE",
                name: "LOCAL_ESTOQUE",
                title: "Local",
                className: "text-center",
            },

            {
                data: "EMPRESA_BAIXA",
                name: "EMPRESA_BAIXA",
                title: "Baixa",
            },
            {
                data: "ST_BAIXA",
                name: "ST_BAIXA",
                title: "St Baixa",
            },
            {
                data: "DT_BAIXA",
                name: "DT_BAIXA",
                title: "Data Baixa",
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return moment(data).format("DD/MM/YYYY");
                    }
                    return data;
                },
            },
            {
                data: "action",
                name: "action",
                title: "Ações",
                orderable: false,
                searchable: false,
                // width: '10%',
                className: "text-center",
            },
        ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: "select-checkbox",
            },
        ],
        order: [[0, "desc"]],
    });
});

$(document).on("click", "#tab-carcaca-entrada", function () {
    $("#estoque-carcacas").DataTable().ajax.reload();
});

$(document).on("click", ".btn-cancelar-baixa", function () {
    let id = [$(this).data("id")];
    deleteOrDown("A", id);
});

$(document).on("click", "#download-itens", function () {
    let itensCarcacaExport = [];

    itensCarcacaTable.forEach(function (item) {
        itensCarcacaExport.push({
            ID: item.ID,
            Medida: item.DSMEDIDAPNEU,
            Marca: item.DSMARCA,
            Modelo: item.DSMODELO1,
            Fogo: item.NR_FOGO,
            Serie: item.NR_SERIE,
            Dot: item.NR_DOT,
            Tipo: item.DS_TIPO,
            Local: item.LOCAL_ESTOQUE,
        });
    });

    exportarParaExcel(
        itensCarcacaExport,
        "carcacas-no-estoque.xlsx",
        "Carcacas no Estoque",
    );
});
