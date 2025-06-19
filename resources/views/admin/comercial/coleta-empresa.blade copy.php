@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select name="cd_empresa" id="cd_empresa" class="form-control" style="width: 100%;">
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
                                    <label>Dt Emissão</label>
                                    <input type="text" class="form-control" id="daterange" placeholder="Data Emissão">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido Palm</label>
                                    <input type="number" class="form-control" id="pedido_palm" placeholder="Pedido Palm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Pedido</label>
                                    <input type="number" class="form-control" id="pedido" placeholder="Pedido">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grupo Item</label>
                                    <select name="grupo_item" id="grupo_item" class="form-control" style="width: 100%;">
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
                                    <label>Região</label>
                                    <select name="cd_regiaocomercial[]" class="form-control" id="cd_regiaocomercial"
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
                <div class="row">
                    @foreach ($empresa as $e)
                        <div class="col-md-6">
                            <div class="card card-danger card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="acompanhamento-1" data-toggle="pill"
                                                href="#acompanhamento-{{ $e->CD_EMPRESA }}" role="tab"
                                                aria-controls="acompanhamento-pedido"
                                                aria-selected="true">{{ $e->NM_EMPRESA }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="row">
                                            <!-- inputs ocultos -->
                                            <input type="hidden" id="click-dt-inicio-{{ $e->CD_EMPRESA }}" value="">
                                            <input type="hidden" id="click-dt-fim-{{ $e->CD_EMPRESA }}" value="">

                                            <div class="col-sm-6 col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon">
                                                        <i id="icon-anteontem-{{ $e->CD_EMPRESA }}"
                                                            class="fas fa-sort-amount-up-alt"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <a class="btn-anteontem" href="#"
                                                            data-cd_empresa="{{ $e->CD_EMPRESA }}"><span
                                                                class="info-box-text">AnteOntem</span></a>
                                                        <span
                                                            class="info-box-number qt-ante-ontem-{{ $e->CD_EMPRESA }}"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon">
                                                        <i id="icon-ontem-{{ $e->CD_EMPRESA }}"
                                                            class="fas fa-sort-amount-up-alt"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <a class="btn-ontem" href="#"
                                                            data-cd_empresa="{{ $e->CD_EMPRESA }}"><span
                                                                class="info-box-text">Ontem</span></a>
                                                        <span
                                                            class="info-box-number qt-ontem-{{ $e->CD_EMPRESA }}"></span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="info-box">
                                                    <span class="info-box-icon">
                                                        <i id="icon-hoje-{{ $e->CD_EMPRESA }}"
                                                            class="fas fa-sort-amount-up-alt"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <a class="btn-hoje" href="#"
                                                            data-cd_empresa="{{ $e->CD_EMPRESA }}">
                                                            <span class="info-box-text">Hoje</span></a>
                                                        <span class="info-box-number qt-hoje-{{ $e->CD_EMPRESA }}"></span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="tab-pane fade active show" id="acompanhamento-{{ $e->CD_EMPRESA }}"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <div class="float-right mb-2">
                                                <small class="badge badge-danger badge-date-{{ $e->CD_EMPRESA }}"></small>
                                            </div>

                                            <table class="table stripe compact nowrap"
                                                id="coleta-empresa-{{ $e->CD_EMPRESA }}"
                                                style="width:100%; font-size:12px">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="modal fade" id="modal-detalhes-pedido" tabindex="-1" role="dialog"
                    aria-labelledby="modal-default-label" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header py-2">
                                <h5 class="modal-title">Detalhes Pedido
                                    <span class="badge badge-danger" id="badge-num-pedido"></span>
                                    <span class="badge badge-info" id="badge-dt-sinc"></span>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body pt-2 pb-1">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-2">
                                            <label for="nomePessoa" class="mb-0">Pessoa:</label>
                                            <input type="text" class="form-control form-control-sm" id="nomePessoa"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-2">
                                            <label for="pedidoPalm" class="mb-0">Pedido Palm</label>
                                            <input type="text" class="form-control form-control-sm" id="pedidoPalm"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-2">
                                            <label for="pedido" class="mb-0">Pedido</label>
                                            <input type="text" class="form-control form-control-sm" id="pedidoColeta"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="condicaoDetails" class="mb-0">Condição Pagamento:</label>
                                        <input type="text" class="form-control form-control-sm mb-2"
                                            id="condicaoDetails" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="formaDetails" class="mb-0">Forma Pagamento:</label>
                                        <input type="text" class="form-control form-control-sm mb-2" id="formaDetails"
                                            readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dtEmissao" class="mb-0">Data Emissão:</label>
                                        <input type="text" class="form-control form-control-sm mb-2" id="dtEmissao"
                                            readonly>
                                    </div>
                                      <div class="col-md-3">
                                        <label for="dtEntrega" class="mb-0">Data Entrega:</label>
                                        <input type="text" class="form-control form-control-sm mb-2" id="dtEntrega"
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="observacaoDetails" class="mb-0">Observação:</label>
                                    <textarea class="form-control form-control-sm" id="observacaoDetails" rows="2" readonly></textarea>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-sm compact" id="item-pedido"
                                        style="width:100%; font-size:12px">
                                        <thead>
                                            <tr>
                                                <th>Sq</th>
                                                <th>Nr Ordem</th>
                                                <th>Serviço</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer p-2">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        table.dataTable thead tr {
            background-color: #444B53;
            color: #ffffff;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 30%;
        }

        table.dataTable {
            table-layout: fixed;
        }

        div.dt-container div.dt-layout-row div.dt-layout-cell.dt-layout-end {

            display: none;
        }
    </style>
