$(document).on("click", ".btn-transferir-todos-pneus-lote-pcp", function () {
    let cd_empresa = $(this).data("cd_empresa");

    $("#empresa-lote-pcp-transf").val(cd_empresa);

    let data = {
        cd_empresa: cd_empresa,
    };

    inicializaSelect2Lista({
        route: window.routes.getListLotePCPEmProducao,
        selectId: "#lote-pcp-novo-transf",
        placeholder: "Selecione o Novo Lote",
        modalParent: "#modal-transferir-lote-pcp",
        textField: "DSLOTEPCP",
        valueField: "NR_LOTE",
        additionalData: data,
    });

    $("#modal-transferir-lote-pcp")
        .data("tabelaPrincipal", "#pneus-lote-pcp-" + cd_empresa)
        .data("tabelaSecundaria", [])
        .modal("show");
});

$(document).on("submit", "#form-transferir-lote-pcp", function (e) {
    e.preventDefault();

    let cd_empresa = $("#empresa-lote-pcp-transf").val();
    let nr_lote_novo = $("#lote-pcp-novo-transf").val();

    // pega a tabela salva no modal
    let tableSelectorPrimaria = $("#modal-transferir-lote-pcp").data(
        "tabelaPrincipal",
    );

    let tabelaPrincipal = $(tableSelectorPrimaria).DataTable();

    let tabelaSecundaria = $("#modal-transferir-lote-pcp").data(
        "tabelaSecundaria",
    );

    confirmTransferenciaPneus(cd_empresa, nr_lote_novo, tabelaPrincipal).done(
        function () {
            if (tabelaSecundaria && tabelaSecundaria.length > 0) {
                tabelaSecundaria.forEach(function (tabela) {
                    $(tabela).DataTable().ajax.reload();
                });
            }
        },
    );
});

function confirmTransferenciaPneus(cd_empresa, nr_lote_novo, tabela) {
    if (!nr_lote_novo) {
        Swal.fire({
            icon: "warning",
            title: "Lote novo não selecionado.",
            text: "Por favor, selecione um novo lote para transferir os pneus.",
            showConfirmButton: true,
            confirmButtonText: "OK",
        });
        return $.when();
    }

    var tableId = $(tabela.table().node()).attr("id");
    var cbClass =
        tableId === "table-pneus-lote-pcp"
            ? ".dt-row-checkbox-lote-pcp"
            : ".dt-row-checkbox-pcp";
    var idField = "NR_ORDEM";

    let selectedOps = [];
    $(tabela.table().container())
        .find(cbClass)
        .filter(function () {
            return this.checked;
        })
        .each(function () {
            selectedOps.push(String($(this).data("op")));
        });
    let selectedRows = tabela
        .rows()
        .data()
        .toArray()
        .filter(function (row) {
            return selectedOps.includes(String(row[idField]));
        });

    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhum pneu selecionado.",
            text: "Por favor, selecione ao menos um pneu para transferir de Lote.",
            showConfirmButton: true,
            confirmButtonText: "OK",
            buttonsStyling: false,
            customClass: {
                confirmButton: "btn btn-warning btn-sm",
            },
        });
        $("#modal-transferir-lote-pcp").modal("hide");
        return $.when();
    }

    return $.ajax({
        type: "POST",
        url: window.routes.transferirPneusLotePcp,
        beforeSend: function () {
            Swal.fire({
                title: "Transferindo pneus no lote...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        data: {
            _token: window.routes.token,
            empresa: cd_empresa,
            lote_novo: nr_lote_novo,
            ordens_producao: selectedRows,
        },
        success: function (response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Pneus transferidos com sucesso!",
                    text: response.message,
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-success btn-sm",
                    },
                });
                $("#modal-transferir-lote-pcp").modal("hide");
                // Zera checkboxes e badge do contador
                $(tabela.table().container())
                    .find(cbClass)
                    .prop("checked", false);
                $(tabela.table().container())
                    .find(".dt-select-all-pcp, .dt-select-all-lote-pcp")
                    .prop("checked", false);
                if (tableId !== "table-pneus-lote-pcp") {
                    var empresa = tableId.replace("pneus-lote-pcp-", "");
                    $("#pcp-count-badge-" + empresa).hide();
                }
                tabela.ajax.reload(); // recarrega a tabela sem resetar a paginação
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro ao transferir pneus.",
                    text: response.message,
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-danger btn-sm",
                    },
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: "error",
                title: "Erro na requisição.",
                text: "Ocorreu um erro ao tentar transferir os pneus. Por favor, tente novamente.",
                showConfirmButton: true,
                confirmButtonText: "OK",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-danger btn-sm",
                },
            });
        },
    });
}
