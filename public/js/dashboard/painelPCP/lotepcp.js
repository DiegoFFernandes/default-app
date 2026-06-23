$(document).on("shown.bs.tab", 'a[href="#painel-lotesPCP"]', function () {
    $("#lote-pcp").DataTable({
        pageLength: 100,
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.getLotePcp,
            type: "POST",
            data: {
                _token: window.routes.token,
            },
        },
        columns: [
            {
                data: "actions",
                name: "actions",
                title: "Ações",
                orderable: false,
                searchable: false,
                className: "text-center no-wrap",
                width: "5%",
            },
            {
                data: "CD_EMPRESA",
                name: "CD_EMPRESA",
                className: "no-wrap text-center",
                title: "Emp",
                width: "5%",
            },
            {
                data: "NR_LOTE",
                name: "NR_LOTE",
                title: "Lote",
                className: "no-wrap text-center",
                width: "5%",
            },
            {
                data: "DSCONTROLELOTEPCP",
                name: "DSCONTROLELOTEPCP",
                title: "Descrição",
                className: "no-wrap text-center",
                width: "15%",
            },
            {
                data: "DTPRODUCAO",
                name: "DTPRODUCAO",
                title: "Data",
                className: "no-wrap text-center",
                render: function (data, type, row) {
                    if (data) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                    return "";
                },
                width: "10%",
            },
            {
                data: "QTDE_TOT_LOTE",
                name: "QTDE_TOT_LOTE",
                title: "Qtde",
                className: "no-wrap text-center",
                width: "10%",
            },
            {
                data: "QTDE_EM_PROD",
                name: "QTDE_EM_PROD",
                title: "Em Produção",
                className: "no-wrap text-center",
                width: "10%",
            },
            {
                data: "QTDE_SEMEXAME",
                name: "QTDE_SEMEXAME",
                title: "Sem Exame",
                className: "no-wrap text-center",
                width: "10%",
                visible: false,
            },
        ],
        order: [[2, "asc"]],
        drawCallback: function (settings) {
            var api = this.api();
            var data = api.rows().data();

            //busca a quantidade de lotes
            let totalLotes = data.length;

            //atualiza o card de quantidade de lotes
            $("#lotes").text(totalLotes);
        },
    });

    $("#bandas-consumir").DataTable({
        pageLength: 100,
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.consumoEstoqueLoteMateriaPrima,
            type: "POST",
            data: {
                _token: window.routes.token,
            },
        },
        columns: [
            {
                data: "actions",
                name: "actions",
                title: "#",
                orderable: false,
                searchable: false,
                className: "text-center no-wrap",
                width: "5%",
            },
            {
                data: "IDEMPRESA",
                name: "IDEMPRESA",
                title: "Emp.",
                width: "1%",
                className: "no-wrap text-center",
            },
            {
                data: "QTD_PNEUS",
                name: "QTD_PNEUS",
                title: "Pneus",
                className: "no-wrap text-center",
                width: "1%",
            },
            {
                data: "DS_BANDA",
                name: "DS_BANDA",
                title: "Banda",
                width: "30%",
            },
            {
                data: "SG_UNIDMED",
                name: "SG_UNIDMED",
                title: "Unid.",
                width: "1%",
                className: "no-wrap text-center",
            },
            {
                data: "QT_CONSUMO",
                name: "QT_CONSUMO",
                title: "Consumo",
                width: "10%",
                className: "no-wrap text-center",
            },
            {
                data: "QT_ESTOQUE",
                name: "QT_ESTOQUE",
                title: "Estoque",
                width: "10%",
                className: "no-wrap text-center",
            },
        ],
        order: [[1, "asc"]],
    });
});

