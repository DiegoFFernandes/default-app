formularioDinamico(window.routes); // chama a função para deixar a pagina dinamica

initSelect2Pessoa("#pessoa", window.routes.searchPessoa);

$("#desenho, #medida").select2({
    theme: "bootstrap4",
    width: "100%",
    multiple: true,
    placeholder: "Selecione uma opção",
    allowClear: true,
});

$("#desenho").on("change", function () {
    carregaOpcoes("#desenho", "#medida", window.routes.searchMedida, "desenho");
});

$("#btn-adicional").on("click", function () {
    $("#modal-item-adicional").modal("show");
});

$(document).on("click", "#btn-associar", function () {
    const valor = $("#valor").val();
    const nomeTabela = $("#pessoa option:selected").text();

    if (valor === "") {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Por favor, insira um valor válido maior que zero.",
            customClass: {
                confirmButton: "btn btn-warning",
            },
        });
        return;
    } else {
        $("#item-tabela-preco").closest(".card").show();
    }

    if (!$.fn.DataTable.isDataTable("#item-tabela-preco")) {
        initTableTabelaPrecoPrevia();
    }

    $.ajax({
        url: window.routes.previaTabela,
        type: "GET",
        data: {
            _token: window.routes.csrfToken,
            select: "previa",
            pessoa: $("#pessoa").val(),
            desenho: $("#desenho").val(),
            medida: $("#medida").val(),
            valor: valor,
        },
        success: function (response) {
            if (response.errors) {
                Swal.fire({
                    icon: "error",
                    title: "Campos obrigatórios",
                    html: response.errors,
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            }
            const novos_dados = response.data;

            novos_dados.forEach(function (item) {
                item.valor = item.VALOR;
                itens_preview.set(item.ID, item);
            });
            dados_atualizados = Array.from(itens_preview.values());

            // Inverte a ordem dos dados para que os novos itens apareçam no topo
            dados_atualizados.reverse();

            // Limpa a tabela e adiciona os dados no topo
            tabela_preview.clear().rows.add(dados_atualizados).draw();

            Swal.fire({
                icon: "success",
                title: "Itens adicionados à prévia com sucesso!",
                showConfirmButton: false,
                timer: 2000,
            });
            $("#desenho, #medida, #valor").val("").trigger("change"); // limpa os inputs
        },
    });
});

$(document).on("click", "#btn-enviar-importar", function () {
    const dadosTabela = tabela_preview.rows().data().toArray();
    if (dadosTabela.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Nenhum item adicionado",
            text: "Por favor, adicione itens à tabela antes de salvar.",
            customClass: {
                confirmButton: "btn btn-warning",
            },
        });
        return;
    }
    $.ajax({
        url: window.routes.salvaItemTabelaPreco,
        type: "POST",
        data: {
            _token: window.routes.csrfToken,
            dadosTabela: dadosTabela,
        },
        beforeSend: function () {
            Swal.fire({
                title: "Salvando itens...",
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
            if (response.success) {
                Swal.close();

                Swal.fire({
                    title: "Atenção",
                    text: response.message,
                    icon: "success",
                    customClass: {
                        confirmButton: "btn btn-success",
                    },
                });
                recomecar();
            } else {
                Swal.close();

                Swal.fire({
                    icon: "error",
                    title: "Erro ao salvar",
                    text:
                        response.message ||
                        "Ocorreu um erro ao salvar os itens. Tente novamente.",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
    });
});

$("#btn-deletar-itens").on("click", function () {
    var linhasSelecionadas = tabela_preview.rows({
        selected: true,
    });

    if (linhasSelecionadas.count() === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Por favor, selecione pelo menos um item para deletar.",
            customClass: {
                confirmButton: "btn btn-warning",
            },
        });
        return;
    }

    Swal.fire({
        title: "Atenção",
        text: "Deseja realmente deletar os itens selecionados?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, deletar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            linhasSelecionadas.every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                itens_preview.delete(data.ID); // Remove do Map
                return true;
            });

            var dados_atualizados = Array.from(itens_preview.values());
            tabela_preview.clear().rows.add(dados_atualizados).draw();

            Swal.fire({
                icon: "success",
                title: "Itens deletados com sucesso!",
                customClass: {
                    confirmButton: "btn btn-success",
                },
            });
        }
    });
});

