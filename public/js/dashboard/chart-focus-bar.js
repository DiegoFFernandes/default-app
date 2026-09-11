/**
 * chart-focus-bar.js
 * Graficos de barra empilhada com destaque de selecao (cross-highlight),
 * estilo Power BI: cada categoria mostra a fatia "em foco" (cor forte) e o
 * "resto" (mesma cor esmaecida). Sem selecao ativa, funciona como uma barra
 * normal, com o total no topo.
 *
 * Dependencias: Chart.js, chartjs-plugin-datalabels
 * Combina bem com cross-filter.js (crossFilter().isFocused() decide o que
 * entra em `foco` x `resto`), mas nao depende dele.
 *
 * API publica:
 *   focusBarChart(canvasId, options)              — 1 metrica
 *   focusDualBarChart(canvasId, legendId, options) — 2 metricas (eixo duplo, ex: Qtd + Valor)
 *   focusLegend(containerId, swatches)             — legenda fixa (sem toggle de dataset)
 *
 * options comuns:
 *   labels, chaves {array}   - rotulos exibidos / chaves devolvidas no onClick (chaves
 *                              default = labels, quando o rotulo exibido != a chave real
 *                              — ex: nome abreviado exibido x CD_PESSOA usado no filtro —
 *                              informe `chaves` a parte)
 *   temSelecao     {boolean} - true quando ha alguma selecao ativa na tela
 *   onClick(chave)           - clique numa barra
 *   scrollAcimaDe  {number}  - nº de categorias a partir do qual o canvas alarga para
 *                              rolagem horizontal (o wrapper precisa de overflow-x:auto);
 *                              omitido = nunca alarga
 *   larguraPorItem {number}  - px por categoria ao alargar (default: 90)
 *
 * options (focusBarChart), alem das comuns:
 *   foco, resto, total {number[]} - series alinhadas com labels/chaves
 *   cor        {string}  - cor "em foco" (idealmente 'rgba(r,g,b,a)', ver corResto)
 *   corResto   {string}  - cor do "fora da selecao" (default: `cor` com alpha ~0.16 —
 *                           so funciona no formato rgba; em outros formatos informe explicito)
 *   moeda      {boolean} - formata o total do datalabel como R$ (default false)
 *
 * options (focusDualBarChart), alem das comuns:
 *   metrics {array} - 2 entradas, cada uma como o focusBarChart + `eixo` ('y'|'y1') e
 *                      `legenda` (texto mostrado no swatch da legenda fixa)
 */
