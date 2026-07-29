{{-- Cabeçalho da NFS-e (layout padrão / Cambé, empresas 1, 3, 6). Repetido no topo
     de CADA página pela paginação manual do layout-nota-atz. Diferença para o
     emp-3: traz o selo NFS-e e o quadro Nº/Data/barcode numa única linha.
     Usa $tomador, $nota, $municipio, $prestador do escopo pai. --}}

{{-- CANHOTO / RECIBO (parte destacável) --}}
<table class="tb canhoto">
    <tr>
        <td class="p-0">
            <table class="tb">
                <tr>
                    <td style="width: 65%"><strong>Prezado:</strong> {{ $tomador->nomeRazaoSocial }}</td>
                    <td style="width: 35%">Cód. <strong>{{ $tomador->codigo }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2">RECEBEMOS OS ITENS CONSTANTES NO DOCUMENTO INDICADO ABAIXO:</td>
                </tr>
            </table>
            <table class="tb bt">
                <tr>
                    <td class="br" style="width: 28%; height: 42px;"><strong>Data de Recebimento</strong></td>
                    <td><strong>Identificação e Assinatura do Recebedor</strong></td>
                </tr>
            </table>
        </td>
        <td class="bl" style="width: 17%">
            <div class="pb-2">NOTA: <strong>{{ $nota->numero }}</strong></div>
            <div class="pt-2">DPS: <strong>{{ $nota->dps }}</strong></div>
            <div class="pt-2">SÉRIE <strong>{{ $nota->serie }}</strong></div>
        </td>
    </tr>
</table>

<div class="tracejado"></div>

{{-- CABEÇALHO: BRASÃO + MUNICÍPIO + SELO NFS-e --}}
<table class="tb">
    <tr>
        <td rowspan="2" class="text-center" style="width: 15%; vertical-align: middle;">
            <img src="{{ $municipio->logo }}" alt="Logo do Município" class="logo-municipio">
        </td>
        <td class="text-center" style="width: 65%">
            <div class="fs-12"><strong>{{ $municipio->nome }}</strong></div>
            <div class="fs-11">{{ $municipio->secretaria }}</div>
            <div class="fs-12"><strong>Nota Fiscal de Serviços Eletrônica - NFS-e</strong></div>
        </td>
        <td class="text-center" style="width: 20%; vertical-align: middle;">
            <img src="{{ $municipio->logoNfse }}" alt="NFS-e" class="logo-nfse">
        </td>
    </tr>
    {{-- Quadro: Nº da nota / Data de emissão / Código de barras --}}
    <tr>
        <td colspan="2" class="p-0 pt-1">
            <table class="tb quadro quadro-info-nota">
                <tr>
                    <td class="br text-center" style="width: 15%; vertical-align: middle;">
                        <div>Nº da Nota:</div>
                        <div><strong>{{ $nota->numero }}</strong></div>
                    </td>
                    <td class="br text-center nowrap" style="width: 16%; vertical-align: middle;">
                        <div>Data de Emissão:</div>
                        <div><strong>{{ $nota->dataEmissao }}</strong></div>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        @if ($nota->codigoBarrasHtml)
                            <div class="barcode-chave">{!! $nota->codigoBarrasHtml !!}</div>
                        @endif
                        <div class="fs-9"><strong>{{ $nota->codigoVerificacao }}</strong></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- PRESTADOR DE SERVIÇOS --}}
<div class="faixa">PRESTADOR DE SERVIÇOS</div>
<table class="tb secao">
    <tr>
        <td rowspan="4" class="text-center" style="width: 16%; vertical-align: middle;">
            <img src="{{ $prestador->logo }}" alt="Logo da Empresa" class="logo-empresa">
        </td>
        <td colspan="2">Nome/Razão Social: {{ $prestador->nomeRazaoSocial }}</td>
        <td style="width: 30%">Inscrição Estadual: {{ $prestador->inscricaoEstadual }}</td>
    </tr>
    <tr>
        <td style="width: 24%">CNPJ: {{ $prestador->cnpj }}</td>
        <td style="width: 20%">Fone: {{ $prestador->fone }}</td>
        <td class="nowrap">Email: <u>{{ $prestador->email }}</u></td>
    </tr>
    <tr>
        <td colspan="3">Endereço: {{ $prestador->endereco }}, Bairro: {{ $prestador->bairro }}</td>
    </tr>
    <tr>
        <td>Cep: {{ $prestador->cep }}</td>
        <td>Município: {{ $prestador->municipio }}</td>
        <td>Inscrição Municipal: {{ $prestador->inscricaoMunicipal }}</td>
    </tr>
</table>

{{-- TOMADOR DE SERVIÇOS --}}
<div class="faixa">TOMADOR DE SERVIÇOS</div>
<table class="tb secao">
    <tr>
        <td colspan="3">Nome/Razão Social: {{ $tomador->nomeRazaoSocial }}</td>
    </tr>
    <tr>
        <td style="width: 34%">CNPJ/CPF: {{ $tomador->cnpjCpf }}</td>
        <td style="width: 33%">Inscrição Municipal: {{ $tomador->inscricaoMunicipal }}</td>
        <td style="width: 33%">Inscrição Estadual: {{ $tomador->inscricaoEstadual }}</td>
    </tr>
    <tr>
        <td colspan="3">Endereço: {{ $tomador->endereco }}, Nº {{ $tomador->numero }}, Bairro:
            {{ $tomador->bairro }} - {{ $tomador->municipio }}</td>
    </tr>
    <tr>
        <td colspan="3">Complemento: {{ $tomador->complemento }}</td>
    </tr>
    <tr>
        <td>Cep: {{ $tomador->cep }}</td>
        <td>Email: <u>{{ $tomador->email }}</u></td>
        <td>Fone: {{ $tomador->fone }}</td>
    </tr>
    <tr>
        <td>Forma Pagamento: {{ $tomador->formaPagamento }}</td>
        <td colspan="2">Condição Pagamento: {{ $tomador->condicaoPagamento }}</td>
    </tr>
</table>
