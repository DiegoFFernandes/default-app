
//grafico
var ctx = document.getElementById('graficoInadimplencia').getContext('2d');
var graficoInadimplencia;

table.on('xhr', function () {
    var json = table.ajax.json();

    if (json && json.data) {
        var labels = json.data.map(item => item.MES_ANO)
            .reverse(); // ".reverse()" garante ordem da esquerda para a direita
        var valores = json.data.map(item => parseFloat(item.VL_SALDO)).reverse();

        if (graficoInadimplencia) {
            graficoInadimplencia.destroy(); // evita duplicar
        }

        graficoInadimplencia = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Valores (R$)',
                    data: valores,
                    borderWidth: 2,
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108,117,125,0.7)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: function () {
                                return window.innerWidth <= 768 ? 45 : 0;
                            },
                            minRotation: function () {
                                return window.innerWidth <= 768 ? 45 : 0;
                            },
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        type: 'logarithmic',
                        min: 1000,
                        ticks: {
                            maxTicksLimit: 10, //limite de linhas no eixo y 
                            font: {
                                size: 12
                            },
                            callback: function (value, index, ticks) {
                                const log = Math.log10(value);
                                if (log % 1 === 0 || index === 0 || value === 1000) {

                                    const isMobile = window.innerWidth < 768;

                                    //abrevia o valor do eixo y para telas pequenas
                                    if (isMobile) {
                                        return new Intl.NumberFormat('pt-BR', {
                                            notation: 'compact',
                                            compactDisplay: 'short',
                                            maximumFractionDigits: 1
                                        }).format(value);
                                    }

                                    return 'R$ ' + value.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            }
        });
    }
});