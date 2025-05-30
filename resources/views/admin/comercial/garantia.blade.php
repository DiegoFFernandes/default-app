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
                        <span class="info-box-number vl-laudo-pago">760</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">41,410</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">New Members</span>
                        <span class="info-box-number">2,000</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
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
            <div class="card-body" style="display: none">
                <div class="row">
                    <div class="col-md-8">
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
                        <button type="button" class="btn btn-primary btn-block float-right" id="submit-seach">Buscar
                            novos</button>
                    </div>
                    <div class="col-md-4">
                        <input id="filtro-empresa" type="text" class="form-control" placeholder="Filtrar por Empresa">
                    </div>
                    <div class="col-md-4">
                        <input id="filtro-usuario" type="text" class="form-control" placeholder="Filtrar por Faturista">
                    </div>
                    <div class="col-md-4">
                        <input id="filtro-dia" type="text" class="form-control" placeholder="Filtrar por Dia">
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- Small boxes (Stat box) -->
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
                        <h3 class="card-title">Analise</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;">
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
                        <h3 class="card-title">Emissão por Usúario</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-y: auto;">
                            <table id="tabela-usuario" class="compact table" style="font-size: 12px;"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
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
            var chartBarY = null;

            tableAnalise = $('#tabela-garantia').DataTable({
                pageLength: 100,
                scrollY: '300px',
                // autoWidth: false,                
                scrollCollapse: true,
                paging: false,
                searching: true,
                ajax: {
                    url: "{{ route('get-analise-garantia') }}",
                    type: "GET",
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

                        carregaCharBarY(motivo, vlrMotivo, 'chart-motivo', chartBarY);


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

                const naoProcedente = json;

                if (!$.fn.dataTable.isDataTable('#tabela-usuario')) {
                    tableUsuario = $('#tabela-usuario').DataTable({
                        data: naoProcedente,
                        pageLength: 100,
                        scrollY: '300px',
                        rowGroup: gerarRowGroup({
                            dataSrc: 'NM_USUARIO',
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
                    tableUsuario.clear().rows.add(naoProcedente).draw();
                }
            });

            // Coloque fora do xhr.dt
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

            function carregaCharBarY(labels, data, elementId, chartBarY) {
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
                            backgroundColor: 'rgba(23, 162, 184, 0.6)',
                            borderColor: 'rgba(21, 134, 152, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                color: '#158698',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    return value;
                                }
                            }
                        },
                        scales: {
                            y: { // <--- ALTERADO de yAxes para y
                                beginAtZero: true
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

        });
    </script>
@stop
