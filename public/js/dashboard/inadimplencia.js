var inadGerente = null;
var inadMeses = null;
var total = 0;
var atrasados = 0;
var inadimplencia = 0;
var cartorio = 0;
var qtd_titulos_cartorio = 0;
var hierarquia = null;
var carteira60dias = 0;
var carteiraMaior60dias = 0;

var carteira60diasSupervisor = [];
var carteiraMaior60diasSupervisor = [];
var cartorioSupervisor = [];

const datesCache = {};
const MESES_ABREV = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
const pizzaCharts = {};

var supervisoresObj = {};

function tentarProcessar() {
    if (inadGerente && inadMeses) {
        inadGerente.forEach((gerente, gIndex) => {
            var pc_atrasados_gerente = 0;
            var pc_inadimplencia_gerente = 0;
            var pc_cartorio_gerente = 0;

            var pc_atrasados_supervisor = 0;
            var pc_inadimplencia_supervisor = 0;
            var pc_cartorio_supervisor = 0;

            if (hierarquia !== null) {
                if (hierarquia[gerente.nome].nome === gerente.nome) {
                    const carteira60diasGerente =
                        hierarquia[gerente.nome]["carteira60diasGerente"];
                    const carteiraMaior60diasGerente =
                        hierarquia[gerente.nome]["carteiraMaior60diasGerente"];
                    const cartorioGerente =
                        hierarquia[gerente.nome]["cartorio"];

                    if (carteira60diasGerente != 0) {
                        total = carteira60diasGerente;
                        pc_atrasados_gerente =
                            (gerente.atrasados / total) * 100;
                    }
                    if (carteiraMaior60diasGerente != 0) {
                        total = carteiraMaior60diasGerente;
                        pc_inadimplencia_gerente =
                            (gerente.inadimplencia / total) * 100;
                    }
                    if (cartorioGerente != 0) {
                        total = gerente.atrasados + gerente.inadimplencia;
                        pc_cartorio_gerente = (gerente.cartorio / total) * 100;
                    }
                }

                // percorre os supervisores do gerente para preencher os dados dos supervisores
                // valor total que tinha a receber por supervisor
                supervisoresObj = Object.values(
                    hierarquia[gerente.nome].supervisores,
                ).reduce((acc, sup) => {
                    acc[sup.nome] = {
                        nome: sup.nome,
                        carteira60diasSupervisor: sup.carteira60diasSupervisor,
                        carteiraMaior60diasSupervisor:
                            sup.carteiraMaior60diasSupervisor,
                        cartorioSupervisor: sup.cartorio,
                    };
                    return acc;
                }, {});
            }

            $(`.pc_atrasados-gerente-${gIndex}`).html(
                `Atrasados: ${pc_atrasados_gerente.toFixed(2)}%`,
            );
            $(`.pc_inadimplencia-gerente-${gIndex}`).html(
                `Inadimplência: ${pc_inadimplencia_gerente.toFixed(2)}%`,
            );
            $(`.pc_cartorio-gerente-${gIndex}`).html(
                `Cartório: ${pc_cartorio_gerente.toFixed(2)}%`,
            );

            gerente.supervisores.forEach((sup, sIndex) => {
                if (supervisoresObj[sup.nome]) {
                    const carteira60diasSupervisor =
                        supervisoresObj[sup.nome].carteira60diasSupervisor;
                    const carteiraMaior60diasSupervisor =
                        supervisoresObj[sup.nome].carteiraMaior60diasSupervisor;
                    const cartorioSupervisor =
                        supervisoresObj[sup.nome].cartorioSupervisor;

                    if (carteira60diasSupervisor != 0 || sup.atrasados != 0) {
                        var pc_atrasados_supervisor = (
                            (sup.atrasados / carteira60diasSupervisor) * 100 ??
                            0
                        ).toFixed(2);
                    }

                    if (
                        carteiraMaior60diasSupervisor != 0 ||
                        sup.inadimplencia != 0
                    ) {
                        var pc_inadimplencia_supervisor = (
                            (sup.inadimplencia /
                                carteiraMaior60diasSupervisor) *
                                100 ?? 0
                        ).toFixed(2);
                    }

                    if (cartorioSupervisor != 0 || sup.cartorio != 0) {
                        var pc_cartorio_supervisor = (
                            (cartorioSupervisor /
                                (sup.atrasados + sup.inadimplencia)) *
                                100 ?? 0
                        ).toFixed(2);
                    }
                }

                $(`.pc_atrasados-supervisor-${gIndex}-${sIndex}`).html(
                    `Atrasados: ${pc_atrasados_supervisor ?? 0}%`,
                );
                $(`.pc_inadimplencia-supervisor-${gIndex}-${sIndex}`).html(
                    `Inadimplência: ${pc_inadimplencia_supervisor ?? 0}%`,
                );
                $(`.pc_cartorio-supervisor-${gIndex}-${sIndex}`).html(
                    `Cartório: ${pc_cartorio_supervisor ?? 0}%`,
                );
            });
        });
    }
}

