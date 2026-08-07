{{-- Handlers do modal-fornecedor. Incluir dentro do @section('js') da página.
     Ao salvar com sucesso, dispara o evento DOM "fornecedor:saved" com { id, text }. --}}
<script>
(function () {
    const fpToken = $('[name=csrf-token]').attr('content');

    // Máscaras — CNPJ (14) ou CPF (11), alterna pelo tamanho digitado
    $('#fp_cnpj').inputmask({ mask: ['999.999.999-99', '99.999.999/9999-99'], keepStatic: true });
    $('#fp_cep').inputmask({ mask: '99999-999' });

    // Select2 do município (busca AJAX por nome)
    $('#fp_municipio').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Buscar município (mín. 2 caracteres)',
        allowClear: true,
        minimumInputLength: 2,
        dropdownParent: $('#modal-fornecedor'),
        ajax: {
            url: "{{ route('compras.fornecedor.search-municipio') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(i => ({ id: i.id ?? i.ID, text: i.text ?? i.TEXT }))
            }),
            cache: true,
        },
    });

    function setMunicipio(cd, texto) {
        const $m = $('#fp_municipio');
        $m.val(null).trigger('change');
        if (cd && texto) {
            $m.append(new Option(texto, cd, true, true)).trigger('change');
        }
    }

    function limparCamposFornecedor(manterCnpj) {
        if (!manterCnpj) $('#fp_cnpj').val('');
        $('#fp_nm_pessoa, #fp_ds_endereco, #fp_nr_endereco, #fp_ds_bairro, #fp_cep, #fp_nr_fone, #fp_nr_celular').val('');
        setMunicipio(null, null);
    }

    window.abrirModalNovoFornecedor = function () {
        limparCamposFornecedor(false);
        $('#fp_tipo').val('2');
        $('#modal-fornecedor').modal('show');
        setTimeout(() => $('#fp_cnpj').focus(), 300);
    };

    // Consulta o CNPJ (checa Firebird e, se novo, busca na BrasilAPI)
    $('#btn-buscar-cnpj').on('click', function () {
        const cnpj = $('#fp_cnpj').val();
        const len  = $('#fp_cnpj').inputmask('unmaskedvalue').length;
        if (len !== 11 && len !== 14) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe um CNPJ ou CPF válido.', confirmButtonColor: '#dc3545' });
            return;
        }

        Swal.fire({ title: 'Consultando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

        $.getJSON('{{ route('compras.fornecedor.consultar-cnpj') }}', { cnpj: cnpj }, function (res) {
            Swal.close();
            if (res.errors) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: res.errors, confirmButtonColor: '#dc3545' });
                return;
            }
            if (res.exists) {
                Swal.fire({ icon: 'info', title: 'Já cadastrado', text: res.message, confirmButtonColor: '#dc3545' });
                return;
            }
            if (res.found === false) {
                limparCamposFornecedor(true);
                Swal.fire({ icon: 'info',
                    title: res.cpf ? 'CPF' : 'CNPJ não encontrado',
                    text: res.cpf
                        ? 'Para CPF o preenchimento é manual.'
                        : 'Não foi possível obter os dados automaticamente. Preencha manualmente.',
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                return;
            }
            const d = res.data || {};
            $('#fp_nm_pessoa').val(d.nm_pessoa || '');
            $('#fp_ds_endereco').val(d.ds_endereco || '');
            $('#fp_nr_endereco').val(d.nr_endereco || '');
            $('#fp_ds_bairro').val(d.ds_bairro || '');
            $('#fp_cep').val(d.nr_cep || '');
            $('#fp_nr_fone').val(d.nr_fone || '');
            $('#fp_nr_celular').val(d.nr_celular || '');
            setMunicipio(d.cd_municipio, d.ds_municipio);

            if (!d.cd_municipio && d.municipio) {
                Swal.fire({ icon: 'warning', title: 'Município não encontrado',
                    html: 'A empresa é de <b>' + d.municipio + ' - ' + d.uf + '</b>, mas esse município não foi localizado no cadastro. Selecione manualmente.',
                    confirmButtonColor: '#dc3545' });
            }
        }).fail(function () {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Erro', text: 'Falha ao consultar o CNPJ. Tente novamente.', confirmButtonColor: '#dc3545' });
        });
    });

    // Salvar fornecedor
    $('#btn-salvar-fornecedor').on('click', function () {
        const docLen = $('#fp_cnpj').inputmask('unmaskedvalue').length;
        const cnpjOk = docLen === 11 || docLen === 14;
        const nm     = $('#fp_nm_pessoa').val().trim();

        if (!cnpjOk) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe um CNPJ ou CPF válido.', confirmButtonColor: '#dc3545' });
            return;
        }
        if (!nm) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe a razão social / nome.', confirmButtonColor: '#dc3545' });
            return;
        }

        Swal.fire({ title: 'Salvando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

        $.post('{{ route('compras.fornecedor.store') }}', {
            _token:        fpToken,
            nr_cnpjcpf:    $('#fp_cnpj').val(),
            cd_tipopessoa: $('#fp_tipo').val(),
            nm_pessoa:     nm,
            ds_endereco:   $('#fp_ds_endereco').val(),
            nr_endereco:   $('#fp_nr_endereco').val(),
            cd_municipio:  $('#fp_municipio').val() || null,
            nr_cep:        $('#fp_cep').val(),
            ds_bairro:     $('#fp_ds_bairro').val(),
            nr_fone:       $('#fp_nr_fone').val(),
            nr_celular:    $('#fp_nr_celular').val(),
        }, function (res) {
            if (res.errors) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: res.errors, confirmButtonColor: '#dc3545' });
                return;
            }
            $('#modal-fornecedor').modal('hide');
            Swal.fire({ icon: 'success', title: 'Salvo!', text: res.success, toast: true,
                position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
            document.dispatchEvent(new CustomEvent('fornecedor:saved', { detail: { id: res.id, text: res.text } }));
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs, confirmButtonColor: '#dc3545' });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível salvar o fornecedor.', confirmButtonColor: '#dc3545' });
            }
        });
    });
})();
</script>
