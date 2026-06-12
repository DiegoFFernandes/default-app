@extends('layouts.master')

@section('title', 'Análise Faturamento')

@section('content')
    <section class="content">

        {{-- KPI Cards --}}
        <div class="row mb-2">
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-success"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Emitidas</span>
                        <span class="info-box-number notas-emitidas">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-danger"><i class="fas fa-ban"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Canceladas</span>
                        <span class="info-box-number canceladas">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Faturistas</span>
                        <span class="info-box-number nr-faturista">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-warning"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Itens/Nota</span>
                        <span class="info-box-number media-itens">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-orange"><i class="fas fa-percent"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tx. Cancelamento</span>
                        <span class="info-box-number taxa-cancelamento">—</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="info-box shadow-sm mb-2">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Notas/Dia</span>
                        <span class="info-box-number media-dia">—</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Coluna esquerda --}}
            <div class="col-md-8">

                {{-- Filtros --}}
                <div class="card card-primary card-outline">
                    <div class="card-header py-2">
                        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtros</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label class="small font-weight-bold mb-1">Período de Emissão</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="daterange" readonly
                                        placeholder="Selecione o período">
                                    <div class="input-group-append">
                                        <span class="input-group-text btn-limpar-data" style="cursor:pointer"
                                            title="Limpar data">
                                            <i class="fas fa-times text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="submit-seach">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                            <div class="col-md-5">
                                <label class="small font-weight-bold mb-1">Busca rápida</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input id="filtro-global" type="text" class="form-control"
                                        placeholder="Empresa, faturista, cliente...">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <input id="filtro-empresa" type="text" class="form-control form-control-sm"
                                    placeholder="Filtrar por Empresa">
                            </div>
                            <div class="col-md-4">
                                <input id="filtro-usuario" type="text" class="form-control form-control-sm"
                                    placeholder="Filtrar por Faturista">
                            </div>
                            <div class="col-md-4">
                                <input id="filtro-data" type="text" class="form-control form-control-sm"
                                    placeholder="Filtrar por Data (dd/mm/aaaa)">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico diário --}}
                <div class="card card-outline card-primary">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1 text-primary"></i> Produção Diária por Faturista
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="usuario-diario-chart" height="75"></canvas>
                    </div>
                </div>

                {{-- Tabela Faturistas --}}
                <div class="card card-outline card-success">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-1 text-success"></i> Notas Emitidas por Faturista
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" id="btn-expand-faturistas" title="Expandir todos">
                                <i class="fas fa-expand-alt"></i>
                            </button>
                            <button class="btn btn-tool" id="btn-collapse-faturistas" title="Recolher todos">
                                <i class="fas fa-compress-alt"></i>
                            </button>
                            <button class="btn btn-tool" id="btn-export-faturistas" title="Exportar para Excel">
                                <i class="fas fa-file-excel text-success"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <table id="tabela-faturista" class="table table-hover compact table-font-small table-striped mb-0"
                            style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Emp</th>
                                    <th>Faturista</th>
                                    <th>Cliente</th>
                                    <th>Lançamento</th>
                                    <th>Série</th>
                                    <th>Dt Emissão</th>
                                    <th>Status</th>
                                    <th>Itens</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

                {{-- Tabela Cancelamentos --}}
                <div class="card card-outline card-danger">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-times-circle mr-1 text-danger"></i> Motivos de Cancelamento
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" id="btn-expand-cancelamentos" title="Expandir todos">
                                <i class="fas fa-expand-alt"></i>
                            </button>
                            <button class="btn btn-tool" id="btn-collapse-cancelamentos" title="Recolher todos">
                                <i class="fas fa-compress-alt"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <table id="tabela-cancelamentos" class="table table-hover compact table-font-small mb-0"
                            style="width:100%">                            
                        </table>

                    </div>
                </div>

            </div>

            {{-- Coluna direita --}}
            <div class="col-md-4">

                <div class="card card-outline card-success">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1 text-success"></i> Emitidas por Mês
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height:185px">
                            <canvas id="notas-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-user-check mr-1 text-info"></i> Notas por Faturista
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height:210px">
                            <canvas id="usuario-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-danger">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-user-times mr-1 text-danger"></i> Cancelamentos por Responsável
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height:210px">
                            <canvas id="notas-canceladas-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-warning">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1 text-warning"></i> Distribuição por Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height:200px">
                            <canvas id="status-chart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop

