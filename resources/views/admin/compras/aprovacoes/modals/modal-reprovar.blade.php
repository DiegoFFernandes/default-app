<div class="modal fade" id="modal-reprovar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Reprovar Solicitação</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reprovar_id_etapa">
                <div class="form-group">
                    <label>Motivo da Reprovação <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reprovar_motivo" rows="4" maxlength="500"
                        placeholder="Descreva o motivo da reprovação..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-confirmar-reprovar" class="btn btn-danger btn-sm">Confirmar Reprovação</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
