function carregarDadosPrazoMedio(route) {

    const card = $("#cardPrazoMedio");
    const accordionContainer = card.find("#prazoMedioAccordionContainer");
    const totalGeralContainer = card.find(".prazoMedioValorTotal");
    const carregando = card.find(".loading-card");

    $.ajax({
        url: route['prazo_medio'],
        type: "GET",
        beforeSend: function () {
            carregando.removeClass("invisible");
            totalGeralContainer.text("0 dias");
            accordionContainer.empty();
        },
        success: function (data) {
            let totalGeralDias = 0;
            let totalGeralQtd = 0;

            const calcularMedia = (dias, qtd) =>
                qtd > 0 ? Math.round(dias / qtd) : 0;

            const compararPorMaiorMedia = (itemA, itemB) => calcularMedia(itemB.dias, itemB.qtd) - calcularMedia(itemA.dias, itemA.qtd);

            const gerarGerentesHtml = (gerentes) => {

                gerentes.sort(compararPorMaiorMedia);
                return gerentes
                    .map((gerente, i) => {
                        totalGeralDias += gerente.dias;
                        totalGeralQtd += gerente.qtd;

                        const mediaGerente = calcularMedia(
                            gerente.dias,
                            gerente.qtd
                        );
                        const supervisoresHtml = gerarSupervisoresHtml(
                            gerente.supervisores,
                            i
                        );

                        return `
                            <div class="card gerente-card">
                                <div class="card-header p-1">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link d-block text-left" type="button" data-toggle="collapse" data-target="#prazo-gerente-${i}">
                                            👔 ${gerente.nome} (Média: ${mediaGerente} dias)
                                        </button>
                                    </h2>
                                </div>
                                <div id="prazo-gerente-${i}" class="collapse">
                                    <div class="card-body">
                                        ${supervisoresHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                    })
                    .join("");
            };

            const gerarSupervisoresHtml = (supervisores, i) => {
                if (!supervisores) return "";

                supervisores.sort(compararPorMaiorMedia);
                return supervisores
                    .map((supervisor, j) => {
                        const mediaSupervisor = calcularMedia(
                            supervisor.dias,
                            supervisor.qtd
                        );
                        const vendedoresHtml = gerarVendedoresHtml(
                            supervisor.vendedores,
                            i,
                            j
                        );
                        return `
                            <div class="supervisor-container">
                                <button class="btn btn-sm btn-secondary mb-1 d-block btn-d-block text-left" data-toggle="collapse" data-target="#prazo-sup-${i}-${j}">
                                    🛡️ ${supervisor.nome} 
                                    <span class="">(Média: ${mediaSupervisor} dias)</span>
                                </button>
                                <div id="prazo-sup-${i}-${j}" class="collapse mt-2 mb-2">
                                    ${vendedoresHtml}
                                </div>
                            </div>
                        `;
                    })
                    .join("");
            };

            const gerarVendedoresHtml = (vendedores, i, j) => {
                if (!vendedores) return "";

                vendedores.sort(compararPorMaiorMedia);
                return vendedores
                    .map((vendedor, k) => {
                        const mediaVendedor = calcularMedia(
                            vendedor.dias,
                            vendedor.qtd
                        );
                        const clientesHtml = gerarClientesHtml(
                            vendedor.clientes
                        );
                        return `
                            <div class="vendedor-container">
                                <button class="btn btn-sm btn-info mb-1 d-block btn-d-block text-left" data-toggle="collapse" data-target="#prazo-vend-${i}-${j}-${k}">
                                    👤 ${vendedor.nome} 
                                    <span class="saldo">(Média: ${mediaVendedor} dias)</span>
                                </button>
                                <div id="prazo-vend-${i}-${j}-${k}" class="collapse mt-2 mb-2">
                                    <ul class="list-group">${clientesHtml}</ul>
                                </div>
                            </div>
                        `;
                    })
                    .join("");
            };

            const gerarClientesHtml = (clientes) => {
                if (!clientes) return "";
                return Object.values(clientes)
                    .map((cliente) => {
                        const mediaCliente = calcularMedia(
                            cliente.dias,
                            cliente.qtd
                        );
                        return `
                        <li class="list-group-item cliente-item d-flex justify-content-between align-items-start text-small">
                            🏢 ${cliente.nome} <span class="badge badge-primary badge-pill">${mediaCliente} dias</span>
                        </li>`;
                    })
                    .join("");
            };
            const htmlGerentes = gerarGerentesHtml(data);
            accordionContainer.append(htmlGerentes);

            const mediaGeral = calcularMedia(totalGeralDias, totalGeralQtd);
            totalGeralContainer.text(`${mediaGeral} dias`);
        },
        complete: function () {
            carregando.addClass("invisible");
        },
    });
}

$(function () {
    const cardContainer = $('#cardPrazoMedio');
    let debounceTimer;

    function normalizeString(str) {
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }

    cardContainer.on('input', '#buscarCliente', function () {
        clearTimeout(debounceTimer);
        const input = $(this);

        debounceTimer = setTimeout(() => {
            const termoBusca = normalizeString(input.val()); //normaliza o texto da busca
            const accordionContainer = cardContainer.find('#prazoMedioAccordionContainer');

            // input vazio volta o accordion para o estado original
            if (termoBusca === '') {
                accordionContainer.find('.hidden').removeClass('hidden');
                accordionContainer.find('.collapse').collapse('hide');
                $("#noResults").remove();
                return;
            }

            // filtra os clientes
            accordionContainer.find('.cliente-item').each(function () {
                const clienteAtual  = $(this);
                const textoCliente = clienteAtual.text();
                const textoNormalizado = normalizeString(textoCliente);

                if (textoNormalizado.includes(termoBusca)) {
                    clienteAtual.removeClass('hidden'); // mostra o cliente
                } else {
                    clienteAtual.addClass('hidden'); // esconde o cliente 
                }
            });
            const anyVisible = accordionContainer.find('.cliente-item:not(.hidden)').length > 0;

            // filtra os vendedores
            accordionContainer.find('.vendedor-container').each(function () {
                const vendedorAtual = $(this);
                if (vendedorAtual.find('.cliente-item:not(.hidden)').length === 0) {
                    vendedorAtual.addClass('hidden'); // esconde o vendedor
                } else {
                    vendedorAtual.removeClass('hidden'); // mostra o vendedor
                }
            });

            //filtra os supervisores
            accordionContainer.find('.supervisor-container').each(function () {
                const supervisorAtual = $(this);
                if (supervisorAtual.find('.vendedor-container:not(.hidden)').length === 0) {
                    supervisorAtual.addClass('hidden'); // esconde o supervisor
                } else {
                    supervisorAtual.removeClass('hidden'); // mostra o supervisor
                }
            });

            // filtra os gerentes
            accordionContainer.find('.gerente-card').each(function () {
                const gerenteAtual  = $(this);
                if (gerenteAtual.find('.supervisor-container:not(.hidden)').length === 0) {
                    gerenteAtual.addClass('hidden');  // esconde o gerente 
                } else {
                    gerenteAtual.removeClass('hidden'); // mostra o gerente
                    gerenteAtual.find('.collapse').collapse('show');
                }
            });

            if (!anyVisible && $("#noResults").length === 0) {
                $("#prazoMedioAccordionContainer").append(
                    '<div id="noResults">Nenhum cliente encontrado.</div>'
                );
            }
        }, 250);  //tempo para esperar o usuario parar de digitar
    });
});