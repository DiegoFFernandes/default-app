@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabRelatorio" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-relatorio-cobranca" data-toggle="pill"
                                    href="#painel-relatorio-cobranca" role="tab"
                                    aria-controls="painel-relatorio-cobranca" aria-selected="true">
                                    Relatório
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-cartao-cheque" data-toggle="pill" href="#painel-cartao-cheque"
                                    role="tab" aria-controls="painel-cartao-cheque" aria-selected="false">
                                    Cheques e Cartão
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-limite-credito" data-toggle="pill" href="#painel-limite-credito"
                                    role="tab" aria-controls="painel-limite-credito" aria-selected="false">
                                    Limite Crédito
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-prazo-medio" data-toggle="pill" href="#painel-prazo-medio"
                                    role="tab" aria-controls="painel-prazo-medio" aria-selected="false">
                                    Prazo Médio
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="badge badge-danger badge-date-inadimplencia"></small>
                        </div>
                        <div class="tab-content" id="tabContentRelatorio">
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
                                        <div class="col-md-4 mb-2">
                                            <input id="filtro-nome" type="text" class="form-control"
                                                placeholder="Filtrar por Pessoa">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <input id="filtro-vendedor" type="text" class="form-control"
                                                placeholder="Filtrar por Vendedor">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <input id="filtro-cnpj" type="text" class="form-control"
                                                placeholder="Filtrar por CNPJ">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <input id="filtro-supervisor" type="text" class="form-control"
                                                placeholder="Filtrar por Supervisor">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <input id="daterange" type="text" class="form-control"
                                                placeholder="Filtrar por Vencimento">
                                        </div>

                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-default btn-sm float-right"
                                                    id="btn-reset">
                                                    <i class="fas fa-eraser"></i> Limpar
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm float-right mr-2"
                                                    id="btn-search">
                                                    <i class="fas fa-check"></i> Buscar
                                                </button>
                                            </div>
                                            <!-- /.row -->
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </div>
                            </div>
                            <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                aria-labelledby="tab-relatorio-cobranca">
                                <div class="row">
                                    <div class="col-6 col-md-2">
                                        <div class="info-box">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Vencidos</span>
                                                <span class="info-box-number" id="vencidos"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="info-box">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Inadimplência</span>
                                                <span class="info-box-number" id="pc_inadimplencia">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="card card-secondary card-outline mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title">Inadimplência Mensal</h5>
                                                <div class="float-right"><button id="btn-toggle-chart"
                                                        class="btn btn-secondary btn-xs btn-hover">Exibir
                                                        Gráfico</button>
                                                </div>
                                            </div>
                                            <div class="card-body p-2" id="card-inadimplencia-meses">
                                                {{-- Icon loading --}}
                                                <div class="invisible loading-card">
                                                    <div class="overlay loading-image-card"><i
                                                            class="fas fa-3x fa-sync-alt fa-spin"></i>
                                                        <div class="text-bold pt-2"></div>
                                                    </div>
                                                </div>
                                                <div id="container-tabela">
                                                    <div class="table-responsive">
                                                        <table id="tabela-inadimplencia-meses"
                                                            class="table compact table-font-small nowrap"
                                                            style="width:100%; font-size: 12px;">
                                                            <tfoot>
                                                                <tr>
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
                                                <div id="container-grafico" style="display:none;">
                                                    <canvas id="graficoInadimplencia"></canvas>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="modal-table-cliente" tabindex="-1"
                                                role="dialog" aria-labelledby="modal-table-cliente-label"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-flex flex-wrap align-items-center">
                                                            <h6 class="modal-title flex-md-grow-1"
                                                                id="modal-table-cliente-label">
                                                                Detalhes Inadimplência
                                                            </h6>
                                                            <button type="button"
                                                                class="close order-2 order-md-3 ml-auto ml-md-0"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            <input type="text" id="buscarCliente"
                                                                class="form-control input-busca order-3 order-md-2 mt-3 mt-md-0 mr-md-2"
                                                                placeholder="Buscar Cliente...">
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="accordion" id="accordionCliente">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <div class="card card-secondary card-outline mb-4">
                                            <div class="card-header">
                                                <h5 class="card-title">Relatório Vencidos</h5>
                                            </div>
                                            <div class="card-body p-2" id="card-inadimplencia-gerente">
                                                {{-- Icon loading --}}
                                                <div class="invisible loading-card">
                                                    <div class="overlay loading-image-card"><i
                                                            class="fas fa-3x fa-sync-alt fa-spin"></i>
                                                        <div class="text-bold pt-2"></div>
                                                    </div>
                                                </div>
                                                <div class="accordion" id="treeAccordion">
                                                    <!-- Gerente -->
                                                    <div class="card">
                                                        <div class="card-header p-1">
                                                            <h2 class="mb-0">
                                                                <button class="btn btn-link" type="button"
                                                                    data-toggle="collapse" data-target="#sup1">
                                                                </button>
                                                            </h2>
                                                        </div>
                                                        <div id="sup1" class="collapse"
                                                            data-parent="#treeAccordion">
                                                            <div class="card-body">
                                                                <!-- Supervisor -->
                                                                <button class="btn btn-sm btn-secondary"
                                                                    data-toggle="collapse" data-target="#vend1">
                                                                    🛡️ Supervisor
                                                                </button>
                                                                <div id="vend1" class="collapse mt-2">
                                                                    <!-- Vendedor -->
                                                                    <button class="btn btn-sm btn-info"
                                                                        data-toggle="collapse" data-target="#cli1">
                                                                        👤 Vendedor
                                                                    </button>
                                                                    <div id="cli1" class="collapse mt-2">
                                                                        <!-- Clientes -->
                                                                        <ul class="list-group">
                                                                            <li class="list-group-item">🏢 Cliente </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer text-right">
                                                <strong>Total geral: </strong><span id="valorTotalGerente"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">

                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="chequesCartao" class="table compact table-font-small nowrap"
                                            style="width:100%; font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th>Documento</th>
                                                    <th>Nome</th>
                                                    <th>Cnpj</th>
                                                    <th>Vencimento</th>
                                                    <th>Valor</th>
                                                    <th>Juros</th>
                                                    <th>Dias Atraso</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-limite-credito" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-prazo-medio" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </section>
    <!-- /.content -->
