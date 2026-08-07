{{-- Handler de salvar do modal-subgrupo. Incluir dentro do @section('js') da página.
     Ao salvar com sucesso, dispara o evento DOM "compra-subgrupo:saved" com { id, text, isNew }. --}}
<script>
(function () {
    const sgToken    = $('[name=csrf-token]').attr('content');
    const sgStoreUrl = "{{ route('compras.subgrupos.store') }}";
    const sgUpdBase  = "{{ url('compras/subgrupos') }}";

    $('#btn-salvar-subgrupo').on('click', function () {
        const cd = $('#sg_cd').val();
        const ds = $('#sg_ds_subgrupo').val().trim();

        if (!ds) {
            Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Informe a descrição do subgrupo.', confirmButtonColor: '#dc3545' });
            return;
        }

        const isNew = !cd;
        const url   = isNew ? sgStoreUrl : (sgUpdBase + '/' + cd + '/update');

        $.post(url, { _token: sgToken, ds_subgrupo: ds }, function (res) {
            if (res.errors) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: res.errors, confirmButtonColor: '#dc3545' });
                return;
            }
            $('#modal-subgrupo').modal('hide');
            Swal.fire({
                icon: 'success', title: 'Salvo!', text: res.success, toast: true,
                position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true,
            });
            document.dispatchEvent(new CustomEvent('compra-subgrupo:saved', {
                detail: { id: res.id, text: res.text, isNew: isNew }
            }));
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                Swal.fire({ icon: 'warning', title: 'Atenção', html: msgs, confirmButtonColor: '#dc3545' });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível salvar o subgrupo.', confirmButtonColor: '#dc3545' });
            }
        });
    });

    // Reset ao abrir para "novo"
    window.abrirModalNovoSubgrupo = function () {
        $('#modal-subgrupo-title').text('Novo Subgrupo');
        $('#sg_cd').val('');
        $('#sg_ds_subgrupo').val('');
        $('#modal-subgrupo').modal('show');
    };
})();
</script>
