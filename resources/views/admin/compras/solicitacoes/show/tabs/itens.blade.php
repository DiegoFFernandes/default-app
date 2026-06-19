<div class="tab-pane fade" id="pane-itens" role="tabpanel">
    @if($solicitacao->ST_SOLICITACAO === 'RAS')
    {{-- ===== Edição (rascunho) ===== --}}
    <div class="row mt-1 mb-1">
        <div class="col-md-5">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Produto <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm select2-ajax" id="cd_item" style="width:100%"
                    data-url="{{ route('compras.search-item') }}"
                    data-placeholder="Buscar produto (mín. 3 caracteres)"></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Quantidade <span class="text-danger">*</span></small></label>
                <input type="number" class="form-control form-control-sm" id="qt_item" min="0.001" step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Unidade <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="ds_unidade" maxlength="10" placeholder="UN">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2">
                <label class="mb-1"><small>Observação</small></label>
                <input type="text" class="form-control form-control-sm" id="ds_obs_item" maxlength="300">
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12">
            <button id="btn-add-item" class="btn btn-danger btn-sm">
                <i class="fas fa-plus"></i> Adicionar Item
            </button>
        </div>
    </div>
    <table class="table table-striped table-bordered compact table-font-small" id="table-itens" style="width:100%">
        <thead>
            <tr>
                <th>Cód.</th>
                <th>Produto</th>
                <th>Qtd</th>
                <th>Un.</th>
                <th>Observação</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <button id="btn-enviar-solicitacao-analise-compra" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane mr-1"></i> Enviar para Análise de Compra
            </button>
        </div>
    </div>
    @else
    {{-- ===== Somente leitura ===== --}}
    <table class="table table-striped table-bordered table-font-small mt-2" style="width:100%">
        <thead>
            <tr>
                <th>Cód.</th>
                <th>Produto</th>
                <th class="text-center">Qtd</th>
                <th class="text-center">Un.</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itens as $item)
            <tr>
                <td>{{ $item->CD_ITEM }}</td>
                <td>{{ $item->DS_ITEM }}</td>
                <td class="text-center">{{ number_format($item->QT_ITEM, 3, ',', '.') }}</td>
                <td class="text-center">{{ $item->DS_UNIDADE }}</td>
                <td>{{ $item->DS_OBSERVACAO ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">Nenhum item cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif
</div>
