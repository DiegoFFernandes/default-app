var inadGerente = null;
var inadMeses = null;
var total = 0;
var atrasados = 0;
var inadimplencia = 0;
var hierarquia = null;
var carteira60dias = 0;
var carteiraMaior60dias = 0;

function tentarProcessar() {
    
    if (inadGerente && inadMeses) {        
      
        inadGerente.forEach((gerente, gIndex) => {

            var pc_atrasados_gerente = 0;
            var pc_inadimplencia_gerente = 0;
            if (hierarquia !== null) {
                if (hierarquia[gerente.nome].nome === gerente.nome) {
                    const carteira60diasGerente =
                        hierarquia[gerente.nome]["carteira60diasGerente"];
                    const carteiraMaior60diasGerente =
                        hierarquia[gerente.nome]["carteiraMaior60diasGerente"];

                    if (!carteira60diasGerente == 0) {
                        total = carteira60diasGerente;
                        pc_atrasados_gerente =
                            (gerente.atrasados / total) * 100;
                    }
                    if (!carteiraMaior60diasGerente == 0) {
                        total = carteiraMaior60diasGerente;
                        pc_inadimplencia_gerente =
                            (gerente.inadimplencia / total) * 100;
                    }
                }
            }
            // $(`.pc_inadidimplencia-gerente-${gIndex}`).text(percentual.toFixed(2) + "%");
            $(`.pc_atrasados-gerente-${gIndex}`).html(
                `Atrasados: ${pc_atrasados_gerente.toFixed(2)}%`
            );
            $(`.pc_inadimplencia-gerente-${gIndex}`).html(
                `Inadimplência: ${pc_inadimplencia_gerente.toFixed(2)}%`
            );
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
            $(".pc_atrasados-gerente").html(
                '<i class="fas fa-sync-alt fa-spin"></i>'
            );
            $(".pc_inadimplencia-gerente").html(
                '<i class="fas fa-sync-alt fa-spin"></i>'
            );
        },
        success: function (data) {
            inadGerente = data;
            let valorTotalGerente = 0;
            let html = "";
            data.forEach((gerente, gIndex) => {
                valorTotalGerente += gerente.saldo;
                html += `
                            <div class="card gerente-card">
                            <div class="card-header p-1">
                                <button class="btn btn-link text-left" data-toggle="collapse" data-target="#sup-${gIndex}">
                                    👔 ${gerente.nome} (R$ ${formatarValorBR(
                    gerente.saldo
                )}) 
                                    <!--<span class="badge badge-warning pc_inadidimplencia-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>-->
                                    <span class="saldo">
                                        <span class="badge badge-info pc_atrasados-gerente pc_atrasados-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                        <span class="badge badge-warning pc_inadimplencia-gerente pc_inadimplencia-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    </span>
                                </button>
                            </div>
                            <div id="sup-${gIndex}" class="collapse">
                                <div class="card-body p-2">     `;

                gerente.supervisores.forEach((sup, sIndex) => {
                    html += `<div class="supervisor-container">`;
                    html += `
                            <button class="btn btn-sm btn-secondary d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                🛡️ ${sup.nome} (R$ ${formatarValorBR(
                        sup.saldo
                    )}) 
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-2">
                            `;

                    sup.vendedores.forEach((vend, vIndex) => {
                        html += `<div class="vendedor-container">`;
                        html += `
                                <button class="btn btn-sm btn-info d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                👤 ${vend.nome} 
                                <span class="saldo">(R$ ${formatarValorBR(
                                    vend.saldo
                                )})</span>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse mt-2">
                                <ul class="list-group">
                            `;

                        vend.clientes.forEach((detalhe) => {
                            html += `
                                <li class="list-group-item p-1 cliente-item">
                                    🏢 <span class="badge badge-secondary">${
                                        detalhe.PESSOA
                                    }</span><br>
                                     <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted">Nota</th>
                                                <td class="td-small-text">${
                                                    detalhe.NR_DOCUMENTO
                                                }</td>
                                                <th class="text-muted">Total</th>
                                                <td><span class="font-weight-bold">${formatarValorBR(
                                                    detalhe.VL_TOTAL
                                                )}</span></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Emissão</th>
                                                <td class="td-small-text">${formatDate(
                                                    detalhe.DT_LANCAMENTO
                                                )}</td>
                                                <th class="text-muted">Venc.</th>
                                                <td>${formatDate(
                                                    detalhe.DT_VENCIMENTO
                                                )}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Valor</th>
                                                <td><span class="text-success font-weight-bold">R$ ${formatarValorBR(
                                                    detalhe.VL_SALDO
                                                )}</span></td>
                                                <th class="text-muted">Juros</th>
                                                <td><span class="text-danger font-weight-bold">${formatarValorBR(
                                                    detalhe.VL_JUROS
                                                )}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                
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
            $("#" + idCard + " .loading-card").addClass("invisible");
            $(".info-loading.loading-card").addClass("invisible");
            $(".valorTotalGerente").text(
                `R$ ${valorTotalGerente.toLocaleString()}`
            );
            tentarProcessar();
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
                    '<div class="no-results-message">Nenhum cliente encontrado.</div>'
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
    route
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
                    "invisible"
                );
                $(".info-loading.loading-card").removeClass("invisible");
                $(".pc_atrasados-gerente").html(
                    '<i class="fas fa-sync-alt fa-spin"></i>'
                );
                $(".pc_inadimplencia-gerente").html(
                    '<i class="fas fa-sync-alt fa-spin"></i>'
                );
                $("#pc_atrasados").text(`0,00%`);
                $("#pc_inadimplencia").text(`0,00%`);
            },
            dataSrc: function (json) {
                // Salva as variáveis globais com os valores do backend
                atrasados = parseFloat(json.atrasados);
                inadimplencia = parseFloat(json.inadimplencia);
                carteira60dias = parseFloat(json.carteira60dias);
                carteiraMaior60dias = parseFloat(json.carteiraMaior60dias);

                hierarquia = json.hierarquia;
                return json.data;
            },
            complete: function () {
                $("#card-inadimplencia-meses .loading-card").addClass(
                    "invisible"
                );
                $(".info-loading.loading-card").addClass("invisible");
            },
        },

        columns: [
            {
                data: null,
                name: "action",
                ordeable: false,
                searchable: false,
                render: function () {
                    return "<span class='right btn-detalhes details-control mr-2'><i class='fa fa-plus-circle'></i></span>";
                },
            },
            {
                data: "MES_ANO",
                name: "MES_ANO",
                title: "Mês/Ano",
                width: "33%",
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
                width: "33%",
            },
            {
                data: "PC_INADIMPLENCIA",
                name: "PC_INADIMPLENCIA",
                title: "%",
                width: "33%",
                render: function (data) {
                    return formatarValorBR(data) + "%";
                },
            },
        ],
        columnDefs: [
            {
                targets: [2, 3],
                render: $.fn.dataTable.render.number(".", ",", 2),
            },
        ],
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

            var inadimplenciaPercentual =
                (inadimplencia / carteiraMaior60dias) * 100;
            var atrasadosPercentual = (atrasados / carteira60dias) * 100;

            $("#pc_inadimplencia").html(
                formatarValorBR(inadimplenciaPercentual) + "%"
            );
            $("#pc_atrasados").html(formatarValorBR(atrasadosPercentual) + "%");

            $("#vencidos").html(formatarValorBR(totalVencido));

            $("#total_carteira").html(formatarValorBR(totalTotal));

            $("#total_60_atrasados").html(formatarValorBR(carteira60dias));
            $("#vl_60_atrasados").html(formatarValorBR(atrasados));

            $("#total_maior_60_atrasados").html(
                formatarValorBR(carteiraMaior60dias)
            );
            $("#vl_maior_60_atrasados").html(formatarValorBR(inadimplencia));

            // Atualiza o footer do DataTable
            $(api.column(2).footer()).html(formatarValorBR(totalTotal));
            $(api.column(3).footer()).html(formatarValorBR(totalVencido));
            $(api.column(4).footer()).html(
                formatarValorBR(atrasadosPercentual + inadimplenciaPercentual) +
                    "%"
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
            },
            dataType: "json",
            beforeSend: function () {
                $("#loading").removeClass("invisible");
            },
            success: function (response) {
                data = Object.values(response);

                $(".modal-table-cliente-label").html(
                    "Detalhes Inadimplência</br>" +
                        row.data().MES_ANO +
                        " (" +
                        formatarValorBR(row.data().VL_SALDO) +
                        ")"
                );
                $("#" + idAccordion).empty(); // limpa antes de popular

                data.forEach(function (item) {
                    let accordion = `
                        <div class="card card-outline">
                        <div class="card-header pt-1 pb-1" id="heading${
                            item.CD_PESSOA
                        }">
                            <h6 class="mb-0 d-flex align-items-center justify-content-between">
                                <button class="btn collapsed p-0 m-0 text-left" type="button"
                                        data-toggle="collapse"
                                        data-target="#collapse${item.CD_PESSOA}"
                                        aria-expanded="false"
                                        aria-controls="collapse${
                                            item.CD_PESSOA
                                        }" style="font-size: 13px;">
                                <b>${item.NM_PESSOA}</b>
                                </button>
                                <span class="badge badge-warning ml-2">
                                    ${parseFloat(
                                        item.VL_SALDO_AGRUPADO
                                    ).toLocaleString("pt-BR", {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                    })}
                                </span>
                            </h6>
                        </div>
                        <div id="collapse${
                            item.CD_PESSOA
                        }" class="collapse" aria-labelledby="heading${
                        item.CD_PESSOA
                    }">
                    `;
                    item.DETALHES.forEach(function (detalhe) {
                        accordion += `
                                <div class="card-body p-1">
                                    <div class="card-body pt-2 pb-2">
                                    <span class="badge badge-secondary mr-1">${
                                        detalhe.TIPOCONTA
                                    }</span>
                                    <span class="badge badge-dark mr-1">${
                                        detalhe.CD_FORMAPAGTO
                                    }</span>
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted">Nota</th>
                                                <td class="td-small-text">${
                                                    detalhe.NR_DOCUMENTO
                                                }</td>
                                                <th class="text-muted">Total</th>
                                                <td><span class="font-weight-bold">${formatarValorBR(
                                                    detalhe.VL_TOTAL
                                                )}</span></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Emissão</th>
                                                <td class="td-small-text">${formatDate(
                                                    detalhe.DT_LANCAMENTO
                                                )}</td>
                                                <th class="text-muted">Venc.</th>
                                                <td>${formatDate(
                                                    detalhe.DT_VENCIMENTO
                                                )}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Valor</th>
                                                <td><span class="text-success font-weight-bold">R$ ${formatarValorBR(
                                                    detalhe.VL_SALDO
                                                )}</span></td>
                                                <th class="text-muted">Juros</th>
                                                <td><span class="text-danger font-weight-bold">${formatarValorBR(
                                                    detalhe.VL_JUROS
                                                )}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <hr class="mt-0 mb-2">
                        `;
                    });

                    accordion += `
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
    $(`#${idAccordion} .card`).each(function () {
        const $card = $(this);
        const text = normalizeString($card.find("b").text() || "");
        data.push({ element: $card, text: text });
    });
    return data;
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
                        '<div id="noResults">Nenhum cliente encontrado.</div>'
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
