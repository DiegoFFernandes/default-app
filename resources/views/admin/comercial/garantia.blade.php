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
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="col-md-12 p-0">
                            <div style="height: 500px; overflow-y: auto;">
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
                <div class="card card-primary">
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
@section('css')
    <style>
        .tr-bg {
            background-color: #c9c9c9;
            font-weight: bold;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {

            display: none;
        }
    </style>

@stop
@section('js')
    <script>
        $(document).ready(function() {
            var dadosFiltrados = [];
            var tableMotivo = null;
            var tableAnalise = null;
            var collapsedGroups = {};

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
                        return json;
                    }
                },
                rowGroup: {
                    dataSrc: 'DSMOTIVO',
                    startRender: function(rows, group) {
                        var collapsed = !!collapsedGroups[group];

                        rows.nodes().each(function(r) {
                            r.style.display = 'none';
                            if (collapsed) {
                                r.style.display = '';
                            }
                        });

                        // Add category name to the <tr>. NOTE: Hardcoded colspan
                        var tCol = tableAnalise.columns().visible().count();
                        // Soma dos valores da coluna VL_CLASSIFICACAO
                        var total = rows
                            .data()
                            .pluck('VL_CLASSIFICACAO')
                            .reduce(function(a, b) {
                                // Converte para número e soma
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        console.log(total);

                        return $('<tr/>')
                            .append('<td colspan="' + tCol +
                                '" class="tr-bg"><i class="fas fa-chevron-down mr-1"></i>' + group +
                                ' (' + rows.count() + ')</td>')
                            .attr('data-name', group)
                            .toggleClass('collapsed', collapsed);
                    }
                },
                columns: [{
                        data: 'NM_PESSOA',
                        width: '60px', // ou o valor que desejar                        
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
                order: [
                    [3, 'asc']
                ],
            });

            $('#tabela-garantia tbody').on('click', 'tr.dtrg-start', function() {
                var name = $(this).data('name');

                var scrollBody = $('.dt-scroll-body');
                var scrollTop = scrollBody.scrollTop();

                collapsedGroups[name] = !collapsedGroups[name];
                tableAnalise.draw(false);

                // Após o redraw, restaura o scroll
                setTimeout(function() {
                    scrollBody.scrollTop(scrollTop);
                    tableAnalise.columns.adjust(); // força o ajuste correto da largura
                }, 0);

            });


        });
    </script>
@stop