(function (window) {

    var _registry = {};

    function _destroy(canvasId) {
        if (_registry[canvasId]) {
            _registry[canvasId].destroy();
            delete _registry[canvasId];
        }
    }

    function _fmt(v, moeda) {
        return moeda ?
            v.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) :
            v.toLocaleString('pt-BR');
    }

    // Clareia uma cor 'rgba(r,g,b,a)' para usar como "fora da selecao". Formatos
    // que nao casam com o padrao voltam inalterados — nesse caso informe corResto.
    function _clarear(cor) {
        return String(cor).replace(/rgba?\(([^)]+),\s*[\d.]+\)/, function (_, rgb) {
            return 'rgba(' + rgb + ', 0.16)';
        });
    }

    function _ajustarLargura(canvas, qtdCategorias, o) {
        if (!o.scrollAcimaDe) return;
        canvas.parentElement.style.width = qtdCategorias > o.scrollAcimaDe ?
            (qtdCategorias * (o.larguraPorItem || 90)) + 'px' :
            '';
    }

    function _onClick(evt, chart, onClick) {
        if (!onClick) return;
        var pts = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
        if (!pts.length) return;
        onClick(chart.$chaves[pts[0].index]);
    }

    function _datalabelTotal() {
        return {
            anchor: 'end',
            align: 'end',
            clamp: true,
            color: '#000',
            font: { weight: 'bold', size: 11 },
            display: function (c) { return c.dataset._top === true; },
            formatter: function (v, c) {
                var arr = c.dataset._totais;
                var t = arr ? arr[c.dataIndex] : 0;
                return t ? _fmt(t, c.dataset._moeda) : '';
            }
        };
    }

    function focusBarChart(canvasId, o) {
        var el = document.getElementById(canvasId);
        if (!el) return;

        _ajustarLargura(el, o.labels.length, o);
        _destroy(canvasId);

        var corResto = o.corResto || _clarear(o.cor);

        _registry[canvasId] = new Chart(el.getContext('2d'), {
            type: 'bar',
            data: {
                labels: o.labels,
                datasets: [
                    { label: 'Em foco', data: o.foco, backgroundColor: o.cor, stack: 'q' },
                    {
                        label: o.temSelecao ? 'Fora da seleção' : 'Total',
                        data: o.resto,
                        backgroundColor: o.temSelecao ? corResto : o.cor,
                        stack: 'q',
                        _top: true,
                        _moeda: !!o.moeda,
                        _totais: o.total
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { top: 22 } },
                onClick: function (e) { _onClick(e, this, o.onClick); },
                plugins: {
                    legend: { display: false },
                    datalabels: _datalabelTotal()
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, border: { display: false } },
                    y: { stacked: true, display: false, grace: '18%' }
                }
            },
            plugins: [ChartDataLabels]
        });
        _registry[canvasId].$chaves = o.chaves || o.labels;
    }

    function focusDualBarChart(canvasId, legendId, o) {
        var el = document.getElementById(canvasId);
        if (!el) return;

        _ajustarLargura(el, o.labels.length, o);
        _destroy(canvasId);

        var eixos = {};
        var datasets = [];
        o.metrics.forEach(function (m) {
            var corResto = m.corResto || _clarear(m.cor);
            eixos[m.eixo] = true;

            datasets.push({
                label: m.legenda + ' em foco', data: m.foco,
                yAxisID: m.eixo, stack: m.eixo,
                backgroundColor: m.cor
            });
            datasets.push({
                label: o.temSelecao ? (m.legenda + ' fora') : m.legenda,
                data: m.resto, yAxisID: m.eixo, stack: m.eixo,
                backgroundColor: o.temSelecao ? corResto : m.cor,
                _top: true, _moeda: !!m.moeda, _totais: m.total
            });
        });

        var scales = {
            x: { stacked: true, grid: { display: false }, border: { display: false } }
        };
        Object.keys(eixos).forEach(function (eixo, i) {
            scales[eixo] = { stacked: true, position: i === 0 ? 'left' : 'right', display: false, grace: '20%' };
        });

        _registry[canvasId] = new Chart(el.getContext('2d'), {
            type: 'bar',
            data: { labels: o.labels, datasets: datasets },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { top: 26 } },
                onClick: function (e) { _onClick(e, this, o.onClick); },
                plugins: {
                    legend: { display: false },
                    datalabels: _datalabelTotal()
                },
                scales: scales
            },
            plugins: [ChartDataLabels]
        });
        _registry[canvasId].$chaves = o.chaves || o.labels;

        if (legendId) {
            var swatches = o.metrics.map(function (m) { return { cor: m.cor, texto: m.legenda }; });
            if (o.temSelecao) swatches.push({ cor: 'rgba(0,0,0,0.16)', texto: 'Fora da seleção' });
            focusLegend(legendId, swatches);
        }
    }

    function focusLegend(containerId, swatches) {
        var el = document.getElementById(containerId);
        if (!el) return;
        el.innerHTML = swatches.map(function (s) {
            return '<span style="display:inline-flex;align-items:center;margin:0 6px;font-size:11px">' +
                '<span style="width:12px;height:12px;border-radius:2px;background:' + s.cor +
                ';margin-right:4px"></span>' + s.texto + '</span>';
        }).join('');
    }

    window.focusBarChart = focusBarChart;
    window.focusDualBarChart = focusDualBarChart;
    window.focusLegend = focusLegend;

})(window);
