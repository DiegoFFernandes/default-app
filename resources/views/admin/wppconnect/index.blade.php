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
                        <li class="nav-item">
                            <a class="nav-link" id="tab-ia" data-toggle="pill"
                               href="#pane-ia" role="tab">
                                <i class="fas fa-robot mr-1"></i> Contexto IA
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

                            <div class="mb-3 text-right">
                                <button class="btn btn-success btn-sm" id="btn-add-conexao">
                                    <i class="fas fa-plus mr-1"></i>Adicionar Conexão
                                </button>
                            </div>

                            <div class="row" id="row-conexoes">
                                @foreach($sessions as $sess)
                                @php($session = $sess['name'])
                                <div class="col-md-6 col-lg-4" id="col-sessao-{{ $session }}">

                                    {{-- Conectado --}}
                                    <div class="card card-outline card-success mb-3 d-none" id="card-connected-{{ $session }}">
                                        <div class="card-header text-center">
                                            <h4 class="card-title mb-0">
                                                <i class="fas fa-check-circle text-success mr-2"></i>WhatsApp Conectado
                                            </h4>
                                        </div>
                                        <div class="card-body text-center py-4">
                                            <i class="fab fa-whatsapp text-success" style="font-size:64px;"></i>
                                            <p class="mt-3 mb-1 font-weight-bold" style="font-size:16px;">{{ $sess['label'] }}</p>
                                            <p class="mb-0 text-muted" id="status-label-{{ $session }}">Sessão ativa</p>
                                            <button class="btn btn-sm btn-outline-danger mt-3 btn-logout-session" data-session="{{ $session }}">
                                                <i class="fas fa-sign-out-alt mr-1"></i>Desconectar
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Conectar --}}
                                    <div class="card card-outline card-warning mb-3" id="card-qrcode-{{ $session }}">
                                        <div class="card-header text-center">
                                            <h4 class="card-title mb-0">
                                                <i class="fab fa-whatsapp mr-2"></i>{{ $sess['label'] }}
                                                <button class="btn btn-xs btn-outline-danger float-right btn-excluir-conexao"
                                                        data-setor="{{ $sess['setor'] }}" data-label="{{ $sess['label'] }}"
                                                        title="Excluir conexão">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </h4>
                                            <div class="btn-group btn-group-sm mt-2 mb-1" role="group">
                                                <button type="button" class="btn btn-outline-warning btn-modo-qrcode" data-session="{{ $session }}">
                                                    <i class="fas fa-qrcode mr-1"></i>QR Code
                                                </button>
                                                <button type="button" class="btn btn-warning active btn-modo-phone" data-session="{{ $session }}">
                                                    <i class="fas fa-mobile-alt mr-1"></i>Número
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body text-center">

                                            {{-- Modo QR Code --}}
                                            <div id="modo-qrcode-{{ $session }}" style="display:none;">
                                                <div id="area-loading-{{ $session }}" class="py-4">
                                                    <div class="spinner-border text-warning" role="status"></div>
                                                    <p class="mt-2 text-muted" id="loading-text-{{ $session }}">Verificando conexão...</p>
                                                </div>
                                                <div id="area-qrcode-{{ $session }}" style="display:none;">
                                                    <p class="text-muted mb-2" style="font-size:13px;">
                                                        Abra o WhatsApp &rarr; <strong>Dispositivos conectados</strong> &rarr; <strong>Conectar dispositivo</strong>
                                                    </p>
                                                    <img id="qrcode-img-{{ $session }}" src="" alt="QR Code"
                                                         class="img-fluid rounded border"
                                                         style="max-width:220px; margin:0 auto; display:block;">
                                                    <p class="text-muted mt-2" style="font-size:12px;">
                                                        <i class="fas fa-sync-alt mr-1"></i>QR Code atualiza automaticamente
                                                    </p>
                                                </div>
                                                <div id="area-erro-{{ $session }}" style="display:none;" class="py-3">
                                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>
                                                    <p class="mt-2 text-danger" id="erro-text-{{ $session }}">Erro ao conectar.</p>
                                                    <button class="btn btn-warning btn-sm btn-tentar-novamente" data-session="{{ $session }}">
                                                        <i class="fas fa-redo mr-1"></i>Tentar novamente
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Modo Número --}}
                                            <div id="modo-phone-{{ $session }}">
                                                <div id="area-phone-form-{{ $session }}" class="py-3">
                                                    <p class="text-muted mb-3" style="font-size:13px;">
                                                        Abra o WhatsApp &rarr; <strong>Aparelhos vinculados</strong> &rarr; <strong>Vincular com número de telefone</strong>
                                                    </p>
                                                    <div class="input-group mb-3" style="max-width:260px; margin:0 auto;">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                        </div>
                                                        <input type="text" id="input-phone-{{ $session }}" class="form-control"
                                                               placeholder="55119XXXXXXXX" maxlength="15">
                                                    </div>
                                                    <button class="btn btn-warning btn-gerar-codigo" data-session="{{ $session }}">
                                                        <i class="fas fa-key mr-1"></i>Gerar Código
                                                    </button>
                                                </div>
                                                <div id="area-phone-loading-{{ $session }}" style="display:none;" class="py-4">
                                                    <div class="spinner-border text-warning" role="status"></div>
                                                    <p class="mt-2 text-muted">Gerando código de pareamento...</p>
                                                </div>
                                                <div id="area-phone-code-{{ $session }}" style="display:none;" class="py-3">
                                                    <p class="text-muted mb-2" style="font-size:13px;">Digite este código no WhatsApp:</p>
                                                    <div class="display-4 font-weight-bold text-warning letter-spacing-lg" id="pairing-code-{{ $session }}">----</div>
                                                    <p class="text-muted mt-3" style="font-size:12px;">
                                                        <i class="fas fa-clock mr-1"></i>O código expira em alguns minutos
                                                    </p>
                                                    <button class="btn btn-outline-warning btn-sm mt-1 btn-novo-codigo" data-session="{{ $session }}">
                                                        <i class="fas fa-redo mr-1"></i>Gerar novo código
                                                    </button>
                                                </div>
                                                <div id="area-phone-erro-{{ $session }}" style="display:none;" class="py-3">
                                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>
                                                    <p class="mt-2 text-danger" id="phone-erro-text-{{ $session }}">Erro ao gerar código.</p>
                                                    <button class="btn btn-warning btn-sm btn-phone-tentar-novamente" data-session="{{ $session }}">
                                                        <i class="fas fa-redo mr-1"></i>Tentar novamente
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                @endforeach

                                <div class="col-12 {{ count($sessions) ? 'd-none' : '' }}" id="empty-conexoes">
                                    <div class="alert alert-light border text-center text-muted mb-0">
                                        <i class="fab fa-whatsapp mr-1"></i>
                                        Nenhuma conexão cadastrada. Clique em <strong>Adicionar Conexão</strong> para começar.
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

                        {{-- Tab: Contexto IA --}}
                        <div class="tab-pane fade" id="pane-ia" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-outline card-info mb-0">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-robot mr-2"></i>Intenções da IA
                                            </h3>
                                            <div class="card-tools">
                                                <span class="text-muted" style="font-size:12px;">
                                                    Apenas intenções ativas são enviadas ao GPT como opções de resposta
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-hover mb-0" id="table-ia-intencoes">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width:80px;">Intenção</th>
                                                        <th style="width:300px;">Descrição</th>
                                                        <th style="width:60px;" class="text-center">Status</th>
                                                        <th style="width:60px;" class="text-center">Ação</th>
                                                        <th style="width:60px;" class="text-center">SQL</th>
                                                        <th style="width:60px;" class="text-center">Prévia</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-ia-intencoes">
                                                    <tr>
                                                        <td colspan="5" class="text-center py-3">
                                                            <div class="spinner-border spinner-border-sm text-info"></div>
                                                            <span class="ml-2 text-muted">Carregando...</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
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

