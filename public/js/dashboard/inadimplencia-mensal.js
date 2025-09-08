Chart.register(ChartDataLabels); // inicia o datalabels

let graficoInadimplencia;

vincularTabelaAoGrafico("tabela-inadimplencia-meses", "graficoInadimplencia");

function vincularTabelaAoGrafico(idTabela, idGrafico) {
    const seletorTabela = "#" + idTabela;    

    if ($.fn.DataTable.isDataTable(seletorTabela)) {
        $(seletorTabela)
            .DataTable()
            .on("xhr", function () {
                const json = $(seletorTabela).DataTable().ajax.json();
                if (json && json.data) {
                    criarGraficoInadimplencia(json.data, idGrafico);
                }
            });
    } else {
        console.warn(
            "DataTable ainda não foi inicializado em: " + seletorTabela
        );
    }
}

$(".btn-toggle-chart").click(function () {
    var tabela = $(".container-tabela");
    var grafico = $(".container-grafico");

    if (graficoInadimplencia) {
        graficoInadimplencia.resize(); // garante que o canvas ajuste ao tamanho
    }

    if (tabela.is(":visible")) {
        tabela.hide();
        grafico.show();
        $(this).text("Exibir Tabela");
    } else {
        grafico.hide();
        tabela.show();
        $(this).text("Exibir Gráfico");
    }
});

function formatarValorBR(valor) {
    const numero = Number(valor);

    if (isNaN(numero)) {
        return "0,00";
    }

    return numero.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
function formatDate(value) {
    if (!value) return "";
    const date = new Date(value);
    return date.toLocaleDateString("pt-BR");
}

function criarGraficoInadimplencia(data, canvasId) {
    const ctx = document.getElementById(canvasId).getContext("2d");
    const labels = data.map((item) => item.MES_ANO).reverse();
    const valores = data
        .map((item) => parseFloat(String(item.VL_SALDO).replace(",", ".")))
        .reverse();
    const percentuais = data
        .map((item) =>
            parseFloat(String(item.PC_INADIMPLENCIA).replace(",", "."))
        )
        .reverse();

    if (graficoInadimplencia) {
        graficoInadimplencia.destroy();
    }

    graficoInadimplencia = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    type: "bar",
                    label: " Vencidos (R$)",
                    data: valores,
                    borderWidth: 2,
                    borderColor: "#6c757d",
                    backgroundColor: "rgba(108,117,125,0.7)",
                    yAxisID: "y",
                },
                {
                    type: "line",
                    label: "% Inadimplência",
                    data: percentuais,
                    borderColor: "rgba(0, 0, 0, 1)",
                    backgroundColor: "rgba(0, 0, 0, 1)",
                    borderWidth: 2,
                    yAxisID: "y1",
                    fill: false,
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: function (context) {
                        return context.dataset.type === "line"; //label apenas para a linha
                    },
                    formatter: function (value, context) {
                        return value.toFixed(2).replace(".", ",");
                    },
                    anchor: "end",
                    align: "top",
                    offset: window.innerWidth <= 768 ? -6 : -8,
                    color: "black",
                    font: {
                        weight: "bold",
                        size: window.innerWidth <= 768 ? 11 : 12,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const dataset = context.dataset;
                            const value = context.raw;

                            if (dataset.type === "line") {
                                return value + "%";
                            } else {
                                return "R$ " + value.toLocaleString("pt-BR");
                            }
                        },
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: window.innerWidth <= 768 ? 45 : 0,
                        minRotation: window.innerWidth <= 768 ? 45 : 0,
                        font: {
                            size: 10,
                        },
                        callback: function (value) {
                            const label = this.getLabelForValue(value);
                            const mes = label.split("/")[0]; // pega só o mes
                            return mes.substring(0, 3); //exibe o mes abreviado
                        },
                    },
                },
                y: {
                    type: "logarithmic",
                    position: "left",
                    ticks: {
                        display: false,
                        maxTicksLimit: 10, //evita ficar com muitas linhas no fundo
                    },
                },
                y1: {
                    type: "linear",
                    position: "right",
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        display: false,
                    },
                },
            },
        },
    });
}
