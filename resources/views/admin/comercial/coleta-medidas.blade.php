@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Hoje</span>
                        <span id="coleta-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Hoje</span>
                        <span id="media-hoje" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Ontem</span>
                        <span id="coleta-ontem" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Ontem</span>
                        <span id="media-ontem" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Coleta Mês</span>
                        <span id="coleta-mes" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Média Mês</span>
                        <span id="media-mes" class="info-box-number">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros:</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-2 mb-2">
                        <div class="form-group mb-0">
                            <label for="filtro-empresa">Empresa:</label>
                            <select id="filtro-empresa" class="form-control mt-1">
                                <option value="1" selected>Cambe</option>
                                <option value="3">Osvaldo Cruz</option>
                                <option value="5">Ponta Grossa</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <div class="form-group mb-0">
                            <label for="filtro-medida">Medida:</label>
                            <select id="filtro-medidas" class="form-control mt-1">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabColetas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-medidasHoje" data-toggle="pill"
                                    href="#painel-medidasHoje" role="tab" aria-controls="painel-medidasHoje"
                                    aria-selected="true">
                                    Coleta por Medida Hoje
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-medidasOntem" data-toggle="pill" href="#painel-medidasOntem"
                                    role="tab" aria-controls="painel-medidasOntem" aria-selected="false">
                                    Coleta por Medida Ontem
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-medidasMes" data-toggle="pill" href="#painel-medidasMes"
                                    role="tab" aria-controls="painel-medidasMes" aria-selected="false">
                                    Coleta por Medida Mês
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="tabContentColetas">
                            <div class="tab-pane fade show active" id="painel-medidasHoje" role="tabpanel"
                                aria-labelledby="tab-medidasHoje">
                                <span class="badge badge-danger badge-empresa">Empresa:</span>
                                <span class="badge badge-danger badge-periodo badge-hoje">Periodo:</span>
                                <div class="table-responsive">
                                    <table id="coletasMedidasHoje"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Qtde</th>
                                                <th>Valor Médio</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-medidasOntem" role="tabpanel"
                                aria-labelledby="tab-medidasOntem">
                                <span class="badge badge-danger badge-empresa">Empresa:</span>
                                <span class="badge badge-danger badge-periodo badge-ontem">Periodo:</span>
                                <div class="table-responsive">
                                    <table id="coletasMedidasOntem"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Qtde</th>
                                                <th>Valor Médio</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-medidasMes" role="tabpanel"
                                aria-labelledby="tab-medidasMes">
                                <div class="table-responsive">
                                    <span class="badge badge-danger badge-empresa">Empresa:</span>
                                    <span class="badge badge-danger badge-periodo badge-mes">Periodo:</span>
                                    <table id="coletasMedidasMes"
                                        class="table compact table-font-small table-striped table-bordered nowrap"
                                        style="width:100%; font-size: 12px;">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th>Descrição</th>
                                                <th>Qtde</th>
                                                <th>Valor Médio</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        .info-box-text {
            font-size: 14px;
        }

        .info-box-number {
            font-weight: bold;
            font-size: 18px;
        }

        .nav-tabs .nav-link {
            font-size: 15px;
            padding: 7px 15px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            let cd_empresa = $('#filtro-empresa').val();
            let nomeEmpresa = $('#filtro-empresa option:selected').text();

            //datas das tabelas
            let hoje = moment().format('DD.MM.YYYY');
            let ontem = moment().subtract(1, 'days').format('DD.MM.YYYY');
            let primeiroDiaMes = moment().startOf('month').format('DD.MM.YYYY');
            let ultimoDiaMes = moment().endOf('month').format('DD.MM.YYYY');

            // badges das abas
            $('.badge-empresa').text(`Empresa: ${nomeEmpresa}`);
            $('.badge-hoje').text(`Período: ${hoje}`);
            $('.badge-ontem').text(`Período: ${ontem}`);
            $('.badge-mes').text(`Período: ${primeiroDiaMes} - ${ultimoDiaMes}`);

            function coletaMedida(dt_inicio, dt_fim, idTabela, cd_empresa) {
                if ($.fn.DataTable.isDataTable('#' + idTabela)) {
                    $('#' + idTabela).DataTable().destroy();
                }

                $('#' + idTabela).DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                    processing: true,
                    serverSide: false,
                    scrollX: true,
                    paging: true,
                    searching: true,
                    ajax: {
                        method: 'GET',
                        url: '{{ route('get-coleta-medidas') }}',
                        data: {
                            dt_inicio: dt_inicio,
                            dt_fim: dt_fim,
                            cd_empresa: cd_empresa
                        }
                    },
                    columns: [

                        {
                            data: 'DSMEDIDAPNEU',
                            name: 'DSMEDIDAPNEU'
                        },
                        {
                            data: 'QTD',
                            name: 'QTD'
                        },
                        {
                            data: 'VALOR_MEDIO',
                            name: 'VALOR_MEDIO',
                            render: function(data, type, row) {
                                let valor = parseFloat(data);
                                return isNaN(valor) ? '-' : valor.toLocaleString('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                });
                            }
                        }
                    ],
                    drawCallback: function(settings) {
                        var api = this.api();

                        let totalColeta = 0;
                        let somaValorPonderado = 0;

                        api.rows({search: 'applied'}).data().each(function(d){
                            let quantidade = parseInt(d.QTD) || 0;
                            let valorMedio = parseFloat(d.VALOR_MEDIO) || 0;

                            totalColeta += quantidade;
                            somaValorPonderado += quantidade * valorMedio;
                        });

                        const mediaGeral = totalColeta > 0 ? somaValorPonderado / totalColeta : 0;

                        //atualiza os info-box
                        if (idTabela === 'coletasMedidasHoje') {
                            $('#coleta-hoje').text(totalColeta);
                            $('#media-hoje').text(mediaGeral.toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                            }));
                        } else if (idTabela === 'coletasMedidasOntem') {
                            $('#coleta-ontem').text(totalColeta);
                            $('#media-ontem').text(mediaGeral.toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                            }));
                        } else if (idTabela === 'coletasMedidasMes') {
                            $('#coleta-mes').text(totalColeta);
                            $('#media-mes').text(mediaGeral.toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                            }));
                        }
                    }
                });
            }

            coletaMedida(hoje, hoje, 'coletasMedidasHoje', cd_empresa);
            coletaMedida(ontem, ontem, 'coletasMedidasOntem', cd_empresa);
            coletaMedida(primeiroDiaMes, ultimoDiaMes, 'coletasMedidasMes', cd_empresa);

            //ajusta a tabela para o tamanho da aba
            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href');
                $(target).find('table.dataTable').DataTable().columns.adjust().draw();
            });

            $('#submit-seach').on('click', function() {
                cd_empresa = $('#filtro-empresa').val();
                nomeEmpresa = $('#filtro-empresa option:selected').text();

                $('.badge-empresa').text(`Empresa: ${nomeEmpresa}`);
                $('.badge-hoje').text(`Período: ${hoje}`);
                $('.badge-ontem').text(`Período: ${ontem}`);
                $('.badge-mes').text(`Período: ${primeiroDiaMes} - ${ultimoDiaMes}`);

                coletaMedida(hoje, hoje, 'coletasMedidasHoje', cd_empresa);
                coletaMedida(ontem, ontem, 'coletasMedidasOntem', cd_empresa);
                coletaMedida(primeiroDiaMes, ultimoDiaMes, 'coletasMedidasMes', cd_empresa);
            });
        });
    </script>
@stop
