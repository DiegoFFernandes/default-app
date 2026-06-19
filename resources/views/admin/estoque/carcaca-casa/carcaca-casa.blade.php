@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.estoque.carcaca-casa.tabs.nav-tabs')

                    <div class="card-body p-0">
                        <div class="tab-content" id="tabContentRelatorio">
                            @include('admin.estoque.carcaca-casa.tabs.tab-carcaca-entrada')

                            @include('admin.estoque.carcaca-casa.tabs.painel-carcaca-saida')

                            @include('admin.estoque.carcaca-casa.tabs.painel-carcaca-pronta')

                            @include('admin.estoque.carcaca-casa.tabs.painel-carcaca-pronta-terceiros')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.estoque.carcaca-casa.modals.modal-add-carcaca')

        @include('admin.estoque.carcaca-casa.modals.modal-transferir-carcaca')

        @include('admin.estoque.carcaca-casa.modals.modal-criar-pedido')
    </section>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/carcacaCasaEstoque.css?v=' . time()) }}">
    <style>
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

        .accordion-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .nivel1-card > .card-header button:focus,
        .nivel2-container .btn:focus,
        .nivel3-container .btn:focus {
            box-shadow: none;
            outline: none;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript">
        $(document).on('select2:open', function() {
            $('.select2-results__options').css('max-height', '100px');
            $('.select2-results__options').css('overflow-y', 'auto');
        });
        window.routes = {
            token: "{{ csrf_token() }}",
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            searchPessoa: "{{ route('usuario.search-pessoa') }}",
            condicaoPagamento: "{{ route('get-cond-pagamento') }}",
            formaPagamento: "{{ route('get-form-pagamento') }}",
            servicoPneu: "{{ route('get-servico-pneu-medida') }}",
            searchMedidas: "{{ route('search-medidas-pneus') }}",
            searchModelos: "{{ route('search-modelo-pneus') }}",
            deleteCarcaca: "{{ route('delete-carcaca') }}",
            storeCarcaca: "{{ route('store-carcaca') }}",
            editCarcaca: "{{ route('edit-carcaca') }}",
            getCarcacaCasa: "{{ route('get-carcaca-casa') }}",
            transferCarcaca: "{{ route('transfer-carcaca') }}",
            storePedidoPneu: "{{ route('store-pedido-pneu') }}",
            getCarcacaCasaBaixas: "{{ route('get-carcaca-casa-baixas') }}",
            getCarcacaCasaProntas: "{{ route('get-carcaca-casa-prontas') }}",
            getCarcacaCasaProntasTerceiros: "{{ route('get-carcaca-casa-prontas-terceiros') }}",
            reservarCarcacaCasaPronta: "{{ route('reservar-carcaca-casa-pronta') }}",
        }
        window.canEdit = @json(!$canEdit);
    </script>

    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/entradasCarcacaEstoque.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/carcacasCasaPronta.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/carcacasCasaProntaTerceiros.js') }}?v={{ time() }}">
    </script>
@endsection
