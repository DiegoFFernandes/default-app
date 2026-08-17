@extends('layouts.master')

@section('title', 'Pré-visualização do Disparo')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fa fa-flask mr-1"></i>
                    Esta é uma pré-visualização — Os PDFs abaixo foram gerados apenas para conferência.
                </div>

                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            Envio #{{ $envio->CD_ENVIO }}
                            @php
                                $labelsStatus = [
                                    'A' => '<span class="badge badge-warning">Aguardando</span>',
                                    'E' => '<span class="badge badge-success">Enviado</span>',
                                    'V' => '<span class="badge badge-info">Enviado c/ Falha</span>',
                                    'F' => '<span class="badge badge-danger">Falha</span>',
                                ];
                            @endphp
                            {!! $labelsStatus[$envio->ST_ENVIO] ?? $envio->ST_ENVIO !!}
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-2">Cliente</dt>
                            <dd class="col-sm-10">{{ $envio->CD_PESSOA }} - {{ $envio->NM_PESSOA }}</dd>

                            @if ($contexto->TP_CANAL === 'W')
                                <dt class="col-sm-2">Telefone</dt>
                                <dd class="col-sm-10">{{ $envio->DS_TELEFONE }}</dd>
                            @else
                                <dt class="col-sm-2">Destinatário</dt>
                                <dd class="col-sm-10">{{ $envio->DS_EMAILDEST }}</dd>

                                <dt class="col-sm-2">Cópia para</dt>
                                <dd class="col-sm-10">
                                    @if ($envio->DS_EMAILCOPIA)
                                        {{ $envio->DS_EMAILCOPIA }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-2">Assunto</dt>
                                <dd class="col-sm-10">{{ $envio->DS_ASSUNTO }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ $contexto->TP_CANAL === 'W' ? 'Mensagem (WhatsApp)' : 'Corpo do E-mail' }}</h3>
                    </div>
                    <div class="card-body p-0">
                        @if ($contexto->TP_CANAL === 'W')
                            <pre class="p-3 mb-0" style="white-space:pre-wrap; font-family:inherit;">{{ $email['corpo'] }}</pre>
                        @else
                            <iframe srcdoc="{{ $email['corpo'] }}" style="width:100%; height:420px; border:0;"></iframe>
                        @endif
                    </div>
                </div>

                <div class="card card-outline card-warning mb-0">
                    <div class="card-header">
                        <h3 class="card-title">Anexos</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($email['anexos'] as $indice => $anexo)
                            <div class="btn-group mr-2 mb-2">
                                <a href="{{ route('disparo-automatico.envios.preview.anexo', ['id' => $envio->CD_ENVIO, 'indice' => $indice]) }}"
                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-file-pdf mr-1"></i>{{ $anexo['titulo'] }} (PDF)
                                </a>
                                <a href="{{ route('disparo-automatico.envios.preview.anexo.html', ['id' => $envio->CD_ENVIO, 'indice' => $indice]) }}"
                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa fa-code mr-1"></i>HTML
                                </a>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Nenhum anexo gerado.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
