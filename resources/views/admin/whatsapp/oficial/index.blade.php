@extends('layouts.master')

@section('title', 'WhatsApp - API Oficial (WABA)')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card card-success card-outline card-outline-tabs">
                @include('admin.whatsapp.oficial.tabs.nav-tabs')

                <div class="card-body">
                    <div class="tab-content">
                        @include('admin.whatsapp.oficial.tabs.tab-modelos')

                        @include('admin.whatsapp.oficial.tabs.tab-mensagens')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('css')
<style>
    .wa-preview-wrap {
        background: #e5ddd5;
        border-radius: 8px;
        padding: 20px 12px;
        min-height: 220px;
    }
    .wa-preview-bubble {
        background: #fff;
        border-radius: 8px;
        padding: 8px 10px 18px 10px;
        max-width: 100%;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.15);
        font-size: 14px;
        word-wrap: break-word;
    }
    .wa-preview-header {
        font-weight: 700;
        margin-bottom: 4px;
    }
    .wa-preview-header:empty { display: none; }
    .wa-preview-header.doc {
        background: #f0f0f0;
        border-radius: 6px;
        padding: 10px;
        font-weight: 400;
        color: #555;
    }
    .wa-preview-body {
        white-space: pre-wrap;
        color: #111;
    }
    .wa-preview-footer {
        color: #667781;
        font-size: 12px;
        margin-top: 6px;
    }
    .wa-preview-footer:empty { display: none; }
    .wa-preview-time {
        position: absolute;
        bottom: 4px;
        right: 10px;
        font-size: 11px;
        color: #999;
    }

    /* Aba Mensagens */
    .wa-chat-wrap {
        background: #e5ddd5;
        border-radius: 8px;
        padding: 16px;
        min-height: 420px;
        max-height: 420px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .wa-chat-bubble {
        max-width: 75%;
        padding: 6px 10px 16px 10px;
        border-radius: 8px;
        margin-bottom: 8px;
        position: relative;
        font-size: 14px;
        white-space: pre-wrap;
        word-wrap: break-word;
        box-shadow: 0 1px 1px rgba(0,0,0,.15);
    }
    .wa-chat-bubble.enviada {
        background: #d9fdd3;
        align-self: flex-end;
    }
    .wa-chat-bubble.recebida {
        background: #fff;
        align-self: flex-start;
    }
    .wa-chat-bubble .wa-chat-time {
        position: absolute;
        bottom: 4px;
        right: 10px;
        font-size: 10px;
        color: #999;
    }
    .wa-chat-bubble .wa-chat-status {
        margin-left: 4px;
    }
</style>
@stop

@section('js')
<script type="text/javascript">
    window.routesWhatsappOficial = {
        templateStore: "{{ route('whatsapp-oficial.store') }}",
        templateSincronizar: "{{ route('whatsapp-oficial.sincronizar') }}",
        templateBase: "{{ url('whatsapp-oficial') }}",
        mensagensListar: "{{ route('whatsapp-oficial.mensagens.index') }}",
        mensagensEnviar: "{{ route('whatsapp-oficial.mensagens.store') }}",
    };
</script>
<script src="{{ asset('js/dashboard/whatsappOficial/modelos.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/dashboard/whatsappOficial/mensagens.js') }}?v={{ time() }}"></script>
@stop
