<div class="tab-pane fade" id="pane-fornecedor" role="tabpanel">
    <div class="row mt-1">
        <div class="col-md-5">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Cotação Vencedora <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2" id="sel_id_cotacao" style="width:100%">
                    <option value="">Selecione</option>
                    @foreach($cotacoes ?? [] as $c)
                        <option value="{{ $c->ID_COTACAO }}" {{ $c->ST_SELECIONADA === 'S' ? 'selected' : '' }}>
                            {{ $c->NM_FORNECEDOR }} — R$ {{ number_format($c->VL_TOTAL, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Motivo da Escolha <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_motivo_escolha" maxlength="500"
                    value="{{ collect($cotacoes ?? [])->firstWhere('ST_SELECIONADA', 'S')->DS_MOTIVO_ESCOLHA ?? '' }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3">
            <button id="btn-selecionar-forn" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-check"></i> Confirmar Vencedor
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 pt-3 text-center">
            <button id="btn-submeter" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane"></i> Enviar para Aprovação
            </button>
        </div>
    </div>
</div>
