{{-- Handler de salvar do modal-compra-item. Incluir dentro do @section('js') da página.
     Ao salvar com sucesso, dispara o evento DOM "compra-item:saved" com { id, text, un, isNew }. --}}
<script>
(function () {
    const ciToken    = $('[name=csrf-token]').attr('content');
    const ciStoreUrl = "{{ route('compras.itens-proprios.store') }}";
    const ciUpdBase  = "{{ url('compras/itens-proprios') }}";

    // Select2 do subgrupo (lista completa carregada de uma vez, com filtro local)
    $('#ci_subgrupo').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Selecione o subgrupo',
        allowClear: true,
        dropdownParent: $('#modal-compra-item'),
    });

    // Preseleciona um subgrupo (usado na edição) — opções já estão no select
    window.setSubgrupoCompraItem = function (cd) {
        $('#ci_subgrupo').val(cd ? String(cd) : '').trigger('change');
    };

    $('#btn-salvar-compra-item').on('click', function () {
        const cd     = $('#ci_cd').val();
        const dsItem = $('#ci_ds_item').val().trim();

        if (!dsItem) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe a descrição do item.', confirmButtonColor: '#dc3545' });
            return;
        }

        if (!$('#ci_sg_unidmed').val()) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione a unidade.', confirmButtonColor: '#dc3545' });
            return;
        }

        const isNew = !cd;
        const url   = isNew ? ciStoreUrl : (ciUpdBase + '/' + cd + '/update');

        $.post(url, {
            _token:             ciToken,
            ds_item:            dsItem,
            sg_unidmed:         $('#ci_sg_unidmed').val(),
            cd_subgrupo_compra: $('#ci_subgrupo').val() || null,
            st_ativo:           $('#ci_st_ativo').val() || 'S',
        }, function (res) {
            if (res.errors) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: res.errors, confirmButtonColor: '#dc3545' });
                return;
            }
            $('#modal-compra-item').modal('hide');
            Swal.fire({
                icon: 'success', title: 'Salvo!', text: res.success, toast: true,
                position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true,
            });
            document.dispatchEvent(new CustomEvent('compra-item:saved', {
                detail: { id: res.id, text: res.text, un: res.un, isNew: isNew }
            }));
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs, confirmButtonColor: '#dc3545' });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível salvar o item.', confirmButtonColor: '#dc3545' });
            }
        });
    });

    // Reset ao abrir para "novo"
    window.abrirModalNovoCompraItem = function () {
        $('#modal-compra-item-title').text('Novo Item');
        $('#ci_cd').val('');
        $('#ci_ds_item, #ci_sg_unidmed').val('');
        $('#ci_st_ativo').val('S');
        $('#ci_subgrupo').val(null).trigger('change');
        $('#modal-compra-item').modal('show');
    };
})();
</script>
