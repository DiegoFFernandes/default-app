{{-- Rodapé do DANFSe v2.0 (fiel ao PDF oficial): tributação municipal/federal/
     IBS-CBS, valor total e informações complementares. Aparece UMA vez, ao fim
     da última página. Mesmo padrão do cabeçalho: sem borda nas células, título
     da seção como caixinha cinza na 1ª coluna da própria grade. Usa $nota. --}}

{{-- TRIBUTAÇÃO MUNICIPAL (ISSQN) --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        {{-- Larguras alinhadas com o cabeçalho (mesmo cálculo do PRESTADOR/
             FORNECEDOR): 26.95 / 23.1 / 26.95 / 23. --}}
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">TRIBUTAÇÃO MUNICIPAL (ISSQN)</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Tipo de Tributação do ISSQN</span>
            <span class="valor">{{ $nota->tipoTributacaoIssqn }}</span>
        </td>
        <td colspan="2" style="width: 49.95%">
            <span class="rotulo">Município / Sigla UF / País de Incidência do ISSQN</span>
            <span class="valor">{{ $nota->municipioIncidenciaIssqn }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">BC ISSQN</span>
            <span class="valor">{{ $nota->baseCalculoIss }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Alíquota Aplicada</span>
            <span class="valor">{{ $nota->aliquotaIss }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Retenção do ISSQN</span>
            <span class="valor">{{ $nota->retencaoIssqnStatus }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">ISSQN Apurado</span>
            <span class="valor">{{ $nota->issqnApurado }}</span>
        </td>
    </tr>
</table>

{{-- TRIBUTAÇÃO FEDERAL (EXCETO CBS) --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">TRIBUTAÇÃO FEDERAL (EXCETO CBS)</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">IRRF</span>
            <span class="valor">{{ $nota->irrf }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Contribuição Previdenciária - Retida</span>
            <span class="valor">{{ $nota->contribPrevidenciariaRetida }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Contribuições Sociais - Retidas</span>
            <span class="valor">{{ $nota->contribSociaisRetidas }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">PIS - Débito Apuração Própria</span>
            <span class="valor">{{ $nota->pisDebitoApuracaoPropria }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">COFINS - Débito Apuração Própria</span>
            <span class="valor">{{ $nota->cofinsDebitoApuracaoPropria }}</span>
        </td>
        <td colspan="2" style="width: 49.95%">
            <span class="rotulo">Descrição Contrib. Sociais - Retidas</span>
            <span class="valor">{{ $nota->descricaoContribSociaisRetidas }}</span>
        </td>
    </tr>
</table>

{{-- TRIBUTAÇÃO IBS/CBS --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">TRIBUTAÇÃO IBS/CBS</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">CST / cClassTrib</span>
            <span class="valor">{{ $nota->cstClassTrib }}</span>
        </td>
        <td colspan="2" style="width: 49.95%">
            <span class="rotulo">Indicador de Operação / Código IBGE Incidência / Município Incidência / Sigla UF</span>
            <span class="valor">{{ $nota->indicadorOperacaoIncidencia }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">Exclusões e Reduções da Base de Cálculo</span>
            <span class="valor">{{ $nota->exclusoesReducoesBase }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Base de Cálculo Após Exclusões e Reduções</span>
            <span class="valor">{{ $nota->baseAposExclusoes }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Red. Alíquota IBS / Red. Alíquota CBS</span>
            <span class="valor">{{ $nota->redAliquotaIbsCbs }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Alíquota - IBS UF / IBS Mun</span>
            <span class="valor">{{ $nota->aliquotaIbsUf }} / {{ $nota->aliquotaIbsMun }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">Alíq. Efetiva Municipal - IBS</span>
            <span class="valor">{{ $nota->aliqEfetivaMunicipalIbs }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Valor Apurado Municipal - IBS</span>
            <span class="valor">{{ $nota->valorApuradoMunicipalIbs }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Alíq. Efetiva Estadual - IBS</span>
            <span class="valor">{{ $nota->aliqEfetivaEstadualIbs }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Valor Apurado Estadual - IBS</span>
            <span class="valor">{{ $nota->valorApuradoEstadualIbs }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">Valor Total Apurado - IBS</span>
            <span class="valor">{{ $nota->valorTotalApuradoIbs }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Alíquota - CBS</span>
            <span class="valor">{{ $nota->aliquotaCbs }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Alíquota Efetiva - CBS</span>
            <span class="valor">{{ $nota->aliquotaEfetivaCbs }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Valor Total Apurado - CBS</span>
            <span class="valor">{{ $nota->valorTotalApuradoCbs }}</span>
        </td>
    </tr>
</table>

{{-- VALOR TOTAL DA NFS-e --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">VALOR TOTAL DA NFS-e</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">VALOR DA OPERAÇÃO / SERVIÇO</span>
            <span class="valor">{{ $nota->valorOperacaoServico }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Desconto Incondicionado</span>
            <span class="valor">{{ $nota->descontoIncondicionado }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Desconto Condicionado</span>
            <span class="valor">{{ $nota->descontoCondicionado }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 26.95%">
            <span class="rotulo">Total das Retenções (ISSQN / Federais)</span>
            <span class="valor">{{ $nota->totalRetencoes }}</span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">VALOR LÍQUIDO DA NFS-e</span>
            <span class="valor">{{ $nota->valorLiquido }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Total do IBS/CBS</span>
            <span class="valor">{{ $nota->totalIbsCbs }}</span>
        </td>
        {{-- Célula destacada em cinza, como no oficial --}}
        <td class="celula-destaque" style="width: 23%">
            <span class="rotulo">VALOR LÍQUIDO DA NFS-e + IBS/CBS</span>
            <span class="valor">{{ $nota->valorLiquidoMaisIbsCbs }}</span>
        </td>
    </tr>
</table>

{{-- INFORMAÇÕES COMPLEMENTARES (título em linha própria, texto livre sem caixa) --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        <td>
            <span class="rotulo">INFORMAÇÕES COMPLEMENTARES</span>
        </td>
    </tr>
</table>
<div class="info-complementar">{{ $nota->informacoesComplementares }}</div>
{{-- A assinatura saiu daqui - agora é admin.layouts.partials.nota-nacional-assinatura,
     incluída pelo layout-nota-nacional numa linha própria da tabela da página
     (ver comentário lá: técnica de distribuição de altura de linha de tabela,
     mais confiável que CSS de posicionamento no --print-to-pdf real). --}}
