@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="info-box">
                    <x-loading-card />
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="far fa-dot-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pneus</span>
                        <span class="info-box-number pneusTotal"></span>
                    </div>
                </div>
            </div>

            @hasrole('admin|supervisor|gerente unidade|gerente comercial')
                <div class="col-md-3 col-sm-4 col-xs-6">
                    <div class="info-box">
                        <x-loading-card />
                        <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Valor</span>
                            <span class="info-box-number" id="valorTotal"></span>
                        </div>
                    </div>
                </div>
            @endhasrole

            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="info-box">
                    <x-loading-card />
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expedicionado</span>
                        <span class="info-box-number" id="expedicionado"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="info-box">
                    <x-loading-card />
                    <span class="info-box-icon" style="background-color: #d6d6d6;"><i class="fas fa-door-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Embarque</span>
                        <span class="info-box-number" id="embarque"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <x-loading-card />
                    <div class="card-header ui-sortable-handle">
                        <h3 class="card-title card-title-chart">Pneus Gerente</h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <button href="#cliente-chart" class="btn btn-xs nav-link" data-toggle="tab">Cliente</button>
                                </li>
                                <li class="nav-item">
                                    <button href="#gerente-chart" class="btn btn-xs nav-link active ml-1" data-toggle="tab">Gerente</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body" style="height: 300px">
                        <div class="tab-content p-0">
                            <div class="tab-pane active" id="gerente-chart">
                                <div class="d-flex">
                                    <p class="d-flex flex-column">
                                        <span class="text-bold text-lg pneusTotal"></span>
                                        <span>Pneus Sem Faturar</span>
                                    </p>
                                    <p class="ml-auto d-flex flex-column text-right calc-percentual">
                                    </p>
                                </div>
                                {{-- Legenda fixa --}}
                                <div class="mb-2 text-center" id="legend-container-gerente"></div>

                                <div style="overflow-x: auto;">
                                    <div class="position-relative mb-4" style="height: 150px;">
                                        <canvas id="chartPneusGerente"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="cliente-chart">
                                <p class="d-flex flex-column">
                                    <span class="text-bold text-lg pneusTotalCliente"></span>
                                    <span>Pneus Sem Faturar</span>
                                </p>
                                {{-- Legenda fixa --}}
                                <div class="mb-2 text-center" id="legend-container-cliente"></div>

                                <div style="overflow-x: auto;">
                                    <div id="chart-container-cliente" style="height: 180px;">
                                        <canvas id="chartPneusCliente"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <x-loading-card />
                    <div class="card-header ui-sortable-handle">
                        <h3 class="card-title">Pneus Mês</h3>
                    </div>
                    <div class="card-body" style="height: 300px">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg pneusTotal"></span>
                                <span>Pneus Sem Faturar</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right calc-percentual">
                            </p>
                        </div>
                        <div class="position-relative mb-4">
                            <canvas id="chartPneusMesAno" style="width: 100%; height: 150px;"></canvas>
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
                                    <label class="small">Empresa</label>
                                    <select name="cd_empresa" id="cd_empresa" class="form-control form-control-sm"
                                        style="width: 100%;">
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
                                    <label class="small">Dt Emissão</label>
                                    <input type="text" class="form-control form-control-sm" id="daterange"
                                        placeholder="Data Emissão">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="small">Pedido Palm</label>
                                    <input type="number" class="form-control form-control-sm" id="pedido_palm"
                                        placeholder="Pedido Palm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="small">Pedido</label>
                                    <input type="number" class="form-control form-control-sm" id="pedido"
                                        placeholder="Pedido">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small">Grupo Item</label>
                                    <select name="grupo_item" id="grupo_item" class="form-control form-control-sm"
                                        style="width: 100%;">
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
                                    <label class="small">Vendedor</label>
                                    <input type="text" class="form-control form-control-sm" id="nm_vendedor"
                                        placeholder="Nome Vendedor">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="small">Cliente</label>
                                    <input type="text" class="form-control form-control-sm" id="nm_cliente"
                                        placeholder="Nome Cliente">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="small">Região</label>
                                    <select name="cd_regiaocomercial[]" class="form-control form-control-sm"
                                        id="cd_regiaocomercial" style="width: 100%;" multiple>
                                        @foreach ($regiao as $r)
                                            <option value="{{ $r->CD_REGIAOCOMERCIAL }}">
                                                {{ $r->DS_REGIAOCOMERCIAL }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="small">Status Embarque</label>
                                    <select name="st_embarque" id="st_embarque" class="form-control form-control-sm"
                                        style="width: 100%;">
                                        <option value="0">Todos</option>
                                        <option value="1">Com Embarque</option>
                                        <option value="2">Sem Embarque</option>
                                    </select>
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
                        <span class="badge badge-danger periodo"></span>
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
            var fimData = moment().subtract(0, 'days').format('DD.MM.YYYY');
            var dados;
            var table;

            window.routes = {
                getPneusProduzidosSemFaturar: "{{ route('get-pneus-produzidos-sem-faturar') }}",
                getPneusProduzidosSemFaturarDetails: "{{ route('get-pneus-produzidos-sem-faturar-details') }}",
                languageDataTable: "{{ asset('vendor/datatables/pt-br.json') }}"
            };

            $('#grupo_item').select2({
                placeholder: 'Selecione o grupo',
                theme: 'bootstrap4',
            });
            $('#cd_regiaocomercial').select2({
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
                st_embarque: $('#st_embarque').val()
            };

            initTablePneus(dados);

            $('#search').click(function() {
                $('#produzidosTable').DataTable().destroy();

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
                    st_embarque: $('#st_embarque').val()

                };

                initTablePneus(dados);
            });

            $(document).on('click', '.btn-detalhes', function() {

                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var tableId = 'pedido-' + row.data().NR_COLETA + '-' + row.data().EXPEDICIONADO + '-' + row
                    .data().NR_EMBARQUE;

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

            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.addEventListener('click', function() {

                    const texto = this.textContent.trim();

                    document.querySelector('.card-title-chart').textContent = 'Pneus ' + texto;

                });
            });

            function initTablePneus(dados) {
                table = $('#produzidosTable').DataTable({
                    pageLength: 50,
                    "language": {
                        url: window.routes.languageDataTable,
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
                        url: window.routes.getPneusProduzidosSemFaturar,
                        data: {
                            data: dados
                        },
                        beforeSend: function() {
                            $(".loading-card").removeClass('invisible');
                        },
                        dataSrc: function(json) {
                            $(".loading-card").addClass('invisible');
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
                            className: "text-center",
                        },
                        {
                            "data": "NR_EMBARQUE",
                            title: "Embarque"
                        },
                        {
                            "data": "NR_COLETA",
                            title: "Pedido"
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
                            title: "Expedicionado",
                            className: "text-center",
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
                        let embarqueSim = 0;
                        let embarqueNao = 0;

                        data.forEach(function(item) {
                            QtdPneus += Number(item.PNEUS);
                            valorTotal += parseFloat(item.VALOR.replace(/\./g, '').replace(',',
                                '.'));
                            if (item.EXPEDICIONADO == 'SIM') {
                                expedicionadoSim += Number(item.PNEUS);
                            } else {
                                expedicionadoNao += Number(item.PNEUS);
                            }
                            if (item.ST_EMBARQUE != 'SEM EMBARQUE') {
                                embarqueSim += Number(item.PNEUS);
                            } else {
                                embarqueNao += Number(item.PNEUS);
                            }
                        });

                        const totalPneus = this.api()
                            .column(5, {
                                search: 'applied'
                            })
                            .data()
                            .sum()


                        $(this.api().column(5).footer()).html(
                            "Total: " + totalPneus.toLocaleString('pt-BR')
                        );


                        $('.pneusTotal').html(QtdPneus);
                        $('#valorTotal').html('R$ ' + valorTotal.toFixed(2).replace('.', ',').replace(
                            /\B(?=(\d{3})+(?!\d))/g, '.'));
                        $('#expedicionado').html('Sim: ' + expedicionadoSim + ' | Não: ' +
                            expedicionadoNao);
                        $('#embarque').html('Sim: ' + embarqueSim + ' | Não: ' + embarqueNao);

                    },

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
                const vlrGerente = {};

                const acumuladorClientes = {};
                const acumuladorNomeCliente = {};
                const qtdClientes = {};
                const vlrClientes = {};

                data.forEach(({
                    MES_ANO,
                    NM_GERENTE,
                    CD_PESSOA,
                    NM_PESSOA,
                    PNEUS,
                    VALOR
                }) => {
                    const qtde = Number(PNEUS);
                    const valor = parseFloat(VALOR.replace(/\./g, '').replace(',', '.'));

                    acumuladorMeses[MES_ANO] ??= {};
                    acumuladorMeses[MES_ANO][NM_GERENTE] ??= 0;
                    acumuladorGerentes[NM_GERENTE] ??= 0;
                    acumuladorClientes[CD_PESSOA] ??= 0;
                    acumuladorNomeCliente[CD_PESSOA] ??= NM_PESSOA;

                    qtdMes[MES_ANO] ??= 0;
                    qtdGerente[NM_GERENTE] ??= 0;
                    vlrGerente[NM_GERENTE] ??= 0;

                    acumuladorMeses[MES_ANO][NM_GERENTE] += qtde;
                    qtdMes[MES_ANO] += qtde;
                    qtdGerente[NM_GERENTE] += qtde;
                    vlrGerente[NM_GERENTE] += valor;

                    qtdClientes[CD_PESSOA] ??= 0;
                    vlrClientes[CD_PESSOA] ??= 0;
                    qtdClientes[CD_PESSOA] += qtde;
                    vlrClientes[CD_PESSOA] += valor;

                });

                const meses = Object.keys(acumuladorMeses);
                const NomeGerentes = Object.keys(acumuladorGerentes);                

                const qtdPneusMes = Object.values(qtdMes);
                const qtdPneusGerente = Object.values(qtdGerente);
                const vlrPneusGerente = Object.values(vlrGerente);

                // Transformo o objeto de clientes em um array para ordenar pelo valor total 
                const clientesArray = Object.keys(vlrClientes).map(cliente => ({
                    idcliente: cliente,
                    nomeCliente: acumuladorNomeCliente[cliente].split(' ')[0], // Pego apenas o primeiro nome para exibir no gráfico
                    valor: vlrClientes[cliente],
                    quantidade: qtdClientes[cliente]
                }));

                clientesArray.sort((a, b) => b.valor - a.valor);

                const labelsClientes = clientesArray.map(c => c.nomeCliente);
                const qtdPneusCliente = clientesArray.map(c => c.quantidade);
                const vlrPneusCliente = clientesArray.map(c => c.valor);

                


                //VERIFICO SE TEVE AUMENTO NO ULTIMO MES
                const [penultimoMes, ultimoMes, MesAtual] = qtdPneusMes.slice(-3);

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
                        label: "Quantidade",
                        type: 'bar',
                        data: qtdPneusGerente,
                        backgroundColor: coresGerentes,
                        borderColor: coresGerentes.map(cor => cor.replace('0.5', '1')),
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Valor (R$)',
                        data: vlrPneusGerente,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ];

                // Gráfico de clientes
                const datasetsClientes = [{
                        label: "Quantidade",
                        type: 'bar',
                        data: qtdPneusCliente,
                        backgroundColor: 'rgba(60, 145, 230, 0.5)',
                        borderColor: 'rgba(60, 145, 230, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Valor (R$)',
                        data: vlrPneusCliente,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ];

                // Renderiza o gráfico de gerentes
                renderChartJsDualAxis(
                    NomeGerentes,
                    datasetsGerente,
                    'chartPneusGerente',
                    'Meses',
                    'legend-container-gerente'
                );

                // Renderiza o gráfico de Clientes
                renderChartJsDualAxis(
                    labelsClientes,
                    datasetsClientes,
                    'chartPneusCliente',
                    'Clientes',
                    'legend-container-cliente'
                );

            }

            const charts = {};

            function renderChartJsDualAxis(labels, datasets, chartId, labelChart, legendContainerId) {

                const larguraPorItem = 80; // px por barra
                const total = labels.length;

                const larguraTotal = total * larguraPorItem;

                const ctx = document.getElementById(chartId).getContext('2d');
                if (chartId === 'chartPneusCliente') {
                    ctx.canvas.parentElement.style.width = larguraTotal + 'px';
                }


                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                charts[chartId] = new Chart(ctx, {
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        maintainAspectRatio: false,
                        resposive: true,
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
                                display: false,
                                position: 'top'
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

                                    // Se for o dataset de VALOR
                                    if (context.dataset.label.includes('Valor')) {
                                        return value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }

                                    // Se for quantidade
                                    return value;
                                    s
                                }
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y: {
                                type: 'linear',
                                position: 'left',
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });

                gerarLegenda(charts[chartId], legendContainerId);

            }

            function renderChartJs(labels, datasets, chartId, typeChart, labelChart) {

                const ctx = document.getElementById(chartId).getContext('2d');

                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                charts[chartId] = new Chart(ctx, {
                    type: typeChart,
                    data: {
                        labels: labels,
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

            function gerarLegenda(chart, containerId) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                chart.data.datasets.forEach((dataset, index) => {

                    const item = document.createElement('span');
                    item.style.marginRight = '10px';
                    item.style.cursor = 'pointer';
                    item.style.display = 'inline-flex';
                    item.style.alignItems = 'center';
                    item.style.padding = '4px 8px';
                    item.style.borderRadius = '6px';
                    item.style.background = '#f4f6f9';

                    const colorBox = document.createElement('span');
                    colorBox.style.width = '12px';
                    colorBox.style.height = '12px';
                    colorBox.style.background = dataset.borderColor;
                    colorBox.style.marginRight = '5px';
                    colorBox.style.borderRadius = '2px';

                    const label = document.createElement('span');
                    label.textContent = dataset.label;

                    item.appendChild(colorBox);
                    item.appendChild(label);


                    item.addEventListener('click', () => {
                        const meta = chart.getDatasetMeta(index);

                        // alterna visibilidade
                        meta.hidden = meta.hidden === null ? !chart.data.datasets[index].hidden :
                            null;

                        chart.update();

                        // efeito visual (apagado quando desativado)
                        item.style.opacity = meta.hidden ? '0.3' : '1';
                    });

                    container.appendChild(item);
                });
            }



        });
    </script>
@stop
