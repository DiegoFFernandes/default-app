<div class="modal fade" id="modal-parametros-cogs" tabindex="-1" role="dialog"
     aria-labelledby="modal-parametros-cogs-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-parametros-cogs-label">
                    <i class="fas fa-cogs mr-2 text-secondary"></i> Parâmetros — Inadimplência
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="param-cogs-loading" class="text-center py-4">
                    <i class="fas fa-sync-alt fa-spin mr-1 text-muted"></i>
                    <span class="text-muted small">Carregando...</span>
                </div>

                <div id="param-cogs-content" style="display:none;">
                    <p class="text-muted small mb-3 border-left border-info pl-2">
                        Formas de pagamento consideradas no cálculo de inadimplência (Tab 1).                        
                    </p>
                    <div class="row" id="formapagto-checks"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-salvar-param-cogs">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
            </div>

        </div>
    </div>
</div>

@push('js')
<script>
$(function () {
    const OPCOES_FORMAPAGTO = @json(
        collect($formasPagamento ?? [])
            ->map(fn($fp) => ['code' => $fp->CD_FORMAPAGTO, 'label' => $fp->DS_FORMAPAGTO])
            ->values()
    );

    $('#modal-parametros-cogs').on('show.bs.modal', function () {
        $('#param-cogs-loading').show();
        $('#param-cogs-content').hide();

        $.get('{{ route("parametros-cogs") }}', function (data) {
            const $checks = $('#formapagto-checks').empty();

            OPCOES_FORMAPAGTO.forEach(function (opt) {
                const checked = data.formapagto.includes(opt.code) ? 'checked' : '';
                $checks.append(`
                    <div class="col-6 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input fp-check"
                                   id="fp-${opt.code}" value="${opt.code}" ${checked}>
                            <label class="custom-control-label" for="fp-${opt.code}">
                                <code class="text-secondary"
                                      style="font-size:0.78rem; background:#f4f4f4; padding:1px 4px; border-radius:3px;">
                                    ${opt.code}
                                </code>
                                &nbsp;${opt.label}
                            </label>
                        </div>
                    </div>
                `);
            });

            $('#param-cogs-loading').hide();
            $('#param-cogs-content').show();
        }).fail(function () {
            $('#param-cogs-loading').html(
                '<span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>Erro ao carregar parâmetros.</span>'
            );
        });
    });

    $('#btn-salvar-param-cogs').on('click', function () {
        const selecionados = $('.fp-check:checked').map(function () {
            return $(this).val();
        }).get();

        if (selecionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione pelo menos uma forma de pagamento.',
                confirmButtonText: 'OK',
            });
            return;
        }

        const $btn = $(this).prop('disabled', true)
                            .html('<i class="fas fa-sync-alt fa-spin mr-1"></i> Salvando...');

        $.ajax({
            url:    '{{ route("parametros-cogs.update") }}',
            method: 'POST',
            data:   { formapagto: selecionados, _token: '{{ csrf_token() }}' },
            success: function (res) {
                $('#modal-parametros-cogs').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Salvo!',
                    html: res.message + '<br><small class="text-muted">Atualize a tela para aplicar as novas configurações.</small>',
                    confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Atualizar agora',
                    showCancelButton: true,
                    cancelButtonText: 'Fechar',
                    cancelButtonColor: '#6c757d',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao salvar. Tente novamente.',
                    confirmButtonText: 'OK',
                });
            },
            complete: function () {
                $btn.prop('disabled', false)
                    .html('<i class="fas fa-save mr-1"></i> Salvar');
            },
        });
    });
});
</script>
@endpush