@stop

@section('css')

    <style>
        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {
            display: none;
        }

        .btn-hover {
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        #container-grafico {
            height: 300px;
        }

        @media (max-width: 768px) {
            .btn-list {
                font-size: 13px;
            }

            .table-font-xs {
                font-size: 13px;
            }

            .text-small {
                font-size: 13px;
            }
        }

        /* otimiza a busca do modal*/
        .hidden {
            display: none !important;
        }

        .input-busca {
            width: 100%;
        }

        @media (min-width: 768px) {
            .input-busca {
                max-width: 250px;
                width: auto;
            }
        }
    </style>


@stop

@section('js')
    <script type="text/javascript">
        var tableInadimplencia;
        var dtInicio = moment().subtract(240, 'days').format('DD.MM.YYYY');
        var dtFim = moment().subtract(1, 'days').format('DD.MM.YYYY');
        $('.badge-date-inadimplencia').text('Período: ' + dtInicio + ' a ' + dtFim);

        searchDataVencidos();
        initTableMeses();

        // faz a pesquisa pelos filtros
        $('#btn-search').on('click', function() {
            const data = {
                nm_pessoa: $('#filtro-nome').val(),
                nm_vendedor: $('#filtro-vendedor').val(),
                cnpj: $('#filtro-cnpj').val(),
                nm_supervisor: $('#filtro-supervisor').val(),
                session: true
            };

            searchDataVencidos(data);
            initTableMeses(data);
        });

        //limpa as filtros e retorna tudo novamente
        $('#btn-reset').on('click', function() {
            $('#filtro-nome').val('');
            $('#filtro-vendedor').val('');
            $('#filtro-cnpj').val('');
            $('#filtro-supervisor').val('');
            const data = {
                nm_pessoa: '',
                nm_vendedor: '',
                cnpj: '',
                nm_supervisor: '',
                session: false
            };
            searchDataVencidos(data);
            initTableMeses(data);
        });

        // inicializa a tabela de meses
        function initTableMeses(data) {

            if (tableInadimplencia) {
                tableInadimplencia.destroy();
            }

            tableInadimplencia = $('#tabela-inadimplencia-meses').DataTable({
                processing: false,
                serverSide: false,
                searching: false,
                paging: false,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                ajax: {
                    url: "{{ route('get-inadimplencia') }}",
                    data: {
                        filtro: data
                    },
                    beforeSend: function() {
                        $("#card-inadimplencia-meses .loading-card").removeClass('invisible');
                    },
                    complete: function() {
                        $("#card-inadimplencia-meses .loading-card").addClass('invisible');
                    }
                },

                columns: [{
                        data: 'action',
                        name: 'action',
                        title: ''
                    },
                    {
                        data: 'MES_ANO',
                        name: 'MES_ANO',
                        title: 'Mês/Ano',
                        "width": '33%'
                    },
                    {
                        data: 'VL_DOCUMENTO',
                        name: 'VL_DOCUMENTO',
                        title: 'Total',
                        visible: false
                    },
                    {
                        data: 'VL_SALDO',
                        name: 'VL_SALDO',
                        title: 'Vencido',
                        "width": '33%'
                    },
                    {
                        data: 'PC_INADIMPLENCIA',
                        name: 'PC_INADIMPLENCIA',
                        title: '%',
                        "width": '33%'
                    }
                ],
                columnDefs: [{
                    targets: [2, 3],
                    render: $.fn.dataTable.render.number('.', ',', 2)
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ? i.replace(',', '.') * 1 : typeof i === 'number' ? i :
                            0;
                    };

                    // Total over all pages
                    totalTotal = api
                        .column(2)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    totalVencido = api
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(2).footer()).html(
                        formatarValorBR(totalTotal)
                    );
                    $(api.column(3).footer()).html(
                        formatarValorBR(totalVencido)
                    );

                    var inadimplenciaPercentual = (totalVencido / totalTotal) * 100;
                    $('#pc_inadimplencia').html(formatarValorBR(inadimplenciaPercentual) + '%');
                    $('#vencidos').html(formatarValorBR(totalVencido));
                }

            });

            $('#tabela-inadimplencia-meses tbody').on('click', '.details-control', function() {
                var tr = $(this).closest('tr');
                var row = tableInadimplencia.row(tr);

                $('#accordionCliente').empty(); // Limpa antes

                $.ajax({
                    type: "GET",
                    url: "{{ route('get-inadimplencia-cliente', ['id' => '']) }}",
                    data: {
                        mes: row.data().MES,
                        ano: row.data().ANO
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $("#loading").removeClass('invisible');
                    },
                    success: function(response) {
                        data = Object.values(response);

                        $('#modal-table-cliente-label').html('Detalhes Inadimplência</br>' + row.data()
                            .MES_ANO + ' (' + formatarValorBR(row.data().VL_SALDO) + ')');
                        $('#accordionCliente').empty(); // limpa antes de popular

                        data.forEach(function(item) {
                            let accordion = `
                        <div class="card card-outline">
                        <div class="card-header pt-1 pb-1" id="heading${item.CD_PESSOA}">
                            <h6 class="mb-0 d-flex align-items-center justify-content-between">
                                <button class="btn collapsed p-0 m-0 text-left" type="button"
                                        data-toggle="collapse"
                                        data-target="#collapse${item.CD_PESSOA}"
                                        aria-expanded="false"
                                        aria-controls="collapse${item.CD_PESSOA}" style="font-size: 13px;">
                                <b>${item.NM_PESSOA}</b>
                                </button>
                                <span class="badge badge-warning ml-2">
                                    ${parseFloat(item.VL_SALDO_AGRUPADO).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                </span>
                            </h6>
                        </div>
                        <div id="collapse${item.CD_PESSOA}" class="collapse" aria-labelledby="heading${item.CD_PESSOA}">
                    `;
                            item.DETALHES.forEach(function(detalhe) {
                                accordion += `
                                <div class="card-body p-1">
                                    <div class="card-body pt-2 pb-2">
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                        <tr>
                                            <th class="text-muted">Nota</th>
                                            <td class="td-small-text">${detalhe.NR_DOCUMENTO}</td>
                                            <th class="text-muted">Venc.</th>
                                        <td>${formatDate(detalhe.DT_VENCIMENTO)}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Valor</th>
                                            <td><span class="text-success font-weight-bold">R$ ${formatarValorBR(detalhe.VL_SALDO)}</span></td>
                                            <th class="text-muted">Juros</th>
                                            <td><span class="text-danger">${formatarValorBR(detalhe.VL_JUROS)}</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <hr class="mt-0 mb-2">
                        `;
                            });

                            accordion += `
                            </div>
                        </div>
                    `;

                            $('#accordionCliente').append(accordion);
                        });

                        $('#loading').addClass('invisible');
                        $('#modal-table-cliente').modal('show');
                    }

                });

            });
        }

        // inicializa o accordion da inadimplência por gerente
        function searchDataVencidos(data) {
            $.ajax({
                url: "{{ route('get-list-cobranca') }}",
                method: "GET",
                data: {
                    filtro: data
                },
                beforeSend: function() {
                    $("#card-inadimplencia-gerente .loading-card").removeClass('invisible');
                },
                success: function(data) {
                    let valorTotalGerente = 0;
                    let html = '';
                    data.forEach((gerente, gIndex) => {
                        valorTotalGerente += gerente.saldo;
                        html += `
                            <div class="card">
                            <div class="card-header p-1">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#sup-${gIndex}">
                                👔 ${gerente.nome} (R$ ${formatarValorBR(gerente.saldo)})
                                </button>
                            </div>
                            <div id="sup-${gIndex}" class="collapse" data-parent="#treeAccordion">
                                <div class="card-body p-2">     `;


                        gerente.supervisores.forEach((sup, sIndex) => {
                            html += `
                            <button class="btn btn-sm btn-secondary d-block mb-2 btn-list" data-toggle="collapse" data-target="#vend-${gIndex}-${sIndex}">
                                🛡️ ${sup.nome} (R$ ${formatarValorBR(sup.saldo)})
                            </button>
                            <div id="vend-${gIndex}-${sIndex}" class="collapse mt-2">
                            `;

                            sup.vendedores.forEach((vend, vIndex) => {
                                html += `
                                <button class="btn btn-sm btn-info d-block mb-2 btn-list" data-toggle="collapse" data-target="#cli-${gIndex}-${sIndex}-${vIndex}">
                                👤 ${vend.nome} (R$ ${formatarValorBR(vend.saldo)})
                                </button>
                                <div id="cli-${gIndex}-${sIndex}-${vIndex}" class="collapse mt-2">
                                <ul class="list-group">
                            `;

                                vend.clientes.forEach(cliente => {
                                    html += `
                                <li class="list-group-item p-1">
                                    🏢 <span class="text-small">${cliente.nome} - R$ ${formatarValorBR(cliente.saldo)}</span><br>
                                    <table class="table table-sm mb-0 table-font-xs">
                                        <tbody>
                                        <tr>
                                            <th class="text-muted">Nota</th>
                                            <td class="td-small-text">${cliente.detalhes.documento}</td>
                                            <th class="text-muted">Venc.</th>
                                        <td>${formatDate(cliente.detalhes.vencimento)}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Valor</th>
                                            <td><span class="text-success font-weight-bold">R$ ${formatarValorBR(cliente.detalhes.saldo)}</span></td>
                                            <th class="text-muted">Juros</th>
                                            <td><span class="text-danger">${formatarValorBR(cliente.detalhes.juros)}</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                
                                </li>
                                `;
                                });

                                html += `</ul></div>`;
                            });

                            html += `</div>`; // fecha Supervisor
                        });

                        html += `</div></div></div>`; // fecha Gerente
                    });

                    $("#treeAccordion").html(html);
                    $("#card-inadimplencia-gerente .loading-card").addClass('invisible');
                    $('#valorTotalGerente').text(`R$ ${valorTotalGerente.toLocaleString()}`);
                }
            });
        }
    </script>

    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js?v=1') }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}"></script>

@stop
