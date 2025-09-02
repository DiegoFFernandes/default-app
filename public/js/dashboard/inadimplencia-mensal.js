Chart.register(ChartDataLabels); // inicia o datalabels

const ctx = document.getElementById('graficoInadimplencia').getContext('2d');
let graficoInadimplencia;

table.on('xhr', function () {
    const json = table.ajax.json();

    if (json && json.data) {
        const labels = json.data.map(item => item.MES_ANO).reverse();
        const valores = json.data.map(item => parseFloat(String(item.VL_SALDO).replace(',', '.')))
            .reverse();
        const percentuais = json.data.map(item => parseFloat(String(item.PC_INADIMPLENCIA).replace(',',
            '.'))).reverse();


        if (graficoInadimplencia) {
            graficoInadimplencia.destroy();
        }

        graficoInadimplencia = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    type: 'bar',
                    label: ' Vencidos (R$)',
                    data: valores,
                    borderWidth: 2,
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108,117,125,0.7)',
                    yAxisID: 'y',
                }, {
                    type: 'line',
                    label: '% Inadimplência',
                    data: percentuais,
                    borderColor: 'rgba(0, 0, 0, 1)',
                    backgroundColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1',
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: function (context) {
                            return context.dataset.type === 'line'; //label apenas para a linha
                        },
                        formatter: function (value, context) {
                            return value.toFixed(2).replace('.', ',');
                        },
                        anchor: 'end',
                        align: 'top',
                          offset: window.innerWidth <= 768 ? -6 : -8,    
                        color: 'black', 
                        font: {
                            weight: 'bold',
                            size: window.innerWidth <= 768 ? 11 : 12
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const dataset = context.dataset;
                                const value = context.raw;

                                if (dataset.type === 'line') {
                                    return value + '%';
                                } else {
                                    return 'R$ ' + value.toLocaleString(
                                        'pt-BR');
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: window.innerWidth <= 768 ? 45 : 0,
                            minRotation: window.innerWidth <= 768 ? 45 : 0,
                            font: {
                                size: 10
                            },
                            callback: function (value) {
                                const label = this.getLabelForValue(value);
                                const mes = label.split('/')[0]; // pega só o mes
                                return mes.substring(0, 3); //exibe o mes abreviado
                            }
                        }
                    },
                    y: {
                        type: 'logarithmic',
                        position: 'left',
                        ticks: {
                            display: false,
                            maxTicksLimit: 10, //evita ficar com muitas linhas no fundo 
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            display: false,
                        }
                    }
                },

            }
        });
    }
});


$('#btn-toggle-chart').click(function () {
    var tabela = $('#container-tabela');
    var grafico = $('#container-grafico');

    if (tabela.is(':visible')) {
        tabela.hide();
        grafico.show();
        $(this).text('Exibir Tabela');

        if (graficoInadimplencia) {
            graficoInadimplencia.resize(); // garante que o canvas ajuste ao tamanho
        }
    } else {
        grafico.hide();
        tabela.show();
        $(this).text('Exibir Gráfico');
    }
});
