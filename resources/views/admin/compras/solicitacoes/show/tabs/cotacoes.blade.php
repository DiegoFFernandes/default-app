<div class="tab-pane fade" id="pane-cotacoes" role="tabpanel">
    <table class="table table-striped table-bordered table-font-small mt-2" style="width:100%">
        <thead>
            <tr>
                <th>Fornecedor</th>
                <th class="text-center">Prazo</th>
                <th>Condição Pgto.</th>
                <th class="text-center">Forma Pgto.</th>
                <th class="text-right">Valor Total</th>
                <th>Observação</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $pagtoMap = ['BL' => 'Boleto', 'DI' => 'Dinheiro', 'CH' => 'Cheque', 'PX' => 'Pix', 'CC' => 'Cartão de Crédito'];
            @endphp
            @forelse($cotacoes as $cot)
            <tr class="{{ $cot->ST_SELECIONADA === 'S' ? 'table-success font-weight-bold' : '' }}">
                <td>{{ $cot->NM_FORNECEDOR }}</td>
                <td class="text-center">{{ $cot->NR_PRAZO_ENTREGA }} dias</td>
                <td>{{ $cot->DS_CONDICAO_PAGAMENTO }}</td>
                <td class="text-center">{{ $pagtoMap[$cot->CD_FORMAPAGTO ?? ''] ?? '-' }}</td>
                <td class="text-right">R$ {{ number_format($cot->VL_TOTAL, 2, ',', '.') }}</td>
                <td>{{ $cot->DS_OBSERVACAO ?? '-' }}</td>
                <td class="text-center">
                    @if($cot->ST_SELECIONADA === 'S')
                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Selecionado</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Nenhuma cotação cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
