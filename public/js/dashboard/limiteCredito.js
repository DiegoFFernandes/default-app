function initTableLimiteCredito(route) {
    $("#tabela-limite-credito").DataTable({
        processing: false,
        serverSide: false,
        searching: true,
        responsive: true,
        paging: true,
        pagingType: "simple",
        language: {
            url: route['language_datatables'],
        },
        ajax: {
            url: route['limite_credito'],
            type: "GET",
        },
        columns: [
            {
                data: "NM_PESSOA",
                title: "Cliente",
                className: "text-left",
                responsivePriority: 1,
            },
            {
                data: "VL_NOTA",
                title: "Vl Notas",

                responsivePriority: 10000,
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
            {
                data: "VL_USADO",
                title: "Vl Usado",

                responsivePriority: 10000,
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
            {
                data: "VL_CREDITO",
                title: "Vl Credito",

                responsivePriority: 10000,
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
            {
                data: "DISPONIVEL",
                title: "Vl Disponível",

                responsivePriority: 10000,
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
        ],
    });
}
