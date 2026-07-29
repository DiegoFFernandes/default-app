{{-- Rodapé da NFS-e (compartilhado por todos os layouts): retenções, valor total,
     serviço, impostos e outras informações. Aparece UMA vez, ao fim da última
     página. Usa $nota. --}}

{{-- RETENÇÕES --}}
<div class="linha-solida mt-2"></div>
<table class="tb retencoes">
    <tr>
        <td style="width: 12%">Retenção ISS:</td>
        <td style="width: 21%">{{ $nota->retencaoIss }}</td>
        <td style="width: 12%">Retenção PIS:</td>
        <td style="width: 22%">{{ $nota->retencaoPis }}</td>
        <td style="width: 15%">Retenção COFINS:</td>
        <td style="width: 18%">{{ $nota->retencaoCofins }}</td>
    </tr>
    <tr>
        <td>Retenção IR:</td>
        <td>{{ $nota->retencaoIr }}</td>
        <td>Retenção CSLL:</td>
        <td>{{ $nota->retencaoCsll }}</td>
        <td>Retenção INSS:</td>
        <td>{{ $nota->retencaoInss }}</td>
    </tr>
</table>
<div class="linha-solida"></div>

{{-- VALOR TOTAL DA NOTA --}}
<div class="text-center valor-total"><strong>VALOR TOTAL DA NOTA: R$ {{ $nota->valorTotal }}</strong></div>
<div class="linha-solida"></div>

{{-- CÓDIGO E DESCRIÇÃO DO SERVIÇO --}}
<div class="mt-2">Código e Descrição do Serviço:</div>
<div class="mb-2"><strong>{{ $nota->codigoServico }} - {{ $nota->descricaoServico }}</strong></div>

{{-- DEDUÇÕES / BASE DE CÁLCULO / ALÍQUOTA / ISS --}}
<table class="tb quadro impostos">
    <tr>
        <td class="br" style="width: 15%">
            <div>Deduções (R$)</div>
            <div class="valor-imposto">{{ $nota->deducoes }}</div>
        </td>
        <td class="br" style="width: 24%">
            <div>Base de Cálculo ISS (R$)</div>
            <div class="valor-imposto">{{ $nota->baseCalculoIss }}</div>
        </td>
        <td class="br" style="width: 19%">
            <div>Alíquota (%)</div>
            <div class="valor-imposto">{{ $nota->aliquota }}</div>
        </td>
        <td class="br" style="width: 21%">
            <div>Valor do ISS Retido (R$)</div>
            <div class="valor-imposto">{{ $nota->valorIssRetido }}</div>
        </td>
        <td style="width: 21%">
            <div>Valor do ISS (R$)</div>
            <div class="valor-imposto">{{ $nota->valorIss }}</div>
        </td>
    </tr>
</table>

{{-- OUTRAS INFORMAÇÕES --}}
<div class="faixa mt-2">OUTRAS INFORMAÇÕES</div>
<div class="quadro outras-info">
    <p class="m-0"><strong>- Valor Líquido: R$ &nbsp;&nbsp;&nbsp;{{ $nota->valorLiquido }}</strong></p>
    <p class="m-0">- Vencimentos: {{ $nota->vencimentos }}</p>
    <p class="m-0">- {{ $nota->decreto }}</p>
    <p class="m-0"><strong>- RETENÇÕES: *ISS: R$ {{ $nota->retencaoIss }} &nbsp;&nbsp;&nbsp;&nbsp; / &nbsp;
            *IR: R${{ $nota->retencaoIr }}</strong></p>

    <p class="m-0 mt-2">{{ $nota->dsObsNota }}</p>

    @if ($nota->urlConsultaPublica)
        <p class="m-0 mt-2">A autenticidade desta NFS-e pode ser verificada pelo site:</p>
        <p class="m-0 fs-9"><u>{{ $nota->urlConsultaPublica }}</u></p>
    @endif
</div>