{{-- Modal: Adicionar Conexão --}}
<div class="modal fade" id="modal-add-conexao" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp text-success mr-2"></i>Adicionar Conexão
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1" for="select-setor">Setor</label>
                    <select id="select-setor" class="form-control">
                        <option value="">— selecione —</option>
                    </select>
                    <small class="form-text text-muted">
                        Cada setor tem seu próprio número de WhatsApp. Setores já conectados não aparecem na lista.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm" id="btn-salvar-conexao">
                    <i class="fas fa-plus mr-1"></i>Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Prévia da Intenção IA --}}
<div class="modal fade" id="modal-previa-ia" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp text-success mr-2"></i>
                    Prévia — <span id="previa-nome-intencao"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold mb-1" style="font-size:12px;">Data Início</label>
                        <input type="date" id="previa-dt-inicio" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold mb-1" style="font-size:12px;">Data Fim</label>
                        <input type="date" id="previa-dt-fim" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-info btn-sm w-100" id="btn-gerar-previa">
                            <i class="fas fa-sync-alt mr-1"></i>Gerar Prévia
                        </button>
                    </div>
                </div>

                <div id="previa-loading" style="display:none;" class="text-center py-4">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="mt-2 text-muted mb-0">Consultando Firebird...</p>
                </div>

                <div id="previa-erro" style="display:none;" class="alert alert-danger py-2 mb-0"></div>

                <div id="previa-resultado" style="display:none;">
                    <label class="font-weight-bold mb-1" style="font-size:12px;">
                        <i class="fab fa-whatsapp text-success mr-1"></i>Texto que chegará no WhatsApp:
                    </label>
                    <pre id="previa-texto"
                         style="background:#e5ddd5; border-radius:8px; padding:16px;
                                font-family:inherit; font-size:13px; line-height:1.6;
                                white-space:pre-wrap; max-height:420px; overflow-y:auto;
                                border:none; color:#1a1a1a;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
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
    // TAB CONEXÃO — MULTI-SESSÃO
    // ============================================================

    function createSessionWidget(session) {
        var INTERVAL_STATUS = 4000;
        var INTERVAL_QRCODE = 5000;
        var INTERVAL_PHONE  = 3000;
        var MAX_QR          = 5;
        var MAX_CODE        = 30;

        var timerStatus    = null;
        var timerQrCode    = null;
        var timerPhoneCode = null;
        var tentativas     = 0;
        var tentativasQr   = 0;
        var tentativasCode = 0;

        function sid(id)    { return '#' + id + '-' + session; }
        function addSess(u) { return u + (u.indexOf('?') === -1 ? '?' : '&') + 'session=' + session; }

        function pararTimers() {
            clearInterval(timerStatus);
            clearInterval(timerQrCode);
            timerStatus = timerQrCode = null;
        }

        function pararTimerPhone() {
            clearInterval(timerPhoneCode);
            timerPhoneCode = null;
            tentativasCode = 0;
        }

        function mostrarLoading(texto) {
            $(sid('area-loading')).show();
            $(sid('area-qrcode')).hide();
            $(sid('area-erro')).hide();
            $(sid('loading-text')).text(texto || 'Aguarde...');
        }

        function mostrarQrCode(base64) {
            $(sid('area-loading')).hide();
            $(sid('area-erro')).hide();
            $(sid('area-qrcode')).show();
            var src = base64.startsWith('data:') ? base64 : 'data:image/png;base64,' + base64;
            $(sid('qrcode-img')).attr('src', src);
        }

        function mostrarErro(msg) {
            $(sid('area-loading')).hide();
            $(sid('area-qrcode')).hide();
            $(sid('area-erro')).show();
            $(sid('erro-text')).text(msg || 'Erro ao conectar.');
            pararTimers();
        }

        function mostrarConectado(data) {
            pararTimers();
            pararTimerPhone();
            $(sid('card-qrcode')).hide();
            $(sid('card-connected')).removeClass('d-none').show();
            var versao = data && data.version ? ' · v' + data.version : '';
            $(sid('status-label')).text('Sessão: ' + session + versao);
        }

        function verificarStatus() {
            $.get(addSess('{{ route('wppconnect.status') }}'))
                .done(function (res) {
                    if (res.connected) mostrarConectado(res.data);
                })
                .fail(function () {
                    tentativas++;
                    if (tentativas >= 5) mostrarErro('Servidor WppConnect não está respondendo.');
                });
        }

        function buscarQrCode() {
            if (tentativasQr >= MAX_QR) {
                pararTimers();
                mostrarErro('QR Code não gerado após ' + MAX_QR + ' tentativas. Verifique se o servidor WppConnect está ativo.');
                return;
            }
            tentativasQr++;
            $.get(addSess('{{ route('wppconnect.qrcode') }}'))
                .done(function (res) {
                    if (res.success && res.data && res.data.qrcode) {
                        tentativasQr = 0;
                        mostrarQrCode(res.data.qrcode);
                    } else {
                        mostrarLoading('Aguardando QR Code... (' + tentativasQr + '/' + MAX_QR + ')');
                    }
                })
                .fail(function () {
                    mostrarLoading('Erro ao buscar QR Code (' + tentativasQr + '/' + MAX_QR + ')');
                });
        }

        function iniciarSessao() {
            mostrarLoading('Iniciando sessão...');
            tentativas = 0;
            $.post(addSess('{{ route('wppconnect.start-session') }}'), { _token: '{{ csrf_token() }}' })
                .done(function () {
                    tentativasQr = 0;
                    mostrarLoading('Gerando QR Code...');
                    setTimeout(function () {
                        buscarQrCode();
                        timerStatus = setInterval(verificarStatus, INTERVAL_STATUS);
                        timerQrCode = setInterval(buscarQrCode, INTERVAL_QRCODE);
                    }, 2000);
                })
                .fail(function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Falha ao iniciar sessão.';
                    mostrarErro(msg);
                });
        }

        function aguardarPhoneCode() {
            pararTimerPhone();
            tentativasCode = 0;
            timerPhoneCode = setInterval(function () {
                tentativasCode++;
                $.get(addSess('{{ route('wppconnect.phone-code') }}'))
                    .done(function (res) {
                        if (res.code) {
                            pararTimerPhone();
                            $(sid('pairing-code')).text(res.code);
                            $(sid('area-phone-loading')).hide();
                            $(sid('area-phone-code')).show();
                            timerStatus = setInterval(function () {
                                $.get(addSess('{{ route('wppconnect.status') }}')).done(function (r) {
                                    if (r.connected) mostrarConectado(r.data);
                                });
                            }, INTERVAL_STATUS);
                        } else if (tentativasCode >= MAX_CODE) {
                            pararTimerPhone();
                            $(sid('area-phone-loading')).hide();
                            $(sid('phone-erro-text')).text('Tempo esgotado. O código não foi gerado.');
                            $(sid('area-phone-erro')).show();
                        }
                    });
            }, INTERVAL_PHONE);
        }

        function gerarCodigoPareamento() {
            var phone = $(sid('input-phone')).val().trim().replace(/\D/g, '');
            if (phone.length < 10) {
                Swal.fire('Atenção', 'Informe o número com DDD e DDI (ex: 5511999999999).', 'warning');
                return;
            }
            pararTimers();
            pararTimerPhone();
            $(sid('area-phone-form')).hide();
            $(sid('area-phone-code')).hide();
            $(sid('area-phone-erro')).hide();
            $(sid('area-phone-loading')).show();

            $.post(addSess('{{ route('wppconnect.start-session-phone') }}'), {
                _token: '{{ csrf_token() }}',
                phone:  phone,
            }).done(function () {
                aguardarPhoneCode();
            }).fail(function (xhr) {
                $(sid('area-phone-loading')).hide();
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Falha ao iniciar sessão.';
                $(sid('phone-erro-text')).text(msg);
                $(sid('area-phone-erro')).show();
            });
        }

        $(document).on('click', '.btn-tentar-novamente[data-session="' + session + '"]', iniciarSessao);

        $(document).on('click', '.btn-modo-qrcode[data-session="' + session + '"]', function () {
            if ($(this).hasClass('active')) return;
            $(this).addClass('active').removeClass('btn-outline-warning').addClass('btn-warning');
            $('.btn-modo-phone[data-session="' + session + '"]').removeClass('active').addClass('btn-outline-warning').removeClass('btn-warning');
            pararTimerPhone();
            $(sid('modo-phone')).hide();
            $(sid('modo-qrcode')).show();
            tentativas = 0; tentativasQr = 0;
            mostrarLoading('Encerrando sessão anterior...');
            $.post(addSess('{{ route('wppconnect.logout-session') }}'), { _token: '{{ csrf_token() }}' })
                .always(function () {
                    mostrarLoading('Aguardando encerramento...');
                    setTimeout(iniciarSessao, 2000);
                });
        });

        $(document).on('click', '.btn-modo-phone[data-session="' + session + '"]', function () {
            if ($(this).hasClass('active')) return;
            $(this).addClass('active').removeClass('btn-outline-warning').addClass('btn-warning');
            $('.btn-modo-qrcode[data-session="' + session + '"]').removeClass('active').addClass('btn-outline-warning').removeClass('btn-warning');
            pararTimers();
            $(sid('modo-qrcode')).hide();
            $(sid('modo-phone')).show();
            $(sid('area-phone-form')).show();
            $(sid('area-phone-loading')).hide();
            $(sid('area-phone-code')).hide();
            $(sid('area-phone-erro')).hide();
        });

        $(document).on('click', '.btn-gerar-codigo[data-session="' + session + '"]', gerarCodigoPareamento);

        $(document).on('click', '.btn-novo-codigo[data-session="' + session + '"], .btn-phone-tentar-novamente[data-session="' + session + '"]', function () {
            pararTimers();
            $(sid('area-phone-code')).hide();
            $(sid('area-phone-erro')).hide();
            $(sid('area-phone-form')).show();
        });

        $(document).on('click', '.btn-logout-session[data-session="' + session + '"]', function () {
            Swal.fire({
                title: 'Desconectar sessão?',
                text: 'A sessão "' + session + '" será encerrada.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Desconectar',
                cancelButtonText: 'Cancelar',
            }).then(function (r) {
                if (!r.isConfirmed) return;
                $.post(addSess('{{ route('wppconnect.logout-session') }}'), { _token: '{{ csrf_token() }}' })
                    .done(function () {
                        $(sid('card-connected')).hide().addClass('d-none');
                        $(sid('card-qrcode')).show();
                        Swal.fire({ icon: 'success', title: 'Sessão desconectada.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
                    })
                    .fail(function () { Swal.fire('Erro', 'Falha ao desconectar.', 'error'); });
            });
        });

        // Verificação inicial de status
        $.get(addSess('{{ route('wppconnect.status') }}')).done(function (res) {
            if (res.connected) mostrarConectado(res.data);
        });
    }

    // Evita registrar os handlers duas vezes caso a sessão seja recriada
    var widgetsIniciados = {};

    function iniciarWidget(session) {
        if (widgetsIniciados[session]) return;
        widgetsIniciados[session] = true;
        createSessionWidget(session);
    }

    @foreach($sessions as $sess)
    iniciarWidget('{{ $sess['name'] }}');
    @endforeach

    // ============================================================
    // ADICIONAR / EXCLUIR CONEXÃO
    // ============================================================

    // Espelha o markup Blade do card de conexão para sessões criadas em runtime
    function cardSessaoHtml(setor, name, label) {
        return '' +
        '<div class="col-md-6 col-lg-4" id="col-sessao-' + name + '">' +
            '<div class="card card-outline card-success mb-3 d-none" id="card-connected-' + name + '">' +
                '<div class="card-header text-center">' +
                    '<h4 class="card-title mb-0"><i class="fas fa-check-circle text-success mr-2"></i>WhatsApp Conectado</h4>' +
                '</div>' +
                '<div class="card-body text-center py-4">' +
                    '<i class="fab fa-whatsapp text-success" style="font-size:64px;"></i>' +
                    '<p class="mt-3 mb-1 font-weight-bold" style="font-size:16px;">' + label + '</p>' +
                    '<p class="mb-0 text-muted" id="status-label-' + name + '">Sessão ativa</p>' +
                    '<button class="btn btn-sm btn-outline-danger mt-3 btn-logout-session" data-session="' + name + '">' +
                        '<i class="fas fa-sign-out-alt mr-1"></i>Desconectar</button>' +
                '</div>' +
            '</div>' +
            '<div class="card card-outline card-warning mb-3" id="card-qrcode-' + name + '">' +
                '<div class="card-header text-center">' +
                    '<h4 class="card-title mb-0"><i class="fab fa-whatsapp mr-2"></i>' + label +
                        '<button class="btn btn-xs btn-outline-danger float-right btn-excluir-conexao" ' +
                                'data-setor="' + setor + '" data-label="' + label + '" title="Excluir conexão">' +
                            '<i class="fas fa-trash"></i></button>' +
                    '</h4>' +
                    '<div class="btn-group btn-group-sm mt-2 mb-1" role="group">' +
                        '<button type="button" class="btn btn-outline-warning btn-modo-qrcode" data-session="' + name + '">' +
                            '<i class="fas fa-qrcode mr-1"></i>QR Code</button>' +
                        '<button type="button" class="btn btn-warning active btn-modo-phone" data-session="' + name + '">' +
                            '<i class="fas fa-mobile-alt mr-1"></i>Número</button>' +
                    '</div>' +
                '</div>' +
                '<div class="card-body text-center">' +
                    '<div id="modo-qrcode-' + name + '" style="display:none;">' +
                        '<div id="area-loading-' + name + '" class="py-4">' +
                            '<div class="spinner-border text-warning" role="status"></div>' +
                            '<p class="mt-2 text-muted" id="loading-text-' + name + '">Verificando conexão...</p>' +
                        '</div>' +
                        '<div id="area-qrcode-' + name + '" style="display:none;">' +
                            '<p class="text-muted mb-2" style="font-size:13px;">Abra o WhatsApp &rarr; <strong>Dispositivos conectados</strong> &rarr; <strong>Conectar dispositivo</strong></p>' +
                            '<img id="qrcode-img-' + name + '" src="" alt="QR Code" class="img-fluid rounded border" style="max-width:220px; margin:0 auto; display:block;">' +
                            '<p class="text-muted mt-2" style="font-size:12px;"><i class="fas fa-sync-alt mr-1"></i>QR Code atualiza automaticamente</p>' +
                        '</div>' +
                        '<div id="area-erro-' + name + '" style="display:none;" class="py-3">' +
                            '<i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>' +
                            '<p class="mt-2 text-danger" id="erro-text-' + name + '">Erro ao conectar.</p>' +
                            '<button class="btn btn-warning btn-sm btn-tentar-novamente" data-session="' + name + '">' +
                                '<i class="fas fa-redo mr-1"></i>Tentar novamente</button>' +
                        '</div>' +
                    '</div>' +
                    '<div id="modo-phone-' + name + '">' +
                        '<div id="area-phone-form-' + name + '" class="py-3">' +
                            '<p class="text-muted mb-3" style="font-size:13px;">Abra o WhatsApp &rarr; <strong>Aparelhos vinculados</strong> &rarr; <strong>Vincular com número de telefone</strong></p>' +
                            '<div class="input-group mb-3" style="max-width:260px; margin:0 auto;">' +
                                '<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-mobile-alt"></i></span></div>' +
                                '<input type="text" id="input-phone-' + name + '" class="form-control" placeholder="55119XXXXXXXX" maxlength="15">' +
                            '</div>' +
                            '<button class="btn btn-warning btn-gerar-codigo" data-session="' + name + '">' +
                                '<i class="fas fa-key mr-1"></i>Gerar Código</button>' +
                        '</div>' +
                        '<div id="area-phone-loading-' + name + '" style="display:none;" class="py-4">' +
                            '<div class="spinner-border text-warning" role="status"></div>' +
                            '<p class="mt-2 text-muted">Gerando código de pareamento...</p>' +
                        '</div>' +
                        '<div id="area-phone-code-' + name + '" style="display:none;" class="py-3">' +
                            '<p class="text-muted mb-2" style="font-size:13px;">Digite este código no WhatsApp:</p>' +
                            '<div class="display-4 font-weight-bold text-warning letter-spacing-lg" id="pairing-code-' + name + '">----</div>' +
                            '<p class="text-muted mt-3" style="font-size:12px;"><i class="fas fa-clock mr-1"></i>O código expira em alguns minutos</p>' +
                            '<button class="btn btn-outline-warning btn-sm mt-1 btn-novo-codigo" data-session="' + name + '">' +
                                '<i class="fas fa-redo mr-1"></i>Gerar novo código</button>' +
                        '</div>' +
                        '<div id="area-phone-erro-' + name + '" style="display:none;" class="py-3">' +
                            '<i class="fas fa-exclamation-triangle text-danger" style="font-size:40px;"></i>' +
                            '<p class="mt-2 text-danger" id="phone-erro-text-' + name + '">Erro ao gerar código.</p>' +
                            '<button class="btn btn-warning btn-sm btn-phone-tentar-novamente" data-session="' + name + '">' +
                                '<i class="fas fa-redo mr-1"></i>Tentar novamente</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function atualizarEmptyState() {
        $('#empty-conexoes').toggleClass('d-none', $('#row-conexoes [id^="col-sessao-"]').length > 0);
    }

    $('#btn-add-conexao').on('click', function () {
        var $sel = $('#select-setor');
        $sel.html('<option value="">Carregando...</option>').prop('disabled', true);
        $('#modal-add-conexao').modal('show');

        $.get('{{ route('wppconnect.sessoes.disponiveis') }}')
            .done(function (res) {
                if (!res.setores || !res.setores.length) {
                    $sel.html('<option value="">Todos os setores já possuem conexão</option>');
                    return;
                }
                var opts = ['<option value="">— selecione —</option>'];
                res.setores.forEach(function (s) {
                    opts.push('<option value="' + s.setor + '">' + s.label + '</option>');
                });
                $sel.html(opts.join('')).prop('disabled', false);
            })
            .fail(function () {
                $sel.html('<option value="">Erro ao carregar setores</option>');
            });
    });

    $('#btn-salvar-conexao').on('click', function () {
        var setor = $('#select-setor').val();
        if (!setor) {
            Swal.fire('Atenção', 'Selecione o setor da nova conexão.', 'warning');
            return;
        }

        var btn = $(this).prop('disabled', true);

        $.post('{{ route('wppconnect.sessoes.store') }}', {
            _token: '{{ csrf_token() }}',
            setor:  setor,
        }).done(function (res) {
            $('#modal-add-conexao').modal('hide');
            var s = res.sessao;
            $('#empty-conexoes').before(cardSessaoHtml(s.setor, s.name, s.label));
            atualizarEmptyState();
            iniciarWidget(s.name);
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && (xhr.responseJSON.errors || xhr.responseJSON.message)) || 'Falha ao criar conexão.';
            if (typeof msg === 'object') msg = Object.values(msg).flat().join(' ');
            Swal.fire('Erro', msg, 'error');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    $(document).on('click', '.btn-excluir-conexao', function () {
        var setor = $(this).data('setor');
        var label = $(this).data('label');

        Swal.fire({
            title: 'Excluir conexão?',
            text: 'A conexão "' + label + '" será desconectada e removida.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (!r.isConfirmed) return;
            $.post('{{ url('wppconnect/sessoes') }}/' + setor, {
                _token:  '{{ csrf_token() }}',
                _method: 'DELETE',
            }).done(function (res) {
                $('#row-conexoes').find('[data-setor="' + setor + '"]').closest('[id^="col-sessao-"]').remove();
                atualizarEmptyState();
                Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
            }).fail(function (xhr) {
                Swal.fire('Erro', (xhr.responseJSON && xhr.responseJSON.errors) || 'Falha ao excluir conexão.', 'error');
            });
        });
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
        if (href === '#pane-ia')         carregarIaIntencoes();
    });

    $('#btn-atualizar-disparos').on('click', function () {
        if (dtDisparos) dtDisparos.ajax.reload(null, false);
    });

    // ============================================================
    // MODAL PRÉVIA IA
    // ============================================================

    let previaIdAtual = null;

    function formatDateInput(date) {
        return date.toISOString().split('T')[0]; // YYYY-MM-DD
    }

    $(document).on('click', '.btn-previa-intencao', function () {
        previaIdAtual = $(this).data('id');
        const nome    = $(this).data('nome');

        $('#previa-nome-intencao').text(nome);

        // Padrão: primeiro dia do mês até hoje
        const hoje      = new Date();
        const primDia   = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
        $('#previa-dt-inicio').val(formatDateInput(primDia));
        $('#previa-dt-fim').val(formatDateInput(hoje));

        $('#previa-loading').hide();
        $('#previa-resultado').hide();
        $('#previa-erro').hide();

        $('#modal-previa-ia').modal('show');
    });

    function gerarPrevia() {
        if (!previaIdAtual) return;

        const dtInicio = $('#previa-dt-inicio').val();
        const dtFim    = $('#previa-dt-fim').val();

        if (!dtInicio || !dtFim) {
            Swal.fire('Atenção', 'Informe as duas datas.', 'warning');
            return;
        }

        $('#previa-resultado').hide();
        $('#previa-erro').hide();
        $('#previa-loading').show();
        $('#btn-gerar-previa').prop('disabled', true);

        $.get('{{ url('wppconnect/ia-intencoes') }}/' + previaIdAtual + '/previa', {
            dt_inicio: dtInicio,
            dt_fim:    dtFim,
        }).done(function (res) {
            $('#previa-texto').text(res.texto);
            $('#previa-resultado').show();
        }).fail(function (xhr) {
            const msg = xhr.responseJSON?.errors ?? 'Erro ao gerar prévia.';
            $('#previa-erro').text(msg).show();
        }).always(function () {
            $('#previa-loading').hide();
            $('#btn-gerar-previa').prop('disabled', false);
        });
    }

    $('#btn-gerar-previa').on('click', gerarPrevia);

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

    // ============================================================
    // TAB CONTEXTO IA
    // ============================================================

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function carregarIaIntencoes() {
        $('#tbody-ia-intencoes').html(
            '<tr><td colspan="5" class="text-center py-3">' +
            '<div class="spinner-border spinner-border-sm text-info"></div>' +
            '<span class="ml-2 text-muted">Carregando...</span></td></tr>'
        );

        $.get('{{ url('wppconnect/ia-intencoes') }}')
            .done(function (res) { renderIaIntencoes(res.data); })
            .fail(function () {
                $('#tbody-ia-intencoes').html(
                    '<tr><td colspan="5" class="text-center text-danger py-3">Erro ao carregar intenções.</td></tr>'
                );
            });
    }

    function renderIaIntencoes(intencoes) {
        if (!intencoes || !intencoes.length) {
            $('#tbody-ia-intencoes').html(
                '<tr><td colspan="5" class="text-center text-muted py-3">Nenhuma intenção cadastrada.</td></tr>'
            );
            return;
        }

        const rows = [];
        intencoes.forEach(function (item) {
            const badge = item.ativo
                ? '<span class="badge badge-success">Ativo</span>'
                : '<span class="badge badge-secondary">Inativo</span>';

            const btnToggle = item.ativo
                ? `<button class="btn btn-xs btn-danger btn-toggle-intencao" data-id="${item.id}">
                       <i class="fas fa-pause mr-1"></i>Desativar
                   </button>`
                : `<button class="btn btn-xs btn-success btn-toggle-intencao" data-id="${item.id}">
                       <i class="fas fa-play mr-1"></i>Ativar
                   </button>`;

            const btnSql = `<button class="btn btn-xs btn-outline-info btn-ver-sql" data-id="${item.id}">
                                <i class="fas fa-code mr-1"></i>Ver SQL
                            </button>`;

            const btnPrevia = item.sql_template
                ? `<button class="btn btn-xs btn-outline-success btn-previa-intencao"
                           data-id="${item.id}" data-nome="${escHtml(item.nome)}">
                       <i class="fas fa-eye mr-1"></i>Prévia
                   </button>`
                : `<span class="text-muted" style="font-size:11px;">Sem SQL</span>`;

            rows.push(`<tr data-id="${item.id}">
                <td><code>${escHtml(item.nome)}</code></td>
                <td style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                    title="${escHtml(item.descricao)}">${escHtml(item.descricao)}</td>
                <td class="text-center td-intencao-status">${badge}</td>
                <td class="text-center td-intencao-acao">${btnToggle}</td>
                <td class="text-center">${btnSql}</td>
                <td class="text-center">${btnPrevia}</td>
            </tr>`);

            const alertaSemSql = !item.sql_template
                ? `<div class="alert alert-warning py-1 mb-2" style="font-size:12px;">
                       <i class="fas fa-exclamation-triangle mr-1"></i>
                       SQL não configurado — o WhatsApp não retornará resposta para esta intenção.
                   </div>`
                : '';

            rows.push(`<tr class="sql-detail-row d-none" data-id="${item.id}" style="background:#f8f9fa;">
                <td colspan="6" class="p-3">
                    ${alertaSemSql}
                    <div class="mb-2">
                        <small class="text-muted">
                            Placeholders disponíveis:
                            <code>@{{dt_inicio}}</code>
                            <code>@{{dt_fim}}</code>
                            <code>@{{cd_empresa}}</code>
                        </small>
                    </div>
                    <textarea class="form-control form-control-sm textarea-sql font-monospace"
                              data-id="${item.id}"
                              rows="14"
                              style="font-family:monospace; font-size:12px; resize:vertical;"
                              spellcheck="false">${escHtml(item.sql_template || '')}</textarea>
                    <div class="mt-2 text-right">
                        <button class="btn btn-sm btn-info btn-salvar-sql" data-id="${item.id}">
                            <i class="fas fa-save mr-1"></i>Salvar SQL
                        </button>
                    </div>
                </td>
            </tr>`);
        });

        $('#tbody-ia-intencoes').html(rows.join(''));
    }

    // Expandir / recolher SQL
    $(document).on('click', '.btn-ver-sql', function () {
        const id      = $(this).data('id');
        const detail  = $(`.sql-detail-row[data-id="${id}"]`);
        const visible = !detail.hasClass('d-none');

        // Fecha todos os outros
        $('.sql-detail-row').addClass('d-none');
        $('.btn-ver-sql').html('<i class="fas fa-code mr-1"></i>Ver SQL');

        if (!visible) {
            detail.removeClass('d-none');
            $(this).html('<i class="fas fa-times mr-1"></i>Fechar');
        }
    });

    // Toggle ativo/inativo
    $(document).on('click', '.btn-toggle-intencao', function () {
        const id  = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);

        $.post('{{ url('wppconnect/ia-intencoes') }}/' + id + '/toggle', {
            _token: '{{ csrf_token() }}',
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true });

            const badge = res.ativo
                ? '<span class="badge badge-success">Ativo</span>'
                : '<span class="badge badge-secondary">Inativo</span>';
            const newBtn = res.ativo
                ? `<button class="btn btn-xs btn-danger btn-toggle-intencao" data-id="${id}"><i class="fas fa-pause mr-1"></i>Desativar</button>`
                : `<button class="btn btn-xs btn-success btn-toggle-intencao" data-id="${id}"><i class="fas fa-play mr-1"></i>Ativar</button>`;

            $(`tr[data-id="${id}"] .td-intencao-status`).html(badge);
            $(`tr[data-id="${id}"] .td-intencao-acao`).html(newBtn);
        }).fail(function (xhr) {
            Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao alterar status.', 'error');
            btn.prop('disabled', false);
        });
    });

    // Salvar SQL
    $(document).on('click', '.btn-salvar-sql', function () {
        const id  = $(this).data('id');
        const sql = $(`.textarea-sql[data-id="${id}"]`).val();
        const btn = $(this);
        btn.prop('disabled', true);

        $.post('{{ url('wppconnect/ia-intencoes') }}/' + id + '/sql', {
            _token:   '{{ csrf_token() }}',
            _method:  'PUT',
            sql_template: sql,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true });

            // Remove aviso de "sem SQL" se existia
            $(`.sql-detail-row[data-id="${id}"] .alert-warning`).remove();
        }).fail(function (xhr) {
            Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao salvar SQL.', 'error');
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