// inicializa o accordion da inadimplência por gerente
function inadimplenciaGerente(tab, data, route, idAccordion, idCard) {
    $.ajax({
        url: route,
        method: "GET",
        data: {
            filtro: data,
            tab: tab,
        },
        beforeSend: function () {
            $("#" + idCard + " .loading-card").removeClass("invisible");
            $(".info-loading.loading-card").removeClass("invisible");

            $(".valorTotalGerente").text(`R$ 0`);
            $("#pc_atrasados").text(`0,00%`);
            $("#pc_inadimplencia").text(`0,00%`);
            $("#pc_cartorio").text(`0,00%`);

            $(".pc_atrasados-gerente").html(
                '<i class="fas fa-sync-alt fa-spin"></i>',
            );
            $(".pc_inadimplencia-gerente").html(
                '<i class="fas fa-sync-alt fa-spin"></i>',
            );

            $(".pc_cartorio-gerente").html(
                '<i class="fas fa-sync-alt fa-spin"></i>',
            );
        },
        success: function (data) {
            inadGerente = data;
            let valorTotalGerente = 0;
            let html = "";
            data.forEach((gerente, gIndex) => {
                valorTotalGerente += gerente.saldo;
                html += `
                    <div class="card gerente-card mb-2 border-0 shadow-sm">
                        <div class="card-header p-0" style="background:#2d3748; border-left:4px solid #4299e1; border-radius:4px 4px 0 0;">
                            <button class="btn btn-block text-white text-left accordion-item-header py-2 px-3"
                                    data-toggle="collapse" data-target="#sup-${gIndex}">
                                <span style="font-size:0.87rem; font-weight:600;">
                                    <i class="fas fa-user-tie mr-1" style="color:#90cdf4;"></i>
                                    ${gerente.nome}
                                    <small class="ml-2" style="color:#a0aec0; font-weight:400;">R$ ${formatarValorBR(gerente.saldo)}</small>
                                </span>
                                <span class="saldo">
                                    <span class="badge badge-pill badge-info badge-indicador pc_atrasados-gerente pc_atrasados-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    <span class="badge badge-pill badge-warning badge-indicador pc_inadimplencia-gerente pc_inadimplencia-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    <span class="badge badge-pill badge-purple badge-indicador pc_cartorio-gerente pc_cartorio-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                </span>
                            </button>
                        </div>
                        <div id="sup-${gIndex}" class="collapse">
                            <div class="card-body p-2" style="background:#f7fafc;">`;

                gerente.supervisores.forEach((sup, sIndex) => {
                    html += `<div class="supervisor-container mb-2">`;
                    html += `
                            <button class="btn btn-block text-left accordion-item-header py-2 px-3 bg-white border"
                                    style="border-left:4px solid #718096 !important; font-size:0.82rem; font-weight:600; border-radius:4px;"
                                    data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                <span>
                                    <i class="fas fa-shield-alt mr-1 text-secondary"></i>
                                    ${sup.nome}
                                    <small class="ml-2 text-muted" style="font-weight:400;">R$ ${formatarValorBR(sup.saldo)}</small>
                                </span>
                                <span class="saldo">
                                    <span class="badge badge-pill badge-info badge-indicador pc_atrasados-supervisor pc_atrasados-supervisor-${gIndex}-${sIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    <span class="badge badge-pill badge-warning badge-indicador pc_inadimplencia-supervisor pc_inadimplencia-supervisor-${gIndex}-${sIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    <span class="badge badge-pill badge-purple badge-indicador pc_cartorio-supervisor pc_cartorio-supervisor-${gIndex}-${sIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                </span>
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-1 ml-2">
                            `;

                    sup.vendedores.forEach((vend, vIndex) => {
                        html += `<div class="vendedor-container mb-1">`;
                        html += `
                                <button class="btn btn-block text-left accordion-item-header py-1 px-3 bg-white border mt-1"
                                        style="border-left:4px solid #17a2b8 !important; font-size:0.8rem; border-radius:4px;"
                                        data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                    <span>
                                        <i class="fas fa-user mr-1 text-info"></i>
                                        ${vend.nome}
                                        <small class="ml-2 text-muted">R$ ${formatarValorBR(vend.saldo)}</small>
                                    </span>
                                    <i class="fas fa-chevron-down text-muted" style="font-size:0.7rem;"></i>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse mt-1 ml-2">
                                <ul class="list-group list-group-flush">
                            `;

                        vend.clientes.forEach((detalhe) => {
                            html += `
                                <li class="list-group-item cliente-item px-2 py-2 mb-1"
                                    data-vencimento="${detalhe.DT_VENCIMENTO}"
                                    style="border-left:3px solid #e2e8f0; border-radius:3px;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span style="font-size:0.78rem; font-weight:600;">
                                            <i class="fas fa-building mr-1 text-muted" style="font-size:0.68rem;"></i>
                                            ${detalhe.PESSOA}
                                        </span>
                                        ${detalhe.VL_CARTORIO > 0 ? '<span class="badge badge-pill badge-purple" style="font-size:0.65rem;">Em Cartório</span>' : ""}
                                    </div>
                                    <div class="row no-gutters">
                                        <div class="col-6 pr-2">
                                            <table class="table table-sm table-borderless mb-0" style="font-size:0.72rem;">
                                                <tr>
                                                    <td class="text-muted p-0 pr-1" style="width:44%">Nota</td>
                                                    <td class="p-0 font-weight-bold">${detalhe.NR_DOCUMENTO}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted p-0 pr-1">Emissão</td>
                                                    <td class="p-0">${formatDateCobranca(detalhe.DT_LANCAMENTO)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted p-0 pr-1">Venc.</td>
                                                    <td class="p-0">${formatDateCobranca(detalhe.DT_VENCIMENTO)}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-6 pl-2 border-left">
                                            <table class="table table-sm table-borderless mb-0" style="font-size:0.72rem;">
                                                <tr>
                                                    <td class="text-muted p-0 pr-1" style="width:40%">Total</td>
                                                    <td class="p-0 font-weight-bold">R$ ${formatarValorBR(detalhe.VL_TOTAL)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted p-0 pr-1">Saldo</td>
                                                    <td class="p-0 text-success font-weight-bold">R$ ${formatarValorBR(detalhe.VL_SALDO)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted p-0 pr-1">Juros</td>
                                                    <td class="p-0 text-danger font-weight-bold">${formatarValorBR(detalhe.VL_JUROS)}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </li>
                                `;
                        });

                        html += `</ul></div>`;
                        html += `   </div>`;
                    });

                    html += `</div>`; // fecha Supervisor
                    html += `</div>`;
                });

                html += `</div></div></div>`; // fecha Gerente
            });

            $("#" + idAccordion).html(html);

            // Popula os selects de mês e dia
            const allDates = extrairDatasAccordion(data);
            datesCache[idAccordion] = allDates;
            popularSelectMes($("#select-venc-" + idAccordion), allDates);
            const $diaSelect = $("#select-dia-" + idAccordion);
            $diaSelect.val("").prop("disabled", true);
            popularSelectDia($diaSelect, allDates, null);

            $("#" + idCard + " .loading-card").addClass("invisible");
            $(".info-loading.loading-card").addClass("invisible");
            $(".valorTotalGerente").text(
                `R$ ${valorTotalGerente.toLocaleString()}`,
            );
            tentarProcessar();

            // Gráficos de pizza estáticos: % por saldo de gerente e supervisor
            const _gerenteItems = data.map(g => ({ nome: g.nome, valor: parseFloat(g.saldo) || 0 }));
            _pizzaStatic("pizza-g-" + idAccordion, _gerenteItems);

            const _supItems = [];
            data.forEach(g => (g.supervisores || []).forEach(s =>
                _supItems.push({ nome: s.nome, valor: parseFloat(s.saldo) || 0 })
            ));
            _barStatic("pizza-s-" + idAccordion, _supItems);
        },
    });
}

