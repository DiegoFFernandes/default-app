tablePneusNovos = $("#table-pneus-novos").DataTable({
    processing: false,
    serverSide: false,
    scrollX: true,
    language: {
        url: window.routes.languageDatatables,
    },
    pagingType: "simple",
    ajax: {
        url: window.routes.getPneusNovos,
        beforeSend: function () {
            window._swalPneusNovosTimer = setTimeout(function () {
                Swal.fire({
                    title: "Carregando pneus novos...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            }, 400);
        },
        complete: function () {
            clearTimeout(window._swalPneusNovosTimer);
            Swal.close();
        },
        dataSrc: function (response) {
            $("#total-pneus-novos").text(response.total_pneus_novos);

            $("#accordionResumoPneusNovos")
                .html(
                    initAccordion(
                        response.accordion_data_local_marca,
                        "accordionResumoPneusNovos",
                    ),
                )
                .removeClass("d-none");

            itensPneusNovosTable = response.datatable.data;
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
            .column(2, { search: "applied" })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        $(api.column(2).footer()).html(total);
    },
});
