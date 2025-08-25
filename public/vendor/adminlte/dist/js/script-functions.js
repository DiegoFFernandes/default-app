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
                            phone: item.NR_CELULAR                            
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
}

function initDateRangePicker(daterangeSelector = '#daterange') {
    let inicioData = 0;
    let fimData = 0;

    const $daterange = $(daterangeSelector);

    $daterange.on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        inicioData = picker.startDate.format('MM/DD/YYYY');
        fimData = picker.endDate.format('MM/DD/YYYY');
    });

    $daterange.on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        inicioData = 0;
        fimData = 0;
    });

    return {
        getInicio: () => inicioData,
        getFim: () => fimData
    };
}

// Configura os detalhes da linha no DataTable
function configurarDetalhesLinha(selector, options) {
                $(document).on('click', selector, function() {
                    const tr = $(this).closest('tr');
                    const table = tr.closest('table');
                    const tableId = table.attr('id');
                    const row = $('#' + tableId).DataTable().row(tr);

                    const data = row.data();

                    const tableChildId = options.idPrefixo + (options.idCampo ? data[options.idCampo] : data
                        .ID);

                    if (row.child.isShown()) { // Se a linha já está expandida
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).find('i').removeClass(options.iconeMenos).addClass(options.iconeMais);
                    } else { // Se a linha não está expandida
                        row.child(options.templateFn(data)).show();
                        options.initFn(tableChildId, data);
                        tr.addClass('shown');
                        $(this).find('i').removeClass(options.iconeMais).addClass(options.iconeMenos);
                        tr.next().find('td').addClass('no-padding');
                    }
                });
            }
