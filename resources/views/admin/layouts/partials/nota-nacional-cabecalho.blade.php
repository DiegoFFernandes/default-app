{{-- Cabeçalho do DANFSe v2.0 (fiel ao PDF oficial do portal nacional).
     Repetido no topo de CADA página pela paginação manual do layout-nota-nacional.
     Estrutura do oficial: sem borda nas células - as seções são separadas por
     uma linha horizontal de largura total (.secao-nacional) e o título de cada
     seção é uma caixinha cinza (.titulo-secao) na primeira coluna da grade.
     Usa $tomador, $nota, $municipio, $prestador do escopo pai. --}}

{{-- TÍTULO DANFSe + AMBIENTE --}}
<table class="tb titulo-danfse">
    <tr>
        <td style="width: 20%; vertical-align: middle;">
            <img src="{{ asset('img/logoNfse.png') }}" alt="NFS-e" class="logo-nfse-nacional">
        </td>
        <td style="width: 60%; vertical-align: middle;">
            <div class="text-center topo-titulo">DANFSe v2.0</div>
            <div class="text-center topo-subtitulo">Documento Auxiliar da NFS-e</div>
        </td>
        <td style="width: 20%">
            <div class="fs-12">Município: {{ $municipio->nomeUf }}</div>
            <div class="fs-10">Ambiente Gerador: {{ $nota->ambienteGerador }}</div>
            <div class="fs-10">Tipo de Ambiente: {{ $nota->tipoAmbiente }}</div>
        </td>
    </tr>
</table>

{{-- CHAVE DE ACESSO + IDENTIFICAÇÃO DA NFS-e/DPS (esquerda) e QR CODE com o
     texto de autenticidade embaixo (direita), como no oficial. --}}
