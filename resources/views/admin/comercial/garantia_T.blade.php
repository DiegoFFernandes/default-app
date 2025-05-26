@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">CPU Traffic</span>
                        <span class="info-box-number">
                            10
                            <small>%</small>
                        </span>
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
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Sales</span>
                        <span class="info-box-number">760</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
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
            <div class="col-md-8">
                <div class="card ">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-title">Usuários Laudos</h3>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div id="tabela-garantia"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-title">Motivos</h3>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div id="tabela-motivo"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            var dadosFiltrados = [];
            var tableMotivo = null;
            var tableAnalise = null;

            carregaDados();

            function carregaDados() {
                tableAnalise = new Tabulator("#tabela-garantia", {
                    ajaxURL: "{{ route('get-analise-garantia') }}",
                    // height: "400px",
                    layout: "fitColumns",
                    virtualDom: false,
                    renderVertical: "basic",

                    ajaxRequesting: function(url, params) {
                        // $("#loading").removeClass('invisible');
                    },
                    ajaxResponse: function(url, params, response) {
                        // Store the data in a global variable
                        dadosFiltrados = response;
                        // $("#loading").addClass('invisible');

                        // Atualiza a outra tabela
                        if (tableMotivo) {
                            tableMotivo.setData(dadosFiltrados);
                        }

                        return response;
                    },
                    // dataLoader: true,
                    // dataLoaderLoading: "<div class='text-center p-4'><i class='fas fa-spinner fa-spin fa-2x text-danger'></i></div>",
                    groupBy: row => `${row.NM_USUARIO}`,

                    groupStartOpen: function(group) {
                        return false; // inicia todos os grupos fechados
                    },

                    groupHeader: function(value, count, data) {

                        let total = data
                            .filter(d => d.NM_USUARIO === value)
                            .reduce((sum, d) => sum + parseFloat(d.VL_CLASSIFICACAO || 0), 0);

                        // Formata o total como moeda brasileira
                        let totalFormatado = total.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });

                        // <span style="display:inline-block; width: 30%; color: #333;">${ds_motivo}</span> 
                        return `
                        <div style="display:inline-block; width:95%;">                       
                            <span style="display:inline-block; width: 40%; color: #333;">${value}</span>                                                 
                            <span style="display:inline-block; width: 10%; color: #333; text-align: right;">${count} Laudo${count > 1 ? 's' : ''}</span>    
                            <span style="display:inline-block; width: 15%; color: #333; text-align: right;">${totalFormatado}</span>                          
                        </div>
                    `;
                    },

                    columns: [{
                            title: "Laudo",
                            field: "NR_LAUDO",
                            width: 70,

                        },
                        {
                            title: "Pessoa",
                            field: "NM_PESSOA",
                            width: 300,

                        },
                        {
                            title: "Data",
                            field: "DT_LAUDO",
                            width: 80,
                            formatter: function(cell) {
                                return moment(cell.getValue()).format('DD/MM/YYYY');
                            },

                        },
                        {
                            title: "Motivo",
                            field: "DSMOTIVO",
                            width: 300,

                        },
                        {
                            title: "Valor",
                            field: "VL_CLASSIFICACAO",
                            hozAlign: "center",
                            formatter: "money",
                            formatterParams: {
                                decimal: ",",
                                thousand: ".",
                                symbol: "R$ ",
                                precision: 2
                            }
                        }
                    ],
                });

                tableMotivo = new Tabulator("#tabela-motivo", {
                    layout: "fitColumns",
                    height: "400px",
                    groupBy: ["DSMOTIVO"],
                    renderVertical: "basic",
                    groupStartOpen: false,
                    dataLoader: true,
                    dataLoaderLoading: "<div class='text-center p-4'><i class='fas fa-spinner fa-spin fa-2x text-danger'></i></div>",
                    columns: [{
                            title: "Motivo",
                            field: "DSMOTIVO",

                        },
                        {
                            title: "Pessoa",
                            field: "NM_PESSOA",
                        },
                    ]
                });
            }
        });
    </script>
@stop
