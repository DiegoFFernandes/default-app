@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
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
                            <div class="col-md-2 mb-1">
                                <input id="filtro-empresa" type="text" class="form-control"
                                    placeholder="Filtrar por Empresa">
                            </div>                           
                            <div class="col-md-4 mb-1">
                                <input id="filtro-nome" type="text" class="form-control"
                                    placeholder="Filtrar por Pessoa">
                            </div>
                            <div class="col-md-4 mb-1">
                                <input id="filtro-docto" type="text" class="form-control"
                                    placeholder="Filtrar por Documento">
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="card card-danger card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#pendentes" data-toggle="tab"
                                    aria-expanded="true">Aguardando
                                    Analise</a>
                            <li class="">
                                <a class="nav-link" href="#vistos" data-toggle="tab" aria-expanded="false">Pendentes
                                    Bloqueados</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" style="padding: 0 10px; font-size: 12px;">
                            <div class="tab-pane active" id="pendentes">
                                <table class="table stripe compact" id="table-contas-bloqueadas-pendentes"
                                    style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Emp</th>
                                            <th>Pessoa</th>
                                            <th>Docto</th>
                                            <th>Parcelas</th>
                                            <th>Total</th>
                                            <th>Emissão</th>
                                            <th>Ds Liberacao</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                            <div class="tab-pane" id="vistos">
                                <table class="table stripe compact" id="table-contas-bloqueadas-vistos" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Emp</th>
                                            <th>Pessoa</th>
                                            <th>Docto</th>
                                            <th>Parcelas</th>
                                            <th>Total</th>
                                            <th>Emissão</th>
                                            <th>Ds Liberacao</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="liberacao">*Motivo Liberação/Bloqueio</label>
                        <textarea class="form-control" id="liberacao" rows="2" cols="50"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6" style="padding-top: 10px;">
                            <button class="btn btn-success btn-sm btn-block btn-aproover" id="">Aprovar</button>
                        </div>
                        <div class="col-md-6 col-sm-6" style="padding-top: 10px;">
                            <button class="btn btn-primary btn-sm btn-block btn-blocker" id="">Manter
                                Bloquear</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop
