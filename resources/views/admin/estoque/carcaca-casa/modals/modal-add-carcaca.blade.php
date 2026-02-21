<div class="modal fade" id="modal-add-carcaca" tabindex="-1" role="dialog" aria-labelledby="modal-add-carcaca-label"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Adicionar Carcaça</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="number" class="d-none" id="id_carcaca" />
                <div class="form-group">
                    <label for="cd_medida">Medida Carcaca</label>
                    <select class="form-control form-control-sm" id="cd_medida" style="width: 100%">
                    </select>
                </div>
                <div class="form-group">
                    <label for="cd_modelo">Modelo/Marca</label>
                    <select class="form-control form-control-sm" name="cd_modelo" id="cd_modelo" style="width: 100%">
                    </select>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nr_fogo">Número Fogo</label>
                            <input type="number" class="form-control form-control-sm" name="nr_fogo" id="nr_fogo" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nr_serie">Número Série</label>
                            <input type="text" class="form-control form-control-sm" name="nr_serie" id="nr_serie" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nr_dot">Número Dot</label>
                            <input type="text" class="form-control form-control-sm" name="nr_dot" id="nr_dot" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="vl_carcaca">Valor</label>
                            <input type="text" class="form-control form-control-sm" name="vl_carcaca"
                                id="vl_carcaca" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="cd_tipo">Tipo Carcaça</label>
                            <select class="form-control form-control-sm" name="cd_tipo" id="cd_tipo"
                                style="width: 100%">
                                <option value="1" selected="selected">Primeira</option>
                                <option value="2">Segunda</option>
                                <option value="3">Terceira</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="cd_tipo">Local Estoque</label>
                            <select class="form-control form-control-sm" name="cd_local" id="cd_local"
                                style="width: 100%">
                                <option value="1" selected="selected">Cambé</option>
                                <option value="3">Osvaldo Cruz</option>
                                <option value="5">Ponto Grossa</option>
                                <option value="6">Catanduva</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-xs d-none" id="btn-save-carcaca">Salvar</button>
                <button type="button" class="btn btn-warning btn-xs d-none" id="btn-edit-carcaca">Editar</button>
            </div>
        </div>
    </div>
</div>
