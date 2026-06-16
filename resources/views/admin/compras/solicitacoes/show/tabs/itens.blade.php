<div class="tab-pane fade" id="pane-itens" role="tabpanel">
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
</div>
