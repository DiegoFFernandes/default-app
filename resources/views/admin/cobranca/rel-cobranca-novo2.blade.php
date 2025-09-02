@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fa fa-list-ul"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number" id="soma-geral">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="far fa-thumbs-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text maior-divida">Maior divida</span>
                        <span class="info-box-number" id="maior-divida">

                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-sort-amount-up-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text titulos">Quantidade de Titulos</span>
                        <span class="info-box-number" id="qtd-titulos">

                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="list-cobranca">
                            <table class="table table-striped compact" id="table-rel-cobranca"
                                style="width: 100%;font-size: 13px">                                
                                <tfoot>
                                    <tr>                                       
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right"></th>
                                        <th style="text-align: right"></th> <!-- aqui será inserido o total -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal modal-default fade" id="modal-detalhar" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Titulos Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body" style="overflow-x: auto;">
                        <table class="table compact" id="table-list-details" style="font-size: 12px">
                            <thead>
                                <tr>
                                    <th>Emp</th>
                                    <th>Cliente</th>
                                    <th>CNPJ</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
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
        var totalValor = 0;
        var table = $('#table-rel-cobranca').DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
            },
            pageLength: 50,
            // responsive: true,
            // "searching": true,
            // "bInfo": false,
            scrollX: true,
            ajax: "{{ route('get-list-cobranca') }}",
            columns: [{
                    data: "responsavel",
                    name: "responsavel",
                    title: 'Responsável',
                    visible: true
                },
                {
                    data: "DS_REGIAOCOMERCIAL",
                    name: "DS_REGIAOCOMERCIAL",
                    title: 'Região',
                    visible: true
                },                
                {
                    data: "percentual",
                    name: "percentual",
                    title: 'Percentual'
                },
                {
                    data: "VL_SALDO",
                    name: "VL_SALDO",
                    title: 'Valor'
                },
            ],
            columnDefs: [{
                    targets: 3,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }
            ],
            // outras configurações...
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Pegando a coluna desejada (ex: coluna 3 = índice 2)
                var total = api
                    .column(3, {
                        page: 'all'
                    }) // ou 'page: all' para total geral
                    .data()
                    .reduce(function(a, b) {
                        return Number(a) + Number(b.toString().replace(/[^\d.-]/g, ''));
                    }, 0);

                // Atualiza o footer da coluna
                $(api.column(3).footer()).html('Total: ' + total.toLocaleString('pt-BR'));

                let maiorValor = 0;
                let regiaoMaior = '';
                let qtdTitulos = 0;

                data.forEach(function(item) {
                    let valor = Number(item.VL_SALDO);
                        qtdTitulos += Number(item.TITULOS);
                    if (valor > maiorValor) {
                        maiorValor = valor;
                        regiaoMaior = item.DS_REGIAOCOMERCIAL;
                    }
                });                
                let percMaior = ((Number(maiorValor) / Number(total)) * 100).toFixed(2);

                $('#soma-geral').text(total.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
                $('#maior-divida').html(maiorValor.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }) + '<small>  ' + percMaior + '%</small>');
                $('.maior-divida').html('Maior Dívida <b>' + regiaoMaior + '</b>');
                $('#qtd-titulos').text(qtdTitulos);

            },
            order: [
                [3, 'desc']
            ]
        });
        $('#table-rel-cobranca tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            var tableId = 'regiao-' + row.data().CD_REGIAOCOMERCIAL;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                // $(this).removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                // Open this row
                row.child(template(row.data())).show();
                initTable(tableId, row.data());
                // console.log(row.data());
                tr.addClass('shown');
                // $(this).removeClass('fa-plus-circle').addClass('fa-minus-circle');
                tr.next().find('td').addClass('no-padding');
            }
        });

        function initTable(tableId, data) {
            console.log(tableId);
            table_pessoa = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": true,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ajax: {
                    method: "GET",
                    url: " {{ route('get-list-pessoa-cobranca') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        id: data.CD_REGIAOCOMERCIAL
                    }
                },
                columns: [{
                        name: 'details',
                        data: 'details',
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA'
                    },
                    {
                        data: 'NR_CNPJCPF',
                        name: 'NR_CNPJCPF'
                    }, {
                        data: 'TITULOS',
                        name: 'TITULOS'
                    },
                    {
                        data: 'TIPOCONTA',
                        name: 'TIPOCONTA',
                        visible: false
                    },
                    {
                        data: 'VL_SALDO',
                        name: 'VL_SALDO'
                    }

                ],
                columnDefs: [{
                    targets: 5,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }, ],
                order: [
                    [4, 'desc']
                ]

            });
        }
        $('body').on('click', '.detalhar', function() {
            $('#modal-detalhar').modal('show');
            var cd_pessoa = $(this).data('cd_pessoa');
            $('#table-list-details').DataTable().destroy();

            table_pessoa_details = $('#table-list-details').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": true,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ajax: {
                    method: "GET",
                    url: " {{ route('get-list-pessoa-cobranca-details') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        cd_pessoa: cd_pessoa
                    }
                },
                columns: [{
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA'
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA'
                    },
                    {
                        data: 'NR_CNPJCPF',
                        name: 'NR_CNPJCPF'
                    },
                    {
                        data: 'DT_VENCIMENTO',
                        name: 'DT_VENCIMENTO',
                    },
                    {
                        data: 'VL_SALDO',
                        name: 'VL_SALDO'
                    },
                    {
                        data: 'DS_OBSERVACAO',
                        name: 'DS_OBSERVACAO',
                        width: '10%'
                    }
                ],
                columnDefs: [{
                    targets: 4,
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }]

            });
        });
    </script>
@stop