@section('js')
    <script id="details-item-historico" type="text/x-handlebars-template">
        @verbatim
            <span class="badge badge-danger">{{ DS_TIPOCONTA }}</span>
            <table class="table row-border" id="conta-{{ NR_LANCAMENTO }}" style="width:80%; font-size:12px" >
                <thead>
                    <tr>
                        <th>Historico</th>
                        <th>Parcela</th> 
                        <th>Emissão</th>
                        <th>Vencimento</th>
                        <th>Valor</th>                       
                    </tr>
                </thead>
                
            </table>
        @endverbatim
    </script>

    <script id="details-centro-resultado" type="text/x-handlebars-template">
        @verbatim
            <div class="label label-info">{{ DS_TIPOCONTA }}</div>
            <table class="table row-border" id="conta-{{ NR_LANCAMENTO }}" style="width:80%; font-size:12px" >
                <thead>
                    <tr>
                        <th>Centro Resultado</th>
                        <th>Vl Despesas</th>                       
                    </tr>
                </thead>
                
            </table>
        @endverbatim
    </script>

    <script id="details-vencimento" type="text/x-handlebars-template">
        @verbatim
           <div class="label label-info">{{ DS_TIPOCONTA }}</div>
            <table class="table row-border" id="conta-{{ NR_LANCAMENTO }}" style="width:80%; font-size:12px" >
                <thead>
                    <tr>
                        <th>Historico</th>
                        <th>Valor</th>                       
                    </tr>
                </thead>
                
            </table>
        @endverbatim
    </script>

    <script id="details-motivo" type="text/x-handlebars-template">
        @verbatim         
            <div class="label label-info">{{ NM_PESSOA }}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Observação</th>
                        <th>Motivo Bloqueio</th>                       
                    </tr>
                </thead>
                <tbody>                   
                    <tr>
                       <td>{{ DS_OBSERVACAO }}</td>
                        <td>{{ DS_LIBERACAO }}</td>                          
                    </tr>
                </tbody>
            </table>
                    
        @endverbatim
    </script>

    <script type="text/javascript">
        var tableContas;
        var template_historico = Handlebars.compile($("#details-item-historico").html());
        var template_motivo = Handlebars.compile($("#details-motivo").html());
        var template_vencimento = Handlebars.compile($("#details-vencimento").html());
        var template_centro_resultado = Handlebars.compile($("#details-centro-resultado").html());

        tableContas = initableContas('table-contas-bloqueadas-pendentes', 'N');


        //Cliques nas tabs
        $('.nav-tabs a[href="#pendentes"]').on('click', function() {
            $('#table-contas-bloqueadas-pendentes').DataTable().destroy();
            tableContas = initableContas('table-contas-bloqueadas-pendentes', 'N');
        });

        $('.nav-tabs a[href="#vistos"]').on('click', function() {
            $('#table-contas-bloqueadas-vistos').DataTable().destroy();
            tableContas = initableContas('table-contas-bloqueadas-vistos', 'S');
        });


        $('tbody').on('click', '.details-control', function() {
            var tr = $(this).closest('tr');
            var row = tableContas.row(tr);

            var tableId = 'conta-' + row.data().NR_LANCAMENTO;
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $(this).removeClass('fa-minus-circle').addClass('fa-plus-circle');
                $('.details-centrocusto').removeClass('btn-close').addClass('btn-open');
                $('.details-motivo').removeClass('btn-close').addClass('btn-open');
            } else {
                // Open this row
                row.child(template_historico(row.data())).show();
                initTableHistorico(tableId, row.data());
                // console.log(row.data());
                tr.addClass('shown');
                $(this).removeClass('fa-plus-circle').addClass('fa-minus-circle');
                tr.next().find('td').addClass('no-padding bg-gray-light')
                $('.details-centrocusto').removeClass('btn-close').addClass('btn-open');
                $('.details-motivo').removeClass('btn-close').addClass('btn-open');
            }
        });

        $('tbody').on('click', '.details-motivo', function() {
            var tr = $(this).closest('tr');
            var row = tableContas.row(tr);

            var nm_pessoa = row.data().NM_PESSOA;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $(this).removeClass('btn-close').addClass('btn-open');
                $('.details-centrocusto').removeClass('btn-close').addClass('btn-open');
                $('.details-control').removeClass('fa-minus-circle').addClass('fa-plus-circle');

            } else {
                // Open this row
                row.child(template_motivo(row.data())).show();
                // console.log(row.data());
                tr.addClass('shown');
                tr.next().find('td').addClass('no-padding bg-gray-light')
                $('.details-centrocusto').removeClass('btn-close').addClass('btn-open');
                $('.details-control').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                $(this).removeClass('btn-open').addClass('btn-close');
            }
        });

        $('tbody').on('click', '.details-centrocusto', function() {
            var tr = $(this).closest('tr');
            var row = tableContas.row(tr);
            var tableId = 'conta-' + row.data().NR_LANCAMENTO;

            var nm_pessoa = row.data().NM_PESSOA;

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $(this).removeClass('btn-close').addClass('btn-open');
                $('.details-motivo').removeClass('btn-close').addClass('btn-open');
                $('.details-control').removeClass('fa-minus-circle').addClass('fa-plus-circle');

            } else {
                // Open this row
                row.child(template_centro_resultado(row.data())).show();
                initTableCentroResultado(tableId, row.data());

                tr.addClass('shown');
                tr.next().find('td').addClass('no-padding bg-gray-light')
                $('.details-motivo').removeClass('btn-close').addClass('btn-open');
                $('.details-control').removeClass('fa-minus-circle').addClass('fa-plus-circle');
                $(this).removeClass('btn-open').addClass('btn-close');
            }
        });

        $('.btn-aproover').click(function() {
            var dsLiberacao = $('#liberacao').val(); // Captura o valor do textarea       

            if (dsLiberacao == "") {
                alert("Motivo da liberação/Bloqueio e obrigatorio!");
                return false;
            }
            // libera a conta para pagamento
            loadData('N', dsLiberacao)
        });

        $('.btn-blocker').click(function() {
            var dsLiberacao = $('#liberacao').val(); // Captura o valor do textarea     
            if (dsLiberacao == "") {
                alert("Motivo da liberação/Bloqueio e obrigatorio!");
                return false;
            }
            // Mantem a conta bloqueada, mas muda a conta a aba bloqueadas pendentes.
            loadData('S', dsLiberacao)
        });

        $('#filtro-empresa').on('keyup', function() {
            tableContas.column(2).search(this.value).draw();
        });        
        $('#filtro-nome').on('keyup', function() {
            tableContas.column(3).search(this.value).draw();
        });
        $('#filtro-docto').on('keyup', function() {
            tableContas.column(4).search(this.value).draw();
        });


        function loadData(status, dsLiberacao) {
            var rows = tableContas.rows({
                selected: true
            }).data().toArray();

            var contas = [];

            if (rows.length > 0) {
                rows.forEach(function(row) {
                    contas.push({
                        nr_lancamento: row.NR_LANCAMENTO,
                        cd_empresa: row.CD_EMPRESA,
                        status: status,
                        ds_liberacao: row.DS_LIBERACAO,
                    });

                });
                $.ajax({
                    method: "post",
                    url: "{{ route('contas-bloqueadas.update') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        contas: contas,
                        ds_liberacao: dsLiberacao
                    },
                    beforeSend: function() {
                        $("#loading").removeClass('hidden');
                    },
                    success: function(response) {
                        $("#loading").addClass('hidden');
                        if (response.success) {
                            msgToastr(response.success, 'success');
                        } else {
                            msgToastr(response.warning, 'warning');
                        }
                        $('#table-contas-bloqueadas-pendentes').DataTable().ajax.reload();
                        $('#table-contas-bloqueadas-vistos').DataTable().ajax.reload();
                    }
                });

            } else {
                alert('Nenhuma conta foi selecionada!');
            }
        };

        function initTableCentroResultado(tableId, data) {
            var tableHistorico = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ordering: false,
                ajax: {
                    method: "POST",
                    url: " {{ route('centroresultado-contas-bloqueadas.list') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        nr_lancamento: data.NR_LANCAMENTO,
                        cd_empresa: data.CD_EMPRESA
                    }
                },
                columns: [{
                        data: 'DS_CENTROCUSTO',
                        name: 'DS_CENTROCUSTO',
                    },
                    {
                        data: 'VL_CENTROCUSTO',
                        name: 'VL_CENTROCUSTO',
                    }
                ],
                columnDefs: [{
                    targets: [1],
                    render: $.fn.dataTable.render.number('.', ',', 2),
                }],
                "footerCallback": function(tfoot, data, start, end, display) {
                    $(tfoot).find('td').removeClass('no-padding');
                }

            });
        };

        function initableContas(tableID, status) {
            tableContas = $('#' + tableID).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": true,
                "paging": false,
                "bInfo": false,
                orderCellsTop: true,
                processing: false,
                serverSide: false,
                scrollX: true,
                scrollY: '50vh',
                select: {
                    style: 'multi',
                },
                ajax: {
                    url: '{{ route('contas-bloqueadas.list') }}',
                    data: {
                        st_visto: status,
                    }
                },
                columns: [{
                        data: null,
                        "width": "1%",
                        render: DataTable.render.select(),
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        serachable: false,
                        sClass: 'text-center',
                    }, {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        "width": "1%",
                        visible: true,
                    },
                    {
                        data: 'NM_PESSOA',
                        name: 'NM_PESSOA',
                        visible: true,
                    },
                    {
                        data: 'NR_DOCUMENTO',
                        name: 'NR_DOCUMENTO',
                        visible: true,
                    },
                    {
                        data: 'DS_TIPOCONTA',
                        name: 'DS_TIPOCONTA',
                        visible: false,
                    },
                    {
                        data: 'VL_DOCUMENTO',
                        name: 'VL_DOCUMENTO',
                        visible: true,
                    },
                    {
                        data: 'DT_LANCAMENTO',
                        name: 'DT_LANCAMENTO',
                        visible: true,
                    },
                    {
                        data: 'DS_LIBERACAO',
                        name: 'DS_LIBERACAO',
                        visible: false,
                    },
                ],
                columnDefs: [{
                    targets: [7],
                    render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                }, {
                    targets: [6],
                    render: $.fn.dataTable.render.number('.', ',', 2),
                }],
                order: [
                    [2, 'asc']
                ],

                rowCallback: function(row, data) {
                    $(row).attr('data-toggle', 'tooltip').attr('title', data['DS_OBSERVACAO']);
                }

            });
            return tableContas;
        };

        function initTableHistorico(tableId, data) {
            var tableHistorico = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                "bInfo": false,
                processing: false,
                serverSide: false,
                ordering: false,
                ajax: {
                    method: "POST",
                    url: " {{ route('historico-contas-bloqueadas.list') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        nr_lancamento: data.NR_LANCAMENTO,
                        cd_empresa: data.CD_EMPRESA
                    }
                },
                columns: [{
                        data: 'DS_HISTORICO',
                        name: 'DS_HISTORICO',


                    },
                    {
                        data: 'NR_PARCELA',
                        name: 'NR_PARCELA',
                        width: '1%'
                    },
                    {
                        data: 'DT_LANCAMENTO',
                        name: 'DT_LANCAMENTO',
                    },
                    {
                        data: 'DT_VENCIMENTO',
                        name: 'DT_VENCIMENTO',
                    },
                    {
                        data: 'VL_DOCUMENTO',
                        name: 'VL_DOCUMENTO'

                    }
                ],
                columnDefs: [{
                        targets: [2, 3],
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    },
                    {
                        targets: [4],
                        render: $.fn.dataTable.render.number('.', ',', 2),
                    }
                ],
                "footerCallback": function(tfoot, data, start, end, display) {
                    $(tfoot).find('td').removeClass('no-padding');
                }

            });
        };
    </script>
@stop
