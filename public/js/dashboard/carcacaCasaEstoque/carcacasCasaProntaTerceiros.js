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
            beforeSend: function () {
                window._swalTerceirosTimer = setTimeout(function () {
                    Swal.fire({
                        title: 'Carregando prontos em terceiros...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                }, 400);
            },
            complete: function () {
                clearTimeout(window._swalTerceirosTimer);
                Swal.close();
            },
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
            {
                data: "QTD",
                name: "QTD",
                title: "Quantidade",
            },
        ],
        footerCallback: function () {
            var api = this.api();

            var intVal = function (i) {
                return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
            };

            var total = api
                .column(2, {search: 'applied'})
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(2).footer()).html(total);
        },
    });
});
