@extends('layouts.master')
@section('title', 'Dashboard')

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

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small">Gerente</label>
                                <input type="text" class="form-control form-control-sm" id="nm_gerente"
                                    placeholder="Nome Gerente">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small">Supervisor</label>
                                <input type="text" class="form-control form-control-sm" id="nm_supervisor"
                                    placeholder="Nome Supervisor">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small">Borracheiro</label>
                                <input type="text" class="form-control form-control-sm" id="nm_borracheiro"
                                    placeholder="Nome Borracheiro">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small">Cliente</label>
                                <input type="text" class="form-control form-control-sm" id="nm_cliente"
                                    placeholder="Nome Cliente">
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-sm float-right mr-2"
                                id="btn-limpar">Limpar</button>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8 col-md-8 mb-3">
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
                        <div class="tab-content" id="tabContentRequisicaoBorracharia">
                            <div class="tab-pane fade show active" id="painel-requisicao-borracharia-pagar" role="tabpanel"
                                aria-labelledby="tab-requisicao-borracharia-pagar">
                                <div class="card-body p-2">
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
                            <div class="col-3 col-md-3">
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
                            </div>
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
            border-left: 4px solid #dc3545 !important;
        }

        .supervisor-container {
            margin-left: 12px;
            border-left: 3px solid #6c757d;
            padding-left: 6px;
        }

        .borracheiro-container {
            margin-left: 24px;
            border-left: 2px solid #adb5bd;
            padding-left: 6px;
        }

        .detalhe-pessoa-container {
            margin-left: 36px;
            border-left: 1px solid #dee2e6;
            padding-left: 6px;
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
        var tableId = 0;
        var table_item_pedido;
        let accordionResumoGerenteOriginal = [];
        let accordionResumoGerenteFiltrado = [];
        let datatables = [];

        var dtInicio = moment().subtract(1, 'month').startOf('month').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'month').endOf('month').format('DD.MM.YYYY');
        //datas selecionadas no date range picker
        var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

        $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);


        var table = $('#table-requisicao-borracharia').DataTable({
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
                method: 'GET',
                data: {
                    dt_inicio: dtInicio,
                    dt_fim: dtFim
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
            }
        });

        $(document).on('click', '.btn-view-requisicao-borracharia', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);


            $('.pessoa').val(row.data().NM_PESSOA);
            $('.borracheiro').val(row.data().NM_BORRACHEIRO);
            $('.qtd-notas').val(row.data().QTD_NOTA);
            $('.qtd-itens').val(row.data().QTD_ITEM);
            $('.vlr-comissao-total').val(row.data().VL_COMISSAO);

            $('#modal-table-detalhes-requisicao-borracharia').modal('show');


            initTable('table-item-detalhes-requisicao-borracharia', row.data());
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

        function initTable(tableId, data) {

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
                        cd_pessoa: data.CD_PESSOA,
                        cd_borracheiro: data.CD_BORRACHEIRO
                    }
                },
                columns: [{
                        data: 'NR_NOTAFISCAL',
                        name: 'NR_NOTAFISCAL',
                        width: '1%',
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
            let valorTotalGerente = 0;
            let qtdeTotalGerente = 0;
            let html = `<div class="mb-1">
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
                valorTotalGerente += gerente.vl_comissao;
                qtdeTotalGerente += gerente.qtd_item;
                html += `                        
                        <div class="card gerente-card">
                            <div class="card-header p-1">
                                <button
                                    class="btn btn-block"
                                    data-toggle="collapse"
                                    data-target="#sup-${gIndex}"
                                    aria-expanded="false">
                                    <table class="table table-borderless mb-0 w-100">
                                        <tr>
                                            <td class="text-left p-0">
                                                <small class="text-muted">
                                                    <i class="fas fa-chevron-down"></i>
                                                    <strong>${gerente.nome}</strong>
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-10">
                                                <small class="text-muted">
                                                    ${gerente.qtd_item}
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-25">
                                                <small class="text-muted">
                                                    R$ ${formatarValorBR(gerente.vl_comissao)}
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </button>
                            </div>

                            <div id="sup-${gIndex}" class="collapse">
                                <div class="card-body p-1">     `;

                gerente.supervisores.forEach((sup, sIndex) => {
                    html += `<div class="supervisor-container">`;
                    html += `
                            <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                <table class="table table-borderless mb-0 w-100">
                                    <tr>
                                        <td class="text-left p-0">
                                            <small class="text-muted">
                                                <i class="fas fa-chevron-down"></i>
                                                <strong class="ps-3"> ${sup.nome}</strong>
                                            </small>
                                        </td>
                                        <td class="text-right p-0 w-10">
                                            <small class="text-muted">
                                                ${sup.qtd_item}
                                            </small>
                                        </td>
                                        <td class="text-right p-0 w-25">
                                            <small class="text-muted">
                                                R$ ${formatarValorBR(sup.vl_comissao)}
                                            </small>
                                        </td>
                                    </tr>
                                </table>                              
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse">
                            `;

                    sup.borracheiros.forEach((borra, vIndex) => {
                        html += `<div class="borracheiro-container">`;
                        html += `
                                <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                    <table class="table table-borderless mb-0 w-100">
                                        <tr>
                                            <td class="text-left p-0">
                                                <small class="text-muted">
                                                    <i class="fas fa-chevron-down"></i>
                                                    <strong class="ps-3"> ${borra.nome}</strong>
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-10">
                                                <small class="text-muted">
                                                    ${borra.qtd_item}
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-25">
                                                <small class="text-muted">
                                                    R$ ${formatarValorBR(borra.vl_comissao)}
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse detalhe-pessoa-container mr-3">
                                    <table class="table table-borderless mb-2 w-100">
                            `;

                        borra.clientes.forEach((detalhe) => {
                            html += `
                                        <tr ${detalhe.ST_BORRACHARIA === 'N' ? 'class="table-secondary"' : ''}>
                                            <td class="text-left p-0">
                                                ${detalhe.actions}  
                                                <small class="text-muted">
                                                    <strong class="ps-3"> ${detalhe.PESSOA}</strong>
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-10">
                                                <small class="text-muted">
                                                    ${(detalhe.QTD_ITEM).toLocaleString('pt-BR')}
                                                </small>
                                            </td>
                                            <td class="text-right p-0 w-25">
                                                <small class="text-muted">
                                                    R$ ${formatarValorBR(detalhe.VL_COMISSAO)}
                                                </small>
                                            </td>
                                        </tr>                                    
                                    `;
                        });

                        html += `</table>
                                </div>`;
                        html += `   </div>`;
                    });

                    html += `</div>`; // fecha Supervisor
                    html += `</div>`;
                });

                html += `</div></div></div>`; // fecha Gerente


                $("#" + idAccordion).html(html);
            });
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
