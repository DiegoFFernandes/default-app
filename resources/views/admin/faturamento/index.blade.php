@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header border-1">
                        <h3 class="card-title">Faturistas</h3>
                        <div class="card-tools">
                            <a href="#" class="btn btn-tool btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="#" class="btn btn-tool btn-sm">
                                <i class="fas fa-bars"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-1">
                        <div id="tabela-faturista"></div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Faturamento</h3>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">8</span>
                                <span>Total de Faturistas</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success">
                                    <i class="fas fa-arrow-up"></i> 12.5%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas id="visitors-chart" height="200" width="761"
                                style="display: block; width: 761px; height: 200px;"
                                class="chartjs-render-monitor"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square text-primary"></i> This Week
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </section>
@stop

@section('js')
    <script>
        let chat;
        let dadosFiltrados = [];

        const table = new Tabulator("#tabela-faturista", {
            ajaxURL: "{{ route('get-analise-faturamento.index') }}",
            layout: "fitColumns",
            groupStartOpen: false,
            // Agrupamento por chave personalizada
            groupBy: function(row) {
                return `${row.USUARIO}||${row.DS_MES}`;
            },

            groupHeader: function(value, count, data) {
                // return `<strong>${value}</strong> <span class="ml-2">(${count} item${count > 1 ? 's' : ''})</span>`;               
                const [usuario, mes] = value.split("||");

                return `
                    <div style="display:inline-block; width:95%;">
                        <span style="display:inline-block; width: 3%; color: #333;"></span>
                        <span style="display:inline-block; width: 33%; color: #333;">${usuario}</span> 
                        <span style="display:inline-block; width: 25%; color: #333;">${mes}</span>                      
                        <span style="display:inline-block; width: 10%; color: #333; text-align: right;">${count} Nota${count > 1 ? 's' : ''}</span>                            
                    </div>
                    `;
            },
            ajaxResponse: function(url, params, response) {
                // Store the data in a global variable
                dadosFiltrados = response;
                return response;
            },
            columns: [{
                    title: "Emp",
                    field: "CD_EMPRESA",
                    hozAlign: "center",
                    width: 65
                },
                {
                    title: "Faturista",
                    field: "USUARIO",
                },
                {
                    title: "Dt Emissão",
                    field: "DT_EMISSAO",
                    hozAlign: "center",
                    sorter: "date",
                    formatter: function(cell) {
                        let data = cell.getValue();
                        if (!data) return "";
                        let [ano, mes, dia] = data.split("-");
                        return `${dia}/${mes}/${ano}`;
                    }
                },
                {
                    title: "Qtd",
                    field: "QTD_ITENS",
                }
            ]
        });



        const ctx = document.getElementById('visitors-chart').getContext('2d');
        const lineChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas',
                    data: [12, 19, 3, 5, 2, 10],
                    backgroundColor: 'rgba(60,141,188,0.2)',
                    borderColor: 'rgba(60,141,188,1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@stop
