var inadGerente = null;
var inadMeses = null;
var total = 0;
var atrasados = 0;
var inadimplencia = 0;
var hierarquia = null;

function tentarProcessar() {
    if (inadGerente && inadMeses) {      

        inadGerente.forEach((gerente, gIndex) => {
            if (hierarquia !== null) {
                console.log(hierarquia[gerente.nome].nome);
                if (hierarquia[gerente.nome].nome === gerente.nome) {
                    total = hierarquia[gerente.nome]["vl_documento"];
                    let percentual = (gerente.saldo / total) * 100;
                    let pc_atrasados = (gerente.atrasados / total) * 100;
                    let pc_inadimplencia =
                        (gerente.inadimplencia / total) * 100;

                    // $(`.pc_inadidimplencia-gerente-${gIndex}`).text(percentual.toFixed(2) + "%");
                    $(`.pc_atrasados-gerente-${gIndex}`).html(
                        `Atrasados: ${pc_atrasados.toFixed(2)}%`
                    );
                    $(`.pc_inadimplencia-gerente-${gIndex}`).html(
                        `Inadimplência: ${pc_inadimplencia.toFixed(2)}%`
                    );
                }
            }
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
            $(".valorTotalGerente").text(`R$ 0`);
            $("#pc_atrasados").text(`0,00%`);
            $("#pc_inadimplencia").text(`0,00%`);
        },
        success: function (data) {
            inadGerente = data;
            tentarProcessar();
            let valorTotalGerente = 0;
            let html = "";
            data.forEach((gerente, gIndex) => {
                valorTotalGerente += gerente.saldo;

                html += `
                            <div class="card">
                            <div class="card-header p-1">
                                <button class="btn btn-link text-left" data-toggle="collapse" data-target="#sup-${gIndex}">
                                    👔 ${gerente.nome} (R$ ${formatarValorBR(
                    gerente.saldo
                )}) 
                                    <!--<span class="badge badge-warning pc_inadidimplencia-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>-->
                                    <span class="saldo">
                                        <span class="badge badge-info pc_atrasados-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                        <span class="badge badge-warning pc_inadimplencia-gerente-${gIndex}"><i class="fas fa-sync-alt fa-spin"></i></span>
                                    </span>
                                </button>
                            </div>
                            <div id="sup-${gIndex}" class="collapse" data-parent="#${idAccordion}">
                                <div class="card-body p-2">     `;

                gerente.supervisores.forEach((sup, sIndex) => {
                    html += `
                            <button class="btn btn-sm btn-secondary d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                🛡️ ${sup.nome} (R$ ${formatarValorBR(
                        sup.saldo
                    )}) 
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-2">
                            `;

                    sup.vendedores.forEach((vend, vIndex) => {
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
                                <li class="list-group-item p-1">
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
                    });

                    html += `</div>`; // fecha Supervisor
                });

                html += `</div></div></div>`; // fecha Gerente
            });

            $("#" + idAccordion).html(html);
            $("#" + idCard + " .loading-card").addClass("invisible");
            $(".valorTotalGerente").text(
                `R$ ${valorTotalGerente.toLocaleString()}`
            );
        },
    });
}
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
                $("#pc_atrasados").text(`0,00%`);
                $("#pc_inadimplencia").text(`0,00%`);
            },
            dataSrc: function (json) {
                // console.log(json);
                // Salva as variáveis globais com os valores do backend
                atrasados = parseFloat(json.atrasados);
                inadimplencia = parseFloat(json.inadimplencia);

                hierarquia = json.hierarquia;
                return json.data;
            },
            complete: function () {
                $("#card-inadimplencia-meses .loading-card").addClass(
                    "invisible"
                );
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

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === "string"
                    ? i.replace(",", ".") * 1
                    : typeof i === "number"
                    ? i
                    : 0;
            };

            // Total over all pages
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

            // Update footer
            $(api.column(2).footer()).html(formatarValorBR(totalTotal));
            $(api.column(3).footer()).html(formatarValorBR(totalVencido));

            var inadimplenciaPercentual = (inadimplencia / totalTotal) * 100;
            var atrasadosPercentual = (atrasados / totalTotal) * 100;

            $("#pc_inadimplencia").html(
                formatarValorBR(inadimplenciaPercentual) + "%"
            );
            $("#pc_atrasados").html(formatarValorBR(atrasadosPercentual) + "%");

            $("#vencidos").html(formatarValorBR(totalVencido));

            $("#total_carteira").html(formatarValorBR(totalTotal));
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

function buscarTermo(idAccordion) {
    // faz a busca com debounce
    $("#buscarCliente").on("input", function () {
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
