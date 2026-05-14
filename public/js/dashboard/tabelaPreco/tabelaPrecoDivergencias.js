// Tab para ver as tabelas cadastradas para importar
$(document).on("click", "#tab-divergencia", function () {
    initTableDivergenciaTabelaPreco(window.routes);
});

$(document).on("click", ".btn-ver-itens-divergencia", function () {
    var cd_tabela = $(this).data("cd_tabela");
    $(".title-nm-tabela").html($(this).data("nm_tabela"));
    initTableItemTabelaPreco(
        window.routes,
        cd_tabela,
        "tabela_preco",
        "table-item-tab-preco-divergencia",
        "modal-item-tab-preco-divergencia",
    );
});
