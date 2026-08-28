<?php

namespace App\Services\Nota;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Converte o formato plano retornado por NotaCliente::getListNotaClienteNacional()
 * (array de linhas, uma por item, com os dados de cabecalho repetidos) para os
 * objetos $tomador/$nota/$municipio/$prestador/$itens esperados por
 * admin.layouts.layout-nota-nacional (padrão DANFSe v2.0, pós-Reforma Tributária).
 *
 * Extends NotaLayoutData só para reaproveitar codigoBarras() (canhoto) - o
 * build() é totalmente próprio, já que a query nacional traz um conjunto de
 * colunas bem diferente (IBS/CBS, código IBGE, etc.) e a maioria dos valores
 * já chega formatada ("R$ 0,00" / "0,00 %") direto do SQL.
 */
class NotaLayoutDataNacional extends NotaLayoutData
{
    public function build(array $data): array
    {
        $cab = $data[0];

        $tomador = (object) [
            'nomeRazaoSocial'    => $cab->NM_PESSOA,
            'cnpjCpf'            => $cab->NR_CNPJCPF,
            'inscricaoMunicipal' => $cab->NR_INSCMUN,
            'inscricaoEstadual'  => $cab->NR_INSCESTPESSOA,
            'municipioUf'        => $cab->DS_MUNPESSOA,
            'codigoIbgeCep'      => $cab->NR_CEPPESSOA,
            'endereco'           => $cab->DS_ENDPESSOA,
            'email'              => $cab->DS_EMAIL,
            'fone'               => $cab->NR_FONE,
            'formaPagamento'     => $cab->DS_FORMAPAGTO,
            'condicaoPagamento'  => $cab->DS_CONDPAGTO,
        ];

        // DS_MUNEMPRESA vem como "Município / UF / País" (ex.: "Criciúma / SC / BR").
        // Usado de 3 formas diferentes no DANFSe, cada uma com um recorte:
        //   - título do topo: só "Município - UF" (nomeUf)
        //   - "Município / Sigla UF" do prestador: só município/UF, sem país
        //   - "Local da Prestação / Sigla UF / País": com país, mas operação
        //     doméstica mostra "-" em vez de "BR" (confirmado comparando com
        //     DANFSe oficial - o país só aparece preenchido em operação estrangeira).
        $municipioPartes = array_map('trim', explode('/', (string) $cab->DS_MUNEMPRESA));
        $municipioUfPrestador = trim(($municipioPartes[0] ?? '') . ' / ' . ($municipioPartes[1] ?? ''));
        $paisPrestador = ($municipioPartes[2] ?? '') === 'BR' ? '-' : ($municipioPartes[2] ?? '-');
        $localPrestacaoUf = $municipioUfPrestador . ' / ' . $paisPrestador;

        $municipio = (object) [
            'nomeUf' => trim(($municipioPartes[0] ?? '') . ' - ' . ($municipioPartes[1] ?? '')),
        ];

        $prestador = (object) [
            'nomeRazaoSocial'    => $cab->NM_EMPRESA,
            'cnpj'               => $cab->NR_CNPJEMPRESA,
            'inscricaoEstadual'  => $cab->NR_INSCESTEMPRESA,
            'inscricaoMunicipal' => $cab->NR_INSCMUNEMPRESA,
            'municipioUf'        => $municipioUfPrestador,
            'codigoIbgeCep'      => $cab->NR_CEPEMPRESA,
            'endereco'           => $cab->DS_ENDERECOEMP,
            'email'              => $cab->DS_EMAILEMPRESA,
            'fone'               => $cab->NR_FONEEMPRESA,
            // "Optante" / "Não Optante" - já vem pronto (E.ST_FEDERAL).
            'simplesNacional'    => $cab->ST_OPTANTESIMPLES,
            // Sem fonte na query - DANFSe só preenche quando optante pelo SN.
            'regimeApuracaoSN'   => '-',
        ];

        $nota = (object) [
            // Identificação
            'numero'               => $cab->NR_NOTA,
            'dps'                  => $cab->NR_DPS,
            'serie'                => $cab->CD_SERIEPREFEITURA,
            'competencia'          => $this->formatarData($cab->DT_EMISSAONOTA),
            'dataHoraEmissao'      => $this->formatarDataHora($cab->DT_EMISSAONOTA),
            'dataHoraEmissaoDps'   => $this->formatarDataHora($cab->DT_EMISSAORPS),
            // Fixos - sem coluna própria na query hoje (nota sempre emitida/válida
            // e em produção nesse fluxo; ajustar se passar a existir homologação).
            'situacao'             => 'NFS-e Gerada',
            'finalidade'           => 'NFS-e regular',
            'ambienteGerador'      => '2',
            'tipoAmbiente'         => '1',
            'codigoVerificacao'    => $cab->CD_AUTENTICACAO,
            'codigoBarrasHtml'     => $this->codigoBarras($cab->DS_CODBARRASCANHOTO),
            // O DANFSe aponta o QR para a consulta pública (mesma URL que o
            // link de autenticidade no rodapé), não para a chave crua.
            'codigoQrHtml'         => $this->codigoQr($cab->DS_LINK),

            // Serviço prestado
            'descricaoServico'     => $cab->DS_SERVICO,
            // Fixos - sem coluna própria na query hoje (mesmo padrão de
            // NotaLayoutData::codigoServico). CONFIRMADO ERRADO ao comparar com
            // DANFSe oficial (varia por tipo de serviço - 14.01.01 numa nota,
            // 14.04.01 noutra) - precisa de fonte de dados real, ver aviso.
            'codigoTributacao'     => '14.01.01 / -',
            'codigoNbs'            => '1.2002.90.00',
            'localPrestacaoUf'     => $localPrestacaoUf,

            // Tributação municipal (ISSQN)
            'tipoTributacaoIssqn'      => 'Operação Tributável',
            // Mesmo formato do "Local da Prestação" (município/UF/país do
            // prestador) - o DANFSe oficial usa o mesmo valor aqui, não o
            // formato "indicador/IBGE/município/UF" do bloco IBS/CBS.
            'municipioIncidenciaIssqn' => $localPrestacaoUf,
            'baseCalculoIss'           => $this->moeda($cab->VL_BASEISS),
            'aliquotaIss'              => $this->formatarPercentual($cab->PC_ALIQUOTAISS),
            'retencaoIssqnStatus'      => $cab->DS_ST_RETIDO,
            'issqnApurado'             => $this->moeda($cab->VL_ISS),

            // Tributação federal (exceto CBS). IRRF/Contrib. Sociais Retidas
            // fixos em "-": a query retorna "R$ 0,00" (SUM sem linhas vira 0,
            // não NULL) quando o DANFSe oficial mostra "-" nesse caso - decisão
            // 26/08/2026: manter fixo em vez de tentar distinguir 0 real de
            // "não aplicável" na query.
            'irrf'                          => '-',
            'contribPrevidenciariaRetida'   => '-',
            'contribSociaisRetidas'         => '-',
            'pisDebitoApuracaoPropria'      => $this->moeda($cab->VL_PISDEVIDO),
            'cofinsDebitoApuracaoPropria'   => $this->moeda($cab->VL_COFINSDEVIDO),
            'descricaoContribSociaisRetidas' => '-',

            // Tributação IBS/CBS (Reforma Tributária)
            'cstClassTrib'                  => $cab->DS_CSTCLASSTRIB,
            'indicadorOperacaoIncidencia'   => $cab->DS_MUNINCID,
            'exclusoesReducoesBase'         => $this->moeda($cab->VL_EXCLUIBSCBS),
            'baseAposExclusoes'             => $this->moeda($cab->VL_BASEIBSCBS),
            'redAliquotaIbsCbs'             => $cab->PC_REDALIQIBSCBS,
            'aliquotaIbsUf'                 => $cab->PC_IBSUF,
            'aliquotaIbsMun'                => $cab->PC_IBSMUN,
            'aliqEfetivaMunicipalIbs'       => $cab->PC_ALIQEFETIBSMUN,
            'valorApuradoMunicipalIbs'      => $this->moeda($cab->VL_IBSMUN),
            'aliqEfetivaEstadualIbs'        => $cab->PC_ALIQEFETIBSUF,
            'valorApuradoEstadualIbs'       => $this->moeda($cab->VL_IBSUF),
            'valorTotalApuradoIbs'          => $this->moeda($cab->VL_TOTIBS),
            'aliquotaCbs'                   => $cab->PC_CBS,
            'aliquotaEfetivaCbs'            => $cab->PC_ALIQEFETCBS,
            'valorTotalApuradoCbs'          => $this->moeda($cab->VL_CBS),

            // Valor total da NFS-e. Desconto Incondicionado fixo em "-" pelo
            // mesmo motivo do IRRF acima (ver comentário lá em cima).
            'valorOperacaoServico'      => $this->moeda($cab->VL_BRUTOSERVICO),
            'descontoIncondicionado'    => '-',
            'descontoCondicionado'      => $this->moeda($cab->VL_DESCONTOCOND),
            'totalRetencoes'            => $this->moeda($cab->VL_TOTALRET),
            'valorLiquido'              => $this->moeda($cab->VL_LIQUIDO),
            'totalIbsCbs'               => $this->moeda($cab->VL_TOTIBSCBS),
            'valorLiquidoMaisIbsCbs'    => $this->moeda($cab->VL_LIQUIDOMAISIBSCBS),

            // Informações complementares (já monta Inf.Cont + tributos aprox. no SQL)
            'informacoesComplementares' => $cab->DS_INFOCOMPLEMENTAR,
        ];

        // R.O_DS_ITEM só existe quando há itens vinculados ao lançamento - LEFT
        // JOIN sempre presente na query, então uma nota sem item retorna a
        // coluna nula em vez de a linha inteira não vir.
        $itens = array_values(array_filter(array_map(fn($d) => $d->O_DS_ITEM !== null ? (object) [
            'seq'     => $d->SEQ,
            'item'    => $d->O_DS_ITEM,
            'marca'   => $d->O_DS_MARCA,
            'modelo'  => $d->O_DS_MODELO,
            'serie'   => $d->O_NR_SERIE,
            'fogo'    => $d->O_NR_FOGO,
            'dot'     => $d->O_NR_DOT,
            'qtde'    => $d->O_QTDE,
            'vlUnit'  => $d->O_VL_UNITARIO,
            'vlTotal' => $d->O_VL_TOTAL,
        ] : null, $data)));

        return compact('tomador', 'nota', 'municipio', 'prestador', 'itens');
    }

