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
                            @if (auth()->user()->hasRole('admin|gerente comercial'))
                                <li class="nav-item">
                                    <a class="nav-link" id="tab-associadas" data-toggle="tab" href="#painel-associadas"
                                        role="tab">Associadas</a>
                                </li>
                            @endif
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
                                                                name="desenho[]" style="width: 100%;" multiple>
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
                                                <button type="button" id="btn-recomecar" class="btn btn-secondary btn-xs"
                                                    style="width: 80px;">
                                                    Recomecar
                                                </button>
                                                <button type="button" id="btn-deletar-itens" class="btn btn-secondary btn-xs"
                                                    style="width: 80px;">
                                                    Deletar Itens
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs float-right"
                                                    style="width: 80px;" id="btn-enviar-importar">
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
                                    @include('admin.comercial.components.modal-item-tabela-preco', [
                                        'idModal' => 'modal-item-tab-preco-cadastradas',
                                        'idTabelaItem' => 'table-item-tab-preco-cadastradas',
                                    ])
                                    @include('admin.comercial.components.modal-vincular-tabela-preco', [
                                        'idModal' => 'modal-vincular-tab-preco-pessoas',
                                        'idPessoa' => 'cd_pessoa_multi',
                                        'idTabelaPreco' => 'cd_tabela_preco',
                                        'dsTabelaPreco' => 'ds_tabela_preco',
                                    ])
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

                                                    <!-- Margem somente em telas maiores que sm -->
                                                    <div class="form-check form-switch m-0 ml-sm-3">
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
                                    @include('admin.comercial.components.modal-vincular-tabela-preco', [
                                        'idModal' => 'modal-vincular-tab-preco-pessoas2',
                                        'idPessoa' => 'cd_pessoa_multi2',
                                        'idTabelaPreco' => 'cd_tabela_preco2',
                                        'dsTabelaPreco' => 'ds_tabela_preco2',
                                    ]);
                                    @include('admin.comercial.components.modal-item-tabela-preco', [
                                        'idModal' => 'modal-item-tab-preco',
                                        'idTabelaItem' => 'table-item-tab-preco',
                                    ]);
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.comercial.components.modal-tabela-preco')
    </section>
@stop