@endsection

@section('js')
    <script id="details-pedido-vendedor" type="text/x-handlebars-template">
        @verbatim            
            <table class="table-pedido stripe row-border" id="pedido-{{ IDVENDEDOR }}" style="width:100%">
                
            </table>
        @endverbatim
    </script>
    <script id="details-pedido-cliente" type="text/x-handlebars-template">
        @verbatim            
            <table class="table stripe row-border no-padding" id="item-pedido-{{ ID }}" style="width:100%">
                <thead>
                    <tr>                       
                        <th>Sq</th>
                        <th>Nr Ordem</th>
                        <th>Serviço</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script id="details-item-pedido" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ NRORDEM }} - {{DSSERVICO}}</span>
            <table class="table str row-border" id="item-pedido-{{ ID }}" style="width:100%">
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
    <script type="text/javascript">
        var details_pedido_vendedor = Handlebars.compile($("#details-pedido-vendedor").html());
        var details_pedido_cliente = Handlebars.compile($("#details-pedido-cliente").html());
        var details_item_pedido = Handlebars.compile($("#details-item-pedido").html());
        var regiao;
        var table;
        var dados;

        $('#grupo_item').select2({
            placeholder: 'Selecione o grupo',
            theme: 'bootstrap4',
        });
        $('#cd_regiaocomercial').select2({
            theme: 'bootstrap4',
        });

        $('#pedido-acompanhar').DataTable().destroy();

        var tableEmpresa1 = setTimeout(() => initTableColetaGeral(1, 'coleta-empresa-1', moment().format('DD.MM.YYYY'),
            moment().format('DD.MM.YYYY')), 0);
        var tableEmpresa3 = setTimeout(() => initTableColetaGeral(3, 'coleta-empresa-3', moment().format('DD.MM.YYYY'),
            moment().format('DD.MM.YYYY')), 300);
        var tableEmpresa5 = setTimeout(() => initTableColetaGeral(5, 'coleta-empresa-5', moment().format('DD.MM.YYYY'),
            moment().format('DD.MM.YYYY')), 600);
        var tableEmpresa6 = setTimeout(() => initTableColetaGeral(6, 'coleta-empresa-6', moment().format('DD.MM.YYYY'),
            moment().format('DD.MM.YYYY')), 900);

        $('#searchRegiao').click(function() {
            $('#pedido-acompanhar').DataTable().destroy();

            dados = {
                cd_empresa: $('#cd_empresa').val(),
                nm_cliente: $('#nm_cliente').val(),
                nm_vendedor: $('#nm_vendedor').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: inicioData,
                dt_final: fimData,
                regiao: $('#cd_regiaocomercial').val()
            };

            initTableVendedor(dados);
        });

        //Aguarda Click para buscar os detalhes dos pedidos dos vendedores
        configurarDetalhesLinha('.details-control-pedido', {
            idPrefixo: 'pedido-',
            idCampo: 'IDVENDEDOR',
            templateFn: details_pedido_vendedor,
            initFn: initTableVendedor,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle'
        });

        configurarDetalhesLinha('.details-control', {
            idPrefixo: 'item-pedido-',
            idCampo: 'ID',
            templateFn: details_pedido_cliente,
            initFn: initTablePedidoCliente,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle'
        });

        $(document).on('click', '.btn-anteontem', function(e) {
            e.preventDefault();

            const cd_empresa = $(this).data('cd_empresa');
            const tableId = 'coleta-empresa-' + cd_empresa;
            const inicio = moment().subtract(2, 'days').format('DD.MM.YYYY');
            const fim = moment().subtract(2, 'days').format('DD.MM.YYYY');

            $('#' + tableId).DataTable().destroy();

            initTableColetaGeral(cd_empresa, tableId, inicio, fim, 1);
        });

        $(document).on('click', '.btn-hoje', function(e) {
            e.preventDefault();

            const cd_empresa = $(this).data('cd_empresa');
            const tableId = 'coleta-empresa-' + cd_empresa;
            const inicio = moment().format('DD.MM.YYYY');
            const fim = moment().format('DD.MM.YYYY');

            $('#' + tableId).DataTable().destroy();

            initTableColetaGeral(cd_empresa, tableId, inicio, fim, 1);

        });

        $(document).on('click', '.btn-ontem', function(e) {
            e.preventDefault();

            const cd_empresa = $(this).data('cd_empresa');
            const tableId = 'coleta-empresa-' + cd_empresa;
            const inicio = moment().subtract(1, 'days').format('DD.MM.YYYY');
            const fim = moment().subtract(1, 'days').format('DD.MM.YYYY');

            $('#' + tableId).DataTable().destroy();

            initTableColetaGeral(cd_empresa, tableId, inicio, fim, 1);
        });

        $(document).on('click', '.btn-show-modal', function(e) {
            e.preventDefault();

            $('#item-pedido').DataTable().destroy();
            $('#modal-detalhes-pedido').modal('show');
            const dt_sinc = formatDate($(this).data('dt_sincronizacao'));
            
            $('#badge-num-pedido').text('#' + $(this).data('pedido'));
            $('#badge-dt-sinc').text("Sinc: " + dt_sinc);

            $('#nomePessoa').val($(this).data('nm_pessoa'));
            $('#condicaoDetails').val($(this).data('cond_pagamento'));
            $('#formaDetails').val($(this).data('forma_pagamento'));
            $('#observacaoDetails').val($(this).data('observacao'));

            $('#pedidoPalm').val($(this).data('pedido_palm'));
            $('#pedidoColeta').val($(this).data('pedido'));   
            $('#dtEmissao').val($(this).data('dt_emissao'));
            $('#dtEntrega').val($(this).data('dt_entrega'));          
           
           
            

            initTablePedidoCliente('item-pedido', {
                ID: $(this).data('pedido')
            });

        });

        function formatDate(value) {
            if (!value) return '';
            const date = new Date(value);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
        }

        function configurarDetalhesLinha(selector, options) {
            $(document).on('click', selector, function() {
                const tr = $(this).closest('tr');
                const table = tr.closest('table');
                const tableId = table.attr('id');
                const row = $('#' + tableId).DataTable().row(tr);

                const data = row.data();
                const tableChildId = options.idPrefixo + (options.idCampo ? data[options.idCampo] : data.ID);

                if (row.child.isShown()) { // Se a linha já está expandida
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).find('i').removeClass(options.iconeMenos).addClass(options.iconeMais);
                } else { // Se a linha não está expandida
                    row.child(options.templateFn(data)).show();
                    options.initFn(tableChildId, data);
                    tr.addClass('shown');
                    $(this).find('i').removeClass(options.iconeMais).addClass(options.iconeMenos);
                    tr.next().find('td').addClass('no-padding');
                }
            });
        }

        function initTableColetaGeral(empresaId, tableId, inicio, fim, tipo) {

            if (tipo !== 1) {
                getQtdColetaDia(empresaId).then(data => {

                    const anteontem = data[0].QTDPNEUS_ANTEONTEM;
                    const ontem = data[0].QTDPNEUS_ONTEM;
                    const hoje = data[0].QTDPNEUS_HOJE;

                    $('.qt-ante-ontem-' + empresaId).text(anteontem);
                    $('.qt-ontem-' + empresaId).text(ontem);
                    $('.qt-hoje-' + empresaId).text(hoje);

                    if (parseInt(hoje) < parseInt(ontem)) {                        
                        $('#icon-hoje-' + empresaId).removeClass('fa-sort-amount-up-alt text-success');
                        $('#icon-hoje-' + empresaId).addClass('fa-sort-amount-down-alt text-danger');

                    } else {
                        $('#icon-hoje-' + empresaId).addClass('fa-sort-amount-up-alt text-success');
                        $('#icon-hoje-' + empresaId).removeClass('fa-sort-amount-down-alt text-danger');

                    }
                    if (parseInt(ontem) < parseInt(anteontem)) {
                        $('#icon-ontem-' + empresaId).removeClass('fa-sort-amount-up-alt text-success');
                        $('#icon-ontem-' + empresaId).addClass('fa-sort-amount-down-alt text-danger');

                    } else {
                        $('#icon-ontem-' + empresaId).addClass('fa-sort-amount-up-alt text-success');
                        $('#icon-ontem-' + empresaId).removeClass('fa-sort-amount-down-alt text-danger');
                    }


                }).catch(error => {
                    console.error("Erro ao obter a quantidade de pedidos:", error);
                });
            }

            $('.badge-date-' + empresaId).text('Periodo: ' + inicio + ' a ' + fim);

            //Salvas as informações no input oculto para poder reaproveitar na consulta
            $('#click-dt-inicio-' + empresaId).val(inicio);
            $('#click-dt-fim-' + empresaId).val(fim);

            dados = {
                cd_empresa: empresaId,
                nm_cliente: $('#nm_cliente').val(),
                nm_vendedor: $('#nm_vendedor').val(),
                pedido_palm: $('#pedido_palm').val(),
                pedido: $('#pedido').val(),
                grupo_item: $('#grupo_item').val(),
                cd_regiaocomercial: $('#cd_regiaocomercial').val(),
                dt_inicial: inicio,
                dt_final: fim,
            };

            const table = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                pagingType: "simple",
                processing: false,
                serverSide: false,
                pageLength: 25,
                retrieve: true,
                scrollY: '400px',
                ajax: {
                    url: "{{ route('get-coleta-empresa-geral') }}",
                    data: {
                        data: dados
                    }
                },
                columns: [{
                        title: "",
                        data: 'actions',
                        name: 'actions',
                        "width": "1%"
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        title: "Empresa",
                        "width": "1%",
                        visible: false
                    },
                    {
                        data: 'NM_VENDEDOR',
                        title: "Vendedor",
                        width: "20%",
                        name: 'ID',
                        visible: true
                    },
                    {
                        data: 'BLOQUEADAS',
                        title: "Bloq.",
                        name: 'BLOQUEADAS',
                        "width": "1%"
                    },
                    {
                        data: 'QTDPEDIDOS',
                        title: "Pedidos",
                        name: 'QTDPEDIDOS',
                        "width": "1%",
                        visible: false

                    },
                    {
                        data: 'QTDPNEUS',
                        title: "Pneus",
                        name: 'QTDPNEUS',
                        "width": "1%"
                    },
                    {
                        data: 'VALOR_MEDIO',
                        title: "Vlr Médio",
                        name: 'VALOR_MEDIO',
                        "width": "1%"
                    }
                ],
                columnDefs: [{
                        targets: [6],
                        className: 'dt-right',
                        render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                    },
                    {
                        targets: 2, // índice da coluna que você quer truncar
                        className: 'text-truncate'
                    }

                ],

                footerCallback: function(row, data, start, end, display) {


                }
            });
            return table;
        }

        function initTableVendedor(tableId, dados) {

            dados = {
                cd_empresa: dados.CD_EMPRESA,
                idvendedor: dados.IDVENDEDOR,
                pedido: "",
                pedido_palm: "",
                nm_cliente: "",
                nm_vendedor: "",
                grupo_item: 0,
                dt_inicial: moment().format('DD.MM.YYYY'),
                dt_final: moment().format('DD.MM.YYYY')

            }

            if ($('#click-dt-inicio-' + dados.cd_empresa).val() !== '') {
                dados.dt_inicial = $('#click-dt-inicio-' + dados.cd_empresa).val();
                dados.dt_final = $('#click-dt-fim-' + dados.cd_empresa).val();
            }


            table = $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                paging: false,
                sDom: 't',
                processing: false,
                serverSide: false,
                retrieve: true,
                searching: false,
                scrollX: true,
                ajax: {
                    url: "{{ route('get-pedido-acompanhar') }}",
                    data: {
                        data: dados
                    }
                },
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        title: "",
                        "width": "1%"
                    },
                    {
                        data: 'CD_EMPRESA',
                        name: 'CD_EMPRESA',
                        title: "Emp",
                        "width": "1%",
                        visible: false
                    },
                    {
                        data: 'ID',
                        name: 'ID',
                        title: "Pedido",
                        visible: false
                    },
                    {
                        data: 'IDPEDIDOMOVEL',
                        name: 'IDPEDIDOMOVEL',
                        visible: true,
                        title: "Pedido Palm",
                       
                    },
                    {
                        data: 'PESSOA',
                        name: 'PESSOA',
                        title: "Cliente",
                        "width": "30%"
                    },
                    {
                        data: 'VALOR_MEDIO',
                        name: 'VALOR_MEDIO',
                        title: "P. Médio",
                        "width": "1%"
                    },
                    {
                        data: 'QTDPNEUS',
                        name: 'QTDPNEUS',
                        title: "Pneus",
                        "width": "1%"
                    },
                    {
                        data: 'DTEMISSAO',
                        name: 'DTEMISSAO',
                        title: "Dt Emissão",
                        visible: false
                    },
                    {
                        data: 'DTENTREGAPED',
                        name: 'DTENTREGAPED',
                        title: "Dt Entrega",
                        visible: false
                    },
                    {
                        data: 'STPEDIDO',
                        title: "Status",
                        name: 'STPEDIDO',
                        visible: false
                    },
                    {
                        data: 'MOTIVO',
                        name: 'MOTIVO',
                        title: "Bloqueio",
                    },
                    {
                        data: 'DSTIPOPEDIDO',
                        name: 'DSTIPOPEDIDO',
                        title: "Tipo Pedido"
                    }
                ],
                columnDefs: [{
                    targets: [5],
                    className: 'dt-right',
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }],
                "order": [6, 'desc'],
                footerCallback: function(row, data, start, end, display) {


                }
            });
            return table;
        }

        function initTablePedidoCliente(tableId, data) {
           
            return $('#' + tableId).DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json",
                },
                "searching": false,
                "paging": false,
                sDom: 't',
                processing: false,
                serverSide: false,
                ajax: {
                    method: "GET",
                    url: " {{ route('get-item-pedido-acompanhar') }}",
                    data: {
                        _token: $("[name=csrf-token]").attr("content"),
                        id: data.ID
                    }
                },
                columns: [{
                        data: 'NRSEQUENCIA',
                        name: 'NRSEQUENCIA',
                        "width": "1%"
                    },
                    {
                        data: 'NRORDEM',
                        name: 'NRORDEM'
                    },
                    {
                        data: 'DSSERVICO',
                        name: 'DSSERVICO'
                    },
                    {
                        data: 'VLUNITARIO',
                        name: 'VLUNITARIO'
                    }, {
                        data: 'STORDEM',
                        name: 'STORDEM',
                    },
                ],
                columnDefs: [{
                    targets: [3],
                    className: 'dt-right',
                    render: $.fn.dataTable.render.number('.', ',', 2, 'R$ ')
                }],
            });
        }

        function getQtdColetaDia(cd_empresa) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "{{ route('get-qtd-coleta') }}",
                    method: "GET",
                    data: {
                        cd_empresa: cd_empresa
                    },
                    success: function(data) {
                        resolve(data);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        };
    </script>
@stop
