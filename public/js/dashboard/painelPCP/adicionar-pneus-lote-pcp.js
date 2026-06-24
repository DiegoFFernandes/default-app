$(document).on("click", ".btn-adicionar-pneus-lote", function () {
    let cd_empresa = $(this).data("empresa");
    let nr_lote = $(this).data("lote");

    let data = {
        cd_empresa: cd_empresa,
    };

    inicializaSelect2Lista({
        route: window.routes.getListPedidosSemPCP,
        selectId: "#select-pedidopneu",
        placeholder: "Selecione um pedido",
        modalParent: "#modal-adicionar-pneus-lote-pcp",
        textField: "NR_PEDIDO",
        valueField: "NR_PEDIDO",
        additionalData: data,
    });

    inicializaSelect2Lista({
        route: window.routes.getListOrdensProducaoSemPCP,
        selectId: "#select-ordens-producao",
        placeholder: "Selecione uma ordem de produção",
        modalParent: "#modal-adicionar-pneus-lote-pcp",
        textField: "NR_ORDEM",
        valueField: "NR_ORDEM",
        additionalData: data,
    });

    $(".modal-title-adicionar-pneus-lote-pcp").html(
        "Adicionar Pneus ao Lote: <b>" + nr_lote + "</b>",
    );
    $("#empresa-adicionar-lote-pcp").val(cd_empresa);
    $("#nrlote-adicionar-lote-pcp").val(nr_lote);

    $("#div-tabela-adicionar-pneus-lote-pcp").hide();

    $("#tabela-adicionar-pneus-lote-pcp").DataTable().clear().destroy();

    $("#modal-adicionar-pneus-lote-pcp").modal("show");
});

$(document).on("submit", "#form-adicionar-pneus-lote-pcp", function (e) {
    e.preventDefault();

    let dataForms = $(this).serialize();

    $("#tabela-adicionar-pneus-lote-pcp").DataTable({
        paging: false,
        pagingType: "simple",
        destroy: true,
        processing: false,
        serverSide: false,
        scrollY: "400px",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getListPneusLoteSemPCP,
            type: "GET",
            data: {
                _token: window.routes.token,
                dados: dataForms,
            },
        },
        columns: [
            {
                data: null,
                width: "30px",
                className: "text-center",
                orderable: false,
                searchable: false,
                title: '<input type="checkbox" class="dt-select-all-pcp-lote" title="Selecionar todos" style="margin:0;">',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<input type="checkbox" class="dt-row-checkbox-pcp-lote" data-ordem="' + row.NR_ORDEM + '" aria-label="Selecionar linha" style="margin:0;">';
                    }
                    return '';
                },
            },
            {
                data: "NR_PEDIDO",
                name: "NR_PEDIDO",
                title: "Pedido",
                width: "5%",
                className: "text-center",
            },
            {
                data: "ID",
                name: "ID",
                title: "Ordem",
                width: "5%",
                className: "text-center no-wrap",
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Serviço",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "DTEMISSAO",
                name: "DTEMISSAO",
                title: "Emissão",
                render: function (data, type, row) {
                    return moment(data).format("DD/MM/YYYY");
                },
            },
        ],
        order: [[10, "asc"]],
    });

    selectedPcpLoteIds.clear();
    $('#tabela-adicionar-pneus-lote-pcp .dt-select-all-pcp-lote').prop('checked', false);
    $('#pcp-count-badge-adicionar').hide();

    $("#div-tabela-adicionar-pneus-lote-pcp").show();
});

var selectedPcpLoteIds = new Set();

function updateAdicionarLoteBadge() {
    var count = selectedPcpLoteIds.size;
    var $badge = $('#pcp-count-badge-adicionar');
    if (count > 0) {
        $badge.text(count + ' selecionado' + (count > 1 ? 's' : '')).show();
    } else {
        $badge.hide();
    }
}

// Select all — opera apenas nas linhas visíveis (filtro ativo)
$(document).on('click', '.dt-select-all-pcp-lote', function(e) {
    e.stopPropagation();
    var tabela = $("#tabela-adicionar-pneus-lote-pcp").DataTable();
    var checked = this.checked;
    var filteredRows = tabela.rows({ search: 'applied' });
    // Desmarca as linhas filtradas do Set antes de recalcular
    filteredRows.data().each(function(row) { selectedPcpLoteIds.delete(row.NR_ORDEM); });
    filteredRows.nodes().to$().find('.dt-row-checkbox-pcp-lote').prop('checked', checked);
    if (checked) {
        filteredRows.data().each(function(row) { selectedPcpLoteIds.add(row.NR_ORDEM); });
    }
    updateAdicionarLoteBadge();
});

// Checkbox individual
$(document).on('click', '.dt-row-checkbox-pcp-lote', function(e) {
    e.stopPropagation();
    var ordem = $(this).data('ordem');
    if (!ordem) return;
    if (this.checked) {
        selectedPcpLoteIds.add(ordem);
    } else {
        selectedPcpLoteIds.delete(ordem);
    }
    var tabela = $("#tabela-adicionar-pneus-lote-pcp").DataTable();
    var filteredCount = tabela.rows({ search: 'applied' }).count();
    var checkedCount = $(tabela.table().container()).find('.dt-row-checkbox-pcp-lote').filter(function() { return this.checked; }).length;
    $('.dt-select-all-pcp-lote').prop('checked', filteredCount > 0 && checkedCount === filteredCount);
    updateAdicionarLoteBadge();
});

$(document).on("click", "#btn-salvar-pneus-lote-pcp", function (e) {
    e.preventDefault();

    let bandaConsumir = $("#bandas-consumir").DataTable();

    if (selectedPcpLoteIds.size === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhum pneu selecionado",
            text: "Por favor, selecione pelo menos um pneu para adicionar ao lote.",
        });
        return;
    }

    let idsOrdens = Array.from(selectedPcpLoteIds);
    let cd_empresa = $("#empresa-adicionar-lote-pcp").val();
    let nr_lote = $("#nrlote-adicionar-lote-pcp").val();

    $.ajax({
        url: window.routes.salvarPneusLotePcp,
        type: "POST",
        data: {
            _token: window.routes.token,
            cd_empresa: cd_empresa,
            nr_lote: nr_lote,
            id_ordens: idsOrdens,
        },
        beforeSend: function () {
            Swal.fire({
                title: "Salvando pneus no lote...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", true)
                .text("Salvando...");
        },
        success: function (response) {
            $("#modal-adicionar-pneus-lote-pcp").modal("hide");
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", false)
                .text("Adicionar ao Lote");

            Swal.close();

            if (response.success) {
                $("#lote-pcp").DataTable().ajax.reload();
                setTimeout(() => {
                    bandaConsumir.ajax.reload();
                }, 500);
                Swal.fire({
                    icon: "success",
                    title: "Pneus adicionados",
                    text:
                        response.message ||
                        "Os pneus foram adicionados ao lote com sucesso.",
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erro ao adicionar pneus",
                    text:
                        response.message ||
                        "Ocorreu um erro ao adicionar os pneus ao lote.",
                });
            }
        },
        error: function (xhr) {
            Swal.close();
            $("#btn-salvar-pneus-lote-pcp")
                .prop("disabled", false)
                .text("Adicionar ao Lote");
            Swal.fire({
                icon: "error",
                title: "Erro ao adicionar pneus",
                text:
                    xhr.responseJSON.message ||
                    "Ocorreu um erro ao adicionar os pneus ao lote.",
            });
        },
    });
});
