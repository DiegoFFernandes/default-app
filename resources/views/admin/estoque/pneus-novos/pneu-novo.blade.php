@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-12 mb-3">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.estoque.pneus-novos.tabs.nav-tabs')

                    <div class="card-body p-0">
                        <div class="tab-content" id="tabContentRelatorio">
                            @include('admin.estoque.pneus-novos.tabs.painel-pneus-novos', ['ativo' => true])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/accordionResumoEstoque.css?v=' . time()) }}">
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
            getPneusNovos: "{{ route('get-pneus-novos') }}",
            
        }
        window.canEdit = @json(!$canEdit);
    </script>
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/accordionResumo.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/carcacaCasaEstoque/pneusNovos.js') }}?v={{ time() }}"></script>
@endsection
