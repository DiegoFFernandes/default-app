$(document).on("click", ".btn-adicionar-pneus-lote", function () {
    let cd_empresa = $(this).data("empresa");
    let nr_lote = $(this).data("lote");

    let data = {
        cd_empresa: cd_empresa,
    };

    inicializaSelect2Lista({
        route: window.routes.getListPedidosSemPCP,
        selectId: "#select-pedidopneu",
        placeholder: "Selecione um pedido",
        modalParent: "#modal-adicionar-pneus-lote-pcp",
        textField: "NR_PEDIDO",
        valueField: "NR_PEDIDO",
        additionalData: data,
    });

    inicializaSelect2Lista({
        route: window.routes.getListOrdensProducaoSemPCP,
        selectId: "#select-ordens-producao",
        placeholder: "Selecione uma ordem de produção",
        modalParent: "#modal-adicionar-pneus-lote-pcp",
        textField: "NR_ORDEM",
        valueField: "NR_ORDEM",
        additionalData: data,
    });

    $(".modal-title-adicionar-pneus-lote-pcp").html(
        "Adicionar Pneus ao Lote: <b>" + nr_lote + "</b>",
    );
    $("#empresa-adicionar-lote-pcp").val(cd_empresa);
    $("#nrlote-adicionar-lote-pcp").val(nr_lote);

    $("#div-tabela-adicionar-pneus-lote-pcp").hide();

    $("#tabela-adicionar-pneus-lote-pcp").DataTable().clear().destroy();

    $("#modal-adicionar-pneus-lote-pcp").modal("show");
});

$(document).on("submit", "#form-adicionar-pneus-lote-pcp", function (e) {
    e.preventDefault();

    let dataForms = $(this).serialize();

    $("#tabela-adicionar-pneus-lote-pcp").DataTable({
        paging: false,
        pagingType: "simple",
        destroy: true,
        processing: false,
        serverSide: false,
        scrollY: "400px",
        select: {
            style: "multi",
            selector: "td:first-child",
        },
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getListPneusLoteSemPCP,
            type: "GET",
            data: {
                _token: window.routes.token,
                dados: dataForms,
            },
        },
        columns: [
            {
                data: null,
                width: "1%",
                className: "pl-3 pr-3 text-center",
                render: DataTable.render.select(),
                orderable: false,
            },
            {
                data: "NR_PEDIDO",
                name: "NR_PEDIDO",
                title: "Pedido",
                width: "5%",
                className: "text-center",
            },
            {
                data: "ID",
                name: "ID",
                title: "Ordem",
                width: "5%",
                className: "text-center",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Serviço",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "DTEMISSAO",
                name: "DTEMISSAO",
                title: "Emissão",
                render: function (data, type, row) {
                    return moment(data).format("DD/MM/YYYY");
                },
            },
        ],
        order: [[10, "asc"]],
    });

    $("#div-tabela-adicionar-pneus-lote-pcp").show();
});

$(document).on("click", "#btn-salvar-pneus-lote-pcp", function (e) {
    e.preventDefault();

    let bandaConsumir = $("#bandas-consumir").DataTable();

    let tabela = $("#tabela-adicionar-pneus-lote-pcp").DataTable();
    let pneusSelecionados = tabela
        .rows({
            selected: true,
        })
        .data()
        .toArray();

    if (pneusSelecionados.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhum pneu selecionado",
            text: "Por favor, selecione pelo menos um pneu para adicionar ao lote.",
        });
        return;
    }

    let idsOrdens = pneusSelecionados.map((pneu) => pneu.NR_ORDEM);
    let cd_empresa = $("#empresa-adicionar-lote-pcp").val();
    let nr_lote = $("#nrlote-adicionar-lote-pcp").val();

    $.ajax({
        url: window.routes.salvarPneusLotePcp,
        type: "POST",
        data: {
            _token: window.routes.token,
            cd_empresa: cd_empresa,
            nr_lote: nr_lote,
            id_ordens: idsOrdens,
        },
        beforeSend: function () {
            Swal.fire({
                title: "Salvando pneus no lote...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", true)
                .text("Salvando...");
        },
        success: function (response) {
            $("#modal-adicionar-pneus-lote-pcp").modal("hide");
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", false)
                .text("Adicionar ao Lote");

            Swal.close();

            if (response.success) {
                $("#lote-pcp").DataTable().ajax.reload();
                setTimeout(() => {
                    bandaConsumir.ajax.reload();
                }, 500);
                Swal.fire({
                    icon: "success",
                    title: "Pneus adicionados",
                    text:
                        response.message ||
                        "Os pneus foram adicionados ao lote com sucesso.",
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro ao adicionar pneus",
                    text:
                        response.message ||
                        "Ocorreu um erro ao adicionar os pneus ao lote.",
                });
            }
        },
        error: function (xhr) {
            Swal.close();
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", false)
                .text("Adicionar ao Lote");
            Swal.fire({
                icon: "error",
                title: "Erro ao adicionar pneus",
                text:
                    xhr.responseJSON.message ||
                    "Ocorreu um erro ao adicionar os pneus ao lote.",
            });
        },
    });
});
