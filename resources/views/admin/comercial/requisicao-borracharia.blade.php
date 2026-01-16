@extends('layouts.master')
@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="content-fluid">
        <div class="card collapsed-card mb-4">
            <div class="card-header">
                <h3 class="card-title mt-2">Filtros:</h3>
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
                            <input type="text" class="form-control" id="nm_supervisor" placeholder="Nome Supervisor">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Vendedor</label>
                            <input type="text" class="form-control" id="nm_vendedor" placeholder="Nome Vendedor">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cliente</label>
                            <input type="text" class="form-control" id="nm_cliente" placeholder="Nome Cliente">
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
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
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
                                        <div class="col-md-8" id="div-tabela-requisicao-borracharia">
                                            <small class="badge badge-danger badge-date"></small>
                                            <div class="card-body pb-0 pt-0">
                                                <table class="table table-responsive compact table-font-small"
                                                    id="table-requisicao-borracharia">
                                                    <tfoot>
                                                        <tr>
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

                                        <div class="col-md-4">
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
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Resumo Gerente</h6>
                                                            <div class="card-tools m-0">
                                                                <button class="btn btn-xs btn-danger"
                                                                    id="download-resumo-local"><i
                                                                        class="fas fa-download"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
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
        <div class="modal-dialog modal-xl">
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
        @media (max-width: 768px) {
            #table-item-pedido_wrapper {
                display: none !important;
            }

            [id^="card-pedido"] {
                display: block !important;
            }

            .form-control {
                font-size: 13px;
            }

        }

        @media (min-width: 769px) {
            [id^="card-pedido"] {
                display: none;
            }
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 700px;
                margin: 1.75rem auto;
            }
        }

        .input-destaque {
            background-color: #218838 !important;
            /* vermelho claro */
            border: 1px solid #1e7e34 !important;
            /* borda vermelha */
            transition: background-color 0.5s ease;
            color: #fff !important;
        }
    </style>
@stop

@section('js')
    <script>
        var tableId = 0;
        var table_item_pedido;

        var dtInicio = moment().subtract(1, 'month').startOf('month').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'month').endOf('month').format('DD.MM.YYYY');
        //datas selecionadas no date range picker
        var datasSelecionadas = initDateRangePicker('#daterange', dtInicio, dtFim);

        $('.badge-date').text('Período: ' + dtInicio + ' a ' + dtFim);

        var table = $('#table-requisicao-borracharia').DataTable({
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

                    console.log(json.accordionResumoGerente);
                    initAccordion(json.accordionResumoGerente, 'accordionResumoGerente');


                    return json.datatables.data;
                }
            },
            columns: [{
                    data: "actions",
                    name: "actions",
                    "width": "3%",
                    className: 'text-center text-nowrap'
                },
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
                    render: $.fn.dataTable.render.number('.', ',', 0)
                },
                {
                    data: 'VL_COMISSAO',
                    name: 'VL_COMISSAO',
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
                    .column(3, {
                        search: 'applied'
                    })
                    .data()
                    .sum();

                var totalValor = api
                    .column(4, {
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

                $(api.column(3).footer()).html(
                    totalItens.toLocaleString('pt-BR')
                );

                $(api.column(4).footer()).html(
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

                            table.ajax.reload();
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
            let html = "";
            data.forEach((gerente, gIndex) => {
                valorTotalGerente += gerente.vl_comissao;
                qtdeTotalGerente += gerente.qtd_item;
                html += `
                            <div class="card gerente-card">
                            <div class="card-header p-1">
                                <button class="btn btn-link text-left" data-toggle="collapse" data-target="#sup-${gIndex}">
                                    👔 <strong>${gerente.nome}</strong> 
                                    <div class="small text-muted">
                                        Pneus: <span class="fw-semibold"> ${gerente.qtd_item}</span>
                                    </div>
                                    <span class="badge badge-primary fs-6 ms-3">
                                         R$ ${formatarValorBR(gerente.vl_comissao)}
                                    </span>
                                </button>
                            </div>
                            <div id="sup-${gIndex}" class="collapse">
                                <div class="card-body p-2">     `;

                gerente.supervisores.forEach((sup, sIndex) => {
                    html += `<div class="supervisor-container">`;
                    html += `
                            <button class="btn btn-sm btn-secondary d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                🛡️ ${sup.nome} 
                                <span class="badge badge-primary fs-6 ms-3">Pneus: <span class="fw-semibold">${sup.qtd_item}</span></span>
                                <span class="badge badge-primary fs-6 ms-3">R$ ${formatarValorBR(sup.vl_comissao)}</span>                                
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-2">
                            `;

                    sup.borracheiros.forEach((borra, vIndex) => {
                        html += `<div class="borracheiro-container">`;
                        html += `
                                <button class="btn btn-sm btn-info d-block mb-2 btn-list btn-d-block text-left" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                👤 ${borra.nome} 
                                    <span class="badge badge-warning fs-6 ms-3">Pneus: <span class="fw-semibold">${borra.qtd_item}</span></span>
                                    <span class="badge badge-warning fs-6 ms-3">R$ ${formatarValorBR(borra.vl_comissao)}</span>
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse mt-2">
                                <ul class="list-group">
                            `;

                        borra.clientes.forEach((detalhe) => {
                            html += `
                                <li class="list-group-item p-1 cliente-item">
                                    🏢 <span class="badge badge-secondary">${
                                        detalhe.PESSOA
                                    }</span>
                                    <br>
                                     <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted">Qtd</th>
                                                <td class="td-small-text">${
                                                    detalhe.QTD_ITEM
                                                }</td>
                                                <th class="text-muted">Total</th>
                                                <td><span class="font-weight-bold">${formatarValorBR(
                                                    detalhe.VL_COMISSAO
                                                )}</span></td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                
                                </li>
                                `;
                        });

                        html += `</ul></div>`;
                        html += `   </div>`;
                    });

                    html += `</div>`; // fecha Supervisor
                    html += `</div>`;
                });

                html += `</div></div></div>`; // fecha Gerente


                $("#" + idAccordion).html(html);
            });
        }
    </script>

@stop
