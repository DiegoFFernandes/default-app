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
        <div id="div-saldo-ciclo" class="col-md-12" style="display:none">
            <div class="alert alert-light border mb-2 py-2 px-3">
                <small class="text-muted">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Ciclo: <span id="saldo-periodo" class="font-weight-bold text-dark"></span>
                </small>
                <div class="row mt-1">
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Orçamento</small>
                        <span class="font-weight-bold text-primary" id="saldo-orcado"></span>
                    </div>
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Utilizado</small>
                        <span class="font-weight-bold text-warning" id="saldo-utilizado"></span>
                    </div>
                    <div class="col-4 text-center">
                        <small class="d-block text-muted">Saldo</small>
                        <span class="font-weight-bold" id="saldo-valor"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="div-centrocusto" style="display:none">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Centro de Resultado</small></label>
                <select class="form-control form-control-sm select2" id="cd_centrocusto"
                    data-selected="{{ $solicitacao->CD_CENTROCUSTO ?? '' }}"
                    style="width:100%">
                    <option value="">Nenhum</option>
                </select>
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