@section('js')
    <style>
        tr.dtrg-group td {
            background: linear-gradient(90deg, #eef2f7, #e4eaf3) !important;
            font-weight: 600;
            font-size: .85rem;
            border-left: 3px solid #3498db !important;
            padding: 7px 12px !important;
            cursor: pointer;
            user-select: none;
        }

        tr.dtrg-group:hover td {
            background: linear-gradient(90deg, #dce6f0, #d0dcea) !important;
        }

        tr.dtrg-group.group-cancelamentos td {
            border-left-color: #e74c3c !important;
        }

        #tabela-faturista td,
        #tabela-cancelamentos td {
            font-size: .82rem;
            vertical-align: middle;
        }

        .badge-status {
            font-size: .72rem;
            padding: 3px 7px;
        }

        .info-box-number {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .info-box-text {
            font-size: .78rem;
        }
    </style>

    <script src="{{ asset('js/dashboard/datatables-collapsible-group.js') }}"></script>
    <script>
        var inicioData = 0,
            fimData = 0;
        var table, tableCancelados;
        var dadosFiltrados = [];
        var faturistaGroup, cancelamentosGroup;
        var chartUserDay, chartUser, chartNotas, chartUserCanc, chartStatus;

        const COLORS = [
            '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
            '#1abc9c', '#e67e22', '#34495e', '#16a085', '#8e44ad',
            '#2980b9', '#27ae60', '#c0392b', '#d35400', '#7f8c8d'
        ];

        const PT_BR_LANG = "{{ asset('vendor/datatables/pt-BR.json') }}";

        /* ── DateRangePicker ── */
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Limpar',
                applyLabel: 'Aplicar',
                format: 'DD/MM/YYYY',
                separator: ' - ',
                daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                ],
                firstDay: 0
            }
        }).attr('readonly', true);

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            inicioData = picker.startDate.format('MM/DD/YYYY');
            fimData = picker.endDate.format('MM/DD/YYYY');
        });
        $('#daterange').on('cancel.daterangepicker', function() {
            $(this).val('');
            inicioData = 0;
            fimData = 0;
        });
        $('.btn-limpar-data').on('click', function() {
            $('#daterange').val('');
            inicioData = 0;
            fimData = 0;
        });

        $('#submit-seach').on('click', function() {
            if (!inicioData) {
                msgToastr('Selecione um período antes de buscar.', 'info');
                return;
            }
            CarregaDados(inicioData, fimData);
        });

        /* ── Inicialização ── */
        InicializaTabelas();
        CarregaDados(0, 0);

        /* ── DataTables ── */
        function InicializaTabelas() {
            faturistaGroup = collapsibleRowGroup({
                dataSrc: 'USUARIO',
                colIndex: 1,
                colSpan: 8,
                countLabel: 'nota',
                summaryFn: function(rows) {
                    var total = rows.data().pluck('QTD_ITENS').reduce(function(a, b) {
                        return a + (parseInt(b) || 0);
                    }, 0);
                    return '<span class="badge badge-light ml-1"><i class="fas fa-boxes mr-1"></i>' + total + ' itens</span>';
                }
            });

            table = $('#tabela-faturista').DataTable({
                data: [],
                scrollY: '400px',
                scrollX: true,
                language: {
                    url: PT_BR_LANG
                },
                order: [
                    [1, 'asc'],
                    [5, 'asc']
                ],
                orderFixed: faturistaGroup.orderFixed,
                pageLength: 50,
                rowGroup: faturistaGroup.rowGroup,
                rowCallback: faturistaGroup.rowCallback,
                columns: [{
                        title: 'Emp',
                        data: 'CD_EMPRESA',
                        width: '50px',
                        className: 'text-center'
                    },
                    {
                        title: 'Faturista',
                        data: 'USUARIO',
                        visible: false
                    },
                    {
                        title: 'Cliente',
                        data: 'NM_PESSOA',
                        className: 'text-nowrap'
                    },
                    {
                        title: 'Lançamento',
                        data: 'NR_LANCAMENTO',
                        className: 'text-center',
                        width: '90px'
                    },
                    {
                        title: 'Série',
                        data: 'CD_SERIE',
                        className: 'text-center',
                        width: '55px'
                    },
                    {
                        title: 'Emissão',
                        data: 'DT_EMISSAO',
                        className: 'text-center',
                        width: '90px'
                    },
                    {
                        title: 'Status',
                        data: 'ST_NOTA',
                        className: 'text-center',
                        width: '80px',
                        render: function(d) {
                            if (d === 'C')
                                return '<span class="badge badge-danger badge-status">Cancelada</span>';
                            if (d === 'V')
                                return '<span class="badge badge-success badge-status">Valida</span>';

                            return '<span class="badge badge-secondary badge-status">' + (d || '—') +
                                '</span>';
                        }
                    },
                    {
                        title: 'Itens',
                        data: 'QTD_ITENS',
                        className: 'text-center',
                        width: '60px'
                    }
                ],
                dom: '<"row align-items-center mb-1"<"col-auto"l>>rt<"row align-items-center mt-1"<"col-sm-5"i><"col-sm-7"p>>',
            });

            faturistaGroup.bindClick('#tabela-faturista tbody', function() { return table; });
            faturistaGroup.bindExpandCollapseAll(
                '#btn-expand-faturistas',
                '#btn-collapse-faturistas',
                function() { return table; }
            );

            cancelamentosGroup = collapsibleRowGroup({
                dataSrc: 'DS_MOTIVO',
                colIndex: 0,
                colSpan: 5,
                countLabel: 'nota',
                extraClass: 'group-cancelamentos',
                badgeClass: 'badge-danger'
            });

            tableCancelados = $('#tabela-cancelamentos').DataTable({
                data: [],
                scrollY: '400px',
                scrollX: true,
                language: {
                    url: PT_BR_LANG
                },
                order: [
                    [0, 'asc']
                ],
                orderFixed: cancelamentosGroup.orderFixed,
                pageLength: 25,
                rowGroup: cancelamentosGroup.rowGroup,
                rowCallback: cancelamentosGroup.rowCallback,
                columns: [{
                        title: 'Motivo',
                        data: 'DS_MOTIVO',
                        visible: false
                    },
                    {
                        title: 'Faturista',
                        data: 'USUARIO'
                    },
                    {
                        title: 'Cliente',
                        data: 'NM_PESSOA'
                    },
                    {
                        title: 'Emissão',
                        data: 'DT_EMISSAO',
                        className: 'text-center',
                        width: '90px'
                    },
                    {
                        title: 'Cancelado por',
                        data: 'USUARIOCANC'
                    }
                ],
                dom: '<"row align-items-center mb-1"<"col-auto"l>>rt<"row align-items-center mt-1"<"col-sm-5"i><"col-sm-7"p>>'
            });

            cancelamentosGroup.bindClick('#tabela-cancelamentos tbody', function() { return tableCancelados; });
            cancelamentosGroup.bindExpandCollapseAll(
                '#btn-expand-cancelamentos',
                '#btn-collapse-cancelamentos',
                function() { return tableCancelados; }
            );
        }

        /* ── Carregar dados via Ajax ── */
        function CarregaDados(inicioData, fimData) {

            faturistaGroup.reset();
            cancelamentosGroup.reset();

            $.ajax({
                url: "{{ route('get-analise-faturamento.index') }}",
                data: {
                    inicioData: inicioData,
                    fimData: fimData
                },
                beforeSend: function() {
                    Swal.fire({
                        title: "Carregando...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function(response) {
                    dadosFiltrados = response;
                    table.clear().rows.add(dadosFiltrados).draw();

                    var cancelados = dadosFiltrados.filter(function(d) {
                        return d.DS_MOTIVO && d.DS_MOTIVO.trim() !== '';
                    });
                    tableCancelados.clear().rows.add(cancelados).draw();

                    atualizaDados(dadosFiltrados);
                },
                error: function(xhr) {
                    if (xhr.status !== 401) msgToastr('Erro ao carregar dados de faturamento.', 'error');
                },
                complete: function() {
                    Swal.close();
                }
            });
        }

        /* ── Filtros da tabela ── */
        $('#filtro-global').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#filtro-empresa').on('keyup', function() {
            table.column(0).search(this.value).draw();
        });
        $('#filtro-usuario').on('keyup', function() {
            table.column(1).search(this.value).draw();
        });
        $('#filtro-data').on('keyup', function() {
            table.column(5).search(this.value).draw();
        });

        /* ── Export ── */
        $('#btn-export-faturistas').on('click', function() {
            exportarParaExcel(dadosFiltrados, 'faturistas.xlsx', 'Faturistas');
        });

        /* ── Atualiza cards e gráficos ── */
        function atualizaDados(dados) {
            var qtdNotaMes = {};
            var qtdNotaUser = {};
            var qtdCancelada = {};
            var statusCount = {};
            var totalItens = 0;
            var nomeMes = [];

            dados.forEach(function(item) {
                var mes = item.DS_MES;
                var usuario = item.USUARIO;
                var usuarioCanc = item.USUARIOCANC;
                var status = item.ST_NOTA || 'N/A';

                if (!nomeMes.includes(mes)) nomeMes.push(mes);
                qtdNotaMes[mes] = (qtdNotaMes[mes] || 0) + 1;
                qtdNotaUser[usuario] = (qtdNotaUser[usuario] || 0) + 1;
                statusCount[status] = (statusCount[status] || 0) + 1;
                totalItens += parseInt(item.QTD_ITENS || 0);

                if (usuarioCanc && usuarioCanc.trim()) {
                    qtdCancelada[usuarioCanc] = (qtdCancelada[usuarioCanc] || 0) + 1;
                }
            });

            var qtdPorMes = Object.values(qtdNotaMes);
            var qtdEmitidas = qtdPorMes.reduce(function(a, b) {
                return a + b;
            }, 0);
            var qtdCanceladas = Object.values(qtdCancelada).reduce(function(a, b) {
                return a + b;
            }, 0);
            var nomeUser = Object.keys(qtdNotaUser);
            var qtdPorUser = Object.values(qtdNotaUser);
            var nomeUserCanc = Object.keys(qtdCancelada);
            var qtdPorUserCanc = Object.values(qtdCancelada);
            var diasUnicos = [...new Set(dados.map(function(d) {
                return d.DT_EMISSAO;
            }))].length;

            var mediaItens = qtdEmitidas > 0 ? (totalItens / qtdEmitidas).toFixed(1) : '—';
            var taxaCanc = qtdEmitidas > 0 ? ((qtdCanceladas / qtdEmitidas) * 100).toFixed(1) + '%' : '—';
            var mediaDia = diasUnicos > 0 ? (qtdEmitidas / diasUnicos).toFixed(1) : '—';

            $('.notas-emitidas').text(qtdEmitidas.toLocaleString('pt-BR'));
            $('.canceladas').text(qtdCanceladas.toLocaleString('pt-BR'));
            $('.nr-faturista').text(nomeUser.length);
            $('.media-itens').text(mediaItens);
            $('.taxa-cancelamento').text(taxaCanc);
            $('.media-dia').text(mediaDia);

            carregaChartDiario(dados);
            carregaChartMes(nomeMes, qtdPorMes);
            carregaChartUser(nomeUser, qtdPorUser);
            carregaChartUserCancel(nomeUserCanc, qtdPorUserCanc);
            carregaChartStatus(statusCount);
        }

        /* ── Gráfico: Produção diária por faturista ── */
        function carregaChartDiario(dados) {
            var datasUnicas = [...new Set(dados.map(function(d) {
                return d.DT_EMISSAO;
            }))].sort();
            var usuariosUnicos = [...new Set(dados.map(function(d) {
                return d.USUARIO;
            }))];

            var datasets = usuariosUnicos.map(function(usuario, idx) {
                var porData = {};
                dados.filter(function(d) {
                    return d.USUARIO === usuario;
                }).forEach(function(d) {
                    porData[d.DT_EMISSAO] = (porData[d.DT_EMISSAO] || 0) + 1;
                });
                var cor = COLORS[idx % COLORS.length];
                return {
                    label: usuario,
                    data: datasUnicas.map(function(dt) {
                        return porData[dt] || 0;
                    }),
                    borderColor: cor,
                    backgroundColor: cor + '20',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    fill: false,
                    borderWidth: 2
                };
            });

            if (chartUserDay) chartUserDay.destroy();
            chartUserDay = new Chart(document.getElementById('usuario-diario-chart'), {
                type: 'line',
                data: {
                    labels: datasUnicas,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                footer: function(items) {
                                    var total = items.reduce(function(s, i) {
                                        return s + i.raw;
                                    }, 0);
                                    return 'Total: ' + total + ' nota' + (total !== 1 ? 's' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        /* ── Gráfico: Notas emitidas por mês ── */
        function carregaChartMes(nomeMes, qtdPorMes) {
            if (chartNotas) chartNotas.destroy();
            chartNotas = new Chart(document.getElementById('notas-chart'), {
                type: 'bar',
                data: {
                    labels: nomeMes,
                    datasets: [{
                        label: 'Notas Emitidas',
                        data: qtdPorMes,
                        backgroundColor: 'rgba(46,204,113,0.75)',
                        borderColor: 'rgba(39,174,96,1)',
                        borderWidth: 2,
                        borderRadius: 4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#27ae60',
                            font: {
                                weight: 'bold',
                                size: 11
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        /* ── Gráfico: Notas por faturista ── */
        function carregaChartUser(nomeUser, qtdPorUser) {
            if (chartUser) chartUser.destroy();
            chartUser = new Chart(document.getElementById('usuario-chart'), {
                type: 'bar',
                data: {
                    labels: nomeUser,
                    datasets: [{
                        label: 'Notas Emitidas',
                        data: qtdPorUser,
                        backgroundColor: nomeUser.map(function(_, i) {
                            return COLORS[i % COLORS.length] + 'CC';
                        }),
                        borderColor: nomeUser.map(function(_, i) {
                            return COLORS[i % COLORS.length];
                        }),
                        borderWidth: 1,
                        borderRadius: 3
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#2c3e50',
                            font: {
                                weight: 'bold',
                                size: 11
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        /* ── Gráfico: Cancelamentos por responsável ── */
        function carregaChartUserCancel(nomeUser, qtdPorUser) {
            if (chartUserCanc) chartUserCanc.destroy();
            chartUserCanc = new Chart(document.getElementById('notas-canceladas-chart'), {
                type: 'bar',
                data: {
                    labels: nomeUser,
                    datasets: [{
                        label: 'Canceladas',
                        data: qtdPorUser,
                        backgroundColor: 'rgba(231,76,60,0.75)',
                        borderColor: 'rgba(192,57,43,1)',
                        borderWidth: 1,
                        borderRadius: 3
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#c0392b',
                            font: {
                                weight: 'bold',
                                size: 11
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        /* ── Gráfico: Distribuição por status ── */
        function carregaChartStatus(statusCount) {
            var STATUS_LABEL = {
                'V': 'Valida',
                'C': 'Cancelada'
            };
            var STATUS_COLOR = {
                'V': '#2ecc71',
                'C': '#e74c3c'
            };
            var labels = Object.keys(statusCount).map(function(s) {
                return STATUS_LABEL[s] || s;
            });
            var data = Object.values(statusCount);
            var bgs = Object.keys(statusCount).map(function(s, i) {
                return STATUS_COLOR[s] || COLORS[i % COLORS.length];
            });

            if (chartStatus) chartStatus.destroy();
            chartStatus = new Chart(document.getElementById('status-chart'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgs,
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 14,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 13
                            },
                            formatter: function(value, ctx) {
                                var total = ctx.dataset.data.reduce(function(a, b) {
                                    return a + b;
                                }, 0);
                                return ((value / total) * 100).toFixed(1) + '%';
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    </script>
@stop
