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

    $("#modal-transferir-lote-pcp").modal("show");
});

$(document).on("submit", "#form-transferir-lote-pcp", function (e) {
    e.preventDefault();

    let cd_empresa = $("#empresa-lote-pcp-transf").val();
    let nr_lote_novo = $("#lote-pcp-novo-transf").val();

    if (!nr_lote_novo) {
        Swal.fire({
            icon: "warning",
            title: "Lote novo não selecionado.",
            text: "Por favor, selecione um novo lote para transferir os pneus.",
            showConfirmButton: true,
            confirmButtonText: "OK",
        });
        return;
    }

    let tabela = $("#pneus-lote-pcp-" + cd_empresa).DataTable();

    let selectedRows = tabela
        .rows({
            selected: true,
        })
        .data()
        .toArray();

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
        return;
    }

    $.ajax({
        type: "POST",
        url: window.routes.transferirPneusLotePcp,
        data: {
            _token: window.routes.token,
            empresa: cd_empresa,
            lote_novo: nr_lote_novo,
            ordens_producao: selectedRows,
        },
        success: function (response) {
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
});
