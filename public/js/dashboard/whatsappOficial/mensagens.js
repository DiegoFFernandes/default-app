$(function () {
    var csrfToken = $('[name=csrf-token]').attr('content');
    var carregado = false;
    var polling = null;

    function iconeStatus(status) {
        switch (status) {
            case 'read':      return '<span class="wa-chat-status text-primary"><i class="fas fa-check-double"></i></span>';
            case 'delivered': return '<span class="wa-chat-status text-muted"><i class="fas fa-check-double"></i></span>';
            case 'failed':    return '<span class="wa-chat-status text-danger"><i class="fas fa-exclamation-circle"></i></span>';
            case 'sent':
            case 'accepted':  return '<span class="wa-chat-status text-muted"><i class="fas fa-check"></i></span>';
            default:          return '';
        }
    }

    function formatarHora(dataIso) {
        var d = new Date(dataIso);
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }

    function renderizarMensagens(mensagens) {
        var $lista = $('#wa-chat-lista');
        $lista.empty();

        if (!mensagens.length) {
            $lista.append('<div class="text-center text-muted py-4" id="wa-chat-vazio">Nenhuma mensagem ainda.</div>');
            return;
        }

        mensagens.forEach(function (m) {
            var $bubble = $('<div class="wa-chat-bubble"></div>')
                .addClass(m.direcao)
                .text(m.mensagem);

            var $time = $('<span class="wa-chat-time"></span>').text(formatarHora(m.created_at));

            if (m.direcao === 'enviada') {
                $time.append(iconeStatus(m.status));
            }

            $bubble.append($time);
            $lista.append($bubble);
        });

        $lista.scrollTop($lista[0].scrollHeight);
    }

    function carregarMensagens() {
        $.get(window.routesWhatsappOficial.mensagensListar)
            .done(function (mensagens) {
                renderizarMensagens(mensagens);
            });
    }

    function iniciarPolling() {
        if (polling) return;
        polling = setInterval(carregarMensagens, 5000);
    }

    function pararPolling() {
        clearInterval(polling);
        polling = null;
    }

    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var href = $(e.target).attr('href');

        if (href === '#pane-mensagens') {
            if (!carregado) {
                carregado = true;
                carregarMensagens();
            }
            iniciarPolling();
        } else {
            pararPolling();
        }
    });

    $('#form-nova-mensagem').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btn-enviar-mensagem').prop('disabled', true);

        $.post(window.routesWhatsappOficial.mensagensEnviar, {
            _token: csrfToken,
            telefone: $('#input-mensagem-telefone').val(),
            mensagem: $('#input-mensagem-texto').val(),
        })
            .done(function () {
                $('#input-mensagem-texto').val('');
                carregarMensagens();
            })
            .fail(function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao enviar.', 'error');
            })
            .always(function () { $btn.prop('disabled', false); });
    });
});
