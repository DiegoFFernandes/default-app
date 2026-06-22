@extends('layouts.master')

@section('title', 'WhatsApp - WppConnect')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card card-success card-outline card-outline-tabs">

                {{-- Nav Tabs --}}
                <div class="card-header p-0 d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs border-bottom-0" id="tabs-wpp" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-conexao" data-toggle="pill"
                               href="#pane-conexao" role="tab">
                                <i class="fab fa-whatsapp mr-1"></i> Conexão
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-disparos" data-toggle="pill"
                               href="#pane-disparos" role="tab">
                                <i class="fas fa-paper-plane mr-1"></i> Disparos Automáticos
                            </a>
                        </li>
                    </ul>
                    <div class="card-tools mr-2">
                        <button class="btn btn-success btn-xs d-none" id="btn-atualizar-disparos">
                            <i class="fas fa-sync-alt mr-1"></i> Atualizar
                        </button>
                    </div>
                </div>

                {{-- Tab Content --}}
                <div class="card-body">
                    <div class="tab-content">

                        {{-- Tab: Conexão --}}
                        <div class="tab-pane fade show active" id="pane-conexao" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-md-5 col-lg-4">

                                    <div class="card card-outline card-success mb-0" id="card-connected" style="display:none!important;">
                                        <div class="card-header text-center">
                                            <h4 class="card-title mb-0">
                                                <i class="fas fa-check-circle text-success mr-2"></i>WhatsApp Conectado
                                            </h4>
                                        </div>
                                        <div class="card-body text-center py-4">
                                            <i class="fab fa-whatsapp text-success" style="font-size: 80px;"></i>
                                            <p class="mt-3 mb-0 text-muted" id="status-label">Sessão ativa</p>
                                        </div>
                                    </div>

                                    <div class="card card-outline card-warning mb-0" id="card-qrcode">
                                        <div class="card-header text-center">
                                            <h4 class="card-title mb-0">
                                                <i class="fab fa-whatsapp mr-2"></i>Conectar WhatsApp
                                            </h4>
                                        </div>
                                        <div class="card-body text-center">

                                            <div id="area-loading" class="py-4">
                                                <div class="spinner-border text-warning" role="status"></div>
                                                <p class="mt-2 text-muted" id="loading-text">Verificando conexão...</p>
                                            </div>

                                            <div id="area-qrcode" style="display:none;">
                                                <p class="text-muted mb-2" style="font-size:13px;">
                                                    Abra o WhatsApp &rarr; <strong>Dispositivos conectados</strong> &rarr; <strong>Conectar dispositivo</strong>
                                                </p>
                                                <img id="qrcode-img" src="" alt="QR Code"
                                                     class="img-fluid rounded border"
                                                     style="max-width:260px; margin:0 auto; display:block;">
                                                <p class="text-muted mt-2" style="font-size:12px;">
                                                    <i class="fas fa-sync-alt mr-1"></i>QR Code atualiza automaticamente
                                                </p>
                                            </div>

                                            <div id="area-erro" style="display:none;" class="py-3">
                                                <i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>
                                                <p class="mt-2 text-danger" id="erro-text">Erro ao conectar.</p>
                                                <button class="btn btn-warning btn-sm" id="btn-tentar-novamente">
                                                    <i class="fas fa-redo mr-1"></i>Tentar novamente
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Tab: Disparos Automáticos --}}
                        <div class="tab-pane fade" id="pane-disparos" role="tabpanel">
                            <table class="table table-sm table-hover table-bordered compact table-font-small" id="table-disparos" style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>Usuário</th>
                                        <th>Telefone</th>
                                        <th>Mensagem</th>
                                        <th style="width:90px;" class="text-center">Status</th>
                                        <th style="width:150px;">Enviado em</th>
                                        <th style="width:150px;">Registrado em</th>
                                        <th style="width:90px;" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function () {

    // ============================================================
    // TAB CONEXÃO
    // ============================================================

    const INTERVAL_STATUS = 4000;
    const INTERVAL_QRCODE = 20000;
    let timerStatus = null;
    let timerQrCode = null;
    let tentativas  = 0;

    function mostrarLoading(texto) {
        $('#area-loading').show();
        $('#area-qrcode').hide();
        $('#area-erro').hide();
        $('#loading-text').text(texto || 'Aguarde...');
    }

    function mostrarQrCode(base64) {
        $('#area-loading').hide();
        $('#area-erro').hide();
        $('#area-qrcode').show();
        $('#qrcode-img').attr('src', base64.startsWith('data:') ? base64 : 'data:image/png;base64,' + base64);
    }

    function mostrarErro(msg) {
        $('#area-loading').hide();
        $('#area-qrcode').hide();
        $('#area-erro').show();
        $('#erro-text').text(msg || 'Erro ao conectar com o servidor.');
        pararTimers();
    }

    function mostrarConectado(data) {
        pararTimers();
        $('#card-qrcode').hide();
        $('#card-connected').css('display', '').show();
        const versao = data?.version ? ' · v' + data.version : '';
        $('#status-label').text('Sessão: {{ config('services.wppconnect.session') }}' + versao);
    }

    function pararTimers() {
        clearInterval(timerStatus);
        clearInterval(timerQrCode);
    }

    function verificarStatus() {
        $.get('{{ route('wppconnect.status') }}')
            .done(function (res) {
                if (res.connected) mostrarConectado(res.data);
            })
            .fail(function () {
                tentativas++;
                if (tentativas >= 5) mostrarErro('Servidor WppConnect não está respondendo.');
            });
    }

    function buscarQrCode() {
        $.get('{{ route('wppconnect.qrcode') }}')
            .done(function (res) {
                if (res.success && res.data?.qrcode) mostrarQrCode(res.data.qrcode);
            });
    }

    function iniciarSessao() {
        mostrarLoading('Iniciando sessão...');
        tentativas = 0;
        $.post('{{ route('wppconnect.start-session') }}', { _token: '{{ csrf_token() }}' })
            .done(function () {
                mostrarLoading('Gerando QR Code...');
                setTimeout(function () {
                    buscarQrCode();
                    timerStatus = setInterval(verificarStatus, INTERVAL_STATUS);
                    timerQrCode = setInterval(buscarQrCode,  INTERVAL_QRCODE);
                }, 2000);
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message ?? 'Falha ao iniciar sessão.';
                console.error('[WppConnect] iniciarSessao falhou', {
                    status:   xhr.status,
                    statusText: xhr.statusText,
                    message:  msg,
                    response: xhr.responseJSON ?? xhr.responseText,
                });
                mostrarErro(msg);
            });
    }

    $('#btn-tentar-novamente').on('click', function () { iniciarSessao(); });

    mostrarLoading('Verificando conexão...');
    $.get('{{ route('wppconnect.status') }}')
        .done(function (res) {
            if (res.connected) mostrarConectado(res.data);
            else iniciarSessao();
        })
        .fail(function () { iniciarSessao(); });

    // ============================================================
    // TAB DISPAROS
    // ============================================================

    let dtDisparos = null;

    function initDisparos() {
        if (dtDisparos) { dtDisparos.ajax.reload(null, false); return; }

        dtDisparos = $('#table-disparos').DataTable({
            ajax: {
                url: '{{ route('wppconnect.disparos') }}',
                dataSrc: 'data',
            },
            columns: [
                { data: 'id',          width: '50px' },
                { data: 'user' },
                { data: 'phone' },
                {
                    data: 'mensagem',
                    render: function (val, type, row) {
                        const esc = $('<div>').text(val).html();
                        const icon = row.erro
                            ? ` <i class="fas fa-exclamation-circle text-danger" title="${$('<div>').text(row.erro).html()}"></i>`
                            : '';
                        return `<span style="display:inline-block;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${esc}">${esc}</span>${icon}`;
                    },
                },
                {
                    data: 'status',
                    width: '90px',
                    className: 'text-center',
                    render: function (val) {
                        return val === 'E'
                            ? '<span class="badge badge-success">Enviado</span>'
                            : '<span class="badge badge-danger">Falha</span>';
                    },
                },
                { data: 'dt_envio',    width: '150px' },
                { data: 'dt_registro', width: '150px' },
                {
                    data: null,
                    width: '90px',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function (val, type, row) {
                        if (row.status !== 'F') return '';
                        return `<button class="btn btn-warning btn-xs btn-reenviar" data-id="${row.id}" title="Reenviar">
                                    <i class="fas fa-redo mr-1"></i>Reenviar
                                </button>`;
                    },
                },
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: { url: '{{ asset('vendor/datatables/pt-br.json') }}' },
        });
    }

    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        const isDisparos = $(e.target).attr('href') === '#pane-disparos';
        $('#btn-atualizar-disparos').toggleClass('d-none', !isDisparos);
        if (isDisparos) initDisparos();
    });

    $('#btn-atualizar-disparos').on('click', function () {
        if (dtDisparos) dtDisparos.ajax.reload(null, false);
    });

    $('body').on('click', '.btn-reenviar', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Reenviar mensagem?',
            text: 'Uma nova mensagem será enviada para o aprovador com um novo link de ação.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Sim, reenviar',
            cancelButtonText: 'Cancelar',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ url('wppconnect/disparos') }}/' + id + '/reenviar', {
                _token: '{{ csrf_token() }}',
            }, function (res) {
                if (res.errors) {
                    Swal.fire('Erro', res.errors, 'error');
                } else {
                    Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                    dtDisparos.ajax.reload(null, false);
                }
            }).fail(function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao reenviar.', 'error');
            });
        });
    });

});
</script>
@endsection
