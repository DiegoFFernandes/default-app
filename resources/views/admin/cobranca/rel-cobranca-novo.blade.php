@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input id="filtro-nome" type="text" class="form-control" placeholder="Filtrar por Pessoa">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-vendedor" type="text" class="form-control" placeholder="Filtrar por Vendedor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-cnpj" type="text" class="form-control" placeholder="Filtrar por CNPJ">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="filtro-supervisor" type="text" class="form-control"
                            placeholder="Filtrar por Supervisor">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input id="daterange" type="text" class="form-control" placeholder="Filtrar por Vencimento">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkVencidas" name="filtroVencimento">
                            <label for="checkVencidas" class="custom-control-label">Vencidas</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox"
                                id="checkAvencer" name="filtroVencimento">
                            <label for="checkAvencer" class="custom-control-label">A Vencer</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-danger" type="checkbox" id="checkAll"
                                name="filtroVencimento" checked>
                            <label for="checkAll" class="custom-control-label">Todas</label>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default btn-sm float-right" id="btn-limpar">
                                <i class="fas fa-eraser"></i> Limpar
                            </button>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
        </div>
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
                        <div class="tab-content" id="tabContentRelatorio">
                            <div class="tab-pane fade show active" id="painel-relatorio-cobranca" role="tabpanel"
                                aria-labelledby="tab-relatorio-cobranca">
                                <div class="row">
                                    <div class="col-md-8 col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Relatório</h5>
                                            </div>
                                            <div class="card-body p-2">
                                                <table id="table-rel-cobranca"
                                                    class="table compact table-font-small table-striped table-bordered nowrap"
                                                    style="width:100%; font-size: 12px;">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <h5 class="card-title">Inadimplência Mensal</h5>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="d-flex justify-content-start justify-content-md-end mt-2 mt-md-0">
                                                            <button id="btn-toggle-chart"
                                                                class="btn btn-secondary btn-sm btn-hover">Exibir
                                                                Gráfico</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div id="container-tabela">
                                                    <div class="table-responsive">
                                                        <table id="tabela-inadimplencia-vendedor"
                                                            class="table compact table-font-small table-striped table-bordered nowrap"
                                                            style="width:100%; font-size: 12px;">
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
                                                        <div class="modal-header">
                                                            <h6 class="modal-title" id="modal-table-cliente-label">
                                                                Detalhes Inadimplência</h6>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
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
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-cartao-cheque" role="tabpanel"
                                aria-labelledby="tab-cartao-cheque">
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table id="chequesCartao"
                                            class="table compact table-font-small table-striped table-bordered nowrap"
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
        .btn-hover {
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        #container-grafico {
            height: 300px;
        }

        /* diminui a fonte do modal da nota*/
         .td-small-text {
            font-size: 12px;
        } 
    </style>


@stop

@section('js')

    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
        <div class="right badge badge-danger">{{ DS_REGIAOCOMERCIAL }}</div>
        <table class="table row-border" id="regiao-{{ CD_REGIAOCOMERCIAL }}" style="width:100%">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Cliente</th>
                    <th>CNPJ</th>
                    <th>Titulos</th>
                    <th>Tipo Conta</th>
                    <th>Valor</th>
                </tr>
            </thead>
        </table>
        @endverbatim
    </script>

    <script type="text/javascript">
        var template = Handlebars.compile($("#details-template").html());
        var table = $("#table-rel-cobranca").DataTable({
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            },
            pageLength: 50,
            paging: false,
            searching: false,
            responsive: true,
            // "searching": true,
            // "bInfo": false,
            // scrollX: true,
            ajax: "{{ route('get-list-cobranca') }}",
            columns: [{
                    data: "actions",
                    name: "actions",
                    title: "Ações",
                    orderable: false,
                    searchable: false,
                    "width": "1%",
                },
                {
                    data: "responsavel",
                    name: "responsavel",
                    title: "Responsável",
                    visible: true,
                },
                {
                    data: "qtd_titulos",
                    name: "qtd_titulos",
                    title: "Títulos",
                    visible: true,
                },
                {
                    data: "saldo",
                    name: "saldo",
                    title: "Valor",
                },
            ]
        });
    </script>

    <script>
        var table = $('#tabela-inadimplencia-vendedor').DataTable({
            processing: false,
            serverSide: false,
            searching: false,
            paging: false,
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            },
            ajax: "{{ route('get-inadimplencia') }}",
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
                    return typeof i === 'string' ? i.replace(',', '.') * 1 : typeof i === 'number' ? i : 0;
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
                    totalTotal.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );
                $(api.column(3).footer()).html(
                    totalVencido.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );

                var inadimplenciaPercentual = (totalVencido / totalTotal) * 100;
                $('#pc_inadimplencia').html(inadimplenciaPercentual.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '%');

                $('#vencidos').html(totalVencido.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
            },
        });

        $('#tabela-inadimplencia-vendedor tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

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
                        .MES_ANO);
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
                                            <td><span class="text-success font-weight-bold">R$ ${parseFloat(detalhe.VL_SALDO).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span></td>
                                            <th class="text-muted">Juros</th>
                                            <td><span class="text-danger">R$ 0,00</span></td>
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

        function formatDate(value) {
            if (!value) return "";
            const date = new Date(value);
            return (
                date.toLocaleDateString("pt-BR")
            );
        }
    </script>

    <script src="{{ asset('js/dashboard/inadimplencia-mensal.js') }}"></script>
    <script src="{{ asset('js/dashboard/relatorioCobranca.js') }}"></script>
    <script src="{{ asset('js/dashboard/inadimplencia.js') }}"></script>
    <script src="{{ asset('js/dashboard/chequesCartao.js') }}"></script>
    <script src="{{ asset('js/dashboard/limiteCredito.js') }}"></script>
    <script src="{{ asset('js/dashboard/prazoMedio.js') }}"></script>

@stop
