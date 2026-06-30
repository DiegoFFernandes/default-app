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

                            @include('admin.comercial.tabela-preco.tabs.painel-item-faltante')

                        </div>
                    </div>
                </div>
            </div>
            @include('admin.comercial.tabela-preco.modals.modal-tabela-preco')
    </section>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap4.min.css">
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

        /* Tabs */
        .nav-tabs .nav-link {
            font-size: 13px;
            padding: .5rem .9rem;
            color: #6c757d;
        }

        .nav-tabs .nav-link i {
            font-size: 13px;
        }

        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #343a40;
        }

        .nav-tabs .nav-link:not(.active):hover {
            color: #343a40;
            background-color: #f8f9fa;
        }

        /* Filtros da aba Associadas */
        .filtros-associadas {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .filtros-associadas .form-check {
            margin-bottom: 0;
        }

        .filtros-associadas .form-check-label {
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
        }

        /* Oculta botões internos do DataTables — exports via card-tools */
        #tabela-item-faltante_wrapper .dt-buttons {
            display: none !important;
        }

        /* Agrupamento visual por cliente — tabela itens faltantes */
        #tabela-item-faltante tbody tr.grupo-a td {
            background-color: #ffffff !important;
        }

        #tabela-item-faltante tbody tr.grupo-b td {
            background-color: #eef3fb !important;
        }

        #tabela-item-faltante tbody tr.grupo-inicio td {
            border-top: 2px solid #6c757d !important;
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
            input:not([type="checkbox"]):not([type="radio"]),
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

            .nav-tabs .nav-link {
                padding: .4rem .6rem;
                font-size: 12px;
            }
        }
    </style>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/handlebars.min.js') }}"></script>
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
            itemFaltanteTabelaPreco: "{{ route('item-faltante-tabela-preco') }}",
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
    <script src="{{ asset('js/dashboard/tabelaPreco/tabelaPrecoItemFaltante.js') }}?v={{ time() }}"></script>

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

        $('link[href*="custom_datatables"]').remove();
    </script>

@stop
