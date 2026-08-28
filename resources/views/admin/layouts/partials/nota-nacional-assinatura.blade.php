{{-- ASSINATURA (faixa com bordas, igual ao rodapé do oficial). Renderiza no
     fluxo normal, logo após o rodapé (ver comentário em layout-nota-nacional
     sobre por que não tentamos mais ancorá-la no rodapé físico da folha).
     Usa $nota. --}}
<table class="tb grade linha-compacta assinatura">
    <tr>
        <td style="width: 25%">
            <span class="rotulo">DATA CIENTIFICAÇÃO:</span>
        </td>
        <td style="width: 25%">
            <span class="rotulo">IDENTIFICAÇÃO E ASSINATURA</span>
        </td>
        <td style="width: 50%">
            <span class="rotulo">N&deg; NFS-e / CHAVE NFS-e</span>
            <span class="valor">{{ $nota->numero }} / {{ $nota->codigoVerificacao }}</span>
        </td>
    </tr>
</table>
