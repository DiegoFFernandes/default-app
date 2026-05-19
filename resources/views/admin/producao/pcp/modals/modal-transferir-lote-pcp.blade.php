<div class="modal fade" id="modal-transferir-lote-pcp" tabindex="-1" role="dialog"
    aria-labelledby="modal-transferir-lote-pcp-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <form id="form-transferir-lote-pcp">
                <div class="modal-header">
                    <h6 class="modal-title modal-title-lote">Transferencia de Lote</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="empresa-lote-pcp-transf">Empresa</label>
                                <input type="text" class="form-control form-control-sm" id="empresa-lote-pcp-transf"
                                    name="empresa" readonly>
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small" for="lote-pcp-novo-transf">Lote Novo</label>
                                <select class="form-control form-control-sm" id="lote-pcp-novo-transf"
                                    name="lote_pcp_novo" required>
                                    <option value="">Selecione um Lote Novo</option>
                                </select>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary btn-xs"
                        id="btn-update-transferir-lote-pcp">
                        <i class="fas fa-exchange-alt"></i>
                        Transferir</button>
                </div>
            </form>
        </div>
    </div>
</div>
