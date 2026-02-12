@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabCarcacas" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-carcaca-entrada" data-toggle="pill"
                                    href="#painel-carcaca-entrada" role="tab" aria-controls="painel-carcaca-entrada"
                                    aria-selected="true">
                                    Entradas
                                </a>
                            </li>
                            <li class="nav-item" @if ($canEdit) style="display:none;" @endif>
                                <a class="nav-link" id="tab-carcaca-saida" data-toggle="pill" href="#painel-carcaca-saida"
                                    role="tab" aria-controls="painel-carcaca-saida" aria-selected="false">
                                    Saídas
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="tabContentRelatorio">
                            <div class="tab-pane fade show active" id="painel-carcaca-entrada" role="tabpanel"
                                aria-labelledby="tab-carcaca-entrada">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-8" id="div-tabela-carcacas">
                                            <div class="card-header">
                                                @include('admin.estoque.components.buttons-estoque-carcaca')
                                                <div class="card-tools m-0">
                                                    <button class="btn btn-xs btn-danger" id="btn-add-carcaca"
                                                        title="Adicionar Carcaça"
                                                        @if ($canEdit) style="display:none;" @endif>
                                                        <i class="fas fa-plus"></i></button>
                                                    <button class="btn btn-xs btn-danger" id="download-itens"
                                                        title="Fazer Download"><i class="fas fa-download"></i></button>
                                                </div>
                                            </div>
                                            <div class="card-body pb-0">
                                                <table class="table table-bordered compact table-font-small"
                                                    id="estoque-carcacas">
                                                </table>
                                            </div>
                                            <div class="card-footer pt-0">
                                                @include('admin.estoque.components.buttons-estoque-carcaca')
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="info-box">
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Total</span>
                                                            <span class="info-box-number">
                                                                <span id="total-carcacas"></span>
                                                                <small>Unidades</small>
                                                            </span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                    <!-- /.info-box -->
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Resumo Local</h6>
                                                            <div class="card-tools m-0">
                                                                <button class="btn btn-xs btn-danger"
                                                                    id="download-resumo-local"><i
                                                                        class="fas fa-download"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="accordionResumoLocal" class="d-none"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-12 col-md-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Resumo Marca</h6>
                                                            <div class="card-tools m-0">
                                                                <button class="btn btn-xs btn-danger"
                                                                    id="download-resumo-marca"><i
                                                                        class="fas fa-download"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="accordionResumo" class="d-none"></div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-carcaca-saida" role="tabpanel"
                                aria-labelledby="tab-carcaca-saida">
                                <div class="card-body p-2">
                                    <div class="col-md-8" id="div-tabela-carcacas"
                                        @if ($canEdit) style="display:none;" @endif>
                                        <div class="card-header">
                                            <h6 class="card-title">Baixas</h6>
                                        </div>
                                        <div class="card-body pb-0">
                                            <table class="table table-bordered compact table-font-small table-responsive"
                                                id="estoque-carcacas-baixas">
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
        <div class="modal fade" id="modal-add-carcaca" tabindex="-1" role="dialog"
            aria-labelledby="modal-add-carcaca-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Adicionar Carcaça</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="number" class="d-none" id="id_carcaca" />
                        <div class="form-group">
                            <label for="cd_medida">Medida Carcaca</label>
                            <select class="form-control form-control-sm" id="cd_medida" style="width: 100%">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cd_modelo">Modelo/Marca</label>
                            <select class="form-control form-control-sm" name="cd_modelo" id="cd_modelo"
                                style="width: 100%">
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nr_fogo">Número Fogo</label>
                                    <input type="number" class="form-control form-control-sm" name="nr_fogo"
                                        id="nr_fogo" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nr_serie">Número Série</label>
                                    <input type="text" class="form-control form-control-sm" name="nr_serie"
                                        id="nr_serie" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nr_dot">Número Dot</label>
                                    <input type="text" class="form-control form-control-sm" name="nr_dot"
                                        id="nr_dot" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="vl_carcaca">Valor</label>
                                    <input type="text" class="form-control form-control-sm" name="vl_carcaca"
                                        id="vl_carcaca" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="cd_tipo">Tipo Carcaça</label>
                                    <select class="form-control form-control-sm" name="cd_tipo" id="cd_tipo"
                                        style="width: 100%">
                                        <option value="1" selected="selected">Primeira</option>
                                        <option value="2">Segunda</option>
                                        <option value="3">Terceira</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="cd_tipo">Local Estoque</label>
                                    <select class="form-control form-control-sm" name="cd_local" id="cd_local"
                                        style="width: 100%">
                                        <option value="1" selected="selected">Cambé</option>
                                        <option value="3">Osvaldo Cruz</option>
                                        <option value="5">Ponto Grossa</option>
                                        <option value="6">Catanduva</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary btn-xs d-none"
                            id="btn-save-carcaca">Salvar</button>
                        <button type="button" class="btn btn-warning btn-xs d-none"
                            id="btn-edit-carcaca">Editar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-transferir-carcaca" tabindex="-1" role="dialog"
            aria-labelledby="modal-transferir-carcaca-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Transferir Carcaça</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cd_local_transferir">Local Estoque</label>
                            <select class="form-control form-control-sm" name="cd_local_transferir"
                                id="cd_local_transferir" style="width: 100%">
                                <option value="1" selected="selected">Cambé</option>
                                <option value="3">Osvaldo Cruz</option>
                                <option value="5">Ponto Grossa</option>
                                <option value="6">Catanduva</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary btn-xs" id="btn-transferir-carcaca">Confirmar
                            Transferencia</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-criar-pedido" tabindex="-1" role="dialog"
            aria-labelledby="modal-criar-pedido-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <!-- LOADER -->
                    <div id="modal-loader" class="loading-card invisible loader-overlay">
                        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="modal-header">
                        <h6 class="modal-title">Criar Pedido</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="nm_pessoa" class="form-label small">Empresa</label>
                                    <select name='cd_empresa' class="form-control form-control-sm" id="cd_empresa"
                                        style="width: 100%">
                                        <option value="1" selected="selected">Cambé</option>
                                        <option value="3">Osvaldo Cruz</option>
                                        <option value="5">Ponta Grossa</option>
                                        <option value="6">Cantanduva</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="form-group">
                                    <label for="nm_pessoa" class="form-label small">Cliente</label>
                                    <select name='pessoa' class="form-control form-control-sm" id="pessoa"
                                        style="width: 100%">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="cd_cond_pagto" class="form-label small">Cond. Pagto</label>
                                    <select name='cond_pagto' class="form-control form-control-sm" id="cd_cond_pagto"
                                        style="width: 100%">
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="cd_form_pagto" class="form-label small">Forma Pagto</label>
                                    <select name='form_pagto' class="form-control form-control-sm" id="cd_form_pagto"
                                        style="width: 100%">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <strong class="p-1 small">Itens do Pedido</strong>

                            <div class="col-12 col-md-12" id="itens-pedido">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary btn-xs" id="btn-confirmar-pedido">Confirmar
                            Pedido</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <style>
        .divAccordion {
            margin-bottom: 3px;
        }

        @media (max-width: 768px) {

            .divAccordion {
                margin-bottom: 0px;
                font-size: 13px;
            }

            .btn-sm-phone {
                padding: .25rem .4rem;
                margin-bottom: .4rem;
                font-size: 12px;
            }
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

        .btn-action {
            width: 25px;
            padding: 0;
        }

        .col-nome-header {
            color: #6c757d !important;
        }

        .col-qtd-header {
            width: 15%;
            white-space: nowrap;
            text-align: right;
            color: #6c757d !important;
        }

        .col-valor-header {
            width: 25%;
            text-align: right;
            white-space: nowrap;
            color: #6c757d !important;
        }

        .col-nome {
            font-size: 14px;
            color: #6c757d !important;
        }

        .col-qtd {
            width: 15%;
            font-size: 14px;
            white-space: nowrap;
            text-align: right;
            color: #6c757d !important;
        }

        .col-valor {
            width: 25%;
            font-size: 14px;
            /* font-weight: 600; */
            text-align: right;
            white-space: nowrap;
            color: #6c757d !important;
        }

        @media (max-width: 576px) {

            .indent-1 {
                padding-left: 2px !important;
            }

            .indent-2 {
                padding-left: 4px !important;
            }

            .indent-3 {
                padding-left: 6px !important;
            }

            .indent-4 {
                padding-left: 8px !important;
            }

            .card-title {
                font-size: 16px;
            }

            .tabela tr {
                display: flex;
                flex-wrap: wrap;
            }

            .tabela td {
                padding: 2px 0;
            }

            .col-nome-header {
                width: 20%;
                font-weight: 600;
                font-size: 14px;
            }

            .col-qtd-header {
                text-align: right;
                font-size: 14px;
                font-weight: 600;
                padding-left: 30px;
            }

            .col-valor-header {
                text-align: right;
                font-weight: 600;
                font-size: 14px;
            }

            .col-nome {
                width: 100%;
                font-size: 13px;
            }

            .col-qtd {
                width: 50%;
                text-align: right;
                font-size: 13px;
                font-weight: 600;
                padding-left: 30px;
            }

            .col-valor {
                width: 50%;
                text-align: right;
                font-weight: 600;
                font-size: 13px;
            }
        }

        

        .loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            background: rgba(255, 255, 255, 0.7);

            display: flex;
            align-items: center;
            justify-content: center;

            z-index: 10;
            /* maior que conteúdo interno */
        }
    </style>