    private function formatarData(?string $timestamp): string
    {
        return $timestamp ? Carbon::parse($timestamp)->format('d/m/Y') : '';
    }

    /**
     * Reformata um valor monetário que a query já devolve como "R$ 1234,56"
     * (vírgula decimal, SEM separador de milhar - bug da query) para
     * "R$ 1.234,56". Mais simples e seguro que corrigir as ~25 expressões
     * COALESCE/REPLACE espalhadas pela query nacional.
     */
    private function moeda($valorSql): string
    {
        $valor = trim((string) $valorSql);

        if ($valor === '' || $valor === '-') {
            return $valor !== '' ? $valor : '-';
        }

        $numero = str_replace([' ', 'R$', '.', ','], ['', '', '', '.'], $valor);

        return is_numeric($numero) ? 'R$ ' . number_format((float) $numero, 2, ',', '.') : $valor;
    }

    private function formatarDataHora(?string $timestamp): string
    {
        return $timestamp ? Carbon::parse($timestamp)->format('d/m/Y H:i:s') : '';
    }

    // PC_ALIQUOTAISS chega numérico puro (sem "%" nem vírgula) - diferente dos
    // demais percentuais do IBS/CBS, que a query já formata como "0,00 %".
    private function formatarPercentual($valor): string
    {
        return $valor !== null ? number_format((float) $valor, 2, ',', '.') . ' %' : '-';
    }

    /**
     * QR Code (SVG) apontando para a consulta pública da NFS-e - único ponto
     * de identificação visual do DANFSe nacional (não tem barcode linear como
     * o layout tradicional).
     */
    private function codigoQr(?string $conteudo): string
    {
        $conteudo = trim((string) $conteudo);

        if ($conteudo === '') {
            return '';
        }

        try {
            $qrCode = QrCode::create($conteudo)
                ->setSize(160)
                ->setMargin(0);

            $svg = (new SvgWriter())->write($qrCode)->getString();

            // Mesmo motivo do codigoBarras(): remove prólogo XML/DOCTYPE, que a
            // lib inclui por padrão e é inválido inline em HTML.
            $inicio = strpos($svg, '<svg');

            return $inicio !== false ? substr($svg, $inicio) : '';
        } catch (Throwable $e) {
            // Chave fora do padrão não deve impedir a emissão da nota.
            Log::warning('[NotaLayoutNacional] Falha ao gerar QR Code: ' . $e->getMessage());
            return '';
        }
    }
}
