<div class="modal fade" id="modal-centrocusto" tabindex="-1" role="dialog" aria-labelledby="modal-centrocusto-title" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="modal-centrocusto-title">
                    <i class="fas fa-chart-bar mr-1"></i> Centro de Resultado
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="cc_cd">
                <input type="hidden" id="cc_cd_empresa">

                <div class="form-group mb-3">
                    <label class="mb-1"><small class="text-muted">Centro de Resultado</small></label>
                    <p class="font-weight-bold mb-0" id="cc_ds_label"></p>
                </div>

                <div class="form-group mb-3">
                    <label class="mb-1"><small>Responsável</small></label>
                    <select class="form-control form-control-sm select2" id="cc_cd_usuario" style="width:100%">
                        <option value="">— sem responsável —</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group mb-0">
                            <label class="mb-1"><small>Orçamento Mensal</small></label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input type="text" class="form-control money-mask" id="cc_vl_orcado" placeholder="0,00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-1"><small>Dia Início do Ciclo</small></label>
                            <input type="number" class="form-control form-control-sm" id="cc_dia_inicio"
                                min="1" max="31" placeholder="ex: 1">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-salvar-centro">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
            </div>

        </div>
    </div>
</div>
