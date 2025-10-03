<div class="modal fade" id="modal-item-adicional" tabindex="-1" role="dialog" aria-labelledby="modal-item-adicional-label"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-item-adicional-label">Adicional</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-vulc-carga">Grupo</label>
                            <select id="select-vulc-carga" name="select-vulc-carga" class="form-control form-control-sm"
                                style="width: 100%">
                                <option value="105">Vulcanização Carga</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="select-vulc-carga">Valor</label>
                            <input type="number" id="input-vulc-carga-valor" name="input-vulc-carga-valor"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-vulc-agricola">Grupo</label>
                            <select id="select-vulc-agricola" name="select-vulc-agricola"
                                class="form-control form-control-sm" style="width: 100%">
                                <option value="305">Vulcanização Agricola e OTR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-vulc-agricola-valor">Valor</label>
                            <input type="number" id="input-vulc-agricola-valor" name="input-vulc-agricola-valor"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-manchao">Grupo</label>
                            <select id="select-manchao" name="select-manchao" class="form-control form-control-sm"
                                style="width: 100%">
                                <option value="105">Manchão</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-manchao-valor">Valor</label>
                            <input type="number" id="input-manchao-valor" name="input-manchao-valor"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-manchao-agricola">Grupo</label>
                            <select id="select-manchao-agricola" name="select-manchao-agricola"
                                class="form-control form-control-sm" style="width: 100%">
                                <option value="105">Manchão Agrícola e OTR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-manchao-valor-agricola">Valor</label>
                            <input type="number" id="input-manchao-valor-agricola" name="input-manchao-valor-agricola"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-enchimento">Grupo</label>
                            <select id="select-enchimento" name="select-enchimento" class="form-control form-control-sm"
                                style="width: 100%">
                                <option value="105">Enchimento</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-enchimento">Valor</label>
                            <input type="number" id="input-enchimento-valor" name="input-enchimento"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-enchimento-ombro-1">Grupo</label>
                            <select id="select-enchimento-ombro-1" name="select-enchimento-ombro-1" class="form-control form-control-sm"
                                style="width: 100%">
                                <option value="105">Enchimento Ombro 1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-enchimento-ombro-1">Valor</label>
                            <input type="number" id="input-enchimento-ombro-1-valor" name="input-enchimento-ombro-1-valor"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                    <div class="col-md-8 col-8">
                        <div class="form-group">
                            <label for="select-enchimento-ombro-2">Grupo</label>
                            <select id="select-enchimento-ombro-2" name="select-enchimento-ombro-2" class="form-control form-control-sm"
                                style="width: 100%">
                                <option value="105">Enchimento Ombro 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="form-group">
                            <label for="input-enchimento-ombro-2">Valor</label>
                            <input type="number" id="input-enchimento-ombro-2-valor" name="input-enchimento-ombro-2-valor"
                                class="form-control form-control-sm" style="width: 100%" placeholder="0,00">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"
                    style="width: 100px">Fechar</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-add-modal"
                    style="width: 100px">Adicionar</button>
            </div>
        </div>
    </div>
</div>
