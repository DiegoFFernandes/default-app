@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon"><i class="fas fa-sort-amount-up-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Qtde Laudos</span>
                        <span class="info-box-number qt-laudos"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Valor Pago</span>
                        <span class="info-box-number vl-laudo-pago"></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body mt-2" style="display: none">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="cd_empresa" id="cd_empresa" class="form-control" style="width: 100%;">
                                <option value="0" selected>Todas Empresas</option>
                                @foreach ($empresa as $e)
                                    <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input id="filtro-motivo" type="text" class="form-control" placeholder="Filtrar por Motivo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input id="filtro-usuario" type="text" class="form-control"
                                placeholder="Filtrar por Faturista">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input id="filtro-cliente" type="text" class="form-control"
                                placeholder="Filtrar por Cliente">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control float-right" id="daterange">
                            </div>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block float-right"
                            id="submit-seach">Filtrar</button>
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laudo por Empresa</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;" class="mt-3">
                            <canvas id="chart-por-empresa" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Evolução Mensal</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;" class="mt-3">
                            <canvas id="chart-por-mes" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Emissão por Usúario</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;">
                            <table id="tabela-usuario" class="compact table" style="font-size: 12px;"></table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Análise por Usúario</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;" class="mt-3">
                            <canvas id="chart-usuario"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Procedente</h3>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12 p-0">
                            <div style="overflow-y: auto;">
                                <table id="tabela-garantia" class="compact table" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>Pessoa</th>
                                            <th>Laudo</th>
                                            <th>Data</th>
                                            <th>Motivo</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Análise Procedente (R$)</h3>
                    </div>
                    <div class="card-body box">
                        <div class="subbox">
                            <canvas id="chart-motivo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Não procedente</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;">
                            <table id="tabela-nao-procedente" class="compact table" style="font-size: 12px;"></table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Análise Não Procedente (Qtd)</h3>
                    </div>
                    <div class="card-body box">
                        <div class="subbox2">
                            <canvas id="chart-nao-procedente"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        .tr-bg {
            background-color: #c9c9c9;
            font-weight: bold;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {

            display: none;
        }

        .card-body {
            padding: 0 1.25rem 1.25rem 1.25rem;
        }

        .box {
            overflow-y: scroll;
            max-height: 440px;
            height: 500px;
        }
    </style>

@stop
@section('js')
    <script>
        $(document).ready(function() {
            var dadosFiltrados;
            var tableMotivo = null;
            var tableAnalise = null;
            var collapsedGroups = {};
            var filtradoProcedente;
            var motivo = [];
            var vlrMotivo = [];
            var chartMotivoBarY = null;
            var chartUsuarioBarX = null;
            var chartNaoProcedenteBarY = null;
            var chartPorEmpresaBarX = null;
            var chartPorMesBarX = null;
            var tableUsuario = null;
            var tableNaoProcedente = null;
            var inicioData = 0;
            var fimData = 0;

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                inicioData = picker.startDate.format('MM/DD/YYYY');
                fimData = picker.endDate.format('MM/DD/YYYY');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val("");
                inicioData = 0;
                fimData = 0;
            });


            initTablePrimary();

            function initTablePrimary() {
                tableAnalise = $('#tabela-garantia').DataTable({
                    language: {
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                    scrollY: '300px',
                    // autoWidth: false,                
                    scrollCollapse: true,
                    paging: false,
                    searching: true,
                    ajax: {
                        url: "{{ route('get-analise-garantia') }}",
                        type: "GET",
                        data: {
                            nm_usuario: $('#filtro-usuario').val(),
                            ds_motivo: $('#filtro-motivo').val(),
                            nm_pessoa: $('#filtro-cliente').val(),
                            cd_empresa: $('#cd_empresa').val(),
                            dt_inicial: inicioData,
                            dt_final: fimData,
                        },
                        dataSrc: function(json) {
                            dadosFiltrados = json;

                            filtradoProcedente = json.filter(item => ['1', '3', '4'].includes(item
                                .CD_CLASSIFICACAO));

                            const resultado = filtradoProcedente.reduce((acc, item) => {
                                const motivo = item.DSMOTIVO;
                                const valor = parseFloat(item.VL_CLASSIFICACAO) || 0;

                                if (!acc[motivo]) {
                                    acc[motivo] = 0;
                                }
                                acc[motivo] += valor;
                                return acc;
                            }, {});


                            // Transforma em dois arrays para o Chart.js
                            const motivo = Object.keys(resultado);
                            const vlrMotivo = Object.values(resultado);

                            chartMotivoBarY = carregaCharBarY(motivo, vlrMotivo, 'chart-motivo',
                                chartMotivoBarY);


                            const qtdLaudos = json.length;
                            const valorTotal = json.reduce((valor, item) => valor + parseFloat(item
                                .VL_CLASSIFICACAO || 0), 0);

                            $('.vl-laudo-pago').html(valorTotal.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            $('.qt-laudos').html(qtdLaudos);

                            return filtradoProcedente;
                        }
                    },
                    rowGroup: gerarRowGroup({
                        dataSrc: 'DSMOTIVO',
                        valorSomado: 'VL_CLASSIFICACAO',
                        colSpan: 4
                    }),
                    columns: [{
                            data: 'NM_PESSOA',
                            width: '60px',
                        },
                        {
                            data: 'NR_LAUDO',
                            width: '50px'
                        },
                        {
                            data: 'DT_LAUDO'
                        },
                        {
                            data: 'DSMOTIVO'
                        },
                        {
                            data: 'VL_CLASSIFICACAO'
                        }
                    ],
                    columnDefs: [{
                        targets: 4,
                        className: 'dt-body-right'

                    }],
                    order: [
                        [3, 'asc']
                    ],
                });
            }

            function gerarRowGroup(config) {
                return {
                    dataSrc: config.dataSrc,
                    startRender: function(rows, group) {
                        const collapsed = !!collapsedGroups[group];

                        // Esconde ou mostra as linhas do grupo
                        rows.nodes().each(function(r) {
                            r.style.display = collapsed ? '' : 'none';
                        });

                        const total = rows
                            .data()
                            .pluck(config.valorSomado || 'VL_CLASSIFICACAO')
                            .reduce((a, b) => (parseFloat(a) || 0) + (parseFloat(b) || 0), 0)
                            .toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });

                        const colSpan = config.colSpan || 4;

                        return $('<tr/>')
                            .append(
                                `<td colspan="${colSpan}" class="tr-bg">
                                    <i class="fas fa-chevron-down mr-1"></i> ${group} (${rows.count()})
                                </td>
                                <td class="tr-bg text-right">${total}</td>`
                            )
                            .attr('data-name', group)
                            .toggleClass('collapsed', collapsed);
                    }
                };
            }
            $('#tabela-garantia tbody').on('click', 'tr.dtrg-start', function() {
                const name = $(this).data('name');
                const scrollBody = $('.dt-scroll-body');
                const scrollTop = scrollBody.scrollTop();

                collapsedGroups[name] = !collapsedGroups[name];
                tableAnalise.draw(false);

                setTimeout(() => {
                    scrollBody.scrollTop(scrollTop);
                    tableAnalise.columns.adjust(); // reajusta colunas
                }, 0);
            });

            $('#tabela-garantia').on('xhr.dt', function(e, settings, json, xhr) {

                const usuarioGarantia = json;
                const naoProcedente = json.filter(item => ['2'].includes(item.CD_CLASSIFICACAO));

                tableUsuario = initTableSecond(tableUsuario, 'tabela-usuario', usuarioGarantia,
                    'NM_USUARIO');
                tableNaoProcedente = initTableSecond(tableNaoProcedente, 'tabela-nao-procedente',
                    naoProcedente, 'DSMOTIVO');

                const resultado = json.reduce((acc, item) => {
                    const usuario = item.NM_USUARIO;
                    const motivo = item.DSMOTIVO;
                    const empresa = item.IDEMPRELAUDO;
                    const mes = item.NM_MES;

                    const valor = parseFloat(item.VL_CLASSIFICACAO) || 0;

                    // Agrupamento por usuário
                    if (!acc.porUsuario[usuario]) {
                        acc.porUsuario[usuario] = 0;
                    }
                    acc.porUsuario[usuario] += valor;

                    // Agrupamento por Não procedente
                    if (valor == 0) {
                        if (!acc.porMotivo[motivo]) {
                            acc.porMotivo[motivo] = 0;
                        }
                        acc.porMotivo[motivo] += 1;
                    }

                    if (!acc.porEmpresa[empresa]) {
                        acc.porEmpresa[empresa] = 0;
                    }
                    acc.porEmpresa[empresa] += valor;

                    if (!acc.porMes[mes]) {
                        acc.porMes[mes] = 0;
                    }
                    acc.porMes[mes] += valor;

                    return acc;
                }, {
                    porUsuario: {},
                    porMotivo: {},
                    porEmpresa: {},
                    porMes: {}
                });

                const usuario = Object.keys(resultado.porUsuario);
                const vlrUsuario = Object.values(resultado.porUsuario);

                const motivoNaoProcedente = Object.keys(resultado.porMotivo);
                const vlrMtivoNaoProcedente = Object.values(resultado.porMotivo);

                const porEmpresa = Object.keys(resultado.porEmpresa);
                const vlrPorEmpresa = Object.values(resultado.porEmpresa);

                const porMes = Object.keys(resultado.porMes);
                const vlrPorMes = Object.values(resultado.porMes);

                chartUsuarioBarX = carregaCharBarX(usuario, vlrUsuario, 'chart-usuario', chartUsuarioBarX);
                chartNaoProcedenteBarY = carregaCharBarY(motivoNaoProcedente, vlrMtivoNaoProcedente,
                    'chart-nao-procedente', chartNaoProcedenteBarY);
                chartPorEmpresaBarX = carregaCharBarX(porEmpresa, vlrPorEmpresa, 'chart-por-empresa',
                    chartPorEmpresaBarX);
                chartPorMesBarX = carregaCharBarX(porMes, vlrPorMes, 'chart-por-mes', chartPorMesBarX);


            });

            $(document).on('click', '#tabela-usuario tbody tr.dtrg-start', function() {
                const name = $(this).data('name');

                const scrollBody = $('.dt-scroll-body');
                const scrollTop = scrollBody.scrollTop();

                collapsedGroups[name] = !collapsedGroups[name];
                tableUsuario.draw(false);

                setTimeout(() => {
                    scrollBody.scrollTop(scrollTop);
                    tableUsuario.columns.adjust();
                }, 0);
            });
            $(document).on('click', '#tabela-nao-procedente tbody tr.dtrg-start', function() {
                const name = $(this).data('name');

                const scrollBody = $('.dt-scroll-body');
                const scrollTop = scrollBody.scrollTop();

                collapsedGroups[name] = !collapsedGroups[name];
                tableNaoProcedente.draw(false);

                setTimeout(() => {
                    scrollBody.scrollTop(scrollTop);
                    tableNaoProcedente.columns.adjust();
                }, 0);
            });

            function carregaCharBarY(labels, data, elementId, chartBarY) {

                const subbox = document.querySelector('.subbox');
                subbox.style.height = '600px';

                const subbox2 = document.querySelector('.subbox2');
                subbox2.style.height = '600px';

                const ctx = document.getElementById(elementId).getContext('2d');

                if (chartBarY) {
                    chartBarY.destroy(); // Destrói o gráfico anterior
                }
                chartBarY = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Motivos',
                            data: data,
                            backgroundColor: 'rgba(220, 53, 69, 0.6)',
                            borderColor: 'rgba(135, 45, 54, 1)',
                            borderWidth: 1
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
                                anchor: 'center',
                                align: 'end',
                                color: '#000000',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {

                                    if (elementId == 'chart-motivo') {
                                        return 'R$' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { // <--- ALTERADO de yAxes para y
                                beginAtZero: true
                            },
                            x: {
                                // beginAtZero: true,
                                // max: Math.max(...data)+100,
                                // ticks: {
                                //     stepSize: 1 // <--- Espaçamento fixo no eixo X
                                // }
                            },
                        }
                    },
                    plugins: [ChartDataLabels]
                });
                return chartBarY;
            }

            function carregaCharBarX(labels, data, elementId, chartBar) {
                const ctx = document.getElementById(elementId).getContext('2d');

                if (chartBar) {
                    chartBar.destroy(); // Destrói o gráfico anterior
                }
                Chart.defaults.font.size = 11;
                chartBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: 'rgba(220, 53, 69, 0.6)',
                            borderColor: 'rgba(135, 45, 54, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {

                        plugins: {
                            legend: {
                                display: false,
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;

                                        return 'R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });

                                    }
                                }
                            },
                            datalabels: {
                                anchor: 'center',
                                align: 'top',
                                color: '#000000',
                                font: {
                                    weight: 'bold',
                                    size: 10
                                },
                                formatter: function(value, context) {
                                    return 'R$' + value.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        },

                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + new Intl.NumberFormat('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }).format(value);
                                    }
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
                return chartBar;
            }

            function initTableSecond(table, tableId, data, collapsedGroups) {
                if (!$.fn.dataTable.isDataTable('#' + tableId)) {
                    table = $('#' + tableId).DataTable({
                        language: {
                            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                        },
                        data: data,
                        scrollY: '300px',
                        rowGroup: gerarRowGroup({
                            dataSrc: collapsedGroups,
                            valorSomado: 'VL_CLASSIFICACAO',
                            colSpan: 4
                        }),
                        columns: [{
                                data: 'NM_USUARIO',
                                title: 'Usuario'
                            },
                            {
                                data: 'NR_LAUDO',
                                title: 'Laudo'
                            },
                            {
                                data: 'DT_LAUDO',
                                title: 'Data'
                            },
                            {
                                data: 'DSMOTIVO',
                                title: 'Motivo'
                            },
                            {
                                data: 'VL_CLASSIFICACAO',
                                title: 'Valor',
                            }
                        ],
                        paging: false
                    });
                } else {
                    // Atualiza se já existe
                    table.clear().rows.add(data).draw();
                }
                return table;
            }

            // Filtro por Região
            document.getElementById("submit-seach").addEventListener("click", function() {
                $('#tabela-garantia').DataTable().destroy();
                initTablePrimary();

            });

        });
    </script>
@stop
