@extends('layouts.master')

@section('title', 'Tabela de Preço')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.comercial.tabela-preco.tabs.nav-tabs')
                    <div class="card-body">
                        <div class="tab-content">
                            @include('admin.comercial.tabela-preco.tabs.painel-inserir')

                            @include('admin.comercial.tabela-preco.tabs.painel-cadastradas')

                            @include('admin.comercial.tabela-preco.tabs.painel-associadas')

                            @include('admin.comercial.tabela-preco.tabs.painel-divergencia')

                        </div>
                    </div>
                </div>
            </div>
            @include('admin.comercial.tabela-preco.modals.modal-tabela-preco')
    </section>
@stop

@section('css')
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(1.6em + .75rem + 2px) !important;
            font-size: 14px !important;
        }

        .form-group {
            margin-bottom: .6rem;
        }

        .btn-tools {
            min-width: 80px;
            padding: .25rem .5rem;
            font-size: 12px;
        }

        @media (max-width: 768px) {

            .card-title-previa {
                margin-bottom: 15px;
            }

            .card-body {
                padding: .8rem;
            }

            .card-footer {
                padding: .8rem;
            }

            .form-group {
                margin-bottom: .7rem;
            }

            /* IMPORTANTE PARA IOS */
            .form-control,
            input,
            select,
            textarea {
                height: 38px;
                font-size: 16px !important;
            }

            label.small {
                margin-bottom: .2rem;
                font-size: 12px;
            }

            .select2-container--bootstrap4 .select2-selection--single {
                height: 38px !important;
                font-size: 16px !important;
            }

            .select2-container--bootstrap4 .select2-selection--multiple {
                min-height: 38px !important;
                font-size: 16px !important;
            }

            .select2-container--bootstrap4 .select2-results__option {
                padding: 0 .76rem;
                font-size: 20px;
            }

            .select2-selection__rendered {
                line-height: 36px !important;
                font-size: 16px !important;
            }

            .select2-selection__choice {
                font-size: 14px !important;
            }

            #btn-associar {
                width: 100%;
                min-height: 40px;
                font-size: 16px;
            }

            #btn-enviar-importar {
                width: 100%;
                min-height: 40px;
                font-size: 16px;
            }

            .btn-tools {
                min-width: 40px;
                padding: .25rem .5rem;
                font-size: 14px;
            }
        }
    </style>
@stop
@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
            <span class="badge bg-info">{{ PESSOA }}</span>
            <table class="table row-border table-left" id="cliente-tabela-{{ CD_TABPRECO }}" style="width:90%; ">
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
        window.routes = {
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
            cancelarVinculo: "{{ route('cancelar-vinculo') }}",
            divergenciaTabelaPreco: "{{ route('divergencia-tabela-preco') }}",
            csrfToken: '{{ csrf_token() }}'
        };

        var tabelaClientesTabela = Handlebars.compile($("#details-template").html());
        var tabelaPreco = null;
        var tabelaPrecoCadastradas = null;       
        var itemTabelaCliente = null;
        var dados_atualizados = [];
        var itens_preview = new Map();
        var tabela_preview = null;
    </script>
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPreco.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPrecoInserir.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPrecoCadastradas.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPrecoAssociadas.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPrecoDivergencias.js') }}?v={{ time() }}"></script>

    <script>
        //Aguarda Click para buscar os detalhes da tabela de preço
        configurarDetalhesLinha('.details-control', {
            idPrefixo: 'cliente-tabela-',
            idCampo: 'CD_TABPRECO',
            templateFn: tabelaClientesTabela,
            initFn: initTableClienteTabela,
            iconeMais: 'fa-plus-circle',
            iconeMenos: 'fa-minus-circle',
            routes: window.routes
        });
    </script>

@stop
