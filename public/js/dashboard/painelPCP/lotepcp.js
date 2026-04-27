$(document).on("click", "#tab-lotesPCP", function () {
    if ($.fn.DataTable.isDataTable("#lote-pcp")) {
        $("#lote-pcp").DataTable().destroy();
    }

    $("#lote-pcp").DataTable({
        pageLength: 100,
        processing: false,
        serverSide: false,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getLotePcp,
            type: "POST",
            data: {
                _token: window.routes.token,
            },
        },
        columns: [
            {
                data: "actions",
                name: "actions",
                title: "Ações",
                orderable: false,
                searchable: false,
                className: "text-center no-wrap",
                width: "5%",
            },
            {
                data: "CD_EMPRESA",
                name: "CD_EMPRESA",
                className: "no-wrap text-center",
                title: "Emp",
                width: "5%",
            },
            {
                data: "NR_LOTE",
                name: "NR_LOTE",
                title: "Lote",
                className: "no-wrap text-center",
                width: "5%",
            },
            {
                data: "DSCONTROLELOTEPCP",
                name: "DSCONTROLELOTEPCP",
                title: "Descrição",
                className: "no-wrap text-center",
                width: "15%",
            },
            {
                data: "DTPRODUCAO",
                name: "DTPRODUCAO",
                title: "Produção",
                className: "no-wrap text-center",
                render: function (data, type, row) {
                    if (data) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                    return "";
                },
                width: "10%",
            },
            {
                data: "QTDE_TOT_LOTE",
                name: "QTDE_TOT_LOTE",
                title: "Qtde",
                className: "no-wrap text-center",
                width: "10%",
            },
            {
                data: "QTDE_EM_PROD",
                name: "QTDE_EM_PROD",
                title: "Em.produção",
                className: "no-wrap text-center",
                className: "no-wrap text-center",
                width: "10%",
            },
            {
                data: "QTDE_SEMEXAME",
                name: "QTDE_SEMEXAME",
                title: "Sem.Exame",
                className: "no-wrap text-center",
                width: "10%",
                visible: false,
            },
        ],
        drawCallback: function (settings) {
            var api = this.api();
            var data = api.rows().data();

            //busca a quantidade de lotes
            let totalLotes = data.length;

            //atualiza o card de quantidade de lotes
            $("#lotes").text(totalLotes);
        },
    });
});

$(document).on("click", ".btn-pneus-lote", function () {
    let lote = $(this).data("lote");
    let empresa = $(this).data("empresa");

    $(".modal-title-lote").text(`Pneus do Lote ${lote}`);

    $("#table-pneus-lote-pcp").DataTable({
        pageLength: 100,
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.detalhesPneusLotePcp,
            type: "POST",
            data: {
                _token: window.routes.token,
                lote: lote,
                empresa: empresa,
            },
        },
        columns: [
            {
                data: "IDEMPRESA",
                name: "IDEMPRESA",
                title: "Emp.",
                className: "no-wrap text-center",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
                className: "no-wrap text-center",
            },
            {
                data: "NR_ORDEM",
                name: "NR_ORDEM",
                title: "Ordem",
                className: "no-wrap text-center",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Serviço",
                className: "no-wrap text-center",
            },
            {
                data: "STORDEM",
                name: "STORDEM",
                title: "Status Ordem",
                className: "no-wrap text-center",
            }
        ],
    });

    $("#modal-pneus-lote-pcp").modal("show");
});
