@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    @include('admin.follow-up.tabs.nav-tabs')

                    <div class="card-body">
                        <div class="tab-content">
                            @include('admin.follow-up.tabs.tab-junsoft')

                            @include('admin.follow-up.tabs.tab-disparos-automaticos')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.follow-up.modals.modal-email')

        @include('admin.follow-up.modals.modal-contextos-disparo')

        @include('admin.follow-up.modals.modal-horario-contexto')

        @include('admin.follow-up.modals.modal-whatsapp-contexto')

        @include('admin.follow-up.modals.modal-editar-email-disparo')

        @include('admin.follow-up.modals.modal-editar-telefone-disparo')
    </section>
@stop
@section('css')
    <style>
        /* Ajuste para form-control-sm */
        .select2-container .select2-selection--single {
            font-size: .875rem;
            /* font-size do form-control-sm */
            /* height: calc(1.5em + .5rem + 2px); */
            /* padding: .25rem .5rem; */
        }

        .select2-selection__rendered {
            font-size: .875rem !important;
            /* line-height: 1.5 !important; */
        }

        /* Tamanho da fonte da lista de opções (dropdown) */
        .select2-results__option {
            font-size: .875rem !important;
            /* ou o tamanho que quiser */
            padding: 4px 8px;
            /* opcional, para diminuir o espaçamento */
        }

        .my-small-title {
            font-size: 20px !important;
        }

        .my-small-text {
            font-size: 16px !important;
        }

        .fa-eye {
            color: #ffffff !important;
        }
    </style>
@stop
@section('js')
    <script type="text/javascript">
        window.routesFollowUp = {
            languageDatatables: "{{ asset('vendor/datatables/pt-br.json') }}",
            getSearchEnvio: "{{ route('get-search-envio') }}",
            getEmailFollow: "{{ route('get-email-follow') }}",
            reenviaFollow: "{{ route('reenvia-follow') }}",
            disparoListContextos: "{{ route('disparo-automatico.contextos') }}",
            disparoListEmpresas: "{{ route('firebird.empresas') }}",
            disparoToggleContexto: "{{ route('disparo-automatico.contextos.toggle', ['id' => ':id']) }}",
            disparoHorarioContexto: "{{ route('disparo-automatico.contextos.horario', ['id' => ':id']) }}",
            disparoWhatsAppContexto: "{{ route('disparo-automatico.contextos.whatsapp', ['id' => ':id']) }}",
            disparoListEnvios: "{{ route('disparo-automatico.envios') }}",
            disparoCriarEnvioPendente: "{{ route('disparo-automatico.envios.criar-pendente') }}",
            disparoReenviarEnvio: "{{ route('disparo-automatico.envios.reenviar', ['id' => ':id']) }}",
            disparoAtualizarEmailEnvio: "{{ route('disparo-automatico.envios.email', ['id' => ':id']) }}",
            disparoAtualizarTelefoneEnvio: "{{ route('disparo-automatico.envios.telefone', ['id' => ':id']) }}",
        };
    </script>
    <script src="{{ asset('js/dashboard/followUp/junsoft.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/dashboard/followUp/disparosAutomaticos.js') }}?v={{ time() }}"></script>
@stop
