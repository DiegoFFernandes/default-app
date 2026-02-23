var table_carcaca_itens;
var itensCarcacaTable = [];

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

    let columns = [
        {
            data: null,
            // width: "1%",
            defaultContent: "",
            className: "select-checkbox",
            orderable: false,
        },
    ];

    if (!window.canEdit) {
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
        scrollX: true,
        select: {
            style: "multi",
            selector: "td.select-checkbox",
        },
        language: {
            url: window.routes.languageDatatables,
        },
        pagingType: "simple",
        ajax: {
            url: window.routes.getCarcacaCasa,
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

function initAccordion(data, idDivAccordion = "accordionResumo") {
    let html = ``;

    data.forEach((Nivel1Key, gIndex) => {
        html += `
                    <div class="card nivel1-card">
                        ${renderNivel0(Nivel1Key, gIndex)}
                    </div>
                `;
    });

    $("#" + idDivAccordion).html(html);
}

function renderNivel0(Nivel1Key, lIndex) {
    let html = `
                <div class="card-header p-1">
                    <button class="btn btn-block"
                        data-toggle="collapse"
                        data-target="#nivel0-${lIndex}">
                        <table class="table table-borderless mb-0 w-100">
                            <tr>
                                <td class="text-left p-0">
                                    <small class="text-muted">
                                        <i class="fas fa-chevron-down"></i>
                                        <strong>${Nivel1Key.local}</strong>
                                    </small>
                                </td>
                                <td class="text-right p-0 w-10">
                                    ${Nivel1Key.qtd} unid.
                                </td>                                
                            </tr>
                        </table>
                    </button>
                </div>

                <div id="nivel0-${lIndex}" class="collapse">
                    <div class="card-body p-1">
            `;

    Nivel1Key.medida.forEach((Nivel2Key, maIndex) => {
        html += renderNivel1(Nivel2Key, lIndex, maIndex);
    });

    html += `
                    </div>
                </div>
            `;

    return html;
}

function renderNivel1(Nivel2Key, lIndex, maIndex) {
    let html = `
                <div class="medida-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#medida-${lIndex}-${maIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-1 col-nome">                                     
                                        <i class="fas fa-chevron-down"></i>
                                        <strong class="ps-3"> ${Nivel2Key.medida}</strong>                                     
                                </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel2Key.qtd} unid.</td>                                
                            </tr>
                        </table>
                    </button>
                    <div id="medida-${lIndex}-${maIndex}" class="collapse">
            `;

    Nivel2Key.marca.forEach((Nivel2Key, moIndex) => {
        html += renderNivel2(Nivel2Key, lIndex, maIndex, moIndex);
    });

    html += `
                    </div>
                </div>
            `;

    return html;
}

function renderNivel2(Nivel2Key, lIndex, maIndex, moIndex) {
    let html = `
                <div class="marca-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#marca-${lIndex}-${maIndex}-${moIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-2 col-nome">                                     
                                    <i class="fas fa-chevron-down"></i>
                                    <strong class="ps-3"> ${Nivel2Key.marca}</strong>                                        
                                </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel2Key.qtd} unid.</td>                                
                            </tr>
                        </table>
                    </button>
                    <div id="marca-${lIndex}-${maIndex}-${moIndex}" class="collapse">
                    `;

    Nivel2Key.modelo.forEach((Nivel3Key, moIndex) => {
        html += renderNivel3(Nivel3Key);
    });

    html += `
                    </div>
                </div>
                `;
    return html;
}

function renderNivel3(Nivel3Key) {
    let html = `
                <div class="modelo-container ml-1 mr-1">
                    <button class="btn btn-list btn-block pt-0 pb-0">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-3 col-nome"> ${Nivel3Key.modelo} </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel3Key.qtd} unid.</td>
                            </tr>
                        </table>
                    </button>
                </div>
            `;
    return html;
}

function deleteOrDown(status, id) {
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

$(document).on("click", "#btn-baixar-todos", function () {
    let selectedRows = table_carcaca_itens
        .rows({
            selected: true,
        })
        .data();
    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para baixar.",
        });
        return;
    }
    var id = [];
    selectedRows.each(function (rowData) {
        id.push(rowData.ID);
    });

    deleteOrDown("B", id);
});

$(document).on("click", "#btn-transferir-todos", function () {
    let selectedRows = table_carcaca_itens
        .rows({
            selected: true,
        })
        .data();
    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para transferencia.",
        });
        return;
    }
    var id = [];
    selectedRows.each(function (rowData) {
        id.push(rowData.ID);
    });
    // grava os IDs dentro do modal
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

$(document).on("click", "#btn-criar-pedido", function () {
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

    let selectedRows = table_carcaca_itens
        .rows({
            selected: true,
        })
        .data();

    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada.",
            text: "Por favor, selecione ao menos uma carcaça para criar o pedido.",
        });
        return;
    }

    selectedRows.each(function (rowData) {
        let itemHtml = `
                                <div class="row mb-2 item-pedido" data-item-id="${rowData.ID}">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Medida</label>
                                        <input type="text"
                                            class="form-control form-control-sm"
                                            value="${rowData.DSMEDIDAPNEU}"
                                            readonly />
                                    </div>

                                     <!-- Botão -->
                                    <div class="col-1 col-md-auto d-flex align-items-end">
                                        <button type="button"
                                            class="btn btn-success btn-sm btn-atualizar-servicos mb-1"
                                            data-item-id="${rowData.ID}"
                                            data-medida-pneu="${rowData.IDMEDIDAPNEU}"
                                            >
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <div class="col-11 col-md-5">
                                        <label class="form-label small">Serviço</label>
                                        <select class="form-control form-control-sm servico-item-${rowData.ID}" style="width: 100%">                                           
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="form-label small">Valor</label>
                                        <input type="text"
                                            class="form-control form-control-sm input-venda"                                           
                                            />
                                    </div>
                                </div>
                            `;

        $("#itens-pedido").append(itemHtml);

        $(".input-venda").inputmask({
            mask: ["999", "9.999"],
            radixPoint: ",",
        });

        inicializaSelect2Lista({
            route:
                window.routes.servicoPneu +
                "?idMedidaPneu=" +
                rowData.IDMEDIDAPNEU,
            selectId: `.servico-item-${rowData.ID}`,
            placeholder: "Selecione o Serviço",
            modalParent: ".item-pedido",
            textField: "DSSERVICO",
            valueField: "ID",
        });
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
        let valor = $(this).find("input.input-venda").val().replace(".", "");

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

    // reinicializa o select2 com os novos serviços
    inicializaSelect2Lista({
        route: window.routes.servicoPneu + "?idMedidaPneu=" + medidaPneu,
        selectId: `.servico-item-${itemId}`,
        placeholder: "Selecione o Serviço",
        modalParent: "#modal-criar-pedido",
        textField: "DSSERVICO",
        valueField: "ID",
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
            Local: item.LOCAL_ESTOQUE                    
        });
    });

    exportarParaExcel(
        itensCarcacaExport,
        "carcacas-no-estoque.xlsx",
        "Carcacas no Estoque",
    );
});
