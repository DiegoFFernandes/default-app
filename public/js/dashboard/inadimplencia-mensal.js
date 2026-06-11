
Chart.register(ChartDataLabels); // inicia o datalabels

let graficoInadimplencia;
const chartDataCache = {};

setTimeout(() => {
    vincularTabelaAoGrafico(
        "tabela-inadimplencia-meses",
        "graficoInadimplencia",
    );
}, 1000);

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
            "DataTable ainda não foi inicializado em: " + seletorTabela,
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
        $(this).html('<i class="fas fa-table mr-1"></i>Tabela');
    } else {
        grafico.hide();
        tabela.show();
        $(this).html('<i class="fas fa-chart-bar mr-1"></i>Gráfico');
    }
});

function resetarVisualizacaoAba(abaAtiva) {
    let $container = $(abaAtiva);

    if (!$container.length) return; // se não encontrar o container, sai da função

    let $tabela = $container.find(".container-tabela"); // procura a tabela
    let $grafico = $container.find(".container-grafico"); // procura o gráfico
    let $botao = $container.find(".btn-toggle-chart"); // procura o botão

    $tabela.show();
    $grafico.hide();

    // reseta o texto do botão
    if ($botao.length) {
        $botao.html('<i class="fas fa-chart-bar mr-1"></i>Gráfico');
    }
}

// quando muda de aba
$('a[data-toggle="tab"], a[data-toggle="pill"]').on(
    "shown.bs.tab",
    function (e) {
        let abaAtiva = $(e.target).attr("href");
        resetarVisualizacaoAba(abaAtiva);
    },
);

