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
        },
        language: {
            url: window.routes.languageDatatables,
        },
        pagingType: "simple",
        ajax: {
            url: window.routes.getCarcacaCasaProntas,
            dataSrc: function (response) {
                $("#total-carcacas-prontas").text(
                    response.total_carcacas_prontas,
                );

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
                width: "1%",
                render: DataTable.render.select(),
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

$(document).on("click", "#btn-reservar-carcaca", function () {
    let selectedRows = tableCarcacaPronta.rows({ selected: true }).data();
    let config = {
        swalText:
            "Por favor, selecione pelo menos uma carcaça para reservar.",
    };

    reservarCarcacaPronta(selectedRows, "S", config);
});

$(document).on("click", "#btn-cancelar-reserva-carcaca", function () {
    let selectedRows = tableCarcacaPronta.rows({ selected: true }).data();
    let config = {
        swalText:
            "Por favor, selecione pelo menos uma carcaça para cancelar a reservar.",
    };
    reservarCarcacaPronta(selectedRows, "N", config);
});

function reservarCarcacaPronta(selectedRows, st_Reserva, config) {
    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhuma carcaça selecionada",
            text: config.swalText,
        });
        return;
    }

    let NrOrdens = [];

    selectedRows.each(function (rowData) {
        NrOrdens.push(rowData.NR_ORDEM);
    });

    $.ajax({
        url: window.routes.reservarCarcacaCasaPronta,
        method: "GET",
        data: {
            _token: window.routes.token,
            NR_ORDEM: NrOrdens,
            ST_RESERVA: st_Reserva,
        },
        beforeSend: function () {
            $(".loading-card").removeClass("invisible");
        },
        success: function (response) {
            $(".loading-card").addClass("invisible");
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    text: response.message,
                    showConfirmButton: true,
                    confirmButtonText: "Ok",
                });
                tableCarcacaPronta.ajax.reload();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro ao reservar carcaças.",
                    text: response.errors
                        ? response.message
                        : "Ocorreu um erro inesperado.",
                });
            }
        },
        error: function (xhr) {
            $(".loading-card").addClass("invisible");
            Swal.fire({
                icon: "error",
                title: "Erro ao reservar carcaças.",
                text: xhr.responseText,
            });
        },
    });
}