$(function () {
    let debounceTimer;

    function normalizeString(str) {
        if (!str) return "";
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }

    $(".card-input-busca").on("input", ".input-busca-cliente", function () {
        clearTimeout(debounceTimer);
        const input = $(this);

        const cardContainer = input.closest(".card-input-busca");
        const treeAccordionId = cardContainer.data("tree-accordion");
        const accordionContainer = $("#" + treeAccordionId);

        debounceTimer = setTimeout(() => {
            const termoBusca = normalizeString(input.val());

            // input vazio volta o accordion para o estado original
            if (termoBusca === "") {
                accordionContainer.find(".hidden").removeClass("hidden");
                accordionContainer.find(".collapse").collapse("hide");
                accordionContainer.find(".no-results-message").remove();
                return;
            }

            // filtra os clientes
            accordionContainer.find(".cliente-item").each(function () {
                const clienteAtual = $(this);
                const textoCliente = clienteAtual.text();
                const textoNormalizado = normalizeString(textoCliente);

                if (textoNormalizado.includes(termoBusca)) {
                    clienteAtual.removeClass("hidden"); // mostra o cliente
                } else {
                    clienteAtual.addClass("hidden"); // esconde o cliente
                }
            });
            const anyVisible =
                accordionContainer.find(".cliente-item:not(.hidden)").length >
                0;
            accordionContainer.find(".no-results-message").remove();

            // filtra os vendedores
            accordionContainer.find(".vendedor-container").each(function () {
                const vendedorAtual = $(this);
                if (
                    vendedorAtual.find(".cliente-item:not(.hidden)").length ===
                    0
                ) {
                    vendedorAtual.addClass("hidden"); // esconde o vendedor
                } else {
                    vendedorAtual.removeClass("hidden"); // mostra o vendedor
                }
            });

            //filtra os supervisores
            accordionContainer.find(".supervisor-container").each(function () {
                const supervisorAtual = $(this);
                if (
                    supervisorAtual.find(".vendedor-container:not(.hidden)")
                        .length === 0
                ) {
                    supervisorAtual.addClass("hidden"); // esconde o supervisor
                } else {
                    supervisorAtual.removeClass("hidden"); //mostra o supervisor
                }
            });

            // filtra os gerentes
            accordionContainer.find(".gerente-card").each(function () {
                const gerenteAtual = $(this);
                if (
                    gerenteAtual.find(".supervisor-container:not(.hidden)")
                        .length === 0
                ) {
                    gerenteAtual.addClass("hidden"); // esconde o gerente
                } else {
                    gerenteAtual.removeClass("hidden"); // mostra o gerente
                    gerenteAtual.find(".collapse").collapse("show");
                }
            });

            if (!anyVisible) {
                accordionContainer.append(
                    '<div class="no-results-message">Nenhum cliente encontrado.</div>',
                );
            }
        }, 250); //tempo para esperar o usuario parar de digitar
    });
});

