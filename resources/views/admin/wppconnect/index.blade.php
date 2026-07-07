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
                        <li class="nav-item">
                            <a class="nav-link" id="tab-parametros" data-toggle="pill"
                               href="#pane-parametros" role="tab">
                                <i class="fas fa-cog mr-1"></i> Parâmetros
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
                                            <div class="btn-group btn-group-sm mt-2 mb-1" role="group">
                                                <button type="button" class="btn btn-outline-warning" id="btn-modo-qrcode">
                                                    <i class="fas fa-qrcode mr-1"></i>QR Code
                                                </button>
                                                <button type="button" class="btn btn-warning active" id="btn-modo-phone">
                                                    <i class="fas fa-mobile-alt mr-1"></i>Número
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body text-center">

                                            {{-- Modo QR Code --}}
                                            <div id="modo-qrcode" style="display:none;">
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

                                            {{-- Modo Número --}}
                                            <div id="modo-phone">

                                                <div id="area-phone-form" class="py-3">
                                                    <p class="text-muted mb-3" style="font-size:13px;">
                                                        Abra o WhatsApp &rarr; <strong>Aparelhos vinculados</strong> &rarr; <strong>Vincular com número de telefone</strong>
                                                    </p>
                                                    <div class="input-group mb-3" style="max-width:280px; margin:0 auto;">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                        </div>
                                                        <input type="text" id="input-phone" class="form-control"
                                                               placeholder="55119XXXXXXXX" maxlength="15">
                                                    </div>
                                                    <button class="btn btn-warning" id="btn-gerar-codigo">
                                                        <i class="fas fa-key mr-1"></i>Gerar Código
                                                    </button>
                                                </div>

                                                <div id="area-phone-loading" style="display:none;" class="py-4">
                                                    <div class="spinner-border text-warning" role="status"></div>
                                                    <p class="mt-2 text-muted">Gerando código de pareamento...</p>
                                                </div>

                                                <div id="area-phone-code" style="display:none;" class="py-3">
                                                    <p class="text-muted mb-2" style="font-size:13px;">
                                                        Digite este código no WhatsApp:
                                                    </p>
                                                    <div class="display-4 font-weight-bold text-warning letter-spacing-lg" id="pairing-code">----</div>
                                                    <p class="text-muted mt-3" style="font-size:12px;">
                                                        <i class="fas fa-clock mr-1"></i>O código expira em alguns minutos
                                                    </p>
                                                    <button class="btn btn-outline-warning btn-sm mt-1" id="btn-novo-codigo">
                                                        <i class="fas fa-redo mr-1"></i>Gerar novo código
                                                    </button>
                                                </div>

                                                <div id="area-phone-erro" style="display:none;" class="py-3">
                                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>
                                                    <p class="mt-2 text-danger" id="phone-erro-text">Erro ao gerar código.</p>
                                                    <button class="btn btn-warning btn-sm" id="btn-phone-tentar-novamente">
                                                        <i class="fas fa-redo mr-1"></i>Tentar novamente
                                                    </button>
                                                </div>

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

                        {{-- Tab: Parametrizações --}}
                        <div class="tab-pane fade" id="pane-parametros" role="tabpanel">

                            {{-- Toggle IA ativa --}}
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-robot mr-2"></i>Respostas via IA (WhatsApp)
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-3" style="font-size:13px;">
                                                Quando ativado, usuários autorizados podem enviar perguntas de negócio
                                                via WhatsApp e receber respostas geradas por IA.
                                            </p>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="toggle-wppconnect-ia">
                                                <label class="custom-control-label font-weight-bold" for="toggle-wppconnect-ia">
                                                    Ativar IA via WhatsApp
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Usuários autorizados --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-users mr-2"></i>Usuários com acesso à IA via WhatsApp
                                            </h3>
                                            <div class="card-tools">
                                                <span class="text-muted" style="font-size:12px;">
                                                    Apenas usuários com telefone cadastrado são exibidos
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0" id="table-parametros-users">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Usuário</th>
                                                        <th style="width:140px;">Telefone</th>
                                                        <th style="width:220px;">WPP LID</th>
                                                        <th style="width:90px;" class="text-center">Status</th>
                                                        <th style="width:110px;" class="text-center">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-parametros-users">
                                                    <tr>
                                                        <td colspan="5" class="text-center py-3">
                                                            <div class="spinner-border spinner-border-sm text-primary"></div>
                                                            <span class="ml-2 text-muted">Carregando...</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- LIDs pendentes de associação --}}
                            <div class="row" id="card-lids-pendentes" style="display:none;">
                                <div class="col-12">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-link mr-2"></i>LIDs detectados — aguardando associação
                                            </h3>
                                            <div class="card-tools">
                                                <span class="text-muted" style="font-size:12px;">
                                                    Remetentes que enviaram mensagem mas ainda não estão vinculados a um usuário
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0" id="table-lids-pendentes">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Nome (WhatsApp)</th>
                                                        <th>LID</th>
                                                        <th>Última mensagem</th>
                                                        <th style="width:120px;">Recebido em</th>
                                                        <th style="width:200px;">Associar a</th>
                                                        <th style="width:90px;" class="text-center">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-lids-pendentes"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
    const INTERVAL_QRCODE = 5000;
    let timerStatus   = null;
    let timerQrCode   = null;
    let tentativas    = 0;
    let tentativasQr  = 0;

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

    const MAX_TENTATIVAS_QR = 5;

    function buscarQrCode() {
        if (tentativasQr >= MAX_TENTATIVAS_QR) {
            pararTimers();
            mostrarErro('QR Code não gerado após ' + MAX_TENTATIVAS_QR + ' tentativas. Verifique se o servidor WppConnect está ativo e tente novamente.');
            return;
        }
        tentativasQr++;
        $.get('{{ route('wppconnect.qrcode') }}')
            .done(function (res) {
                if (res.success && res.data?.qrcode) {
                    tentativasQr = 0;
                    mostrarQrCode(res.data.qrcode);
                } else {
                    mostrarLoading('Aguardando QR Code... (tentativa ' + tentativasQr + ' de ' + MAX_TENTATIVAS_QR + ')');
                }
            })
            .fail(function () {
                mostrarLoading('Erro ao buscar QR Code (tentativa ' + tentativasQr + ' de ' + MAX_TENTATIVAS_QR + ')');
            });
    }

    function iniciarSessao() {
        mostrarLoading('Iniciando sessão...');
        tentativas = 0;
        $.post('{{ route('wppconnect.start-session') }}', { _token: '{{ csrf_token() }}' })
            .done(function () {
                tentativasQr = 0;
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

    // ============================================================
    // MODO NÚMERO DE TELEFONE
    // ============================================================

    $('#btn-modo-qrcode').on('click', function () {
        if ($('#btn-modo-qrcode').hasClass('active')) return;
        $('#btn-modo-qrcode').addClass('active').removeClass('btn-outline-warning').addClass('btn-warning');
        $('#btn-modo-phone').removeClass('active').addClass('btn-outline-warning').removeClass('btn-warning');
        pararTimerPhoneCode();
        $('#modo-phone').hide();
        $('#modo-qrcode').show();
        tentativas = 0;
        tentativasQr = 0;
        mostrarLoading('Encerrando sessão anterior...');
        $.post('{{ route('wppconnect.logout-session') }}', { _token: '{{ csrf_token() }}' })
            .always(function () {
                mostrarLoading('Aguardando encerramento...');
                setTimeout(function () { iniciarSessao(); }, 2000);
            });
    });

    $('#btn-modo-phone').on('click', function () {
        $('#btn-modo-phone').addClass('active').removeClass('btn-outline-warning').addClass('btn-warning');
        $('#btn-modo-qrcode').removeClass('active').addClass('btn-outline-warning').removeClass('btn-warning');
        pararTimers();
        $('#modo-qrcode').hide();
        $('#modo-phone').show();
        $('#area-phone-form').show();
        $('#area-phone-loading').hide();
        $('#area-phone-code').hide();
        $('#area-phone-erro').hide();
    });

    let timerPhoneCode = null;
    const INTERVAL_PHONE_CODE = 3000;
    const MAX_TENTATIVAS_CODE = 30; // 30 x 3s = 90s aguardando o código
    let tentativasCode = 0;

    function pararTimerPhoneCode() {
        clearInterval(timerPhoneCode);
        timerPhoneCode = null;
        tentativasCode = 0;
    }

    function aguardarPhoneCode() {
        pararTimerPhoneCode();
        tentativasCode = 0;
        timerPhoneCode = setInterval(function () {
            tentativasCode++;
            $.get('{{ route('wppconnect.phone-code') }}')
                .done(function (res) {
                    if (res.code) {
                        pararTimerPhoneCode();
                        $('#pairing-code').text(res.code);
                        $('#area-phone-loading').hide();
                        $('#area-phone-code').show();
                        timerStatus = setInterval(function () {
                            $.get('{{ route('wppconnect.status') }}').done(function (r) {
                                if (r.connected) {
                                    pararTimerPhoneCode();
                                    mostrarConectado(r.data);
                                }
                            });
                        }, INTERVAL_STATUS);
                    } else if (tentativasCode >= MAX_TENTATIVAS_CODE) {
                        pararTimerPhoneCode();
                        $('#area-phone-loading').hide();
                        $('#phone-erro-text').text('Tempo esgotado. O código não foi gerado.');
                        $('#area-phone-erro').show();
                    }
                });
        }, INTERVAL_PHONE_CODE);
    }

    function gerarCodigoPareamento() {
        const phone = $('#input-phone').val().trim().replace(/\D/g, '');
        if (phone.length < 10) {
            Swal.fire('Atenção', 'Informe o número com DDD e DDI (ex: 5511999999999).', 'warning');
            return;
        }

        pararTimers();
        pararTimerPhoneCode();
        $('#area-phone-form').hide();
        $('#area-phone-code').hide();
        $('#area-phone-erro').hide();
        $('#area-phone-loading').show();

        $.post('{{ route('wppconnect.start-session-phone') }}', {
            _token: '{{ csrf_token() }}',
            phone: phone,
        })
        .done(function () {
            aguardarPhoneCode();
        })
        .fail(function (xhr) {
            $('#area-phone-loading').hide();
            const msg = xhr.responseJSON?.message ?? 'Falha ao iniciar sessão.';
            $('#phone-erro-text').text(msg);
            $('#area-phone-erro').show();
        });
    }

    $('#btn-gerar-codigo').on('click', gerarCodigoPareamento);

    $('#btn-novo-codigo, #btn-phone-tentar-novamente').on('click', function () {
        pararTimers();
        $('#area-phone-code').hide();
        $('#area-phone-erro').hide();
        $('#area-phone-form').show();
    });

    $.get('{{ route('wppconnect.status') }}')
        .done(function (res) {
            if (res.connected) mostrarConectado(res.data);
        });

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
        const href = $(e.target).attr('href');
        $('#btn-atualizar-disparos').toggleClass('d-none', href !== '#pane-disparos');
        if (href === '#pane-disparos')   initDisparos();
        if (href === '#pane-parametros') { initParametros(); carregarLidsPendentes(); }
    });

    $('#btn-atualizar-disparos').on('click', function () {
        if (dtDisparos) dtDisparos.ajax.reload(null, false);
    });

    // ============================================================
    // TAB PARAMETRIZAÇÕES
    // ============================================================

    let parametrosCarregado = false;

    function initParametros() {
        if (parametrosCarregado) return;
        parametrosCarregado = true;

        $.get('{{ route('wppconnect.parametros') }}').done(function (res) {
            // Toggle IA
            $('#toggle-wppconnect-ia').prop('checked', res.wpp_ia_ativo);

            // Tabela de usuários
            const rows = res.usuarios.map(u => {
                const badge = u.wpp_ia
                    ? '<span class="badge badge-success">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>';
                const btn = u.wpp_ia
                    ? `<button class="btn btn-xs btn-danger btn-toggle-wppconnect-ia" data-id="${u.id}"><i class="fas fa-times mr-1"></i>Revogar</button>`
                    : `<button class="btn btn-xs btn-success btn-toggle-wppconnect-ia" data-id="${u.id}"><i class="fas fa-check mr-1"></i>Liberar</button>`;
                const lidInput = `<div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm input-wpp-lid"
                           data-id="${u.id}" value="${u.wpp_lid || ''}"
                           placeholder="ex: 177451523166369"
                           style="font-size:11px;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm btn-salvar-lid" data-id="${u.id}" title="Salvar LID">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </div>`;
                return `<tr data-id="${u.id}">
                    <td>${u.name}</td>
                    <td>${u.phone || '<span class="text-muted">—</span>'}</td>
                    <td class="td-lid">${lidInput}</td>
                    <td class="text-center td-status">${badge}</td>
                    <td class="text-center td-acao">${btn}</td>
                </tr>`;
            });
            $('#tbody-parametros-users').html(rows.join('') || '<tr><td colspan="5" class="text-center text-muted">Nenhum usuário com telefone cadastrado.</td></tr>');
        }).fail(function () {
            $('#tbody-parametros-users').html('<tr><td colspan="5" class="text-center text-danger">Erro ao carregar dados.</td></tr>');
        });
    }

    // Toggle IA ativo/inativo
    $(document).on('change', '#toggle-wppconnect-ia', function () {
        const ativo = $(this).is(':checked');
        $.post('{{ url('wppconnect/parametros/wpp_ia_ativo') }}', {
            _token: '{{ csrf_token() }}',
            valor: ativo ? 1 : 0,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        }).fail(function () {
            $('#toggle-wppconnect-ia').prop('checked', !ativo);
            Swal.fire('Erro', 'Não foi possível salvar o parâmetro.', 'error');
        });
    });

    // Toggle permissão por usuário
    $(document).on('click', '.btn-toggle-wppconnect-ia', function () {
        const id  = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);
        $.post('{{ url('wppconnect/usuarios') }}/' + id + '/wppconnect-ia', {
            _token: '{{ csrf_token() }}',
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
            // Atualiza as duas colunas sem recarregar tudo
            const badge = res.wpp_ia ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">Inativo</span>';
            const newBtn = res.wpp_ia
                ? `<button class="btn btn-sm btn-danger btn-toggle-wppconnect-ia" data-id="${id}"><i class="fas fa-times mr-1"></i>Revogar</button>`
                : `<button class="btn btn-sm btn-success btn-toggle-wppconnect-ia" data-id="${id}"><i class="fas fa-check mr-1"></i>Liberar</button>`;
            $(`tr[data-id="${id}"] .td-status`).html(badge);
            $(`tr[data-id="${id}"] .td-acao`).html(newBtn);
        }).fail(function (xhr) {
            Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao alterar permissão.', 'error');
            btn.prop('disabled', false);
        });
    });

    // LIDs pendentes de associação
    function carregarLidsPendentes() {
        $.get('{{ route('wppconnect.lids-pendentes') }}')
        .fail(function (xhr) { console.error('[LIDs pendentes] erro:', xhr.status, xhr.responseText); })
        .done(function (res) {
            if (!res.pendentes || !res.pendentes.length) {
                $('#card-lids-pendentes').hide();
                return;
            }
            $('#card-lids-pendentes').show();

            const selectOpts = res.usuarios.map(u =>
                `<option value="${u.id}">${u.name} (${u.phone})</option>`
            ).join('');

            const rows = res.pendentes.map(p => {
                const texto = p.ultimo_texto
                    ? `<span title="${p.ultimo_texto}" style="max-width:180px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${p.ultimo_texto}</span>`
                    : '<span class="text-muted">—</span>';
                return `<tr>
                    <td><strong>${p.pushname}</strong></td>
                    <td><code>${p.lid}</code></td>
                    <td>${texto}</td>
                    <td>${p.updated_at}</td>
                    <td>
                        <select class="form-control form-control-sm select-associar" data-lid="${p.lid}">
                            <option value="">— selecione —</option>
                            ${selectOpts}
                        </select>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-xs btn-warning btn-associar-lid" data-lid="${p.lid}">
                            <i class="fas fa-link mr-1"></i>Associar
                        </button>
                    </td>
                </tr>`;
            });
            $('#tbody-lids-pendentes').html(rows.join(''));
        });
    }

    $(document).on('click', '.btn-associar-lid', function () {
        const lid    = $(this).data('lid');
        const userId = $(`.select-associar[data-lid="${lid}"]`).val();
        if (!userId) {
            Swal.fire('Atenção', 'Selecione o usuário para associar.', 'warning');
            return;
        }
        $.post('{{ route('wppconnect.lids-pendentes.associar') }}', {
            _token: '{{ csrf_token() }}',
            lid:     lid,
            user_id: userId,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
            parametrosCarregado = false;
            initParametros();
        }).fail(function (xhr) {
            Swal.fire('Erro', xhr.responseJSON?.errors ?? xhr.responseJSON?.message ?? 'Falha ao associar.', 'error');
        });
    });

    // Salvar WPP LID por usuário
    $(document).on('click', '.btn-salvar-lid', function () {
        const id  = $(this).data('id');
        const lid = $(`input.input-wpp-lid[data-id="${id}"]`).val().trim();
        const btn = $(this);
        btn.prop('disabled', true);
        $.post('{{ url('wppconnect/usuarios') }}/' + id + '/wpp-lid', {
            _token: '{{ csrf_token() }}',
            wpp_lid: lid,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
            $(`input.input-wpp-lid[data-id="${id}"]`).val(res.wpp_lid);
        }).fail(function (xhr) {
            const msg = xhr.responseJSON?.errors
                ?? (xhr.responseJSON?.message)
                ?? 'Falha ao salvar LID.';
            Swal.fire('Erro', msg, 'error');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    $('body').on('click', '.btn-reenviar', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Reenviar mensagem?',
            text: 'Uma nova mensagem será enviada.',
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
