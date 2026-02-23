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
@stop
@section('js')
    <script type="text/javascript">
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
        }
        window.canEdit = @json($canEdit);
    </script>
        
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/entradasCarcacaEstoque.js?v=' . time()) }}"></script>
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/carcacasCasaPronta.js?v=' . time()) }}"></script>
    
@endsection
