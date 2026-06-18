@extends('layouts.master')

@section('title', 'Adiantamento para Despesas')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.despesa.tabs.nav-tabs')

                    <div class="card-body">
                        <div class="tab-content" id="tabContentDespesa">
                            @include('admin.despesa.tabs.tab-registrar')
                            @include('admin.despesa.tabs.tab-lista')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.despesa.modals.modal-foto')
        @include('admin.despesa.modals.modal-camera')
        @include('admin.despesa.modals.modal-editar')
    </section>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/info-box-custom.css') }}?v={{ time() }}">
    <style>
        #preview-container img {
            height: 80px;
            width: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin: 4px;
            border: 1px solid #dee2e6;
        }

        .badge-tipo {
            font-size: 0.8rem;
        }
    </style>
@stop

@section('js')
    <script type="text/javascript">
        window.routes = {
            token: "{{ csrf_token() }}",
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            storeDespesa: "{{ route('despesa.store') }}",
            searchVeiculos: "{{ route('despesa.veiculos') }}",
            getComprovantes: "{{ route('despesa.get') }}",
            updateDespesa: "{{ route('despesa.update', ':id') }}",
            toggleVisto: "{{ route('despesa.toggle-visto', ':id') }}",
        };
        window.canStatusDespesas = @json($canStatusDespesas);
    </script>
    <script src="{{ asset('js/dashboard/chart-helpers.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/despesa/despesa.js') }}?v={{ time() }}"></script>
@endsection
