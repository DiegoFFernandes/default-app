@extends('layouts.master')
@section('title', $title)

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-dark card-outline card-outline-tabs">
                    @include('admin.comercial.libera-ordem.tabs.nav-tabs')
                    <div class="card-body">
                        <div class="tab-content">
                            @include('admin.comercial.libera-ordem.tabs.pedidos-bloqueados')

                            @include('admin.comercial.libera-ordem.tabs.substituir-comissao')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.comercial.libera-ordem.modals.modal-table-pedido')

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pedidoBloqueado.css') }}">
@stop

@section('js')
    <script>
        window.routes = {
            languageDatatables: "{{ asset('vendor/datatables/pt-BR.json') }}",
            calculaComissao: "{{ route('get-calcula-comissao') }}",
            ordensBloqueadas: "{{ route('get-ordens-bloqueadas-comercial') }}",            
            savePedidosLiberadas: "{{ route('save-libera-pedido') }}",
            liberaAbaixoDesconto: "{{ route('libera-abaixo-desconto') }}",
            itensPneusOrdensBloqueadas: "{{ route('get-pneus-ordens-bloqueadas-comercial', ':pedido') }}"
        };
    </script>
    <script src="{{ asset('js/dashboard/pedidoBloqueado.js') }}"></script>
@stop
