{{-- Parâmetros: $painelId, $titulo --}}
<div class="modal resumo-ia-modal" id="{{ $painelId }}" tabindex="-1" role="dialog"
     data-backdrop="false" data-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header resumo-ia-drag-handle">
                <h6 class="modal-title mb-0">
                    <i class="fas fa-robot mr-2 text-info"></i>{{ $titulo }}
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height:60vh; overflow-y:auto;">
                <div class="resumo-ia-painel-loading text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                    Gerando resumo...
                </div>
                <div class="resumo-ia-painel-conteudo d-none" style="line-height:1.75; font-size:.9rem;"></div>
            </div>
            <div class="modal-footer resumo-ia-painel-footer d-none flex-wrap justify-content-between" style="gap:8px;">
                <div class="input-group input-group-sm" style="max-width:280px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-success text-white border-success">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control painel-wpp-telefone"
                           placeholder="DDD + número (ex: 11999999999)" maxlength="11">
                    <div class="input-group-append">
                        <button class="btn btn-success painel-wpp-btn" type="button"
                                onclick="enviarPainelIAWpp('{{ $painelId }}')">
                            <i class="fas fa-paper-plane mr-1"></i>Enviar
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@once
<style>
.resumo-ia-modal               { overflow: visible !important; }
.resumo-ia-modal .modal-dialog { margin: 1.75rem auto; }
.resumo-ia-modal .modal-header {
    cursor: move;
    background: #343a40;
    color: #fff;
    user-select: none;
    border-radius: calc(.3rem - 1px) calc(.3rem - 1px) 0 0;
}
.resumo-ia-modal .modal-header .modal-title { font-size: .9rem; font-weight: 500; }
.resumo-ia-modal .modal-header .close       { color: rgba(255,255,255,.7); text-shadow: none; }
.resumo-ia-modal .modal-header .close:hover { color: #fff; }
</style>

<script>
/* ── Arrastar modal pelo header (vanilla JS — sem jQuery) ───────────────── */
document.addEventListener('mousedown', function (e) {
    var handle = e.target.closest('.resumo-ia-drag-handle');
    if (!handle || e.target.closest('.close')) return;

    var dialog = handle.closest('.modal-dialog');
    var rect   = dialog.getBoundingClientRect();
    var startX = e.clientX;
    var startY = e.clientY;
    var origL  = rect.left;
    var origT  = rect.top;

    dialog.style.position = 'absolute';
    dialog.style.margin   = '0';
    dialog.style.left     = origL + 'px';
    dialog.style.top      = origT + 'px';

    function onMove(ev) {
        dialog.style.left = (origL + ev.clientX - startX) + 'px';
        dialog.style.top  = (origT + ev.clientY - startY) + 'px';
    }
    function onUp() {
        document.removeEventListener('mousemove', onMove);
        document.removeEventListener('mouseup',   onUp);
    }
    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup',   onUp);
});

/* ── Cache frontend ─────────────────────────────────────────────────────── */
var _painelIACache = {};

function _iaHash(intent, dados) {
    var s = intent + '|' + dados.length;
    if (dados.length > 0) s += '|' + JSON.stringify(dados[0]).slice(0, 120);
    if (dados.length > 1) s += '|' + JSON.stringify(dados[dados.length - 1]).slice(0, 120);
    var h = 0;
    for (var i = 0; i < s.length; i++) { h = Math.imul(31, h) + s.charCodeAt(i) | 0; }
    return h.toString(36);
}

/* ── Funções genéricas do painel ────────────────────────────────────────── */
function abrirPainelIA(painelId, dados, intent) {
    var $modal    = $('#' + painelId);
    var $dialog   = $modal.find('.modal-dialog');
    var cacheKey  = painelId + '|' + _iaHash(intent, dados);
    var cached    = _painelIACache[cacheKey];

    // Reseta posição para voltar ao centro ao reabrir
    $dialog.css({ position: '', margin: '', left: '', top: '' });
    $modal.find('.resumo-ia-painel-footer').addClass('d-none');
    $modal.find('.painel-wpp-telefone').val('');

    if (cached) {
        $modal.find('.resumo-ia-painel-loading').addClass('d-none');
        $modal.find('.resumo-ia-painel-conteudo').removeClass('d-none').html(cached);
        $modal.find('.resumo-ia-painel-footer').removeClass('d-none');
        $modal.modal('show');
        return;
    }

    $modal.find('.resumo-ia-painel-loading').removeClass('d-none');
    $modal.find('.resumo-ia-painel-conteudo').addClass('d-none').html('');
    $modal.modal('show');

    fetch('{{ route('ia-resumo') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ dados: dados, intent: intent })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        var html = (data.resumo || 'Sem resumo disponível.').replace(/\n/g, '<br>');
        _painelIACache[cacheKey] = html;
        $modal.find('.resumo-ia-painel-loading').addClass('d-none');
        $modal.find('.resumo-ia-painel-conteudo').removeClass('d-none').html(html);
        $modal.find('.resumo-ia-painel-footer').removeClass('d-none');
    })
    .catch(function () {
        $modal.find('.resumo-ia-painel-loading').addClass('d-none');
        $modal.find('.resumo-ia-painel-conteudo').removeClass('d-none')
            .html('<span class="text-danger">Erro ao gerar o resumo. Tente novamente.</span>');
    });
}

function fecharPainelIA(painelId) {
    $('#' + painelId).modal('hide');
}

function enviarPainelIAWpp(painelId) {
    var $modal   = $('#' + painelId);
    var telefone = $modal.find('.painel-wpp-telefone').val().replace(/\D/g, '');

    if (telefone.length < 10 || telefone.length > 11) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe um número válido com DDD (10 ou 11 dígitos).' });
        return;
    }

    var mensagem = $modal.find('.resumo-ia-painel-conteudo').html()
        .replace(/<br\s*\/?>/gi, '\n')
        .replace(/<[^>]+>/g, '')
        .trim();

    if (!mensagem) return;

    var btn = $modal.find('.painel-wpp-btn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Enviando...');

    fetch('{{ route('ia-resumo-whatsapp') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ telefone: telefone, mensagem: mensagem })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Enviado!', text: 'Resumo enviado via WhatsApp.' });
        } else {
            Swal.fire({ icon: 'error', title: 'Erro', text: data.message || 'Falha ao enviar.' });
        }
    })
    .catch(function () {
        Swal.fire({ icon: 'error', title: 'Erro', text: 'Falha na comunicação com o servidor.' });
    })
    .finally(function () {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i>Enviar');
    });
}
</script>
@endonce
