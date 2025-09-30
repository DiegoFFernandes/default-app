function msgToastr(msg, classe) {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-bottom-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "3000",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };
    toastr[classe](msg);
}

function exportarParaExcel(
    dados,
    nomeArquivo = "dados.xlsx",
    nomeAba = "Planilha"
) {
    // Cria uma nova planilha a partir dos dados (array de objetos)
    const worksheet = XLSX.utils.json_to_sheet(dados);

    // Cria o workbook (arquivo Excel)
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, nomeAba);

    // Faz o download do arquivo
    XLSX.writeFile(workbook, nomeArquivo);
}

function initSelect2Pessoa(selector, routeUrl, modalSelector = null) {
    const $element = $(selector);

    $element.select2({
        placeholder: "Pessoa",
        theme: "bootstrap4",
        language: "pt-BR",
        width: "100%",
        allowClear: true,
        minimumInputLength: 2,
        dropdownParent: modalSelector ? $(modalSelector) : $(document.body),
        ajax: {
            url: routeUrl,
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.NM_PESSOA,
                            id: item.ID,
                            email: item.DS_EMAIL,
                            tipopessoa: item.CD_TIPOPESSOA,
                            phone: item.NR_CELULAR,
                        };
                    }),
                };
            },
            cache: true,
        },
    });

    $element.on("change", function (e) {
        const data = $(e.target).select2("data")[0] || {};
        $("#name").val(data.text || "");
        $("#email").val(data.email || "");
        $("#ds_tipopessoa").val(data.tipopessoa || "");
        $("#phone").val(data.phone || "");
        $("#cd_pessoa").val(data.id || "");
    });

    $element.on("select2:open", function () {
        $(".select2-search__field").css("width", "100%");
    });
}

function initDateRangePicker(
    daterangeSelector = "#daterange",
    inicioData = null,
    fimData = null
) {
    const $daterange = $(daterangeSelector);

    $daterange.inputmask({
        mask: ["99/99/9999 - 99/99/9999"],
    });

    let start = inicioData
        ? moment(inicioData, "DD.MM.YYYY")
        : moment().subtract(30, "days");
    let end = fimData
        ? moment(fimData, "DD.MM.YYYY")
        : moment().subtract(1, "days");

    let inicioSelecionado = start.format("DD.MM.YYYY");
    let fimSelecionado = end.format("DD.MM.YYYY");

    // Evita inicialização duplicada
    if ($daterange.data("daterangepicker")) {
        $daterange.data("daterangepicker").remove(); // remove instancia anterior se existir
    }

    $daterange.daterangepicker({
        showDropdowns: true,
        startDate: start,
        endDate: end,
        locale: {
            format: "DD/MM/YYYY",
            separator: " - ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "De",
            toLabel: "Até",
            customRangeLabel: "Personalizado",
            daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
            monthNames: [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro",
            ],
            firstDay: 0,
        },
        autoUpdateInput: false,
    });

    $daterange.on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
            picker.startDate.format("DD/MM/YYYY") +
                " - " +
                picker.endDate.format("DD/MM/YYYY")
        );
        inicioSelecionado = picker.startDate.format("DD.MM.YYYY");
        fimSelecionado = picker.endDate.format("DD.MM.YYYY");
    });

    $daterange.on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
        inicioSelecionado = 0;
        fimSelecionado = 0;
    });

    $daterange.attr("readonly", true);

    return {
        getInicio: () => inicioSelecionado,
        getFim: () => fimSelecionado,
    };
}

// Configura os detalhes da linha no DataTable
function configurarDetalhesLinha(selector, options) {
    $(document).on("click", selector, function () {
        const tr = $(this).closest("tr");
        const table = tr.closest("table");
        const tableId = table.attr("id");
        const row = $("#" + tableId)
            .DataTable()
            .row(tr);

        const data = row.data();

        console.log(data);

        const tableChildId =
            options.idPrefixo +
            (options.idCampo ? data[options.idCampo] : data.ID);

        if (row.child.isShown()) {
            // Se a linha já está expandida
            row.child.hide();
            tr.removeClass("shown");
            $(this)
                .find("i")
                .removeClass(options.iconeMenos)
                .addClass(options.iconeMais);
        } else {
            // Se a linha não está expandida
            row.child(options.templateFn(data)).show();
            options.initFn(tableChildId, data, options.routes);
            tr.addClass("shown");
            $(this)
                .find("i")
                .removeClass(options.iconeMais)
                .addClass(options.iconeMenos);
            tr.next().find("td").addClass("no-padding");
        }
    });
}
function formatDate(value) {
    if (!value) return "";
    const date = new Date(value);
    return (
        date.toLocaleDateString("pt-BR") +
        " " +
        date.toLocaleTimeString("pt-BR")
    );
}
function formatarValorBR(valor) {
    const numero = Number(valor);

    if (isNaN(numero)) {
        return "0,00";
    }

    return numero.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
