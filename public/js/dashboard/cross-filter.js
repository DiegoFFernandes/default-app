/**
 * crossFilter
 *
 * Estado de selecao multi-dimensional para cross-highlight em graficos +
 * filtro/enfase em DataTables, no estilo Power BI: multi-selecao DENTRO de
 * uma dimensao (ex: 2 gerentes ao mesmo tempo), mas clicar numa dimensao
 * diferente da que esta ativa zera a selecao anterior — evita misturar
 * chips de dimensoes diferentes (que combinados por OR deixam os numeros
 * sem sentido).
 *
 * Nao depende de Chart.js — so de jQuery + DataTables (para attachTable/
 * rowCallback/aplicarEnfase). O aggregate/render dos graficos fica por
 * conta de cada tela (ou de um helper como chart-focus-bar.js).
 *
 * @param {object} options
 *   dims        {string[]}          - nomes das dimensoes (ex: ['mes','gerente','linhas'])
 *   campo       {object<string,fn>} - dim -> function(row): chave da linha nessa dimensao
 *   linhaClass  {string}            - classe CSS aplicada na <tr> em foco (default: 'linha-selecionada')
 *
 * @returns {object}
 *   .toggle(dim, chave)              - alterna o chip; trocar de dimensao zera as outras
 *   .clear()                         - zera toda a selecao
 *   .count()                         - total de chips selecionados (soma de todas as dims)
 *   .hasSelection(opts)              - ha alguma selecao ativa? (opts.ignorarDims: string[])
 *   .isFocused(row, opts)            - a linha casa com a selecao ativa? (opts.ignorarDims idem)
 *   .attachTable(tableId, opts)      - registra o filtro (ext.search) desta selecao numa
 *                                      DataTable pelo id (string); opts.ignorarDims exclui
 *                                      dimensoes do filtro (ex: selecao de linha so da enfase,
 *                                      nao filtra a tabela). Chamar 1x por tabela, mesmo que
 *                                      ela seja destruida/recriada depois (ex: botao "buscar").
 *   .rowCallback(rowEl, data)        - pronta pra usar em rowCallback do DataTable, marca
 *                                      linhaClass conforme a dimensao 'linhas' (se existir)
 *   .aplicarEnfase(table)            - reaplica linhaClass sem precisar de um redraw
 *                                      (usar apos toggle() quando so os graficos mudaram)
 *
 * Exemplo:
 *   var cf = crossFilter({
 *       dims: ['mes', 'gerente', 'cliente', 'supervisor', 'linhas'],
 *       campo: {
 *           mes:        function (r) { return r.MES_ANO; },
 *           gerente:    function (r) { return r.NM_GERENTE; },
 *           cliente:    function (r) { return String(r.CD_PESSOA); },
 *           supervisor: function (r) { return r.NM_SUPERVISOR; },
 *           linhas:     function (r) { return r.NR_COLETA + '|' + r.NR_LOTEEXP; }
 *       }
 *   });
 *
 *   cf.attachTable('minhaTabela', { ignorarDims: ['linhas'] });
 *
 *   table = $('#minhaTabela').DataTable({ rowCallback: cf.rowCallback, ... });
 *
 *   // no clique de uma barra do grafico:
 *   cf.toggle('gerente', 'JOAO');
 *   renderGraficos();     // total = agrupar(baseRows), foco = agrupar(baseRows.filter(cf.isFocused))
 *   table.draw();         // reaplica o ext.search -> tabela so com o foco
 */
function crossFilter(options) {
    var dims = (options && options.dims) || [];
    var campo = (options && options.campo) || {};
    var linhaClass = (options && options.linhaClass) || 'linha-selecionada';

    var sel = {};
    dims.forEach(function (d) { sel[d] = new Set(); });
    var dimAtiva = null;

    function count() {
        return dims.reduce(function (acc, d) { return acc + sel[d].size; }, 0);
    }

    function hasSelection(opts) {
        var ignorar = (opts && opts.ignorarDims) || [];
        return dims.some(function (d) {
            return ignorar.indexOf(d) === -1 && sel[d].size > 0;
        });
    }

    function isFocused(row, opts) {
        var ignorar = (opts && opts.ignorarDims) || [];
        if (!hasSelection(opts)) return true;
        return dims.some(function (d) {
            if (ignorar.indexOf(d) !== -1) return false;
            var fn = campo[d];
            if (!fn) return false;
            return sel[d].has(String(fn(row)));
        });
    }

    function toggle(dim, chave) {
        if (!sel[dim]) return;
        if (dimAtiva && dimAtiva !== dim) {
            dims.forEach(function (d) { if (d !== dim) sel[d].clear(); });
        }
        var k = String(chave);
        if (sel[dim].has(k)) sel[dim].delete(k);
        else sel[dim].add(k);
        dimAtiva = hasSelection() ? dim : null;
    }

    function clear() {
        dims.forEach(function (d) { sel[d].clear(); });
        dimAtiva = null;
    }

    function attachTable(tableId, opts) {
        var ignorar = (opts && opts.ignorarDims) || [];
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
            if (settings.nTable.id !== tableId) return true;
            var r = rowData || (settings.aoData[dataIndex] && settings.aoData[dataIndex]._aData);
            return r ? isFocused(r, { ignorarDims: ignorar }) : true;
        });
    }

    function linhaKey(data) {
        var fn = campo.linhas;
        return (sel.linhas && fn) ? String(fn(data)) : null;
    }

    function rowCallback(rowEl, data) {
        var k = linhaKey(data);
        $(rowEl).toggleClass(linhaClass, !!k && sel.linhas.has(k));
    }

    function aplicarEnfase(table) {
        if (!table || !sel.linhas) return;
        table.rows().every(function () {
            var d = this.data();
            if (!d) return;
            var k = linhaKey(d);
            $(this.node()).toggleClass(linhaClass, !!k && sel.linhas.has(k));
        });
    }

    return {
        toggle: toggle,
        clear: clear,
        count: count,
        hasSelection: hasSelection,
        isFocused: isFocused,
        attachTable: attachTable,
        rowCallback: rowCallback,
        aplicarEnfase: aplicarEnfase
    };
}
