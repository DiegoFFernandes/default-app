var totalCanhoto = 0;

// inicializa a tabela de meses relatorio de gerente
function initTableCanhotoMeses(
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
            url: route["canhoto"],
            data: {
                filtro: data,
                tab: tab,
            },
            beforeSend: function () {
                $(".loading-card").removeClass("invisible");
            },
            dataSrc: function (json) {
                return json.data;
            },
            complete: function () {
                $(".loading-card").addClass("invisible");
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
            },
            {
                data: "QTD_NOTA",
                name: "QTD_NOTA",
                title: "Canhotos",
            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === "string"
                    ? i.replace(",", ".") * 1
                    : typeof i === "number"
                    ? i
                    : 0;
            };

            // Total over all pages
            total = api
                .column(2)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Update footer
            $(api.column(2).footer()).html(total);
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
            url: route["modal_canhotos"],
            data: {
                mes: row.data().MES,
                ano: row.data().ANO,
            },
            dataType: "json",
            beforeSend: function () {
                $("#loading").removeClass("invisible");
            },
            success: function (response) {
                data = Object.values(response);

                $(".modal-table-canhoto-label").html(
                    "Detalhes Canhoto</br>" +
                        row.data().MES_ANO +
                        " (" +
                        row.data().QTD_NOTA +
                        " Canhotos)"
                );
                $("#" + idAccordion).empty(); // limpa antes de popular

                let html = initAccordionCanhoto(data, idAccordion);
                $("#" + idAccordion).html(html);
                $("#loading").addClass("invisible");
                $("#" + idModal).modal("show");
            },
        });
    });
}
function initAccordionCanhoto(data, idAccordion) {
    let html = "";
    data.forEach((gerente, gIndex) => {
        html += `
                            <div class="card">
                            <div class="card-header p-1">
                                <button class="btn btn-link text-left" data-toggle="collapse" data-target="#sup-${gIndex}">
                                    👔 ${gerente.nome} (${gerente.qtd_notas} Canhotos)
                                </button>
                            </div>
                            <div id="sup-${gIndex}" class="collapse" data-parent="#${idAccordion}">
                                <div class="card-body p-2">     `;

        gerente.supervisores.forEach((sup, sIndex) => {
            html += `
                            <button class="btn btn-sm btn-secondary d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                🛡️ ${sup.nome} (${sup.qtd_notas})
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-2">
                            `;

            sup.vendedores.forEach((vend, vIndex) => {
                html += `
                                <button class="btn btn-sm btn-info d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                👤 ${vend.nome} 
                                <span class="saldo">(${vend.qtd_notas})</span>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse mt-2">                                
                            `;

                vend.clientes.forEach((cli, cIndex) => {
                    html += `
                                <button class="btn btn-sm btn-default d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}-${cIndex}">
                                🏢 ${cli.nome} 
                                <span class="saldo">(${cli.qtd_notas})</span>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}-${cIndex}" class="collapse mt-2">
                                <ul class="list-group">
                                `;

                    cli.detalhes.forEach((detalhe, dIndex) => {
                        html += `
                                <li class="list-group-item p-1">  
                                    <span class="badge badge-secondary">${
                                        cli.nome
                                    }</span>                                  
                                     <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted">Documento</th>
                                                <td class="td-small-text">${
                                                    detalhe.nr_documento
                                                }</td>
                                                <th class="text-muted">Serie</th>
                                                <td><span class="font-weight-bold">${
                                                    detalhe.cd_serie
                                                }</span></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Emissão</th>
                                                <td class="td-small-text">${formatDate(
                                                    detalhe.dt_lancamento
                                                )}</td>
                                                <th class="text-muted"></th>
                                                <td></td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                
                                </li>
                                `;
                    });
                    html += `</ul></div>`;
                });

                html += `</ul></div>`;
            });

            html += `</div>`; // fecha Supervisor
        });

        html += `</div></div></div>`; // fecha Gerente
    });

    return html;
}

function canhotoGerente(tab, data, routes, idAccordion, idCard) {
    $("#" + idAccordion).empty(); // Limpa antes
    let valorTotalCanhotos = 0;

    $.ajax({
        type: "GET",
        url: routes["modal_canhotos"],
        data: {
            mes: 0,
            ano: 0,
        },
        dataType: "json",
        
        success: function (response) {
            data = Object.values(response);         
            
            valorTotalCanhotos = data.reduce((acc, gerente) => acc + gerente.qtd_notas, 0);           

            let html = initAccordionCanhoto(data, idAccordion);
            $("#" + idAccordion).html(html);               
            $('.valorTotalCanhoto').html(valorTotalCanhotos + ' Canhotos');     
        },
    });
}
