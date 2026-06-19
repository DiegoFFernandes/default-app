<div id="print-area" style="font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a;">

    {{-- Cabeçalho --}}
    <table style="width:100%; border-collapse:collapse; border-bottom:3px solid #1a1a1a; padding-bottom:12px; margin-bottom:16px;">
        <tr>
            <td style="vertical-align:bottom; padding-bottom:10px;">
                <div style="font-size:20px; font-weight:900; text-transform:uppercase; letter-spacing:1px;">{{ $solicitacao->NM_EMPRESA }}</div>
                <div style="font-size:11px; color:#666; text-transform:uppercase; letter-spacing:2px; margin-top:3px;">Solicitação de Compra</div>
            </td>
            <td style="text-align:right; vertical-align:bottom; padding-bottom:10px;">
                <div style="font-size:26px; font-weight:bold; color:#c0392b;">Nº {{ str_pad($solicitacao->CD_SOLICITACAO, 6, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size:10px; color:#666; margin-top:2px;">Emitido em {{ now()->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Dados da solicitação --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:16px; font-size:11px;">
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold; width:20%;">Empresa</td>
            <td style="border:1px solid #ccc; padding:5px 8px; width:30%;">{{ $solicitacao->NM_EMPRESA }}</td>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold; width:20%;">Data da Solicitação</td>
            <td style="border:1px solid #ccc; padding:5px 8px; width:30%;">{{ \Carbon\Carbon::parse($solicitacao->DT_SOLICITACAO)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold;">Justificativa</td>
            <td colspan="3" style="border:1px solid #ccc; padding:5px 8px;">{{ $solicitacao->DS_JUSTIFICATIVA }}</td>
        </tr>
        @if($solicitacao->DS_OBSERVACAO)
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold;">Observação</td>
            <td colspan="3" style="border:1px solid #ccc; padding:5px 8px;">{{ $solicitacao->DS_OBSERVACAO }}</td>
        </tr>
        @endif
    </table>

    {{-- Tabela de itens --}}
    <div style="background:#2c3e50; color:#fff; font-weight:bold; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:6px 10px;">
        Itens Solicitados
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:11px; margin-bottom:20px;">
        <thead>
            <tr style="background:#ecf0f1;">
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:center; width:30px;">Nº</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:left;">Produto / Descrição</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:center; width:65px;">Qtd.</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:center; width:45px;">Un.</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:left; width:120px;">Observação</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:center; width:90px;">Valor Unit. (R$)</th>
                <th style="border:1px solid #ccc; padding:5px 8px; text-align:center; width:90px;">Valor Total (R$)</th>
            </tr>
        </thead>
        <tbody id="print-items-tbody">
            @foreach($itens as $idx => $item)
            <tr style="{{ $idx % 2 !== 0 ? 'background:#fafafa;' : '' }}">
                <td style="border:1px solid #ccc; padding:5px 8px; text-align:center;">{{ $idx + 1 }}</td>
                <td style="border:1px solid #ccc; padding:5px 8px;">{{ $item->DS_ITEM }}</td>
                <td style="border:1px solid #ccc; padding:5px 8px; text-align:center;">{{ number_format($item->QT_ITEM, 3, ',', '.') }}</td>
                <td style="border:1px solid #ccc; padding:5px 8px; text-align:center;">{{ $item->DS_UNIDADE }}</td>
                <td style="border:1px solid #ccc; padding:5px 8px;">{{ $item->DS_OBSERVACAO ?? '' }}</td>
                <td style="border:1px solid #ccc; padding:5px 8px;"></td>
                <td style="border:1px solid #ccc; padding:5px 8px;"></td>
            </tr>
            @endforeach
        </tbody>
        <tbody>
            <tr>
                <td colspan="5" style="border:1px solid #ccc; padding:5px 10px; text-align:right; font-weight:bold; background:#ecf0f1;">VALOR TOTAL DA PROPOSTA</td>
                <td style="border:1px solid #ccc; background:#ecf0f1;"></td>
                <td style="border:1px solid #ccc; background:#ecf0f1;"></td>
            </tr>
        </tbody>
    </table>

    {{-- Dados da proposta (fornecedor preenche) --}}
    <div style="background:#2c3e50; color:#fff; font-weight:bold; font-size:11px; text-transform:uppercase; letter-spacing:1px; padding:6px 10px;">
        Dados da Proposta — a ser preenchido pelo Fornecedor
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:11px; margin-bottom:24px;">
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold; width:20%;">Condição de Pagamento</td>
            <td style="border:1px solid #ccc; padding:5px 8px; width:30%;"></td>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold; width:20%;">Prazo de Entrega</td>
            <td style="border:1px solid #ccc; padding:5px 8px; width:30%;"></td>
        </tr>
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold;">Validade da Proposta</td>
            <td style="border:1px solid #ccc; padding:5px 8px;"></td>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold;">Frete</td>
            <td style="border:1px solid #ccc; padding:5px 8px;"></td>
        </tr>
        <tr>
            <td style="background:#f0f0f0; border:1px solid #ccc; padding:5px 8px; font-weight:bold;">Observações do Fornecedor</td>
            <td colspan="3" style="border:1px solid #ccc; padding:30px 8px;"></td>
        </tr>
    </table>

    {{-- Assinaturas --}}
    <table style="width:100%; border-collapse:collapse; margin-top:20px;">
        <tr>
            <td style="width:44%; text-align:center; padding:0 8px;">
                <div style="border-top:1px solid #333; padding-top:5px; font-size:11px;">Fornecedor — Assinatura e Carimbo</div>
            </td>
            <td style="width:12%;"></td>
            <td style="width:44%; text-align:center; padding:0 8px;">
                <div style="border-top:1px solid #333; padding-top:5px; font-size:11px;">Data: _____ / _____ / __________</div>
            </td>
        </tr>
    </table>

    {{-- Rodapé --}}
    <div style="margin-top:20px; border-top:1px solid #ddd; padding-top:6px; font-size:9px; color:#aaa; text-align:center;">
        Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }} · Solicitação Nº {{ $solicitacao->CD_SOLICITACAO }} · {{ $solicitacao->NM_EMPRESA }}
    </div>

</div>
