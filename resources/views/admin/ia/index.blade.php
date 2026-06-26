@extends('layouts.master')

@section('title', 'Assistente IA')

@section('content_top_nav_right')
    <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block" style="display: none;">
            <div id="searchBox">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Pesquisar..."
                        aria-label="Pesquisar" id="customSearch">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </li>
@endsection

@section('content')
    <section class="content-fluid" id="conteudo-ia">

        <div id="resposta-info"></div>

        <div class="row d-none" id="row-resposta">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center flex-wrap" style="gap:8px;">
                        <h5 class="card-title card-header-tabela mb-0 mr-auto"></h5>
                        <div class="input-group input-group-sm d-none" id="busca-accordion-group" style="max-width:240px;">
                            <input type="text" id="busca-accordion"
                                class="form-control"
                                placeholder="Buscar vendedor ou cliente...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="limpar-busca-accordion"
                                    title="Limpar busca">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-info d-none" id="btn-resumo-ia"
                            onclick="abrirResumoIA()" title="Gerar resumo com IA">
                            <i class="fas fa-robot mr-1"></i> Resumo IA
                        </button>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-default" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-0 pr-3">
                        <div class="row">
                            <div class="col-md-8 pr-3" id="resposta-tabela" style="border-right: 1px solid #dee2e6;"></div>
                            <div class="col-md-4">
                                <p id="resposta-progress-title-vendedor" class="text-center"></p>
                                <div id="resposta-progress-vendedor" style="max-height: 400px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Coletas por Banda / Cliente</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-default" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-0 pr-3">
                        <div class="row">
                            <div class="col-md-8 pr-3" style="border-right: 1px solid #dee2e6;">
                                <p class="text-center"><strong>Coletas por Banda</strong></p>
                                <div class="chart">
                                    <div class="chartjs-bar">
                                        <canvas id="resposta-grafico"
                                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p id="resposta-progress-title-cliente" class="text-center">
                                    <strong>Coletas por Cliente</strong>
                                </p>
                                <div id="resposta-progress-cliente" style="max-height: 400px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center align-items-center" style="height: 70vh;" id="bloco-central">
            <div class="col-md-6">
                <h3 class="mb-4 text-center title-ia">
                    E ai {{ Auth()->user()->name }}, vamos começar?
                </h3>

                <div class="p-2" id="input-area">
                    <div class="ia-input-inner">
                        <input type="text" class="ia-input" id="pergunta"
                            placeholder="Ex: Quantos pneus foram coletados em março de 2026?"
                            onkeypress="if(event.key === 'Enter') perguntarIA()">
                        <div class="ia-input-actions">
                            <button class="btn btn-primary btn-circle-ia btn-enviar" type="button"
                                onclick="perguntarIA()" title="Enviar">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-circle-ia btn-nova-pergunta" type="button"
                                onclick="novaPergunta()" title="Nova pergunta">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center px-2 mt-1 sugestoes-ia">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill sugestao-chip m-1"
                        data-pergunta="Quantos pneus foram coletados hoje?">
                        <i class="fas fa-calendar-day mr-1"></i>Coletas de hoje
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill sugestao-chip m-1"
                        data-pergunta="Quantos pneus foram coletados em março de 2026?">
                        <i class="fas fa-calendar-alt mr-1"></i>Coletas mar/2026
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill sugestao-chip m-1"
                        data-pergunta="Quantos pneus foram coletados em junho de 2026?">
                        <i class="fas fa-calendar-alt mr-1"></i>Coletas jun/2026
                    </button>
                </div>
            </div>
        </div>

    </section>

    @include('admin.ia.resumo-painel', [
        'painelId' => 'painel-resumo-ia',
        'titulo'   => 'Resumo IA',
    ])
@stop

