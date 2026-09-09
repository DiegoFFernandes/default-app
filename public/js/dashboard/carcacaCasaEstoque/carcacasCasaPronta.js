var tableCarcacaPronta;
var itensCarcacaProntaTable = [];
var selectedProntasIds = new Set();

function updateProntasBadge() {
    var count = selectedProntasIds.size;
    var $badge = $(".carcacas-prontas-count-badge");
    if (count > 0) {
        $badge.text(count + " selecionada" + (count > 1 ? "s" : "")).show();
    } else {
        $badge.hide();
    }
}

// Mantem o "selecionar todos" do cabecalho coerente com o que esta marcado
// nas linhas atualmente visiveis (respeitando filtro).
function syncSelectAllProntas() {
    if (!tableCarcacaPronta) return;
    var rows = tableCarcacaPronta.rows({ search: "applied" }).data();
    var total = rows.length;
    var allSelected = total > 0;
    rows.each(function (row) {
        if (!selectedProntasIds.has(String(row.NR_ORDEM))) {
            allSelected = false;
        }
    });
    $(".dt-select-all-prontas").prop("checked", allSelected);
}

function getSelectedProntasRows() {
    return itensCarcacaProntaTable.filter(function (row) {
        return selectedProntasIds.has(String(row.NR_ORDEM));
    });
}

$(document).on("click", "#tab-carcaca-pronta", function () {
    $("#table-carcacas-prontas").DataTable().destroy();
    selectedProntasIds.clear();
    updateProntasBadge();

    tableCarcacaPronta = $("#table-carcacas-prontas").DataTable({
        processing: false,
        serverSide: false,
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        pageLength: -1,
        lengthMenu: [
            [-1, 25, 50, 100],
            ["Todos", 25, 50, 100],
        ],
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getCarcacaCasaProntas,
            beforeSend: function () {
                window._swalProntaTimer = setTimeout(function () {
                    Swal.fire({
                        title: 'Carregando carcaças prontas...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                }, 400);
            },
            complete: function () {
                clearTimeout(window._swalProntaTimer);
                Swal.close();
            },
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
                orderable: false,
                searchable: false,
                className: "text-center",
                title: '<input type="checkbox" class="dt-select-all-prontas" aria-label="Selecionar todos">',
                render: function (data, type, row) {
                    if (type === "display") {
                        var checked = selectedProntasIds.has(String(row.NR_ORDEM))
                            ? " checked"
                            : "";
                        return (
                            '<input type="checkbox" class="dt-row-checkbox-prontas" data-id="' +
                            row.NR_ORDEM +
                            '" aria-label="Selecionar linha"' +
                            checked +
                            ">"
                        );
                    }
                    return "";
                },
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

    tableCarcacaPronta.on("draw", syncSelectAllProntas);
});

// Selecionar todos — opera apenas nas linhas visiveis (filtro ativo)
$(document).on("click", ".dt-select-all-prontas", function (e) {
    e.stopPropagation();
    var checked = this.checked;
    var rows = tableCarcacaPronta.rows({ search: "applied" });
    rows.data().each(function (row) {
        var id = String(row.NR_ORDEM);
        if (checked) {
            selectedProntasIds.add(id);
        } else {
            selectedProntasIds.delete(id);
        }
    });
    rows.nodes().to$().find(".dt-row-checkbox-prontas").prop("checked", checked);
    updateProntasBadge();
});

// Checkbox individual
$(document).on("click", ".dt-row-checkbox-prontas", function (e) {
    e.stopPropagation();
    var id = String($(this).data("id"));
    if (this.checked) {
        selectedProntasIds.add(id);
    } else {
        selectedProntasIds.delete(id);
    }
    updateProntasBadge();
    syncSelectAllProntas();
});

$(document).on("click", "#btn-reservar-carcaca", function () {
    let config = {
        swalText: "Por favor, selecione pelo menos uma carcaça para reservar.",
    };

    reservarCarcacaPronta(getSelectedProntasRows(), "S", config);
});

$(document).on("click", "#btn-cancelar-reserva-carcaca", function () {
    let config = {
        swalText:
            "Por favor, selecione pelo menos uma carcaça para cancelar a reservar.",
    };
    reservarCarcacaPronta(getSelectedProntasRows(), "N", config);
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

    let NrOrdens = selectedRows.map(function (rowData) {
        return rowData.NR_ORDEM;
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
                selectedProntasIds.clear();
                updateProntasBadge();
                $(".dt-select-all-prontas").prop("checked", false);
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
