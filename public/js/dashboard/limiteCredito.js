function initTableLimiteCredito(route) {
    $("#tabela-limite-credito").DataTable({
        processing: false,
        serverSide: false,
        searching: true,
        // responsive: true,
        pageLength: 50,
        paging: true,
        // fixedHeader: true,
        scrollY: 400,
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
            },            
            {
                data: "VL_USADO",
                title: "Vl Usado",                
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
            {
                data: "VL_CREDITO",
                title: "Vl Credito",                
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
            {
                data: "DISPONIVEL",
                title: "Vl Disponível",                
                render: $.fn.dataTable.render.number(".", ",", 2, "R$ "),
            },
        ],
    });
}
