@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="content-fluid">
            <div class="row mb-2">
                <div class="col-12 col-sm-4 col-md-3 mb-2">
                    <div class="stat-card stat-primary">
                        <div class="stat-title"><i class="fas fa-file-invoice-dollar"></i> Total Bloqueadas</div>
                        <div class="stat-value"><span id="qtd-bloqueadas">0</span> <small style="font-size:.7rem;font-weight:400;">Contas</small></div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Valor</span>
                                <span class="stat-row-val" id="valor-bloqueadas">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-3 mb-2">
                    <div class="stat-card stat-info">
                        <div class="stat-title"><i class="fas fa-clock"></i> Aguardando Análise</div>
                        <div class="stat-value"><span id="qtd-aguardando-analise">0</span> <small style="font-size:.7rem;font-weight:400;">Contas</small></div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Valor</span>
                                <span class="stat-row-val" id="valor-aguardando-analise">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4 col-md-3 mb-2">
                    <div class="stat-card stat-warning">
                        <div class="stat-title"><i class="fas fa-exclamation-triangle"></i> Pendentes Bloqueadas</div>
                        <div class="stat-value"><span id="qtd-pendentes-bloqueadas">0</span> <small style="font-size:.7rem;font-weight:400;">Contas</small></div>
                        <div class="stat-rows">
                            <div class="stat-row">
                                <span class="stat-row-label">Valor</span>
                                <span class="stat-row-val" id="valor-pendentes-bloqueadas">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1 text-muted"></i> Filtros</h3>
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
                            <input id="filtro-empresa" type="text" class="form-control form-control-sm"
                                placeholder="Filtrar por Empresa">
                        </div>
                        <div class="col-md-4 mb-1">
                            <input id="filtro-nome" type="text" class="form-control form-control-sm" placeholder="Filtrar por Pessoa">
                        </div>
                        <div class="col-md-2 mb-1">
                            <input id="filtro-docto" type="text" class="form-control form-control-sm"
                                placeholder="Filtrar por Documento">
                        </div>
                        <div class="col-md-2 mb-1">
                            <input id="filtro-data" type="text" class="form-control form-control-sm"
                                placeholder="Filtrar por Vencimento">
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <div class="card card-danger card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#pendentes" data-toggle="tab" aria-expanded="true">Aguardando
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
                            <table class="table stripe compact" id="table-contas-bloqueadas-pendentes" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" class="dt-select-all-contas" title="Selecionar todos" style="margin:0;"></th>
                                        <th>#</th>
                                        <th>Emp</th>
                                        <th>Pessoa</th>
                                        <th>Docto</th>
                                        <th>Parcelas</th>
                                        <th>Total</th>
                                        <th>Emissão</th>
                                        <th>Vencimento</th>
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
                                        <th><input type="checkbox" class="dt-select-all-contas" title="Selecionar todos" style="margin:0;"></th>
                                        <th>#</th>
                                        <th>Emp</th>
                                        <th>Pessoa</th>
                                        <th>Docto</th>
                                        <th>Parcelas</th>
                                        <th>Total</th>
                                        <th>Emissão</th>
                                        <th>Vencimento</th>
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
                <div class="row mb-5">
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
    </section>
