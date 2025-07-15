@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="card card-outline card-dark collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtros:</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body" style="display: none">
                <div class="row">
                    <div class="col-md-2">
                        <select id="filtro-empresa" class="form-control">
                            <option value="" disabled selected>Filtrar por Empresa</option>
                            <option value="1">Cambe</option>
                            <option value="3">Osvaldo Cruz</option>
                            <option value="5">Ponta Grossa</option>
                            <option value="6">Catanduva</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control float-right" id="daterange"
                                    placeholder="Filtrar por Datas">
                            </div>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block float-right" id="submit-seach">Buscar
                            novos</button>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="row d-flex" style="align-items: stretch;">
            <div class="col-12 col-lg-8 mb-3 d-flex flex-column">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="exame-inicial" data-toggle="pill" href="#exame-inicial"
                                    role="tab" aria-controls="exame-inicial" aria-selected="true">Exame Inicial</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="raspa" data-toggle="pill" href="#raspa" role="tab"
                                    aria-controls="raspa" aria-selected="false">Raspa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prep-banda" data-toggle="pill" href="#prep-banda" role="tab"
                                    aria-controls="prep-banda" aria-selected="false">Prep. Banda</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            <div class="tab-pane fade show active" id="exame-inicial" role="tabpanel"
                                aria-labelledby="exame-inicial">
                                <div class="table-responsive">
                                    <table id="executorEtapas" class="table compact table-bordered table-striped"
                                        style="font-size: 12px;">
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="raspa" role="tabpanel" aria-labelledby="raspa">
                                <!-- Conteúdo da aba Raspa -->
                            </div>
                            <div class="tab-pane fade" id="prep-banda" role="tabpanel" aria-labelledby="prep-banda">
                                <!-- Conteúdo da aba Prep. Banda -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mb-3 d-flex flex-column">
                <div class="card card-outline card-danger flex-fill mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Resumo por Setor</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="setorResumo" class="table compact table-bordered table-striped"
                                style="font-size: 12px;">
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card card-outline card-danger flex-fill">
                    <div class="card-header">
                        <h3 class="card-title">Resumo por Executor</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="executorResumo" class="table compact table-bordered table-striped"
                                style="font-size: 12px;">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            let inicioData = moment().format('DD.MM.YYYY 00:00');
            let fimData = moment().format('DD.MM.YYYY 23:59');
            // Variável global da tabela
            let tabela;

            const datasSelecionadas = initDateRangePicker();

            initTable(1, inicioData, fimData);

            // Função para iniciar/reiniciar a DataTable
            function initTable(cdempresa, dtinicio, dtfim) {
                if ($.fn.DataTable.isDataTable('#executorEtapas')) {
                    $('#executorEtapas').DataTable().destroy();
                }

                tabela = $('#executorEtapas').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 100,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
                    },
                    ajax: {
                        url: '{{ route('get-producao-executor-etapas') }}',
                        data: {
                            cd_empresa: cdempresa,
                            dt_inicio: dtinicio,
                            dt_fim: dtfim
                        },
                        type: 'GET'
                    },
                    columns: [{
                            data: 'IDEMPRESA',
                            name: 'IDEMPRESA',
                            title: 'Emp',
                            "width": '1%',
                        },                        
                        {
                            data: 'NM_EXECUTOR',
                            name: 'NM_EXECUTOR',                            
                            title: 'Executor',
                            "width": '10%',
                        },
                        {
                            data: 'EXAME_INI',
                            name: 'EXAME_INI',
                            title: 'Exame Inicial',
                            "width": '3%',
                        },
                         {
                            data: 'EXAME_INI',
                            name: 'EXAME_INI',
                            title: 'Meta',
                            "width": '3%',
                        },
                        {
                            data: 'DT_FIM',
                            name: 'DT_FIM',
                            "width": '2%',
                            title: 'Finalização'
                        },
                        {
                            data: 'RASPA',
                            name: 'RASPA',
                            title: 'Raspagem',
                            visible: false
                        },
                        {
                            data: 'PREPBANDA',
                            name: 'PREPBANDA',
                            title: 'Prep. Banda',
                            visible: false

                        },
                        {
                            data: 'ESCAREACAO',
                            name: 'ESCAREACAO',
                            title: 'Escareação',
                            visible: false
                        },
                        {
                            data: 'LIMPEZAMANCHAO',
                            name: 'LIMPEZAMANCHAO',
                            title: 'Limpeza Manchão',
                            visible: false
                        },
                        {
                            data: 'APLICOLA',
                            name: 'APLICOLA',
                            title: 'Cola',
                            visible: false

                        },
                        {
                            data: 'EMBORRACHAMENTO',
                            name: 'EMBORRACHAMENTO',
                            title: 'Emborrachamento',
                            visible: false
                        },
                        {
                            data: 'VULCANIZACAO',
                            name: 'VULCANIZACAO',
                            title: 'Vulcanização',
                            visible: false
                        },
                        {
                            data: 'EXAMEFINAL',
                            name: 'EXAMEFINAL',
                            title: 'Exame Final',
                            visible: false
                        }
                    ],
                    columnDefs: [{
                        targets: 4,
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    }],
                    order: [
                        [4, 'asc']
                    ],

                    drawCallback: function(settings) {
                        let dadosPaginaAtual = this.api().rows({
                            page: 'current'
                        }).data().toArray();
                        gerarResumo(dadosPaginaAtual);
                    }
                });
            }

            //Busca os dados ao clicar no botão "Buscar novos"
            $('#submit-seach').on('click', function() {

                const inicioData = datasSelecionadas.getInicio() + ' 00:00';
                const fimData = datasSelecionadas.getFim() + ' 23:59';

                const empresa = $('#filtro-empresa').val();

                if (!empresa) {
                    msgToastr('Selecione uma empresa para continuar.',
                        'warning');
                    return;
                }

                if (datasSelecionadas.getInicio() == 0 || datasSelecionadas.getFim() == 0) {
                    msgToastr('Selecione o período para continuar.',
                        'warning');
                    return;
                }

                initTable(empresa, inicioData, fimData);
            });

            // Inicializa a tabela Resumo Executor
            let tabelaResumoExecutor = $('#executorResumo').DataTable({
                columns: [{
                        title: 'Executor',
                        data: 'executor'
                    },
                    {
                        title: 'Total Produzido',
                        data: 'total',
                        render: $.fn.dataTable.render.number('.', ',', 0)
                    }
                ],
                searching: false,
                paging: false,
                info: false,
                ordering: false
            });

            // Inicializa a tabela Resumo Setor
            let tabelaResumoSetor = $('#setorResumo').DataTable({
                columns: [{
                        title: 'Setor',
                        data: 'setor'
                    },
                    {
                        title: 'Total Produção',
                        data: 'total',
                        render: $.fn.dataTable.render.number('.', ',', 0)
                    }
                ],
                searching: false,
                paging: false,
                info: false,
                ordering: false
            });

            function gerarResumo(data) {
                let resumoExecutor = {};
                let resumoSetor = {
                    'Exame Inicial': 0,
                    'Raspagem': 0,
                    'Prep. Banda': 0,
                    'Escareação': 0,
                    'Limpeza Manchão': 0,
                    'Cola': 0,
                    'Emborrachamento': 0,
                    'Vulcanização': 0,
                    'Exame Final': 0
                };

                data.forEach(item => {
                    // Total produção por executor (soma de todas as etapas)
                    let totalProducao = Number(item.EXAME_INI) + Number(item.RASPA) + Number(item
                        .PREPBANDA) + Number(item.ESCAREACAO) + Number(item.LIMPEZAMANCHAO) + Number(
                        item
                        .APLICOLA) + Number(item.EMBORRACHAMENTO) + Number(item.VULCANIZACAO) + Number(
                        item.EXAMEFINAL);

                    resumoExecutor[item.NM_EXECUTOR] = (resumoExecutor[item.NM_EXECUTOR] || 0) +
                        totalProducao;

                    // Soma produção por setor (coluna)
                    resumoSetor['Exame Inicial'] += Number(item.EXAME_INI);
                    resumoSetor['Raspagem'] += Number(item.RASPA);
                    resumoSetor['Prep. Banda'] += Number(item.PREPBANDA);
                    resumoSetor['Escareação'] += Number(item.ESCAREACAO);
                    resumoSetor['Limpeza Manchão'] += Number(item.LIMPEZAMANCHAO);
                    resumoSetor['Cola'] += Number(item.APLICOLA);
                    resumoSetor['Emborrachamento'] += Number(item.EMBORRACHAMENTO);
                    resumoSetor['Vulcanização'] += Number(item.VULCANIZACAO);
                    resumoSetor['Exame Final'] += Number(item.EXAMEFINAL);
                });

                // Transforma em array para popular DataTables
                let resumoExecutorArray = Object.entries(resumoExecutor).map(([executor, total]) => ({
                    executor,
                    total
                }));

                let resumoSetorArray = Object.entries(resumoSetor).map(([setor, total]) => ({
                    setor,
                    total
                }));

                // Atualiza as tabelas resumo
                tabelaResumoExecutor.clear().rows.add(resumoExecutorArray).draw();
                tabelaResumoSetor.clear().rows.add(resumoSetorArray).draw();
            }
        });
    </script>
@stop