<table class="tb linha-compacta">
    <tr>
        {{-- padding 0 no td externo: sem isso, o padding dele soma com o das
             células da tabela interna (recuo dobrado) e as colunas do topo
             desalinham da grade de 25% das seções abaixo. --}}
        <td style="width: 77%; vertical-align: top; padding: 0;">
            <div class="mt-1" style="padding: 0 6px;">
                <span class="rotulo">CHAVE DE ACESSO DA NFS-e</span>
                <span class="valor">{{ $nota->codigoVerificacao }}</span>
            </div>

            {{-- IDENTIFICAÇÃO DA NFS-e / DPS (sem bordas, como o oficial).
                 Larguras calculadas para as quebras caírem em 25% e 50% da
                 PÁGINA (o container tem 77%): 25/77 ≈ 32.5%. --}}
            <table class="tb linha-compacta">
                <tr>
                    <td style="width: 35%">
                        <span class="rotulo">NÚMERO DA NFS-e</span>
                        <span class="valor">{{ $nota->numero }}</span>
                    </td>
                    <td style="width: 30%">
                        <span class="rotulo">COMPETÊNCIA DA NFS-e</span>
                        <span class="valor">{{ $nota->competencia }}</span>
                    </td>
                    <td style="width: 35%">
                        <span class="rotulo">DATA E HORA DA EMISSÃO DA NFS-e</span>
                        <span class="valor">{{ $nota->dataHoraEmissao }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="rotulo">NÚMERO DA DPS</span>
                        <span class="valor">{{ $nota->dps }}</span>
                    </td>
                    <td>
                        <span class="rotulo">SÉRIE DA DPS</span>
                        <span class="valor">{{ $nota->serie }}</span>
                    </td>
                    <td>
                        <span class="rotulo">DATA E HORA DA EMISSÃO DA DPS</span>
                        <span class="valor">{{ $nota->dataHoraEmissaoDps }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="bg-cinza">
                        <span class="rotulo"><span class="titulo-secao">EMITENTE DA NFS-e</span></span>
                        <span class="valor">Prestador</span>
                    </td>
                    <td>
                        <span class="rotulo">SITUAÇÃO DA NFS-e</span>
                        <span class="valor">{{ $nota->situacao }}</span>
                    </td>
                    <td>
                        <span class="rotulo">FINALIDADE</span>
                        <span class="valor">{{ $nota->finalidade }}</span>
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 23%; vertical-align: top;" class="qrcode">
            @if ($nota->codigoQrHtml)
                {!! $nota->codigoQrHtml !!}
            @endif
            <div class="fs-9" style="text-align: justify;">A autenticidade desta NFS-e pode ser verificada pela leitura deste
                código QR ou pela consulta da chave de acesso no portal nacional da NFS-e</div>
        </td>
    </tr>
</table>

{{-- PRESTADOR / FORNECEDOR --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        {{-- Larguras calculadas para as bordas alinharem verticalmente com a
             tabela IDENTIFICAÇÃO DA NFS-e/DPS logo acima (que fica num
             container de 77% da página, com colunas 35/30/35) e com a coluna
             do QR code (23%): 26.95 / 23.1 / 26.95 / 23. --}}
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">PRESTADOR / FORNECEDOR</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">CNPJ / CPF / NIF</span>
            <span class="valor">{{ $prestador->cnpj }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Indicador Municipal (Inscrição)</span>
            <span class="valor">{{ $prestador->inscricaoMunicipal ?: '-' }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Telefone</span>
            <span class="valor">{{ $prestador->fone ?: '-' }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="rotulo">Nome / Nome Empresarial</span>
            <span class="valor">{{ $prestador->nomeRazaoSocial }}</span>
        </td>
        <td>
            <span class="rotulo">Município / Sigla UF</span>
            <span class="valor">{{ $prestador->municipioUf }}</span>
        </td>
        <td>
            <span class="rotulo">Código IBGE / CEP</span>
            <span class="valor">{{ $prestador->codigoIbgeCep }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="rotulo">Endereço</span>
            <span class="valor">{{ $prestador->endereco }}</span>
        </td>
        <td colspan="2">
            <span class="rotulo">E-mail</span>
            <span class="valor">{{ $prestador->email ?: '-' }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="rotulo">Simples Nacional na Data de Competência</span>
            <span class="valor">{{ $prestador->simplesNacional }}</span>
        </td>
        <td colspan="3">
            <span class="rotulo">Regime de Apuração Tributária pelo SN</span>
            <span class="valor">{{ $prestador->regimeApuracaoSN }}</span>
        </td>
    </tr>
</table>

{{-- TOMADOR / ADQUIRENTE --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        {{-- Mesma largura calculada do PRESTADOR/FORNECEDOR acima, pra manter
             o grid alinhado verticalmente até aqui: 26.95 / 23.1 / 26.95 / 23. --}}
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">TOMADOR / ADQUIRENTE</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">CNPJ / CPF / NIF</span>
            <span class="valor">{{ $tomador->cnpjCpf }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Indicador Municipal (Inscrição)</span>
            <span class="valor">{{ $tomador->inscricaoMunicipal ?: '-' }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Telefone</span>
            <span class="valor">{{ $tomador->fone ?: '-' }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="rotulo">Nome / Nome Empresarial</span>
            <span class="valor">{{ $tomador->nomeRazaoSocial }}</span>
        </td>
        <td>
            <span class="rotulo">Município / Sigla UF</span>
            <span class="valor">{{ $tomador->municipioUf }}</span>
        </td>
        <td>
            <span class="rotulo">Código IBGE / CEP</span>
            <span class="valor">{{ $tomador->codigoIbgeCep }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="rotulo">Endereço</span>
            <span class="valor">{{ $tomador->endereco }}</span>
        </td>
        <td colspan="2">
            <span class="rotulo">E-mail</span>
            <span class="valor">{{ $tomador->email ?: '-' }}</span>
        </td>
    </tr>
</table>

<div class="banner-nao-identificado">DESTINATÁRIO DA OPERAÇÃO NÃO IDENTIFICADO NA NFS-e</div>
<div class="banner-nao-identificado">INTERMEDIÁRIO DA OPERAÇÃO NÃO IDENTIFICADO NA NFS-e</div>

{{-- SERVIÇO PRESTADO --}}
<table class="tb secao-nacional linha-compacta">
    <tr>
        {{-- Mesma largura calculada do PRESTADOR/FORNECEDOR acima. --}}
        <td class="bg-cinza" style="width: 26.95%">
            <span class="rotulo"><span class="titulo-secao">SERVIÇO PRESTADO</span></span>
        </td>
        <td style="width: 23.1%">
            <span class="rotulo">Código de Tributação Nacional/Municipal</span>
            <span class="valor">{{ $nota->codigoTributacao }}</span>
        </td>
        <td style="width: 26.95%">
            <span class="rotulo">Código da NBS</span>
            <span class="valor">{{ $nota->codigoNbs }}</span>
        </td>
        <td style="width: 23%">
            <span class="rotulo">Local da Prestação / Sigla UF / País</span>
            <span class="valor">{{ $nota->localPrestacaoUf }}</span>
        </td>
    </tr>
</table>
{{-- Nome do serviço como texto livre abaixo da grade, igual ao oficial. A
     "Descrição do Serviço" do oficial (lista dos itens em colchetes) é
     substituída pelo bloco DISCRIMINAÇÃO DOS SERVIÇOS PRESTADOS logo abaixo. --}}
<div class="servico-nome">{{ $nota->descricaoServico }}</div>