$(document).on("click", ".btn-ver-pneus-lote", function () {
    let lote = $(this).data("lote");
    let empresa = $(this).data("empresa");

    $("#cd_empresa_pneus_lote_pcp").val(empresa);
    $("#lote_pneus_lote_pcp").val(lote);

    $(".modal-title-lote").text(`Pneus do Lote: ${lote} - Empresa: ${empresa}`);

    $("#table-pneus-lote-pcp").DataTable({
        pageLength: -1,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"],
        ],
        processing: false,
        serverSide: false,
        destroy: true,
        autoWidth: false,
        scrollY: "400px",
        scrollCollapse: true,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.detalhesPneusLotePcp,
            type: "POST",
            data: {
                _token: window.routes.token,
                lote: lote,
                empresa: empresa,
            },
        },
        columns: [
            {
                data: null,
                width: "30px",
                className: "text-center",
                orderable: false,
                searchable: false,
                title: '<input type="checkbox" class="dt-select-all-lote-pcp" title="Selecionar todos" style="margin:0;">',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<input type="checkbox" class="dt-row-checkbox-lote-pcp" data-op="' + row.NR_ORDEM + '" aria-label="Selecionar linha" style="margin:0;">';
                    }
                    return '';
                },
            },
            {
                data: "IDEMPRESA",
                name: "IDEMPRESA",
                title: "Emp.",
                className: "no-wrap text-center",
               
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
                className: "no-wrap text-center",               
            },
            {
                data: "NR_PEDIDO",
                name: "NR_PEDIDO",
                title: "Pedido",
                className: "no-wrap text-center",
               
            },
            {
                data: "NR_OP",
                name: "NR_OP",
                title: "Ordem",
                className: "no-wrap text-center",
               
            },
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Serviço",
                className: "no-wrap text-center",               
            },
            {
                data: "STORDEM",
                name: "STORDEM",
                title: "Status",
                className: "no-wrap text-center",
               
            },
        ],
        order: [[1, "asc"]],
    });

    $("#modal-pneus-lote-pcp").modal("show");
});

$(document).on("click", ".btn-banda-sem-associacao", function () {
    $("#modal-bandas-sem-associacao").modal("show");

    $("#table-bandas-sem-associacao").DataTable({
        pageLength: 100,
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        language: {
            url: window.routes.languageDatatables,
        },
        ajax: {
            url: window.routes.bandasSemAssociacao,
            type: "POST",
            data: {
                _token: window.routes.token,
                empresa: empresa,
            },
        },
        columns: [
            {
                data: "DS_ITEM",
                name: "DS_ITEM",
                title: "Serviço",
                className: "no-wrap",
            },
            {
                data: "NRPERIMETROMAX",
                name: "NRPERIMETROMAX",
                title: "Perímetro",
                className: "no-wrap text-center",
            },
        ],
    });
});

$(document).on("click", ".btn-banda-com-associacao", function () {
    Swal.fire({
        title: "Banda Associada",
        text: "Tudo ok. Esse item está associado a uma banda!",
        icon: "success",
        confirmButtonText: "Fechar",
    });
});

//Remover pneus do lote PCP
$(document).on(
    "click",
    ".btn-remover-todos-pneus-lote-pcp-detalhes",
    function () {
        let cd_empresa = $("#cd_empresa_pneus_lote_pcp").val();
        let lote = $("#lote_pneus_lote_pcp").val();

        let tabelaPneusLotePcp = $("#table-pneus-lote-pcp").DataTable();
        let tabelaLotePcp = $("#lote-pcp").DataTable();
        let bandaConsumir = $("#bandas-consumir").DataTable();

        let selectedOpsRemover = [];
        $(tabelaPneusLotePcp.table().container()).find('.dt-row-checkbox-lote-pcp').filter(function() {
            return this.checked;
        }).each(function() {
            selectedOpsRemover.push(String($(this).data('op')));
        });
        let selectedRows = tabelaPneusLotePcp.rows().data().toArray().filter(function(row) {
            return selectedOpsRemover.includes(String(row.NR_ORDEM));
        });

        if (selectedRows.length === 0) {
            Swal.fire({
                title: "Nenhum pneu selecionado",
                text: "Por favor, selecione os pneus que deseja remover.",
                icon: "warning",
                confirmButtonText: "Fechar",
            });
            return;
        }

        let possuiFinalizados = false;

        selectedRows.forEach(function (rowData) {
            if (
                rowData.CHAR_STORDEM === "F" ||
                rowData.ST_EXAMEINICIAL === "S"
            ) {
                possuiFinalizados = true;
                return false;
            }
        });

        if (possuiFinalizados) {
            Swal.fire({
                title: "Atenção",
                text: "Não é possível remover os pneus selecionados, pois um ou mais já foram finalizados ou passaram do exame inicial.",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-warning btn-sm",
                },
            });
            return; // interrompe a função inteira
        }

        confirmRemoverPneusLote(selectedRows, tabelaPneusLotePcp, [
            tabelaLotePcp,
            bandaConsumir,
        ]);
    },
);

