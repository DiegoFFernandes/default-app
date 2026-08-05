<div class="modal fade" id="modal-item-adicional" tabindex="-1" role="dialog" aria-labelledby="modal-item-adicional-label"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-item-adicional-label">Itens Adicionais</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="small" for="select-grupos-adicionais">Selecione os Grupos</label>
                            <select id="select-grupos-adicionais" name="grupos-adicionais[]"
                                class="form-control form-control-sm select2" style="width: 100%" multiple>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="linhas-grupos-adicionais"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal"
                    style="width: 100px">Fechar</button>
                <button type="button" class="btn btn-danger btn-xs" id="btn-add-modal"
                    style="width: 100px">Adicionar</button>
            </div>
        </div>
    </div>
</div>
