@extends('layouts.master')

@section('title', 'Produzidos não Faturados')

@section('content')
    <section class="content">

        @include('admin.producao.produzidos-sem-faturar.cards.cards-info')

        @include('admin.producao.produzidos-sem-faturar.cards.cards-charts')

        @include('admin.producao.produzidos-sem-faturar.filtros.filtros')

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <span class="badge badge-danger periodo"></span>
                        <table id="produzidosTable" class="table table-bordered table-font-small compact">
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align: right;"></th>
                                    @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                        <th colspan="5"></th>
                                    @else
                                        <th colspan="3"></th>
                                    @endhasrole
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@stop

@section('css')
    <style>
        .col-actions {
            width: 1% !important;
        }

        @media (max-width: 768px) {
            .table-left {
                margin-left: 0 !important;
            }

            .col-actions {
                width: 2% !important;
            }
        }
    </style>
@stop

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge badge-danger">{{ NM_PESSOA }}</span>
            <table class="table stripe row-border no-padding table-left" id="pedido-{{ NR_COLETA }}-{{ EXPEDICIONADO }}-{{ NR_EMBARQUE }}" style="width:80%">
                <thead style="background-color: #434A51;">
                    <tr>
                        <th>Expedicinado</th>
                        <th>Nr Ordem</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script>
        $(document).ready(function() {
            var inicioData = 0;
            var fimData = moment().subtract(0, 'days').format('DD.MM.YYYY');
            var dados;
            var table;

            window.routes = {
                getPneusProduzidosSemFaturar: "{{ route('get-pneus-produzidos-sem-faturar') }}",
                getPneusProduzidosSemFaturarDetails: "{{ route('get-pneus-produzidos-sem-faturar-details') }}",
                languageDataTable: "{{ asset('vendor/datatables/pt-BR.json') }}"
            };

            $('#grupo_item').select2({
                placeholder: 'Selecione o grupo',
                theme: 'bootstrap4',
            });

            $('#cd_regiaocomercial').select2({
                theme: 'bootstrap4',
            });

            var template = Handlebars.compile($("#details-template").html());

            var datasSelecionadas = initDateRangePicker('#daterange', '01.10.2024', fimData);
            // var datasSelecionadas = initDateRangePicker('#daterange', '01.01.2026', '28.02.2026');

            $('.periodo').text('Período: ' + datasSelecionadas.getInicio() + ' - ' + datasSelecionadas.getFim());

            dados = {
                cd_empresa: $('#cd_empresa').val(),
                nm_cliente: $('#nm_cliente').val(),
                nm_vendedor: $('#nm_vendedor').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: datasSelecionadas.getInicio(),
                dt_final: datasSelecionadas.getFim(),
                regiao: $('#cd_regiaocomercial').val(),
                st_embarque: $('#st_embarque').val(),
                supervisor: $('#supervisor').val(),
            };

            initTablePneus(dados);

            $('#search').click(function() {
                $('#produzidosTable').DataTable().destroy();

                $('.periodo').text('Período: ' + datasSelecionadas.getInicio() + ' - ' + datasSelecionadas
                    .getFim());

                dados = {
                    cd_empresa: $('#cd_empresa').val(),
                    nm_cliente: $('#nm_cliente').val(),
                    nm_vendedor: $('#nm_vendedor').val(),
                    pedido_palm: $('#pedido_palm').val(),
                    pedido: $('#pedido').val(),
                    grupo_item: $('#grupo_item').val(),
                    cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                    dt_inicial: datasSelecionadas.getInicio(),
                    dt_final: datasSelecionadas.getFim(),
                    regiao: $('#cd_regiaocomercial').val(),
                    st_embarque: $('#st_embarque').val(),
                    supervisor: $('#supervisor').val(),

                };

                initTablePneus(dados);
            });

            $(document).on('click', '.btn-detalhes', function() {

                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var tableId = 'pedido-' + row.data().NR_COLETA + '-' + row.data().EXPEDICIONADO + '-' + row
                    .data().NR_EMBARQUE;

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    // Open this row
                    row.child(template(row.data())).show();
                    initTable(tableId, row.data());
                    tr.addClass('shown');
                    $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    // tr.next().find('td').addClass('no-padding');
                }

            });

            $(document).on('click', '.btn-observacao-embarque', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                var observacao = row.data().DS_OBSFATURAMENTO;

                if (observacao == null || observacao.trim() == '') {
                    observacao = 'Nenhuma observação de faturamento para este embarque.';
                }

                Swal.fire({
                    title: 'Observação de Faturamento',
                    text: observacao,
                    confirmButtonText: 'Fechar'
                });
            });

            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.addEventListener('click', function() {

                    const texto = this.textContent.trim();

                    document.querySelector('.card-title-chart').textContent = 'Pneus ' + texto;

                });
            });

            function initTablePneus(dados) {
                table = $('#produzidosTable').DataTable({
                    language: {
                        url: window.routes.languageDataTable,
                    },
                    fixedHeader: true,
                    scrollY: '400px',
                    pageLength: -1,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todos"],
                    ],
                    layout: {
                        topStart: {
                            buttons: [{
                                    extend: "excelHtml5",
                                    title: 'Pneus Produzidos Sem Faturar',
                                },
                                {
                                    extend: "print",
                                    title: 'Pneus Produzidos Sem Faturar',
                                    customize: function(win) {
                                        $(win.document.body)
                                            .find("h1")
                                            .css("font-size", "12pt")
                                            .css("color", "#333");
                                    },
                                },
                            ],
                        },
                    },
                    ajax: {
                        url: window.routes.getPneusProduzidosSemFaturar,
                        data: {
                            data: dados
                        },
                        beforeSend: function() {
                            $(".loading-card").removeClass('invisible');
                        },
                        dataSrc: function(json) {
                            $(".loading-card").addClass('invisible');
                            carregaDados(json.datatables.data);

                            return json.datatables.data;
                        }
                    },
                    "columns": [{
                            "data": "actions",
                            title: "#",
                            orderable: false,
                            searchable: false,
                            className: "text-center",

                        },
                        {
                            "data": "CD_EMPRESA",
                            title: "Emp",
                            width: "1%",
                            className: "text-center",
                        },
                        {
                            "data": "NR_EMBARQUE",
                            title: "Embarque"
                        },
                        {
                            "data": "NR_COLETA",
                            title: "Pedido"
                        },
                        {
                            "data": "NM_PESSOA",
                            title: "Cliente"
                        },
                        {
                            "data": "PNEUS",
                            title: "Pneus",
                            className: "text-center",
                        },
                        {
                            "data": "NM_VENDEDOR",
                            title: "Vendedor"
                        },
                        {
                            "data": "EXPEDICIONADO",
                            title: "Expedição",
                            className: "text-center",
                        },
                        {
                            "data": "DTFIM",
                            render: function(data) {
                                return moment(data).format('DD/MM/YYYY HH:mm');
                            },
                            title: "Data",
                            className: "text-center",
                            "visible": true
                        },
                        {
                            "data": "NM_GERENTE",
                            title: "Gerente",
                            "visible": false
                        },
                        {
                            "data": "MES_ANO",
                            title: "Mês/Ano",
                            "visible": false
                        },
                        @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                            {
                                "data": "VALOR",
                                title: "Valor"
                            },
                        @endhasrole
                    ],
                    "columnDefs": [{
                        "targets": 0,
                        "className": "text-center",
                    }],

                    footerCallback: function(row, data, start, end, display) {
                        let QtdPneus = 0;
                        let valorTotal = 0;
                        let expedicionadoSim = 0;
                        let expedicionadoNao = 0;
                        let embarqueSim = 0;
                        let embarqueNao = 0;

                        data.forEach(function(item) {
                            QtdPneus += Number(item.PNEUS);
                            valorTotal += parseFloat(item.VALOR.replace(/\./g, '').replace(',',
                                '.'));
                            if (item.EXPEDICIONADO == 'SIM') {
                                expedicionadoSim += Number(item.PNEUS);
                            } else {
                                expedicionadoNao += Number(item.PNEUS);
                            }
                            if (item.ST_EMBARQUE != 'SEM EMBARQUE') {
                                embarqueSim += Number(item.PNEUS);
                            } else {
                                embarqueNao += Number(item.PNEUS);
                            }
                        });

                        const totalPneus = this.api()
                            .column(5, {
                                search: 'applied'
                            })
                            .data()
                            .sum()


                        $(this.api().column(5).footer()).html(
                            "Total: " + totalPneus.toLocaleString('pt-BR')
                        );


                        $('.pneusTotal').html(QtdPneus);
                        $('#valorTotal').html('R$ ' + valorTotal.toFixed(2).replace('.', ',').replace(
                            /\B(?=(\d{3})+(?!\d))/g, '.'));
                        $('#expedicionado').html('Sim: ' + expedicionadoSim + ' | Não: ' +
                            expedicionadoNao);
                        $('#embarque').html('Sim: ' + embarqueSim + ' | Não: ' + embarqueNao);

                    },

                });
            }

            function initTable(tableId, data) {

                if ($.fn.DataTable.isDataTable('#' + tableId)) {
                    $('#' + tableId).DataTable().destroy();
                }

                var tableItemOrdem = $('#' + tableId)
                    .DataTable({
                        "language": {
                            url: window.routes.languageDataTable,
                        },
                        sDom: 't',
                        paging: false,
                        searching: true,
                        ajax: {
                            "url": window.routes.getPneusProduzidosSemFaturarDetails,
                            "method": "GET",
                            "data": {
                                'pedido': data.NR_COLETA,
                                'nr_embarque': data.NR_EMBARQUE,
                                'expedicionado': data.EXPEDICIONADO
                            }
                        },
                        columns: [{
                                data: "EXPEDICIONADO",
                                title: "Expedicionado"
                            },
                            {
                                data: "NRORDEMPRODUCAO",
                                title: "Nr Ordem"
                            },
                            {
                                data: "DS_ITEM",
                                title: "Descrição"
                            },
                            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                {
                                    "data": "VALOR",
                                    title: "Valor"
                                },
                            @endhasrole {
                                data: "DTFIM",
                                title: "Data",
                                render: function(data) {
                                    return moment(data).format('DD/MM/YYYY HH:mm');
                                }
                            }
                        ],
                        order: [
                            [1, 'asc']
                        ]
                    });
            }

            function carregaDados(data, chartId) {
                const acumuladorMeses = {};
                const acumuladorGerentes = {};
                const qtdMes = {};
                const qtdGerente = {};
                const vlrGerente = {};

                const acumuladorClientes = {};
                const acumuladorNomeCliente = {};
                const qtdClientes = {};
                const vlrClientes = {};

                const acumuladorSupervisores = {};
                const qtdSupervisores = {};
                const vlrSupervisores = {};

                data.forEach(({
                    MES_ANO,
                    NM_GERENTE,
                    NM_SUPERVISOR,
                    CD_PESSOA,
                    NM_PESSOA,
                    PNEUS,
                    VALOR
                }) => {
                    const qtde = Number(PNEUS);
                    const valor = parseFloat(VALOR.replace(/\./g, '').replace(',', '.'));

                    acumuladorMeses[MES_ANO] ??= {};
                    acumuladorMeses[MES_ANO][NM_GERENTE] ??= 0;
                    acumuladorGerentes[NM_GERENTE] ??= 0;

                    qtdMes[MES_ANO] ??= 0;
                    qtdGerente[NM_GERENTE] ??= 0;
                    vlrGerente[NM_GERENTE] ??= 0;

                    acumuladorMeses[MES_ANO][NM_GERENTE] += qtde;
                    qtdMes[MES_ANO] += qtde;
                    qtdGerente[NM_GERENTE] += qtde;
                    vlrGerente[NM_GERENTE] += valor;

                    acumuladorClientes[CD_PESSOA] ??= 0;
                    acumuladorNomeCliente[CD_PESSOA] ??= NM_PESSOA;
                    qtdClientes[CD_PESSOA] ??= 0;
                    vlrClientes[CD_PESSOA] ??= 0;
                    qtdClientes[CD_PESSOA] += qtde;
                    vlrClientes[CD_PESSOA] += valor;

                    acumuladorSupervisores[NM_SUPERVISOR] ??= 0;
                    qtdSupervisores[NM_SUPERVISOR] ??=
                        0; // Inicializa a quantidade para o supervisor se ainda não existir
                    vlrSupervisores[NM_SUPERVISOR] ??=
                        0; // Inicializa o valor para o supervisor se ainda não existir
                    qtdSupervisores[NM_SUPERVISOR] += qtde;
                    vlrSupervisores[NM_SUPERVISOR] += valor;

                });

                const meses = Object.keys(acumuladorMeses);
                const NomeGerentes = Object.keys(acumuladorGerentes);
                const NomeSupervisores = Object.keys(acumuladorSupervisores);

                const qtdPneusMes = Object.values(qtdMes);
                const qtdPneusGerente = Object.values(qtdGerente);
                const vlrPneusGerente = Object.values(vlrGerente);

                // Transformo o objeto de clientes em um array para ordenar pelo valor total 
                const clientesArray = Object.keys(vlrClientes).map(cliente => ({
                    idcliente: cliente,
                    nomeCliente: acumuladorNomeCliente[cliente].split(' ')[
                        0], // Pego apenas o primeiro nome para exibir no gráfico
                    valor: vlrClientes[cliente],
                    quantidade: qtdClientes[cliente]
                }));

                clientesArray.sort((a, b) => b.valor - a.valor);

                const labelsClientes = clientesArray.map(c => c.nomeCliente);
                const qtdPneusCliente = clientesArray.map(c => c.quantidade);
                const vlrPneusCliente = clientesArray.map(c => c.valor);

                // Transformo o objeto de supervisores em um array para ordenar pelo valor total
                const supervisoresArray = Object.keys(vlrSupervisores).map(supervisor => ({
                    nomeSupervisor: supervisor,
                    valor: vlrSupervisores[supervisor],
                    quantidade: qtdSupervisores[supervisor]
                }));

                supervisoresArray.sort((a, b) => b.valor - a.valor);

                const labelsSupervisores = supervisoresArray.map(s => s.nomeSupervisor);
                const qtdPneusSupervisor = supervisoresArray.map(s => s.quantidade);
                const vlrPneusSupervisor = supervisoresArray.map(s => s.valor);


                //Verifico se teve aumento no ultimo mês em relação ao penúltimo mês
                const [penultimoMes, ultimoMes, MesAtual] = qtdPneusMes.slice(-3);

                const percentual = ((ultimoMes - penultimoMes) / penultimoMes) * 100;

                const calcPercentual = $('.calc-percentual').html(`
                    <span class="${percentual >= 0 ? 'text-success' : 'text-danger'}">
                        <i class="fas fa-arrow-${percentual >= 0 ? 'up' : 'down'}"></i> ${percentual.toFixed(2)}%
                    </span>
                    <span class="text-muted">${percentual >= 0 ? 'Aumento do ultimo Mês' : 'Queda do ultimo Mês'}</span>
                `);


                const datasetsMeses = [{
                    data: qtdPneusMes,
                    backgroundColor: 'rgba(206,212,218)',
                    borderColor: 'rgba(206,212,218, 1)',
                    borderWidth: 1
                }];

                // Renderiza o gráfico de meses
                renderChartJs(
                    meses,
                    datasetsMeses,
                    'chartPneusMesAno',
                    'bar',
                    'Meses');


                // Datasets de gerentes    
                const datasetsGerente = loadDatasets(qtdPneusGerente, vlrPneusGerente);

                // Datasets de clientes
                const datasetsClientes = loadDatasets(qtdPneusCliente, vlrPneusCliente);

                // Datasets de supervisores
                const datasetsSupervisores = loadDatasets(qtdPneusSupervisor, vlrPneusSupervisor);

                // Renderiza o gráfico de gerentes
                renderChartJsDualAxis(
                    NomeGerentes,
                    datasetsGerente,
                    'chartPneusGerente',
                    'Meses',
                    'legend-container-gerente'
                );

                // Renderiza o gráfico de Clientes
                renderChartJsDualAxis(
                    labelsClientes,
                    datasetsClientes,
                    'chartPneusCliente',
                    'Clientes',
                    'legend-container-cliente'
                );

                // Renderiza o gráfico de Supervisores
                renderChartJsDualAxis(
                    labelsSupervisores,
                    datasetsSupervisores,
                    'chartPneusSupervisor',
                    'Supervisores',
                    'legend-container-supervisor'
                );
            }

            const charts = {};

            function renderChartJsDualAxis(labels, datasets, chartId, labelChart, legendContainerId) {

                const larguraPorItem = 80; // px por barra
                const total = labels.length;

                const larguraTotal = total * larguraPorItem;

                const ctx = document.getElementById(chartId).getContext('2d');
                if (chartId === 'chartPneusCliente') {
                    ctx.canvas.parentElement.style.width = larguraTotal + 'px';
                }


                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                charts[chartId] = new Chart(ctx, {
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        maintainAspectRatio: false,
                        resposive: true,
                        onClick: function(evt) {

                            const points = this.getElementsAtEventForMode(
                                evt,
                                'nearest', {
                                    intersect: true
                                },
                                true
                            );

                            if (!points.length) return;

                            const point = points[0];

                            const label = this.data.labels[point.index];
                            const value = this.data.datasets[point.datasetIndex].data[point.index];

                            onChartClick(label, value, chartId);
                        },

                        plugins: {
                            legend: {
                                display: false,
                                position: 'top'
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center', // 'top', 'bottom', 'center'
                                color: '#000',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {

                                    // Se for o dataset de VALOR
                                    if (context.dataset.label.includes('Valor')) {
                                        return value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }

                                    // Se for quantidade
                                    return value;
                                    s
                                }
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y: {
                                type: 'linear',
                                position: 'left',
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });

                gerarLegenda(charts[chartId], legendContainerId);

            }

            function renderChartJs(labels, datasets, chartId, typeChart, labelChart) {

                const ctx = document.getElementById(chartId).getContext('2d');

                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                charts[chartId] = new Chart(ctx, {
                    type: typeChart,
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        maintainAspectRatio: false,

                        onClick: function(evt) {

                            const points = this.getElementsAtEventForMode(
                                evt,
                                'nearest', {
                                    intersect: true
                                },
                                true
                            );

                            if (!points.length) return;

                            const point = points[0];

                            const label = this.data.labels[point.index];
                            const value = this.data.datasets[point.datasetIndex].data[point.index];

                            onChartClick(label, value, chartId);
                        },

                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center', // 'top', 'bottom', 'center'
                                color: '#000',
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
                            x: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            function onChartClick(label, value, chartId) {
                table.search(label).draw();
            }

            function loadDatasets(qtd = [], vlr = []) {

                return [{
                        label: "Quantidade",
                        type: 'bar',
                        data: qtd,
                        backgroundColor: 'rgba(60, 145, 230, 0.5)',
                        borderColor: 'rgba(60, 145, 230, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Valor (R$)',
                        data: vlr,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ];
            }

            function gerarLegenda(chart, containerId) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                chart.data.datasets.forEach((dataset, index) => {

                    const item = document.createElement('span');
                    item.style.marginRight = '10px';
                    item.style.cursor = 'pointer';
                    item.style.display = 'inline-flex';
                    item.style.alignItems = 'center';
                    item.style.padding = '4px 8px';
                    item.style.borderRadius = '6px';
                    item.style.background = '#f4f6f9';

                    const colorBox = document.createElement('span');
                    colorBox.style.width = '12px';
                    colorBox.style.height = '12px';
                    colorBox.style.background = dataset.borderColor;
                    colorBox.style.marginRight = '5px';
                    colorBox.style.borderRadius = '2px';

                    const label = document.createElement('span');
                    label.textContent = dataset.label;

                    item.appendChild(colorBox);
                    item.appendChild(label);


                    item.addEventListener('click', () => {
                        const meta = chart.getDatasetMeta(index);

                        // alterna visibilidade
                        meta.hidden = meta.hidden === null ? !chart.data.datasets[index].hidden :
                            null;

                        chart.update();

                        // efeito visual (apagado quando desativado)
                        item.style.opacity = meta.hidden ? '0.3' : '1';
                    });

                    container.appendChild(item);
                });
            }

        });
    </script>
@stop
