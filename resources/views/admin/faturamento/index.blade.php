@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box shadow">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Notas Emitidas</span>
                                <span class="info-box-number notas-emitidas"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box shadow">
                            <span class="info-box-icon bg-danger"><i class="fas fa-window-close"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Canceladas</span>
                                <span class="info-box-number canceladas"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box shadow">
                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Faturista</span>
                                <span class="info-box-number nr-faturista"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="display: none">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="daterange">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-block float-right"
                                    id="submit-seach">Buscar novos</button>
                            </div>
                            <div class="col-md-4">
                                <input id="filtro-empresa" type="text" class="form-control"
                                    placeholder="Filtrar por Empresa">
                            </div>
                            <div class="col-md-4">
                                <input id="filtro-usuario" type="text" class="form-control"
                                    placeholder="Filtrar por Faturista">
                            </div>
                            <div class="col-md-4">
                                <input id="filtro-dia" type="text" class="form-control" placeholder="Filtrar por Dia">
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Faturistas Diario</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="height: auto">
                                    <canvas id="usuario-diario-chart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-1">
                                <h3 class="card-title">Faturistas</h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-tool btn-sm btn-download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="#" class="btn btn-tool btn-sm">
                                        <i class="fas fa-bars"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-1">
                                <div id="tabela-faturista"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-1">
                                <h3 class="card-title">Motivos Cancelamentos</h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-tool btn-sm btn-download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="#" class="btn btn-tool btn-sm">
                                        <i class="fas fa-bars"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-1">
                                <div id="tabela-cancelamentos"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">

                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Notas Emitidas (Mês)</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px">
                            <canvas id="notas-chart"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square" style="color: #28a745"></i> Mês
                            </span>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Usuários</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px">
                            <canvas id="usuario-chart"></canvas>
                        </div>
                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square" style="color: #158698"></i> Usuarios
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Notas Canceladas Mês</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px">
                            <canvas id="notas-canceladas-chart"></canvas>
                        </div>
                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square" style="color: #DC3545"></i> Canceladas
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
@stop