$(document).on("click", "#item-tabela-preco td:nth-child(3)", function () {
    var tr = $(this).closest("tr");
    var row = tabela_preview.row(tr);
    var rowData = row.data();
    var valorCell = tr.find("td").eq(2);
    var valorTabela = parseFloat(rowData.VALOR).toFixed(2);

    if (!valorCell.find("input").length) {
        valorCell.html(
            '<input type="text" inputmode="decimal" class="input-venda valor-edit" value="' +
                valorTabela +
                '" style="width: 100%; box-sizing: border-box;" />',
        );

        var input = valorCell.find("input");
        input.focus();
        input.select();

        input.on("blur", function (e) {
            var novoValor = parseFloat($(this).val().replace(",", "."));

            if (isNaN(novoValor)) {
                Swal.fire({
                    icon: "error",
                    title: "Valor inválido",
                    text: "Por favor, insira um número válido para o valor.",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                row.data(rowData).draw(false);
                return;
            }

            rowData.VALOR = novoValor;
            row.data(rowData).draw(false);
        });

        input.on("keydown", function (e) {
            if (e.which === 13) {
                // Enter
                e.preventDefault(); // evita quebra de linha
                $(this).blur(); // força o blur, que chama a função de atualização
            }
            if (e.which === 27) {
                // Esc
                e.preventDefault();
                rowData.VALOR = valorTabela;
                row.data(rowData).draw(false); // reverte para o valor original
            }
        });
    }
});

$(document).on("click", "#btn-add-modal", function () {
    const vulc_carga_valor = $("#input-vulc-carga-valor").val() || 0;
    const vulc_agricola_valor = $("#input-vulc-agricola-valor").val() || 0;
    const manchao_valor = $("#input-manchao-valor").val() || null;
    const manchao_agricola_valor =
        $("#input-manchao-valor-agricola").val() || 0;
    const enchimento_valor = $("#input-enchimento-valor").val() || 0;
    const enchimento_ombro_1_valor =
        $("#input-enchimento-ombro-1-valor").val() || 0;
    const enchimento_ombro_2_valor =
        $("#input-enchimento-ombro-2-valor").val() || 0;

    const pessoa = $("#pessoa").val();

    $.ajax({
        url: window.routes.searchAdicional,
        type: "GET",
        data: {
            _token: window.routes.csrfToken,
            pessoa: pessoa,
            vulc_carga_valor: vulc_carga_valor,
            vulc_agricola_valor: vulc_agricola_valor,
            manchao_valor: manchao_valor,
            manchao_agricola_valor: manchao_agricola_valor,
            enchimento_valor: enchimento_valor,
            enchimento_ombro_1_valor: enchimento_ombro_1_valor,
            enchimento_ombro_2_valor: enchimento_ombro_2_valor,
        },
        beforeSend: function () {
            Swal.fire({
                title: "Adicionando itens adicionais...",
                text: "Por favor, aguarde.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        },
        success: function (response) {
            if (response.errors) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Campos obrigatórios",
                    html: response.errors,
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            }
            const novos_dados = response.data;

            novos_dados.forEach(function (item) {
                item.valor = item.VALOR;
                itens_preview.set(item.ID, item);
            });
            const dados_atualizados = Array.from(itens_preview.values());

            tabela_preview.clear().rows.add(dados_atualizados).draw();

            Swal.fire({
                icon: "success",
                title: "Itens adicionados à prévia com sucesso!",
                showConfirmButton: false,
                timer: 2000,
            });
        },
        complete: function () {
            Swal.close();
        },
    });

    const dados_atualizados = Array.from(itens_preview.values());
    tabela_preview.clear().rows.add(dados_atualizados).draw();
    $("#modal-item-adicional").modal("hide");
    // Limpar os campos do modal
    $("#input-vulc-carga-valor").val("");
    $("#input-vulc-agricola-valor").val("");
    $("#input-manchao-valor").val("");
    $("#input-manchao-valor-agricola").val("");
    $("#input-enchimento-valor").val("");
    $("#input-enchimento-ombro-1-valor").val("");
    $("#input-enchimento-ombro-2-valor").val("");
});

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

function formularioDinamico(route) {
    const cardTabela = $("#item-tabela-preco").closest(".card");

    $("#desenho, #medida, #valor, #btn-associar").closest(".form-group").hide(); // esconde os select
    cardTabela.hide(); // esconde o card da tabela

    // exibe desenho quando pessoa for selecionado
    $("#pessoa").on("change", function () {
        if ($(this).val() === null || $(this).val().length === 0) {
            // Impede a requisição caso esteja vazio
            return false;
        }
        $.ajax({
            type: "GET",
            url: route.verificaTabelaCadastrada,
            data: {
                idTabela: $(this).val(),
            },
            success: function (response) {
                if (response.data.length > 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Atenção",
                        text: "Já existe uma tabela de preço cadastrada para este cliente, deseja atualiza-la?",
                        showCancelButton: true,
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não",
                        customClass: {
                            confirmButton: "btn btn-danger mr-2",
                            cancelButton: "btn btn-secondary",
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#desenho").closest(".form-group").show();
                            cardTabela.show();
                            initTableTabelaPrecoPrevia();

                            // Adiciona os dados existentes na tabela
                            response.data.forEach(function (item) {
                                itens_preview.set(item.ID, item); // Adiciona no Map
                            });

                            tabela_preview
                                .clear()
                                .rows.add(Array.from(itens_preview.values()))
                                .draw(); // Atualiza a tabela com os dados existentes
                        } else {
                            $("#pessoa").val("").trigger("change");
                            $("#desenho, #medida, #valor, #btn-associar")
                                .closest(".form-group")
                                .hide();
                        }
                    });
                } else {
                    $("#desenho").closest(".form-group").show();
                }
            },
        });
    });

    // exibe medida quando desenho for selecionado
    $("#desenho").on("change", function () {
        if ($(this).val() && $(this).val().length > 0) {
            $("#medida").closest(".form-group").show();
        } else {
            $("#medida, #valor").closest(".form-group").hide();
        }
    });

    // exibe valor quando medida for selecionado
    $("#medida").on("change", function () {
        if ($(this).val() && $(this).val().length > 0) {
            $("#valor").closest(".form-group").show();
            $("#btn-associar").closest(".form-group").show();
        } else {
            $("#valor").closest(".form-group").hide();
            $("#btn-associar").closest(".form-group").hide();
        }
    });

    // limpa e esconde
    $("#btn-recomecar").on("click", function () {
        Swal.fire({
            icon: "warning",
            title: "Atenção",
            text: "Deseja realmente reiniciar?",
            showCancelButton: true,
            confirmButtonText: "Sim",
            cancelButtonText: "Não",
            customClass: {
                confirmButton: "btn btn-danger mr-2",
                cancelButton: "btn btn-secondary",
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                recomecar();
            }
        });
    });
}

function carregaOpcoes(selectOrigem, selectDestino, url, paramName) {
    let selected = $(selectOrigem).val();

    $(selectDestino).empty().trigger("change");

    if (selected && selected.length > 0) {
        $.ajax({
            url: url,
            type: "GET",
            data: {
                [paramName]: selected,
                select: paramName,
            },
            success: function (data) {
                data.forEach(function (item) {
                    let newOption = new Option(
                        item.DESCRICAO,
                        item.ID,
                        false,
                        false,
                    );
                    $(selectDestino).append(newOption);
                });
                $(selectDestino).trigger("change");
            },
        });
    }
}

function recomecar() {
    $("#item-tabela-preco").DataTable().clear().destroy();
    dados_atualizados = [];
    itens_preview = new Map();
    $("#pessoa, #desenho, #medida, #valor").val("").trigger("change"); // limpa os inputs
    $("#desenho").closest(".form-group").hide(); // esconde os selects
    $("#item-tabela-preco").closest(".card").hide();
}
