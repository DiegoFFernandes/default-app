$(document).on("click", ".btn-remover-pneus-lote", function () {
    let cd_empresa = $(this).data("empresa");
    let tabela = $("#pneus-lote-pcp-" + cd_empresa).DataTable();

    let selectedRows = [tabela.row($(this).closest("tr")).data()];

    removerOrdensLotePCP(selectedRows, tabela);
});

$(document).on("click", ".btn-remover-todos-pneus-lote-pcp", function () {
    let cd_empresa = $(this).data("cd_empresa");
    let tableId = "pneus-lote-pcp-" + cd_empresa;
    let tabela = $("#" + tableId).DataTable();

    let selectedOps = [];
    $('#' + tableId).closest('.dataTables_wrapper').find('.dt-row-checkbox-pcp:checked').each(function() {
        selectedOps.push(String($(this).data('op')));
    });
    let selectedRows = tabela.rows().data().toArray().filter(function(row) {
        return selectedOps.includes(String(row.NR_OP));
    });

    removerOrdensLotePCP(selectedRows, tabela);
});

function removerOrdensLotePCP(selectedRows, tabela) {
    if (selectedRows.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhum pneu selecionado.",
            text: "Por favor, selecione ao menos um pneu para remover do lote.",
        });
        return;
    }

    let possuiExameInicial = false;

    selectedRows.forEach(function (rowData) {
        if (rowData.CD_ETAPA > 0) {
            possuiExameInicial = true;
            return false; // interrompe o loop
        }
    });

    if (possuiExameInicial) {
        Swal.fire({
            title: "Atenção",
            text: "Não é possível remover os pneus selecionados, pois um ou mais já passaram do exame inicial.",
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

    confirmRemoverPneusLote(selectedRows, tabela);
}

function confirmRemoverPneusLote(
    selectedRows,
    tabelaPrincipal,
    tabelasRelacionadas = [],
) {
    Swal.fire({
        title: "Confirmação",
        text: "Tem certeza que deseja remover os pneus deste lote?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, remover",
        cancelButtonText: "Cancelar",
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger btn-sm mr-2",
            cancelButton: "btn btn-secondary btn-sm",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Lógica para remover os pneus do lote
            $.ajax({
                type: "POST",
                url: window.routes.removerOrdemProducaoLotePcp,
                data: {
                    ordem_producao: selectedRows,
                    _token: window.routes.token,
                },
                beforeSend: function () {
                    Swal.fire({
                        title: "Removendo pneus...",
                        text: "Por favor, aguarde enquanto os pneus são removidos do lote.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (response) {
                    Swal.close(); // fecha o modal de carregamento
                    if (response.success) {
                        Swal.fire({
                            title: "Sucesso",
                            text: response.message,
                            icon: "success",
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                        });
                        tabelaPrincipal.ajax.reload();

                        tabelasRelacionadas.forEach(
                            function (tabelaRelacionada) {
                                setTimeout(function () {
                                    tabelaRelacionada.ajax.reload();
                                }, 500); // Atraso de 500ms para garantir que a tabela principal seja recarregada primeiro
                            },
                        );
                    } else {
                        Swal.fire({
                            title: "Erro",
                            text: response.message,
                            icon: "error",
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close(); // fecha o modal de carregamento
                    Swal.fire({
                        title: "Erro",
                        text: "Ocorreu um erro ao remover os pneus do lote. Por favor, tente novamente.",
                        icon: "error",
                        showConfirmButton: true,
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
}
