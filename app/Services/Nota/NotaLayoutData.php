<?php

namespace App\Services\Nota;

use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Throwable;

/**
 * Converte o formato plano retornado por NotaCliente::getListNotaCliente() (array de
 * linhas, uma por item, com os dados de cabecalho repetidos) para os objetos
 * $tomador/$nota/$municipio/$prestador/$itens esperados por
 * admin.layouts.layout-nota-atz.
 */
class NotaLayoutData
{
    public function build(array $data): array
    {
        $cab = $data[0];

        $tomador = (object) [
            'codigo'             => $cab->CD_PESSOA,
            'nomeRazaoSocial'    => $cab->NM_PESSOA,
            'cnpjCpf'            => $cab->NR_CNPJCPF,
            'inscricaoMunicipal' => $cab->NR_INSCMUN,
            'inscricaoEstadual'  => $cab->NR_INSCESTPESSOA,
            'endereco'           => $cab->DS_ENDERECOPESSOA,
            'numero'             => $cab->NR_ENDPESSOA,
            'bairro'             => $cab->DS_BAIRROPESSOA,
            'municipio'          => $cab->DS_MUNPESSOA,
            'complemento'        => $cab->DS_COMPPESSOA,
            'cep'                => $cab->NR_CEPPESSOA,
            'email'              => $cab->DS_EMAIL,
            'fone'               => $cab->NR_FONE,
            'formaPagamento'     => $cab->DS_FORMAPAGTO,
            'condicaoPagamento'  => $cab->DS_CONDPAGTO,
        ];

        $prestador = (object) [
            'logo'               => asset('img/logo.png'),
            'nomeRazaoSocial'    => $cab->NM_EMPRESA,
            'inscricaoEstadual'  => $cab->NR_INSCESTEMPRESA,
            'cnpj'               => $cab->NR_CNPJEMPRESA,
            'fone'               => $cab->NR_FONEEMPRESA,
            'email'              => $cab->DS_EMAILEMPRESA,
            'endereco'           => $cab->DS_ENDEMPRESA,
            'bairro'             => $cab->DS_BAIRROEMPRESA,
            'cep'                => $cab->NR_CEPEMPRESA,
            'municipio'          => $cab->DS_MUNICIPIOEMP,
            'inscricaoMunicipal' => $cab->NR_INSCMUNEMPRESA,
        ];

        // Dados da prefeitura variam por empresa - config/empresas.php.
        // Empresa sem mapeamento cai no padrão (brasão genérico, sem decreto).
        $muni = config('empresas.municipios.' . (int) $cab->CD_EMPRESA)
            ?? config('empresas.municipio_padrao');

        $municipio = (object) [
            'logo'       => asset($muni['logo']),
            'nome'       => $muni['nome'],
            'secretaria' => $muni['secretaria'],
            'logoNfse'   => asset('img/logoNfse.png'),
        ];

        $nota = (object) [
            'numero'            => $cab->NR_NOTA,
            'dps'                => $cab->NR_RPS,
            'serie'              => $cab->CD_SERIE,
            'dataEmissao'        => $cab->DS_DTEMISSAO,
            'codigoVerificacao'  => $cab->CD_AUTENTICACAO,
            'codigoBarrasHtml'   => $this->codigoBarras($cab->CD_AUTENTICACAO),
            'retencaoIss'        => number_format($cab->VL_ISSQN_RETIDO, 2, ',', '.'),
            'retencaoPis'        => number_format('0.00', 2, ',', '.'), //number_format($cab->VL_PIS, 2, ',', '.'),
            'retencaoCofins'     => number_format('0.00', 2, ',', '.'), //number_format($cab->VL_COFINS, 2, ',', '.'),
            'retencaoIr'         => number_format($cab->VL_IR, 2, ',', '.'),
            'retencaoCsll'       => number_format('0.00', 2, ',', '.'), //number_format($cab->VL_CSLL, 2, ',', '.'),
            'retencaoInss'       => number_format('0.00', 2, ',', '.'), //number_format($cab->VL_INSS, 2, ',', '.'),
            'valorTotal'         => number_format($cab->VL_CONTABIL, 2, ',', '.'),
            // Fixos - mesmo comportamento do layout antigo (nao ha campo de servico na query hoje).
            'codigoServico'      => '14.04',
            'descricaoServico'   => 'Recauchutagem ou regeneração de pneus.',
            'deducoes'           => '0,00',
            'baseCalculoIss'     => number_format($cab->VL_CONTABIL, 2, ',', '.'),
            'aliquota'           => number_format(($cab->VL_ISSQN/$cab->VL_CONTABIL)*100, 2, ',', '.'),
            'valorIssRetido'     => number_format($cab->VL_ISSQN_RETIDO, 2, ',', '.'),
            'valorIss'           => number_format($cab->VL_ISSQN, 2, ',', '.'),
            'valorLiquido'       => number_format($cab->VL_CONTABIL, 2, ',', '.'),
            'vencimentos'        => $cab->O_DS_CONDPAGTO,
            'decreto'            => $muni['decreto'],
            // Sem fonte de dados na query atual - deixados em branco (o layout antigo
            // tinha valores de exemplo fixos aqui, nao reais).
            'pedidoSmartphone'   => '',
            'coletasOrdens'      => '',
            'placa'              => '',
            'vendedor'           => $cab->NM_VENDEDOR,
            'dsObsNota'          => $cab->DS_OBSNOTA,
            // Vazio quando o município não usa o portal nacional (config/empresas.php) -
            // o rodapé (nota-atz-rodape) só mostra o link quando isto não é vazio.
            'urlConsultaPublica' => $muni['url_consulta_publica']
                ? $muni['url_consulta_publica'] . '?tpc=1&chave=' . $cab->CD_AUTENTICACAO
                : '',
        ];

        // getListNotaCliente() só traz as colunas de item quando recebe $nr_lancamento;
        // sem elas (consulta de cabeçalho) a nota simplesmente não tem itens a listar.
        $itens = property_exists($cab, 'SEQ')
            ? array_map(fn($d) => (object) [
                'seq'    => $d->SEQ,
                'item'   => $d->O_DS_ITEM,
                'marca'  => $d->O_DS_MARCA,
                'modelo' => $d->O_DS_MODELO,
                'serie'  => $d->O_NR_SERIE,
                'fogo'   => $d->O_NR_FOGO,
                'dot'    => $d->O_NR_DOT,
                'qtde'   => $d->O_QTDE,
                'vlUnit' => $d->O_VL_UNITARIO,
                'vlTotal' => $d->O_VL_TOTAL,
            ], $data)
            : [];

        return compact('tomador', 'nota', 'municipio', 'prestador', 'itens');
    }

