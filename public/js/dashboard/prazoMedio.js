
function carregarDadosPrazoMedio(route) {
    const card = $('#cardPrazoMedio');
    const accordionContainer = card.find('#prazoMedioAccordionContainer');
    const totalGeralContainer = card.find('.prazoMedioValorTotal');
    const carregando = card.find('.loading-card');

    $.ajax({
        url: route,
        type: 'GET',
        beforeSend: function() {
            carregando.removeClass('invisible');
            totalGeralContainer.text('0 dias');
        },
        success: function(data) {
            let totalGeralDias = 0;
            let totalGeralQtd = 0;

            const calcularMedia = (dias, qtd) => (qtd > 0 ? Math.round(dias / qtd) : 0);

            const gerarGerentesHtml = (gerentes) => {
                return gerentes
                    .map((gerente, i) => {
                        totalGeralDias += gerente.dias;
                        totalGeralQtd += gerente.qtd;

                        const mediaGerente = calcularMedia(gerente.dias, gerente.qtd);
                        const supervisoresHtml = gerarSupervisoresHtml(gerente.supervisores, i);

                        return `
                            <div class="card">
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
                .join('');
            };

            const gerarSupervisoresHtml = (supervisores, i) => {
                if (!supervisores) return '';
                return supervisores
                    .map((supervisor, j) => {
                        const mediaSupervisor = calcularMedia(supervisor.dias, supervisor.qtd);
                        const vendedoresHtml = gerarVendedoresHtml(supervisor.vendedores, i, j);
                        return `
                            <button class="btn btn-sm btn-secondary mb-1 d-block" data-toggle="collapse" data-target="#prazo-sup-${i}-${j}">
                                🛡️ ${supervisor.nome} (Média: ${mediaSupervisor} dias)
                            </button>
                            <div id="prazo-sup-${i}-${j}" class="collapse mt-2 mb-2 ml-3">
                                ${vendedoresHtml}
                            </div>
                        `;
                    })
                .join('');
            };

            const gerarVendedoresHtml = (vendedores, i, j) => {
                if (!vendedores) return '';
                return vendedores
                    .map((vendedor, k) => {
                        const mediaVendedor = calcularMedia(vendedor.dias, vendedor.qtd);
                        const clientesHtml = gerarClientesHtml(vendedor.clientes);
                        return `
                            <button class="btn btn-sm btn-info mb-1 d-block" data-toggle="collapse" data-target="#prazo-vend-${i}-${j}-${k}">
                                👤 ${vendedor.nome} (Média: ${mediaVendedor} dias)
                            </button>
                            <div id="prazo-vend-${i}-${j}-${k}" class="collapse mt-2 mb-2 ml-3">
                                <ul class="list-group">${clientesHtml}</ul>
                            </div>
                        `;
                    })
                .join('');
            };

            const gerarClientesHtml = (clientes) => {
                if (!clientes) return '';
                return Object.values(clientes)
                    .map(cliente => {
                        const mediaCliente = calcularMedia(cliente.dias, cliente.qtd);
                        return `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            🏢 ${cliente.nome} <span class="badge badge-primary badge-pill">${mediaCliente} dias</span>
                        </li>`;
                    })
                .join('');
            };
            const htmlGerentes = gerarGerentesHtml(data);
            accordionContainer.append(htmlGerentes);
            
            const mediaGeral = calcularMedia(totalGeralDias, totalGeralQtd);
            totalGeralContainer.text(`${mediaGeral} dias`);
        },
        complete: function() {
            carregando.addClass('invisible');
        }
    });
}
