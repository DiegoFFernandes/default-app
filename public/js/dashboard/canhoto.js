
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
            url: route["tabela_mensal"],
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
        // footerCallback: function (row, data, start, end, display) {
        //     var api = this.api();

        //     inadMeses = data;
        //     tentarProcessar();

        //     // Remove the formatting to get integer data for summation
        //     var intVal = function (i) {
        //         return typeof i === "string"
        //             ? i.replace(",", ".") * 1
        //             : typeof i === "number"
        //             ? i
        //             : 0;
        //     };

        //     // Total over all pages
        //     totalTotal = api
        //         .column(2)
        //         .data()
        //         .reduce(function (a, b) {
        //             return intVal(a) + intVal(b);
        //         }, 0);

        //     totalVencido = api
        //         .column(3)
        //         .data()
        //         .reduce(function (a, b) {
        //             return intVal(a) + intVal(b);
        //         }, 0);

        //     // Update footer
        //     $(api.column(2).footer()).html(formatarValorBR(totalTotal));
        //     $(api.column(3).footer()).html(formatarValorBR(totalVencido));

        //     var inadimplenciaPercentual = (inadimplencia / totalTotal) * 100;
        //     var atrasadosPercentual = (atrasados / totalTotal) * 100;

        //     $("#pc_inadimplencia").html(
        //         formatarValorBR(inadimplenciaPercentual) + "%"
        //     );
        //     $("#pc_atrasados").html(formatarValorBR(atrasadosPercentual) + "%");

        //     $("#vencidos").html(formatarValorBR(totalVencido));

        //     $("#total_carteira").html(formatarValorBR(totalTotal));
        // },
    });

    // $("#" + idTable + " tbody").on("click", ".details-control", function () {
    //     var tr = $(this).closest("tr");
    //     var row = $("#" + idTable)
    //         .DataTable()
    //         .row(tr);

    //     $("#" + idAccordion).empty(); // Limpa antes

    //     $.ajax({
    //         type: "GET",
    //         url: route["modal_clientes"],
    //         data: {
    //             mes: row.data().MES,
    //             ano: row.data().ANO,
    //             tab: tab,
    //         },
    //         dataType: "json",
    //         beforeSend: function () {
    //             $("#loading").removeClass("invisible");
    //         },
    //         success: function (response) {
    //             data = Object.values(response);

    //             $(".modal-table-cliente-label").html(
    //                 "Detalhes Inadimplência</br>" +
    //                     row.data().MES_ANO +
    //                     " (" +
    //                     formatarValorBR(row.data().VL_SALDO) +
    //                     ")"
    //             );
    //             $("#" + idAccordion).empty(); // limpa antes de popular

    //             data.forEach(function (item) {
    //                 let accordion = `
    //                     <div class="card card-outline">
    //                     <div class="card-header pt-1 pb-1" id="heading${
    //                         item.CD_PESSOA
    //                     }">
    //                         <h6 class="mb-0 d-flex align-items-center justify-content-between">
    //                             <button class="btn collapsed p-0 m-0 text-left" type="button"
    //                                     data-toggle="collapse"
    //                                     data-target="#collapse${item.CD_PESSOA}"
    //                                     aria-expanded="false"
    //                                     aria-controls="collapse${
    //                                         item.CD_PESSOA
    //                                     }" style="font-size: 13px;">
    //                             <b>${item.NM_PESSOA}</b>
    //                             </button>
    //                             <span class="badge badge-warning ml-2">
    //                                 ${parseFloat(
    //                                     item.VL_SALDO_AGRUPADO
    //                                 ).toLocaleString("pt-BR", {
    //                                     minimumFractionDigits: 2,
    //                                     maximumFractionDigits: 2,
    //                                 })}
    //                             </span>
    //                         </h6>
    //                     </div>
    //                     <div id="collapse${
    //                         item.CD_PESSOA
    //                     }" class="collapse" aria-labelledby="heading${
    //                     item.CD_PESSOA
    //                 }">
    //                 `;
    //                 item.DETALHES.forEach(function (detalhe) {
    //                     accordion += `
    //                             <div class="card-body p-1">
    //                                 <div class="card-body pt-2 pb-2">
    //                                 <span class="badge badge-secondary mr-1">${
    //                                     detalhe.TIPOCONTA
    //                                 }</span>
    //                                 <span class="badge badge-dark mr-1">${
    //                                     detalhe.CD_FORMAPAGTO
    //                                 }</span>
    //                                 <table class="table table-sm mb-0">
    //                                     <tbody>
    //                                         <tr>
    //                                             <th class="text-muted">Nota</th>
    //                                             <td class="td-small-text">${
    //                                                 detalhe.NR_DOCUMENTO
    //                                             }</td>
    //                                             <th class="text-muted">Total</th>
    //                                             <td><span class="font-weight-bold">${formatarValorBR(
    //                                                 detalhe.VL_TOTAL
    //                                             )}</span></td>
    //                                         </tr>
    //                                         <tr>
    //                                             <th class="text-muted">Emissão</th>
    //                                             <td class="td-small-text">${formatDate(
    //                                                 detalhe.DT_LANCAMENTO
    //                                             )}</td>
    //                                             <th class="text-muted">Venc.</th>
    //                                             <td>${formatDate(
    //                                                 detalhe.DT_VENCIMENTO
    //                                             )}</td>
    //                                         </tr>
    //                                         <tr>
    //                                             <th class="text-muted">Valor</th>
    //                                             <td><span class="text-success font-weight-bold">R$ ${formatarValorBR(
    //                                                 detalhe.VL_SALDO
    //                                             )}</span></td>
    //                                             <th class="text-muted">Juros</th>
    //                                             <td><span class="text-danger font-weight-bold">${formatarValorBR(
    //                                                 detalhe.VL_JUROS
    //                                             )}</span></td>
    //                                         </tr>
    //                                     </tbody>
    //                                 </table>
    //                             </div>

    //                         </div>
    //                         <hr class="mt-0 mb-2">
    //                     `;
    //                 });

    //                 accordion += `
    //                         </div>
    //                     </div>
    //                 `;

    //                 $("#" + idAccordion).append(accordion);
    //             });

    //             $("#loading").addClass("invisible");
    //             $("#" + idModal).modal("show");
    //         },
    //     });
    // });
}