// inicializa a tabela de meses relatorio de gerente
function initTableInadimplenciaMeses(
    tab,
    idTable,
    idModal,
    idAccordion,
    data,
    route,
) {
    if ($.fn.DataTable.isDataTable("#" + idTable)) {
        $("#" + idTable)
            .DataTable()
            .destroy();
    }

    $("#" + idTable).DataTable({
        processing: false,
        serverSide: false,
        searching: false,
        paging: false,
        language: {
            url: route["language_datatables"],
        },
        ajax: {
            url: route["tabela_mensal"],
            data: {
                filtro: data,
                tab: tab,
            },
            beforeSend: function () {
                $("#card-inadimplencia-meses .loading-card").removeClass(
                    "invisible",
                );
                $(".info-loading.loading-card").removeClass("invisible");
                $(".pc_atrasados-gerente").html(
                    '<i class="fas fa-sync-alt fa-spin"></i>',
                );
                $(".pc_inadimplencia-gerente").html(
                    '<i class="fas fa-sync-alt fa-spin"></i>',
                );
                $("#pc_atrasados").text(`0,00%`);
                $("#pc_inadimplencia").text(`0,00%`);
            },
            dataSrc: function (json) {
                // Salva as variáveis globais com os valores do backend
                atrasados = parseFloat(json.atrasados);
                inadimplencia = parseFloat(json.inadimplencia);
                cartorio = parseFloat(json.cartorio);
                carteira60dias = parseFloat(json.carteira60dias);
                carteiraMaior60dias = parseFloat(json.carteiraMaior60dias);
                qtd_titulos_cartorio = parseFloat(json.qtd_titulos_cartorio);

                hierarquia = json.hierarquia;
                return json.data;
            },
            complete: function () {
                $("#card-inadimplencia-meses .loading-card").addClass(
                    "invisible",
                );
                $(".info-loading.loading-card").addClass("invisible");
            },
        },

        columns: [
            {
                data: null,
                name: "action",
                orderable: false,
                searchable: false,
                width: "1%",
                className: "text-center",
                render: function () {
                    return `<button class="btn btn-sm btn-outline-primary details-control btn-detalhes" title="Ver clientes">
                                <i class="fas fa-eye"></i>
                            </button>`;
                },
            },
            {
                data: "MES_ANO",
                name: "MES_ANO",
                title: "Mês/Ano",
            },
            {
                data: "VL_DOCUMENTO",
                name: "VL_DOCUMENTO",
                title: "Total",
                visible: false,
            },
            {
                data: "VL_SALDO",
                name: "VL_SALDO",
                title: "Vencido",
                className: "text-right",
            },
            {
                data: "PC_INADIMPLENCIA",
                name: "PC_INADIMPLENCIA",
                title: "%",
                className: "text-right",
                render: function (data) {
                    const val = parseFloat(data);
                    let color = "#38a169";
                    if (val >= 5)  color = "#d69e2e";
                    if (val >= 10) color = "#e53e3e";
                    return `<span style="color:${color}; font-weight:700;">${formatarValorBR(data)}%</span>`;
                },
            },
        ],
        columnDefs: [
            {
                targets: [3],
                render: $.fn.dataTable.render.number(".", ",", 2),
            },
        ],
        createdRow: function (row, data) {
            const pc = parseFloat(data.PC_INADIMPLENCIA);
            if (pc >= 10) {
                $(row).css("background-color", "#fff5f5");
            } else if (pc >= 5) {
                $(row).css("background-color", "#fffff0");
            }
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            inadMeses = data;
            tentarProcessar();

            // Remove a formatação para fazer a soma
            var intVal = function (i) {
                return typeof i === "string"
                    ? i.replace(",", ".") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
            };

            // Total das colunas
            totalTotal = api
                .column(2)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            totalVencido = api
                .column(3)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            totalPercentual = api
                .column(4)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            qtdLinhas = api.column(4).data().length;

            var inadimplenciaPercentual =
                (inadimplencia / carteiraMaior60dias) * 100;
            var atrasadosPercentual = (atrasados / carteira60dias) * 100;

            $("#pc_inadimplencia").html(
                formatarValorBR(inadimplenciaPercentual) + "%",
            );
            $("#pc_atrasados").html(formatarValorBR(atrasadosPercentual) + "%");

            $("#vencidos").html(formatarValorBR(totalVencido));

            $("#total_carteira").html(formatarValorBR(totalTotal));

            $("#total_60_atrasados").html(formatarValorBR(carteira60dias));
            $("#vl_60_atrasados").html(formatarValorBR(atrasados));

            $("#total_maior_60_atrasados").html(
                formatarValorBR(carteiraMaior60dias),
            );
            $("#vl_maior_60_atrasados").html(formatarValorBR(inadimplencia));

            $("#vl_cartorio_protesto").html(formatarValorBR(cartorio));
            $("#pc_cartorio_protesto").html(
                formatarValorBR((cartorio / totalVencido) * 100) + "%",
            );

            $("#qtd_cartorio_protesto").html(qtd_titulos_cartorio);

            // Atualiza o footer do DataTable
            $(api.column(2).footer()).html(formatarValorBR(totalTotal));
            $(api.column(3).footer()).html(formatarValorBR(totalVencido));
            $(api.column(4).footer()).html(
                formatarValorBR(totalPercentual / qtdLinhas) + "%",
            );
        },
    });

    $("#" + idTable + " tbody").on("click", ".details-control", function () {
        var tr = $(this).closest("tr");
        var row = $("#" + idTable)
            .DataTable()
            .row(tr);

        $("#" + idAccordion).empty(); // Limpa antes

        $.ajax({
            type: "GET",
            url: route["modal_clientes"],
            data: {
                mes: row.data().MES,
                ano: row.data().ANO,
                tab: tab,
                filtro: {
                    filtro_cartorio: 0,
                },
            },
            dataType: "json",
            beforeSend: function () {
                Swal.fire({
                    title: "Carregando detalhes...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            success: function (response) {
                Swal.close();
                data = Object.values(response);

                $(".modal-table-cliente-label").html(
                    "Detalhes Inadimplência</br>" +
                        row.data().MES_ANO +
                        " (" +
                        formatarValorBR(row.data().VL_SALDO) +
                        ")",
                );
                $("#" + idAccordion).empty(); // limpa antes de popular
                let accordion = '';

                data.forEach(function (item) {
                    const vlSaldo = parseFloat(item.VL_SALDO_AGRUPADO).toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const vlCartorio = parseFloat(item.VL_CARTORIO_AGRUPADO);
                    accordion = `
                        <div class="card gerente-card mb-2 border-0 shadow-sm">
                            <div class="card-header p-0" style="background:#2d3748; border-left:4px solid #4299e1; border-radius:4px 4px 0 0;">
                                <button class="btn btn-block text-white text-left accordion-item-header py-2 px-3"
                                        type="button" data-toggle="collapse" data-target="#collapse${item.CD_PESSOA}">
                                    <span class="accordion-nome" style="font-size:0.87rem; font-weight:600;">
                                        <i class="fas fa-building mr-1" style="color:#90cdf4;"></i>
                                        ${item.NM_PESSOA}
                                    </span>
                                    <span class="saldo">
                                        <span class="badge badge-pill badge-warning badge-indicador ml-1">R$ ${vlSaldo}</span>
                                        ${vlCartorio > 0 ? `<span class="badge badge-pill badge-purple badge-indicador ml-1">R$ ${vlCartorio.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>` : ""}
                                    </span>
                                </button>
                            </div>
                            <div id="collapse${item.CD_PESSOA}" class="collapse">
                                <div class="card-body p-2" style="background:#f7fafc;">
                    `;
                    item.DETALHES.forEach(function (detalhe) {
                        accordion += `
                                    <div class="mb-2 px-2 py-2" style="border-left:3px solid #e2e8f0; border-radius:3px; background:#fff;">
                                        <div class="d-flex align-items-center mb-1" style="gap:4px; flex-wrap:wrap;">
                                            <span class="badge badge-pill badge-secondary" style="font-size:0.7rem;">${detalhe.TIPOCONTA}</span>
                                            <span class="badge badge-pill badge-dark" style="font-size:0.7rem;">${detalhe.CD_FORMAPAGTO}</span>
                                            ${detalhe.VL_CARTORIO > 0 ? '<span class="badge badge-pill badge-purple" style="font-size:0.65rem;">Em Cartório</span>' : ""}
                                        </div>
                                        <div class="row no-gutters">
                                            <div class="col-6 pr-2">
                                                <table class="table table-sm table-borderless mb-0" style="font-size:0.72rem;">
                                                    <tr><td class="text-muted p-0 pr-1" style="width:44%">Nota</td><td class="p-0 font-weight-bold">${detalhe.NR_DOCUMENTO}</td></tr>
                                                    <tr><td class="text-muted p-0 pr-1">Emissão</td><td class="p-0">${formatDateCobranca(detalhe.DT_LANCAMENTO)}</td></tr>
                                                    <tr><td class="text-muted p-0 pr-1">Venc.</td><td class="p-0">${formatDateCobranca(detalhe.DT_VENCIMENTO)}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-6 pl-2 border-left">
                                                <table class="table table-sm table-borderless mb-0" style="font-size:0.72rem;">
                                                    <tr><td class="text-muted p-0 pr-1" style="width:40%">Total</td><td class="p-0 font-weight-bold">R$ ${formatarValorBR(detalhe.VL_TOTAL)}</td></tr>
                                                    <tr><td class="text-muted p-0 pr-1">Saldo</td><td class="p-0 text-success font-weight-bold">R$ ${formatarValorBR(detalhe.VL_SALDO)}</td></tr>
                                                    <tr><td class="text-muted p-0 pr-1">Juros</td><td class="p-0 text-danger font-weight-bold">${formatarValorBR(detalhe.VL_JUROS)}</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                        `;
                    });

                    accordion += `
                                </div>
                            </div>
                        </div>
                    `;

                    $("#" + idAccordion).append(accordion);
                });

                $("#loading").addClass("invisible");
                $("#" + idModal).modal("show");
            },
        });
    });
}

function formatDateCobranca(value) {
    if (!value) return "";

    const [ano, mes, dia] = value.split("-");

    return `${dia}/${mes}/${ano}`;
}

let debounceTimer;

//remove acentos e coloca em minusculo
function normalizeString(str) {
    return str
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase();
}

//cria um array com os cards e seus textos normalizados
function getCardsData(idAccordion) {
    const data = [];
    $(`#${idAccordion} > .gerente-card`).each(function () {
        const $card = $(this);
        const text = normalizeString($card.find(".accordion-nome").text().trim() || "");
        data.push({ element: $card, text: text });
    });
    return data;
}

// ── Helpers: filtros de vencimento ──────────────────────────────────────────

function extrairDatasAccordion(data) {
    const set = new Set();
    data.forEach(g => g.supervisores.forEach(s => s.vendedores.forEach(v =>
        v.clientes.forEach(c => { if (c.DT_VENCIMENTO) set.add(c.DT_VENCIMENTO); })
    )));
    return [...set].sort();
}

function popularSelect($sel, options) {
    $sel.find("option:not(:first)").remove();
    options.forEach(({ value, label }) => $sel.append(`<option value="${value}">${label}</option>`));
}

function popularSelectMes($sel, dates) {
    const meses = [...new Set(dates.map(d => d.substring(0, 7)))].sort();
    popularSelect($sel, meses.map(ym => {
        const [ano, mes] = ym.split("-");
        return { value: ym, label: `${MESES_ABREV[parseInt(mes, 10) - 1]}/${ano}` };
    }));
}

function popularSelectDia($sel, dates, mesAno) {
    const filtered = mesAno ? dates.filter(d => d.startsWith(mesAno)) : dates;
    popularSelect($sel, [...new Set(filtered)].sort().map(d => {
        const [ano, mes, dia] = d.split("-");
        return { value: d, label: `${dia}/${mes}/${ano}` };
    }));
}

function filtrarAccordion(accordionId, mesAno, dataCompleta) {
    const $accordion = $("#" + accordionId);

    $accordion.find(".cliente-item").each(function () {
        const dtVenc = $(this).data("vencimento") || "";
        let match = true;
        if (dataCompleta)      match = dtVenc === dataCompleta;
        else if (mesAno)       match = dtVenc.startsWith(mesAno);
        $(this).toggleClass("hidden", !match);
    });

    $accordion.find(".vendedor-container").each(function () {
        $(this).toggleClass("hidden", $(this).find(".cliente-item:not(.hidden)").length === 0);
    });
    $accordion.find(".supervisor-container").each(function () {
        $(this).toggleClass("hidden", $(this).find(".vendedor-container:not(.hidden)").length === 0);
    });
    $accordion.find(".gerente-card").each(function () {
        $(this).toggleClass("hidden", $(this).find(".supervisor-container:not(.hidden)").length === 0);
    });

    // Auto-expand/collapse gerente cards based on filter state
    // (using direct class toggle to bypass data-parent accordion restriction)
    if (mesAno || dataCompleta) {
        $accordion.find(".gerente-card:not(.hidden) > .collapse").addClass("show");
        $accordion.find(".gerente-card.hidden > .collapse").removeClass("show");
    } else {
        $accordion.find(".gerente-card > .collapse").removeClass("show");
    }

    const semResultado = $accordion.find(".gerente-card:not(.hidden)").length === 0;
    $accordion.find(".no-results-venc").remove();
    if (semResultado && (mesAno || dataCompleta)) {
        $accordion.append('<div class="no-results-venc text-muted text-center py-3" style="font-size:0.82rem;">Nenhum título encontrado para este período.</div>');
    }
}

// Mês/Ano → habilita e popula o select de dia; filtra por mês
$(document).on("change", ".select-vencimento", function () {
    const mesAno      = $(this).val();
    const accordionId = $(this).data("accordion");
    const allDates    = datesCache[accordionId] || [];
    const $diaSelect  = $("#select-dia-" + accordionId);

    $diaSelect.val("").prop("disabled", !mesAno);
    popularSelectDia($diaSelect, allDates, mesAno);
    filtrarAccordion(accordionId, mesAno, "");
});

// Dia → filtra pela data exata YYYY-MM-DD
$(document).on("change", ".select-dia-vencimento", function () {
    const dataCompleta = $(this).val();
    const accordionId  = $(this).data("accordion");
    const mesAno       = $("#select-venc-" + accordionId).val();
    filtrarAccordion(accordionId, mesAno, dataCompleta);
});

// ── Gráficos de pizza estáticos (% por saldo) ────────────────────────────────

function _pizzaStatic(canvasId, items) {
    const $canvas = $("#" + canvasId);
    if (!$canvas.length) return;

    if (pizzaCharts[canvasId]) {
        pizzaCharts[canvasId].destroy();
        delete pizzaCharts[canvasId];
    }

    const total = items.reduce((a, b) => a + b.valor, 0);
    if (total === 0) return;

    const PALETTE = [
        "#4299e1","#48bb78","#ed8936","#9f7aea","#e53e3e",
        "#38b2ac","#f6ad55","#667eea","#fc8181","#68d391",
        "#63b3ed","#b794f4"
    ];

    pizzaCharts[canvasId] = new Chart($canvas[0].getContext("2d"), {
        type: "doughnut",
        data: {
            labels: items.map(i => i.nome),
            datasets: [{
                data:            items.map(i => i.valor),
                backgroundColor: items.map((_, k) => PALETTE[k % PALETTE.length] + "cc"),
                borderColor:     items.map((_, k) => PALETTE[k % PALETTE.length]),
                borderWidth:     1.5,
                hoverOffset:     5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "52%",
            plugins: {
                legend: {
                    display: true,
                    position: "bottom",
                    labels: { font: { size: 9 }, padding: 5, boxWidth: 8, color: "#4a5568" }
                },
                datalabels: {
                    display: ctx => (ctx.dataset.data[ctx.dataIndex] / total) * 100 >= 4,
                    formatter: v => ((v / total) * 100).toFixed(1) + "%",
                    color: "#fff",
                    font: { weight: "bold", size: 9 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const pct = ((ctx.raw / total) * 100).toFixed(1);
                            return ` ${pct}% — R$ ${formatarValorBR(ctx.raw)}`;
                        }
                    }
                }
            }
        }
    });
}

function _barStatic(canvasId, items) {
    const $canvas = $("#" + canvasId);
    if (!$canvas.length) return;

    if (pizzaCharts[canvasId]) {
        pizzaCharts[canvasId].destroy();
        delete pizzaCharts[canvasId];
    }

    const total = items.reduce((a, b) => a + b.valor, 0);
    if (total === 0) return;

    // Ajusta altura conforme qtd de barras (mínimo 175px, ~22px por item)
    const dynH = Math.max(175, items.length * 22 + 30);
    $canvas.closest(".card-body").css("height", dynH + "px");

    const pcts   = items.map(i => parseFloat(((i.valor / total) * 100).toFixed(2)));
    const PALETTE = [
        "#4299e1","#48bb78","#ed8936","#9f7aea","#e53e3e",
        "#38b2ac","#f6ad55","#667eea","#fc8181","#68d391",
        "#63b3ed","#b794f4"
    ];

    pizzaCharts[canvasId] = new Chart($canvas[0].getContext("2d"), {
        type: "bar",
        data: {
            labels: items.map(i => i.nome),
            datasets: [{
                data:            pcts,
                backgroundColor: items.map((_, k) => PALETTE[k % PALETTE.length] + "bb"),
                borderColor:     items.map((_, k) => PALETTE[k % PALETTE.length]),
                borderWidth:     1,
                borderRadius:    3
            }]
        },
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    display: true,
                    anchor: "end",
                    align: "right",
                    formatter: v => v.toFixed(1) + "%",
                    color: "#4a5568",
                    font: { weight: "600", size: 9 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const item = items[ctx.dataIndex];
                            return ` ${ctx.raw.toFixed(1)}% — R$ ${formatarValorBR(item.valor)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false,
                    max: Math.max(...pcts) * 1.25
                },
                y: {
                    ticks: {
                        font: { size: 9 },
                        color: "#4a5568",
                        callback: function (v) {
                            const lbl = this.getLabelForValue(v);
                            return lbl.length > 14 ? lbl.substring(0, 13) + "…" : lbl;
                        }
                    },
                    grid: { display: false }
                }
            }
        }
    });
}

function buscarTermo(idAccordion, idSelector) {
    // faz a busca com debounce
    $(idSelector).on("input", function () {
        clearTimeout(debounceTimer);
        const termo = normalizeString($(this).val());

        debounceTimer = setTimeout(() => {
            const cardsData = getCardsData(idAccordion);
            let anyVisible = false;

            // percorre os cards e mostra/oculta conforme o termo
            cardsData.forEach((card) => {
                const match = card.text.includes(termo);
                card.element.toggleClass("hidden", !match);
                if (match) anyVisible = true;
            });

            if (!anyVisible) {
                if ($("#noResults").length === 0) {
                    $(`#${idAccordion}`).append(
                        '<div id="noResults">Nenhum cliente encontrado.</div>',
                    );
                }
            } else {
                $("#noResults").remove();
            }
        }, 250); //tempo para esperar o usuario parar de digitar
    });
}

// limpa o campo de busca ao fechar o modal
$("#modal-table-cliente").on("hidden.bs.modal", function () {
    $("#buscarCliente").val("");
});
