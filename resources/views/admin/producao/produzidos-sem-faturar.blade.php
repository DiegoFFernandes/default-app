@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pneus</span>
                        <span class="info-box-number pneusTotal"></span>
                    </div>
                </div>
            </div>

            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Valor</span>
                            <span class="info-box-number" id="valorTotal"></span>
                        </div>
                    </div>
                </div>
            @endhasrole

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expedicionado</span>
                        <span class="info-box-number" id="expedicionado"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Pneus Gerente</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg pneusTotal"></span>
                                <span>Pneus Sem Faturar</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right calc-percentual">
                            </p>
                        </div>
                        <div class="position-relative mb-4">
                            <canvas id="chartPneusGerente" style="width: 100%; height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Pneus Mês</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg pneusTotal"></span>
                                <span>Pneus Sem Faturar</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right calc-percentual">
                            </p>
                        </div>
                        <div class="position-relative mb-4">
                            <canvas id="chartPneusMesAno" style="width: 100%; height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros:</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select name="cd_empresa" id="cd_empresa" class="form-control" style="width: 100%;">
                                        <option value="0" selected>Todas</option>
                                        @foreach ($empresa as $e)
                                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Dt Emissão</label>
                                    <input type="text" class="form-control" id="daterange" placeholder="Data Emissão">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido Palm</label>
                                    <input type="number" class="form-control" id="pedido_palm" placeholder="Pedido Palm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido</label>
                                    <input type="number" class="form-control" id="pedido" placeholder="Pedido">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grupo Item</label>
                                    <select name="grupo_item" id="grupo_item" class="form-control" style="width: 100%;">
                                        <option value="0">Todos</option>
                                        {{-- @foreach ($grupo as $g)
                                        <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}
                                        </option>
                                    @endforeach --}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Região</label>
                                    <select name="cd_regiaocomercial[]" class="form-control" id="cd_regiaocomercial"
                                        style="width: 100%;" multiple>
                                        @foreach ($regiao as $r)
                                            <option value="{{ $r->CD_REGIAOCOMERCIAL }}">
                                                {{ $r->DS_REGIAOCOMERCIAL }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vendedor</label>
                                    <input type="text" class="form-control" id="nm_vendedor"
                                        placeholder="Nome Vendedor">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" id="nm_cliente"
                                        placeholder="Nome Cliente">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-sm float-right mr-2"
                                        id="search">Filtrar</button>
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <table id="produzidosTable" class="table table-bordered table-font-small compact">
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align: right;"></th>
                                    @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                        <th colspan="4"></th>
                                    @else
                                        <th colspan="3"></th>
                                    @endhasrole
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
    <style>
        .col-actions {
            width: 1% !important;
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
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge badge-danger">{{ NM_PESSOA }}</span>
            <table class="table stripe row-border no-padding table-left" id="pedido-{{ NR_COLETA }}-{{ EXPEDICIONADO }}-{{ NR_EMBARQUE }}" style="width:80%">
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
            var fimData = 0;
            var dados;
            var table;

            $('#grupo_item').select2({
                placeholder: 'Selecione o grupo',
                theme: 'bootstrap4',
            });
            $('#cd_regiaocomercial').select2({
                theme: 'bootstrap4',
            });
            var template = Handlebars.compile($("#details-template").html());

            initTablePneus();

            $('#search').click(function() {
                $('#produzidosTable').DataTable().destroy();

                dados = {
                    cd_empresa: $('#cd_empresa').val(),
                    nm_cliente: $('#nm_cliente').val(),
                    nm_vendedor: $('#nm_vendedor').val(),
                    pedido_palm: $('#pedido_palm').val(),
                    pedido: $('#pedido').val(),
                    grupo_item: $('#grupo_item').val(),
                    cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                    dt_inicial: inicioData,
                    dt_final: fimData,
                    regiao: $('#cd_regiaocomercial').val()
                };

                initTablePneus(dados);
            });

            $(document).on('click', '.btn-detalhes', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                // console.log(tableId);
                var tableId = 'pedido-' + row.data().NR_COLETA;

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    // Open this row
                    row.child(template(row.data())).show();
                    initTable(tableId, row.data());
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

            function initTablePneus(dados) {
                table = $('#produzidosTable').DataTable({
                    pageLength: 50,
                    "language": {
                        url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    },
                    fixedHeader: true,
                    "scrollX": true,
                    layout: {
                        topStart: {
                            buttons: [{
                                    extend: "excelHtml5",
                                    title: 'Pneus Produzidos Sem Faturar',
                                },
                                {
                                    extend: "print",
                                    title: 'Pneus Produzidos Sem Faturar',
                                    customize: function(win) {
                                        $(win.document.body)
                                            .find("h1")
                                            .css("font-size", "12pt")
                                            .css("color", "#333");
                                    },
                                },
                            ],
                        },
                    },
                    ajax: {
                        url: "{{ route('get-pneus-produzidos-sem-faturar') }}",
                        data: {
                            data: dados
                        },
                        beforeSend: function() {
                            $("#loading").removeClass('invisible');
                        },
                        dataSrc: function(json) {
                            $("#loading").addClass('invisible');
                            carregaDados(json.datatables.data);

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
                        },
                        {
                            "data": "NR_EMBARQUE",
                            title: "Embarque"
                        },
                        {
                            "data": "NR_COLETA",
                            title: "Coleta"
                        },
                        {
                            "data": "NM_PESSOA",
                            title: "Cliente"
                        },
                        {
                            "data": "PNEUS",
                            title: "Pneus"
                        },
                        {
                            "data": "NM_VENDEDOR",
                            title: "Vendedor"
                        },
                        {
                            "data": "EXPEDICIONADO",
                            title: "Expedicionado",
                        },
                        {
                            "data": "DTFIM",
                            render: function(data) {
                                return moment(data).format('DD/MM/YYYY HH:mm');
                            },
                            title: "Data",
                            "visible": true
                        },
                        {
                            "data": "NM_GERENTE",
                            title: "Gerente",
                        },
                        @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                            {
                                "data": "VALOR",
                                title: "Valor"
                            },
                        @endhasrole
                    ],
                    "columnDefs": [{
                        "targets": 0,
                        "className": "text-center",
                    }],

                    footerCallback: function(row, data, start, end, display) {
                        let QtdPneus = 0;
                        let valorTotal = 0;
                        let expedicionadoSim = 0;
                        let expedicionadoNao = 0;

                        data.forEach(function(item) {
                            QtdPneus += Number(item.PNEUS);
                            valorTotal += parseFloat(item.VALOR.replace(/\./g, '').replace(',',
                                '.'));
                            if (item.EXPEDICIONADO == 'SIM') {
                                expedicionadoSim += Number(item.PNEUS);
                            } else {
                                expedicionadoNao += Number(item.PNEUS);
                            }

                        });

                        $(this.api().column(5).footer()).html('Total: ' + QtdPneus);

                        $('.pneusTotal').html(QtdPneus);
                        $('#valorTotal').html('R$ ' + valorTotal.toFixed(2).replace('.', ',').replace(
                            /\B(?=(\d{3})+(?!\d))/g, '.'));
                        $('#expedicionado').html('Sim: ' + expedicionadoSim + ' | Não: ' +
                            expedicionadoNao);

                    },

                });
            }

            function initTable(tableId, data) {
                var tableItemOrdem = $('#' + tableId + '-' + data.EXPEDICIONADO + '-' + data.NR_EMBARQUE)
                    .DataTable({
                        "language": {
                            url: "{{ asset('vendor/datatables/pt-br.json') }}",
                        },
                        sDom: 't',
                        paging: false,
                        searching: true,
                        ajax: {
                            "url": "{{ route('get-pneus-produzidos-sem-faturar-details') }}",
                            "method": "GET",
                            "data": {
                                'pedido': data.NR_COLETA,
                                'nr_embarque': data.NR_EMBARQUE,
                                'expedicionado': data.EXPEDICIONADO
                            }
                        },
                        columns: [{
                                data: "EXPEDICIONADO",
                                title: "Expedicionado"
                            },
                            {
                                data: "NRORDEMPRODUCAO",
                                title: "Nr Ordem"
                            },
                            {
                                data: "DS_ITEM",
                                title: "Descrição"
                            },
                            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                                {
                                    "data": "VALOR",
                                    title: "Valor"
                                },
                            @endhasrole {
                                data: "DTFIM",
                                title: "Data",
                                render: function(data) {
                                    return moment(data).format('DD/MM/YYYY HH:mm');
                                }
                            }
                        ]
                    });
            }

            function carregaDados(data, chartId) {
                const acumuladorMeses = {};
                const acumuladorGerentes = {};
                const qtdMes = {};
                const qtdGerente = {};

                data.forEach(({
                    MES_ANO,
                    NM_GERENTE,
                    PNEUS
                }) => {
                    const qtde = Number(PNEUS);

                    acumuladorMeses[MES_ANO] ??= {};
                    acumuladorMeses[MES_ANO][NM_GERENTE] ??= 0;
                    acumuladorGerentes[NM_GERENTE] ??= 0;

                    qtdMes[MES_ANO] ??= 0;
                    qtdGerente[NM_GERENTE] ??= 0;

                    acumuladorMeses[MES_ANO][NM_GERENTE] += qtde;
                    qtdMes[MES_ANO] += qtde;
                    qtdGerente[NM_GERENTE] += qtde;
                });

                const meses = Object.keys(acumuladorMeses);
                const NomeGerentes = Object.keys(acumuladorGerentes);

                const qtdPneusMes = Object.values(qtdMes);
                const qtdPneusGerente = Object.values(qtdGerente);

                //VERIFICO SE TEVE AUMENTO NO ULTIMO MES
                const [penultimoMes, ultimoMes] = qtdPneusMes.slice(-2);
                const percentual = ((ultimoMes - penultimoMes) / penultimoMes) * 100;

                const calcPercentual = $('.calc-percentual').html(`
                    <span class="${percentual >= 0 ? 'text-success' : 'text-danger'}">
                        <i class="fas fa-arrow-${percentual >= 0 ? 'up' : 'down'}"></i> ${percentual.toFixed(2)}%
                    </span>
                    <span class="text-muted">${percentual >= 0 ? 'Aumento do ultimo Mês' : 'Queda do ultimo Mês'}</span>
                `);


                const datasetsMeses = [{
                    data: qtdPneusMes,
                    backgroundColor: 'rgba(206,212,218)',
                    borderColor: 'rgba(206,212,218, 1)',
                    borderWidth: 1
                }];

                // Renderiza o gráfico de meses
                renderChartJs(
                    meses,
                    datasetsMeses,
                    'chartPneusMesAno',
                    'bar',
                    'Meses');


                const coresGerentes = [
                    'rgba(60, 145, 230, 0.5)',
                    'rgba(9, 188, 138, 0.5)',
                    'rgba(43, 65, 98, 0.5)'
                ];

                const datasetsGerente = [{
                    data: qtdPneusGerente,
                    backgroundColor: coresGerentes,
                    borderColor: coresGerentes.map(cor => cor.replace('0.5', '1')),
                    borderWidth: 1
                }];

                // Renderiza o gráfico de gerentes
                renderChartJs(
                    NomeGerentes,
                    datasetsGerente,
                    'chartPneusGerente',
                    'bar',
                    'Meses');
            }

            const charts = {};

            function renderChartJs(nomeMes, datasets, chartId, typeChart, labelChart) {

                const ctx = document.getElementById(chartId).getContext('2d');

                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                charts[chartId] = new Chart(ctx, {
                    type: typeChart,
                    data: {
                        labels: nomeMes,
                        datasets: datasets
                    },
                    options: {
                        maintainAspectRatio: false,

                        onClick: function(evt) {

                            const points = this.getElementsAtEventForMode(
                                evt,
                                'nearest', {
                                    intersect: true
                                },
                                true
                            );

                            if (!points.length) return;

                            const point = points[0];

                            const label = this.data.labels[point.index];
                            const value = this.data.datasets[point.datasetIndex].data[point.index];

                            onChartClick(label, value, chartId);
                        },

                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'center', // 'top', 'bottom', 'center'
                                color: '#000',
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
                            x: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            function onChartClick(label, value, chartId) {
                table.search(label).draw();
            }


        });
    </script>
@stop
