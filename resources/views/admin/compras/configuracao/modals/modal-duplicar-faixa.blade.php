<div class="modal fade" id="modal-duplicar-faixa" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Duplicar Informações</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dup_id_faixa_origem">
                <p class="text-muted mb-3" style="font-size:0.85rem">
                    <i class="fas fa-info-circle mr-1"></i>
                    Copiando a faixa <strong id="dup-ds-origem"></strong>. Selecione a empresa de destino.
                </p>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm" id="dup_cd_empresa" style="width:100%">
                        <option value="">Selecione</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Descrição <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="dup_ds" maxlength="100">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Ordem <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="dup_ordem" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Mínimo <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="dup_vl_min" placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Máximo <span class="text-muted">(vazio = ilimitado)</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="dup_vl_max" placeholder="0,00">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-duplicar-faixa" class="btn btn-danger btn-sm">Duplicar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
