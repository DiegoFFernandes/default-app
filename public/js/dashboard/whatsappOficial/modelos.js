$(function () {
    var $form = $('#form-novo-template');
    var exemploValores = {}; // { '1': 'Diego Ferreira', '2': '123456', ... }
    var csrfToken = $('[name=csrf-token]').attr('content');

    // Extrai os números das variáveis do corpo (padrão de duas chaves), em ordem, sem repetir.
    function detectarVariaveis(texto) {
        var nums = [], regex = /\{\{(\d+)\}\}/g, m;
        while ((m = regex.exec(texto || '')) !== null) {
            var n = parseInt(m[1], 10);
            if (nums.indexOf(n) === -1) nums.push(n);
        }
        nums.sort(function (a, b) { return a - b; });
        return nums;
    }

    // Recria os campos de amostra abaixo do corpo, um por variável detectada -
    // igual o Gerenciador de Modelos da Meta. Preserva o que já foi digitado
    // (guardado em exemploValores por número), mesmo recriando os inputs.
    function renderizarCamposExemplos() {
        var vars = detectarVariaveis($('#input-corpo').val());
        var $lista = $('#exemplos-lista').empty();

        $('#exemplos-wrapper').toggle(vars.length > 0);

        vars.forEach(function (n) {
            var $row = $('<div class="form-group row align-items-center mb-2"></div>');
            var $label = $('<label class="col-2 col-form-label text-right font-weight-bold"></label>').text('{{' + n + '}}');
            var $input = $('<input type="text" class="form-control form-control-sm exemplo-input">')
                .attr('data-var', n)
                .attr('placeholder', 'Exemplo para {{' + n + '}}')
                .val(exemploValores[n] || '');
            var $col = $('<div class="col-10"></div>').append($input);

            $row.append($label).append($col);
            $lista.append($row);
        });

        atualizarExemplosHidden();
    }

    function atualizarExemplosHidden() {
        var vars = detectarVariaveis($('#input-corpo').val());
        var valores = vars.map(function (n) { return exemploValores[n] || ''; });
        $('#input-exemplos-hidden').val(valores.join(', '));
    }

    $(document).on('input', '.exemplo-input', function () {
        exemploValores[$(this).data('var')] = $(this).val();
        atualizarExemplosHidden();
        atualizarPreview();
    });

    $('#input-corpo').on('input', renderizarCamposExemplos);

    function atualizarPreview() {
        var headerTipo  = $('#select-header-tipo').val();
        var headerTexto = $('#input-header-texto').val();
        var rodape      = $('#input-rodape').val();

        var corpo = $('#input-corpo').val().replace(/\{\{(\d+)\}\}/g, function (match, n) {
            return exemploValores[n] || match;
        });

        var $header = $('#preview-header').removeClass('doc').text('');
        if (headerTipo === 'TEXT' && headerTexto) {
            $header.text(headerTexto);
        } else if (headerTipo === 'DOCUMENT') {
            $header.addClass('doc').html('<i class="fas fa-file-pdf mr-1"></i>documento.pdf');
        }

        $('#preview-body').text(corpo || 'A mensagem aparece aqui...');
        $('#preview-footer').text(rodape);
    }

    $('#select-header-tipo').on('change', function () {
        $('#grupo-header-texto').toggle($(this).val() === 'TEXT');
        $('#grupo-header-arquivo').toggle($(this).val() === 'DOCUMENT');
        atualizarPreview();
    });

    $form.on('input change', 'input, textarea, select', atualizarPreview);
    atualizarPreview();

    function resetarFormulario() {
        $form[0].reset();
        $('#input-template-id').val('');
        $('#input-nome').prop('readonly', false);
        $('#grupo-header-texto').hide();
        $('#grupo-header-arquivo').hide();
        $('#texto-arquivo-atual').text('');
        $('#titulo-form-template').text('Novo template (rascunho)');
        $('#btn-cancelar-edicao').addClass('d-none');
        exemploValores = {};
        renderizarCamposExemplos();
        atualizarPreview();
    }

    $('#btn-cancelar-edicao').on('click', resetarFormulario);

    $('#tabela-templates').on('click', '.btn-editar', function () {
        var d = $(this).data();

        $('#input-template-id').val(d.id);
        $('#input-nome').val(d.nome).prop('readonly', true);
        $('#input-categoria').val(d.categoria);
        $('#input-idioma').val(d.idioma);
        $('#select-header-tipo').val(d.headerTipo || '').trigger('change');
        $('#input-header-texto').val(d.headerTexto);
        $('#texto-arquivo-atual').text(d.temArquivo ? 'Já existe um PDF enviado — escolha um arquivo aqui só se quiser substituir.' : '');
        $('#input-corpo').val(d.corpo);
        $('#input-rodape').val(d.rodape);

        exemploValores = {};
        var partes = (d.exemplos || '').split(',').map(function (v) { return v.trim(); });
        detectarVariaveis(d.corpo).forEach(function (n, i) {
            exemploValores[n] = partes[i] || '';
        });
        renderizarCamposExemplos();

        $('#titulo-form-template').text('Editando: ' + d.nome);
        $('#btn-cancelar-edicao').removeClass('d-none');
        atualizarPreview();

        $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 300);
    });

    $form.on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btn-salvar-template').prop('disabled', true);
        var templateId = $('#input-template-id').val();

        var url = templateId ? window.routesWhatsappOficial.templateBase + '/' + templateId : window.routesWhatsappOficial.templateStore;
        var formData = new FormData($form[0]);
        if (templateId) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        })
            .done(function () {
                location.reload();
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && (xhr.responseJSON.errors || xhr.responseJSON.message)) || 'Falha ao salvar.';
                if (typeof msg === 'object') msg = Object.values(msg).flat().join(' ');
                Swal.fire('Erro', msg, 'error');
            })
            .always(function () { $btn.prop('disabled', false); });
    });

    $('#tabela-templates').on('click', '.btn-enviar', function () {
        var id = $(this).data('id');
        var $btn = $(this).prop('disabled', true);

        Swal.fire({
            title: 'Enviar para análise?',
            text: 'O template será submetido para aprovação da Meta.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
        }).then(function (result) {
            if (!result.isConfirmed) { $btn.prop('disabled', false); return; }

            Swal.fire({
                title: 'Enviando para a Meta...',
                text: 'Isso pode levar alguns segundos (upload do PDF de amostra, quando houver).',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function () { Swal.showLoading(); },
            });

            $.post(window.routesWhatsappOficial.templateBase + '/' + id + '/enviar', { _token: csrfToken })
                .done(function (res) {
                    Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                    location.reload();
                })
                .fail(function (xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao enviar.', 'error');
                    $btn.prop('disabled', false);
                });
        });
    });

    $('#tabela-templates').on('click', '.btn-excluir', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Excluir template?',
            text: 'Se já estiver enviado/aprovado, também será removido da Meta.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Excluir',
            cancelButtonText: 'Cancelar',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: window.routesWhatsappOficial.templateBase + '/' + id,
                type: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
            }).done(function (res) {
                Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                location.reload();
            }).fail(function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao excluir.', 'error');
            });
        });
    });

    $('#btn-sincronizar').on('click', function () {
        var $btn = $(this).prop('disabled', true);

        $.post(window.routesWhatsappOficial.templateSincronizar, { _token: csrfToken })
            .done(function (res) {
                Swal.fire({ icon: 'success', title: res.success, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                location.reload();
            })
            .fail(function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.errors ?? 'Falha ao sincronizar.', 'error');
            })
            .always(function () { $btn.prop('disabled', false); });
    });
});
