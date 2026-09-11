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
                        <button type="button" id="limparSelecaoGraficos"
                            class="btn btn-xs btn-outline-secondary ml-2" style="display:none">
                            <i class="fas fa-times"></i> Limpar seleção (<span class="qtd-sel">0</span>)
                        </button>
                        <table id="produzidosTable" class="table table-bordered table-font-small compact">
                            <tfoot>
                                <tr>
                                    <th></th>{{-- 0: actions --}}
                                    <th></th>{{-- 1: empresa --}}
                                    <th></th>{{-- 2: embarque --}}
                                    <th></th>{{-- 3: pedido --}}
                                    <th class="text-right">Total:</th>{{-- 4: cliente --}}
                                    <th></th>{{-- 5: pneus (total preenchido via footerCallback) --}}
                                    <th></th>{{-- 6: vendedor --}}
                                    <th></th>{{-- 7: expedição --}}
                                    <th></th>{{-- 8: data --}}
                                    <th></th>{{-- 9: gerente (hidden) --}}
                                    <th></th>{{-- 10: mês/ano (hidden) --}}
                                    @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                        <th></th>{{-- 11: valor --}}
                                    @endhasrole
                                    <th></th>{{-- 11/12: supervisor (hidden) --}}
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap4.min.css">
    <style>
        .col-actions {
            width: 1% !important;
        }
        /* --- Header --- */
        table.dataTable thead tr {
            background-color: #444B53;
            color: #ffffff;
        }

        table.dataTable thead th {
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .3px;
            padding: 8px 10px;
            border-bottom: 2px solid #2d3238 !important;
            white-space: nowrap;
        }

        /* Cross-highlight: seleção de linha para filtrar/enfatizar os gráficos */
        #produzidosTable tbody tr {
            cursor: pointer;
        }

        #produzidosTable tbody tr.linha-selecionada > td {
            background-color: #fff3cd !important;
            box-shadow: inset 3px 0 0 #e0a800;
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
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
    <script src="{{ asset('js/dashboard/cross-filter.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/chart-focus-bar.js') }}?v={{ time() }}"></script>
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge badge-danger">{{ NM_PESSOA }}</span>
            <table class="table stripe row-border no-padding table-left" id="{{ DETAIL_ID }}" style="width:80%">
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

            window.podeVerValorProduzidos =
                {{ auth()->user()->hasRole('admin|supervisor|gerente unidade|gerente comercial') ? 'true' : 'false' }};

            // Estado de cross-highlight dos graficos (cross-filter.js) - precisa existir
            // antes do primeiro initTablePneus(), que ja usa cf.rowCallback/attachTable.
            var baseRows = [];

            var COR = {
                qtdFoco: 'rgba(60, 145, 230, 0.85)',
                vlrFoco: 'rgba(220, 53, 69, 0.85)',
                mesFoco: 'rgba(73, 80, 87, 0.85)'
            };

            function rowKey(d) {
                return [d.NR_COLETA, d.EXPEDICIONADO, d.NR_EMBARQUE, d.NR_LOTEEXP].join('|');
            }

            var cf = crossFilter({
                dims: ['mes', 'gerente', 'cliente', 'supervisor', 'linhas'],
                campo: {
                    mes: function(r) { return r.MES_ANO; },
                    gerente: function(r) { return r.NM_GERENTE; },
                    cliente: function(r) { return String(r.CD_PESSOA); },
                    supervisor: function(r) { return r.NM_SUPERVISOR; },
                    linhas: rowKey
                }
            });
            // Selecao de linha e so enfase visual - nao filtra a tabela.
            cf.attachTable('produzidosTable', { ignorarDims: ['linhas'] });

            $('#grupo_item').select2({
                placeholder: 'Selecione o grupo',
                theme: 'bootstrap4',
            });

            $('#cd_regiaocomercial').select2({
                theme: 'bootstrap4',
            });

            $('#supervisor').select2({
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

                // Nova consulta = contexto novo: zera as selecoes de cross-highlight.
                cf.clear();

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

            function buildDetailId(d) {
                return ('pedido-' + d.NR_COLETA + '-' + d.EXPEDICIONADO + '-' + d.NR_EMBARQUE + '-' + d.NR_LOTEEXP)
                    .replace(/[^A-Za-z0-9_-]+/g, '_'); // "SEM EMBARQUE" -> "SEM_EMBARQUE"
            }

            $(document).on('click', '.btn-detalhes', function() {

                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var d = row.data();
                var tableId = buildDetailId(d);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    // Open this row
                    row.child(template($.extend({}, d, { DETAIL_ID: tableId }))).show();
                    initTable(tableId, d);
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

            $(document).on('click', '.btn-enviar-whatsapp', function() {
                var row = table.row($(this).closest('tr')).data();
                var $icon = $(this).find('i');

                // Abre a aba já no clique para não esbarrar no bloqueador de pop-up;
                // o endereço só é definido depois que os detalhes chegam.
                var win = window.open('', '_blank');

                $icon.removeClass('fab fa-whatsapp').addClass('fas fa-spinner fa-spin');

                $.get(window.routes.getPneusProduzidosSemFaturarDetails, {
                    pedido: row.NR_COLETA,
                    nr_embarque: row.NR_EMBARQUE,
                    expedicionado: row.EXPEDICIONADO,
                    nr_loteexp: row.NR_LOTEEXP
                }).done(function(resp) {
                    var itens = (resp && resp.data) ? resp.data : [];

                    if (!itens.length) {
                        if (win) win.close();
                        Swal.fire('Sem itens', 'Nenhum pneu encontrado para este pedido.', 'info');
                        return;
                    }

                    var msg = montaMensagemWhatsapp(row, itens);
                    var url = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(msg);

                    if (win) {
                        win.location = url;
                    } else {
                        window.open(url, '_blank');
                    }
                }).fail(function() {
                    if (win) win.close();
                    Swal.fire('Erro', 'Não foi possível carregar os detalhes do pedido.', 'error');
                }).always(function() {
                    $icon.removeClass('fas fa-spinner fa-spin').addClass('fab fa-whatsapp');
                });
            });

            function formataMoeda(valor) {
                return (Number(valor) || 0).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function montaMensagemWhatsapp(row, itens) {
                var podeVerValor = window.podeVerValorProduzidos;
                var grupos = {};
                var totalPneus = itens.length;
                var totalValor = 0;

                itens.forEach(function(item) {
                    var unit = parseFloat(item.VALOR) || 0;
                    var chave = podeVerValor ? item.DS_ITEM + '|' + unit : item.DS_ITEM;

                    grupos[chave] = grupos[chave] || {
                        ds_item: item.DS_ITEM,
                        unit: unit,
                        qtd: 0,
                        total: 0
                    };
                    grupos[chave].qtd += 1;
                    grupos[chave].total += unit;
                    totalValor += unit;
                });

                var embarque = (row.NR_EMBARQUE && row.NR_EMBARQUE !== 'SEM EMBARQUE') ?
                    'Embarque ' + row.NR_EMBARQUE :
                    'Sem embarque';

                var linhas = [];
                linhas.push('*Produzidos sem Faturar - Pedido ' + row.NR_COLETA + '*');
                linhas.push('Cliente: ' + row.NM_PESSOA);
                linhas.push(embarque + ' | Vendedor: ' + (row.NM_VENDEDOR || '-') +
                    ' | Expedicao: ' + row.EXPEDICIONADO + ' | ' + totalPneus + ' pneus');
                linhas.push('');

                Object.keys(grupos).forEach(function(chave) {
                    var g = grupos[chave];
                    var linha = '- ' + g.ds_item + ' | Qtd ' + g.qtd;
                    if (podeVerValor) {
                        linha += ' | Unit R$ ' + formataMoeda(g.unit) +
                            ' | Total R$ ' + formataMoeda(g.total);
                    }
                    linhas.push(linha);
                });

                linhas.push('');
                linhas.push('Total: ' + totalPneus + ' pneus' +
                    (podeVerValor ? ' | R$ ' + formataMoeda(totalValor) : ''));

                return linhas.join('\n');
            }

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
                    scrollY: '400px',
                    paging: false,
                    pageLength: -1,
                    searchDelay: 300,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "Todos"],
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                {
                                    extend: "excelHtml5",
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    className: 'btn btn-xs btn-success',
                                    title: 'Pneus Produzidos Sem Faturar',
                                    footer: false,
                                }
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
                            // Universo dos graficos = retorno do backend. Busca/clique
                            // sao camadas de cross-highlight tratadas em setBase/renderCharts.
                            setBase(json.datatables.data);
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
                            title: "Embarque",
                            className: "text-center",
                        },
                        {
                            "data": "NR_COLETA",
                            title: "Pedido",
                            className: "text-center",
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
                            render: function(data, type) {
                                if (type !== 'display') {
                                    return data;
                                }
                                var sim = data === 'SIM';
                                return '<span class="badge badge-' + (sim ? 'success' : 'danger') +
                                    '">' + (sim ? 'SIM' : 'NÃO') + '</span>';
                            }
                        },
                        {
                            "data": "DTFIM",
                            name: "DTFIM",
                            render: function(data, type) {
                                if (type === 'display' || type === 'filter') {
                                    return data ? moment(data).format('DD/MM/YYYY HH:mm') : '';
                                }
                                // sort/type: valor cru "YYYY-MM-DD HH:mm:ss" ordena cronologicamente
                                return data || '';
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
                                name: "VALOR",
                                title: "Valor",
                                className: "text-right"
                            },
                        @endhasrole {
                            "data": "NM_SUPERVISOR",
                            title: "Supervisor",
                            "visible": false
                        },
                    ],
                    "columnDefs": [{
                        "targets": 0,
                        "className": "text-center",
                    }],

                    order: [
                        ['DTFIM:name', 'asc']
                    ],

                    // Mantem a enfase da linha selecionada apos redraw (busca/ordenacao).
                    rowCallback: cf.rowCallback,

                    footerCallback: function(row, data, start, end, display) {
                        var api = new $.fn.dataTable.Api(this);

                        var QtdPneus = 0;
                        var valorTotal = 0;
                        var expedicionadoSim = 0;
                        var expedicionadoNao = 0;
                        var embarqueSim = 0;
                        var embarqueNao = 0;

                        // Agrega sobre as linhas visiveis (respeita o filtro/busca),
                        // igual ao total de pneus no rodape.
                        api.rows({ search: 'applied' }).data().each(function(item) {
                            var pneus = Number(item.PNEUS) || 0;
                            QtdPneus += pneus;
                            valorTotal += parseFloat(
                                String(item.VALOR).replace(/\./g, '').replace(',', '.')
                            ) || 0;

                            if (item.EXPEDICIONADO === 'SIM') {
                                expedicionadoSim += pneus;
                            } else {
                                expedicionadoNao += pneus;
                            }
                            if (item.ST_EMBARQUE !== 'SEM EMBARQUE') {
                                embarqueSim += pneus;
                            } else {
                                embarqueNao += pneus;
                            }
                        });

                        var valorFmt = 'R$ ' + valorTotal.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        // Rodape: total de pneus
                        $(api.column(5).footer()).html(QtdPneus.toLocaleString('pt-BR'));

                        // Rodape: total do valor (coluna so existe com permissao)
                        var colValor = api.column('VALOR:name');
                        if (colValor.length && colValor.footer()) {
                            $(colValor.footer()).addClass('text-right').html(valorFmt);
                        }

                        $('.pneusTotal').html(QtdPneus.toLocaleString('pt-BR'));
                        $('#valorTotal').html(valorFmt);
                        $('#expedicionadoSim').html(expedicionadoSim.toLocaleString('pt-BR'));
                        $('#expedicionadoNao').html(expedicionadoNao.toLocaleString('pt-BR'));
                        $('#embarqueSim').html(embarqueSim.toLocaleString('pt-BR'));
                        $('#embarqueNao').html(embarqueNao.toLocaleString('pt-BR'));

                    },

                });

                // A busca nativa da tabela redefine o universo dos graficos. O render
                // inicial e o do botao "#search" ja acontecem no dataSrc; comeca em ''
                // para nao renderizar de novo no primeiro draw. Ordenacao nao altera o
                // conjunto, entao e ignorada.
                var ultimoFiltroGraficos = '';
                table.on('draw.dt', function() {
                    var filtroAtual = table.search();
                    if (filtroAtual === ultimoFiltroGraficos) {
                        return;
                    }
                    ultimoFiltroGraficos = filtroAtual;
                    setBase(table.rows({ search: 'applied' }).data().toArray());
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
                                'nr_embarque': data.NR_EMBARQUE === 'SEM EMBARQUE' ? 0 : data.NR_EMBARQUE,
                                'expedicionado': data.EXPEDICIONADO,
                                'nr_loteexp': data.NR_LOTEEXP
                            }
                        },
                        columns: [{
                                data: "EXPEDICIONADO",
                                title: "Expedicionado",
                                className: 'text-center'
                            },
                            {
                                data: "NRORDEMPRODUCAO",
                                title: "Nr Ordem",
                                className: 'text-center'
                            },
                            {
                                data: "DS_ITEM",
                                title: "Descrição"
                            },
                            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                {
                                    "data": "VALOR",
                                    title: "Valor",
                                    className: 'text-center',
                                    render: function(data) {
                                        if (data === null || data === undefined || data === '') {
                                            return '';
                                        }
                                        var valor = parseFloat(data);
                                        if (isNaN(valor)) {
                                            return data;
                                        }
                                        return valor.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    },
                                    createdCell: function(td, cellData) {
                                        if ((parseFloat(cellData) || 0) === 0) {
                                            $(td).css('background-color', '#f8d7da');
                                        }
                                    }
                                },
                            @endhasrole {
                                data: "DTFIM",
                                title: "Data",
                                className: 'text-center',
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

            // ============================================================
            //  Graficos com cross-highlight (multi-selecao), estilo Power BI.
            //  Estado/selecao: cross-filter.js (var cf, no topo deste script).
            //  Render dos graficos: chart-focus-bar.js (focusBarChart /
            //  focusDualBarChart). Aqui fica so o que e especifico desta tela:
            //  mapeamento de campos, agregacao e as cores/ids de cada grafico.
            // ============================================================
            function toNumeroBR(v) {
                return parseFloat(String(v).replace(/\./g, '').replace(',', '.')) || 0;
            }

            function agrupar(rows) {
                var g = {
                    mesQtd: {},
                    gerQtd: {}, gerVlr: {},
                    cliQtd: {}, cliVlr: {}, cliNome: {},
                    supQtd: {}, supVlr: {}
                };
                rows.forEach(function(r) {
                    var q = Number(r.PNEUS) || 0;
                    var v = toNumeroBR(r.VALOR);
                    var cli = String(r.CD_PESSOA);

                    g.mesQtd[r.MES_ANO] = (g.mesQtd[r.MES_ANO] || 0) + q;
                    g.gerQtd[r.NM_GERENTE] = (g.gerQtd[r.NM_GERENTE] || 0) + q;
                    g.gerVlr[r.NM_GERENTE] = (g.gerVlr[r.NM_GERENTE] || 0) + v;
                    g.cliQtd[cli] = (g.cliQtd[cli] || 0) + q;
                    g.cliVlr[cli] = (g.cliVlr[cli] || 0) + v;
                    if (g.cliNome[cli] === undefined) g.cliNome[cli] = r.NM_PESSOA || cli;
                    g.supQtd[r.NM_SUPERVISOR] = (g.supQtd[r.NM_SUPERVISOR] || 0) + q;
                    g.supVlr[r.NM_SUPERVISOR] = (g.supVlr[r.NM_SUPERVISOR] || 0) + v;
                });
                return g;
            }

            // "MM-YYYY" -> ordena cronologicamente; chaves invalidas vao pro fim
            function ordenaMesAno(a, b) {
                var pa = String(a).split('-'),
                    pb = String(b).split('-');
                if (pa.length < 2 || isNaN(pa[1])) return 1;
                if (pb.length < 2 || isNaN(pb[1])) return -1;
                return (pa[1] - pb[1]) || (pa[0] - pb[0]);
            }

            function setBase(rows) {
                baseRows = rows || [];
                renderCharts();
                cf.aplicarEnfase(table);
            }

            function renderCharts() {
                var total = agrupar(baseRows);
                var foco = agrupar(baseRows.filter(function(r) { return cf.isFocused(r); }));
                var temSelecao = cf.hasSelection();

                // ----- Meses (somente quantidade) -----
                var meses = Object.keys(total.mesQtd).sort(ordenaMesAno);
                var mFoco = meses.map(function(m) { return foco.mesQtd[m] || 0; });
                var mTot = meses.map(function(m) { return total.mesQtd[m] || 0; });
                var mResto = meses.map(function(m, i) { return mTot[i] - mFoco[i]; });

                focusBarChart('chartPneusMesAno', {
                    labels: meses,
                    chaves: meses,
                    foco: mFoco,
                    resto: mResto,
                    total: mTot,
                    cor: COR.mesFoco,
                    temSelecao: temSelecao,
                    onClick: function(chave) { onSegmentoClick('mes', chave); }
                });

                atualizaPercentual(mTot);

                // ----- Gerente / Cliente / Supervisor (quantidade + valor) -----
                renderDimensao('chartPneusGerente', 'legend-container-gerente', 'gerente',
                    total.gerQtd, total.gerVlr, foco.gerQtd, foco.gerVlr,
                    function(k) { return k; }, temSelecao);

                renderDimensao('chartPneusCliente', 'legend-container-cliente', 'cliente',
                    total.cliQtd, total.cliVlr, foco.cliQtd, foco.cliVlr,
                    function(k) { return String(total.cliNome[k] || k).split(' ')[0]; }, temSelecao);

                renderDimensao('chartPneusSupervisor', 'legend-container-supervisor', 'supervisor',
                    total.supQtd, total.supVlr, foco.supQtd, foco.supVlr,
                    function(k) { return k; }, temSelecao);

                atualizarBotaoLimpar();
            }

            function renderDimensao(chartId, legendId, dim, totQ, totV, focQ, focV, nomeFn, temSelecao) {
                var s = montarSeries(totQ, totV, focQ, focV, nomeFn);
                focusDualBarChart(chartId, legendId, {
                    labels: s.labels,
                    chaves: s.chaves,
                    temSelecao: temSelecao,
                    scrollAcimaDe: 6,
                    larguraPorItem: 90,
                    metrics: [
                        { foco: s.focoQ, resto: s.restoQ, total: s.totQ, cor: COR.qtdFoco, legenda: 'Quantidade', eixo: 'y' },
                        { foco: s.focoV, resto: s.restoV, total: s.totV, cor: COR.vlrFoco, legenda: 'Valor', eixo: 'y1', moeda: true }
                    ],
                    onClick: function(chave) { onSegmentoClick(dim, chave); }
                });
            }

            // Ordena as categorias por valor total desc e devolve os arrays alinhados
            function montarSeries(totQ, totV, focQ, focV, nomeFn) {
                var chaves = Object.keys(totQ).sort(function(a, b) {
                    return (totV[b] || 0) - (totV[a] || 0);
                });
                return {
                    chaves: chaves,
                    labels: chaves.map(nomeFn),
                    focoQ: chaves.map(function(k) { return focQ[k] || 0; }),
                    restoQ: chaves.map(function(k) { return (totQ[k] || 0) - (focQ[k] || 0); }),
                    totQ: chaves.map(function(k) { return totQ[k] || 0; }),
                    focoV: chaves.map(function(k) { return focV[k] || 0; }),
                    restoV: chaves.map(function(k) { return (totV[k] || 0) - (focV[k] || 0); }),
                    totV: chaves.map(function(k) { return totV[k] || 0; })
                };
            }

            function atualizaPercentual(qtdPorMes) {
                var ult = qtdPorMes.slice(-3);
                var pen = ult[ult.length - 2];
                var atu = ult[ult.length - 1];
                var pct = ((atu - pen) / pen) * 100;

                if (!isFinite(pct)) {
                    $('.calc-percentual').empty();
                    return;
                }
                $('.calc-percentual').html(
                    '<span class="' + (pct >= 0 ? 'text-success' : 'text-danger') + '">' +
                    '<i class="fas fa-arrow-' + (pct >= 0 ? 'up' : 'down') + '"></i> ' +
                    pct.toFixed(2) + '%</span>' +
                    '<span class="text-muted">' +
                    (pct >= 0 ? 'Aumento do ultimo Mês' : 'Queda do ultimo Mês') +
                    '</span>'
                );
            }

            // ----- Interacao: clique num segmento alterna o chip da dimensao -----
            function onSegmentoClick(dim, chave) {
                if (!dim || chave === undefined || chave === null) return;
                cf.toggle(dim, chave);
                renderCharts();
                if (table) table.draw(); // reaplica o filtro global -> tabela so com o foco
                cf.aplicarEnfase(table);
            }

            function atualizarBotaoLimpar() {
                var n = cf.count();
                $('#limparSelecaoGraficos').toggle(n > 0);
                $('#limparSelecaoGraficos .qtd-sel').text(n);
            }

            function limparSelecao() {
                cf.clear();
                $('#produzidosTable tbody tr').removeClass('linha-selecionada');
                renderCharts();
                if (table) table.draw(); // remove o filtro global -> tabela volta ao universo
            }

            $('#limparSelecaoGraficos').on('click', limparSelecao);

            // Clique numa linha da tabela alterna a selecao daquela linha
            $('#produzidosTable').on('click', '> tbody > tr', function(e) {
                if ($(e.target).closest('.btn-detalhes, .btn-observacao-embarque, .btn-enviar-whatsapp, button, a').length) {
                    return;
                }
                var d = table.row(this).data();
                if (!d) return; // linha de detalhe (child row)
                cf.toggle('linhas', rowKey(d));
                renderCharts();
                if (table) table.draw(); // se veio de outra dimensao, remove o filtro dela
                cf.aplicarEnfase(table);
            });



        });

        $('link[href*="custom_datatables"]').remove();
    </script>
@stop