//Transferir pneus do lote PCP para outro lote
$(document).on(
    "click",
    ".btn-transferir-todos-pneus-lote-pcp-detalhes",
    function () {
        let cd_empresa = $("#cd_empresa_pneus_lote_pcp").val();
        let lote = $("#lote_pneus_lote_pcp").val();

        let tabelaPneusLotePcp = $("#table-pneus-lote-pcp").DataTable();
        let tabelaLotePcp = $("#lote-pcp").DataTable();
        let bandaConsumir = $("#bandas-consumir").DataTable();

        let selectedOpsTransferir = [];
        $(tabelaPneusLotePcp.table().container()).find('.dt-row-checkbox-lote-pcp').filter(function() {
            return this.checked;
        }).each(function() {
            selectedOpsTransferir.push(String($(this).data('op')));
        });
        let selectedRows = tabelaPneusLotePcp.rows().data().toArray().filter(function(row) {
            return selectedOpsTransferir.includes(String(row.NR_ORDEM));
        });

        if (selectedRows.length === 0) {
            Swal.fire({
                title: "Nenhum pneu selecionado",
                text: "Por favor, selecione os pneus que deseja transferir.",
                icon: "warning",
                confirmButtonText: "Fechar",
            });
            return;
        }

        let possuiFinalizados = false;

        selectedRows.forEach(function (rowData) {
            if (rowData.CHAR_STORDEM === "F") {
                possuiFinalizados = true;
                return false; // interrompe o loop
            }
        });

        if (possuiFinalizados) {
            Swal.fire({
                title: "Atenção",
                text: "Não é possível transferir os pneus selecionados, pois um ou mais já foram finalizados.",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-warning btn-sm",
                },
            });
            return; // interrompe a função inteira
        }

        $("#empresa-lote-pcp-transf").val(cd_empresa);

        let data = {
            cd_empresa: cd_empresa,
        };

        inicializaSelect2Lista({
            route: window.routes.getListLotePCPEmProducao,
            selectId: "#lote-pcp-novo-transf",
            placeholder: "Selecione o Novo Lote",
            modalParent: "#modal-transferir-lote-pcp",
            textField: "DSLOTEPCP",
            valueField: "NR_LOTE",
            additionalData: data,
        });

        $("#modal-transferir-lote-pcp")
            .data("tabelaPrincipal", "#table-pneus-lote-pcp")

            .data("tabelaSecundaria", ["#lote-pcp", "#bandas-consumir"])

            .modal("show");
    },
);

// Select all — #table-pneus-lote-pcp
$(document).on('click', '.dt-select-all-lote-pcp', function(e) {
    e.stopPropagation();
    var checked = this.checked;
    var tabela = $("#table-pneus-lote-pcp").DataTable();
    tabela.rows().nodes().to$().find('.dt-row-checkbox-lote-pcp').prop('checked', checked);
});

// Checkbox individual — #table-pneus-lote-pcp
$(document).on('click', '.dt-row-checkbox-lote-pcp', function(e) {
    e.stopPropagation();
    var tabela = $("#table-pneus-lote-pcp").DataTable();
    var total = tabela.rows().count();
    var checkedCount = $(tabela.table().container()).find('.dt-row-checkbox-lote-pcp').filter(function() { return this.checked; }).length;
    $('.dt-select-all-lote-pcp').prop('checked', total > 0 && checkedCount === total);
});
