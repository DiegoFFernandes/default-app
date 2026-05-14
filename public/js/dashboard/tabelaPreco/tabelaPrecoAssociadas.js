$(document).on("click", "#tab-associadas", function () {
    initTabelaPreco(window.routes);
});

$(document).on("click", ".btn-ver-itens-associadas", function () {
    var cd_tabela = $(this).data("cd_tabela");
    $(".title-nm-tabela").html($(this).data("nm_tabela"));
    initTableItemTabelaPreco(
        window.routes,
        cd_tabela,
        "tabela_preco",
        "table-item-tab-preco",
        "modal-item-tab-preco",
    );
});

$(document).on(
    "click",
    "#tabela-preco .btn-vincular-tabela-associadas",
    function () {
        var cd_tabela = $(this).data("cd_tabela");
        $("#cd_tabela_preco2").val(cd_tabela);
        var ds_tabela = $(this).closest("tr").find("td:eq(1)").text();
        $("#ds_tabela_preco2").val(ds_tabela);
        $("#modal-vincular-tab-preco-pessoas2").modal("show");
        initSelect2Pessoa(
            "#cd_pessoa_multi2",
            window.routes.searchPessoa,
            "#modal-vincular-tab-preco-pessoas2",
        );
    },
);

//Cancelar o vinculo da tabela de preço com o cliente
$(document).on("click", ".btn-cancelar-vinculo", function () {
    const cd_tabela = $(this).data("cd_tabela");
    const cd_pessoa = $(this).data("cd_pessoa");

    Swal.fire({
        title: "Confirmar Cancelamento",
        text: "Você tem certeza que deseja cancelar o vínculo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, cancelar",
        cancelButtonText: "Não, manter",
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: window.routes.cancelarVinculo,
                data: {
                    _token: window.routes.csrfToken,
                    cd_tabela: cd_tabela,
                    cd_pessoa: cd_pessoa,
                },
                success: function (response) {
                    $("#cliente-tabela-" + cd_tabela)
                        .DataTable()
                        .ajax.reload();
                    $("#tabela-divergencia").DataTable().ajax.reload();
                    Swal.fire({
                        icon: "success",
                        title: "Vínculo Cancelado",
                        text: response.message,
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    });
                },
            });
        }
    });
});

$(document).on("click", "#btn-salvar-vinculo2", function () {
    var cd_tabela = $("#cd_tabela_preco2").val();
    var cd_pessoa = $("#cd_pessoa_multi2").val();
    const csrf = window.routes.csrfToken;
    salvarVinculoTabelaPessoa(
        cd_tabela,
        cd_pessoa,
        window.routes,
        "modal-vincular-tab-preco-pessoas2",
        "tabela-preco",
        "cd_pessoa_multi2",
        csrf,
    );
});

// Deletar tabela de preço na tab associadas
$(document).on("click", ".btn-delete-tabela-associadas", function () {
    var cd_tabela = $(this).data("cd_tabela");
    var nm_tabela = $(this).data("nm_tabela");
    const csrf = window.routes.csrfToken;
    deleteTabelaPreco(
        window.routes,
        cd_tabela,
        nm_tabela,
        "tabela_preco",
        "tabela-preco",
        csrf,
    );
});

const chkNaoAssociados = document.getElementById("checkNaoAssociadas");
const chkAssociados = document.getElementById("checkAssociadas");

$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    const valor = parseInt(data[3]) || 0; // Use data for the "Associados" column

    const naoAssociados = chkNaoAssociados && chkNaoAssociados.checked;
    const associados = chkAssociados && chkAssociados.checked;

    // Ambos marcados mostra tudo
    if (naoAssociados && associados) {
        return true;
    }
    // Só não associados
    if (naoAssociados) {
        return valor === 0;
    }
    // Só associados
    if (associados) {
        return valor > 0;
    }
    return true;
});

if (chkNaoAssociados) {
    chkNaoAssociados.addEventListener("change", function () {
        tabelaPreco.draw();
    });
}
if (chkAssociados) {
    chkAssociados.addEventListener("change", function () {
        tabelaPreco.draw();
    });
}


