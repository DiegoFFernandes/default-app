@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row mb-1">
            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-primary" id="card-pedidos">
                    <x-loading-card />
                    <div class="stat-title"><i class="fas fa-list-alt"></i> Pedidos</div>
                    <div class="stat-rows">
                        <div class="stat-row stat-row-clickable" id="i-finalizados">
                            <span class="stat-row-label">Finalizados</span>
                            <span class="stat-row-val finalizados">0</span>
                        </div>
                        <div class="stat-row stat-row-clickable" id="i-aguardando">
                            <span class="stat-row-label">Aguardando</span>
                            <span class="stat-row-val aguardando">0</span>
                        </div>
                        <div class="stat-row stat-row-clickable" id="i-producao">
                            <span class="stat-row-label">Em Produção</span>
                            <span class="stat-row-val producao">0</span>
                        </div>                       
                        <div class="stat-row stat-row-clickable" id="i-cancelados">
                            <span class="stat-row-label">Cancelados</span>
                            <span class="stat-row-val canceladas">0</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- card-bloqueados --}}
            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-danger" id="card-bloqueados">
                    <x-loading-card />
                    <div class="stat-title"><i class="fas fa-ban"></i> Bloqueados</div>
                    <div class="stat-rows">
                        <div class="stat-row stat-row-clickable" id="i-bloq-cadastro" data-motivo="CADASTRO">
                            <span class="stat-row-label">Cadastro</span>
                            <span class="stat-row-val">
                                <span class="bloq-cadastro">0</span>
                                <small class="stat-row-pct bloq-cadastro-pct">0%</small>
                            </span>
                        </div>
                        <div class="stat-row stat-row-clickable" id="i-bloq-comercial" data-motivo="COMERCIAL">
                            <span class="stat-row-label">Comercial</span>
                            <span class="stat-row-val">
                                <span class="bloq-comercial">0</span>
                                <small class="stat-row-pct bloq-comercial-pct">0%</small>
                            </span>
                        </div>
                        <div class="stat-row stat-row-clickable" id="i-bloq-financeiro" data-motivo="FINANCEIRO">
                            <span class="stat-row-label">Financeiro</span>
                            <span class="stat-row-val">
                                <span class="bloq-financeiro">0</span>
                                <small class="stat-row-pct bloq-financeiro-pct">0%</small>
                            </span>
                        </div>
                        <div class="stat-row stat-row-clickable" id="i-bloq-ambos" data-motivo="AMBOS">
                            <span class="stat-row-label">Comercial/Financeiro</span>
                            <span class="stat-row-val">
                                <span class="bloq-ambos">0</span>
                                <small class="stat-row-pct bloq-ambos-pct">0%</small>
                            </span>
                        </div>
                         <div class="stat-row stat-row-clickable" id="i-bloqueado-chao">
                            <span class="stat-row-label">Bloqueios no Chão de Fábrica</span>
                            <span class="stat-row-val bloqueado-chao">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4 col-md mb-2">
                <div class="stat-card stat-warning" id="card-analise">
                    <x-loading-card />
                    <div class="stat-title"><i class="fas fa-shield-alt"></i> Análise</div>
                    <div class="stat-rows">
                        <div class="stat-row stat-row-clickable" id="i-garantias">
                            <span class="stat-row-label">Garantia</span>
                            <span class="stat-row-val garantias">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-danger card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="acompanhamento" data-toggle="pill"
                                    href="#acompanhamento-pedido" role="tab" aria-controls="acompanhamento-pedido"
                                    aria-selected="true">
                                    <i class="fas fa-search mr-1"></i> Acompanhamento
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="bloqueio" data-toggle="pill" href="#bloqueio-pedido" role="tab"
                                    aria-controls="bloqueio-pedido" aria-selected="false">
                                    <i class="fas fa-ban mr-1"></i> Pedidos Bloqueados
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="acompanhamento-pedido" role="tabpanel"
                                aria-labelledby="custom-tabs-four-home-tab">
                                <div class="card collapsed-card mb-2">
                                    <div class="card-header pt-2 pb-2">
                                        <h3 class="card-title mt-2"><i class="fas fa-filter mr-1 text-muted"></i> Filtros
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Empresa</label>
                                                    <select name="cd_empresa" id="cd_empresa"
                                                        class="form-control form-control-sm" style="width: 100%;">
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
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="daterange" placeholder="Data Emissão">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Pedido Palm</label>
                                                    <input type="number" class="form-control form-control-sm"
                                                        id="pedido_palm" placeholder="Pedido Palm">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Pedido</label>
                                                    <input type="number" class="form-control form-control-sm"
                                                        id="pedido" placeholder="Pedido">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="small">Grupo Item</label>
                                                    <select name="grupo_item" id="grupo_item"
                                                        class="form-control form-control-sm" style="width: 100%;"
                                                        multiple>
                                                        <option value="0">Todos</option>
                                                        @foreach ($grupo as $g)
                                                            <option value="{{ $g->CD_GRUPO }}">{{ $g->DS_GRUPO }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="small">Região</label>
                                                    <select name="cd_regiaocomercial[]"
                                                        class="form-control form-control-sm" id="cd_regiaocomercial"
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
                                                    <label class="small">Vendedor</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nm_vendedor" placeholder="Nome Vendedor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="small">Cliente</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nm_cliente" placeholder="Nome Cliente">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="bold">Dados Pneus:</p>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Nr Fogo</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nr_fogo" placeholder="Nr Fogo">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Nr Serie</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nr_serie" placeholder="Nr Serie">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="small">Nr Dot</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nr_dot" placeholder="Nr Dot">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary btn-sm float-right mr-2"
                                                        id="searchRegiao">Filtrar</button>
                                                </div>
                                                <!-- /.row -->
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="mb-2">
                                        <small class="badge badge-danger badge-date"></small>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <x-loading-card />
                                            <table class="table stripe compact nowrap table-font-small"
                                                id="pedido-acompanhar">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="bloqueio-pedido" role="tabpanel">
                                <div class="card collapsed-card mb-4">
                                    <div class="card-header pt-2 pb-2">
                                        <h3 class="card-title mt-2"><i class="fas fa-filter mr-1 text-muted"></i> Filtros
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Supervisor</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nm_supervisor_bloq" placeholder="Nome Supervisor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Vendedor</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nm_vendedor_bloq" placeholder="Nome Vendedor">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Cliente</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="nm_cliente_bloq" placeholder="Nome Cliente">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-xs float-right mr-2"
                                                        id="searchRegiao">Filtrar</button>
                                                </div>
                                                <!-- /.row -->
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="table-responsive">
                                        <x-loading-card />
                                        <table class="table table-font-small stripe compact nowrap" id="bloqueio-pedidos">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
    </section>

    @include('admin.comercial.coleta-empresa.modals.modal-detalhe-pedido')
@stop

@section('css')
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,.09);
            border-left: 4px solid;
            border-radius: 4px;
            padding: 10px 12px;
            height: 100%;
            position: relative;
        }
        .stat-card .stat-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-card .stat-title i { font-size: 0.7rem; }
        .stat-card .stat-rows { margin-top: 1px; }
        .stat-card .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 0.85rem;
            padding: 4px 0;
            border-top: 1px solid rgba(0,0,0,.05);
        }
        .stat-card .stat-row-label { color: #6c757d; flex-shrink: 0; }
        .stat-card .stat-row-val {
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 58%;
        }
        .stat-card .stat-row.stat-row-clickable { cursor: pointer; }
        .stat-card .stat-row.stat-row-clickable:hover { background-color: rgba(0,0,0,.03); }
        .stat-card .stat-row-pct {
            font-weight: 400;
            color: #6c757d;
            margin-left: 4px;
        }

        /* Cores */
        .stat-primary { border-left-color: #007bff; }
        .stat-primary .stat-title i { color: #007bff; }

        .stat-danger { border-left-color: #dc3545; }
        .stat-danger .stat-title i { color: #dc3545; }

        .stat-warning { border-left-color: #e0a800; }
        .stat-warning .stat-title i { color: #c89100; }

        @media (max-width: 575px) {
            .stat-card { padding: 8px 10px; }
            .stat-card .stat-row { font-size: 0.78rem; }
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 10%;
        }

        .table-left {
            margin-left: 0 !important;
        }

        /* --- Header --- */
        table.dataTable thead tr {
            background-color: #444B53;
            color: #ffffff;
        }

        table.dataTable thead th {
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .3px;
            padding: 8px 10px;
            border-bottom: 2px solid #2d3238 !important;
            white-space: nowrap;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border table-left" id="pedido-{{ ID }}" style="width:80%; ">
                <thead>                    
                </thead>
            </table>
        @endverbatim
    </script>
    <script id="details-item-pedido" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ NRORDEM }} - {{DSSERVICO}}</span>
            <table class="table row-border" id="item-pedido-{{ ID }}" style="width:100%">
                <thead>
                    <tr>
                        <th>Etapa</th>
                        <th>Usúario</th>
                        <th>Entrada</th>
                        <th>Saida</th>
                        <th>Detalhes</th>
                        <th>Retrabalho</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script src="{{ asset('js/dashboard/coletaEmpresa/modal-detalhes-pedidos.js') }}?v={{ time() }}"></script>
    <script type="text/javascript">

        /*Faz o navbar já abrir collapse*/
        function collapseMenu() {
            $('[data-widget="pushmenu"]').PushMenu('collapse');
        }

        $(window).on('load resize', function() {
            setTimeout(() => {
                collapseMenu();
            }, 50);
        });
        /*Fim bloco abrir collapse*/

        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-br.json') }}",
            getItemPedidoAcompanhar: "{{ route('get-item-pedido-acompanhar') }}"
        }

        var template = Handlebars.compile($("#details-template").html());
        var details_item_pedido = Handlebars.compile($("#details-item-pedido").html());
        var regiao;
        var table;
        var tableBloqueio;
        var inicioData = 0;
        var fimData = 0;
        var pendingMotivoFilter = null;

        var dtInicio = moment().subtract(30, 'days').startOf('day').format('DD.MM.YYYY');
        var dtFim = moment().subtract(0, 'days').endOf('day').format('DD.MM.YYYY');

        var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

        $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);

        $('#grupo_item').select2({
            theme: 'bootstrap4',
            width: '100%',
        });
        $('#cd_regiaocomercial').select2({
            theme: 'bootstrap4',
        });

        $('#bloqueio').click(function() {
            //Rever essa rotina atualiza caso o usuario voltar para aba bloqueio
            $('#bloqueio-pedidos').DataTable().destroy();
            tableBloqueio = $('#bloqueio-pedidos').DataTable({
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                processing: '<i class="fas fa-circle-notch fa-spin mr-2 text-primary"></i>Carregando...',
                pagingType: "simple",
                processing: true,
                serverSide: false,
                pageLength: 100,
                ajax: {
                    url: "{{ route('get-bloqueio-pedidos') }}"
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        "width": "1%",
                        title: 'Emp'
                    },
                    {
                        data: 'QTD_COMPRA',
                        name: 'QTD_COMPRA',
                        title: 'Compras Ult. 90dd',
                        width: "1%",
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type !== 'display') {
                                return data;
                            }
                            var qtd = parseInt(data, 10) || 0;
                            var badge = (qtd > 3)
                                ? '<span class="badge badge-success">' + qtd  + ' - Recorrente</span>'
                                : '<span class="badge badge-secondary">' + qtd  + ' - Novo</span>';
                            return badge;
                        }
                    },
                    {
                        data: 'CLIENTE',
                        name: 'CLIENTE',
                        "width": "10%",
                        title: 'Cliente'
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        title: 'Emp',
                        visible: false,
                    },
                    {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        "width": "1%",
                        title: 'Pedido',
                        visible: false,
                    },
                    {
                        data: 'MOBILE',
                        name: 'MOBILE',
                        title: 'Palm',
                        "width": "1%",
                    },
                    {
                        data: 'DATA',
                        name: 'DATA',
                        title: 'Data'
                    },
                    {
                        data: 'MOTIVO',
                        name: 'MOTIVO',
                        title: 'Bloqueio'
                    },
                    {
                        data: 'ST_ATIVA',
                        name: 'ST_ATIVA',
                        title: 'Ativo'
                    },
                    {
                        data: 'ST_SCPC',
                        name: 'ST_SCPC',
                        title: 'Scpc'
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                        title: 'Status'
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                        title: 'Tipo Pedido'
                    }, {
                        data: 'VENDEDOR',
                        name: 'VENDEDOR',
                        title: 'Vendedor',
                        visible: false
                    },

                    {
                        data: 'NM_SUPERVISOR',
                        name: 'NM_SUPERVISOR',
                        title: 'Supervisor',
                        visible: false
                    }
                    

                ],
                columnDefs: [{
                        targets: [6],
                        render: function(data, type, row) {
                            if ((type === 'display' || type === 'filter') && data) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                            return data;
                        }
                    }, {
                        targets: [2],
                        className: 'text-truncate'
                    }

                ],
                createdRow: (row, data, dataIndex, cells) => {
                    $(cells[8]).css('background-color', data.status_cliente);
                    $(cells[9]).css('background-color', data.status_scpc);
                    $(cells[10]).css('background-color', data.status_pedido);
                },
                initComplete: function() {
                    if (pendingMotivoFilter) {
                        tableBloqueio.column(7).search(pendingMotivoFilter).draw();
                        pendingMotivoFilter = null;
                    }
                }
            });

        });

        $('#nm_supervisor_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(13).search(value).draw();
        });

        $('#nm_vendedor_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(12).search(value).draw();
        });

        $('#nm_cliente_bloq').on('keyup change', function() {
            let value = $(this).val();
            tableBloqueio.column(2).search(value).draw();
        });

        $('#btn-limpar').on('click', function() {
            $('#nm_supervisor_bloq').val('');
            $('#nm_vendedor_bloq').val('');
            $('#nm_cliente_bloq').val('');
            tableBloqueio.search('').columns().search('').draw();
        });


        $('#acompanhamento').click(function() {
            $('#pedido-acompanhar').DataTable().ajax.reload();
        });

        $('#title-page').text('Acompanhameto Pedido');

        $('#pedido-acompanhar').DataTable().destroy();

        table = initTableAcompanhar();

        // Pré-carrega os totais do card "Bloqueados" (Cadastro/Comercial/Financeiro/Ambos)
        // sem esperar o clique na aba "Pedidos Bloqueados"
        function loadBloqueioTotais() {
            $('#card-bloqueados .loading-card').removeClass('invisible');
            $.getJSON("{{ route('get-bloqueio-pedidos') }}")
                .done(function(json) {
                    var totais = {
                        CADASTRO: 0,
                        COMERCIAL: 0,
                        FINANCEIRO: 0,
                        AMBOS: 0
                    };
                    (json.data || []).forEach(function(row) {
                        var motivo = (row.MOTIVO || '').trim();
                        if (totais.hasOwnProperty(motivo)) {
                            totais[motivo]++;
                        }
                    });
                    $('.bloq-cadastro').text(totais.CADASTRO);
                    $('.bloq-comercial').text(totais.COMERCIAL);
                    $('.bloq-financeiro').text(totais.FINANCEIRO);
                    $('.bloq-ambos').text(totais.AMBOS);

                    // % que cada motivo representa sobre o total de bloqueios
                    // (não entra o "Bloqueios no Chão de Fábrica", que é outra origem de dado)
                    var totalMotivos = totais.CADASTRO + totais.COMERCIAL + totais.FINANCEIRO + totais.AMBOS;
                    var pct = function(valor) {
                        return totalMotivos > 0 ? Math.round((valor / totalMotivos) * 100) + '%' : '0%';
                    };
                    $('.bloq-cadastro-pct').text(pct(totais.CADASTRO));
                    $('.bloq-comercial-pct').text(pct(totais.COMERCIAL));
                    $('.bloq-financeiro-pct').text(pct(totais.FINANCEIRO));
                    $('.bloq-ambos-pct').text(pct(totais.AMBOS));
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao carregar os bloqueios',
                        text: 'Por favor, tente novamente.',
                    });
                })
                .always(function() {
                    $('#card-bloqueados .loading-card').addClass('invisible');
                });
        }
        loadBloqueioTotais();

        $('.stat-row[data-motivo]').on('click', function() {
            pendingMotivoFilter = $(this).data('motivo');
            $('#bloqueio').trigger('click');
        });

        // Loading dos cards "Pedidos" e "Análise", que são alimentados pela mesma tabela
        $('#pedido-acompanhar').on('processing.dt', function(e, settings, processing) {
            var loadingCards = $('#card-pedidos .loading-card, #card-analise .loading-card');
            if (processing) {
                loadingCards.removeClass('invisible');
            } else {
                loadingCards.addClass('invisible');
            }
        });

        // Atualiza o card "Pedidos" com os totais por status vindos do backend
        $('#pedido-acompanhar').on('xhr.dt', function(e, settings, json) {
            if (json && json.totais) {
                var t = json.totais;
                $('.finalizados').text(t.atendido ?? 0);
                $('.producao').text(t.em_producao ?? 0);
                $('.aguardando').text(t.aguardando ?? 0);
                $('.garantias').text(t.garantia ?? 0);
                $('.canceladas').text(t.cancelado ?? 0);
                $('.bloqueado-chao').text(t.bloqueado ?? 0);
            }
        });

        $('#pedido-acompanhar tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            // console.log(tableId);
            var tableId = 'pedido-' + row.data().ID;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');

                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');

            } else {
                // Open this row
                row.child(template(row.data())).show();
                initTable(tableId, row.data());
                // console.log(row.data());
                tr.addClass('shown');
                $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                tr.next().find('td').addClass('no-padding');
            }
        });

        $('#searchRegiao').click(function() {
            var dtInicio = datasSelecionadas.getInicio();
            var dtFim = datasSelecionadas.getFim();
            $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);

            Swal.fire({
                title: 'Carregando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            table.ajax.reload(function() {
                Swal.close();
            });
        });

        function initTableAcompanhar() {

            table = $('#pedido-acompanhar').DataTable({

                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                    processing: '<i class="fas fa-circle-notch fa-spin mr-2 text-primary"></i>Carregando...',
                },
                pagingType: "simple_numbers",
                processing: true,
                serverSide: true,
                searchDelay: 1500,
                pageLength: 50,
                scrollY: "400px",
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('get-pedido-acompanhar') }}",
                    data: function(d) {
                        d.cd_empresa = $('#cd_empresa').val();
                        d.nm_cliente = $('#nm_cliente').val();
                        d.nm_vendedor = $('#nm_vendedor').val();
                        d.pedido_palm = $('#pedido_palm').val();
                        d.pedido = $('#pedido').val();
                        d.grupo_item = $('#grupo_item').val();
                        d.regiao = $('#cd_regiaocomercial').val();
                        d.dt_inicial = datasSelecionadas.getInicio();
                        d.dt_final = datasSelecionadas.getFim();
                        d.nr_fogo = $('#nr_fogo').val();
                        d.nr_serie = $('#nr_serie').val();
                        d.nr_dot = $('#nr_dot').val();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao carregar os pedidos',
                            text: 'Por favor, tente novamente.',
                        });
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        "width": "1%",
                        title: 'Emp'
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        visible: false,
                        title: 'Emp'
                    },
                    {
                        data: 'ID',
                        name: 'ID',
                        visible: true,
                        title: 'Pedido'
                    },
                    {
                        data: 'IDPEDIDOMOVEL',
                        name: 'IDPEDIDOMOVEL',
                        "width": "10%",
                        title: 'Palm'
                    },
                    {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        "width": "20%",
                        title: 'Cliente'
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS',
                        "width": "1%",
                        title: 'Pneus'
                    },
                    {
                        data: 'QTD_FINALIZADAS',
                        name: 'QTD_FINALIZADAS',
                        className: 'dt-center',
                        "width": "1%",
                        title: 'Finalizados'
                    },
                    {
                        data: 'DTEMISSAO',
                        name: 'DTEMISSAO',
                        title: 'Dt Emissão',
                        className: 'dt-center',
                        render: function(data, type, row) {
                            if ((type === 'display' || type === 'filter') && data) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'DTENTREGAPED',
                        name: 'DTENTREGAPED',
                        title: 'Dt Entrega',
                        className: 'dt-center',
                        render: function(data, type, row) {
                            if ((type === 'display' || type === 'filter') && data) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'STPEDIDO',
                        name: 'STPEDIDO',
                        title: 'Status'
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                        title: 'Tipo Pedido'
                    }
                ],

                "order": [7, 'desc'],
            });

            // Remove os dots animados do processing (fallback JS além do CSS)
            $('#pedido-acompanhar').closest('.dt-container')
                .find('.dt-processing > div:last-child').remove();

            return table;
        }

        function initTable(tableId, data) {
            table_item_pedido = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ajax: {
                    method: "GET",
                    url: " {{ route('get-item-pedido-acompanhar') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        id: data.ID,
                        //dados dos pneus
                        nr_fogo: $('#nr_fogo').val(),
                        nr_serie: $('#nr_serie').val(),
                        nr_dot: $('#nr_dot').val(),
                    }
                },
                columns: [{
                        "className": 'details-item-control',
                        "orderable": false,
                        "searchable": false,
                        "data": 'null',
                        "defaultContent": '<span class="right mr-2"><i class="btn-detalhes fas fa-plus-circle"></i></span>',
                        "width": "1%",
                        title: '#'
                    },
                    {
                        data: 'NRSEQUENCIA',
                        name: 'NRSEQUENCIA',
                        "width": "2%",
                        title: 'Sq'
                    },
                    {
                        data: 'NRORDEM',
                        name: 'NRORDEM',
                        "width": "3%",
                        title: 'Ordem'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO',
                        "width": "20%",
                        title: 'Serviço'
                    },
                    {
                        data: 'NRFOGO',
                        name: 'NRFOGO',
                        title: 'Nr Fogo',
                        "width": "5%",
                    },
                    {
                        data: 'NRSERIE',
                        name: 'NRSERIE',
                        title: 'Nr Série',
                        "width": "5%",
                    },
                    {
                        data: 'NRDOT',
                        name: 'NRDOT',
                        title: 'Nr Dot',
                        "width": "5%",
                    },
                    {
                        data: 'VLUNITARIO',
                        name: 'VLUNITARIO',
                        width: "5%",
                        title: 'Vl Unit',
                        render: $.fn.dataTable.render.number('.', ',', 2)
                    }, {
                        data: 'STORDEM',
                        name: 'STORDEM',
                        title: 'Status Ordem',
                        "width": "2%",
                    },
                ]
            });
        }

        $('#pedido-acompanhar tbody').on('click', 'td.details-item-control', function() {
            var tr_item = $(this).closest('tr');
            var row_item = table_item_pedido.row(tr_item);
            var tableId = 'item-pedido-' + row_item.data().ID;
            if (row_item.child.isShown()) {
                // This row is already open - close it
                row_item.child.hide();
                tr_item.removeClass('shown');
                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Open this row
                row_item.child(details_item_pedido(row_item.data())).show();
                initTableItemDetalhesPedido(tableId, row_item.data());
                tr_item.addClass('shown');
                $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
                tr_item.next().find('td').addClass('no-padding');
            }
        });


        // Ativar popover após cada renderização
        $('#bloqueio-pedidos').on('draw.dt', function() {
            $('[data-toggle="popover"]').popover({
                trigger: 'focus', // ou 'click' se quiser persistente
                html: true,
                placement: 'top'
            });
        });

        $('#i-finalizados').click(function() {
            table.search('ATENDIDO').draw();
        });
        $('#i-producao').click(function() {
            table.search('EM PRODUCAO').draw();
        });
        $('#i-aguardando').click(function() {
            table.search('AGUARDANDO').draw();
        });
        $('#i-cancelados').click(function() {
            table.search('CANCELADO').draw();
        });
        $('#i-bloqueado-chao').click(function() {
            table.search('BLOQUEADO').draw();
        });
        $('#i-garantias').click(function() {
            table.search('BLOQ. GARANTIA').draw();
        });

        $('link[href*="custom_datatables"]').remove();
    </script>
@stop
