<div class="modal fade" id="{{ $idModal }}" tabindex="-1" role="dialog" aria-labelledby="{{ $idModal }}"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-nm-tabela">Vincular Pessoa/Tabela</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group form-group-sm">
                            <label>Cod.</label>
                            <input type="text" class="form-control" id="{{ $idTabelaPreco }}" style="width: 100%"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <label>Tabela Preço</label>
                            <input type="text" class="form-control" id="{{ $dsTabelaPreco }}" style="width: 100%"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-sm">
                            <label>Cliente(s)</label>
                            <select name='pessoa' class="form-control" id="{{ $idPessoa }}" multiple="multiple"
                                style="width: 100%">                               
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-sm" id="{{ $idBtnSalvarVinculo }}">Vincular</button>
            </div>
        </div>
    </div>
</div>
