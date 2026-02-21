var tableCarcacaPronta;
var itensCarcacaProntaTable = [];

$(document).on("click", "#tab-carcaca-pronta", function () {
    $("#table-carcacas-prontas").DataTable().destroy();

    tableCarcacaPronta = $("#table-carcacas-prontas").DataTable({
        processing: false,
        serverSide: false,
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
            url: window.routes.getCarcacaCasaProntas,
            dataSrc: function (response) {
                $("#total-carcacas-prontas").text(response.total_carcacas_prontas);

                $("#accordionResumoCarcacaProntaLocal")
                    .html(
                        initAccordion(
                            response.accordion_data_local_marca,
                            "accordionResumoCarcacaProntaLocal",
                        ),
                    )
                    .removeClass("d-none");

                itensCarcacaProntaTable = response.datatable.data;
                return response.datatable.data;
            },
        },
        columns: [
            {
                data: null,
                // width: "1%",
                defaultContent: "",
                className: "select-checkbox",
                orderable: false,
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
                title: "Reservar",
                // width: '10%',
                className: "text-center text-nowrap pl-1",
            },
            {
                data: "LOCAL_ESTOQUE",
                name: "LOCAL_ESTOQUE",
                title: "Local",
            },
            {
                data: "NR_COLETA",
                name: "NR_COLETA",
                title: "Coleta",
            },
            {
                data: "NR_ORDEM",
                name: "NR_ORDEM",
                title: "Ordem",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                className: "text-nowrap",
                title: "Item",
            },
            {
                data: "DSMODELO",
                name: "DSMODELO",
                title: "Modelo",
            },
            {
                data: "NRSERIE",
                name: "NRSERIE",
                title: "Série",
                className: "text-nowrap",
            },
            {
                data: "NRFOGO",
                name: "NRFOGO",
                title: "Fogo",
                visible: false,
                className: "text-nowrap",
            },
            {
                data: "NRDOT",
                name: "NRDOT",
                title: "Nr Dot",
                
            },
        ],
        columnDefs: [
            {
                targets: [1, 2, 3, 4, 7, 8, 9],
                className: "text-center",
            },
        ],
    });
});