@section('css')
    <style>


    </style>
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
            importarTabelaPreco: "{{ route('importar-tabela-preco') }}",
            vincularTabelaPreco: "{{ route('vincular-tabela-preco') }}",
            deleteTabelaPreco: "{{ route('deletar-tabela-preco') }}",
        };

        initSelect2Pessoa('#pessoa', routes.searchPessoa);

        $('#tab-associadas').click(function() {
            tabelaPreco = initTabelaPreco(routes);
        });

        $('#tabela-preco').on('click', '.btn-ver-itens', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $('.title-nm-tabela').html($(this).data('nm_tabela'));
            initTableItemTabelaPreco(routes, cd_tabela, 'tabela_preco', 'table-item-tab-preco',
                'modal-item-tab-preco');
        });

        $('#tabela-preco').on('click', '.btn-vincular-tabela', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $('#cd_tabela_preco2').val(cd_tabela);
            var ds_tabela = $(this).closest('tr').find('td:eq(1)').text();
            $('#ds_tabela_preco2').val(ds_tabela);
            $('#modal-vincular-tab-preco-pessoas2').modal('show');
            initSelect2Pessoa('#cd_pessoa_multi2', routes.searchPessoa, '#modal-vincular-tab-preco-pessoas2');
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

            const valor = $('#valor').val();
            const nomeTabela = $("#pessoa option:selected").text();
            $(".card-title").html(
                "<span class='badge bg-gray-dark'>" + formatarNome(nomeTabela) + "</span>"
            );

            if (valor === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, insira um valor válido maior que zero.',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                });
                return;
            } else {
                $("#item-tabela-preco").closest(".card").show();
            }

            if (!$.fn.DataTable.isDataTable("#item-tabela-preco")) {
                initTableTabelaPrecoPrevia();
            }

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

                    msgToastr('Itens adicionados à prévia com sucesso!', 'success');
                    $("#desenho, #medida, #valor").val("").trigger("change"); // limpa os inputs
                }
            });
        });

        $('#btn-adicional').on('click', function() {
            $('#modal-item-adicional').modal('show');
        });

        $('#btn-deletar-itens').on('click', function() {
            var linhasSelecionadas = tabela_preview.rows({ selected: true });

            if (linhasSelecionadas.count() === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, selecione pelo menos um item para deletar.',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Atenção',
                text: 'Deseja realmente deletar os itens selecionados?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, deletar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    linhasSelecionadas.every(function(rowIdx, tableLoop, rowLoop) {
                        var data = this.data();
                        itens_preview.delete(data.ID); // Remove do Map
                        return true;
                    });

                    var dados_atualizados = Array.from(itens_preview.values());
                    tabela_preview.clear().rows.add(dados_atualizados).draw();

                    Swal.fire({
                        icon: 'success',
                        title: 'Itens deletados com sucesso!',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
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

                    msgToastr('Itens adicionados à prévia com sucesso!', 'success');
                }
            });



            const dados_atualizados = Array.from(itens_preview.values());
            tabela_preview.clear().rows.add(dados_atualizados).draw();
            $('#modal-item-adicional').modal('hide');
            // Limpar os campos do modal
            $('#input-vulc-carga-valor').val('');
            $('#input-vulc-agricola-valor').val('');
            $('#input-manchao-valor').val('');
            $('#input-manchao-valor-agricola').val('');
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
                beforeSend: function() {
                    $("#loading").removeClass('invisible');
                },
                success: function(response) {

                    console.log(response);
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
                    $("#loading").addClass('invisible');
                }
            });
        });

        formularioDinamico(routes); // chama a função para deixar a pagina dinamica

        // Tab para ver as tabelas cadastradas para importar
        $('#tab-cadastradas').on('click', function() {
            $('#tabela-preco-cadastradas').DataTable().destroy();
            initTableTabelaPrecoCadastradasPreview(routes);
        });

        $('#tabela-preco-cadastradas').on('click', '.btn-ver-itens', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $('.title-nm-tabela').html($(this).data('nm_tabela'));
            initTableItemTabelaPreco(routes, cd_tabela, 'tabela_preco_preview',
                'table-item-tab-preco-cadastradas', 'modal-item-tab-preco-cadastradas');
        });

        $('#tabela-preco-cadastradas').on('click', '.btn-importar', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $.ajax({
                type: "GET",
                url: routes.importarTabelaPreco,
                data: {
                    _token: '{{ csrf_token() }}',
                    cd_tabela: cd_tabela
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loading").removeClass('invisible');
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao importar',
                            text: response.error ||
                                'Ocorreu um erro ao importar a tabela. Tente novamente.',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                        $("#loading").addClass('invisible');
                        return;
                    } else {
                        $('#tabela-preco-cadastradas').DataTable().destroy();
                        initTableTabelaPrecoCadastradasPreview(routes);
                        Swal.fire({
                            title: 'Atenção',
                            text: response.message,
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        $("#loading").addClass('invisible');
                    }

                }
            });

        });

        $('#tabela-preco-cadastradas').on('click', '.btn-vincular-tabela', function() {
            var cd_tabela = $(this).data('cd_tabela');
            $('#cd_tabela_preco').val(cd_tabela);
            var ds_tabela = $(this).closest('tr').find('td:eq(1)').text();
            $('#ds_tabela_preco').val(ds_tabela);
            $('#modal-vincular-tab-preco-pessoas').modal('show');
            initSelect2Pessoa('#cd_pessoa_multi', routes.searchPessoa, '#modal-vincular-tab-preco-pessoas');
        });

        $('#btn-salvar-vinculo').on('click', function() {
            var cd_tabela = $('#cd_tabela_preco').val();
            var cd_pessoa = $('#cd_pessoa_multi').val();
            salvarVinculoTabelaPessoa(cd_tabela, cd_pessoa, routes);

        });

        $('#tabela-preco-cadastradas').on('click', '.btn-delete', function() {
            var cd_tabela = $(this).data('cd_tabela');
            var nm_tabela = $(this).data('nm_tabela');

            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                html: 'Deseja realmente excluir esta tabela de preço?</br>' + nm_tabela,
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-delete'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: routes.deleteTabelaPreco,
                        data: {
                            _token: '{{ csrf_token() }}',
                            cd_tabela: cd_tabela
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $("#loading").removeClass('invisible');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#tabela-preco-cadastradas').DataTable().ajax.reload();

                                $("#loading").addClass('invisible');

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Atenção',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Atenção',
                                    text: response.message ||
                                        'Ocorreu um erro ao deletar a tabela. Tente novamente.',
                                    customClass: {
                                        confirmButton: 'btn btn-warning'
                                    }
                                });
                                $("#loading").addClass('invisible');
                                return;
                            }
                        }
                    });
                }
            });
            return;
        });

        function salvarVinculoTabelaPessoa(cd_tabela, cd_pessoa, routes) {
            if (!cd_pessoa || cd_pessoa.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, selecione pelo menos um cliente para vincular.',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                });
                return;
            }

            $.ajax({
                type: "GET",
                url: routes.vincularTabelaPreco,
                data: {
                    _token: '{{ csrf_token() }}',
                    cd_tabela: cd_tabela,
                    cd_pessoa: cd_pessoa
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loading").removeClass('invisible');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-vincular-tab-preco-pessoas').modal('hide');
                        $('#tabela-preco-cadastradas').DataTable().destroy();
                        initTableTabelaPrecoCadastradasPreview(routes);
                        Swal.fire({
                            title: 'Atenção',
                            text: response.message,
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        $("#loading").addClass('invisible');
                        $('#cd_pessoa_multi').val('').trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: response.message ||
                                'Ocorreu um erro ao vincular a tabela. Tente novamente.',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            }
                        });
                        $("#loading").addClass('invisible');
                        return;
                    }

                }
            });
        }
    </script>

@stop
