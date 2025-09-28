@extends('layouts.master')

@section('title', 'Tabela de Preço')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-inserir" data-toggle="tab" href="#painel-inserir"
                                    role="tab">Inserir</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-cadastradas" data-toggle="tab" href="#painel-cadastradas"
                                    role="tab">Cadastradas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-associadas" data-toggle="tab" href="#painel-associadas"
                                    role="tab">Associadas</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="painel-inserir" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-sm">
                                                            <label>Nome da Tabela</label>
                                                            <select name='pessoa' class="form-control" id="pessoa"
                                                                style="width: 100%">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-sm">
                                                            <label>Selecione o Desenho</label>
                                                            <select class="form-control select2" id="desenho"
                                                                name="desenho[]" style="width: 100%;" multiple="multiple">
                                                                @foreach ($desenho as $item)
                                                                    <option value="{{ $item->ID }}">
                                                                        {{ $item->DESCRICAO }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-sm">
                                                            <label>Selecione a Medida</label>
                                                            <select class="form-control select2" id="medida"
                                                                name="medida[]" style="width: 100%; " multiple="multiple">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group form-group-sm">
                                                            <label>Digite o Valor</label>
                                                            <input type="number" id="valor" class="form-control"
                                                                placeholder="Digite o Valor...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                            <div class="card-footer">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <button id="btn-associar" class="btn btn-danger btn-sm">
                                                            Incluir na Previa
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <span class="badge badge-secondary">Prévia Tabela</span>
                                                </h3>
                                                <div class="card-tools d-flex w-100 justify-content-end">
                                                    <!-- Botão com ajuste responsivo -->
                                                    <button type="button" class="btn btn-secondary btn-sm btn-adicional"
                                                        id="btn-adicional">
                                                        Itens Adicionais
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="item-tabela-preco"
                                                        class="table compact table-font-small table-striped table-bordered"
                                                        style="width:100%; font-size: 11px;">
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <button type="button" id="btn-recomecar" class="btn btn-secondary btn-sm"
                                                    style="width: 100px;">
                                                    Recomecar
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm float-right"
                                                    style="width: 100px;" id="btn-enviar-importar">
                                                    Salvar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-cadastradas" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-primary">
                                            <div class="card-body">
                                                <table
                                                    class="table table-bordered compact table-responsive table-font-small"
                                                    id="tabela-preco-cadastradas">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="painel-associadas" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-primary">
                                            <div class="card-body">
                                                <div
                                                    class="d-flex flex-wrap gap-4 align-items-center mb-3 border-bottom pb-2">
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="checkNaoAssociadas" name="checkNaoAssociadas">
                                                        <label class="form-check-label" for="checkNaoAssociadas">
                                                            Tabelas não Associadas
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch m-0 ml-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="checkAssociadas" name="checkAssociadas">
                                                        <label class="form-check-label" for="checkAssociadas">
                                                            Tabelas Associadas
                                                        </label>
                                                    </div>
                                                </div>
                                                <table
                                                    class="table table-bordered compact table-responsive table-font-small"
                                                    id="tabela-preco">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="modal-item-tab-preco" tabindex="-1" role="dialog"
                                    aria-labelledby="modal-item-tab-preco" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title title-nm-tabela">Item Tabela Preço</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <table id="table-item-tab-preco"
                                                    class="table table-bordered compact table-responsive table-font-small">
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
            @include('admin.comercial.components.modal-tabela-preco')
    </section>
@stop
@section('js')
    <script src="{{ asset('js/dashboard/TabelaPreco.js?v=') }}{{ time() }}"></script>
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border table-left" id="cliente-tabela-{{ CD_TABPRECO }}" style="width:80%; ">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Supervisor</th>                       
                    </tr>
                </thead>
            </table>
        @endverbatim
    </script>
    <script>
        var tabelaClientesTabela = Handlebars.compile($("#details-template").html());
        var tabelaPreco = null;
        var itemTabelaCliente = null;
        var dados_atualizados = [];

        var routes = {
            tabelaPreco: "{{ route('get-tabela-preco') }}",
            itemTabelaPrecoCliente: "{{ route('get-tabela-cliente-preco') }}",
            language_datatables: "{{ asset('vendor/datatables/pt-br.json') }}",
            itemtabelaPreco: "{{ route('get-item-tabela-preco') }}",
            searchPessoa: "{{ route('usuario.search-pessoa') }}",
            searchMedida: "{{ route('get-search-medida') }}",
            previaTabela: "{{ route('get-previa-tabela-preco') }}",
            searchAdicional: "{{ route('get-search-adicional') }}",
            verificaTabelaCadastrada: "{{ route('get-verifica-tabela-cadastrada') }}",
            salvaItemTabelaPreco: "{{ route('salva-item-tabela-preco') }}",
            tabelaPrecoCadastradasPreview: "{{ route('get-tabela-preco-preview') }}",
        };

        initSelect2Pessoa('#pessoa', routes.searchPessoa);

        $('#tab-associadas').click(function() {
            tabelaPreco = initTabelaPreco(routes);
        });

        $('#tabela-preco').on('click', '.btn-ver-itens', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $('.title-nm-tabela').html($(this).data('nm_tabela'));
            initTableItemTabelaPreco(routes, cd_tabela);
        });

        //Aguarda Click para buscar os detalhes dos pedidos dos vendedores
        configurarDetalhesLinha('.details-control', {
            idPrefixo: 'cliente-tabela-',
            idCampo: 'CD_TABPRECO',
            templateFn: tabelaClientesTabela,
            initFn: initTableClienteTabela,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle',
            routes: routes
        });


        $('#desenho, #medida').select2({
            theme: 'bootstrap4',
            width: '100%',
            multiple: true,
        });

        $('#desenho').on('change', function() {
            carregaOpcoes('#desenho', '#medida', routes.searchMedida, 'desenho');
        });

        var itens_preview = new Map();
        var tabela_preview = null;

        $('#btn-associar').on('click', function() {
            if (!$.fn.DataTable.isDataTable("#item-tabela-preco")) {
                initTableTabelaPrecoPrevia();
            }

            const valor = $('#valor').val();

            $.ajax({
                url: routes.previaTabela,
                type: 'GET',
                data: {
                    _csrf: '{{ csrf_token() }}',
                    select: 'previa',
                    pessoa: $('#pessoa').val(),
                    desenho: $('#desenho').val(),
                    medida: $('#medida').val(),
                    valor: valor
                },
                success: function(response) {

                    if (response.errors) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Campos obrigatórios',
                            html: response.errors,
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                        return;
                    }
                    const novos_dados = response.data;

                    novos_dados.forEach(function(item) {
                        item.valor = item.VALOR;
                        itens_preview.set(item.ID, item);

                    });
                    dados_atualizados = Array.from(itens_preview.values());

                    // Inverte a ordem dos dados para que os novos itens apareçam no topo
                    dados_atualizados.reverse();

                    // Limpa a tabela e adiciona os dados no topo
                    tabela_preview.clear().rows.add(dados_atualizados).draw();
                }
            });
        });

        $('#btn-adicional').on('click', function() {
            $('#modal-item-adicional').modal('show');
        });

        $('#btn-add-modal').on('click', function() {
            const vulc_carga_valor = $('#input-vulc-carga-valor').val() || 0;
            const vulc_agricola_valor = $('#input-vulc-agricola-valor').val() || 0;
            const manchao_valor = $('#input-manchao-valor').val() || 0;
            const manchao_agricola_valor = $('#input-manchao-valor-agricola').val() || 0;
            const pessoa = $('#pessoa').val();

            $.ajax({
                url: routes.searchAdicional,
                type: 'GET',
                data: {
                    _csrf: '{{ csrf_token() }}',
                    pessoa: pessoa,
                    vulc_carga_valor: vulc_carga_valor,
                    vulc_agricola_valor: vulc_agricola_valor,
                    manchao_valor: manchao_valor,
                    manchao_agricola_valor: manchao_agricola_valor,
                },
                success: function(response) {
                    if (response.errors) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Campos obrigatórios',
                            html: response.errors,
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                        return;
                    }
                    const novos_dados = response.data;

                    novos_dados.forEach(function(item) {
                        item.valor = item.VALOR;
                        itens_preview.set(item.ID, item);

                    });
                    const dados_atualizados = Array.from(itens_preview.values());

                    tabela_preview.clear().rows.add(dados_atualizados).draw();
                }
            });



            const dados_atualizados = Array.from(itens_preview.values());
            tabela_preview.clear().rows.add(dados_atualizados).draw();
            $('#modal-item-adicional').modal('hide');
            // Limpar os campos do modal
            $('#input-vulc-carga').val('');
            $('#input-vulc-agricola').val('');
            $('#input-manchao').val('');
        });

        $('#btn-enviar-importar').on('click', function() {
            const dadosTabela = tabela_preview.rows().data().toArray();
            if (dadosTabela.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhum item adicionado',
                    text: 'Por favor, adicione itens à tabela antes de salvar.',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                });
                return;
            }
            $.ajax({
                url: routes.salvaItemTabelaPreco,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dadosTabela: dadosTabela,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Atenção',
                            text: response.message,
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });

                        recomecar();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao salvar',
                            text: response.message ||
                                'Ocorreu um erro ao salvar os itens. Tente novamente.',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                }
            });
        });

        formularioDinamico(routes); // chama a função para deixar a pagina dinamica

        // Tab para ver as tabelas cadastradas para importar
        $('#tab-cadastradas').on('click', function() {
           initTableTabelaPrecoCadastradasPreview(routes);
        });


        function initTableTabelaPrecoCadastradasPreview(route) {
            $('#tabela-preco-cadastradas').DataTable({
                processing: false,
                serverSide: false,
                pagingType: 'simple',
                language: {
                    url: route.language_datatables
                },
                ajax: {
                    url: route.tabelaPrecoCadastradasPreview,
                    type: 'GET',
                },
                columns: [{
                        title: 'Ações',
                        data: 'action',
                        orderable: false,
                        searchable: false,                       
                    },
                    {
                        title: 'ID',
                        data: 'CD_TABPRECO',                       
                        visible: false,
                    },
                    {
                        title: 'Nome da Tabela',
                        data: 'DS_TABPRECO', 
                        width: '70%',                     
                    },
                    {
                        title: 'Itens',
                        data: 'QTD_ITENS',                       
                    },

                ],
            });
        }
    </script>

@stop
