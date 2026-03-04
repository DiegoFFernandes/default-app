var tableCarcacaPronta;
var itensCarcacaProntaTable = [];

$(document).on("click", "#tab-carcaca-pronta-terceiros", function () {
    $("#table-carcacas-prontas-terceiros").DataTable().destroy();

    tableCarcacaPronta = $("#table-carcacas-prontas-terceiros").DataTable({
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
            url: window.routes.getCarcacaCasaProntasTerceiros,
            dataSrc: function (response) {
                $("#total-carcacas-prontas-terceiros").text(
                    response.total_carcacas_prontas,
                );

                $("#accordionResumoCarcacaProntaTerceiros")
                    .html(
                        initAccordion(
                            response.accordion_data_local_marca,
                            "accordionResumoCarcacaProntaTerceiros",
                        ),
                    )
                    .removeClass("d-none");

                itensCarcacaProntaTable = response.datatable.data;
                return response.datatable.data;
            },
        },
        columns: [
            {
                data: "LOCAL_ESTOQUE",
                name: "LOCAL_ESTOQUE",
                title: "Local",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                className: "text-nowrap",
                title: "Item",
            },
        ],
    });
});
