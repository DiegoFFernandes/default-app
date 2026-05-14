function initTabelaPreco(route) {
    tabelaPreco = $("#tabela-preco").DataTable({
        processing: false,
        serverSide: false,
        pagingType: "simple",
        pageLength: 50,
        destroy: true,
        scrollY: "400px",
        scrollCollapse: true,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.tabelaPreco,
            type: "GET",
            beforeSend: function () {
                Swal.fire({
                    title: "Carregando...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            complete: function () {
                Swal.close();
            },
        },
        columns: [
            {
                data: "clientes_associados",
                name: "clientes_associados",
                title: "Cód",
                className: "text-center",
            },
            {
                data: "DS_TABPRECO",
                name: "DS_TABPRECO",
                title: "Descrição",
            },
            {
                data: "QTD_ITENS",
                name: "QTD_ITENS",
                title: "Itens",
            },
            {
                data: "ASSOCIADOS",
                name: "ASSOCIADOS",
                title: "Clientes",
            },
            {
                data: "action",
                name: "action",
                title: "Ações",
                visible: false,
                className: "text-center",
            },
        ],
    });

    return tabelaPreco;
}

function initTableClienteTabela(tableId, data, route) {
    itemTabelaCliente = $("#" + tableId).DataTable({
        searching: false,
        paging: false,
        sDom: "t",
        processing: false,
        serverSide: false,
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.itemTabelaPrecoCliente,
            type: "GET",
            data: {
                cd_tabela: data.CD_TABPRECO,
            },
        },
        columns: [
            {
                data: "action",
                name: "action",
                title: "Ações",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "VENDEDOR",
                name: "VENDEDOR",
                title: "Vendedor",
            },
            {
                data: "SUPERVISOR",
                name: "SUPERVISOR",
                title: "Supervisor",
            },
        ],
    });
    return itemTabelaCliente;
}

function initTableItemTabelaPreco(
    route,
    idTabela,
    tela,
    idTabelaItem,
    idModal,
) {
    const title = $("#" + idModal + " .title-nm-tabela").text();

    $("#" + idModal).modal("show");
    if ($.fn.DataTable.isDataTable("#" + idTabelaItem)) {
        $("#" + idTabelaItem)
            .DataTable()
            .clear()
            .destroy();
    }
    $("#" + idTabelaItem).DataTable({
        paging: false,
        searching: true,
        scrollY: "300px",
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: "excelHtml5",
                        title: title,
                    },
                    {
                        extend: "print",
                        title: title,
                        customize: function (win) {
                            $(win.document.body)
                                .find("h1")
                                .css("font-size", "12pt")
                                .css("color", "#333");
                        },
                    },
                ],
            },
        },
        language: {
            url: route.language_datatables,
        },
        ajax: {
            url: route.itemtabelaPreco,
            type: "GET",
            data: {
                cd_tabela: idTabela,
                tela: tela,
            },
            beforeSend: function () {
                Swal.fire({
                    title: "Carregando...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            complete: function () {
                Swal.close();
            },
        },
        columns: [
            {
                data: "CD_TABELA",
                name: "CD_TABELA",
                title: "Cód Tabela",
                visible: false,
            },
            {
                data: "DESCRICAO",
                name: "DESCRICAO",
                width: "80%",
                title: "Descrição",
            },
            {
                data: "VALOR",
                name: "VALOR",
                title: "Valor",
            },
        ],
    });
}

function formatarNome(str) {
    return str.toLowerCase().replace(/\b\w/g, function (letra) {
        return letra.toUpperCase();
    });
}

function initTableTabelaPrecoPrevia() {
    tabela_preview = $("#item-tabela-preco").DataTable({
        paging: true,
        searching: true,
        destroy: true,
        scrollY: isMobile() ? false : "300px",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"],
        ],
        pageLength: isMobile() ? 10 : -1,
        language: {
            url: window.routes.language_datatables,
        },
        select: {
            style: "multi",
        },
        data: [],
        columns: [
            {
                data: null,
                width: "1%",
                render: DataTable.render.select(),
            },
            {
                title: "Cód",
                data: "ID",
                visible: false,
            },
            {
                title: "Descrição",
                data: "DESCRICAO",
                width: isMobile() ? "80%" : "60%",
            },
            {
                title: "Valor",
                data: "VALOR",
                width: isMobile() ? "20%" : "20%",
                render: $.fn.dataTable.render.number(".", ",", 2),
                className: "text-center",
            },
            {
                title: "Subgrupo",
                data: "SUBGRUPO",
                width: "20%",
                visible: isMobile() ? false : true, // Esconde em mobile
            },
        ],
        orderBy: [[0, "asc"]],
    });
}

