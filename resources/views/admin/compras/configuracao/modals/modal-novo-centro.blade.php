<div class="modal fade" id="modal-novo-centro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    Novo Centro de Resultado
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group mb-3">
                    <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm select2" id="nc_cd_empresa" style="width:100%">
                        <option value="">Selecione</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->CD_EMPRESA }}">{{ $e->NM_EMPRESA }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="mb-1"><small>Tipo <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm select2" id="nc_cd_centrocusto"
                        style="width:100%" disabled>
                        <option value="">— selecione a empresa primeiro —</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="mb-1"><small>Responsável</small></label>
                    <select class="form-control form-control-sm select2" id="nc_cd_usuario" style="width:100%">
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
                                <input type="text" class="form-control money-mask" id="nc_vl_orcado" placeholder="0,00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-1"><small>Dia Início do Ciclo</small></label>
                            <input type="number" class="form-control form-control-sm" id="nc_dia_inicio"
                                min="1" max="31" placeholder="ex: 1">
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm" id="btn-salvar-novo-centro">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
            </div>

        </div>
    </div>
</div>