//quando clica na aba ativa
$('a[data-toggle="tab"], a[data-toggle="pill"]').on("click", function (e) {
    if ($(this).hasClass("active")) {
        let abaAtiva = $(this).attr("href");
        resetarVisualizacaoAba(abaAtiva);
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
    const [ano, mes, dia] = value.split("-");
    return `${dia}/${mes}/${ano}`;
}

function criarGraficoInadimplencia(data, canvasId, tipo) {
    chartDataCache[canvasId] = data;
    tipo = tipo || "bar";

    const labels  = data.map(d => d.MES_ANO).reverse();
    const valores = data.map(d => parseFloat(String(d.VL_SALDO).replace(",", "."))).reverse();
    const pcts    = data.map(d => parseFloat(String(d.PC_INADIMPLENCIA).replace(",", "."))).reverse();

    if (graficoInadimplencia) graficoInadimplencia.destroy();

    const isMobile  = window.innerWidth <= 768;
    const pctColors = pcts.map(p => p >= 10 ? "#e53e3e" : p >= 5 ? "#d69e2e" : "#38a169");
    const barBg     = pcts.map(p => p >= 10 ? "rgba(229,62,62,0.8)"  : p >= 5 ? "rgba(214,158,46,0.8)"  : "rgba(56,161,105,0.8)");
    const barBrd    = pcts.map(p => p >= 10 ? "rgba(197,48,48,1)"    : p >= 5 ? "rgba(183,121,31,1)"    : "rgba(39,103,73,1)");

    const scaleX = {
        ticks: {
            autoSkip: false,
            maxRotation: isMobile ? 45 : 0,
            minRotation: isMobile ? 45 : 0,
            font: { size: 10 },
            callback: function (v) {
                const lbl = this.getLabelForValue(v);
                return lbl.split("/")[0].substring(0, 3);
            },
        },
        grid: { display: false },
    };

    const fmtMoeda = v => v >= 1e6 ? "R$ " + (v / 1e6).toFixed(1) + "M"
                        : v >= 1e3  ? "R$ " + (v / 1e3).toFixed(0)  + "K"
                        : "R$ " + v;

    const ctx = document.getElementById(canvasId).getContext("2d");

    if (tipo === "bar") {
        graficoInadimplencia = new Chart(ctx, {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        type: "bar",
                        label: "Vencidos (R$)",
                        data: valores,
                        backgroundColor: barBg,
                        borderColor: barBrd,
                        borderWidth: 1.5,
                        borderRadius: 4,
                        yAxisID: "y",
                    },
                    {
                        type: "line",
                        label: "% Inadimplência",
                        data: pcts,
                        borderColor: "rgba(229,62,62,1)",
                        borderWidth: 2,
                        pointBackgroundColor: pctColors,
                        pointBorderColor: "#fff",
                        pointBorderWidth: 1.5,
                        pointRadius: 4,
                        tension: 0.3,
                        fill: false,
                        yAxisID: "y1",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: "bottom",
                        labels: { font: { size: 11 }, padding: 10, boxWidth: 12 },
                    },
                    datalabels: {
                        display: ctx => ctx.dataset.type === "line",
                        formatter: v => v.toFixed(2).replace(".", ",") + "%",
                        anchor: "end",
                        align: "top",
                        offset: isMobile ? -6 : -8,
                        color: ctx => pctColors[ctx.dataIndex],
                        font: { weight: "bold", size: isMobile ? 10 : 11 },
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.type === "line"
                                ? ` % Inad.: ${ctx.raw.toFixed(2).replace(".", ",")}%`
                                : ` Vencido: R$ ${ctx.raw.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}`,
                        },
                    },
                },
                scales: {
                    x: scaleX,
                    y: {
                        type: "logarithmic",
                        position: "left",
                        ticks: {
                            maxTicksLimit: 5,
                            font: { size: 9 },
                            color: "#718096",
                            callback: function (v) {
                                return Number.isInteger(Math.log10(v)) ? fmtMoeda(v) : "";
                            },
                        },
                        grid: { color: "rgba(0,0,0,0.04)" },
                    },
                    y1: {
                        type: "linear",
                        position: "right",
                        grid: { drawOnChartArea: false },
                        ticks: {
                            font: { size: 9 },
                            color: "#e53e3e",
                            callback: v => v.toFixed(1) + "%",
                        },
                    },
                },
            },
        });
    } else {
        // Modo Tendência — area chart somente do %
        graficoInadimplencia = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: "% Inadimplência",
                        data: pcts,
                        borderColor: "rgba(229,62,62,1)",
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const gradient = chart.ctx.createLinearGradient(0, 0, 0, chart.height);
                            gradient.addColorStop(0, "rgba(229,62,62,0.35)");
                            gradient.addColorStop(1, "rgba(229,62,62,0.02)");
                            return gradient;
                        },
                        borderWidth: 2.5,
                        pointBackgroundColor: pctColors,
                        pointBorderColor: "#fff",
                        pointBorderWidth: 1.5,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true,
                        formatter: v => v.toFixed(2).replace(".", ",") + "%",
                        anchor: "end",
                        align: "top",
                        offset: isMobile ? -6 : -8,
                        color: ctx => pctColors[ctx.dataIndex],
                        font: { weight: "bold", size: isMobile ? 10 : 11 },
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` % Inad.: ${ctx.raw.toFixed(2).replace(".", ",")}%`,
                        },
                    },
                },
                scales: {
                    x: scaleX,
                    y: {
                        position: "right",
                        ticks: {
                            font: { size: 9 },
                            color: "#e53e3e",
                            callback: v => v.toFixed(1) + "%",
                        },
                        grid: { color: "rgba(0,0,0,0.04)" },
                    },
                },
            },
        });
    }
}

// Alterna o tipo de gráfico
$(document).on("click", ".btn-chart-type", function () {
    const $btn     = $(this);
    const canvasId = $btn.closest(".container-grafico").find("canvas").attr("id");
    const tipo     = $btn.data("type");

    $btn.closest(".chart-type-btns").find(".btn-chart-type")
        .removeClass("btn-secondary active").addClass("btn-outline-secondary");
    $btn.removeClass("btn-outline-secondary").addClass("btn-secondary active");

    if (chartDataCache[canvasId]) {
        criarGraficoInadimplencia(chartDataCache[canvasId], canvasId, tipo);
    }
});