@stop
@section('js')
    <script type="text/javascript">
        const routes = {
            searchPessoa: "{{ route('usuario.search-pessoa') }}",
            condicaoPagamento: "{{ route('get-cond-pagamento') }}",
            formaPagamento: "{{ route('get-form-pagamento') }}",
            servicoPneu: "{{ route('get-servico-pneu-medida') }}"
        }

        initSelect2Pessoa('#pessoa', routes.searchPessoa, '#modal-criar-pedido');

        $('#btn-add-carcaca').on('click', function() {
            $('#modal-add-carcaca').modal('show');
            $('#modal-add-carcaca .modal-title').text('Adicionar Carcaça');
            $('#cd_medida').val(null).trigger('change');
            $('#cd_modelo').val(null).trigger('change');
            $('#nr_fogo').val('');
            $('#nr_serie').val('');
            $('#cd_tipo').val('1').change();
            $('#cd_local').val('1').change();
            $('#btn-save-carcaca').removeClass('d-none');
            $('#btn-edit-carcaca').addClass('d-none');
        });

        $("#cd_medida").select2({
            placeholder: "Selecione a Medida",
            theme: "bootstrap4",
            width: "100%",
            allowClear: true,
            dropdownParent: $("#modal-add-carcaca"),
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('search-medidas-pneus') }}",
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.DS_MEDIDA,
                                id: item.ID
                            };
                        }),
                    };
                },
                cache: false,
            },
        });

        $("#cd_modelo").select2({
            placeholder: "Selecione o Modelo",
            theme: "bootstrap4",
            width: "100%",
            allowClear: true,
            dropdownParent: $("#modal-add-carcaca"),
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('search-modelo-pneus') }}",
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.DSMODELO,
                                id: item.IDMODELO
                            };
                        }),
                    };
                },
                cache: false,
            },
        });

        var table_carcaca_itens;

        initTableCarcaca();

        let tabelaCarcacaLocal = agrupaTabelaResumo('estoque-carcacas-local', 'Local');

        let tabelaCarcacaMarca = agrupaTabelaResumo('estoque-carcacas-marca', 'Marca');

        function initTableCarcaca() {
            if (table_carcaca_itens) {
                table_carcaca_itens.destroy();
            }
            table_carcaca_itens = $('#estoque-carcacas').DataTable({
                processing: false,
                serverSide: false,
                responsive: false,
                scrollX: true,
                select: {
                    style: "multi",
                    selector: "td.select-checkbox"
                },
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                pagingType: "simple",
                ajax: {
                    url: '{{ route('get-carcaca-casa') }}',
                    dataSrc: function(response) {

                        $('#total-carcacas').text(response.extra.total_carcacas);                        

                        $('#accordionResumoLocal').html(initAccordion(response.accordion_data_local_marca,
                            'accordionResumoLocal')).removeClass(
                            'd-none');

                        // $('#accordionResumo').html(renderizaAccordion(response.extra.accordion_data,
                        //     'accordionResumo')).removeClass(
                        //     'd-none');

                        preencheTabelaResumo(tabelaCarcacaLocal, response.extra.local_agrupado);
                        preencheTabelaResumo(tabelaCarcacaMarca, response.extra.marca_agrupado);


                        return response.datatable.data;
                    }
                },
                columns: [{
                        data: null,
                        // width: "1%",
                        defaultContent: "",
                        className: "select-checkbox",
                        orderable: false
                    },

                    @if (!$canEdit)
                        {
                            data: 'action',
                            name: 'action',
                            title: 'Ações',
                            orderable: false,
                            searchable: false,
                            // width: '10%',
                            className: 'text-center text-nowrap',
                        },
                    @endif {
                        data: 'ID',
                        name: 'ID',
                        title: 'Cód.',
                        visible: true,
                        className: 'text-center',
                    },
                    {
                        data: 'DSMEDIDAPNEU',
                        name: 'DSMEDIDAPNEU',
                        title: 'Medida'
                    },
                    {
                        data: 'DSMODELO',
                        name: 'DSMODELO',
                        title: 'Modelo'
                    },
                    {
                        data: 'NR_FOGO',
                        name: 'NR_FOGO',
                        visible: false,
                        title: 'Fogo',
                        className: 'text-center',
                    },
                    {
                        data: 'NR_SERIE',
                        name: 'NR_SERIE',
                        title: 'Serie',
                        className: 'text-center',
                    },
                    {
                        data: 'NR_DOT',
                        name: 'NR_DOT',
                        title: 'Dot',
                        className: 'text-center',
                    },
                    {
                        data: 'VL_CARCACA',
                        name: 'VL_CARCACA',
                        title: 'Valor',
                        className: 'text-center',
                        render: $.fn.dataTable.render.number('.', ',', 2),
                    },
                    {
                        data: 'DS_TIPO',
                        name: 'DS_TIPO',
                        title: 'Tipo',
                    },
                    {
                        data: 'LOCAL_ESTOQUE',
                        name: 'LOCAL_ESTOQUE',
                        title: 'Local'
                    }
                ],
                order: [
                    [0, "desc"]
                ],

            });
        }

        function agrupaTabelaResumo(idTabela, colunaAgrupamento) {
            return $('#' + idTabela).DataTable({
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        title: colunaAgrupamento
                    },
                    {
                        title: "Total"
                    }
                ],
                columnDefs: [{
                    targets: 1,
                    className: 'text-center',
                }],
            });
        }

        function preencheTabelaResumo(tabela, dados) {
            tabela.clear();
            for (const chave in dados) {
                tabela.row.add([chave, dados[chave]]);
            }
            tabela.draw();
        }

        function renderizaAccordion(dados, idDivAccordion = 'accordionResumo') {
            let html = '';

            Object.keys(dados).forEach(function(marcaKey, index) {
                let marcaItem = dados[marcaKey];
                let marcaCollapseId = 'collapse' + index;
                html += `
                <div class="card divAccordion">
                    <div class="card-header p-1" id="headingMarca${index}">                        
                        <button class="btn btn-link" 
                                data-toggle="collapse" 
                                data-target="#${marcaCollapseId + idDivAccordion}" 
                                aria-expanded="true" 
                                aria-controls="${marcaCollapseId + idDivAccordion}">
                            <i class="fas fa-chevron-down"></i> ${marcaKey} (${marcaItem.qtd} unidades)
                        </button>                       
                    </div>
                    <div id="${marcaCollapseId + idDivAccordion}" class="collapse" aria-labelledby="headingMarca${index}" data-parent="#${idDivAccordion}">
                        <div class="card-body p-2">`;

                // MEDIDAS
                Object.keys(marcaItem.medida).forEach(function(medidaKey, mIndex) {

                    let medidaItem = marcaItem.medida[medidaKey];
                    let medidaCollapseId = `collapseMedidas${index}-${mIndex}`;
                    html += `
                        <div class="card mb-2">
                            <div class="card-header p-1" id="headingMedida${index}-${mIndex}">
                                <button class="btn btn-link" 
                                        data-toggle="collapse" 
                                        data-target="#${medidaCollapseId + idDivAccordion}" 
                                        aria-expanded="false" 
                                        aria-controls="${medidaCollapseId + idDivAccordion}">
                                    <i class="fas fa-chevron-down"></i> ${medidaKey} (${medidaItem.qtd} unidades)
                                </button>
                            </div>
                         
                            <div id="${medidaCollapseId + idDivAccordion}" 
                            class="collapse" 
                            aria-labelledby="headingMedida${index}-${mIndex}" 
                            data-parent="#${marcaCollapseId + idDivAccordion}">

                            <div class="card mb-2">
                            <div class="card-body p-2">                                                              
                            `;
                    // MODELOS                       
                    Object.keys(medidaItem.modelo).forEach(function(modeloKey) {
                        let modeloItem = medidaItem.modelo[modeloKey];
                        html += `
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>${modeloKey}</span>
                                    <span>${modeloItem.qtd} unid.</span>
                                </div>
                                `;
                    });
                    html += `  
                                </div>     
                            </div>
                        </div> 
                    </div> 
                            `;
                });

                html += `     </div>
                        </div>     
                    </div>
                `;
            });

            return html;
        }

        function initAccordion(data, idDivAccordion = 'accordionResumo') {
            let html = ``;

            data.forEach((Nivel1Key, gIndex) => {
                html += `
                    <div class="card nivel1-card">
                        ${renderNivel0(Nivel1Key, gIndex)}
                    </div>
                `;
            });

            $("#" + idDivAccordion).html(html);
        }

        function renderNivel0(Nivel1Key, lIndex) {
            let html = `
                <div class="card-header p-1">
                    <button class="btn btn-block"
                        data-toggle="collapse"
                        data-target="#nivel0-${lIndex}">
                        <table class="table table-borderless mb-0 w-100">
                            <tr>
                                <td class="text-left p-0">
                                    <small class="text-muted">
                                        <i class="fas fa-chevron-down"></i>
                                        <strong>${Nivel1Key.local}</strong>
                                    </small>
                                </td>
                                <td class="text-right p-0 w-10">
                                    ${Nivel1Key.qtd} unid.
                                </td>                                
                            </tr>
                        </table>
                    </button>
                </div>

                <div id="nivel0-${lIndex}" class="collapse">
                    <div class="card-body p-1">
            `;            

            Nivel1Key.medida.forEach((Nivel2Key, maIndex) => {
                html += renderNivel1(Nivel2Key, lIndex, maIndex);
            });

            html += `
                    </div>
                </div>
            `;

            return html;
        }

        function renderNivel1(Nivel2Key, lIndex, maIndex) {
            let html = `
                <div class="medida-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#medida-${lIndex}-${maIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-1 col-nome">                                     
                                        <i class="fas fa-chevron-down"></i>
                                        <strong class="ps-3"> ${Nivel2Key.medida}</strong>                                     
                                </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel2Key.qtd} unid.</td>                                
                            </tr>
                        </table>
                    </button>
                    <div id="medida-${lIndex}-${maIndex}" class="collapse">
            `;

            Nivel2Key.marca.forEach((Nivel2Key, moIndex) => {
                html += renderNivel2(Nivel2Key, lIndex, maIndex, moIndex);
            });

            html += `
                    </div>
                </div>
            `;

            return html;
        }

        function renderNivel2(Nivel2Key, lIndex, maIndex, moIndex) {
            let html = `
                <div class="marca-container">
                    <button class="btn btn-list btn-block pt-0 pb-0" data-toggle="collapse"
                            data-target="#marca-${lIndex}-${maIndex}-${moIndex}">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-2 col-nome">                                     
                                    <i class="fas fa-chevron-down"></i>
                                    <strong class="ps-3"> ${Nivel2Key.marca}</strong>                                        
                                </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel2Key.qtd} unid.</td>                                
                            </tr>
                        </table>
                    </button>
                    <div id="marca-${lIndex}-${maIndex}-${moIndex}" class="collapse">
                    `;

            Nivel2Key.modelo.forEach((Nivel3Key, moIndex) => {
                html += renderNivel3(Nivel3Key);
            });

            html += `
                    </div>
                </div>
                `;
            return html;
        }

        function renderNivel3(Nivel3Key) {
            let html = `
                <div class="modelo-container ml-1 mr-1">
                    <button class="btn btn-list btn-block pt-0 pb-0">
                        <table class="table table-bordered table-borderless mb-0 w-100 tabela">
                            <tr>
                                <td class="text-left p-0 indent-3 col-nome"> ${Nivel3Key.modelo} </td>
                                <td class="text-right p-0 col-qtd"> ${Nivel3Key.qtd} unid.</td>
                            </tr>
                        </table>
                    </button>
                </div>
            `;
            return html;
        }

        function deleteOrDown(status, id) {
            const config = verificaStatus(status);

            Swal.fire({
                // title: 'Tem certeza?',
                text: config.confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: config.confirmButtonTitle,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('delete-carcaca') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#modal-add-carcaca').modal('hide');
                                $('#estoque-carcacas').DataTable().ajax.reload();
                                $('#estoque-carcacas-baixas').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: config.swalSuccessText,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                // Erro
                                Swal.fire({
                                    icon: 'error',
                                    title: config.swalErrorTitle,
                                    html: response.errors,
                                    customClass: {
                                        htmlContainer: 'text-left'
                                    }
                                });
                                return;
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: config.swalErrorTitle,
                                text: xhr.responseText
                            });
                        }
                    });
                } else {
                    return;
                }
            });
        }

        function verificaStatus(status) {
            let messages = {
                B: {
                    confirmButtonTitle: 'Sim, baixar!',
                    confirmText: "Tem certeza que deseja baixar esta carcaça?",
                    swalSuccessText: "Carcaça baixada com sucesso!",
                    swalErrorTitle: "Erro ao baixar carcaça."
                },
                D: {
                    confirmButtonTitle: 'Sim, deletar!',
                    confirmText: "Você não poderá reverter isso!",
                    swalSuccessText: "Carcaça deletada com sucesso!",
                    swalErrorTitle: "Erro ao deletar carcaça."
                },
                A: {
                    confirmButtonTitle: 'Sim, cancelar!',
                    confirmText: "Tem certeza que deseja cancelar a baixa?",
                    swalSuccessText: "Baixa cancelada com sucesso!",
                    swalErrorTitle: "Erro ao cancelar baixa."
                }
            }
            return messages[status] || messages['default'];
        }

        $(document).on('click', '#btn-save-carcaca', function() {
            let medida = $('#cd_medida').val();
            let modelo = $('#cd_modelo').val();
            let fogo = $('#nr_fogo').val();
            let serie = $('#nr_serie').val();
            let tipo = $('#cd_tipo').val();
            let local = $('#cd_local').val();
            let dot = $('#nr_dot').val();
            let valor = $('#vl_carcaca').val().replace(',', '.');

            $.ajax({
                url: '{{ route('store-carcaca') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    medida: medida,
                    modelo: modelo,
                    fogo: fogo,
                    serie: serie,
                    dot: dot,
                    valor: valor,
                    tipo: tipo,
                    local: local
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-add-carcaca').modal('hide');
                        $('#estoque-carcacas').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Carcaça salva com sucesso!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Erro
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao salvar carcaça.',
                            html: response.errors,
                            customClass: {
                                htmlContainer: 'text-left'
                            }
                        });
                        return;
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao salvar carcaça.',
                        text: xhr.responseText
                    });
                }
            });
        });

        $(document).on('click', '.btn-baixar', function() {
            let id = [$(this).data('id')];
            deleteOrDown('B', id);
        });

        $(document).on('click', '.btn-deletar', function() {
            let id = [$(this).data('id')];
            deleteOrDown('D', id);
        });

        $(document).on('click', '.btn-editar', function() {
            const $dataRow = $('#estoque-carcacas').DataTable().row($(this).parents('tr')).data();

            $('.modal-title').text('Editar Carcaça');
            $('#id_carcaca').val($dataRow['ID']);
            $('#cd_medida')
                .append(new Option($dataRow['DSMEDIDAPNEU'], $dataRow['IDMEDIDAPNEU'], true, true))
                .trigger('change');
            $('#cd_modelo')
                .append(new Option($dataRow['DSMODELO'], $dataRow['IDMODELOPNEU'], true, true))
                .trigger('change');
            $('#nr_fogo').val($dataRow['NR_FOGO']);
            $('#nr_serie').val($dataRow['NR_SERIE']);
            $('#cd_tipo').val($dataRow['CD_TIPO']);
            $('#cd_local').val($dataRow['CD_LOCAL']);
            $('#nr_dot').val($dataRow['NR_DOT']);
            $('#vl_carcaca').val($dataRow['VL_CARCACA']);

            $('#btn-save-carcaca').addClass('d-none');
            $('#btn-edit-carcaca').removeClass('d-none');

            $('#modal-add-carcaca').modal('show');
        });

        $(document).on('click', '#btn-edit-carcaca', function() {
            let medida = $('#cd_medida').val();
            let modelo = $('#cd_modelo').val();
            let fogo = $('#nr_fogo').val();
            let serie = $('#nr_serie').val();
            let dot = $('#nr_dot').val();
            let valor = $('#vl_carcaca').val().replace(',', '.');
            let tipo = $('#cd_tipo').val();
            let local = $('#cd_local').val();
            let id_carcaca = $('#id_carcaca').val();

            $.ajax({
                url: '{{ route('edit-carcaca') }}',
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id_carcaca,
                    medida: medida,
                    modelo: modelo,
                    fogo: fogo,
                    serie: serie,
                    dot: dot,
                    valor: valor,
                    tipo: tipo,
                    local: local
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-add-carcaca').modal('hide');
                        $('#estoque-carcacas').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Carcaça atualizada com sucesso!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Erro
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao atualizar carcaça.',
                            html: response.errors,
                            customClass: {
                                htmlContainer: 'text-left'
                            }
                        });
                        return;
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao atualizar carcaça.',
                        text: xhr.responseText
                    });
                }
            });
        });

        $(document).on('click', '#btn-baixar-todos', function() {
            let selectedRows = table_carcaca_itens.rows({
                selected: true
            }).data();
            if (selectedRows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma carcaça selecionada.',
                    text: 'Por favor, selecione ao menos uma carcaça para baixar.'
                });
                return;
            }
            var id = [];
            selectedRows.each(function(rowData) {
                id.push(rowData.ID);
            });

            deleteOrDown('B', id);
        });

        $(document).on('click', '#btn-transferir-todos', function() {
            let selectedRows = table_carcaca_itens.rows({
                selected: true
            }).data();
            if (selectedRows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma carcaça selecionada.',
                    text: 'Por favor, selecione ao menos uma carcaça para transferencia.'
                });
                return;
            }
            var id = [];
            selectedRows.each(function(rowData) {
                id.push(rowData.ID);
            });
            // grava os IDs dentro do modal
            $('#modal-transferir-carcaca').data('ids', id);
            $('#modal-transferir-carcaca').modal('show');
        });

        $(document).on('click', '#btn-transferir-carcaca', function() {
            let ids = $('#modal-transferir-carcaca').data('ids');
            let local = $('#cd_local_transferir').val();

            $.ajax({
                url: '{{ route('transfer-carcaca') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids,
                    local: local
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-transferir-carcaca').modal('hide');
                        $('#estoque-carcacas').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Carcaças transferidas com sucesso!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Erro
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao transferir carcaças.',
                            html: response.errors,
                            customClass: {
                                htmlContainer: 'text-left'
                            }
                        });
                        return;
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao transferir carcaças.',
                        text: xhr.responseText
                    });
                }
            });
        });

        $(document).on('click', '#btn-criar-pedido', function() {

            $('#itens-pedido').html('');
            $('#btn-confirmar-pedido').prop('disabled', false);

            inicializaSelect2Lista({
                route: routes.condicaoPagamento,
                selectId: '#cd_cond_pagto',
                placeholder: 'Selecione a Condição de Pagamento',
                modalParent: '#modal-criar-pedido',
                textField: 'DS_CONDPAGTO',
                valueField: 'CD_CONDPAGTO'
            });

            inicializaSelect2Lista({
                route: routes.formaPagamento,
                selectId: '#cd_form_pagto',
                placeholder: 'Selecione a Forma de Pagamento',
                modalParent: '#modal-criar-pedido',
                textField: 'DS_FORMAPAGTO',
                valueField: 'CD_FORMAPAGTO'
            });

            let selectedRows = table_carcaca_itens.rows({
                selected: true
            }).data();

            if (selectedRows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma carcaça selecionada.',
                    text: 'Por favor, selecione ao menos uma carcaça para criar o pedido.'
                });
                return;
            }

            selectedRows.each(function(rowData) {
                let itemHtml = `
                                <div class="row mb-2 item-pedido" data-item-id="${rowData.ID}">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small">Medida</label>
                                        <input type="text"
                                            class="form-control form-control-sm"
                                            value="${rowData.DSMEDIDAPNEU}"
                                            readonly />
                                    </div>

                                     <!-- Botão -->
                                    <div class="col-1 col-md-auto d-flex align-items-end">
                                        <button type="button"
                                            class="btn btn-success btn-sm btn-atualizar-servicos mb-1"
                                            data-item-id="${rowData.ID}"
                                            data-medida-pneu="${rowData.IDMEDIDAPNEU}"
                                            >
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    <div class="col-11 col-md-5">
                                        <label class="form-label small">Serviço</label>
                                        <select class="form-control form-control-sm servico-item-${rowData.ID}" style="width: 100%">                                           
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="form-label small">Valor</label>
                                        <input type="text"
                                            class="form-control form-control-sm input-venda"                                           
                                            />
                                    </div>
                                </div>
                            `;

                $('#itens-pedido').append(itemHtml);

                $('.input-venda').inputmask({
                    mask: ['999', '9.999'],
                    radixPoint: ',',
                });

                inicializaSelect2Lista({
                    route: routes.servicoPneu + '?idMedidaPneu=' + rowData.IDMEDIDAPNEU,
                    selectId: `.servico-item-${rowData.ID}`,
                    placeholder: 'Selecione o Serviço',
                    modalParent: '.item-pedido',
                    textField: 'DSSERVICO',
                    valueField: 'ID'
                })
            });

            $('#modal-criar-pedido').modal('show');
        });

        $(document).on('click', '#btn-confirmar-pedido', function() {

            let $btn = $(this);

            if ($btn.prop('disabled')) {
                return; // impede segundo clique
            }

            $btn.prop('disabled', true); // bloqueia botão

            let cd_empresa = $('#cd_empresa').val();
            let pessoa = $('#pessoa').val();
            let cond_pagto = $('#cd_cond_pagto').val();
            let form_pagto = $('#cd_form_pagto').val();

            let itens = [];

            $('.item-pedido').each(function() {
                let itemId = $(this).data('item-id');
                let servico = $(this).find('select').val();
                let valor = $(this).find('input.input-venda').val().replace('.', '');

                itens.push({
                    itemId: itemId,
                    servico: servico,
                    valor: valor
                });
            });

            $.ajax({
                url: '{{ route('store-pedido-pneu') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cd_empresa: cd_empresa,
                    pessoa: pessoa,
                    cond_pagto: cond_pagto,
                    form_pagto: form_pagto,
                    itens: itens
                },
                beforeSend: function() {
                    $(".loading-card").removeClass("invisible");
                },
                success: function(response) {
                    if (response.success) {
                        $(".loading-card").addClass("invisible");
                        $('#modal-criar-pedido').modal('hide');
                        $('#estoque-carcacas').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Pedido <strong>' + response.id +
                                '</strong> criado com sucesso!',
                            showConfirmButton: true,
                            confirmButtonText: 'Ok'
                        });
                    } else {
                        // Erro
                        $(".loading-card").addClass("invisible");
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao criar pedido.',
                            html: response.errors,
                            customClass: {
                                htmlContainer: 'text-left'
                            }
                        });
                        return;
                    }
                },
                error: function(xhr) {
                    $(".loading-card").addClass("invisible");
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao criar pedido.',
                        text: xhr.responseText
                    });
                }
            });
        });

        $(document).on('click', '.btn-atualizar-servicos', function() {
            let itemId = $(this).data('item-id');
            let medidaPneu = $(this).data('medida-pneu');
            let selectServico = $(`.servico-item-${itemId}`);

            if (!medidaPneu) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao atualizar serviços.',
                    text: 'Medida do pneu não encontrada.'
                });
                return;
            }

            // reinicializa o select2 com os novos serviços
            inicializaSelect2Lista({
                route: routes.servicoPneu + '?idMedidaPneu=' + medidaPneu,
                selectId: `.servico-item-${itemId}`,
                placeholder: 'Selecione o Serviço',
                modalParent: '#modal-criar-pedido',
                textField: 'DSSERVICO',
                valueField: 'ID'
            });
        });

        $(document).on('click', '#tab-carcaca-saida', function() {
            $('#estoque-carcacas-baixas').DataTable().destroy();

            var table_carcaca_itens_baixas = $('#estoque-carcacas-baixas').DataTable({
                processing: false,
                serverSide: false,
                language: {
                    url: "{{ asset('vendor/datatables/pt-br.json') }}",
                },
                pagingType: "simple",
                ajax: {
                    url: '{{ route('get-carcaca-casa-baixas') }}',
                    dataSrc: function(response) {
                        return response.datatable.data;
                    }
                },
                columns: [{
                        data: 'ID',
                        name: 'ID',
                        title: 'id',
                        visible: false,
                        className: 'text-center',
                    },
                    {
                        data: 'DSMEDIDAPNEU',
                        name: 'DSMEDIDAPNEU',
                        title: 'Medida',
                        width: '20%',
                    },
                    {
                        data: 'DSMODELO',
                        name: 'DSMODELO',
                        title: 'Modelo'
                    },
                    {
                        data: 'NR_FOGO',
                        name: 'NR_FOGO',
                        title: 'Fogo',
                        className: 'text-center',
                    },
                    {
                        data: 'NR_SERIE',
                        name: 'NR_SERIE',
                        title: 'Serie',
                        className: 'text-center',
                    },
                    {
                        data: 'NR_DOT',
                        name: 'NR_DOT',
                        title: 'Dot',
                        className: 'text-center',
                    },
                    {
                        data: 'DS_TIPO',
                        name: 'DS_TIPO',
                        title: 'Tipo',
                    }, {
                        data: 'PEDIDO',
                        name: 'PEDIDO',
                        title: 'Pedido',
                        className: 'text-center',
                    }, {
                        data: 'LOCAL_ESTOQUE',
                        name: 'LOCAL_ESTOQUE',
                        title: 'Local',
                        className: 'text-center',
                    },

                    {
                        data: 'EMPRESA_BAIXA',
                        name: 'EMPRESA_BAIXA',
                        title: 'Baixa',
                    },
                    {
                        data: 'ST_BAIXA',
                        name: 'ST_BAIXA',
                        title: 'St Baixa',
                    }, {
                        data: 'DT_BAIXA',
                        name: 'DT_BAIXA',
                        title: 'Data Baixa',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return moment(data).format('DD/MM/YYYY');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Ações',
                        orderable: false,
                        searchable: false,
                        // width: '10%',
                        className: 'text-center',
                    },
                ],
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    className: 'select-checkbox',
                }],
                order: [
                    [0, "desc"]
                ],

            });
        });

        $(document).on('click', '#tab-carcaca-entrada', function() {
            $('#estoque-carcacas').DataTable().ajax.reload();
        });

        $(document).on('click', '.btn-cancelar-baixa', function() {
            let id = [$(this).data('id')];
            deleteOrDown('A', id);
        });
    </script>
@endsection
