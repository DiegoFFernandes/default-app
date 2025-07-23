@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Hoje</span>
                        <span id="coleta-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Ontem</span>
                        <span id="coleta-ontem" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Projeção/Mês</span>
                        <span id="projecao-mes" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Dif. ano Passado</span>
                        <span id="dif-ano-passado" class="info-box-number">0%</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">% Índice de Recusa</span>
                        <span class="info-box-number">4</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Médio Hoje</span>
                        <span class="info-box-number" id="valor-medio-hoje">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Valor Médio Ontem</span>
                        <span class="info-box-number" id="valor-medio-ontem">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filtro (mudar de acordo com a nescessidade da página) -->
        <div class="row">
            <div class="col-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h5 class="card-title">Filtros</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-empresa">Empresa:</label>
                                    <select id="filtro-empresa" class="form-control mt-1">
                                        <option value="1" selected>Cambe</option>
                                        <option value="3">Osvaldo Cruz</option>
                                        <option value="5">Ponta Grossa</option>
                                    </select>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <div class="form-group mb-0">
                                    <label for="daterange">Data:</label>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="daterange"
                                            placeholder="Selecione a Data">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2">
                                <div class="form-group mb-0">
                                    <label for="filtro-empresa">Vendedor:</label>
                                    <select id="filtro-empresa" class="form-control mt-1">
                                        <option value="0" selected>Todos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-vendedor" data-toggle="pill" href="#painel-vendedor"
                                    role="tab" aria-controls="painel-vendedor" aria-selected="true">
                                    Coleta por Vendedor Hoje
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-ultimos6" data-toggle="pill" href="#painel-ultimos6"
                                    role="tab" aria-controls="painel-ultimos6" aria-selected="false">
                                    Coletas Últimos 6 Meses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-coletaDesenho" data-toggle="pill"
                                    href="#painel-coletaDesenho" role="tab" aria-controls="painel-coletaDesenho"
                                    aria-selected="false">
                                    Coletas por Desenho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-coletaMedida" data-toggle="pill" href="#painel-coletaMedida"
                                    role="tab" aria-controls="painel-coletaMedida" aria-selected="false">
                                    Coletas por Medida
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-vendedor" role="tabpanel"
                                aria-labelledby="tab-vendedor">
                                <span class="badge badge-danger badge-empresa">Empresa:</span>
                                <span class="badge badge-danger badge-periodo badge-mes">Período:</span>
                                <div class="table-responsive">
                                    <table id="coletasVendedorHoje"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-ultimos6" role="tabpanel"
                                aria-labelledby="tab-ultimos6">
                                <div>
                                    <canvas id="barChart" style="height: 120px;"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-coletaDesenho" role="tabpanel"
                                aria-labelledby="tab-coletaDesenho">
                                <div>
                                    <canvas id="coletaPorDesenho"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-coletaMedida" role="tabpanel"
                                aria-labelledby="tab-coletaMedida">
                                <div>
                                    <canvas id="coletaPorMedida"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .info-box-text {
            font-size: 14px;
        }

        .info-box-number {
            font-weight: bold;
            font-size: 18px;
        }

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.6);
        }

        .btn-primary {
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.6);
        }

        .nav-tabs .nav-link {
            font-size: 15px;
            padding: 7px 15px;
        }
    </style>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            const datasSelecionadas = initDateRangePicker();

            let tabela;
            let inicioData = moment().format('DD.MM.YYYY');
            let fimData = moment().format('DD.MM.YYYY');

            $('.badge-periodo').text(`Período: ${inicioData} - ${fimData}`);
            $('.badge-empresa').text(`Empresa: Cambe`);

            //formata os valores para o real
            function formatarMoeda(valor) {
                return valor.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            }

            // define os dias uteis do mes
            function contarDiasUteis(inicio, fim) {
                let current = moment(inicio);
                let end = moment(fim);
                let count = 0;

                while (current <= end) {
                    const dia = current.day();
                    if (dia !== 0 && dia !== 6) {
                        count++;
                    }
                    current.add(1, 'day');
                }

                return count;
            }

            function calcularTotais(dados) {
                let total = 0;
                let soma = 0;
                let contador = 0;

                dados.forEach(function(row) {
                    let qtdPneus = parseFloat(row.QTDPNEUS) || 0;
                    let valorMedio = parseFloat(row.VALOR_MEDIO) || 0;

                    total += qtdPneus;
                    soma += valorMedio * qtdPneus;
                    contador += qtdPneus;
                });

                const valorMedio = contador > 0 ? soma / contador : 0;

                return {
                    total,
                    valorMedio
                };
            }

            initTable(1, inicioData, fimData);

            function initTable(cdempresa, dtinicio, dtfim) {
                if ($.fn.DataTable.isDataTable('#coletasVendedorHoje')) {
                    $('#coletasVendedorHoje').DataTable().destroy();
                }

                tabela = $('#coletasVendedorHoje').DataTable({
                    processing: true,
                    serverSide: false,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json"
                    },
                    ajax: {
                        url: "{{ route('get-coleta-geral') }}",
                        type: 'GET',
                        data: function(d) {
                            d.cd_empresa = cdempresa; // Ajuste conforme necessário
                            d.dt_inicial = dtinicio;
                            d.dt_final = dtfim;
                        }
                    },
                    columns: [{
                            data: 'NM_VENDEDOR',
                            name: 'NM_VENDEDOR',
                            title: 'Vendedor'
                        },
                        {
                            data: 'QTDPNEUS',
                            name: 'QTDPNEUS',
                            title: 'Quantidade',
                            width: '20%'
                        },
                        {
                            data: 'VALOR_MEDIO',
                            name: 'VALOR_MEDIO',
                            title: 'Valor Médio',
                            width: '30%'
                        }
                    ],
                    // exibe os info box de hoje de acordo com a tabela gerada
                    drawCallback: function(settings) {
                        var api = this.api();
                        var data = api.rows({
                            search: 'applied'
                        }).data();

                        let totalColetaHoje = 0;
                        let somaValorHoje = 0;
                        let contadorColetasHoje = 0;

                        data.each(function(rowData) {
                            let qtdPneus = parseFloat(rowData.QTDPNEUS) || 0;
                            let valorMedio = parseFloat(rowData.VALOR_MEDIO) || 0;

                            totalColetaHoje += qtdPneus;
                            somaValorHoje += valorMedio * qtdPneus;
                            contadorColetasHoje += qtdPneus;
                        });

                        const valorMedioHoje = contadorColetasHoje > 0 ? somaValorHoje /
                            contadorColetasHoje : 0;

                        $('#coleta-hoje').text(totalColetaHoje);
                        $('#valor-medio-hoje').text(formatarMoeda(valorMedioHoje));

                        let fimFiltro = moment(dtfim, 'DD.MM.YYYY');
                        let inicioMes = fimFiltro.clone().startOf('month');
                    }
                });

                function calcularProjecaoMensal(cdempresa, dtfim) {
                    let fimFiltro = moment(dtfim, 'DD.MM.YYYY');
                    let inicioMes = fimFiltro.clone().startOf('month');
                    let diasUteisPassados = contarDiasUteis(inicioMes, fimFiltro);
                    let totalDiasUteisNoMes = contarDiasUteis(inicioMes, fimFiltro.clone().endOf('month'));

                    $.ajax({
                        url: "{{ route('get-coleta-geral') }}",
                        type: 'GET',
                        data: {
                            cd_empresa: cdempresa,
                            dt_inicial: inicioMes.format('DD.MM.YYYY'),
                            dt_final: fimFiltro.format('DD.MM.YYYY')
                        },
                        success: function(response) {
                            let dadosMes = response.data || [];

                            let totalAcumulado = 0;

                            dadosMes.forEach(function(row) {
                                totalAcumulado += parseFloat(row.QTDPNEUS) || 0;
                            });

                            let projecaoMes = diasUteisPassados > 0 ?
                                Math.round((totalAcumulado / diasUteisPassados) * totalDiasUteisNoMes) :
                                0;

                            $('#projecao-mes').text(projecaoMes);
                        },
                        error: function(err) {
                            console.error("Erro ao calcular projeção mensal:", err);
                            $('#projecao-mes').text("Erro");
                        }
                    });
                }

                function carregarDadosOntem(cdempresa, dataFinal) {
                    const dataFinalFormatada = dataFinal.replace(/\//g, '.'); // troca barras por pontos
                    let dataFimMoment = moment(dataFinalFormatada, 'DD.MM.YYYY', true);
                    if (!dataFimMoment.isValid()) {
                        console.error("Data inválida:", dataFinal);
                        return;
                    }
                    let dataOntem = dataFimMoment.clone().subtract(1, 'day').format('DD.MM.YYYY');

                    $.ajax({
                        url: "{{ route('get-coleta-geral') }}",
                        type: 'GET',
                        data: {
                            cd_empresa: cdempresa,
                            dt_inicial: dataOntem,
                            dt_final: dataOntem
                        },
                        success: function(response) {
                            console.log(response);
                            const dataOntem = response.data || [];

                            let totalColeta = 0;
                            let somaValor = 0;
                            let contador = 0;

                            dataOntem.forEach(function(row) {
                                let qtdPneus = parseFloat(row.QTDPNEUS) || 0;
                                let valorMedio = parseFloat(row.VALOR_MEDIO) || 0;

                                totalColeta += qtdPneus;
                                somaValor += valorMedio * qtdPneus;
                                contador += qtdPneus;
                            });

                            const valorMedio = contador > 0 ? somaValor / contador : 0;

                            $('#coleta-ontem').text(totalColeta);
                            $('#valor-medio-ontem').text(formatarMoeda(valorMedio));
                        },
                        error: function(err) {
                            console.error("Erro ao buscar dados de ontem:", err);
                        }
                    });
                }

                function carregarDiferencaAnoPassado(cdempresa, dtinicio, dtfim) {
                    // datas no formato DD.MM.YYYY
                    let dataInicioAtual = moment(dtinicio, 'DD.MM.YYYY');
                    let dataFimAtual = moment(dtfim, 'DD.MM.YYYY');

                    // ano passado mesmo intervalo
                    let dataInicioAnoPassado = dataInicioAtual.clone().subtract(1, 'year').format('DD.MM.YYYY');
                    let dataFimAnoPassado = dataFimAtual.clone().subtract(1, 'year').format('DD.MM.YYYY');

                    $.when(
                        $.ajax({
                            url: "{{ route('get-coleta-geral') }}",
                            data: {
                                cd_empresa: cdempresa,
                                dt_inicial: dtinicio,
                                dt_final: dtfim
                            },
                            type: 'GET'
                        }),
                        $.ajax({
                            url: "{{ route('get-coleta-geral') }}",
                            data: {
                                cd_empresa: cdempresa,
                                dt_inicial: dataInicioAnoPassado,
                                dt_final: dataFimAnoPassado
                            },
                            type: 'GET'
                        })
                    ).done(function(responseAtual, responseAnoPassado) {
                        let dadosAtual = responseAtual[0].data || [];
                        let dadosAnoPassado = responseAnoPassado[0].data || [];

                        let totalAtual = dadosAtual.reduce((acc, item) => acc + (parseFloat(item
                            .QTDPNEUS) || 0), 0);
                        let totalAnoPassado = dadosAnoPassado.reduce((acc, item) => acc + (parseFloat(item
                            .QTDPNEUS) || 0), 0);

                        let difPercent = 0;
                        if (totalAnoPassado > 0) {
                            difPercent = ((totalAtual - totalAnoPassado) / totalAnoPassado) * 100;
                        }

                        $('#dif-ano-passado').text(difPercent.toFixed(2) + '%');
                    }).fail(function() {
                        console.error('Erro ao carregar dados para diferença do ano passado');
                        $('#dif-ano-passado').text('N/A');
                    });
                }

                function carregaGraficoUltimos6Meses(cdempresa) {
                    const hoje = new Date();
                    let labelsMeses = [];
                    let dadosMensaisAtual = [0, 0, 0, 0, 0, 0];
                    let dadosMensaisAnoAnterior = [0, 0, 0, 0, 0, 0];

                    let datasAlvo = [];

                    for (let i = 0; i < 6; i++) {
                        const dataAlvo = new Date(hoje);
                        dataAlvo.setMonth(dataAlvo.getMonth() - i);
                        datasAlvo.push(new Date(dataAlvo));
                        const nomeMesAbreviado = dataAlvo.toLocaleString('pt-BR', {
                            month: 'short'
                        });
                        const ano = dataAlvo.getFullYear();
                        labelsMeses.push(nomeMesAbreviado);
                    }

                    labelsMeses.reverse();
                    datasAlvo.reverse();

                    let ctx = document.getElementById('barChart').getContext('2d');

                    if (window.grafico6Meses) {
                        window.grafico6Meses.destroy();
                    }

                    window.grafico6Meses = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labelsMeses,
                            datasets: [{
                                    label: 'Coletas Ano Atual',
                                    data: dadosMensaisAtual,
                                    backgroundColor: 'rgba(23, 162, 184, 1)'
                                },
                                {
                                    label: 'Coletas Ano Passado',
                                    data: dadosMensaisAnoAnterior,
                                    backgroundColor: 'rgba(108, 117, 125, 1)'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            animation: {
                                duration: 300
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });

                    // buscar os dados de cada mês para este ano e o ano passado
                    datasAlvo.forEach((dataAlvo, i) => {
                        const mes = dataAlvo.getMonth() + 1;
                        const anoAtual = dataAlvo.getFullYear();
                        const anoAnterior = anoAtual - 1;
                        const primeiroDiaAtual = `01.${String(mes).padStart(2, '0')}.${anoAtual}`;
                        const ultimoDiaAtual =
                            `${new Date(anoAtual, mes, 0).getDate()}.${String(mes).padStart(2, '0')}.${anoAtual}`;
                        const primeiroDiaAnterior = `01.${String(mes).padStart(2, '0')}.${anoAnterior}`;
                        const ultimoDiaAnterior =
                            `${new Date(anoAnterior, mes, 0).getDate()}.${String(mes).padStart(2, '0')}.${anoAnterior}`;

                        // ano atual
                        $.ajax({
                            url: "{{ route('get-coleta-geral') }}",
                            type: 'GET',
                            data: {
                                cd_empresa: cdempresa,
                                dt_inicial: primeiroDiaAtual,
                                dt_final: ultimoDiaAtual
                            },
                            success: function(response) {
                                let total = 0;
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(row => {
                                        total += parseInt(row.QTDPNEUS) || 0;
                                    });
                                }
                                window.grafico6Meses.data.datasets[0].data[i] = total;
                                window.grafico6Meses.update();
                            },
                            error: function() {
                                window.grafico6Meses.data.datasets[0].data[i] = 0;
                                window.grafico6Meses.update();
                            }
                        });

                        // ano passado
                        $.ajax({
                            url: "{{ route('get-coleta-geral') }}",
                            type: 'GET',
                            data: {
                                cd_empresa: cdempresa,
                                dt_inicial: primeiroDiaAnterior,
                                dt_final: ultimoDiaAnterior
                            },
                            success: function(response) {
                                let total = 0;
                                if (response.data && response.data.length > 0) {
                                    response.data.forEach(row => {
                                        total += parseInt(row.QTDPNEUS) || 0;
                                    });
                                }
                                window.grafico6Meses.data.datasets[1].data[i] = total;
                                window.grafico6Meses.update();
                            },
                            error: function() {
                                window.grafico6Meses.data.datasets[1].data[i] = 0;
                                window.grafico6Meses.update();
                            }
                        });
                    });
                }
                //carregaGraficoUltimos6Meses(1);


                //Busca os dados ao clicar no botão "Buscar novos"
                $('#submit-seach').on('click', function() {

                    const empresa = $('#filtro-empresa').val(); // puxa o código da empresa
                    const empresaNome = $('#filtro-empresa option:selected').text(); //puxa o nome da empresa

                    if (!empresa) {
                        msgToastr('Selecione uma empresa para continuar.',
                            'warning');
                        return;
                    }

                    //ajusta as datas para gerar o dados de "ontem"
                    const inicioRaw = datasSelecionadas.getInicio();
                    const fimRaw = datasSelecionadas.getFim();

                    if (!inicioRaw || !fimRaw) {
                        msgToastr('Selecione o período para continuar.',
                            'warning');
                        return;
                    }

                    // converte para DD.MM.YYYY
                    const inicioData = moment(inicioRaw, 'MM/DD/YYYY').format('DD.MM.YYYY');
                    const fimData = moment(fimRaw, 'MM/DD/YYYY').format('DD.MM.YYYY');

                    // automatiza os badges
                    $('.badge-empresa').text(`Empresa: ${empresaNome}`);
                    $('.badge-periodo').text(
                        `Período: ${moment(inicioRaw + ' 00:00', "MM/DD/YYYY HH:mm").format("DD/MM/YYYY HH:mm")} - ${moment(fimRaw + ' 23:59', "MM/DD/YYYY HH:mm").format("DD/MM/YYYY HH:mm")}`
                    );

                    initTable(empresa, inicioData, fimData);
                    //carregaGraficoUltimos6Meses(empresa)

                });
                calcularProjecaoMensal(cdempresa, dtfim);
                carregarDadosOntem(cdempresa, dtfim);
                carregarDiferencaAnoPassado(cdempresa, dtinicio, dtfim);
            }
        });
    </script>
@stop