@section('css')
    <style>
        /* ── Modo chat: input fixo no rodapé ── */
        .modo-chat #bloco-central {
            height: auto !important;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 5px 0;
            background-color: #33393f75;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .modo-chat #input-area {
            max-width: 800px;
            margin: 0 auto;
            transition: all .3s ease-in-out;
        }

        .modo-chat #row-resposta {
            padding-bottom: 66px;
            display: block !important;
        }

        /* quando sidebar aberta */
        body:not(.sidebar-collapse) .modo-chat #input-area {
            margin-left: 125px;
        }

        /* quando sidebar fechada */
        body.sidebar-collapse .modo-chat #input-area {
            margin-left: auto;
        }

        /* ── Visibilidade por modo ── */
        .modo-chat .title-ia {
            display: none;
        }

        .modo-chat .sugestoes-ia .sugestao-chip {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.4);
        }

        .modo-chat .sugestoes-ia .sugestao-chip:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .btn-nova-pergunta {
            display: none;
        }

        .modo-chat .btn-nova-pergunta {
            display: inline-flex !important;
            align-items: center;
        }

        /* ── Tipografia ── */
        .title-ia {
            margin-bottom: 3.5rem !important;
        }

        .resumo-ia {
            font-size: 0.9rem;
        }

        /* ── Input pill ── */
        .ia-input-inner {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 50px;
            padding: 6px 6px 6px 20px;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .ia-input-inner:focus-within {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .ia-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 1rem;
            padding: 4px 8px 4px 0;
            min-width: 0;
        }

        .ia-input-actions {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .btn-circle-ia {
            width: 38px;
            height: 38px;
            border-radius: 50% !important;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .modo-chat .ia-input-inner {
            padding: 3px 4px 3px 16px;
        }

        .modo-chat .ia-input {
            font-size: 0.9rem;
        }

        .modo-chat .btn-circle-ia {
            width: 32px;
            height: 32px;
        }

        /* ── Botões sugestão ── */
        .sugestao-chip {
            font-size: 0.8rem;
            transition: all 0.15s ease;
        }

        .sugestao-chip:hover {
            background-color: #6c757d;
            color: #fff;
        }

        /* ── Info boxes ── */
        .info-box-custom {
            border-radius: 12px;
            padding: 6px;
            transition: all 0.2s ease;
            min-height: 60px !important;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .info-box-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .info-box-custom .info-box-icon {
            font-size: 20px;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            margin: 0 10px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .info-box-custom .info-box-text {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        .info-box-custom .info-box-number {
            font-size: 20px;
            margin-top: -4px !important;
            font-weight: 600;
        }

        /* ── Responsivo ── */
        @media (max-width: 767.98px) {
            .card-title {
                font-size: 1rem !important;
            }

            .info-box-custom .info-box-number {
                font-size: 16px !important;
            }

            .info-box-custom .info-box-text {
                font-size: 10px !important;
            }

            .sugestoes-ia {
                display: none;
            }
        }
    </style>
@stop

@section('js')
    <script>
        window.routes = {
            perguntar: '{{ route('ia-perguntar') }}',
            resumo:          '{{ route('ia-resumo') }}',
            resumoWhatsapp:  '{{ route('ia-resumo-whatsapp') }}',
        };

        let progressDataGlobalVendedores = null;
        let progressDataGlobalClientes = null;
        let dadosGlobal  = null;
        let intentGlobal = null;

        // ── Enviar pergunta ──────────────────────────────────────────────────────

        function perguntarIA() {
            const pergunta = document.getElementById('pergunta').value.trim();

            if (!pergunta) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Digite uma pergunta antes de continuar.' });
                return;
            }

            Swal.fire({
                title: 'Processando pergunta...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(window.routes.perguntar, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ pergunta })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();

                    if (data.tipo === 'texto') {
                        Swal.fire({ title: data.titulo, text: data.dados });
                        return;
                    }

                    if (!data.tabela?.dados?.length) {
                        Swal.fire({ icon: 'info', title: 'Sem dados', text: 'Nenhum resultado encontrado.' });
                        return;
                    }

                    dadosGlobal  = data.tabela.dados;
                    intentGlobal = data.intent ?? 'pneus_coletados';
                    renderAccordionIA(dadosGlobal);

                    if (data.componentes) renderInfoBox(data.componentes);

                    if (data.progress_vendedores) {
                        progressDataGlobalVendedores = data.progress_vendedores;
                        document.getElementById('resposta-progress-title-vendedor').innerHTML =
                            `<strong>${data.progress_vendedores.titulo}</strong>`;
                        renderProgressBarItens(data.progress_vendedores.progress, 'resposta-progress-vendedor');
                    }

                    if (data.progress_clientes) {
                        progressDataGlobalClientes = data.progress_clientes;
                        document.getElementById('resposta-progress-title-cliente').innerHTML =
                            `<strong>${data.progress_clientes.titulo}</strong>`;
                        renderProgressBarItens(data.progress_clientes.progress, 'resposta-progress-cliente');
                    }

                    if (data.progress_desenho_banda) renderChartBar(data.progress_desenho_banda);

                    $('.card-header-tabela').text(data.tabela.titulo);
                    $('#row-resposta').removeClass('d-none');
                    $('#conteudo-ia').addClass('modo-chat');
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Erro', text: 'Ocorreu um erro ao processar a pergunta. Tente novamente.' });
                    console.error('Erro ao perguntar IA:', error);
                });
        }

        // ── Nova pergunta (reset) ────────────────────────────────────────────────

        function novaPergunta() {
            progressDataGlobalVendedores = null;
            progressDataGlobalClientes = null;

            ['resposta-info', 'resposta-tabela', 'resposta-progress-vendedor',
             'resposta-progress-cliente', 'resposta-progress-title-vendedor'].forEach(id => {
                document.getElementById(id).innerHTML = '';
            });

            dadosGlobal  = null;
            intentGlobal = null;
            fecharPainelIA('painel-resumo-ia');
            $('#busca-accordion').val('');
            $('#busca-accordion-group').addClass('d-none');
            $('#btn-resumo-ia').addClass('d-none');

            document.getElementById('pergunta').value = '';

            if (window.graficoBarra) {
                window.graficoBarra.destroy();
                window.graficoBarra = null;
            }

            $('.card-header-tabela').text('');
            $('#row-resposta').addClass('d-none');
            $('#conteudo-ia').removeClass('modo-chat');
            document.getElementById('pergunta').focus();
        }

        // ── Resumo IA ────────────────────────────────────────────────────────────

        function abrirResumoIA() {
            if (!dadosGlobal) return;
            abrirPainelIA('painel-resumo-ia', dadosGlobal, intentGlobal);
        }

        // ── Accordion: Vendedor → Pessoa → Itens ────────────────────────────────

        function renderAccordionIA(dados) {
            const agrupado = {};

            dados.forEach(item => {
                const vendedor = item.NM_VENDEDOR ?? 'Sem Vendedor';
                const pessoa   = item.NM_PESSOA   ?? 'Sem Cliente';

                if (!agrupado[vendedor]) agrupado[vendedor] = { qtd: 0, valor: 0, clientes: {} };
                if (!agrupado[vendedor].clientes[pessoa])
                    agrupado[vendedor].clientes[pessoa] = { qtd: 0, valor: 0, itens: [] };

                agrupado[vendedor].qtd   += parseInt(item.QTD) || 0;
                agrupado[vendedor].valor += parseFloat(item.VL_TOTAL) || 0;
                agrupado[vendedor].clientes[pessoa].qtd   += parseInt(item.QTD) || 0;
                agrupado[vendedor].clientes[pessoa].valor += parseFloat(item.VL_TOTAL) || 0;
                agrupado[vendedor].clientes[pessoa].itens.push(item);
            });

            const htmlVendedores = Object.entries(agrupado).map(([vendedor, vData], vi) => {
                const vId = `ia-v-${vi}`;

                const htmlPessoas = Object.entries(vData.clientes).map(([pessoa, pData], pi) => {
                    const pId = `ia-p-${vi}-${pi}`;

                    const htmlItens = pData.itens.map(item => `
                        <tr>
                            <td class="text-nowrap">${item.DS_SERVICOPNEU ?? ''}</td>
                            <td>${formatarData(item.DT_EMISSAO)}</td>
                            <td class="text-center">${parseInt(item.QTD) || 0}</td>
                            <td class="text-right">${formatarMoeda(item.VALOR_MEDIO)}</td>
                            <td class="text-right">${formatarMoeda(item.VL_TOTAL)}</td>
                        </tr>
                    `).join('');

                    return `
                        <div class="card mb-1 border-0 shadow-none accordion-pessoa"
                             data-pessoa="${pessoa.toLowerCase()}">
                            <div class="card-header p-0" style="background:#f4f6f9;">
                                <button class="btn btn-block text-left d-flex justify-content-between align-items-center px-3 py-2"
                                        type="button" data-toggle="collapse" data-target="#${pId}"
                                        style="font-size:0.8rem; color:#333;">
                                    <span>
                                        <i class="fas fa-building mr-2 text-info"></i>${pessoa}
                                    </span>
                                    <span>
                                        <span class="badge badge-info">${pData.qtd} pneus</span>
                                        <span class="badge badge-success ml-1">${formatarMoeda(pData.valor)}</span>
                                    </span>
                                </button>
                            </div>
                            <div id="${pId}" class="collapse">
                                <div class="card-body p-2">
                                    <table class="table table-sm table-bordered table-striped mb-0"
                                           style="font-size:0.78rem;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Serviço</th>
                                                <th>Data</th>
                                                <th class="text-center">Qtd</th>
                                                <th class="text-right">P.Médio</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>${htmlItens}</tbody>
                                        <tfoot class="font-weight-bold bg-light">
                                            <tr>
                                                <td colspan="2">Total</td>
                                                <td class="text-center">${pData.qtd}</td>
                                                <td></td>
                                                <td class="text-right">${formatarMoeda(pData.valor)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="card mb-1 accordion-vendedor" data-vendedor="${vendedor.toLowerCase()}">
                        <div class="card-header p-0" style="background:#3c3f43;">
                            <button class="btn btn-block text-left d-flex justify-content-between align-items-center px-3 py-2 text-white"
                                    type="button" data-toggle="collapse" data-target="#${vId}"
                                    aria-expanded="false" style="font-size:0.85rem;">
                                <span>
                                    <i class="fas fa-user mr-2 text-warning"></i>
                                    <strong>${vendedor}</strong>
                                </span>
                                <span>
                                    <span class="badge badge-warning">${vData.qtd} pneus</span>
                                    <span class="badge badge-success ml-1">${formatarMoeda(vData.valor)}</span>
                                </span>
                            </button>
                        </div>
                        <div id="${vId}" class="collapse" data-parent="#accordion-ia">
                            <div class="card-body p-2">
                                <div class="accordion" id="acc-${vId}">
                                    ${htmlPessoas}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('resposta-tabela').innerHTML = `
                <div id="accordion-ia" style="max-height:480px; overflow-y:auto;">
                    ${htmlVendedores}
                </div>
            `;

            $('#busca-accordion').val('');
            $('#busca-accordion-group').removeClass('d-none');
            $('#btn-resumo-ia').removeClass('d-none');
        }

        // ── Filtro compartilhado ─────────────────────────────────────────────────

        function filtrarAccordion(termo) {
            termo = (termo || '').toLowerCase().trim();

            $('#accordion-ia .accordion-vendedor').each(function() {
                const vendedorNome = $(this).data('vendedor') || '';
                const vendedorMatch = !termo || vendedorNome.includes(termo);
                let anyPessoa = false;

                $(this).find('.accordion-pessoa').each(function() {
                    const pessoaNome = $(this).data('pessoa') || '';
                    const visible = vendedorMatch || pessoaNome.includes(termo);
                    $(this).toggle(visible);
                    if (visible) anyPessoa = true;
                });

                const show = vendedorMatch || anyPessoa;
                $(this).toggle(show);
                if (termo && show) $(this).find('.collapse').first().collapse('show');
            });

            if (progressDataGlobalVendedores) {
                const filtrado = progressDataGlobalVendedores.progress.filter(item =>
                    !termo || Object.values(item).join(' ').toLowerCase().includes(termo)
                );
                renderProgressBarItens(filtrado, 'resposta-progress-vendedor');
            }

            if (progressDataGlobalClientes) {
                const filtrado = progressDataGlobalClientes.progress.filter(item =>
                    !termo || Object.values(item).join(' ').toLowerCase().includes(termo)
                );
                renderProgressBarItens(filtrado, 'resposta-progress-cliente');
            }
        }

        $('#busca-accordion').on('input', function() { filtrarAccordion(this.value); });
        $('#customSearch').on('input', function() { filtrarAccordion(this.value); });

        $('#limpar-busca-accordion').on('click', function() {
            $('#busca-accordion').val('').trigger('input').focus();
        });

        $(document).on('click', '.filtro-vendedor', function(e) {
            e.preventDefault();
            const nome = $(this).data('vendedor');
            $('#busca-accordion').val(nome);
            filtrarAccordion(nome);
        });

        $(document).on('click', '.sugestao-chip', function() {
            document.getElementById('pergunta').value = $(this).data('pergunta');
            perguntarIA();
        });

        // ── Formatação ───────────────────────────────────────────────────────────

        function formatarMoeda(valor) {
            return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
        }

        function formatarData(data) {
            if (!data) return '';
            const p = data.split('-');
            return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : data;
        }

        // ── Info boxes ───────────────────────────────────────────────────────────

        function renderInfoBox(componentes) {
            document.getElementById('resposta-info').innerHTML =
                '<div class="row">' +
                componentes.filter(c => c.tipo === 'info_box').map(c => `
                    <div class="col-md-2 col-sm-6 col-6">
                        <div class="info-box info-box-custom">
                            <span class="info-box-icon bg-${c.cor}"><i class="${c.icone}"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">${c.titulo}</span>
                                <span class="info-box-number">${c.valor}</span>
                            </div>
                        </div>
                    </div>
                `).join('') +
                '</div>';
        }

        // ── Progress bars ────────────────────────────────────────────────────────

        function renderProgressBarItens(itens, selector) {
            document.querySelector(`#${selector}`).innerHTML = itens.map(item => `
                <div class="progress-group" style="font-size:0.8rem;">
                    <div class="d-flex align-items-center pr-2">
                        <a href="" class="filtro-vendedor flex-grow-1 text-truncate" data-vendedor="${item.nome}">
                            ${item.nome}
                        </a>
                        <span class="badge badge-primary ml-2 flex-shrink-0">
                            <b>${item.qtdColetado}/${item.totalPneus}</b>
                        </span>
                        <span class="badge badge-success ml-2 flex-shrink-0">(${item.valor})</span>
                    </div>
                    <div class="progress progress-xs">
                        <div class="progress-bar bg-primary" style="width:${item.percQtd}%"></div>
                    </div>
                    <div class="progress progress-xs">
                        <div class="progress-bar bg-success" style="width:${item.percValor}%"></div>
                    </div>
                </div>
            `).join('');
        }

        // ── Gráfico ──────────────────────────────────────────────────────────────

        function renderChartBar(data) {
            if (window.graficoBarra) window.graficoBarra.destroy();

            window.graficoBarra = new Chart(
                document.getElementById('resposta-grafico').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.progress.map(i => i.nome),
                        datasets: [{
                            label: data.titulo ?? 'Coletas',
                            data: data.progress.map(i => i.qtdColetado),
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                }
            );
        }
    </script>
@stop
