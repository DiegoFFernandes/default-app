$(document).on("click", "#btn-novo-lote-pcp", function () {
    $("#modal-adicionar-lote-pcp").modal("show");
});

$(document).on("change", "#select-empresa-lote-pcp", function (e) {
    e.preventDefault();
    const id_empresa = $(this).val();

    data = {
        id_empresa: id_empresa,
    };

    inicializaSelect2Lista({
        route: window.routes.getControleLotePCP,
        selectId: "#select-lote-pcp",
        placeholder: "Selecione o Lote de PCP",
        modalParent: "#modal-adicionar-lote-pcp",
        textField: "DSCONTROLELOTEPCP",
        valueField: "ID",
        additionalData: data,
    });

    inicializaSelect2Lista({
        route: window.routes.getExecutorEtapa,
        selectId: "#select-responsavel-lote-pcp",
        placeholder: "Selecione o Responsável",
        modalParent: "#modal-adicionar-lote-pcp",
        textField: "NMEXECUTOR",
        valueField: "ID",
        additionalData: data,
    });

    $dataDM1 = new Date();
    $dataDM1.setDate($dataDM1.getDate() + 1);

    $("#data-producao-lote-pcp").val($dataDM1.toISOString().split("T")[0]);
});

$(document).on("submit", "#form-adicionar-lote-pcp", function (e) {
    e.preventDefault();

    const dataForms = $("#form-adicionar-lote-pcp").serialize();    

    $.ajax({
        type: "POST",
        url: window.routes.salvarLotePcp,
        data: dataForms,
        dataType: "json",
        beforeSend: function () {
            $("#btn-adicionar-lote-pcp")
                .prop("disabled", true)
                .text("Salvando...");
            $("#modal-adicionar-lote-pcp").modal("hide");
            Swal.fire({
                title: "Salvando Lote de PCP",
                text: "Por favor, aguarde...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function (response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Sucesso",
                    html: response.message,
                    confirmButtonText: "OK",
                    showConfirmButton: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-success btn-sm",
                    },
                });
                $("#lote-pcp").DataTable().ajax.reload();               

                $("#btn-adicionar-lote-pcp")
                    .prop("disabled", false)
                    .text("Salvar");
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    html: response.message,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-danger btn-sm",
                    },
                });
                
                $("#btn-adicionar-lote-pcp")
                    .prop("disabled", false)
                    .text("Salvar");
            }
        },
    });
});


