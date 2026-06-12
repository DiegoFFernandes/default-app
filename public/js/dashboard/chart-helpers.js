/**
 * chart-helpers.js
 * Funções reutilizáveis de gráficos Chart.js para o painel.
 *
 * Dependências (devem ser carregadas antes das chamadas):
 *   - Chart.js
 *   - chartjs-plugin-datalabels
 *   - formatarValorBR() — definida em inadimplencia-mensal.js
 *
 * API pública:
 *   pizzaStatic(canvasId, items)  — doughnut com % por fatia
 *   barStatic(canvasId, items)    — barras horizontais com % por item
 *
 * items: Array<{ nome: string, valor: number }>
 */
(function (window) {

    var _registry = {};

    var PALETTE = [
        "#4299e1", "#48bb78", "#ed8936", "#9f7aea", "#e53e3e",
        "#38b2ac", "#f6ad55", "#667eea", "#fc8181", "#68d391",
        "#63b3ed", "#b794f4"
    ];

    function _destroy(canvasId) {
        if (_registry[canvasId]) {
            _registry[canvasId].destroy();
            delete _registry[canvasId];
        }
    }

    /**
     * Doughnut estático — % de participação por item.
     * Exibe rótulo nas fatias >= 4%, tooltip com % e valor em R$.
     */
    function pizzaStatic(canvasId, items) {
        var $canvas = $("#" + canvasId);
        if (!$canvas.length) return;

        _destroy(canvasId);

        var total = items.reduce(function (a, b) { return a + b.valor; }, 0);
        if (total === 0) return;

        _registry[canvasId] = new Chart($canvas[0].getContext("2d"), {
            type: "doughnut",
            data: {
                labels: items.map(function (i) { return i.nome; }),
                datasets: [{
                    data:            items.map(function (i) { return i.valor; }),
                    backgroundColor: items.map(function (_, k) { return PALETTE[k % PALETTE.length] + "cc"; }),
                    borderColor:     items.map(function (_, k) { return PALETTE[k % PALETTE.length]; }),
                    borderWidth:     1.5,
                    hoverOffset:     5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "52%",
                plugins: {
                    legend: {
                        display: true,
                        position: "bottom",
                        labels: { font: { size: 9 }, padding: 5, boxWidth: 8, color: "#4a5568" }
                    },
                    datalabels: {
                        display: function (ctx) {
                            return (ctx.dataset.data[ctx.dataIndex] / total) * 100 >= 4;
                        },
                        formatter: function (v) {
                            return ((v / total) * 100).toFixed(1) + "%";
                        },
                        color: "#fff",
                        font: { weight: "bold", size: 9 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                var pct = ((ctx.raw / total) * 100).toFixed(1);
                                return " " + pct + "% — R$ " + formatarValorBR(ctx.raw);
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Barras horizontais estáticas — % de participação por item.
     * Altura dinâmica: max(175, itens * 22 + 30) px.
     * Ajusta o elemento .card-body pai automaticamente.
     */
    function barStatic(canvasId, items) {
        var $canvas = $("#" + canvasId);
        if (!$canvas.length) return;

        _destroy(canvasId);

        var total = items.reduce(function (a, b) { return a + b.valor; }, 0);
        if (total === 0) return;

        var dynH = Math.max(175, items.length * 22 + 30);
        $canvas.closest(".card-body").css("height", dynH + "px");

        var pcts = items.map(function (i) {
            return parseFloat(((i.valor / total) * 100).toFixed(2));
        });

        _registry[canvasId] = new Chart($canvas[0].getContext("2d"), {
            type: "bar",
            data: {
                labels: items.map(function (i) { return i.nome; }),
                datasets: [{
                    data:            pcts,
                    backgroundColor: items.map(function (_, k) { return PALETTE[k % PALETTE.length] + "bb"; }),
                    borderColor:     items.map(function (_, k) { return PALETTE[k % PALETTE.length]; }),
                    borderWidth:     1,
                    borderRadius:    3
                }]
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        display: true,
                        anchor: "end",
                        align: "right",
                        formatter: function (v) { return v.toFixed(1) + "%"; },
                        color: "#4a5568",
                        font: { weight: "600", size: 9 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                var item = items[ctx.dataIndex];
                                return " " + ctx.raw.toFixed(1) + "% — R$ " + formatarValorBR(item.valor);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: false,
                        max: Math.max.apply(null, pcts) * 1.25
                    },
                    y: {
                        ticks: {
                            font: { size: 9 },
                            color: "#4a5568",
                            callback: function (v) {
                                var lbl = this.getLabelForValue(v);
                                return lbl.length > 14 ? lbl.substring(0, 13) + "…" : lbl;
                            }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    window.pizzaStatic = pizzaStatic;
    window.barStatic   = barStatic;

})(window);
