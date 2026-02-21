<div class="modal fade" id="modal-criar-pedido" tabindex="-1" role="dialog" aria-labelledby="modal-criar-pedido-label"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- LOADER -->
            <div id="modal-loader" class="loading-card invisible loader-overlay">
                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">Criar Pedido</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="nm_pessoa" class="form-label small">Empresa</label>
                            <select name='cd_empresa' class="form-control form-control-sm" id="cd_empresa"
                                style="width: 100%">
                                <option value="1" selected="selected">Cambé</option>
                                <option value="3">Osvaldo Cruz</option>
                                <option value="5">Ponta Grossa</option>
                                <option value="6">Cantanduva</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="form-group">
                            <label for="nm_pessoa" class="form-label small">Cliente</label>
                            <select name='pessoa' class="form-control form-control-sm" id="pessoa"
                                style="width: 100%">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="cd_cond_pagto" class="form-label small">Cond. Pagto</label>
                            <select name='cond_pagto' class="form-control form-control-sm" id="cd_cond_pagto"
                                style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="cd_form_pagto" class="form-label small">Forma Pagto</label>
                            <select name='form_pagto' class="form-control form-control-sm" id="cd_form_pagto"
                                style="width: 100%">
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <strong class="p-1 small">Itens do Pedido</strong>

                    <div class="col-12 col-md-12" id="itens-pedido">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-xs" id="btn-confirmar-pedido">Confirmar
                    Pedido</button>
            </div>
        </div>
    </div>
</div>
