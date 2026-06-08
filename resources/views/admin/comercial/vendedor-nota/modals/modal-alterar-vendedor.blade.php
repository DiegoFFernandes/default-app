<div class="modal fade" id="modal-alterar-vendedor" tabindex="-1" role="dialog" aria-labelledby="modal-alterar-vendedor-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Alterar Vendedor Nota</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="small">Empresa</label>
                            <input type="text" class="form-control form-control-sm" id="cd_empresa" style="width: 100%"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="small">Nr. Lancamento</label>
                            <input type="text" class="form-control form-control-sm" id="nr_lancamento" style="width: 100%"
                                readonly>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label class="small">Nr. Nota</label>
                            <input type="text" class="form-control form-control-sm" id="nr_nota" style="width: 100%"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label class="small">Cliente</label>
                            <input type="text" class="form-control form-control-sm" id="nm_pessoa" style="width: 100%"
                                readonly>
                        </div>
                    </div>  
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label class="small">Vendedor Atual</label>
                            <input type="text" class="form-control form-control-sm" id="vendedor_atual" style="width: 100%"
                                readonly>
                        </div>
                    </div>  
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label class="small">Vendedor Novo</label>
                            <select name="vendedor_novo" id="cd_vendedor_novo" class="form-control form-control-sm">                               
                            </select>
                        </div>
                    </div>                   
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-update-vendedor-nota">Alterar</button>
            </div>
        </div>
    </div>
</div>
