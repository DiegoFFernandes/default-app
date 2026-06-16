<div class="tab-pane fade show active" id="pane-cabecalho" role="tabpanel">
    <div class="row mt-1">
        <div class="col-md-4">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Empresa <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2" id="cd_empresa" style="width:100%">
                    <option value="">Selecione</option>
                    @foreach($empresas as $e)
                        <option value="{{ $e->CD_EMPRESA }}"
                            {{ isset($solicitacao) && $solicitacao->CD_EMPRESA == $e->CD_EMPRESA ? 'selected' : '' }}>
                            {{ $e->NM_EMPRESA }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Data <span class="text-danger">*</span></small></label>
                <input type="date" class="form-control form-control-sm" id="dt_solicitacao"
                    value="{{ isset($solicitacao) ? $solicitacao->DT_SOLICITACAO : date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Justificativa <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_justificativa" maxlength="500"
                    value="{{ $solicitacao->DS_JUSTIFICATIVA ?? '' }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Observações</small></label>
                <textarea class="form-control form-control-sm" id="ds_observacao" rows="2" maxlength="500">{{ $solicitacao->DS_OBSERVACAO ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3">
            @if(!$idSolicitacao)
                <button id="btn-salvar" class="btn btn-danger btn-sm">
                    <i class="fas fa-save"></i> Salvar Rascunho
                </button>
            @else
                <button id="btn-atualizar" class="btn btn-warning btn-sm">
                    <i class="fas fa-save"></i> Atualizar Cabeçalho
                </button>
            @endif
        </div>
    </div>
</div>