    /**
     * Resolve qual Blade renderiza a NFS-e da empresa (cada prefeitura tem um
     * desenho). Config em config/empresas.php; empresa sem mapa usa o padrão.
     */
    public static function viewName($cdEmpresa): string
    {
        return config('empresas.layouts.' . (int) $cdEmpresa)
            ?? config('empresas.layout_padrao');
    }

    /**
     * A view nacional (DANFSe) exige um par diferente de query+builder
     * (NotaCliente::getListNotaClienteNacional + NotaLayoutDataNacional) - os
     * pontos que montam o PDF/HTML da nota checam isto para decidir qual usar.
     */
    public static function isNacional($cdEmpresa): bool
    {
        return static::viewName($cdEmpresa) === 'admin.layouts.layout-nota-nacional';
    }

    /**
     * Gera o código de barras da chave de verificação, como SVG (vetorial,
     * nítido em qualquer resolução).
     *
     * A simbologia depende do código:
     *   - Chave nacional (44 dígitos, sempre par) -> CODE-128C, mais compacto e
     *     no padrão da NFS-e/DANFE.
     *   - Códigos menores/ímpares (ex.: Ponta Grossa, 9 dígitos) ou alfanuméricos
     *     -> CODE-128 automático, que aceita qualquer tamanho.
     * Em ambos o leitor decodifica exatamente o mesmo texto.
     *
     * Obs.: NÃO usa Helper::codigoBarrasHtml(), que gera Interleaved 2 of 5
     * (simbologia do boleto/FEBRABAN) e não seria lido por um leitor de CODE-128.
     */
    protected function codigoBarras(?string $codigo): string
    {
        $codigo = trim((string) $codigo);

        if ($codigo === '') {
            return '';
        }

        $tipo = (ctype_digit($codigo) && strlen($codigo) % 2 === 0)
            ? BarcodeGenerator::TYPE_CODE_128_C
            : BarcodeGenerator::TYPE_CODE_128;

        try {
            $svg = (new BarcodeGeneratorSVG())->getBarcode(
                $codigo,
                $tipo,
                1,   // widthFactor
                35   // altura em px
            );

            // A lib devolve prólogo XML + DOCTYPE, que são inválidos inline em HTML;
            // mantém-se apenas a tag <svg> em diante.
            $inicio = strpos($svg, '<svg');

            if ($inicio === false) {
                return '';
            }

            $svg = substr($svg, $inicio);

            // Sem isto o viewBox mantém a proporção e sobra espaço nas laterais.
            // Esticar só na horizontal preserva a razão entre as barras, que é o
            // que o leitor decodifica — a altura é livre.
            return preg_replace('/<svg /', '<svg preserveAspectRatio="none" ', $svg, 1);
        } catch (Throwable $e) {
            // Chave fora do padrão não deve impedir a emissão da nota.
            Log::warning('[NotaLayout] Falha ao gerar código de barras: ' . $e->getMessage());
            return '';
        }
    }
}