@section('js')
    <script>
        var inicioData = 0;
        var fimData = 0;
        var table = null;
        var tableCancelados = null;

        $('#daterange').daterangepicker({
            autoUpdateInput: false,
        }).attr('readonly', true);
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                'DD/MM/YYYY'));
            inicioData = picker.startDate.format('MM/DD/YYYY');
            fimData = picker.endDate.format('MM/DD/YYYY');
        });
        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val("");
            inicioData = 0;
            fimData = 0;
        });

        $('#submit-seach').click(function() {

            if (inicioData == 0) {
                msgToastr('Período deve ser preenchida!', 'info');
                return false;
            }
            CarregaDados(inicioData, fimData);
        });

        CarregaDados(inicioData, fimData);

        let chat;
        let dadosFiltrados = [];
        let chartUserDay;
        let chartUser;
        let chartNotas;
        let chartUserCanc;

        function CarregaDados(inicioData, fimData) {
            table = new Tabulator("#tabela-faturista", {
                ajaxURL: "{{ route('get-analise-faturamento.index') }}",
                ajaxParams: {
                    inicioData: inicioData,
                    fimData: fimData
                },
                ajaxRequesting: function(url, params) {

                    if (!inicioData == 0) {
                        $("#loading").removeClass('invisible');
                    }
                },
                layout: "fitDataStretch",
                dataLoader: true,
                dataLoaderLoading: "<div class='text-center p-4'><i class='fas fa-spinner fa-spin fa-2x text-danger'></i></div>",
                groupStartOpen: false,
                // Agrupamento por chave personalizada
                groupBy: function(row) {
                    return `${row.USUARIO}||${row.DS_MES}`;
                },

                groupHeader: function(value, count, data) {
                    // return `<strong>${value}</strong> <span class="ml-2">(${count} item${count > 1 ? 's' : ''})</span>`;               
                    const [usuario, mes] = value.split("||");

                    return `
                    <div style="display:inline-block; width:95%;">
                        <span style="display:inline-block; width: 3%; color: #333;"></span>
                        <span style="display:inline-block; width: 33%; color: #333;">${usuario}</span> 
                        <span style="display:inline-block; width: 25%; color: #333;">${mes}</span>                      
                        <span style="display:inline-block; width: 10%; color: #333; text-align: right;">${count} Nota${count > 1 ? 's' : ''}</span>                            
                    </div>
                    `;
                },
                ajaxResponse: function(url, params, response) {
                    // Store the data in a global variable
                    dadosFiltrados = response;
                    $("#loading").addClass('invisible');

                    atualizaDados(dadosFiltrados);

                    return response;
                },
                columns: [{
                        title: "Emp",
                        field: "CD_EMPRESA",
                        hozAlign: "center",
                        width: 65
                    },
                    {
                        title: "Faturista",
                        field: "USUARIO",
                    },
                    {
                        title: "Cliente",
                        field: "NM_PESSOA",
                    },
                    {
                        title: "Lancamento",
                        field: "NR_LANCAMENTO",
                    },
                    {
                        title: "Serie",
                        field: "CD_SERIE",
                    },
                    {
                        title: "Dt Emissao",
                        field: "DT_REGISTRO",
                        hozAlign: "center",
                        formatter: function(cell) {
                            const rawDate = cell.getValue();
                            return formatDate(rawDate);
                        }
                    },
                    {
                        title: "Qtd Itens",
                        field: "QTD_ITENS",
                    }
                ]
            });
            tableCancelados = new Tabulator("#tabela-cancelamentos", {
                layout: "fitColumns",
                groupBy: ["DS_MOTIVO"],
                groupStartOpen: false,
                dataLoader: true,
                dataLoaderLoading: "<div class='text-center p-4'><i class='fas fa-spinner fa-spin fa-2x text-danger'></i></div>",
                columns: [{
                        title: "Motivo",
                        field: "DS_MOTIVO",
                    },
                    {
                        title: "Faturista",
                        field: "USUARIOCANC",
                    }
                ]
            });
        }

        // Filtro por nome
        document.getElementById("filtro-empresa").addEventListener("keyup", function() {
            table.setFilter("CD_EMPRESA", "like", this.value.toLowerCase());
            tableFiltred();
        });

        // Filtro por CNPJ
        document.getElementById("filtro-usuario").addEventListener("keyup", function() {
            table.setFilter("USUARIO", "like", this.value.toLowerCase());
            tableFiltred();
        });
        // Filtro por Região
        document.getElementById("filtro-dia").addEventListener("keyup", function() {
            table.setFilter("DT_EMISSAO", "like", this.value.toLowerCase());
            tableFiltred();
        });

        document.querySelectorAll(".btn-download").forEach(btn => {
            btn.addEventListener("click", function() {



                exportarParaExcel(dadosFiltrados, "faturista.xlsx", "Faturista");
            });
        });

        function formatDate(value) {
            if (!value) return '';
            const date = new Date(value);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
        }

        function tableFiltred() {
            table.on("dataFiltered", function(filters, rows) {

                let dadosFiltrados = rows.map(row => row.getData());
                // Atualiza os dados com os novos filtros
                atualizaDados(dadosFiltrados);
            });
        }

        function atualizaDados(dados) {
            let nomeMes = [];
            let qtdNotaMes = {};
            let qtdNotaUser = {};
            let qtdNotaCancelada = {};

            let qtdNota = 0;
            let nomeUser = [];


            dados.forEach(function(item) {
                const mes = item.DS_MES;
                const usuario = item.USUARIO;
                const usuarioCanc = item.USUARIOCANC;

                const stNota = item.ST_NOTA;

                if (!nomeMes.includes(mes)) {
                    nomeMes.push(mes);
                }
                if (!qtdNotaMes[mes]) {
                    qtdNotaMes[mes] = 0;
                }
                if (!qtdNotaUser[usuario]) {
                    qtdNotaUser[usuario] = 0;
                }
                if (!usuarioCanc == "") {

                    if (!qtdNotaCancelada[usuarioCanc]) {
                        qtdNotaCancelada[usuarioCanc] = 0;
                    }
                    qtdNotaCancelada[usuarioCanc]++;
                }

                qtdNotaMes[mes]++;
                qtdNotaUser[usuario]++;
            });


            let qtdPorMes = Object.values(qtdNotaMes);
            let qtdNotaEmitidas = Object.values(qtdNotaMes).reduce((a, b) => a + b, 0);
            let qtdNotaCanceladas = Object.values(qtdNotaCancelada).reduce((a, b) => a + b, 0);

            // console.log(qtdNotaCanceladas);
            nomeUser = Object.keys(qtdNotaUser);
            nomeUserCancel = Object.keys(qtdNotaCancelada);

            let qtdUser = nomeUser.length;
            let qtdPoruser = Object.values(qtdNotaUser);
            let qtdPoruserCancel = Object.values(qtdNotaCancelada);


            // Enviar as informações para os cards
            $('.nr-faturista').html(qtdUser);
            $('.notas-emitidas').html(qtdNotaEmitidas);
            $('.canceladas').html(qtdNotaCanceladas);

            //Carregar os gráficos 
            carregaChart(nomeMes, qtdPorMes);
            carregaChartUser(nomeUser, qtdPoruser);
            carregaChartUserCancel(nomeUserCancel, qtdPoruserCancel);
            carregaChartDiario(dados);

            // Filtra os cancelados
            let cancelados = dados.filter(item => item.DS_MOTIVO);

            // Atualiza a outra tabela
            if (tableCancelados) {
                tableCancelados.setData(cancelados);
            }
        }

        function carregaChartDiario(dados) {
            const datasUnicas = [...new Set(dados.map(d => d.DT_EMISSAO))].sort();
            const usuariosUnicos = [...new Set(dados.map(d => d.USUARIO))];
            const datasets = usuariosUnicos.map(usuario => {
                // Cria um objeto com soma de QTD_ITENS por data
                const somaPorData = {};
                dados
                    .filter(d => d.USUARIO === usuario)
                    .forEach(d => {
                        const data = d.DT_EMISSAO;
                        const qtd = parseInt(d.QTD_ITENS);
                        somaPorData[data] = (somaPorData[data] || 0) + qtd;
                    });

                // Monta o array de dados para cada data
                const dadosPorDia = datasUnicas.map(data => somaPorData[data] || 0);

                // Cores automáticas simples
                const cor = '#' + Math.floor(Math.random() * 16777215).toString(16);

                return {
                    label: usuario,
                    data: dadosPorDia,
                    borderColor: cor,
                    backgroundColor: cor + '44',
                    tension: 0.3
                };
            });

            const ctx = document.getElementById('usuario-diario-chart').getContext('2d');

            if (chartUserDay) {
                chartUserDay.destroy(); // Destrói o gráfico anterior
            }
            chartUserDay = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: datasUnicas,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function carregaChart(nomeMes, qtdPorMes) {
            const ctx = document.getElementById('notas-chart').getContext('2d');
            if (chartNotas) {
                chartNotas.destroy(); // Destrói o gráfico anterior
            }
            chartNotas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nomeMes,
                    datasets: [{
                        label: 'Qtd Emitidas',
                        data: qtdPorMes,
                        backgroundColor: 'rgba(40, 167, 69,0.2)',
                        borderColor: 'rgba(28, 118, 49,1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    // responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            color: '#28a745',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                return value;
                            }
                        }
                    },
                    scales: {
                        y: { // <--- ALTERADO de yAxes para y
                            beginAtZero: true
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function carregaChartUser(nomeUser, qtdPoruser) {
            const ctx = document.getElementById('usuario-chart').getContext('2d');

            if (chartUser) {
                chartUser.destroy(); // Destrói o gráfico anterior
            }
            chartUser = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nomeUser,
                    datasets: [{
                        label: 'Notas Emitidas',
                        data: qtdPoruser,
                        backgroundColor: 'rgba(23, 162, 184, 0.6)',
                        borderColor: 'rgba(21, 134, 152, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#158698',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                return value;
                            }
                        }
                    },
                    scales: {
                        y: { // <--- ALTERADO de yAxes para y
                            beginAtZero: true
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function carregaChartUserCancel(nomeUser, qtdPoruser) {
            const ctx = document.getElementById('notas-canceladas-chart').getContext('2d');

            if (chartUserCanc) {
                chartUserCanc.destroy(); // Destrói o gráfico anterior
            }
            chartUserCanc = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nomeUser,
                    datasets: [{
                        label: 'Notas Canceladas',
                        data: qtdPoruser,
                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                        borderColor: 'rgba(176, 39, 53, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#DC3545',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                return value;
                            }
                        }
                    },
                    scales: {
                        y: { // <--- ALTERADO de yAxes para y
                            beginAtZero: true
                        },
                        x: {
                            beginAtZero: true,
                            max: Math.max(...qtdPoruser) + 10,
                            ticks: {
                                stepSize: 1 // <--- Espaçamento fixo no eixo X
                            }
                        },

                    }
                },
                plugins: [ChartDataLabels]
            });
        }        
    </script>
@stop
