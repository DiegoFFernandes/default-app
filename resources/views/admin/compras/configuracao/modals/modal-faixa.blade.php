<div class="modal fade" id="modal-faixa">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-faixa-title">Nova Faixa</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faixa_id">
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm select2" id="faixa_cd_empresa" style="width:100%">
                        <option value="">Selecione</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1"><small>Descrição <span class="text-danger">*</span></small></label>
                    <input type="text" class="form-control form-control-sm" id="faixa_ds" maxlength="100"
                        placeholder="Ex: Faixa 1 — até R$ 1.000,00">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Ordem <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="faixa_ordem" min="1" placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Mínimo <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="faixa_vl_min" placeholder="0,00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small>Valor Máximo <span class="text-muted">(vazio = ilimitado)</span></small></label>
                            <input type="text" class="form-control form-control-sm money-mask" id="faixa_vl_max" placeholder="0,00">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2" id="div-st-ativo" style="display:none">
                    <label class="mb-1"><small>Status</small></label>
                    <select class="form-control form-control-sm" id="faixa_st_ativo">
                        <option value="S">Ativo</option>
                        <option value="N">Inativo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-salvar-faixa" class="btn btn-danger btn-sm">Salvar</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
