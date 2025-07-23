@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title">Filtros:</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-2 mb-2">
                        <div class="form-group mb-0">
                            <label for="filtro-empresa">Empresa:</label>
                            <select id="filtro-empresa" class="form-control mt-1">
                                @foreach ($empresas as $empresa)
                                    <option value="{{ $empresa->CD_EMPRESA }}">{{ $empresa->NM_EMPRESA }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-2">
                        <div class="form-group mb-0">
                            <label for="daterange">Data:</label>
                            <div class="input-group mt-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control" id="daterange" placeholder="Selecione a Data">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-2">
                        <div class="form-group mb-0">
                            <label for="filtro-executor">Executor:</label>
                            <select id="filtro-executor" class="form-control mt-1">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-block" id="submit-seach">Buscar</button>
                    </div>
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
                            <li class="nav-item">
                                <a class="nav-link" id="escareacao" data-toggle="pill" href="#escareacao" role="tab"
                                    aria-controls="escareacao" aria-selected="false">Escareação</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="limpeza-manchao" data-toggle="pill" href="#limpeza-manchao"
                                    role="tab" aria-controls="limpeza-manchao" aria-selected="false">Limp.
                                    Manchão</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cola" data-toggle="pill" href="#cola" role="tab"
                                    aria-controls="cola" aria-selected="false">Cola</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="emborrachamento" data-toggle="pill" href="#emborrachamento"
                                    role="tab" aria-controls="emborrachamento"
                                    aria-selected="false">Emborrachamento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="vulcanizacao" data-toggle="pill" href="#vulcanizacao"
                                    role="tab" aria-controls="vulcanizacao" aria-selected="false">Vulcanização</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="exame-final" data-toggle="pill" href="#exame-final"
                                    role="tab" aria-controls="exame-final" aria-selected="false">Exame Final</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <span class="badge badge-danger badge-empresa">Empresa:</span>
                        <span class="badge badge-danger badge-periodo">Periodo:</span>
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
                            <div class="tab-pane fade" id="escareacao" role="tabpanel" aria-labelledby="escareacao">
                                <!-- Conteúdo da aba Escareação -->
                            </div>
                            <div class="tab-pane fade" id="limpeza-manchao" role="tabpanel"
                                aria-labelledby="limpeza-manchao">
                                <!-- Conteúdo da aba Limpeza-manchão -->
                            </div>
                            <div class="tab-pane fade" id="emborrachamento" role="tabpanel"
                                aria-labelledby="emborrachamento">
                                <!-- Conteúdo da aba Emborrachamento -->
                            </div>
                            <div class="tab-pane fade" id="vulcanizacao" role="tabpanel" aria-labelledby="vulcanizacao">
                                <!-- Conteúdo da aba Vulcanização -->
                            </div>
                            <div class="tab-pane fade" id="exame-final" role="tabpanel" aria-labelledby="exame-final">
                                <!-- Conteúdo da aba Exame Final -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mb-3 d-flex flex-column">
                <div class="card card-danger card-outline card-outline-tabs flex-fill">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="resumo-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="resumo-setor-tab" data-toggle="pill" href="#resumo-setor"
                                    role="tab" aria-controls="resumo-setor" aria-selected="true">Resumo por Setor</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="resumo-executor-tab" data-toggle="pill" href="#resumo-executor"
                                    role="tab" aria-controls="resumo-executor" aria-selected="false">Resumo por
                                    Executor</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="resumo-tabs-content">
                            <div class="tab-pane fade show active" id="resumo-setor" role="tabpanel"
                                aria-labelledby="resumo-setor-tab">
                                <div class="table-responsive">
                                    <table id="setorResumo" class="table compact table-bordered table-striped"
                                        style="font-size: 12px;"></table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="resumo-executor" role="tabpanel"
                                aria-labelledby="resumo-executor-tab">
                                <div class="table-responsive">
                                    <table id="executorResumo" class="table compact table-bordered table-striped"
                                        style="font-size: 12px;"></table>
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
        .nav-tabs .nav-link {
            font-size: 15px;
            padding: 7px 7px;
            white-space: nowrap;
        }

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.6);
        }

        .btn-primary {
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.6);
        }

        /*Garante que os cards lateriais nao quebrem o layout */
        .col-lg-8.d-flex.flex-column>.card,
        .col-lg-4.d-flex.flex-column>.card {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        #custom-tabs-four-tabContent,
        #resumo-tabs-content {
            min-height: 350px;
        }
    </style>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            let inicioData = moment().format('DD.MM.YYYY 00:00');
            let fimData = moment().format('DD.MM.YYYY 23:59');
            let abaAtiva = 'exame-inicial';

            $('.badge-periodo').text(`Periodo: ${inicioData} - ${fimData}`);
            $('.badge-empresa').text('Empresa: Cambé')

            // Variável global da tabela
            let tabela;

            // Mapeia o id de cada aba
            const columnMapping = {
                'exame-inicial': [2, 3],
                'raspa': [4, 5],
                'prep-banda': [6, 7],
                'escareacao': [8, 9],
                'limpeza-manchao': [10, 11],
                'cola': [12, 13],
                'emborrachamento': [14, 15],
                'vulcanizacao': [16, 17],
                'exame-final': [18, 19]
            };

            const datasSelecionadas = initDateRangePicker();


            initTable(1, inicioData, fimData);

            // Função para iniciar/reiniciar a DataTable
            function initTable(cdempresa, dtinicio, dtfim) {
                if ($.fn.DataTable.isDataTable('#executorEtapas')) {
                    $('#executorEtapas').DataTable().destroy();
                }

                tabela = $('#executorEtapas').DataTable({
                    processing: true,
                    serverSide: false,
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
                        type: 'GET',
                        //filtro para não mostrar colunas com valores zerados
                        dataSrc: function(json) {
                            return json.data.filter(item => {
                                switch (abaAtiva) {
                                    case 'exame-inicial':
                                        return Number(item.EXAME_INI) > 0;
                                    case 'raspa':
                                        return Number(item.RASPA) > 0;
                                    case 'prep-banda':
                                        return Number(item.PREPBANDA) > 0;
                                    case 'escareacao':
                                        return Number(item.ESCAREACAO) > 0;
                                    case 'limpeza-manchao':
                                        return Number(item.LIMPEZAMANCHAO) > 0;
                                    case 'cola':
                                        return Number(item.APLICOLA) > 0;
                                    case 'emborrachamento':
                                        return Number(item.EMBORRACHAMENTO) > 0;
                                    case 'vulcanizacao':
                                        return Number(item.VULCANIZACAO) > 0;
                                    case 'exame-final':
                                        return Number(item.EXAMEFINAL) > 0;
                                    default:
                                        return true;
                                }
                            });
                        }
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
                            data: 'RASPA',
                            name: 'RASPA',
                            title: 'Raspagem',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'RASPA',
                            name: 'RASPA',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'PREPBANDA',
                            name: 'PREPBANDA',
                            title: 'Prep. Banda',
                            "width": '3%',
                            visible: false

                        },
                        {
                            data: 'PREPBANDA',
                            name: 'PREPBANDA',
                            title: 'Meta',
                            width: '3%',
                            visible: false

                        },
                        {
                            data: 'ESCAREACAO',
                            name: 'ESCAREACAO',
                            title: 'Escareação',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'ESCAREACAO',
                            name: 'ESCAREACAO',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'LIMPEZAMANCHAO',
                            name: 'LIMPEZAMANCHAO',
                            title: 'Limpeza Manchão',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'LIMPEZAMANCHAO',
                            name: 'LIMPEZAMANCHAO',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'APLICOLA',
                            name: 'APLICOLA',
                            title: 'Cola',
                            "width": '3%',
                            visible: false

                        },
                        {
                            data: 'APLICOLA',
                            name: 'APLICOLA',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'EMBORRACHAMENTO',
                            name: 'EMBORRACHAMENTO',
                            title: 'Emborrachamento',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'EMBORRACHAMENTO',
                            name: 'EMBORRACHAMENTO',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'VULCANIZACAO',
                            name: 'VULCANIZACAO',
                            title: 'Vulcanização',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'VULCANIZACAO',
                            name: 'VULCANIZACAO',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'EXAMEFINAL',
                            name: 'EXAMEFINAL',
                            title: 'Exame Final',
                            "width": '3%',
                            visible: false
                        },
                        {
                            data: 'EXAMEFINAL',
                            name: 'EXAMEFINAL',
                            title: 'Meta',
                            width: '3%',
                            visible: false
                        },
                        {
                            data: 'DT_FIM',
                            name: 'DT_FIM',
                            title: 'Finalização',
                            "width": '2%',
                        },
                    ],
                    columnDefs: [{
                        targets: 20,
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    }],
                    order: [
                        [20, 'asc']
                    ],
                    drawCallback: function(settings) {
                        let dadosPaginaAtual = this.api().rows({
                            page: 'current'
                        }).data().toArray();
                        gerarResumo(dadosPaginaAtual);
                    }
                });
            }

            // Mostrar colunas corretas ao trocar de aba
            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                const aba = $(e.target).attr('id');
                const colunas = columnMapping[aba];
                abaAtiva = aba;

                if (tabela && colunas) {

                    //esconde a tabela para não carregar a tabela com valores zerados
                    $('#executorEtapas').hide();

                    tabela.columns([2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19])
                        .visible(false);
                    tabela.columns(colunas).visible(true);

                    //recarrega a tabela sem valorezes redados
                    tabela.ajax.reload(() => {
                        $('#executorEtapas').fadeIn(100); //tempo de animação na volta da tabela
                    });
                }
            });

            //Busca os dados ao clicar no botão "Buscar novos"
            $('#submit-seach').on('click', function() {

                const inicioData = datasSelecionadas.getInicio() + ' 00:00';
                const fimData = datasSelecionadas.getFim() + ' 23:59';

                const empresa = $('#filtro-empresa').val(); // puxa o código da empresa
                const empresaNome = $('#filtro-empresa option:selected').text(); //puxa o nome da empresa

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

                // automatiza o  badge-danger da empresa 
                $('.badge-empresa').text(`Empresa: ${empresaNome}`);
                // automatiza o  badge-danger do periodo e formata o valor 
                $('.badge-periodo').text(
                    `Período: ${moment(datasSelecionadas.getInicio() + ' 00:00', "MM/DD/YYYY HH:mm").format("DD/MM/YYYY HH:mm")} - ${moment(datasSelecionadas.getFim() + ' 23:59', "MM/DD/YYYY HH:mm").format("DD/MM/YYYY HH:mm")}`
                    );


                initTable(empresa, inicioData, fimData);
            });

            // Inicializa a tabela Resumo Executor
            let tabelaResumoExecutor = $('#executorResumo').DataTable({
                columns: [{
                        title: 'Executor',
                        data: 'executor',
                        width: '50%'
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
                        data: 'setor',
                        width: '50%'
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
