/**
 * collapsibleRowGroup
 *
 * Cria configuração de RowGroup colapsável para DataTables.
 * Grupos iniciam fechados e expandem/recolhem ao clicar.
 * A coluna agrupada deve ter visible: false na config do DataTable.
 *
 * @param {object} options
 *   dataSrc    {string}   - Campo de agrupamento (ex: 'USUARIO')
 *   colIndex   {number}   - Índice da coluna — garante sort fixo ao clicar em headers
 *   colSpan    {number}   - colspan da célula do cabeçalho do grupo
 *   countLabel {string}   - Rótulo do badge de contagem (padrão: 'item')
 *   extraClass {string}   - Classe CSS extra para o tr do grupo (opcional)
 *   badgeClass {string}   - Classe do badge (padrão: 'badge-primary')
 *   summaryFn  {function} - function(rows) → string HTML extra no cabeçalho (opcional)
 *
 * @returns {object}
 *   .rowGroup      → config para `rowGroup` do DataTable
 *   .rowCallback   → config para `rowCallback` do DataTable
 *   .orderFixed    → config para `orderFixed` do DataTable (evita grupos fragmentados)
 *   .reset()       → limpa estados (chamar antes de recarregar dados)
 *   .expandAll(getTableFn)    → expande todos os grupos
 *   .collapseAll(getTableFn)  → recolhe todos os grupos
 *   .bindClick(tbodySelector, getTableFn)
 *       → registra toggle de click com preservação de scroll
 *   .bindExpandCollapseAll(expandSel, collapseSel, getTableFn)
 *       → registra botões expandir/recolher tudo
 *
 * Exemplo de uso:
 *   var grp = collapsibleRowGroup({ dataSrc: 'USUARIO', colIndex: 1, colSpan: 8, countLabel: 'nota' });
 *   var dt  = $('#minha-tabela').DataTable({
 *       rowGroup:    grp.rowGroup,
 *       rowCallback: grp.rowCallback,
 *       orderFixed:  grp.orderFixed,
 *   });
 *   grp.bindClick('#minha-tabela tbody', function () { return dt; });
 *   grp.bindExpandCollapseAll('#btn-expand', '#btn-collapse', function () { return dt; });
 *   // Ao recarregar dados:
 *   grp.reset();
 */
function collapsibleRowGroup(options) {
    var state      = {};
    var dataSrc    = options.dataSrc;
    var colIndex   = options.colIndex !== undefined ? options.colIndex : null;
    var colSpan    = options.colSpan    || 1;
    var extraClass = options.extraClass || '';
    var badgeClass = options.badgeClass || 'badge-primary';
    var countLabel = options.countLabel || 'item';
    var summaryFn  = options.summaryFn  || null;

    function getScrollBody(getTableFn) {
        return $(getTableFn().table().node()).closest('.dataTables_scrollBody');
    }

    function drawPreservingScroll(getTableFn, $scrollBody) {
        var top = ($scrollBody && $scrollBody.length) ? $scrollBody.scrollTop() : 0;
        getTableFn().draw(false);
        if ($scrollBody && $scrollBody.length) {
            setTimeout(function () { $scrollBody.scrollTop(top); }, 0);
        }
    }

    function expandAll(getTableFn) {
        var dt = getTableFn();
        dt.rows().data().each(function (row) { state[row[dataSrc]] = true; });
        drawPreservingScroll(getTableFn, getScrollBody(getTableFn));
    }

    function collapseAll(getTableFn) {
        Object.keys(state).forEach(function (k) { delete state[k]; });
        drawPreservingScroll(getTableFn, getScrollBody(getTableFn));
    }

    var result = {

        rowGroup: {
            dataSrc: dataSrc,
            startRender: function (rows, group) {
                var isOpen = !!state[group];
                var count  = rows.count();
                var icon   = isOpen ? 'fa-chevron-down' : 'fa-chevron-right';
                var extra  = summaryFn ? summaryFn(rows) : '';

                return $('<tr class="dtrg-group ' + extraClass + '"/>')
                    .append(
                        $('<td colspan="' + colSpan + '"/>').html(
                            '<i class="fas ' + icon + ' mr-2 text-muted fa-sm"></i>' +
                            '<strong>' + group + '</strong>' +
                            '<span class="badge ' + badgeClass + ' badge-pill ml-2">' +
                                count + ' ' + countLabel + (count !== 1 ? 's' : '') +
                            '</span>' + extra
                        )
                    )
                    .attr('data-name', group)
                    .attr('data-src', dataSrc);
            }
        },

        rowCallback: function (row, data) {
            row.style.display = state[data[dataSrc]] ? '' : 'none';
        },

        reset: function () {
            Object.keys(state).forEach(function (k) { delete state[k]; });
        },

        expandAll: expandAll,
        collapseAll: collapseAll,

        bindClick: function (tbodySelector, getTableFn) {
            $(tbodySelector).off('click.crg').on('click.crg', 'tr.dtrg-group', function () {
                var name      = $(this).data('name');
                var $scroll   = $(this).closest('.dataTables_scrollBody');
                var scrollTop = $scroll.scrollTop();
                state[name]   = !state[name];
                getTableFn().draw(false);
                setTimeout(function () { $scroll.scrollTop(scrollTop); }, 0);
            });
        },

        bindExpandCollapseAll: function (expandSel, collapseSel, getTableFn) {
            $(expandSel).off('click.crg-ea').on('click.crg-ea', function () {
                expandAll(getTableFn);
            });
            $(collapseSel).off('click.crg-ca').on('click.crg-ca', function () {
                collapseAll(getTableFn);
            });
        }
    };

    if (colIndex !== null) {
        result.orderFixed = { pre: [[colIndex, 'asc']] };
    }

    return result;
}
