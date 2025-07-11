@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="card card-outline card-danger collapsed-card">
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
                    <div class="col-md-4">
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
                                <input type="text" class="form-control float-right" id="daterange"  placeholder="Filtrar por Datas">
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
                <div class="card card-outline card-danger flex-fill">
                    <div class="card-header">
                        <h3 class="card-title">Producao X Executor</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="executorEtapas" class="table compact table-bordered table-striped"
                                style="font-size: 12px;">
                            </table>
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

            let inicioData = null;
            let fimData = null;

            $('#daterange').daterangepicker({
                locale: {
                    format: 'DD.MM.YYYY'
                },
                autoUpdateInput: false
            });

            // Evento ao aplicar o filtro de data
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format(
                    'DD.MM.YYYY'));
                inicioData = picker.startDate.format('DD.MM.YYYY') + ' 00:00';
                fimData = picker.endDate.format('DD.MM.YYYY') + ' 23:59';
            });

            // Evento ao cancelar o filtro
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                inicioData = null;
                fimData = null;
            });

            // Variável global da tabela
            let tabela;

            // Função para iniciar/reiniciar a DataTable
            function initTable(cdempresa, dtinicio, dtfim) {
                if ($.fn.DataTable.isDataTable('#executorEtapas')) {
                    $('#executorEtapas').DataTable().destroy();
                }

                tabela = $('#executorEtapas').DataTable({
                    processing: true,
                    serverSide: true,
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
                            data: 'DT_FIM',
                            name: 'DT_FIM',
                            title: 'Finalização'
                        },
                        {
                            data: 'IDEMPRESA',
                            name: 'IDEMPRESA',
                            title: 'Empresa'
                        },
                        {
                            data: 'NM_EXECUTOR',
                            name: 'NM_EXECUTOR',
                            title: 'Executor'
                        },
                        {
                            data: 'EXAME_INI',
                            name: 'EXAME_INI',
                            title: 'Exame Inicial'
                        },
                        {
                            data: 'RASPA',
                            name: 'RASPA',
                            title: 'Raspagem'
                        },
                        {
                            data: 'PREPBANDA',
                            name: 'PREPBANDA',
                            title: 'Prep. Banda'
                        },
                        {
                            data: 'ESCAREACAO',
                            name: 'ESCAREACAO',
                            title: 'Escareação'
                        },
                        {
                            data: 'LIMPEZAMANCHAO',
                            name: 'LIMPEZAMANCHAO',
                            title: 'Limpeza Manchão'
                        },
                        {
                            data: 'APLICOLA',
                            name: 'APLICOLA',
                            title: 'Cola'
                        },
                        {
                            data: 'EMBORRACHAMENTO',
                            name: 'EMBORRACHAMENTO',
                            title: 'Emborrachamento'
                        },
                        {
                            data: 'VULCANIZACAO',
                            name: 'VULCANIZACAO',
                            title: 'Vulcanização'
                        },
                        {
                            data: 'EXAMEFINAL',
                            name: 'EXAMEFINAL',
                            title: 'Exame Final'
                        }
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
                const empresa = $('#filtro-empresa').val();

                if (!empresa) {
                    alert('Selecione uma empresa!');
                    return;
                }

                if (!inicioData || !fimData) {
                    alert('Selecione o período!');
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
