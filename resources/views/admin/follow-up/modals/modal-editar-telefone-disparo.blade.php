<div class="modal fade" id="modal-editar-telefone-disparo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Telefone do Destinatário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="telefone_disparo_cd_envio">
                <div class="form-group">
                    <label for="telefone_disparo_ds_telefone">Telefone (WhatsApp)</label>
                    <input type="text" class="form-control form-control-sm" id="telefone_disparo_ds_telefone"
                        placeholder="DDD + número, só números">
                    <small class="form-text text-muted">Ex.: 44999998888 (DDD + número, sem espaços ou
                        traços).</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-success" id="btn-salvar-telefone-disparo">Salvar</button>
            </div>
        </div>
    </div>
</div>
