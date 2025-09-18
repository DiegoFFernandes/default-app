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
                                                        <div class="form-group">
                                                            <label>Nome da Tabela</label>
                                                            <select name='pessoa' class="form-control" id="pessoa"
                                                                style="width: 100%">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Selecione o Desenho</label>
                                                            <select class="form-control select2" id="desenho"
                                                                style="width: 100%; ">
                                                                <option selected="selected">Selecione</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Selecione a Medida</label>
                                                            <select class="form-control select2" id="medida"
                                                                style="width: 100%; ">
                                                                <option selected="selected">Selecione</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Digite o Valor</label>
                                                            <input type="text" id="valor"
                                                                class="form-control order-3 order-md-2 mt-3 mt-md-0 mr-md-2"
                                                                placeholder="Digite o Valor...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                            <div class="card-footer">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <button type="submit" id="btn-cadastrar"
                                                            class="btn btn-danger btn-sm btn-block">Cadastrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="inserir"
                                                        class="table compact table-font-small table-striped table-bordered"
                                                        style="width:100%; font-size: 11px;">
                                                        <thead>
                                                            <tr>
                                                                <th>CD_ITEM</th>
                                                                <th>DS_ITEM</th>
                                                                <th>VALOR</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
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
                                                        <input class="form-check-input" type="checkbox" id="checkAssociadas"
                                                            name="checkAssociadas">
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
    </section>
@stop
@section('js')
    <script src="{{ asset('js/dashboard/TabelaPreco.js?v=3') }}"></script>
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

        var routes = {
            tabelaPreco: "{{ route('get-tabela-preco') }}",
            itemTabelaPrecoCliente: "{{ route('get-tabela-cliente-preco') }}",
            language_datatables: "{{ asset('vendor/datatables/pt-br.json') }}",
            itemtabelaPreco: "{{ route('get-item-tabela-preco') }}",
            searchPessoa: "{{ route('usuario.search-pessoa') }}",
        };

        initSelect2Pessoa('#pessoa', routes.searchPessoa);

        $('#tab-cadastradas').click(function() {
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

        // Tab para Inserir
        $('#desenho').select2({
            theme: 'bootstrap4'
        });
        $('#medida').select2({
            theme: 'bootstrap4'
        });


        $("#inserir").DataTable({
            paging: true,
            searching: true,
            ordering: false,
            language: {
                url: "{{ asset('vendor/datatables/pt-br.json') }}",
            }
        });
    </script>

@stop