@stop
@section('css')
    <style>
        .stat-card { background:#fff; border:1px solid rgba(0,0,0,.09); border-left:4px solid; border-radius:4px; padding:10px 12px; height:100%; position:relative; }
        .stat-card .stat-title { font-size:.68rem; text-transform:uppercase; letter-spacing:.4px; color:#6c757d; display:flex; align-items:center; gap:5px; margin-bottom:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .stat-card .stat-title i { font-size:.7rem; }
        .stat-card .stat-value { font-size:1rem; font-weight:700; word-break:break-all; line-height:1.3; }
        .stat-card .stat-rows { margin-top:1px; }
        .stat-card .stat-row { display:flex; justify-content:space-between; align-items:baseline; font-size:.71rem; padding:2px 0; border-top:1px solid rgba(0,0,0,.05); }
        .stat-card .stat-row-label { color:#6c757d; flex-shrink:0; }
        .stat-card .stat-row-val { font-weight:600; text-align:right; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:58%; }
        .stat-primary { border-left-color:#007bff; } .stat-primary .stat-title i,.stat-primary .stat-value { color:#007bff; }
        .stat-info    { border-left-color:#17a2b8; } .stat-info .stat-title i,.stat-info .stat-value    { color:#17a2b8; }
        .stat-warning { border-left-color:#e0a800; } .stat-warning .stat-title i,.stat-warning .stat-value { color:#c89100; }

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
@stop
@section('js')
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
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
            <div class="badge badge-danger">{{ DS_TIPOCONTA }}</div>
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
           <div class="badge badge-danger">{{ DS_TIPOCONTA }}</div>
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
            <div class="badge badge-danger">{{ NM_PESSOA }}</div>
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

        $('#table-contas-bloqueadas-pendentes').DataTable().destroy();

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
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo da liberação/Bloqueio é obrigatório!',
                    showConfirmButton: false,
                    timer: 2000
                });
                return false;
            }
            // libera a conta para pagamento
            loadData('N', dsLiberacao)
        });

        $('.btn-blocker').click(function() {
            var dsLiberacao = $('#liberacao').val(); // Captura o valor do textarea     
            if (dsLiberacao == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Motivo da liberação/Bloqueio é obrigatório!',
                    showConfirmButton: false,
                    timer: 2000
                });
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
        $('#filtro-data').on('keyup', function() {
            tableContas.column(8).search(this.value).draw();
        });


        function loadData(status, dsLiberacao) {
            var contas = [];

            tableContas.rows().nodes().to$().each(function() {
                if ($(this).find('.dt-row-checkbox-contas').is(':checked')) {
                    var row = tableContas.row(this).data();
                    if (row) {
                        contas.push({
                            nr_lancamento: row.NR_LANCAMENTO,
                            cd_empresa: row.CD_EMPRESA,
                            status: status,
                            ds_liberacao: row.DS_LIBERACAO,
                        });
                    }
                }
            });

            if (contas.length > 0) {
                $.ajax({
                    method: "post",
                    url: "{{ route('contas-bloqueadas.update') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        contas: contas,
                        ds_liberacao: dsLiberacao
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processando...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.success,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: response.warning,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                        $('#table-contas-bloqueadas-pendentes').DataTable().ajax.reload();
                        $('#table-contas-bloqueadas-vistos').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocorreu um erro ao processar a solicitação.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });

            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma conta selecionada',
                    text: 'Selecione pelo menos 1 conta para continuar.',
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-warning',
                    },
                });
                return;
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
                ajax: {
                    url: '{{ route('contas-bloqueadas.list') }}',
                    data: {
                        st_visto: status,
                    },
                    beforeSend: function() {
                        window._swalContasTimer = setTimeout(function() {
                            Swal.fire({
                                title: 'Carregando contas...',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                        }, 400);
                    },
                    complete: function() {
                        clearTimeout(window._swalContasTimer);
                        Swal.close();
                    },
                    dataSrc: function(json) {
                        $('#qtd-bloqueadas').text(json.qtd_bloqueadas);
                        $('#valor-bloqueadas').text(json.vlr_bloqueadas);

                        $('#qtd-aguardando-analise').text(json.qtd_aguardando_analise);
                        $('#valor-aguardando-analise').text(json.vlr_aguardando_analise);

                        $('#qtd-pendentes-bloqueadas').text(json.qtd_pendentes_bloqueadas);
                        $('#valor-pendentes-bloqueadas').text(json.vlr_pendentes_bloqueadas);


                        return json.datatables.data.filter(item => item.ST_VISTO === status);
                    }
                },
                columns: [{
                        data: null,
                        width: "30px",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<input type="checkbox" class="dt-row-checkbox-contas" data-lancamento="' +
                                    row.NR_LANCAMENTO + '" data-empresa="' + row.CD_EMPRESA +
                                    '" aria-label="Selecionar linha" style="margin:0;">';
                            }
                            return '';
                        },
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
                        data: 'DT_VENCIMENTO',
                        name: 'DT_VENCIMENTO',
                        visible: true,
                    },
                    {
                        data: 'DS_LIBERACAO',
                        name: 'DS_LIBERACAO',
                        visible: false,
                    },
                ],
                columnDefs: [{
                    targets: [7, 8],
                    render: function(data, type, row) {
                        if (!data) return '';
                        var d = new Date(data);
                        if (isNaN(d)) return data;
                        return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-
                            2) + '/' + d.getFullYear();
                    }
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
                        render: function(data, type, row) {
                            if (!data) return '';
                            var d = new Date(data);
                            if (isNaN(d)) return data;
                            return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-
                                2) + '/' + d.getFullYear();
                        }
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

        // Select all — usa API DataTables para acessar os nós reais das linhas
        $(document).on('click', '.dt-select-all-contas', function(e) {
            e.stopPropagation();
            var checked = $(this).is(':checked');
            tableContas.rows().nodes().to$().find('.dt-row-checkbox-contas').prop('checked', checked);
        });

        // Checkbox individual — atualiza estado do select-all
        $(document).on('click', '.dt-row-checkbox-contas', function(e) {
            e.stopPropagation();
            var total = tableContas.rows().count();
            var selected = tableContas.rows().nodes().to$().find('.dt-row-checkbox-contas:checked').length;
            $(this).closest('.dataTables_wrapper').find('.dt-select-all-contas').prop('checked', total > 0 && total === selected);
        });

        $('link[href*="custom_datatables"]').remove();
    </script>
@stop
