<div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
    @if($solicitacao->ST_SOLICITACAO === 'RAS')
    {{-- ===== Edição (rascunho) ===== --}}
    <div class="row mt-1 mb-1">
        <div class="col-md-4">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Fornecedor <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2-ajax" id="cd_fornecedor" style="width:100%"
                    data-url="{{ route('compras.search-fornecedor') }}"
                    data-placeholder="Buscar fornecedor (mín. 3 caracteres)"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Prazo (dias) <span class="text-danger">*</span></small></label>
                <input type="number" class="form-control form-control-sm" id="nr_prazo" min="1" placeholder="0">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Condição de Pagamento <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_condicao" maxlength="200" placeholder="Ex: 30 dias">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Valor Total (R$) <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm money-mask" id="vl_total_cot" placeholder="0,00">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Obs.</small></label>
                <input type="text" class="form-control form-control-sm" id="ds_obs_cot" maxlength="500">
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">
            <button id="btn-add-cot" class="btn btn-info btn-sm">
                <i class="fas fa-plus"></i> Adicionar Cotação
            </button>
        </div>
    </div>
    <table class="table table-striped table-bordered compact table-font-small" id="table-cotacoes" style="width:100%">
        <thead>
            <tr>
                <th>Fornecedor</th>
                <th>Prazo</th>
                <th>Condição</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
    @else
    {{-- ===== Somente leitura ===== --}}
    <table class="table table-striped table-bordered table-font-small mt-2" style="width:100%">
        <thead>
            <tr>
                <th>Fornecedor</th>
                <th class="text-center">Prazo</th>
                <th>Condição Pgto.</th>
                <th class="text-right">Valor Total</th>
                <th>Observação</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotacoes as $cot)
            <tr class="{{ $cot->ST_SELECIONADA === 'S' ? 'table-success font-weight-bold' : '' }}">
                <td>{{ $cot->NM_FORNECEDOR }}</td>
                <td class="text-center">{{ $cot->NR_PRAZO_ENTREGA }} dias</td>
                <td>{{ $cot->DS_CONDICAO_PAGAMENTO }}</td>
                <td class="text-right">R$ {{ number_format($cot->VL_TOTAL, 2, ',', '.') }}</td>
                <td>{{ $cot->DS_OBSERVACAO ?? '-' }}</td>
                <td class="text-center">
                    @if($cot->ST_SELECIONADA === 'S')
                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Selecionado</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted">Nenhuma cotação cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif
</div>
