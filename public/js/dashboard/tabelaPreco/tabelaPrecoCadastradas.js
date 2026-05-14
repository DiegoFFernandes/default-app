// Tab para ver as tabelas cadastradas para importar
$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
    const target = $(e.target).attr("href");

    if (target === "#painel-cadastradas") {
        initTableTabelaPrecoCadastradasPreview();
    }
});

$(document).on("click", "#tab-cadastradas", function () {
    initTableTabelaPrecoCadastradasPreview();
});

$(document).on("click", ".btn-ver-itens-cadastradas", function () {
    var cd_tabela = $(this).data("cd_tabela");
    $(".title-nm-tabela").html($(this).data("nm_tabela"));

    initTableItemTabelaPreco(
        window.routes,
        cd_tabela,
        "tabela_preco_preview",
        "table-item-tab-preco-cadastradas",
        "modal-item-tab-preco-cadastradas",
    );
});

$(document).on("click", "#btn-salvar-vinculo", function () {
    var cd_tabela = $("#cd_tabela_preco").val();
    var cd_pessoa = $("#cd_pessoa_multi").val();
    const csrf = window.routes.csrfToken;
    salvarVinculoTabelaPessoa(
        cd_tabela,
        cd_pessoa,
        window.routes,
        "modal-vincular-tab-preco-pessoas",
        "tabela-preco-cadastradas",
        "cd_pessoa_multi",
        csrf,
    );
});

$(document).on("click", ".btn-importar", function () {
    var cd_tabela = $(this).data("cd_tabela");
    $.ajax({
        type: "POST",
        url: window.routes.importarTabelaPreco,
        data: {
            _token: window.routes.csrfToken,
            cd_tabela: cd_tabela,
        },
        dataType: "json",
        beforeSend: function () {
            Swal.fire({
                title: "Importando tabela...",
                text: "Por favor, aguarde.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: "swal2-border-radius-none",
                },
            });
        },
        success: function (response) {
            if (response.error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Erro ao importar",
                    text:
                        response.error ||
                        "Ocorreu um erro ao importar a tabela. Tente novamente.",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            } else {
                Swal.close();
                $("#tabela-preco-cadastradas").DataTable().destroy();
                initTableTabelaPrecoCadastradasPreview(window.routes);
                Swal.fire({
                    title: "Atenção",
                    text: response.message,
                    icon: "success",
                    customClass: {
                        confirmButton: "btn btn-success",
                    },
                });
            }
        },
    });
});

$(document).on("click", ".btn-vincular-tabela-cadastradas", function () {
    var cd_tabela = $(this).data("cd_tabela");
    var ds_tabela = $(this).data("ds_tabela");

    $("#cd_tabela_preco").val(cd_tabela);
    $("#ds_tabela_preco").val(ds_tabela);
    $("#modal-vincular-tab-preco-pessoas").modal("show");
    initSelect2Pessoa(
        "#cd_pessoa_multi",
        window.routes.searchPessoa,
        "#modal-vincular-tab-preco-pessoas",
    );
});

// Deletar tabela de preço na tab cadastradas
$(document).on("click", ".btn-delete-tabela-cadastradas", function () {
    var cd_tabela = $(this).data("cd_tabela");
    var nm_tabela = $(this).data("nm_tabela");
    const csrf = window.routes.csrfToken;
    deleteTabelaPreco(
        window.routes,
        cd_tabela,
        nm_tabela,
        "tabela_preco_preview",
        "tabela-preco-cadastradas",
        csrf,
    );
});

function initTableTabelaPrecoCadastradasPreview(route) {
    if (isMobile()) {
        $.ajax({
            url: window.routes.tabelaPrecoCadastradasPreview,
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
            success: function (json) {
                const dados = json.data || json;

                renderizarCards(dados, "card-container");
            },
            complete: function () {
                Swal.close();               
            },
        });

        return;
    }

    // if (tabelaPrecoCadastradas) {
    //     tabelaPrecoCadastradas.columns.adjust().draw();
    //     tabelaPrecoCadastradas.ajax.reload(null, false);
    //     return;
    // }

    tabelaPrecoCadastradas = $("#tabela-preco-cadastradas").DataTable({
        paging: true,
        searching: true,
        destroy: true,
        scrollY: "300px",
        pagingType: "simple",
        autoWidth: false,
        deferRender: true,
        language: {
            url: window.routes.language_datatables,
        },
        ajax: {
            url: window.routes.tabelaPrecoCadastradasPreview,
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
            }
        },        
        columns: [
            {
                title: "Ações",
                data: "action",
                width: "10%",
                orderable: false,
                searchable: false,
                className: "text-center no-wrap",
            },
            {
                title: "ID",
                data: "CD_TABPRECO",
                visible: false,
            },
            {
                title: "Tabela",
                data: "DS_TABPRECO",
            },
            {
                title: "Supervisor",
                data: "SUPERVISOR",
            },
            {
                title: "Itens",
                data: "QTD_ITENS",
                className: "text-center",
            },
        ],
    });
}
