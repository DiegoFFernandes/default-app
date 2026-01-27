@extends('layouts.master')
@section('title', 'Requisição Borracharia')

@section('content_header')
@stop

@section('content')
    <div class="content-fluid">
        <div class="col-md-8">
            <div class="card collapsed-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Filtros:</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i> <!-- Ícone "plus" porque está colapsado -->
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body pt-1 pb-1">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Gerente</label>
                                <select name="gerente" id="filtro-gerente" class="form-control form-control-sm"
                                    style="width: 100%">
                                    <option value="0">Todos Gerentes</option>
                                    @foreach ($gerentes as $g)
                                        <option value="{{ $g->cd_usuario }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Supervisor</label>
                                <input type="text" class="form-control form-control-sm" id="nm_supervisor"
                                    placeholder="Nome Supervisor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Vendedor</label>
                                <input type="text" class="form-control form-control-sm" id="nm_vendedor"
                                    placeholder="Nome Vendedor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Borracheiro</label>
                                <input type="text" class="form-control form-control-sm" id="nm_borracheiro"
                                    placeholder="Nome Borracheiro">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small">Cliente</label>
                                <select name='pessoa' class="form-control" id="pessoa" style="width: 100%">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label class="small">Período</label>
                                <input id="daterange" type="text" class="form-control form-control-sm"
                                    placeholder="Selecione o período">
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-xs float-right mr-2"
                                id="btn-limpar">Limpar</button>
                            <button type="button" class="btn btn-primary btn-xs float-right mr-2"
                                id="btn-filtrar">Filtrar</button>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-8 col-sm-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRequisicaoBorracharia" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-requisicao-borracharia-pagar" data-toggle="pill"
                                    href="#painel-requisicao-borracharia-pagar" role="tab"
                                    aria-controls="painel-requisicao-borracharia-pagar" aria-selected="true">
                                    Requisições
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-clientes-desabilitados" data-toggle="pill"
                                    href="#clientes-desabilitados" role="tab" aria-controls="clientes-desabilitados"
                                    aria-selected="false">
                                    Clientes Desabilitados
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="loading-card">
                            <div class="overlay loading-image-card"><i class="fas fa-3x fa-sync-alt fa-spin"></i>
                                <div class="text-bold pt-2"></div>
                            </div>
                        </div>
                        <div class="tab-content" id="tabContentRequisicaoBorracharia">
                            <div class="tab-pane fade show active" id="painel-requisicao-borracharia-pagar" role="tabpanel"
                                aria-labelledby="tab-requisicao-borracharia-pagar">
                                <div class="card-body p-2">
                                    <div class="mb-2">
                                        <small class="badge badge-danger badge-date"></small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8 d-none" id="div-tabela-requisicao-borracharia">
                                            <small class="badge badge-danger badge-date"></small>
                                            <div class="card-body pb-0 pt-0">
                                                <table
                                                    class="table table-responsive compact table-bordered table-font-small"
                                                    id="table-requisicao-borracharia">
                                                    <tr>
                                                        {{-- <th></th> --}}
                                                        <th>Emp.</th>
                                                        <th>Cliente</th>
                                                        <th>Qtde</th>
                                                        <th>Valor</th>
                                                        <th>Borracheiro</th>
                                                        <th>Vendedor</th>
                                                        <th>Supervisor</th>
                                                        <th>Gerente</th>
                                                    </tr>
                                                    <tfoot>
                                                        <tr>
                                                            {{-- <th></th> --}}
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-6 col-sm-6 col-md-6">
                                                    <div class="info-box">
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Total</span>
                                                            <span class="info-box-number">
                                                                <span id="total-itens"></span>
                                                                <small>Pneus</small>
                                                            </span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                    <!-- /.info-box -->
                                                </div>
                                                <div class="col-6 col-sm-6 col-md-6">
                                                    <div class="info-box">
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Valor</span>
                                                            <span class="info-box-number">
                                                                <small>R$</small>
                                                                <span id="total-valor"></span>
                                                            </span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                    <!-- /.info-box -->
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <div class="card pt-0 mb-3">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Resumo Gerente</h6>
                                                            <div class="card-tools m-0">
                                                                <button class="btn btn-xs btn-success"
                                                                    id="download-resumo-excel"><i
                                                                        class="fas fa-file-excel"></i></button>
                                                                <button class="btn btn-xs btn-danger"
                                                                    id="download-resumo-pdf"><i
                                                                        class="fas fa-file-pdf"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body pt-1">
                                                            <div id="accordionResumoGerente"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="clientes-desabilitados" role="tabpanel"
                                aria-labelledby="tab-clientes-desabilitados">
                                <div class="card-body p-2">
                                    <div class="col-md-8">
                                        <div class="card-header">
                                            <h6 class="card-title">Clientes Desabilitados</h6>
                                        </div>
                                        <div class="card-body pb-0">
                                            <table class="table table-bordered compact table-font-small table-responsive"
                                                id="clientes-desabilitados">

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Itens --}}
    <div class="modal modal-default fade" id="modal-table-detalhes-requisicao-borracharia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Requisição</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                <div class="form-group">
                                    <label for="pessoa" class="form-label small">Pessoa</label>
                                    <input id="" class="form-control form-control-sm pessoa" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="form-group">
                                    <label for="borracheiro" class="form-label small">Borracheiro</label>
                                    <input id="" class="form-control form-control-sm borracheiro" type="text"
                                        readonly>

                                </div>
                            </div>
                            {{-- <div class="col-3 col-md-3">
                                <div class="form-group">
                                    <label for="qtd-notas" class="form-label small">Qtde Notas</label>
                                    <input id="" class="form-control form-control-sm qtd-notas" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-3 col-md-3">
                                <div class="form-group">
                                    <label for="qtd-notas" class="form-label small">Qtde Itens</label>
                                    <input id="" class="form-control form-control-sm qtd-itens" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-3 col-md-3">
                                <div class="form-group">
                                    <label for="vlr-comissao-total" class="form-label small">Valor Total</label>
                                    <input id="" class="form-control form-control-sm vlr-comissao-total"
                                        type="text" readonly>

                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card mb-2 d-none" id="card-detalhes-requisicao-borracharia">
                        <div class="card-header">
                            <h3 class="card-title">Itens</h3>
                        </div>
                    </div>
                    <table class="table compact row-border" id="table-item-detalhes-requisicao-borracharia"
                        style="font-size:12px">
                    </table>
                    <div class="modal-footer">
                        <div class="d-flex">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('css')
    <style>
        table.dataTable {
            table-layout: fixed;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        .gerente-card {
            /* border-left: 4px solid #dc3545 !important; */
        }

        .indent-1 {
            padding-left: 10px !important;
        }

        .indent-2 {
            padding-left: 20px !important;
        }

        .indent-3 {
            padding-left: 30px !important;
        }

        .indent-4 {
            padding-left: 40px !important;
        }

        .supervisor-container {
            border-left: 3px solid #6c757d;
        }

        .supervisor-container::before {
            border-left: 3px solid #6c757d;
        }

        .vendedor-container {
            border-left: 2px solid #adb5bd;
            --padding-left: 6px;
        }

        .borracheiro-container {
            border-left: 2px solid #dee2e6;
            --padding-left: 6px;
        }

        .detalhe-pessoa-container {
            border-left: 1px solid #e9ecef;
            padding: 0 10px 0 10px;
        }

        .btn-list {
            background: transparent;
            text-align: left;
            border-radius: 0;
        }

        .btn-list:hover {
            background-color: rgba(0, 0, 0, .03);
        }

        .btn i.fa-chevron-down {
            transition: transform 0.2s ease;
            color: #6c757d;
        }

        .btn[aria-expanded="true"] i.fa-chevron-down {
            transform: rotate(180deg);
            color: #343a40;
        }
    </style>
@stop

@section('js')
    <script>
        var table;
        var tableId = 0;
        var table_item_pedido;
        let accordionResumoGerenteOriginal = [];
        let accordionResumoGerenteFiltrado = [];
        let datatables = [];

        var routes = {
            'searchPessoa': '{{ route('usuario.search-pessoa') }}'
        }

        var dtInicio = moment().subtract(1, 'month').startOf('month').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'month').endOf('month').format('DD.MM.YYYY');
        //datas selecionadas no date range picker
        var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

        let data = {
            nm_pessoa: $("#pessoa option:selected").text(),
            nm_vendedor: $('#nm_vendedor').val(),
            nm_supervisor: $('#nm_supervisor').val(),
            nm_borracheiro: $('#nm_borracheiro').val(),
            cd_gerente: $('#filtro-gerente').val(),
            session: true,
            dtFim: dtFim,
            dtInicio: dtInicio
        };

        //Carrega o select2 de pessoa
        initSelect2Pessoa('#pessoa', routes.searchPessoa);

        $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);

        initTableRequisicaoBorracharia(data);


        $(document).on('click', '.btn-view-requisicao-borracharia', function() {
            let cd_pessoa = $(this).data('cd-pessoa');
            let cd_borracheiro = $(this).data('cd-borracheiro');
            let nm_pessoa = $(this).data('nm-pessoa');
            let nm_borracheiro = $(this).data('nm-borracheiro');

            $('.pessoa').val(nm_pessoa);
            $('.borracheiro').val(nm_borracheiro);
            // $('.qtd-notas').val();
            // $('.qtd-itens').val();
            // $('.vlr-comissao-total').val();

            $('#modal-table-detalhes-requisicao-borracharia').modal('show');


            initTable('table-item-detalhes-requisicao-borracharia', cd_pessoa, cd_borracheiro);
        });

        $(document).on('click', '.btn-desabilita-cliente', function() {

            var cd_pessoa = $(this).data('cd-pessoa');

            var parms = {
                cd_pessoa: cd_pessoa,
                st_borracheiro: 'N',
                title: 'Desabilitar Cliente?',
                text: "Tem certeza que deseja desabilitar este cliente para pagar borracharia?",
                icon: 'warning',
                confirmButtonText: 'Sim, desabilitar!',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#d33'
            }

            DesabilitaHabilitaClienteBorracharia(parms);


        });

        $(document).on('click', '.btn-habilita-cliente', function() {

            var cd_pessoa = $(this).data('cd-pessoa');

            var parms = {
                cd_pessoa: cd_pessoa,
                st_borracheiro: 'S',
                title: 'Habilitar Cliente?',
                text: "Tem certeza que deseja habilitar este cliente para pagar borracharia?",
                icon: 'warning',
                confirmButtonText: 'Sim, habilitar!',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#d33'
            }

            DesabilitaHabilitaClienteBorracharia(parms);
        });

        $(document).on('click', '#download-resumo-excel', function() {

            exportarParaExcel(datatables, "pagamento-borracharia.xlsx", "Pagamento Borracharia");
        });

        $(document).on('click', '#download-resumo-pdf', function() {

            $.ajax({
                type: "GET",
                url: "{{ route('print-pdf-requisicao-borracharia') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                },
                dataType: "json",
                beforeSend: function() {
                    $('.loading-card').removeClass('invisible');
                },
                success: function(response) {
                    $('.loading-card').addClass('invisible');
                    window.location.href = response.url;
                }
            });
        });

        $(document).on('click', '#btn-filtrar', function() {

            dtFim = datasSelecionadas.getFim();
            dtInicio = datasSelecionadas.getInicio();

            $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);

            const data = {
                nm_pessoa: $("#pessoa option:selected").text(),
                nm_vendedor: $('#nm_vendedor').val(),
                nm_supervisor: $('#nm_supervisor').val(),
                nm_borracheiro: $('#nm_borracheiro').val(),
                cd_gerente: $('#filtro-gerente').val(),
                session: true,
                dtFim: dtFim,
                dtInicio: dtInicio
            };
            initTableRequisicaoBorracharia(data);
        });

        table.on('search.dt', function() {
            const termo = table.search().toLowerCase().trim();

            if (!Array.isArray(accordionResumoGerenteOriginal)) return;

            // Se busca vazia → volta tudo
            if (!termo) {
                initAccordion(accordionResumoGerenteOriginal, 'accordionResumoGerente');
                return;
            } else {
                accordionResumoGerenteFiltrado = accordionResumoGerenteOriginal
                    .map(gerente => {

                        const supervisoresFiltrados = gerente.supervisores
                            .map(supervisor => {

                                const borracheirosFiltrados = supervisor.borracheiros.filter(borracheiro =>
                                    (borracheiro.nome || '').toLowerCase().includes(termo)
                                );

                                // Se não sobrar borracheiro → remove supervisor
                                if (borracheirosFiltrados.length === 0) return null;

                                return {
                                    ...supervisor,
                                    borracheiros: borracheirosFiltrados
                                };
                            })
                            .filter(Boolean);

                        // Se não sobrar supervisor → remove gerente
                        if (supervisoresFiltrados.length === 0) return null;

                        return {
                            ...gerente,
                            supervisores: supervisoresFiltrados
                        };
                    })
                    .filter(Boolean);
            }

            initAccordion(accordionResumoGerenteFiltrado, 'accordionResumoGerente');
        });

        let openedAccordions = [];

        function DesabilitaHabilitaClienteBorracharia(parms) {
            Swal.fire({
                title: parms.title,
                text: parms.text,
                icon: parms.icon,
                showCancelButton: true,
                confirmButtonColor: parms.confirmButtonColor,
                cancelButtonColor: parms.cancelButtonColor,
                confirmButtonText: parms.confirmButtonText,
                cancelButtonText: parms.cancelButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('desabilita-cliente-borracharia') }}',
                        method: 'POST',
                        data: {
                            cd_pessoa: parms.cd_pessoa,
                            st_borracheiro: parms.st_borracheiro,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.title,
                                    text: response.message,
                                    timer: 1500
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.title,
                                    text: response.message,
                                    timer: 1500
                                });
                            }

                            openedAccordions = getOpenedAccordions('accordionResumoGerente');


                            table.ajax.reload(function() {
                                restoreAccordions(openedAccordions);
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: response.title,
                                text: response.message
                            });
                        }
                    });
                }
            });
        };

        function initTableRequisicaoBorracharia(data) {
            if (table) {
                table.clear().destroy();
            }

            table = $('#table-requisicao-borracharia').DataTable({
                orderCellsTop: true,
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 10,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: "{{ route('get-requisicao-borracharia') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        filtro: data
                    },
                    beforeSend: function() {
                        $('.loading-card').removeClass('invisible');
                    },
                    dataSrc: function(json) {

                        accordionResumoGerenteOriginal = json.accordionResumoGerente;
                        datatables = json.datatables.data;

                        initAccordion(accordionResumoGerenteOriginal, 'accordionResumoGerente');

                        return json.datatables.data;
                    }
                },
                columns: [
                    // {
                    //     data: "actions",
                    //     name: "actions",                    
                    //     className: 'text-center text-nowrap pl-1'
                    // },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        className: 'text-center',
                        title: 'Emp.'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        title: 'Cliente',

                    },
                    {
                        data: 'QTD_ITEM',
                        name: 'QTD_ITEM',
                        title: 'Qtde',
                        className: 'text-center',
                        render: $.fn.dataTable.render.number('.', ',', 0)
                    },
                    {
                        data: 'VL_COMISSAO',
                        name: 'VL_COMISSAO',
                        className: 'text-center',
                        title: 'Valor'
                    },
                    {
                        data: 'NM_BORRACHEIRO',
                        name: 'NM_BORRACHEIRO',
                        title: 'Borracheiro',

                    },
                    {
                        data: 'NM_VENDEDOR',
                        name: 'NM_VENDEDOR',
                        title: 'Vendedor',

                    },
                    {
                        data: 'NM_SUPERVISOR',
                        name: 'NM_SUPERVISOR',
                        title: 'Supervisor',

                    }, {
                        data: 'gerente_comercial',
                        name: 'gerente_comercial',
                        title: 'Gerente',
                    }
                ],
                order: [2, 'asc'],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Soma apenas os registros filtrados (search aplicado)
                    var totalItens = api
                        .column(2, {
                            search: 'applied'
                        })
                        .data()
                        .sum();

                    var totalValor = api
                        .column(3, {
                            search: 'applied'
                        })
                        .data()
                        .sum();

                    $('#total-itens').html(
                        totalItens.toLocaleString('pt-BR')
                    );

                    $('#total-valor').html(
                        totalValor.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    $(api.column(2).footer()).html(
                        totalItens.toLocaleString('pt-BR')
                    );

                    $(api.column(3).footer()).html(
                        totalValor.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    $('.loading-card').addClass('invisible');
                }
            });
            return table;
        }

        function initTable(tableId, cd_pessoa, cd_borracheiro) {

            $('#' + tableId).DataTable().clear().destroy();

            table_item_pedido = $('#' + tableId).DataTable({
                processing: false,
                serverSide: false,
                pagingType: "simple",
                pageLength: 50,
                scrollY: "400px",
                scrollCollapse: true,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: '{{ route('get-detalhes-requisicao-borracharia') }}',
                    data: {
                        cd_pessoa: cd_pessoa,
                        cd_borracheiro: cd_borracheiro
                    }
                },
                columns: [{
                        data: 'NR_NOTAFISCAL',
                        name: 'NR_NOTAFISCAL',
                        width: '10%',
                        title: 'Nota'

                    },
                    {
                        data: 'DS_ITEM',
                        name: 'DS_ITEM',
                        title: 'Item',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'QTD_ITEM',
                        name: 'QTD_ITEM',
                        title: 'Qtde'
                    },
                    {
                        data: 'VL_COMISSAO',
                        name: 'VL_COMISSAO',
                        render: $.fn.dataTable.render.number('.', ',', 2),
                        title: 'Comissão'
                    }
                ]
            });
        }

        function initAccordion(data, idAccordion) {
            let html = `
                <div class="mb-1">
                    <div class="card-header pb-1 mb-3 bg-light">
                        <table class="table table-borderless mb-2 w-100">
                            <tr class="">
                                <th class="text-left p-0">
                                    <small class="text-muted"><strong>Nome</strong></small>
                                </th>
                                <th class="text-right p-0">
                                    <small class="text-muted"><strong>Qtd. Itens</strong></small>
                                </th>
                                <th class="text-right p-0 w-25">
                                    <small class="text-muted"><strong>Valor</strong></small>
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>`;

            data.forEach((gerente, gIndex) => {
                html += `
                    <div class="card gerente-card">
                        ${renderGerente(gerente, gIndex)}
                    </div>
                `;
            });

            $("#" + idAccordion).html(html);
        }

        function renderGerente(gerente, gIndex) {
            let html = `
                <div class="card-header p-1">
                    <button class="btn btn-block"
                        data-toggle="collapse"
                        data-target="#sup-${gIndex}">
                        <table class="table table-borderless mb-0 w-100">
                            <tr>
                                <td class="text-left p-0">
                                    <small class="text-muted">
                                        <i class="fas fa-chevron-down"></i>
                                        <strong>${gerente.nome}</strong>
                                    </small>
                                </td>
                                <td class="text-right p-0 w-10">
                                    ${gerente.qtd_item}
                                </td>
                                <td class="text-right p-0 w-25">
                                    R$ ${formatarValorBR(gerente.vl_comissao)}
                                </td>
                            </tr>
                        </table>
                    </button>
                </div>

                <div id="sup-${gIndex}" class="collapse">
                    <div class="card-body p-1">
            `;

            gerente.supervisores.forEach((sup, sIndex) => {
                html += renderSupervisorContainer(sup, gIndex, sIndex);
            });

            html += `
                    </div>
                </div>
            `;

            return html;
        }

        function renderSupervisorContainer(sup, gIndex, sIndex) {
            let html = `
                <div class="supervisor-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#vend-${gIndex}-${sIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100">
                            <tr>
                                <td class="text-left p-0 indent-1"> <small class="text-muted"> <i class="fas fa-chevron-down"></i>
                                        <strong class="ps-3"> ${sup.nome}</strong> </small> </td>
                                <td class="text-right p-0 w-10"> <small class="text-muted"> ${sup.qtd_item} </small> </td>
                                <td class="text-right p-0 w-25"> <small class="text-muted"> R$
                                        ${formatarValorBR(sup.vl_comissao)} </small> </td>
                            </tr>
                        </table>
                    </button>
                    <div id="vend-${gIndex}-${sIndex}" class="collapse">
            `;

            sup.vendedores.forEach((vend, vIndex) => {
                html += renderVendedorContainer(vend, gIndex, sIndex, vIndex);
            });

            html += `
                    </div>
                </div>
            `;

            return html;
        }

        function renderVendedorContainer(vend, gIndex, sIndex, vIndex) {
            let html = `
            <div class="vendedor-container">
                <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                        data-target="#borr-${gIndex}-${sIndex}-${vIndex}">
                    <table class="table table-bordered table-borderless mb-0 w-100">
                        <tr>
                            <td class="text-left p-0 indent-2"> <small class="text-muted"> <i class="fas fa-chevron-down"></i>
                                    <strong class="ps-3"> ${vend.nome}</strong> </small> </td>
                            <td class="text-right p-0 w-10"> <small class="text-muted"> ${vend.qtd_item} </small> </td>
                            <td class="text-right p-0 w-25"> <small class="text-muted"> R$
                                    ${formatarValorBR(vend.vl_comissao)} </small> </td>
                        </tr>
                    </table>
                </button>
                <div id="borr-${gIndex}-${sIndex}-${vIndex}" class="collapse">
        `;

            vend.borracheiros.forEach((borr, bIndex) => {
                html += renderBorracheiroContainer(borr, gIndex, sIndex, vIndex, bIndex);
            });

            html += `
                </div>
            </div>  

            `;
            return html;
        };

        function renderBorracheiroContainer(borr, gIndex, sIndex, vIndex, bIndex) {
            let html = `
                <div class="borracheiro-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#det-${gIndex}-${sIndex}-${vIndex}-${bIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100">
                            <tr>
                                <td class="text-left p-0 indent-3"> <small class="text-muted"> <i class="fas fa-chevron-down"></i>
                                        <strong class="ps-3"> ${borr.nome}</strong> </small> </td>
                                <td class="text-right p-0 w-10"> <small class="text-muted"> ${borr.qtd_item} </small> </td>
                                <td class="text-right p-0 w-25"> <small class="text-muted"> R$
                                        ${formatarValorBR(borr.vl_comissao)} </small> </td>
                            </tr>
                        </table>
                    </button>
                    <div class="detalhe-pessoa-container collapse" id="det-${gIndex}-${sIndex}-${vIndex}-${bIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100">
                 `;
            borr.clientes.forEach((cli, dIndex) => {
                html += renderDetalhePessoaContainer(cli);
            });
            html += `
                        </table>
                    </div>
                </div>  
                `;
            return html;
        }

        function renderDetalhePessoaContainer(cli) {
            let html = `
                <tr ${cli.ST_BORRACHARIA === 'N' ? 'class="table-secondary"' : ''}>
                    <td class="text-left p-0 indent-4">
                        ${cli.actions}  
                        <small class="text-muted">
                            <strong class="ps-3"> ${cli.PESSOA}</strong>
                        </small>
                    </td>
                    <td class="text-right p-0 w-10">
                        <small class="text-muted">
                            ${(cli.QTD_ITEM).toLocaleString('pt-BR')}
                        </small>
                    </td>
                    <td class="text-right p-0 w-25">
                        <small class="text-muted">
                            R$ ${formatarValorBR(cli.VL_COMISSAO)}
                        </small>
                    </td>
                </tr>  
            `;
            return html;
        }


        function getOpenedAccordions(containerId) {
            let opened = [];

            $('#' + containerId + ' .collapse.show').each(function() {
                opened.push(this.id);
            });

            return opened;
        }

        function restoreAccordions(openedIds) {
            openedIds.forEach(id => {
                $('#' + id).collapse('show');
            });
        }
    </script>

@stop