function salvarVinculoTabelaPessoa(
    cd_tabela,
    cd_pessoa,
    routes,
    idModal,
    idTabela,
    inputCdPessoaMulti,
    csrf,
) {
    if (!cd_pessoa || cd_pessoa.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Por favor, selecione pelo menos um cliente para vincular.",
            customClass: {
                confirmButton: "btn btn-warning",
            },
        });
        return;
    }

    $.ajax({
        type: "POST",
        url: window.routes.vincularTabelaPreco,
        data: {
            _token: csrf,
            cd_tabela: cd_tabela,
            cd_pessoa: cd_pessoa,
        },
        dataType: "json",
        beforeSend: function () {
            $("#loading").removeClass("invisible");
        },
        success: function (response) {
            if (response.success) {
                $("#" + idModal).modal("hide");
                $("#" + idTabela)
                    .DataTable()
                    .destroy();
                if (idTabela === "tabela-preco") initTabelaPreco(routes);
                else {
                    initTableTabelaPrecoCadastradasPreview(routes);
                }

                $("#loading").addClass("invisible");
                $("#" + inputCdPessoaMulti)
                    .val("")
                    .trigger("change");

                Swal.fire({
                    title: "Atenção",
                    text: response.message,
                    icon: "success",
                    customClass: {
                        confirmButton: "btn btn-success",
                    },
                });
            } else {
                $("#loading").addClass("invisible");
                Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    text:
                        response.message ||
                        "Ocorreu um erro ao vincular a tabela. Tente novamente.",
                    customClass: {
                        confirmButton: "btn btn-warning",
                    },
                });
                return;
            }
        },
    });
}

function deleteTabelaPreco(
    routes,
    cd_tabela,
    nm_tabela,
    tipo_tabela,
    idTabela,
    csrf,
) {
    Swal.fire({
        icon: "warning",
        title: "Atenção",
        html:
            'Deseja realmente excluir esta tabela de preço?</br><span class="font-weight-bold">' +
            nm_tabela +
            "</span>",
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
        showCancelButton: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger mr-2 btn-delete",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: window.routes.deleteTabelaPreco,
                data: {
                    _token: csrf,
                    cd_tabela: cd_tabela,
                    tipo_tabela: tipo_tabela,
                },
                dataType: "json",
                beforeSend: function () {
                    Swal.fire({
                        title: "Deletando tabela...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (response) {
                    Swal.close();

                    if (response.success) {
                        // se for mobile, reinicia a tabela para ajustar o layout, se não, apenas recarrega os dados
                        if (isMobile()) {
                            initTableTabelaPrecoCadastradasPreview(
                                window.routes,
                            );
                        } else {
                            $("#" + idTabela)
                                .DataTable()
                                .ajax.reload();
                        }

                        Swal.fire({
                            icon: "success",
                            title: "Atenção",
                            text: response.message,
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-success",
                            },
                        });
                        
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Atenção",
                            text:
                                response.message ||
                                "Ocorreu um erro ao deletar a tabela. Tente novamente.",
                            customClass: {
                                confirmButton: "btn btn-warning",
                            },
                        });

                        return;
                    }
                },
            });
        }
    });
    return;
}

function initTableDivergenciaTabelaPreco(routes) {
    $("#tabela-divergencia").DataTable({
        processing: false,
        serverSide: false,
        destroy: true,
        pagingType: "simple",
        language: {
            url: window.routes.language_datatables,
        },
        ajax: {
            url: window.routes.divergenciaTabelaPreco,
            type: "get",
            data: {
                _token: "{{ csrf_token() }}",
            },
            beforeSend: function () {
                Swal.fire({
                    title: "Carregando...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            complete: function () {
                Swal.close();
            },
        },
        columns: [
            {
                data: "action",
                name: "action",
                title: "#",
            },
            {
                data: "NM_PESSOA",
                name: "NM_PESSOA",
                title: "Cliente",
            },
            {
                data: "DS_TABPRECO",
                name: "DS_TABPRECO",
                title: "Tabela de Preço",
            },
        ],
    });
}

function isMobile() {
    return window.innerWidth < 768; // Exemplo de breakpoint para mobile
}

function renderizarCards(dados, containerId) {
    const container = $("#" + containerId);
    container.empty();

    dados.forEach(function (item) {
        const card = `
                    <div class="card mb-3">
                        <div class="card-body shadow-sm p-3">
                            <div class="col-12 col-md-12">
                                <div class="form-group mb-0">                                  
                                    <label class="small">Tabela:
                                        <badge class="badge badge bg-primary">${item.QTD_ITENS} Itens</badge>
                                    </label> 
                                    <input type="text" 
                                        class="form-control form-control-sm"
                                        value="${item.DS_TABPRECO}" readonly />                                    
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="form-group mb-0">                                  
                                    <label class="small">Supervisor:</label> 
                                    <input type="text" 
                                        class="form-control form-control-sm"
                                        value="${item.SUPERVISOR}" readonly />                                    
                                </div>
                            </div>                  
                        </div>
                        <div class="card-footer p-2">
                            <div class="text-center">
                                ${item.action}
                            </div>
                        </div>
                    </div>
                `;
        container.append(card);
    });
}
